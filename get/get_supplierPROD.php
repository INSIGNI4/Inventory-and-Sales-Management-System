
<?php

header('Content-Type: application/json; charset=utf-8');

include(__DIR__ . '/../connect.php');

try {

    $query = "SELECT Supplier_ID FROM supplier";

    $result = $conn->query($query);

    if (!$result) {
        throw new RuntimeException($conn->error);
    }

    $supplier = [];

    while ($row = $result->fetch_assoc()) {
        $supplier[] = $row['Supplier_ID'];
    }

    $result->free();

    echo json_encode($supplier);

} catch (Throwable $e) {

    error_log('Supplier ID retrieval failed: ' . $e->getMessage());

    http_response_code(500);

    echo json_encode([
        'success' => false,
        'message' => 'Unable to retrieve supplier IDs.'
    ]);
}

?>
