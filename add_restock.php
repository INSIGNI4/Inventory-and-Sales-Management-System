
<?php

date_default_timezone_set('Asia/Manila');

session_start();
require_once 'connect.php';

// =========================================
// CHECK LOGIN
// =========================================

$email = $_SESSION['email'] ?? null;

if (!$email) {
    $_SESSION['response'] = [
        'success' => false,
        'message' => 'You must be logged in to add an order.'
    ];

    header('Location: ' . ($_SERVER['HTTP_REFERER'] ?? 'homepage2.php'));
    exit;
}

// =========================================
// CHECK ITEMS
// =========================================

if (
    !isset($_POST['items']) ||
    !is_array($_POST['items']) ||
    empty($_POST['items'])
) {
    $_SESSION['response'] = [
        'success' => false,
        'message' => 'No order items were provided.'
    ];

    header('Location: ' . ($_SERVER['HTTP_REFERER'] ?? 'homepage2.php'));
    exit;
}

// =========================================
// DATABASE TRANSACTION
// =========================================

$packStmt = null;
$offerStmt = null;
$supplierStmt = null;
$insertStmt = null;

try {
    $conn->begin_transaction();

    $usedProducts = [];
    $purchaseOrderItems = [];

    // Retrieve product name and pack size from the database.
    // $packStmt = $conn->prepare("
    //     SELECT ProductName, Pack_Size
    //     FROM product
    //     WHERE Product_ID = ?
    //       AND IsArchived = 0
    //     LIMIT 1
    // ");



    $packStmt = $conn->prepare("
        SELECT ProductName, Pack_Size
        FROM product
        WHERE Product_ID = ?
        AND IsArchived = 0
        LIMIT 1
    ");


    // Retrieve the active supplier-specific purchase price.
    // $offerStmt = $conn->prepare("
    //     SELECT SupplierPrice
    //     FROM product_supplier
    //     WHERE Product_ID = ?
    //       AND Supplier_ID = ?
    //       AND IsActive = 1
    //     LIMIT 1
    // ");



    // Retrieve the active supplier-specific purchase price.
    // Archived products and suppliers cannot be used for new orders.
    $offerStmt = $conn->prepare("
        SELECT ps.SupplierPrice
        FROM product_supplier ps
        INNER JOIN product p
            ON p.Product_ID = ps.Product_ID
        INNER JOIN supplier s
            ON s.Supplier_ID = ps.Supplier_ID
        WHERE ps.Product_ID = ?
        AND ps.Supplier_ID = ?
        AND ps.IsActive = 1
        AND p.IsArchived = 0
        AND s.IsArchived = 0
        LIMIT 1
    ");




    // Retrieve supplier details for the printed document.
    // $supplierStmt = $conn->prepare("
    //     SELECT
    //         CompanyName,
    //         ContactPerson,
    //         Address,
    //         City,
    //         PhoneNumber,
    //         Email
    //     FROM supplier
    //     WHERE Supplier_ID = ?
        
    //     LIMIT 1
    // ");


    $supplierStmt = $conn->prepare("
        SELECT
            CompanyName,
            ContactPerson,
            Address,
            City,
            PhoneNumber,
            Email
        FROM supplier
        WHERE Supplier_ID = ?
        AND IsArchived = 0
        LIMIT 1
    ");







    $insertStmt = $conn->prepare("
        INSERT INTO restock (
            Product_ID,
            Supplier_ID,
            Quantity,
            PurchaseUnitCost,
            Status,
            Expected_Receive_Date
        )
        VALUES (?, ?, ?, ?, ?, ?)
    ");

    if (
        !$packStmt ||
        !$offerStmt ||
        !$supplierStmt ||
        !$insertStmt
    ) {
        throw new Exception(
            'Unable to prepare the order database queries.'
        );
    }

    foreach ($_POST['items'] as $item) {
        if (!is_array($item)) {
            throw new Exception('Invalid order item data.');
        }

        $productID = isset($item['Product_ID'])
            ? (int) $item['Product_ID']
            : 0;

        $supplierID = isset($item['Supplier_ID'])
            ? (int) $item['Supplier_ID']
            : 0;

        $quantity = isset($item['Quantity'])
            ? (int) $item['Quantity']
            : 0;

        $expectedReceiveDate = isset($item['Expected_Receive_Date'])
            ? trim($item['Expected_Receive_Date'])
            : '';

        // Prevent duplicate products in the same submission.
        if ($productID <= 0) {
            throw new Exception(
                'Please select a product for every order item.'
            );
        }

        if (in_array($productID, $usedProducts, true)) {
            throw new Exception(
                'The same product cannot be added more than once in the same order.'
            );
        }

        $usedProducts[] = $productID;

        if ($supplierID <= 0) {
            throw new Exception(
                'Please select a supplier for every order item.'
            );
        }

        if ($quantity <= 0) {
            throw new Exception(
                'Order quantity must be greater than zero.'
            );
        }

        // Validate expected receive date.
        $dateCheck = DateTime::createFromFormat(
            '!Y-m-d',
            $expectedReceiveDate
        );

        if (
            !$dateCheck ||
            $dateCheck->format('Y-m-d') !== $expectedReceiveDate
        ) {
            throw new Exception(
                'Please select a valid expected receive date.'
            );
        }

        // Validate product and retrieve its name and pack size.
        $packStmt->bind_param('i', $productID);
        $packStmt->execute();

        $product = $packStmt->get_result()->fetch_assoc();

        if (!$product) {
            throw new Exception(
                "Product ID {$productID} was not found."
            );
        }

        $packSize = (int) $product['Pack_Size'];

        if ($packSize <= 0) {
            throw new Exception(
                "Invalid pack size for Product ID {$productID}."
            );
        }

        if ($quantity % $packSize !== 0) {
            throw new Exception(
                "Quantity for Product ID {$productID} must be a multiple of pack size ({$packSize})."
            );
        }

        // Verify the active supplier offer.
        $offerStmt->bind_param(
            'ii',
            $productID,
            $supplierID
        );

        $offerStmt->execute();
        $offer = $offerStmt->get_result()->fetch_assoc();

        if (!$offer) {
            throw new Exception(
                "The selected supplier has no active offer for Product ID {$productID}. Please refresh the supplier offers and try again."
            );
        }

        $purchaseUnitCost = (float) $offer['SupplierPrice'];

        if (!is_finite($purchaseUnitCost) || $purchaseUnitCost < 0) {
            throw new Exception(
                "The supplier purchase cost for Product ID {$productID} is invalid."
            );
        }

        // Retrieve supplier information from the database.
        $supplierStmt->bind_param('i', $supplierID);
        $supplierStmt->execute();

        $supplier = $supplierStmt->get_result()->fetch_assoc();

        if (!$supplier) {
            throw new Exception(
                "Supplier ID {$supplierID} was not found."
            );
        }

        // Save the order item using the existing database structure.
        $status = 'Requested';

        $insertStmt->bind_param(
            'iiidss',
            $productID,
            $supplierID,
            $quantity,
            $purchaseUnitCost,
            $status,
            $expectedReceiveDate
        );

        if (!$insertStmt->execute()) {
            throw new Exception(
                'Failed to add restock item: ' . $insertStmt->error
            );
        }

        // Collect details for the printable purchase order.
        $purchaseOrderItems[] = [
            'Product_ID' => $productID,
            'ProductName' => $product['ProductName'],
            'Pack_Size' => $packSize,
            'Supplier_ID' => $supplierID,
            'CompanyName' => $supplier['CompanyName'],
            'ContactPerson' => $supplier['ContactPerson'],
            'Address' => $supplier['Address'],
            'City' => $supplier['City'],
            'PhoneNumber' => $supplier['PhoneNumber'],
            'Email' => $supplier['Email'],
            'Quantity' => $quantity,
            'PurchaseUnitCost' => $purchaseUnitCost,
            'ItemTotal' => $quantity * $purchaseUnitCost,
            'Expected_Receive_Date' => $expectedReceiveDate
        ];
    }

    $packStmt->close();
    $offerStmt->close();
    $supplierStmt->close();
    $insertStmt->close();

    // Only show the purchase order after the whole transaction succeeds.
    $conn->commit();

    $_SESSION['purchase_order'] = [
        'created_at' => date('Y-m-d H:i:s'),
        'items' => $purchaseOrderItems
    ];

    $_SESSION['response'] = [
        'success' => true,
        'message' => 'Order/restock items added successfully.'
    ];

    header('Location: purchase_order.php');
    exit;

} catch (Throwable $e) {
    if (isset($conn) && $conn->connect_errno === 0) {
        $conn->rollback();
    }

    foreach ([$packStmt, $offerStmt, $supplierStmt, $insertStmt] as $stmt) {
        if ($stmt instanceof mysqli_stmt) {
            $stmt->close();
        }
    }

    error_log('Restock order creation failed: ' . $e->getMessage());

    $_SESSION['response'] = [
        'success' => false,
        'message' => $e instanceof Exception
            ? $e->getMessage()
            : 'An unexpected error occurred while creating the order.'
    ];

    header('Location: ' . ($_SERVER['HTTP_REFERER'] ?? 'homepage2.php'));
    exit;
}




































// session_start();
// require_once 'connect.php';

// // =========================================
// // CHECK LOGIN
// // =========================================

// $email = $_SESSION['email'] ?? null;

// if (!$email) {
//     $_SESSION['response'] = [
//         'success' => false,
//         'message' => 'You must be logged in to add an order.'
//     ];

//     header('Location: ' . ($_SERVER['HTTP_REFERER'] ?? 'homepage2.php'));
//     exit;
// }

// // =========================================
// // CHECK ITEMS
// // =========================================

// if (
//     !isset($_POST['items']) ||
//     !is_array($_POST['items']) ||
//     empty($_POST['items'])
// ) {
//     $_SESSION['response'] = [
//         'success' => false,
//         'message' => 'No order items were provided.'
//     ];

//     header('Location: ' . ($_SERVER['HTTP_REFERER'] ?? 'homepage2.php'));
//     exit;
// }

// // =========================================
// // DATABASE TRANSACTION
// // =========================================

// try {
//     $conn->begin_transaction();

//     $usedProducts = [];

//     // Prepare reusable statements once.
//     $packStmt = $conn->prepare("
//         SELECT Pack_Size
//         FROM product
//         WHERE Product_ID = ?
//         LIMIT 1
//     ");

//     $offerStmt = $conn->prepare("
//         SELECT SupplierPrice
//         FROM product_supplier
//         WHERE Product_ID = ?
//           AND Supplier_ID = ?
//           AND IsActive = 1
//         LIMIT 1
//     ");

//     $insertStmt = $conn->prepare("
//         INSERT INTO restock (
//             Product_ID,
//             Supplier_ID,
//             Quantity,
//             PurchaseUnitCost,
//             Status,
//             Expected_Receive_Date
//         )
//         VALUES (?, ?, ?, ?, ?, ?)
//     ");

//     if (!$packStmt || !$offerStmt || !$insertStmt) {
//         throw new Exception(
//             'Unable to prepare the order database queries.'
//         );
//     }

//     foreach ($_POST['items'] as $item) {
//         if (!is_array($item)) {
//             throw new Exception('Invalid order item data.');
//         }

//         $productID = isset($item['Product_ID'])
//             ? (int) $item['Product_ID']
//             : 0;

//         $supplierID = isset($item['Supplier_ID'])
//             ? (int) $item['Supplier_ID']
//             : 0;

//         $quantity = isset($item['Quantity'])
//             ? (int) $item['Quantity']
//             : 0;

//         $expectedReceiveDate = isset($item['Expected_Receive_Date'])
//             ? trim($item['Expected_Receive_Date'])
//             : '';

//         // Prevent duplicate products in the same submission.
//         if ($productID <= 0) {
//             throw new Exception(
//                 'Please select a product for every order item.'
//             );
//         }

//         if (in_array($productID, $usedProducts, true)) {
//             throw new Exception(
//                 'The same product cannot be added more than once in the same order.'
//             );
//         }

//         $usedProducts[] = $productID;

//         if ($supplierID <= 0) {
//             throw new Exception(
//                 'Please select a supplier for every order item.'
//             );
//         }

//         if ($quantity <= 0) {
//             throw new Exception(
//                 'Order quantity must be greater than zero.'
//             );
//         }

//         // Validate the expected receive date.
//         $dateCheck = DateTime::createFromFormat(
//             '!Y-m-d',
//             $expectedReceiveDate
//         );

//         if (
//             !$dateCheck ||
//             $dateCheck->format('Y-m-d') !== $expectedReceiveDate
//         ) {
//             throw new Exception(
//                 'Please select a valid expected receive date.'
//             );
//         }

//         // Validate that the product exists and retrieve its pack size.
//         $packStmt->bind_param('i', $productID);
//         $packStmt->execute();

//         $product = $packStmt->get_result()->fetch_assoc();

//         if (!$product) {
//             throw new Exception(
//                 "Product ID {$productID} was not found."
//             );
//         }

//         $packSize = (int) $product['Pack_Size'];

//         if ($packSize <= 0) {
//             throw new Exception(
//                 "Invalid pack size for Product ID {$productID}."
//             );
//         }

//         if ($quantity % $packSize !== 0) {
//             throw new Exception(
//                 "Quantity for Product ID {$productID} must be a multiple of pack size ({$packSize})."
//             );
//         }

//         // IMPORTANT:
//         // Get the cost from the database, not from submitted JavaScript data.
//         // The offer must belong to this product and supplier and be active.
//         $offerStmt->bind_param(
//             'ii',
//             $productID,
//             $supplierID
//         );

//         $offerStmt->execute();

//         $offer = $offerStmt->get_result()->fetch_assoc();

//         if (!$offer) {
//             throw new Exception(
//                 "The selected supplier has no active offer for Product ID {$productID}. Please refresh the supplier offers and try again."
//             );
//         }

//         $purchaseUnitCost = (float) $offer['SupplierPrice'];

//         if (!is_finite($purchaseUnitCost) || $purchaseUnitCost < 0) {
//             throw new Exception(
//                 "The supplier purchase cost for Product ID {$productID} is invalid."
//             );
//         }

//         // Save the supplier cost as a historical snapshot.
//         $status = 'Requested';

//         $insertStmt->bind_param(
//             'iiidss',
//             $productID,
//             $supplierID,
//             $quantity,
//             $purchaseUnitCost,
//             $status,
//             $expectedReceiveDate
//         );

//         if (!$insertStmt->execute()) {
//             throw new Exception(
//                 'Failed to add restock item: ' . $insertStmt->error
//             );
//         }
//     }

//     $packStmt->close();
//     $offerStmt->close();
//     $insertStmt->close();

//     $conn->commit();

//     $_SESSION['response'] = [
//         'success' => true,
//         'message' => 'Order/restock items added successfully with historical purchase costs.'
//     ];

// } catch (Throwable $e) {
//     if (isset($conn) && $conn->connect_errno === 0) {
//         $conn->rollback();
//     }

//     if (isset($packStmt) && $packStmt instanceof mysqli_stmt) {
//         $packStmt->close();
//     }

//     if (isset($offerStmt) && $offerStmt instanceof mysqli_stmt) {
//         $offerStmt->close();
//     }

//     if (isset($insertStmt) && $insertStmt instanceof mysqli_stmt) {
//         $insertStmt->close();
//     }

//     error_log('Restock order creation failed: ' . $e->getMessage());

//     $_SESSION['response'] = [
//         'success' => false,
//         'message' => $e instanceof Exception
//             ? $e->getMessage()
//             : 'An unexpected error occurred while creating the order.'
//     ];
// }

// // =========================================
// // RETURN TO PREVIOUS PAGE
// // =========================================

// header('Location: ' . ($_SERVER['HTTP_REFERER'] ?? 'homepage2.php'));
// exit;
?>
