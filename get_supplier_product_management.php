
<?php
session_start();
header('Content-Type: application/json; charset=utf-8');

require_once 'connect.php';

function respond($status, $message = '', $data = [])
{
    echo json_encode(array_merge([
        'status' => $status,
        'message' => $message
    ], $data));
    exit;
}

$role = strtolower(trim($_SESSION['accountType'] ?? ''));

if (
    empty($_SESSION['user_id']) ||
    !in_array($role, ['manager', 'super admin'], true)
) {
    http_response_code(403);
    respond('error', 'You are not authorized to manage supplier offers.');
}

try {
    $products = [];

    $result = $conn->query("
        SELECT Product_ID, ProductName, Type, Pack_Size
        FROM product
        ORDER BY ProductName
    ");

    while ($row = $result->fetch_assoc()) {
        $products[] = $row;
    }

    $suppliers = [];

    $result = $conn->query("
        SELECT Supplier_ID, CompanyName, ContactPerson
        FROM supplier
        ORDER BY CompanyName
    ");

    while ($row = $result->fetch_assoc()) {
        $suppliers[] = $row;
    }

    $offers = [];

    $result = $conn->query("
        SELECT
            ps.ProductSupplier_ID,
            ps.Product_ID,
            ps.Supplier_ID,
            ps.SupplierPrice,
            ps.IsActive,
            p.ProductName,
            p.Type AS ProductCategory,
            s.CompanyName,
            s.ContactPerson
        FROM product_supplier ps
        INNER JOIN product p
            ON p.Product_ID = ps.Product_ID
        INNER JOIN supplier s
            ON s.Supplier_ID = ps.Supplier_ID
        ORDER BY p.ProductName, s.CompanyName
    ");

    while ($row = $result->fetch_assoc()) {
        $row['ProductSupplier_ID'] = (int) $row['ProductSupplier_ID'];
        $row['Product_ID'] = (int) $row['Product_ID'];
        $row['Supplier_ID'] = (int) $row['Supplier_ID'];
        $row['SupplierPrice'] = (float) $row['SupplierPrice'];
        $row['IsActive'] = (int) $row['IsActive'];

        $offers[] = $row;
    }

    respond('success', 'Supplier offers loaded.', [
        'products' => $products,
        'suppliers' => $suppliers,
        'offers' => $offers
    ]);

} catch (Throwable $e) {
    error_log('Supplier-product loading error: ' . $e->getMessage());

    http_response_code(500);
    respond('error', 'Unable to load supplier offers. Check the PHP error log.');
}
