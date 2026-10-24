<?php
ob_start();
require_once '../connect.php';

header('Content-Type: application/json; charset=utf-8');

$conn->set_charset('utf8mb4');

function respond($success, $message, $data = [])
{
    ob_clean();
    echo json_encode(array_merge([
        'success' => $success,
        'message' => $message
    ], $data));
    exit;
}

function getStock($conn, $productId, $lock = false)
{
    $sql = "SELECT Inventory FROM inventory
            WHERE Product_ID = ?
            LIMIT 1";

    if ($lock) {
        $sql .= " FOR UPDATE";
    }

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $productId);
    $stmt->execute();

    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    $stmt->close();

    if (!$row) {
        throw new Exception(
            'No inventory record exists for this product.'
        );
    }

    return (int)($row['Inventory'] ?? 0);
}

function updateStock($conn, $productId, $newStock)
{
    $stmt = $conn->prepare(
        "UPDATE inventory SET Inventory = ? WHERE Product_ID = ?"
    );
    $stmt->bind_param("ii", $newStock, $productId);
    $stmt->execute();

    if ($stmt->affected_rows === 0) {
        $stmt->close();

        $check = $conn->prepare(
            "SELECT Product_ID FROM inventory WHERE Product_ID = ?"
        );
        $check->bind_param("i", $productId);
        $check->execute();
        $exists = $check->get_result()->num_rows > 0;
        $check->close();

        if (!$exists) {
            throw new Exception('Inventory record was not found.');
        }
    }

    $stmt->close();
}

$action = $_GET['action'] ?? '';

if ($action === 'products') {
    $result = $conn->query(
        "SELECT Product_ID, ProductName
         FROM product
         WHERE IsArchived = 0
         ORDER BY ProductName ASC"
    );

    if (!$result) {
        respond(false, 'Unable to load products.');
    }

    $products = [];

    while ($row = $result->fetch_assoc()) {
        $products[] = $row;
    }

    respond(true, 'Products loaded.', [
        'products' => $products
    ]);
}

if ($action === 'batches') {
    $productId = filter_input(INPUT_GET, 'product_id', FILTER_VALIDATE_INT);

    if (!$productId) {
        respond(false, 'Invalid product.');
    }

    $stmt = $conn->prepare(
        "SELECT Expiration_ID, BatchNum, ExpirationDate, Quantity
         FROM expiration
         WHERE Product_ID = ?
         ORDER BY ExpirationDate ASC, Expiration_ID ASC"
    );

    $stmt->bind_param("i", $productId);
    $stmt->execute();

    $result = $stmt->get_result();
    $batches = [];

    while ($row = $result->fetch_assoc()) {
        $batches[] = $row;
    }

    $stmt->close();

    respond(true, 'Batches loaded.', [
        'batches' => $batches
    ]);
}

if ($action === 'history') {
    $result = $conn->query(
        "SELECT a.Date_Created,
                p.ProductName,
                a.Previous_Stock,
                a.Adjustment_Type,
                a.Quantity,
                a.New_Stock,
                a.Reason,
                a.User_ID,
                a.BatchNum
         FROM stock_adjustments a
         LEFT JOIN product p ON p.Product_ID = a.Product_ID
         ORDER BY a.Adjustment_ID DESC
         LIMIT 200"
    );

    if (!$result) {
        respond(false, 'Unable to load adjustment history.');
    }

    $history = [];

    while ($row = $result->fetch_assoc()) {
        $history[] = $row;
    }

    respond(true, 'History loaded.', [
        'history' => $history
    ]);
}

