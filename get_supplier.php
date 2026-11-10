
<?php

header('Content-Type: application/json; charset=utf-8');

include('connect.php');

try {

    $productID = isset($_GET['Product_ID'])
        ? (int) $_GET['Product_ID']
        : 0;

    if ($productID > 0) {

        $stmt = $conn->prepare("
            SELECT
                s.Supplier_ID,
                s.CompanyName,
                s.CompanyName AS SupplierName,
                s.ContactPerson,
                s.Address,
                s.City,
                s.ProductCategory,
                s.PhoneNumber,
                s.Email,
                ps.SupplierPrice
            FROM product_supplier ps
            INNER JOIN supplier s
                ON s.Supplier_ID = ps.Supplier_ID
            INNER JOIN product p
                ON p.Product_ID = ps.Product_ID
            WHERE ps.Product_ID = ?
              AND ps.IsActive = 1
              AND s.IsArchived = 0
              AND p.IsArchived = 0
            ORDER BY s.CompanyName ASC
        ");

        $stmt->bind_param("i", $productID);
        $stmt->execute();

        $result = $stmt->get_result();

    } else {

        $result = $conn->query("
            SELECT
                Supplier_ID,
                CompanyName,
                CompanyName AS SupplierName,
                ContactPerson,
                Address,
                City,
                ProductCategory,
                PhoneNumber,
                Email
            FROM supplier
            WHERE IsArchived = 0
            ORDER BY CompanyName ASC
        ");

        if (!$result) {
            throw new RuntimeException($conn->error);
        }
    }

    $suppliers = [];

    while ($row = $result->fetch_assoc()) {
        $suppliers[] = $row;
    }

    $result->free();

    if (isset($stmt)) {
        $stmt->close();
    }

    echo json_encode($suppliers);

} catch (Throwable $e) {

    error_log('Supplier retrieval failed: ' . $e->getMessage());

    http_response_code(500);

    echo json_encode([
        'success' => false,
        'message' => 'Unable to retrieve supplier records.'
    ]);
}

?>
