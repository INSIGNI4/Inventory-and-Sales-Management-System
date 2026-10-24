
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
 * Retrieve the authenticated user's name from the database.
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
        throw new Exception('Could not prepare the audit user lookup.');
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
 * Insert the audit record inside the current transaction.
 */
function writePricingAudit(
    mysqli $conn,
    int $userId,
    string $userName,
    int $productId,
    int $supplierId,
    int $productSupplierId,
    float $previousPrice,
    float $newPrice,
    string $details
): void {
    $actionType = 'apply_suggested_price';

    $previousValue = number_format($previousPrice, 2, '.', '');
    $newValue = number_format($newPrice, 2, '.', '');

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
        throw new Exception('Could not prepare the pricing audit record.');
    }

    $stmt->bind_param(
        'issiiisss',
        $userId,
        $userName,
        $actionType,
        $productId,
        $supplierId,
        $productSupplierId,
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
        'message' => 'You are not authorized to change selling prices.'
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

$productSupplierId = filter_var(
    $input['productSupplierId'] ?? null,
    FILTER_VALIDATE_INT,
    ['options' => ['min_range' => 1]]
);

if (!$productSupplierId) {
    sendJson(400, [
        'status' => 'error',
        'message' => 'A valid supplier offer is required.'
    ]);
}

$userId = (int)$_SESSION['user_id'];

try {
    $conn->begin_transaction();

    // Identify the authenticated user for the audit record.
    $userName = getAuditUser($conn, $userId);

    /*
     * Lock the selected active offer and its product.
     * The supplier ID is captured from the selected offer.
     */
    $sql = "
        SELECT
            p.Product_ID,
            p.ProductName,
            p.Type,
            p.StorePrice,
            ps.ProductSupplier_ID,
            ps.Product_ID AS OfferProduct_ID,
            ps.Supplier_ID,
            ps.SupplierPrice,
            s.CompanyName,
            COALESCE(
                pm.MarkupPercent,
                cm.MarkupPercent,
                gm.MarkupPercent
            ) AS EffectiveMarkup,
            CASE
                WHEN pm.Product_ID IS NOT NULL THEN 'product'
                WHEN cm.CategoryName IS NOT NULL THEN 'category'
                ELSE 'general'
            END AS MarkupSource
        FROM product_supplier ps
        INNER JOIN product p
            ON p.Product_ID = ps.Product_ID
        INNER JOIN supplier s
            ON s.Supplier_ID = ps.Supplier_ID
        LEFT JOIN product_markup pm
            ON pm.Product_ID = p.Product_ID
        LEFT JOIN category_markup cm
            ON cm.CategoryName = p.Type
        INNER JOIN general_markup gm
            ON gm.Markup_ID = 1
        WHERE ps.ProductSupplier_ID = ?
          AND ps.IsActive = 1
        FOR UPDATE
    ";

    $stmt = $conn->prepare($sql);

    if (!$stmt) {
        throw new Exception('Could not prepare price lookup.');
    }

    $stmt->bind_param('i', $productSupplierId);
    $stmt->execute();

    $result = $stmt->get_result();
    $offer = $result->fetch_assoc();

    $stmt->close();

    if (!$offer) {
        $conn->rollback();

        sendJson(404, [
            'status' => 'error',
            'message' => 'The selected active supplier offer was not found.'
        ]);
    }

    $productId = (int)$offer['Product_ID'];
    $supplierId = (int)$offer['Supplier_ID'];
    $offerId = (int)$offer['ProductSupplier_ID'];

    $cost = (float)$offer['SupplierPrice'];
    $markup = (float)$offer['EffectiveMarkup'];
    $currentPrice = (float)$offer['StorePrice'];

    if (
        !is_finite($cost) ||
        !is_finite($markup) ||
        !is_finite($currentPrice) ||
        $cost < 0 ||
        $markup < 0 ||
        $markup > 10000
    ) {
        throw new Exception('The supplier cost, current price, or markup is invalid.');
    }

    // Calculate using trusted database values.
    $suggestedPrice = round(
        $cost * (1 + $markup / 100),
        2,
        PHP_ROUND_HALF_UP
    );

    // Update only the product's shared selling price.
    $update = $conn->prepare("
        UPDATE product
        SET StorePrice = ?
        WHERE Product_ID = ?
    ");

    if (!$update) {
        throw new Exception('Could not prepare the selling price update.');
    }

    $update->bind_param('di', $suggestedPrice, $productId);

    if (!$update->execute()) {
        throw new Exception('Could not update the selling price.');
    }

    $update->close();

    /*
     * Save the audit record before committing.
     * If this insert fails, the selling-price update is rolled back too.
     */
    $details = sprintf(
        'Applied suggested selling price. Product: %s. Supplier: %s. ' .
        'Supplier purchase cost: %s. Effective markup: %s%%. Markup source: %s.',
        (string)$offer['ProductName'],
        (string)$offer['CompanyName'],
        number_format($cost, 2, '.', ''),
        number_format($markup, 2, '.', ''),
        (string)$offer['MarkupSource']
    );

    writePricingAudit(
        $conn,
        $userId,
        $userName,
        $productId,
        $supplierId,
        $offerId,
        $currentPrice,
        $suggestedPrice,
        $details
    );

    // Price change and audit record commit together.
    $conn->commit();

    sendJson(200, [
        'status' => 'success',
        'message' => 'Selling price updated successfully.',
        'productId' => $productId,
        'productName' => $offer['ProductName'],
        'supplier' => $offer['CompanyName'],
        'previousPrice' => $currentPrice,
        'newPrice' => $suggestedPrice,
        'supplierCost' => $cost,
        'effectiveMarkup' => $markup,
        'markupSource' => $offer['MarkupSource']
    ]);

} catch (Throwable $e) {
    try {
        $conn->rollback();
    } catch (Throwable $rollbackError) {
        // Preserve the original error.
    }

    error_log('Apply suggested price error: ' . $e->getMessage());

    sendJson(500, [
        'status' => 'error',
        'message' => 'Unable to apply the suggested selling price.'
    ]);
}
