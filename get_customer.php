
<?php
include('connect.php');

header('Content-Type: application/json; charset=utf-8');

$sql = "SELECT Customer_ID, CustomerName, Location FROM customers";
$result = $conn->query($sql);

if (!$result) {
    error_log('Customer query failed: ' . $conn->error);
    http_response_code(500);
    echo json_encode(['error' => 'Unable to retrieve customers.']);
    exit;
}

$customers = [];

while ($row = $result->fetch_assoc()) {
    $customers[] = $row;
}

echo json_encode($customers);

$result->free();
?>
