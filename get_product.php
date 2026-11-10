
<?php

header('Content-Type: application/json; charset=utf-8');

include('connect.php');

try {

    $query = "
        SELECT
            Product_ID,
            ProductName,
            Pack_Size,
            CurrentStock
        FROM product
        WHERE IsArchived = 0
        ORDER BY ProductName ASC
    ";

    $result = $conn->query($query);

    if (!$result) {
        throw new RuntimeException($conn->error);
    }

    $products = [];

    while ($row = $result->fetch_assoc()) {
        $products[] = $row;
    }

    $result->free();

    echo json_encode($products);

} catch (Throwable $e) {

    error_log('Product retrieval failed: ' . $e->getMessage());

    http_response_code(500);

    echo json_encode([
        'success' => false,
        'message' => 'Unable to retrieve product records.'
    ]);
}

?>
