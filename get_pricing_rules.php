
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

// Only Manager and Super Admin may access pricing settings.
$accountType = strtolower(trim($_SESSION['accountType'] ?? ''));

if (
    !isset($_SESSION['user_id']) ||
    !in_array($accountType, ['manager', 'super admin'], true)
) {
    sendJson(403, [
        'status' => 'error',
        'message' => 'You are not authorized to access pricing settings.'
    ]);
}

if (!isset($conn) || !$conn) {
    sendJson(500, [
        'status' => 'error',
        'message' => 'Inventory database connection is unavailable.'
    ]);
}

try {
    // 1. General markup
    $result = $conn->query(
        "SELECT MarkupPercent
         FROM general_markup
         WHERE Markup_ID = 1
         LIMIT 1"
    );

    if (!$result || $result->num_rows === 0) {
        throw new Exception('General markup configuration is missing.');
    }

    $generalMarkup = (float)$result->fetch_assoc()['MarkupPercent'];

    // 2. Category markup rules
    $categoryRules = [];

    $result = $conn->query(
        "SELECT CategoryName, MarkupPercent
         FROM category_markup
         ORDER BY CategoryName"
    );

    if (!$result) {
        throw new Exception('Could not load category markup rules.');
    }

    while ($row = $result->fetch_assoc()) {
        $categoryRules[$row['CategoryName']] =
            (float)$row['MarkupPercent'];
    }

    // 3. Product-specific markup rules
    $productRules = [];

    $result = $conn->query(
        "SELECT Product_ID, MarkupPercent
         FROM product_markup"
    );

    if (!$result) {
        throw new Exception('Could not load product markup rules.');
    }

    while ($row = $result->fetch_assoc()) {
        $productRules[(int)$row['Product_ID']] =
            (float)$row['MarkupPercent'];
    }

    // 4. Products
    $products = [];

    $result = $conn->query(
        "SELECT Product_ID, ProductName, Type, StorePrice, Pack_Size
         FROM product
         ORDER BY ProductName"
    );

    if (!$result) {
        throw new Exception('Could not load products.');
    }

    while ($row = $result->fetch_assoc()) {
        $productId = (int)$row['Product_ID'];
        $category = $row['Type'];

        if (array_key_exists($productId, $productRules)) {
            $effectiveMarkup = $productRules[$productId];
            $markupSource = 'product';
        } elseif (array_key_exists($category, $categoryRules)) {
            $effectiveMarkup = $categoryRules[$category];
            $markupSource = 'category';
        } else {
            $effectiveMarkup = $generalMarkup;
            $markupSource = 'general';
        }

        $row['Product_ID'] = $productId;
        $row['StorePrice'] = (float)$row['StorePrice'];
        $row['Pack_Size'] = (int)$row['Pack_Size'];
        $row['EffectiveMarkup'] = $effectiveMarkup;
        $row['MarkupSource'] = $markupSource;
        $row['ProductMarkup'] =
            $productRules[$productId] ?? null;

        $products[$productId] = $row;
    }

    // 5. Supplier-specific purchase prices
    $supplierOffers = [];

    $result = $conn->query(
        "SELECT
            ps.ProductSupplier_ID,
            ps.Product_ID,
            ps.Supplier_ID,
            ps.SupplierPrice,
            ps.IsActive,
            s.CompanyName,
            s.ContactPerson
         FROM product_supplier ps
         INNER JOIN supplier s
            ON s.Supplier_ID = ps.Supplier_ID
         ORDER BY ps.Product_ID, s.CompanyName"
    );

    if (!$result) {
        throw new Exception('Could not load supplier offers.');
    }

    while ($row = $result->fetch_assoc()) {
        $row['ProductSupplier_ID'] =
            (int)$row['ProductSupplier_ID'];
        $row['Product_ID'] = (int)$row['Product_ID'];
        $row['Supplier_ID'] = (int)$row['Supplier_ID'];
        $row['SupplierPrice'] = (float)$row['SupplierPrice'];
        $row['IsActive'] = (int)$row['IsActive'];

        $supplierOffers[] = $row;
    }

    // 6. Available product categories
    $categories = [];

    $result = $conn->query(
        "SELECT DISTINCT Type
         FROM product
         ORDER BY Type"
    );

    if (!$result) {
        throw new Exception('Could not load product categories.');
    }

    while ($row = $result->fetch_assoc()) {
        $categories[] = $row['Type'];
    }

    sendJson(200, [
        'status' => 'success',
        'generalMarkup' => $generalMarkup,
        'categoryRules' => $categoryRules,
        'productRules' => $productRules,
        'categories' => $categories,
        'products' => array_values($products),
        'supplierOffers' => $supplierOffers
    ]);

} catch (Throwable $e) {
    error_log('Get pricing rules error: ' . $e->getMessage());

    sendJson(500, [
        'status' => 'error',
        'message' => 'Unable to load pricing settings.'
    ]);
}
