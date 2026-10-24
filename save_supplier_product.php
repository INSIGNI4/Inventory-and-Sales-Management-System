
<?php
session_start();
header('Content-Type: application/json; charset=utf-8');

require_once 'connect.php';

function respond($status, $message, $httpCode = 200)
{
    http_response_code($httpCode);

    echo json_encode([
        'status' => $status,
        'message' => $message
    ]);

    exit;
}

/**
 * Record the authenticated user's identity using the database.
 * Never trust a user ID or name sent by the browser.
 */

function getAuditUser($conn, $userId)
{
    $userName = '';

    $stmt = $conn->prepare("
        SELECT userName
        FROM users
        WHERE id = ?
        LIMIT 1
    ");

    $stmt->bind_param('i', $userId);
    $stmt->execute();

    $result = $stmt->get_result();
    $row = $result->fetch_assoc();

    $stmt->close();

    if (!$row || !isset($row['userName']) || trim($row['userName']) === '') {
        throw new RuntimeException(
            'Unable to find the logged-in user in the users table.'
        );
    }

    return trim($row['userName']);
}

/**
 * Insert an audit record.
 * Call this inside the same transaction as the change being recorded.
 */
function writeAudit(
    $conn,
    $userId,
    $userName,
    $actionType,
    $productId,
    $supplierId,
    $offerId,
    $previousValue,
    $newValue,
    $details
) {
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

$role = strtolower(trim($_SESSION['accountType'] ?? ''));

if (
    empty($_SESSION['user_id']) ||
    !in_array($role, ['manager', 'super admin'], true)
) {
    respond(
        'error',
        'You are not authorized to manage supplier offers.',
        403
    );
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    respond('error', 'POST requests only.', 405);
}

$data = json_decode(file_get_contents('php://input'), true);

if (!is_array($data)) {
    respond('error', 'Invalid request data.', 400);
}

$action = $data['action'] ?? '';
$userId = (int) $_SESSION['user_id'];

try {
    // Get the user's name from the login database.
    $userName = getAuditUser($conn, $userId);

    /*
     * ACTION 1: ADD SUPPLIER OFFER
     */
    if ($action === 'add') {
        $productId = filter_var(
            $data['productId'] ?? null,
            FILTER_VALIDATE_INT
        );

        $supplierId = filter_var(
            $data['supplierId'] ?? null,
            FILTER_VALIDATE_INT
        );

        $priceValue = $data['supplierPrice'] ?? null;

        if (
            !$productId ||
            !$supplierId ||
            !is_numeric($priceValue) ||
            !is_finite((float) $priceValue) ||
            (float) $priceValue <= 0
        ) {
            respond(
                'error',
                'Select a product and supplier, and enter a valid purchase price.',
                400
            );
        }

        $price = round((float) $priceValue, 2);

        $conn->begin_transaction();

        // The unique product/supplier constraint prevents duplicates.
        $stmt = $conn->prepare("
            INSERT INTO product_supplier
                (Product_ID, Supplier_ID, SupplierPrice, IsActive)
            VALUES (?, ?, ?, 1)
        ");

        $stmt->bind_param(
            'iid',
            $productId,
            $supplierId,
            $price
        );

        $stmt->execute();
        $offerId = (int) $conn->insert_id;
        $stmt->close();

        writeAudit(
            $conn,
            $userId,
            $userName,
            'add_supplier_offer',
            $productId,
            $supplierId,
            $offerId,
            null,
            number_format($price, 2, '.', ''),
            'Added supplier offer with initial purchase price. Offer activated.'
        );

        $conn->commit();

        respond('success', 'Supplier offer added successfully.');
    }

    /*
     * ACTION 2: UPDATE SUPPLIER PURCHASE PRICE
     */
    if ($action === 'update_price') {
        $offerId = filter_var(
            $data['offerId'] ?? null,
            FILTER_VALIDATE_INT
        );

        $priceValue = $data['supplierPrice'] ?? null;

        if (
            !$offerId ||
            !is_numeric($priceValue) ||
            !is_finite((float) $priceValue) ||
            (float) $priceValue <= 0
        ) {
            respond(
                'error',
                'Enter a valid supplier offer and purchase price.',
                400
            );
        }

        $price = round((float) $priceValue, 2);

        $conn->begin_transaction();

        // Lock the offer and capture its previous price.
        $stmt = $conn->prepare("
            SELECT Product_ID, Supplier_ID, SupplierPrice
            FROM product_supplier
            WHERE ProductSupplier_ID = ?
            FOR UPDATE
        ");

        $stmt->bind_param('i', $offerId);
        $stmt->execute();
        $stmt->bind_result(
            $productId,
            $supplierId,
            $previousPrice
        );

        $found = $stmt->fetch();
        $stmt->close();

        if (!$found) {
            $conn->rollback();
            respond('error', 'Supplier offer not found.', 404);
        }

        $previousPrice = round((float) $previousPrice, 2);

        $stmt = $conn->prepare("
            UPDATE product_supplier
            SET SupplierPrice = ?
            WHERE ProductSupplier_ID = ?
        ");

        $stmt->bind_param('di', $price, $offerId);
        $stmt->execute();
        $stmt->close();

        writeAudit(
            $conn,
            $userId,
            $userName,
            'update_supplier_price',
            (int) $productId,
            (int) $supplierId,
            (int) $offerId,
            number_format($previousPrice, 2, '.', ''),
            number_format($price, 2, '.', ''),
            'Updated supplier purchase price. Product StorePrice and POS selling price were not changed.'
        );

        $conn->commit();

        respond(
            'success',
            'Supplier purchase price updated. Selling price was not changed.'
        );
    }

    /*
     * ACTION 3: ACTIVATE OR DEACTIVATE SUPPLIER OFFER
     */
    if ($action === 'toggle_active') {
        $offerId = filter_var(
            $data['offerId'] ?? null,
            FILTER_VALIDATE_INT
        );

        $active = $data['isActive'] ?? null;

        if (
            !$offerId ||
            !in_array($active, [0, 1, '0', '1'], true)
        ) {
            respond(
                'error',
                'Invalid supplier offer or active status.',
                400
            );
        }

        $active = (int) $active;

        $conn->begin_transaction();

        // Capture the previous status before changing it.
        $stmt = $conn->prepare("
            SELECT Product_ID, Supplier_ID, IsActive
            FROM product_supplier
            WHERE ProductSupplier_ID = ?
            FOR UPDATE
        ");

        $stmt->bind_param('i', $offerId);
        $stmt->execute();
        $stmt->bind_result(
            $productId,
            $supplierId,
            $previousActive
        );

        $found = $stmt->fetch();
        $stmt->close();

        if (!$found) {
            $conn->rollback();
            respond('error', 'Supplier offer not found.', 404);
        }

        $previousActive = (int) $previousActive;

        $stmt = $conn->prepare("
            UPDATE product_supplier
            SET IsActive = ?
            WHERE ProductSupplier_ID = ?
        ");

        $stmt->bind_param('ii', $active, $offerId);
        $stmt->execute();
        $stmt->close();

        $actionType = $active
            ? 'activate_supplier_offer'
            : 'deactivate_supplier_offer';

        $details = $active
            ? 'Activated supplier offer.'
            : 'Deactivated supplier offer.';

        writeAudit(
            $conn,
            $userId,
            $userName,
            $actionType,
            (int) $productId,
            (int) $supplierId,
            (int) $offerId,
            (string) $previousActive,
            (string) $active,
            $details
        );

        $conn->commit();

        respond(
            'success',
            $active
                ? 'Supplier offer activated.'
                : 'Supplier offer deactivated.'
        );
    }

    respond('error', 'Unknown supplier offer action.', 400);

} catch (mysqli_sql_exception $e) {
    try {
        $conn->rollback();
    } catch (Throwable $rollbackError) {
        // Preserve the original database error.
    }

    if ((int) $e->getCode() === 1062) {
        respond(
            'error',
            'This supplier is already linked to this product. Edit the existing offer instead.',
            409
        );
    }

    error_log('Supplier-product save error: ' . $e->getMessage());

    respond(
        'error',
        'Unable to save the supplier offer. Check the PHP error log.',
        500
    );

} catch (Throwable $e) {
    try {
        $conn->rollback();
    } catch (Throwable $rollbackError) {
        // Preserve the original error.
    }

    error_log('Supplier-product save error: ' . $e->getMessage());

    respond(
        'error',
        'An unexpected error occurred while saving the supplier offer.',
        500
    );
}

