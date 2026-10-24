
<?php

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

date_default_timezone_set('Asia/Manila');

header('Content-Type: application/json');

require_once 'connect.php';

$transactionStarted = false;
$purchaseOrderItems = [];

try {
    // 1. Read the AI order request.
    $rawInput = file_get_contents('php://input');
    $data = json_decode($rawInput, true);

    if (
        !is_array($data) ||
        !isset($data['items']) ||
        !is_array($data['items']) ||
        empty($data['items'])
    ) {
        throw new Exception('No AI recommendation items were provided.');
    }

    // 2. Start a transaction.
    $conn->begin_transaction();
    $transactionStarted = true;

    $usedProducts = [];

    // 3. Validate and process every selected product.
    foreach ($data['items'] as $item) {
        $productID = (int) ($item['productId'] ?? 0);
        $supplierID = (int) ($item['supplierId'] ?? 0);
        $productSupplierID = (int) ($item['productSupplierId'] ?? 0);
        $quantity = (int) ($item['orderedQuantity'] ?? 0);

        $expectedReceiveDate = trim(
            (string) ($item['expectedReceiveDate'] ?? '')
        );

        if ($productID <= 0) {
            throw new Exception('Invalid Product ID.');
        }

        if ($supplierID <= 0 || $productSupplierID <= 0) {
            throw new Exception(
                "Please select a valid supplier offer for Product ID $productID."
            );
        }

        if ($quantity <= 0) {
            throw new Exception(
                "Order quantity for Product ID $productID must be greater than zero."
            );
        }

        // 4. Validate the expected receiving date.
        $dateCheck = DateTime::createFromFormat(
            '!Y-m-d',
            $expectedReceiveDate
        );

        $dateErrors = DateTime::getLastErrors();

        if (
            !$dateCheck ||
            (
                $dateErrors !== false &&
                (
                    $dateErrors['warning_count'] > 0 ||
                    $dateErrors['error_count'] > 0
                )
            ) ||
            $dateCheck->format('Y-m-d') !== $expectedReceiveDate
        ) {
            throw new Exception(
                "Invalid expected receiving date for Product ID $productID."
            );
        }

        if ($expectedReceiveDate < date('Y-m-d')) {
            throw new Exception(
                "Expected receiving date cannot be in the past for Product ID $productID."
            );
        }

        // 5. Prevent the same product appearing twice in this order.
        if (isset($usedProducts[$productID])) {
            throw new Exception(
                "Product ID $productID appears more than once in the order."
            );
        }

        $usedProducts[$productID] = true;

        // 6. Verify the product, active supplier offer, and supplier details.
        $sql = "
            SELECT
                ps.ProductSupplier_ID,
                ps.Product_ID,
                ps.Supplier_ID,
                ps.SupplierPrice,
                p.ProductName,
                p.Pack_Size,
                s.CompanyName,
                s.ContactPerson,
                s.Address,
                s.City,
                s.PhoneNumber,
                s.Email
            FROM product_supplier ps
            INNER JOIN product p
                ON p.Product_ID = ps.Product_ID
            INNER JOIN supplier s
                ON s.Supplier_ID = ps.Supplier_ID
            WHERE ps.ProductSupplier_ID = ?
              AND ps.Product_ID = ?
              AND ps.Supplier_ID = ?
              AND ps.IsActive = 1
            LIMIT 1
        ";

        $stmt = $conn->prepare($sql);

        if (!$stmt) {
            throw new RuntimeException(
                'Could not prepare the supplier offer query.'
            );
        }

        $stmt->bind_param(
            'iii',
            $productSupplierID,
            $productID,
            $supplierID
        );

        $stmt->execute();
        $offer = $stmt->get_result()->fetch_assoc();
        $stmt->close();

        if (!$offer) {
            throw new Exception(
                "The selected supplier offer is invalid or inactive for Product ID $productID. Refresh the AI recommendations and try again."
            );
        }

        // 7. Validate pack-size multiples.
        $packSize = (int) $offer['Pack_Size'];

        if ($packSize <= 0) {
            throw new Exception(
                "Invalid pack size for Product ID $productID."
            );
        }

        if ($quantity < $packSize || $quantity % $packSize !== 0) {
            throw new Exception(
                "Order quantity for Product ID $productID must be a multiple of its pack size ($packSize)."
            );
        }

        // Always use the supplier's saved purchase price.
        $unitCost = (float) $offer['SupplierPrice'];

        if (!is_finite($unitCost) || $unitCost < 0) {
            throw new Exception(
                "Invalid supplier purchase price for Product ID $productID."
            );
        }

        // 8. Save the restock record using the existing workflow.
        $status = 'Requested';

        $restockStmt = $conn->prepare("
            INSERT INTO restock
            (
                Product_ID,
                Supplier_ID,
                Quantity,
                Expected_Receive_Date,
                Status
            )
            VALUES (?, ?, ?, ?, ?)
        ");

        if (!$restockStmt) {
            throw new RuntimeException(
                'Could not prepare the restock insert.'
            );
        }

        $restockStmt->bind_param(
            'iiiss',
            $productID,
            $supplierID,
            $quantity,
            $expectedReceiveDate,
            $status
        );

        if (!$restockStmt->execute()) {
            $error = $restockStmt->error;
            $restockStmt->close();

            throw new Exception(
                "Failed to create restock for Product ID $productID: $error"
            );
        }

        $restockStmt->close();

        // 9. Prepare the receipt using the fields expected by purchase_order.php.
        $purchaseOrderItems[] = [
            'Supplier_ID'           => (int) $offer['Supplier_ID'],
            'CompanyName'            => $offer['CompanyName'],
            'ContactPerson'          => $offer['ContactPerson'],
            'Address'                => $offer['Address'],
            'City'                   => $offer['City'],
            'PhoneNumber'            => $offer['PhoneNumber'],
            'Email'                  => $offer['Email'],
            'Expected_Receive_Date'  => $expectedReceiveDate,
            'ProductName'            => $offer['ProductName'],
            'Pack_Size'              => $packSize,
            'Quantity'               => $quantity,
            'PurchaseUnitCost'       => $unitCost,
            'ItemTotal'              => $unitCost * $quantity
        ];
    }

    // 10. Commit only when every item succeeds.
    $conn->commit();
    $transactionStarted = false;

    // Prepare the receipt session only after the commit succeeds.
    $_SESSION['purchase_order'] = [
        'created_at' => date('Y-m-d H:i:s'),
        'items'      => $purchaseOrderItems
    ];

    echo json_encode([
        'success' => true,
        'message' => 'AI recommendation order added to restock successfully.'
    ]);
    exit;

} catch (Throwable $e) {
    if ($transactionStarted) {
        try {
            $conn->rollback();
        } catch (Throwable $rollbackError) {
            // Preserve the original error.
        }
    }

    http_response_code(400);

    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
    exit;
}
