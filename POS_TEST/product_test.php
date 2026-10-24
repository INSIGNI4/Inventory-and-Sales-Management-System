
<?php

// Use Philippine local time (UTC+8)
date_default_timezone_set('Asia/Manila');

include('../connect.php');

// $action = $_GET['action'];

$action = isset($_GET['action']) ? $_GET['action'] : '';

if ($action === 'checkout') {
    saveProducts();
}


function getProducts(){

    $conn = $GLOBALS['conn'];

    $stmt = $conn->prepare("SELECT * FROM product");

    $stmt->execute();

    // Fetch result set using MySQLi
    $result = $stmt->get_result();

    $rows = $result->fetch_all(MYSQLI_ASSOC);

    // Fetch all rows using PDO syntax
    // $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

    return $rows;
}




function saveProducts(){

    // Set headers for JSON response
    header('Content-Type: application/json');

    $conn_pos = $GLOBALS['conn_pos'];
    $conn_inventory = $GLOBALS['conn']; // Connection to inventory DB

    // Read raw JSON input, fallback to $_POST
    $input = json_decode(file_get_contents('php://input'), true);
    $data = $input['data'] ?? $_POST['data'] ?? null;

    if (!$data) {
        http_response_code(400);

        echo json_encode([
            "status" => "error",
            "message" => "Missing cart data."
        ]);

        return;
    }

    // try {

    //     $date_created = date('Y-m-d H:i:s');
        

    try {

            // Start checkout transaction
        // =========================================================
        // 0. Verify logged-in cashier before starting checkout
        // =========================================================

        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }

        if (
            !isset($_SESSION['user_id']) ||
            (int) $_SESSION['user_id'] <= 0
        ) {
            throw new Exception("User is not logged in.");
        }

        $user_id = (int) $_SESSION['user_id'];



        $transaction_started = false;
        // Start checkout transaction
        $conn_pos->begin_transaction();


        $transaction_started = true;
        // =========================================================
        // 0A. Verify cashier has an active shift
        // =========================================================

        $sql_shift = "
            SELECT Shift_ID, Is_Paused
            FROM cashier_shifts
            WHERE User_ID = ?
            AND Status = 'Open'
            ORDER BY Shift_ID DESC
            LIMIT 1
            FOR UPDATE
        ";

        $stmt_shift = $conn_pos->prepare($sql_shift);
        $stmt_shift->bind_param("i", $user_id);
        $stmt_shift->execute();

        // $result_shift = $stmt_shift->get_result();
        // $active_shift = $result_shift->fetch_assoc();

        // $stmt_shift->close();

        // if (!$active_shift) {
        //     throw new Exception(
        //         "No open cashier shift found. Please open your shift before checkout."
        //     );
        // }

        // $shift_id = (int) $active_shift['Shift_ID'];




        $result_shift = $stmt_shift->get_result();
        $active_shift = $result_shift->fetch_assoc();

        $stmt_shift->close();

        if (!$active_shift) {
            throw new Exception(
                "No open cashier shift found. Please open your shift before checkout."
            );
        }

        // Block checkout while the cashier shift is paused.
        if ((int) $active_shift['Is_Paused'] === 1) {
            throw new Exception(
                "This shift is paused. Complete the interim cash count and resume the shift before checkout."
            );
        }

        $shift_id = (int) $active_shift['Shift_ID'];



        $date_created = date('Y-m-d H:i:s');
        $date_updated = date('Y-m-d H:i:s');

        // TEMPORARY DEBUG: write timestamps to PHP's error log
        error_log("POS TEST DEBUG - File: " . __FILE__);
        error_log("POS TEST DEBUG - PHP timezone: " . date_default_timezone_get());
        error_log("POS TEST DEBUG - PHP date: " . $date_created);
        error_log("POS TEST DEBUG - PHP UTC: " . gmdate('Y-m-d H:i:s'));

        // =========================================================
        // 1. Generate Reference Number
        // =========================================================

        $reference_no = 'POS-' . date('Ymd-His');


        // =========================================================
        // 2. Insert Sales Summary (PARENT)
        // =========================================================

        // $sql_sales = "INSERT INTO sales_test
        //             (
        //                 ReferenceNo,
        //                 User_ID,
        //                 total_amount,
        //                 amount_tendered,
        //                 change_amt,
        //                 date_created,
        //                 date_updated
        //             )
        //             VALUES (?, ?, ?, ?, ?, ?, ?)";


        //  // REVISION FOR TAX

        // $sql_sales = "INSERT INTO sales_test
        //     (
        //         ReferenceNo,
        //         User_ID,
        //         total_amount,
        //         subtotal,
        //         tax_name,
        //         tax_rate,
        //         tax_amount,
        //         amount_tendered,
        //         change_amt,
        //         date_created,
        //         date_updated
        //     )
        //     VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";




        // REVISION FOR CASHIER

        $sql_sales = "INSERT INTO sales_test
            (
                ReferenceNo,
                User_ID,
                Shift_ID,
                total_amount,
                subtotal,
                tax_name,
                tax_rate,
                tax_amount,
                amount_tendered,
                change_amt,
                date_created,
                date_updated
            )
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";        






        // // Total amount
        // $total_amount = (float)(
        //     $input['totalAmt'] ??
        //     $input['total_amount'] ??
        //     $input['total'] ??
        //     $_POST['totalAmt'] ??
        //     0
        // );


        // // Change amount
        // $change_amt = (float)(
        //     $input['change'] ??
        //     $input['change_amt'] ??
        //     $_POST['change'] ??
        //     0
        // );


        // // Tendered amount
        // $amount_tendered = (float)(
        //     $input['tenderAmt'] ??
        //     $input['tenderedAmt'] ??
        //     $input['amountTendered'] ??
        //     $input['tendered'] ??
        //     $input['amount_tendered'] ??
        //     $_POST['tenderAmt'] ??
        //     0
        // );





         // REVISION FOR TAX


        /* =========================================================
        1. Calculate Subtotal From Cart Items
        ========================================================= */

        if (!is_array($data) || empty($data)) {
            throw new Exception("The cart is empty or invalid.");
        }

        $subtotal = 0.00;
        $cart_item_count = 0;

        foreach ($data as $product_id => $sales_item) {
            $pid = (int)$product_id;
            $quantity = (int)($sales_item['orderQty'] ?? 0);
            $unit_price = $sales_item['price'] ?? null;

            if ($pid <= 0) {
                throw new Exception("Invalid product ID.");
            }

            if ($quantity < 0) {
                throw new Exception("Invalid quantity for Product ID " . $pid);
            }

            if ($quantity === 0) {
                continue;
            }

            if (!is_numeric($unit_price) || (float)$unit_price < 0) {
                throw new Exception("Invalid price for Product ID " . $pid);
            }

            $line_total = round((float)$unit_price * $quantity, 2);
            $subtotal = round($subtotal + $line_total, 2);
            $cart_item_count++;
        }

        if ($cart_item_count === 0) {
            throw new Exception("The cart contains no valid items.");
        }


        /* =========================================================
        2. Read Saved Tax Settings
        ========================================================= */

        $sql_tax = "SELECT
                        Tax_Name,
                        Tax_Rate,
                        Tax_Mode,
                        Is_Enabled
                    FROM tax_settings
                    ORDER BY Tax_ID ASC
                    LIMIT 1";

        $result_tax = $conn_pos->query($sql_tax);

        if (!$result_tax || $result_tax->num_rows === 0) {
            throw new Exception("Tax settings were not found.");
        }

        $tax_settings = $result_tax->fetch_assoc();

        $tax_enabled = ((int)$tax_settings['Is_Enabled'] === 1);
        $configured_tax_name = trim($tax_settings['Tax_Name'] ?? '');
        $configured_tax_rate = (float)$tax_settings['Tax_Rate'];
        $tax_mode = strtolower(trim($tax_settings['Tax_Mode'] ?? 'exclusive'));

        if (
            $configured_tax_rate < 0 ||
            $configured_tax_rate > 100
        ) {
            throw new Exception("The configured tax rate is invalid.");
        }

        if (!in_array($tax_mode, ['exclusive', 'inclusive'], true)) {
            throw new Exception("The configured tax mode is invalid.");
        }


        /* =========================================================
        3. Calculate Applied Tax and Final Total
        ========================================================= */

        $tax_name = $tax_enabled ? $configured_tax_name : null;
        $tax_rate = $tax_enabled ? $configured_tax_rate : 0.00;
        $tax_amount = 0.00;

        if (!$tax_enabled || $tax_rate == 0) {
            $total_amount = $subtotal;
        } elseif ($tax_mode === 'exclusive') {
            // Tax is added on top of the subtotal.
            $tax_amount = round($subtotal * ($tax_rate / 100), 2);
            $total_amount = round($subtotal + $tax_amount, 2);
        } else {
            // Tax is already included in the displayed subtotal.
            $total_amount = $subtotal;
            $tax_amount = round(
                $subtotal * ($tax_rate / (100 + $tax_rate)),
                2
            );
        }


        /* =========================================================
        4. Read Tendered Amount and Calculate Change
        ========================================================= */

        $amount_tendered = (float)(
            $input['tenderAmt'] ??
            $input['tenderedAmt'] ??
            $input['amountTendered'] ??
            $input['tendered'] ??
            $input['amount_tendered'] ??
            $_POST['tenderAmt'] ??
            0
        );

        if (!is_finite($amount_tendered) || $amount_tendered < 0) {
            throw new Exception("Invalid tendered amount.");
        }

        if ($amount_tendered < $total_amount) {
            throw new Exception(
                "Insufficient payment. Total due: " .
                number_format($total_amount, 2, '.', '')
            );
        }

        $change_amt = round($amount_tendered - $total_amount, 2);






















        // =========================================================
        // Get Logged-in User
        // =========================================================

        // session_start();

        // if (!isset($_SESSION['user_id'])) {
        //     throw new Exception("User is not logged in.");
        // }

        // $user_id = (int) $_SESSION['user_id'];


        // =========================================================
        // Prepare Sales INSERT
        // =========================================================




        // $stmt_sales = $conn_pos->prepare($sql_sales);

        // $stmt_sales->bind_param(
        //     "sidddss",
        //     $reference_no,
        //     $user_id,
        //     $total_amount,
        //     $amount_tendered,
        //     $change_amt,
        //     $date_created,
        //     $date_updated
        // );

        // $stmt_sales->execute();


        // // Get newly created parent Sales_ID
        // $sales_id = $conn_pos->insert_id;

        // REVISION FOR TAX


        // $stmt_sales = $conn_pos->prepare($sql_sales);

        // $stmt_sales->bind_param(
        //     "siddsddddss",
        //     $reference_no,
        //     $user_id,
        //     $total_amount,
        //     $subtotal,
        //     $tax_name,
        //     $tax_rate,
        //     $tax_amount,
        //     $amount_tendered,
        //     $change_amt,
        //     $date_created,
        //     $date_updated
        // );

        // $stmt_sales->execute();

        // // Get the newly created parent Sales_ID.
        // $sales_id = $conn_pos->insert_id;



        // REVISION FOR CASHIER

        $stmt_sales = $conn_pos->prepare($sql_sales);

        $stmt_sales->bind_param(
            "siiddsddddss",
            $reference_no,
            $user_id,
            $shift_id,
            $total_amount,
            $subtotal,
            $tax_name,
            $tax_rate,
            $tax_amount,
            $amount_tendered,
            $change_amt,
            $date_created,
            $date_updated
        );

        $stmt_sales->execute();

        $sales_id = $conn_pos->insert_id;








        // =========================================================
        // 3. Prepare Sales Item INSERT
        // =========================================================

        $sql_item = "INSERT INTO sales_item_test
                    (
                        Sales_ID,
                        Product_ID,
                        Quantity,
                        Unit_Price,
                        TotalPrice,
                        Expiration_ID,
                        date_created,
                        date_updated
                    )
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?)";

        $stmt_item = $conn_pos->prepare($sql_item);


        // =========================================================
        // 4. Prepare FIFO Expiration Query
        // =========================================================

        // $sql_fifo = "SELECT
        //                 Expiration_ID,
        //                 Quantity,
        //                 ExpirationDate
        //              FROM expiration
        //              WHERE Product_ID = ?
        //                AND Quantity > 0
        //                AND ExpirationDate >= CURDATE()
        //              ORDER BY ExpirationDate ASC, Expiration_ID ASC";

        // $stmt_fifo = $conn_inventory->prepare($sql_fifo);


        // 12
        $sql_fifo = "SELECT
                Expiration_ID,
                Quantity,
                ExpirationDate
             FROM login.expiration
             WHERE Product_ID = ?
               AND Quantity > 0
               AND ExpirationDate >= CURDATE()
             ORDER BY ExpirationDate ASC, Expiration_ID ASC";

        $stmt_fifo = $conn_pos->prepare($sql_fifo);


        // =========================================================
        // 5. Prepare Expiration Batch Update
        // =========================================================

        // $sql_update_expiration = "UPDATE expiration
        //                           SET Quantity = Quantity - ?
        //                           WHERE Expiration_ID = ?
        //                             AND Quantity >= ?";

        // $stmt_expiration = $conn_inventory->prepare(
        //     $sql_update_expiration
        // );


        $sql_update_expiration = "UPDATE login.expiration
                                SET Quantity = Quantity - ?
                                WHERE Expiration_ID = ?
                                    AND Quantity >= ?";

        $stmt_expiration = $conn_pos->prepare(
            $sql_update_expiration
        );



        // =========================================================
        // 6. Process Each Cart Item
        // =========================================================

        foreach ($data as $product_id => $sales_item) {

            $pid = (int)$product_id;

            $quantity = (int)(
                $sales_item['orderQty'] ?? 0
            );

            $unit_price = (float)(
                $sales_item['price'] ?? 0
            );

            $total_price = (float)(
                $sales_item['amount'] ?? 0
            );


            // Nothing to process if quantity is zero
            if ($quantity <= 0) {
                continue;
            }


            // =====================================================
            // 6A. Check Whether Product Uses Expiration Tracking
            // =====================================================

            // $sql_check_expiration = "SELECT COUNT(*) AS total_batches
            //                          FROM expiration
            //                          WHERE Product_ID = ?";

            // $stmt_check_expiration = $conn_inventory->prepare(
            //     $sql_check_expiration
            // );

            $sql_check_expiration = "SELECT COUNT(*) AS total_batches
                         FROM login.expiration
                         WHERE Product_ID = ?";

            $stmt_check_expiration = $conn_pos->prepare(    
                $sql_check_expiration
            );

            $stmt_check_expiration->bind_param(
                "i",
                $pid
            );

            $stmt_check_expiration->execute();

            $result_check_expiration =
                $stmt_check_expiration->get_result();

            $expiration_info =
                $result_check_expiration->fetch_assoc();

            $total_batches =
                (int)$expiration_info['total_batches'];


            // =====================================================
            // 6B. Product HAS NO Expiration Records
            // =====================================================

            if ($total_batches === 0) {

                // Product does not use expiration tracking.
                // Allow normal checkout.

                $batch_total_price =
                    $unit_price * $quantity;


                // Insert item with Expiration_ID = NULL
                $sql_item_no_expiration = "INSERT INTO sales_item_test
                    (
                        Sales_ID,
                        Product_ID,
                        Quantity,
                        Unit_Price,
                        TotalPrice,
                        Expiration_ID,
                        date_created,
                        date_updated
                    )
                    VALUES (?, ?, ?, ?, ?, NULL, ?, ?)";

                $stmt_item_no_expiration =
                    $conn_pos->prepare($sql_item_no_expiration);


                $stmt_item_no_expiration->bind_param(
                    "iiiddss",
                    $sales_id,
                    $pid,
                    $quantity,
                    $unit_price,
                    $batch_total_price,
                    $date_created,
                    $date_updated
                );


                $stmt_item_no_expiration->execute();


            // =====================================================
            // 6C. Product HAS Expiration Records
            // =====================================================

            } else {

                // Find valid expiration batches using FIFO.

                $stmt_fifo->bind_param(
                    "i",
                    $pid
                );

                $stmt_fifo->execute();

                $result_fifo =
                    $stmt_fifo->get_result();


                // How many units still need to be allocated
                $remaining_quantity =
                    $quantity;


                // Track allocated quantity
                $allocated_quantity =
                    0;


                // =================================================
                // 6D. Go Through Batches in FIFO Order
                // =================================================

                while (
                    $batch =
                    $result_fifo->fetch_assoc()
                ) {

                    if ($remaining_quantity <= 0) {
                        break;
                    }


                    $expiration_id =
                        (int)$batch['Expiration_ID'];

                    $batch_quantity =
                        (int)$batch['Quantity'];


                    // Determine quantity to take
                    $quantity_from_batch =
                        min(
                            $remaining_quantity,
                            $batch_quantity
                        );


                    if ($quantity_from_batch <= 0) {
                        continue;
                    }


                    // =================================================
                    // 6E. Deduct From Expiration Batch
                    // =================================================

                    $stmt_expiration->bind_param(
                        "iii",
                        $quantity_from_batch,
                        $expiration_id,
                        $quantity_from_batch
                    );

                    $stmt_expiration->execute();


                    // Make sure update actually happened
                    if ($stmt_expiration->affected_rows !== 1) {

                        throw new Exception(
                            "Failed to update expiration batch ID "
                            . $expiration_id
                        );
                    }


                    // =================================================
                    // Calculate Batch Sales Total
                    // =================================================

                    $batch_total_price =
                        $unit_price * $quantity_from_batch;


                    // =================================================
                    // Insert Sales Item
                    // =================================================

                    $stmt_item->bind_param(
                        "iiiddiss",
                        $sales_id,
                        $pid,
                        $quantity_from_batch,
                        $unit_price,
                        $batch_total_price,
                        $expiration_id,
                        $date_created,
                        $date_updated
                    );

                    $stmt_item->execute();


                    // =================================================
                    // Update Remaining Quantity
                    // =================================================

                    $remaining_quantity -=
                        $quantity_from_batch;

                    $allocated_quantity +=
                        $quantity_from_batch;
                }


                // =================================================
                // 6F. Check If Enough Expiration Stock Exists
                // =================================================

                if ($remaining_quantity > 0) {

                    throw new Exception(
                        "Not enough valid expiration stock for Product ID "
                        . $pid
                        . ". Requested: "
                        . $quantity
                        . ", Allocated: "
                        . $allocated_quantity
                    );
                }
            }
        }


        // =========================================================
        // 7. Return Successful Checkout
        // =========================================================

        // Everything succeeded
        $conn_pos->commit();

        $transaction_started = false;


        echo json_encode([
            "success" => true,
            "message" => "Order completed successfully!",
            "sales_id" => $sales_id,
            "reference_no" => $reference_no,
            "products" => getProducts()
        ]);


    } catch (Throwable $e) {

        if (!empty($transaction_started)) {
            $conn_pos->rollback();
        }

        error_log("POS checkout error: " . $e->getMessage());

        http_response_code(500);

        echo json_encode([
            "success" => false,
            "message" => "TEST Transaction Failed: " . $e->getMessage()
        ]);
    }
}