if ($action === 'save' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $productId = filter_input(INPUT_POST, 'product_id', FILTER_VALIDATE_INT);
    $batchIdInput = $_POST['batch_id'] ?? '';
    $type = $_POST['adjustment_type'] ?? '';
    $quantity = filter_input(INPUT_POST, 'quantity', FILTER_VALIDATE_INT);
    $expiryDate = trim($_POST['expiry_date'] ?? '');
    $reason = trim($_POST['reason'] ?? '');
    $notes = trim($_POST['notes'] ?? '');

    if (
        !$productId ||
        !$quantity ||
        $quantity < 1 ||
        !in_array($type, ['increase', 'decrease'], true) ||
        $reason === ''
    ) {
        respond(false, 'Please complete all required fields correctly.');
    }

    if ($batchIdInput === '') {
        respond(false, 'Please select a batch.');
    }

    $isNewBatch = $batchIdInput === 'new';

    if ($isNewBatch && $type !== 'increase') {
        respond(false, 'A new batch can only be created when adding stock.');
    }

    if ($isNewBatch) {
        $date = DateTime::createFromFormat('!Y-m-d', $expiryDate);

        if (!$date || $date->format('Y-m-d') !== $expiryDate) {
            respond(false, 'Please provide a valid expiry date for the new batch.');
        }
    }

    if (!$isNewBatch && !ctype_digit((string)$batchIdInput)) {
        respond(false, 'Invalid batch selection.');
    }

    $batchId = $isNewBatch ? 0 : (int)$batchIdInput;

    try {
        $conn->begin_transaction();

        $productStmt = $conn->prepare(
            "SELECT Product_ID
             FROM product
             WHERE Product_ID = ? AND IsArchived = 0
             LIMIT 1"
        );
        $productStmt->bind_param("i", $productId);
        $productStmt->execute();

        if (!$productStmt->get_result()->fetch_assoc()) {
            $productStmt->close();
            throw new Exception('Product not found or archived.');
        }

        $productStmt->close();

        $previousStock = getStock($conn, $productId, true);

        if ($isNewBatch) {
            $batchNum = 'B' . date('ymdHis') . random_int(100, 999);

            $stmt = $conn->prepare(
                "INSERT INTO expiration
                    (Product_ID, BatchNum, ExpirationDate, Quantity)
                 VALUES (?, ?, ?, ?)"
            );
            $stmt->bind_param(
                "issi",
                $productId,
                $batchNum,
                $expiryDate,
                $quantity
            );
            $stmt->execute();
            $stmt->close();
        } else {
            $batchStmt = $conn->prepare(
                "SELECT Expiration_ID, BatchNum, ExpirationDate, Quantity
                 FROM expiration
                 WHERE Expiration_ID = ? AND Product_ID = ?
                 FOR UPDATE"
            );
            $batchStmt->bind_param("ii", $batchId, $productId);
            $batchStmt->execute();

            $batch = $batchStmt->get_result()->fetch_assoc();
            $batchStmt->close();

            if (!$batch) {
                throw new Exception('The selected batch does not belong to this product.');
            }

            $batchNum = $batch['BatchNum'];
            $batchQuantity = (int)$batch['Quantity'];

            if ($type === 'decrease' && $quantity > $batchQuantity) {
                throw new Exception(
                    'The quantity to deduct exceeds this batch’s available stock (' .
                    $batchQuantity . ').'
                );
            }

            $newBatchQuantity = $type === 'increase'
                ? $batchQuantity + $quantity
                : $batchQuantity - $quantity;

            $updateBatch = $conn->prepare(
                "UPDATE expiration SET Quantity = ?
                 WHERE Expiration_ID = ? AND Product_ID = ?"
            );
            $updateBatch->bind_param(
                "iii",
                $newBatchQuantity,
                $batchId,
                $productId
            );
            $updateBatch->execute();
            $updateBatch->close();
        }

        $newStock = $type === 'increase'
            ? $previousStock + $quantity
            : $previousStock - $quantity;

        if ($newStock < 0) {
            throw new Exception('The adjustment would make total stock negative.');
        }

        updateStock($conn, $productId, $newStock);

        $userId = null;
        $userIdValue = $_SESSION['User_ID'] ?? $_SESSION['user_id'] ?? null;

        if (is_numeric($userIdValue)) {
            $userId = (int)$userIdValue;
        }

        $historyStmt = $conn->prepare(
            "INSERT INTO stock_adjustments
                (Product_ID, User_ID, Adjustment_Type, Quantity,
                 Previous_Stock, New_Stock, Reason, Notes, BatchNum)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)"
        );

        $historyStmt->bind_param(
            "iisiiisss",
            $productId,
            $userId,
            $type,
            $quantity,
            $previousStock,
            $newStock,
            $reason,
            $notes,
            $batchNum
        );

        $historyStmt->execute();
        $historyStmt->close();

        $conn->commit();

        respond(true, 'Stock adjustment saved successfully.', [
            'previous_stock' => $previousStock,
            'new_stock' => $newStock,
            'batch_num' => $batchNum
        ]);

    } catch (Throwable $e) {
        $conn->rollback();
        respond(false, $e->getMessage());
    }
}

respond(false, 'Invalid action.');