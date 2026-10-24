<?php


header('Content-Type: application/json; charset=utf-8');

try {
    $pdo = new PDO(
        "mysql:host=localhost;dbname=login;charset=utf8mb4",
        "root",
        "12345QWERT",
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
        ]
    );

    $stmt = $pdo->query("
        SELECT
            Product_ID,
            ProductName,
            Pack_Size,
            CurrentStock
        FROM product
        WHERE IsArchived = 0
        ORDER BY ProductName ASC
    ");

    echo json_encode($stmt->fetchAll());

} catch (PDOException $e) {
    http_response_code(500);

    echo json_encode([
        'success' => false,
        'message' => 'Unable to retrieve product records.'
    ]);
}









// include('connect.php');


// $pdo = new PDO("mysql:host=localhost;dbname=login", "root", "12345QWERT");
// $stmt = $pdo->query("SELECT Product_ID,ProductName,Pack_Size,CurrentStock FROM product ORDER BY ProductName ASC");
// // $stmt = $pdo->query("SELECT Product_ID, ProductName FROM product ORDER BY ProductName ASC");
// $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
// echo json_encode($products);
?>

