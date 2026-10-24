
<?php
session_start();

header('Content-Type: application/json; charset=utf-8');

require_once 'connect.php';

function sendJson(int $statusCode, array $data): void
{
    http_response_code($statusCode);
    echo json_encode($data);
    exit;
}

/**
 * Get the authenticated user's name from the database.
 */
function getAuditUser(mysqli $conn, int $userId): string
{
    $stmt = $conn->prepare("
        SELECT userName
        FROM users
        WHERE id = ?
        LIMIT 1
    ");

    if (!$stmt) {
        throw new Exception('Could not prepare audit user lookup.');
    }

    $stmt->bind_param('i', $userId);
    $stmt->execute();

    $result = $stmt->get_result();
    $row = $result->fetch_assoc();

    $stmt->close();

    if (
        !$row ||
        !isset($row['userName']) ||
        trim((string)$row['userName']) === ''
    ) {
        throw new Exception(
            'Unable to identify the authenticated user for audit logging.'
        );
    }

    return trim((string)$row['userName']);
}

/**
 * Insert a pricing-rule audit record inside the current transaction.
 */
function writeMarkupAudit(
    mysqli $conn,
    int $userId,
    string $userName,
    string $actionType,
    ?int $productId,
    ?string $previousValue,
    string $newValue,
    string $details
): void {
    $supplierId = null;
    $offerId = null;

    $stmt = $conn->prepare("
        INSERT INTO pricing_audit_log (
            User_ID,
            UserName,
            ActionType,
            Product_ID,
            Supplier_ID,
            ProductSupplier_ID,
            PreviousValue,
            NewValue,
            Details
        )
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
    ");

    if (!$stmt) {
        throw new Exception('Could not prepare markup audit record.');
    }

    $stmt->bind_param(
        'issiiisss',
        $userId,
        $userName,
        $actionType,
        $productId,
        $supplierId,
        $offerId,
        $previousValue,
        $newValue,
        $details
    );

    $stmt->execute();
    $stmt->close();
}

$accountType = strtolower(trim($_SESSION['accountType'] ?? ''));

if (
    !isset($_SESSION['user_id']) ||
    !in_array($accountType, ['manager', 'super admin'], true)
) {
    sendJson(403, [
        'status' => 'error',
        'message' => 'You are not authorized to change pricing settings.'
    ]);
}

if (!isset($conn) || !$conn) {
    sendJson(500, [
        'status' => 'error',
        'message' => 'Inventory database connection is unavailable.'
    ]);
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    sendJson(405, [
        'status' => 'error',
        'message' => 'POST requests are required.'
    ]);
}

$input = json_decode(file_get_contents('php://input'), true);

if (!is_array($input)) {
    sendJson(400, [
        'status' => 'error',
        'message' => 'Invalid request data.'
    ]);
}

$ruleType = $input['ruleType'] ?? '';
$markup = $input['markupPercent'] ?? null;

if (!in_array($ruleType, ['general', 'category', 'product'], true)) {
    sendJson(400, [
        'status' => 'error',
        'message' => 'Invalid markup rule type.'
    ]);
}

if (
    !is_numeric($markup) ||
    !is_finite((float)$markup) ||
    (float)$markup < 0 ||
    (float)$markup > 10000
) {
    sendJson(400, [
        'status' => 'error',
        'message' => 'Markup must be between 0 and 10000 percent.'
    ]);
}

$markup = round((float)$markup, 2);
$userId = (int)$_SESSION['user_id'];

try {
    /*
     * Validate the target before starting the transaction.
     */
    $category = null;
    $productId = null;

    if ($ruleType === 'category') {
        $category = trim($input['categoryName'] ?? '');

        if ($category === '') {
            sendJson(400, [
                'status' => 'error',
                'message' => 'Select a product category.'
            ]);
        }

        $check = $conn->prepare("
            SELECT 1
            FROM product
            WHERE Type = ?
            LIMIT 1
        ");

        if (!$check) {
            throw new Exception('Could not validate product category.');
        }

        $check->bind_param('s', $category);
        $check->execute();
        $check->store_result();

        $categoryExists = $check->num_rows > 0;
        $check->close();

        if (!$categoryExists) {
            sendJson(400, [
                'status' => 'error',
                'message' => 'The selected category does not exist.'
            ]);
        }
    }

    if ($ruleType === 'product') {
        $productId = filter_var(
            $input['productId'] ?? null,
            FILTER_VALIDATE_INT
        );

        if (!$productId || $productId < 1) {
            sendJson(400, [
                'status' => 'error',
                'message' => 'Select a valid product.'
            ]);
        }

        $check = $conn->prepare("
            SELECT Product_ID
            FROM product
            WHERE Product_ID = ?
            LIMIT 1
        ");

        if (!$check) {
            throw new Exception('Could not validate product.');
        }

        $check->bind_param('i', $productId);
        $check->execute();
        $check->store_result();

        $productExists = $check->num_rows > 0;
        $check->close();

        if (!$productExists) {
            sendJson(400, [
                'status' => 'error',
                'message' => 'The selected product does not exist.'
            ]);
        }
    }

    $userName = getAuditUser($conn, $userId);

    $conn->begin_transaction();

    /*
     * GENERAL MARKUP
     */
    if ($ruleType === 'general') {
        $stmt = $conn->prepare("
            SELECT MarkupPercent
            FROM general_markup
            WHERE Markup_ID = 1
            FOR UPDATE
        ");

        if (!$stmt) {
            throw new Exception('Could not read existing general markup.');
        }

        $stmt->execute();
        $stmt->bind_result($previousMarkup);

        $previousExists = $stmt->fetch();
        $stmt->close();

        $previousValue = $previousExists
            ? number_format((float)$previousMarkup, 2, '.', '')
            : null;

        $stmt = $conn->prepare("
            INSERT INTO general_markup (Markup_ID, MarkupPercent)
            VALUES (1, ?)
            ON DUPLICATE KEY UPDATE
                MarkupPercent = VALUES(MarkupPercent)
        ");

        if (!$stmt) {
            throw new Exception('Could not prepare general markup update.');
        }

        $stmt->bind_param('d', $markup);
        $stmt->execute();
        $stmt->close();

        writeMarkupAudit(
            $conn,
            $userId,
            $userName,
            'save_general_markup',
            null,
            $previousValue,
            number_format($markup, 2, '.', ''),
            'Saved general markup rule.'
        );
    }

    /*
     * CATEGORY MARKUP
     */
    elseif ($ruleType === 'category') {
        $stmt = $conn->prepare("
            SELECT MarkupPercent
            FROM category_markup
            WHERE CategoryName = ?
            FOR UPDATE
        ");

        if (!$stmt) {
            throw new Exception('Could not read existing category markup.');
        }

        $stmt->bind_param('s', $category);
        $stmt->execute();
        $stmt->bind_result($previousMarkup);

        $previousExists = $stmt->fetch();
        $stmt->close();

        $previousValue = $previousExists
            ? number_format((float)$previousMarkup, 2, '.', '')
            : null;

        $stmt = $conn->prepare("
            INSERT INTO category_markup (CategoryName, MarkupPercent)
            VALUES (?, ?)
            ON DUPLICATE KEY UPDATE
                MarkupPercent = VALUES(MarkupPercent)
        ");

        if (!$stmt) {
            throw new Exception('Could not prepare category markup update.');
        }

        $stmt->bind_param('sd', $category, $markup);
        $stmt->execute();
        $stmt->close();

        writeMarkupAudit(
            $conn,
            $userId,
            $userName,
            'save_category_markup',
            null,
            $previousValue,
            number_format($markup, 2, '.', ''),
            'Saved category markup for: ' . $category
        );
    }

    /*
     * PRODUCT-SPECIFIC MARKUP
     */
    else {
        $stmt = $conn->prepare("
            SELECT MarkupPercent
            FROM product_markup
            WHERE Product_ID = ?
            FOR UPDATE
        ");

        if (!$stmt) {
            throw new Exception('Could not read existing product markup.');
        }

        $stmt->bind_param('i', $productId);
        $stmt->execute();
        $stmt->bind_result($previousMarkup);

        $previousExists = $stmt->fetch();
        $stmt->close();

        $previousValue = $previousExists
            ? number_format((float)$previousMarkup, 2, '.', '')
            : null;

        $stmt = $conn->prepare("
            INSERT INTO product_markup (Product_ID, MarkupPercent)
            VALUES (?, ?)
            ON DUPLICATE KEY UPDATE
                MarkupPercent = VALUES(MarkupPercent)
        ");

        if (!$stmt) {
            throw new Exception('Could not prepare product markup update.');
        }

        $stmt->bind_param('id', $productId, $markup);
        $stmt->execute();
        $stmt->close();

        $productName = '';

        $nameStmt = $conn->prepare("
            SELECT ProductName
            FROM product
            WHERE Product_ID = ?
            LIMIT 1
        ");

        if (!$nameStmt) {
            throw new Exception('Could not retrieve product name for audit.');
        }

        $nameStmt->bind_param('i', $productId);
        $nameStmt->execute();
        $nameStmt->bind_result($productName);
        $nameStmt->fetch();
        $nameStmt->close();

        writeMarkupAudit(
            $conn,
            $userId,
            $userName,
            'save_product_markup',
            (int)$productId,
            $previousValue,
            number_format($markup, 2, '.', ''),
            'Saved product-specific markup for Product ID ' .
                $productId . ': ' . $productName
        );
    }

    // The markup update and its audit record succeed together.
    $conn->commit();

    sendJson(200, [
        'status' => 'success',
        'message' => 'Markup rule saved successfully.'
    ]);

} catch (Throwable $e) {
    try {
        $conn->rollback();
    } catch (Throwable $rollbackError) {
        // Preserve the original error.
    }

    error_log('Save pricing rule error: ' . $e->getMessage());

    sendJson(500, [
        'status' => 'error',
        'message' => 'Unable to save the markup rule.'
    ]);
}
