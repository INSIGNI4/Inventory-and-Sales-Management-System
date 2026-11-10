
<?php

header('Content-Type: application/json; charset=utf-8');

include(__DIR__ . '/../connect.php');

try {

    // Matches your existing schema: StorePrice, UnitsOrdered, UnitSold
    $query = "
        SELECT
            Product_ID,
            ProductName,
            StorePrice,
            (COALESCE(UnitsOrdered, 0) - COALESCE(UnitSold, 0)) AS AvailableStock
        FROM product
    ";

    $result = $conn->query($query);

    if (!$result) {
        throw new RuntimeException($conn->error);
    }

    $products = [];

    while ($row = $result->fetch_assoc()) {

        $pid = $row['Product_ID'];
        $stock = (int) $row['AvailableStock'];

        $products[$pid] = [
            'name'  => $row['ProductName'],
            'price' => (float) ($row['StorePrice'] ?? 0.00),
            'stock' => $stock < 0 ? 0 : $stock
        ];
    }

    $result->free();

    echo json_encode([
        'status'   => 'success',
        'products' => $products
    ]);

} catch (Throwable $e) {

    error_log('POS test product retrieval failed: ' . $e->getMessage());

    http_response_code(500);

    echo json_encode([
        'status'  => 'error',
        'message' => 'Unable to retrieve products.'
    ]);
}

?>
