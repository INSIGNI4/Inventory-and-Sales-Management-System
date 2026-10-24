
<?php
date_default_timezone_set('Asia/Manila');

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . '/../connect.php';

function respond(array $data, int $status = 200): void
{
    http_response_code($status);
    echo json_encode($data);
    exit;
}

$userId = (int) ($_SESSION['user_id'] ?? 0);

if ($userId <= 0) {
    respond([
        'success' => false,
        'message' => 'Your session has expired. Please log in again.'
    ], 401);
}

$action = $_POST['action'] ?? $_GET['action'] ?? 'status';
$transactionStarted = false;

try {
    /*
     * Check the current user's open shift.
     */
    if ($action === 'status') {
        // $stmt = $conn_pos->prepare("
        //     SELECT Shift_ID, Opening_Float, Opened_At
        //     FROM cashier_shifts
        //     WHERE User_ID = ?
        //       AND Status = 'Open'
        //     ORDER BY Shift_ID DESC
        //     LIMIT 1
        // ");



        $stmt = $conn_pos->prepare("
            SELECT
                Shift_ID,
                Opening_Float,
                Opened_At,
                Is_Paused,
                Paused_At,
                Paused_By
            FROM cashier_shifts
            WHERE User_ID = ?
            AND Status = 'Open'
            ORDER BY Shift_ID DESC
            LIMIT 1
        ");




        $stmt->bind_param('i', $userId);
        $stmt->execute();

        $result = $stmt->get_result();
        $shift = $result->fetch_assoc();
        $stmt->close();

        respond([
            'success' => true,
            'has_open_shift' => (bool) $shift,
            'shift' => $shift
        ]);
    }

    /*
     * Open a new cashier shift.
     */
    if ($action === 'open') {
        $rawFloat = trim((string) ($_POST['opening_float'] ?? ''));

        if (
            $rawFloat === '' ||
            !is_numeric($rawFloat) ||
            !is_finite((float) $rawFloat) ||
            (float) $rawFloat < 0 ||
            (float) $rawFloat > 99999999.99
        ) {
            respond([
                'success' => false,
                'message' => 'Enter a valid opening cash float from ₱0 to ₱99,999,999.99.'
            ], 422);
        }

        $openingFloat = round((float) $rawFloat, 2);

        $conn_pos->begin_transaction();
        $transactionStarted = true;

        // $stmt = $conn_pos->prepare("
        //     SELECT Shift_ID
        //     FROM cashier_shifts
        //     WHERE User_ID = ?
        //       AND Status = 'Open'
        //     LIMIT 1
        //     FOR UPDATE
        // ");






        $stmt = $conn_pos->prepare("
            SELECT Shift_ID, Opening_Float, Is_Paused
            FROM cashier_shifts
            WHERE User_ID = ?
            AND Status = 'Open'
            ORDER BY Shift_ID DESC
            LIMIT 1
            FOR UPDATE
        ");


        $stmt->bind_param('i', $userId);
        $stmt->execute();

        $result = $stmt->get_result();
        $existingShift = $result->fetch_assoc();
        $stmt->close();

        if ($existingShift) {
            $conn_pos->rollback();
            $transactionStarted = false;

            respond([
                'success' => false,
                'message' => 'You already have an open shift. Refresh the POS.'
            ], 409);
        }

        $stmt = $conn_pos->prepare("
            INSERT INTO cashier_shifts
                (User_ID, Opening_Float, Status)
            VALUES (?, ?, 'Open')
        ");

        $stmt->bind_param('id', $userId, $openingFloat);
        $stmt->execute();

        $shiftId = (int) $conn_pos->insert_id;
        $stmt->close();

        $conn_pos->commit();
        $transactionStarted = false;

        respond([
            'success' => true,
            'message' => 'Your shift has been opened.',
            'shift_id' => $shiftId,
            'opening_float' => number_format($openingFloat, 2, '.', '')
        ]);
    }





    /*
    * PHASE 4: Pause, interim cash count, and resume.
    */

    // Pause the current shift before the cashier counts the drawer.
    if ($action === 'pause') {
        $conn_pos->begin_transaction();
        $transactionStarted = true;

        $stmt = $conn_pos->prepare("
            SELECT Shift_ID, Is_Paused
            FROM cashier_shifts
            WHERE User_ID = ?
            AND Status = 'Open'
            ORDER BY Shift_ID DESC
            LIMIT 1
            FOR UPDATE
        ");

        $stmt->bind_param('i', $userId);
        $stmt->execute();
        $result = $stmt->get_result();
        $shift = $result->fetch_assoc();
        $stmt->close();

        if (!$shift) {
            throw new RuntimeException('There is no open shift to pause.');
        }

        if ((int) $shift['Is_Paused'] === 1) {
            throw new RuntimeException('Your shift is already paused.');
        }

        $shiftId = (int) $shift['Shift_ID'];

        $stmt = $conn_pos->prepare("
            UPDATE cashier_shifts
            SET Is_Paused = 1,
                Paused_At = NOW(),
                Paused_By = ?
            WHERE Shift_ID = ?
            AND User_ID = ?
            AND Status = 'Open'
            AND Is_Paused = 0
        ");

        $stmt->bind_param('iii', $userId, $shiftId, $userId);
        $stmt->execute();
        $updated = $stmt->affected_rows;
        $stmt->close();

        if ($updated !== 1) {
            throw new RuntimeException('Unable to pause the shift.');
        }

        $conn_pos->commit();
        $transactionStarted = false;

        respond([
            'success' => true,
            'message' => 'Shift paused. Count the cash in the drawer.',
            'shift_id' => $shiftId
        ]);
    }


    // Save an interim cash count for the currently paused shift.
    if ($action === 'save_cash_count') {
        $rawActual = trim((string) ($_POST['actual_cash'] ?? ''));
        $notes = trim((string) ($_POST['notes'] ?? ''));

        if (
            $rawActual === '' ||
            !is_numeric($rawActual) ||
            !is_finite((float) $rawActual) ||
            (float) $rawActual < 0 ||
            (float) $rawActual > 99999999.99
        ) {
            respond([
                'success' => false,
                'message' => 'Enter a valid physical cash count.'
            ], 422);
        }

        if (strlen($notes) > 60000) {
            respond([
                'success' => false,
                'message' => 'Cash-count notes are too long.'
            ], 422);
        }

        $actualCash = round((float) $rawActual, 2);

        $conn_pos->begin_transaction();
        $transactionStarted = true;

        $stmt = $conn_pos->prepare("
            SELECT Shift_ID, Opening_Float, Paused_At
            FROM cashier_shifts
            WHERE User_ID = ?
            AND Status = 'Open'
            AND Is_Paused = 1
            ORDER BY Shift_ID DESC
            LIMIT 1
            FOR UPDATE
        ");

        $stmt->bind_param('i', $userId);
        $stmt->execute();
        $result = $stmt->get_result();
        $shift = $result->fetch_assoc();
        $stmt->close();

        if (!$shift) {
            throw new RuntimeException(
                'Pause your open shift before saving a cash count.'
            );
        }

        $shiftId = (int) $shift['Shift_ID'];
        $openingFloat = (float) $shift['Opening_Float'];
        $pausedAt = $shift['Paused_At'];

        // Only sales completed before the current pause count toward this count.
        $stmt = $conn_pos->prepare("
            SELECT COALESCE(SUM(total_amount), 0) AS ShiftSales
            FROM sales_test
            WHERE Shift_ID = ?
            AND date_created <= ?
        ");

        $stmt->bind_param('is', $shiftId, $pausedAt);
        $stmt->execute();
        $result = $stmt->get_result();
        $salesRow = $result->fetch_assoc();
        $stmt->close();

        $shiftSales = (float) ($salesRow['ShiftSales'] ?? 0);
        $expectedCash = round($openingFloat + $shiftSales, 2);
        $variance = round($actualCash - $expectedCash, 2);

        // Prevent multiple counts for one pause. A new pause gets a new timestamp.
        $stmt = $conn_pos->prepare("
            SELECT Cash_Count_ID
            FROM cashier_shift_cash_counts
            WHERE Shift_ID = ?
            AND Counted_At >= ?
            LIMIT 1
        ");

        $stmt->bind_param('is', $shiftId, $pausedAt);
        $stmt->execute();
        $result = $stmt->get_result();
        $existingCount = $result->fetch_assoc();
        $stmt->close();

        if ($existingCount) {
            throw new RuntimeException(
                'A cash count is already saved for this pause. Resume the shift first.'
            );
        }

        $stmt = $conn_pos->prepare("
            INSERT INTO cashier_shift_cash_counts
                (Shift_ID, Expected_Cash, Actual_Cash, Cash_Variance,
                Counted_By, Notes)
            VALUES (?, ?, ?, ?, ?, ?)
        ");

        $stmt->bind_param(
            'idddis',
            $shiftId,
            $expectedCash,
            $actualCash,
            $variance,
            $userId,
            $notes
        );

        $stmt->execute();
        $cashCountId = (int) $conn_pos->insert_id;
        $stmt->close();

        $conn_pos->commit();
        $transactionStarted = false;

        respond([
            'success' => true,
            'message' => 'Interim cash count saved. You may resume the shift.',
            'cash_count_id' => $cashCountId,
            'shift_id' => $shiftId,
            'expected_cash' => number_format($expectedCash, 2, '.', ''),
            'actual_cash' => number_format($actualCash, 2, '.', ''),
            'cash_variance' => number_format($variance, 2, '.', '')
        ]);
    }


    // CATCHER


    
    /*
     * Cancel an interim cash count and undo the pause.
     * No cash count is saved by this action.
     */
    // if ($action === 'cancel_pause') {
    //     $conn_pos->begin_transaction();
    //     $transactionStarted = true;

    //     $stmt = $conn_pos->prepare("
    //         SELECT Shift_ID
    //         FROM cashier_shifts
    //         WHERE User_ID = ?
    //           AND Status = 'Open'
    //           AND Is_Paused = 1
    //         ORDER BY Shift_ID DESC
    //         LIMIT 1
    //         FOR UPDATE
    //     ");

    //     $stmt->bind_param('i', $userId);
    //     $stmt->execute();
    //     $result = $stmt->get_result();
    //     $shift = $result->fetch_assoc();
    //     $stmt->close();

    //     if (!$shift) {
    //         $conn_pos->rollback();
    //         $transactionStarted = false;

    //         respond([
    //             'success' => false,
    //             'message' => 'There is no paused shift to cancel.'
    //         ], 409);
    //     }

    //     if ((int) $shift['Is_Paused'] === 1) {
    //         $conn_pos->rollback();
    //         $transactionStarted = false;

    //         respond([
    //             'success' => false,
    //             'message' => 'The shift is paused. Cancel the interim cash count or save the count and resume before closing.'
    //         ], 409);
    //     }

    //     $shiftId = (int) $shift['Shift_ID'];

    //     $stmt = $conn_pos->prepare("
    //         UPDATE cashier_shifts
    //         SET Is_Paused = 0,
    //             Paused_At = NULL,
    //             Paused_By = NULL
    //         WHERE Shift_ID = ?
    //           AND User_ID = ?
    //           AND Status = 'Open'
    //           AND Is_Paused = 1
    //     ");

    //     $stmt->bind_param('ii', $shiftId, $userId);
    //     $stmt->execute();
    //     $updatedRows = $stmt->affected_rows;
    //     $stmt->close();

    //     if ($updatedRows !== 1) {
    //         throw new RuntimeException(
    //             'Unable to cancel the shift pause.'
    //         );
    //     }

    //     $conn_pos->commit();
    //     $transactionStarted = false;

    //     respond([
    //         'success' => true,
    //         'message' => 'Cash count cancelled. Your shift has resumed.',
    //         'shift_id' => $shiftId
    //     ]);
    // }




    /*
     * Cancel an interim cash count and undo the pause.
     * No cash count is saved by this action.
     */
    if ($action === 'cancel_pause') {
        $conn_pos->begin_transaction();
        $transactionStarted = true;

        $stmt = $conn_pos->prepare("
            SELECT Shift_ID
            FROM cashier_shifts
            WHERE User_ID = ?
              AND Status = 'Open'
              AND Is_Paused = 1
            ORDER BY Shift_ID DESC
            LIMIT 1
            FOR UPDATE
        ");

        $stmt->bind_param('i', $userId);
        $stmt->execute();
        $result = $stmt->get_result();
        $shift = $result->fetch_assoc();
        $stmt->close();

        if (!$shift) {
            $conn_pos->rollback();
            $transactionStarted = false;

            respond([
                'success' => false,
                'message' => 'There is no paused shift to cancel. Refresh the POS and check the current shift status.'
            ], 409);
        }

        $shiftId = (int) $shift['Shift_ID'];

        $stmt = $conn_pos->prepare("
            UPDATE cashier_shifts
            SET Is_Paused = 0,
                Paused_At = NULL,
                Paused_By = NULL
            WHERE Shift_ID = ?
              AND User_ID = ?
              AND Status = 'Open'
              AND Is_Paused = 1
        ");

        $stmt->bind_param('ii', $shiftId, $userId);
        $stmt->execute();
        $updatedRows = $stmt->affected_rows;
        $stmt->close();

        if ($updatedRows !== 1) {
            throw new RuntimeException(
                'Unable to cancel the shift pause.'
            );
        }

        $conn_pos->commit();
        $transactionStarted = false;

        respond([
            'success' => true,
            'message' => 'Cash count cancelled. Your shift is active again.',
            'shift_id' => $shiftId
        ]);
    }




    



    


    // Resume only after a cash count has been recorded for this pause.
    if ($action === 'resume') {
        $conn_pos->begin_transaction();
        $transactionStarted = true;

        $stmt = $conn_pos->prepare("
            SELECT Shift_ID, Paused_At
            FROM cashier_shifts
            WHERE User_ID = ?
            AND Status = 'Open'
            AND Is_Paused = 1
            ORDER BY Shift_ID DESC
            LIMIT 1
            FOR UPDATE
        ");

        $stmt->bind_param('i', $userId);
        $stmt->execute();
        $result = $stmt->get_result();
        $shift = $result->fetch_assoc();
        $stmt->close();

        if (!$shift) {
            throw new RuntimeException('There is no paused shift to resume.');
        }

        $shiftId = (int) $shift['Shift_ID'];
        $pausedAt = $shift['Paused_At'];

        $stmt = $conn_pos->prepare("
            SELECT Cash_Count_ID
            FROM cashier_shift_cash_counts
            WHERE Shift_ID = ?
            AND Counted_At >= ?
            LIMIT 1
        ");

        $stmt->bind_param('is', $shiftId, $pausedAt);
        $stmt->execute();
        $result = $stmt->get_result();
        $cashCount = $result->fetch_assoc();
        $stmt->close();

        if (!$cashCount) {
            throw new RuntimeException(
                'Save the cash count before resuming the shift.'
            );
        }

        $stmt = $conn_pos->prepare("
            UPDATE cashier_shifts
            SET Is_Paused = 0,
                Paused_At = NULL,
                Paused_By = NULL
            WHERE Shift_ID = ?
            AND User_ID = ?
            AND Status = 'Open'
            AND Is_Paused = 1
        ");

        $stmt->bind_param('ii', $shiftId, $userId);
        $stmt->execute();
        $updated = $stmt->affected_rows;
        $stmt->close();

        if ($updated !== 1) {
            throw new RuntimeException('Unable to resume the shift.');
        }

        $conn_pos->commit();
        $transactionStarted = false;

        respond([
            'success' => true,
            'message' => 'Shift resumed successfully.',
            'shift_id' => $shiftId
        ]);
    }








    

    /*
     * Close the current shift and save its cash reconciliation.
     */
    if ($action === 'close') {
        $rawActualCash = trim((string) ($_POST['actual_cash'] ?? ''));

        if (
            $rawActualCash === '' ||
            !is_numeric($rawActualCash) ||
            !is_finite((float) $rawActualCash) ||
            (float) $rawActualCash < 0 ||
            (float) $rawActualCash > 99999999.99
        ) {
            respond([
                'success' => false,
                'message' => 'Enter a valid physical cash count from ₱0 to ₱99,999,999.99.'
            ], 422);
        }

        $actualCash = round((float) $rawActualCash, 2);

        $conn_pos->begin_transaction();
        $transactionStarted = true;

        /*
         * Lock the open shift so checkout and closing cannot
         * both proceed against the same open shift concurrently.
         */
        $stmt = $conn_pos->prepare("
            SELECT Shift_ID, Opening_Float
            FROM cashier_shifts
            WHERE User_ID = ?
              AND Status = 'Open'
            ORDER BY Shift_ID DESC
            LIMIT 1
            FOR UPDATE
        ");

        $stmt->bind_param('i', $userId);
        $stmt->execute();

        $result = $stmt->get_result();
        $shift = $result->fetch_assoc();
        $stmt->close();

        if (!$shift) {
            $conn_pos->rollback();
            $transactionStarted = false;

            respond([
                'success' => false,
                'message' => 'There is no open shift to close. Refresh the POS.'
            ], 409);
        }

        $shiftId = (int) $shift['Shift_ID'];
        $openingFloat = (float) $shift['Opening_Float'];

        /*
         * Cash-only POS:
         * Each completed sale contributes its total_amount
         * to the drawer. Only sales assigned to this shift count.
         */
        $stmt = $conn_pos->prepare("
            SELECT COALESCE(SUM(total_amount), 0) AS ShiftSales
            FROM sales_test
            WHERE Shift_ID = ?
        ");

        $stmt->bind_param('i', $shiftId);
        $stmt->execute();

        $result = $stmt->get_result();
        $salesRow = $result->fetch_assoc();
        $stmt->close();

        $shiftSales = (float) ($salesRow['ShiftSales'] ?? 0);

        $expectedCash = round($openingFloat + $shiftSales, 2);
        $cashVariance = round($actualCash - $expectedCash, 2);

        $stmt = $conn_pos->prepare("
            UPDATE cashier_shifts
            SET Closed_At = NOW(),
                Expected_Cash = ?,
                Actual_Cash = ?,
                Cash_Variance = ?,
                Status = 'Closed',
                Closed_By = ?
            WHERE Shift_ID = ?
              AND User_ID = ?
              AND Status = 'Open'
        ");

        $stmt->bind_param(
            'dddiii',
            $expectedCash,
            $actualCash,
            $cashVariance,
            $userId,
            $shiftId,
            $userId
        );

        $stmt->execute();
        $updatedRows = $stmt->affected_rows;
        $stmt->close();

        if ($updatedRows !== 1) {
            throw new RuntimeException(
                'The shift could not be closed. Refresh and check its current status.'
            );
        }

        $conn_pos->commit();
        $transactionStarted = false;

        respond([
            'success' => true,
            'message' => 'Cashier shift closed successfully.',
            'shift_id' => $shiftId,
            'opening_float' => number_format($openingFloat, 2, '.', ''),
            'shift_sales' => number_format($shiftSales, 2, '.', ''),
            'expected_cash' => number_format($expectedCash, 2, '.', ''),
            'actual_cash' => number_format($actualCash, 2, '.', ''),
            'cash_variance' => number_format($cashVariance, 2, '.', '')
        ]);
    }



    // REVISION FOR REVIEW


    /*
     * Manager-only shift review and finalization.
     */
    if ($action === 'list_closed' || $action === 'finalize' || $action === 'list_cash_counts') {
        $accountType = strtolower(trim($_SESSION['accountType'] ?? ''));

        if (!in_array($accountType, ['manager', 'super admin'], true)) {
            respond([
                'success' => false,
                'message' => 'You are not authorized to review or finalize shifts.'
            ], 403);
        }

        if ($action === 'list_closed') {
            $stmt = $conn_pos->prepare("
                SELECT
                    cs.Shift_ID,
                    cs.User_ID,
                    u.userName AS CashierName,
                    cs.Opening_Float,
                    cs.Opened_At,
                    cs.Closed_At,
                    cs.Expected_Cash,
                    cs.Actual_Cash,
                    cs.Cash_Variance,
                    cs.Status,
                    cs.Closed_By,
                    cs.Finalized_By,
                    cs.Finalized_At,
                    COALESCE(s.ShiftSales, 0) AS ShiftSales
                FROM cashier_shifts cs
                LEFT JOIN login.users u
                    ON u.id = cs.User_ID
                LEFT JOIN (
                    SELECT Shift_ID, SUM(total_amount) AS ShiftSales
                    FROM sales_test
                    WHERE Shift_ID IS NOT NULL
                    GROUP BY Shift_ID
                ) s ON s.Shift_ID = cs.Shift_ID
                WHERE cs.Status IN ('Closed', 'Finalized')
                ORDER BY cs.Closed_At DESC, cs.Shift_ID DESC
            ");

            $stmt->execute();
            $result = $stmt->get_result();
            $shifts = [];

            while ($row = $result->fetch_assoc()) {
                $shifts[] = $row;
            }

            $stmt->close();

            respond([
                'success' => true,
                'shifts' => $shifts
            ]);
        }




        if ($action === 'list_cash_counts') {
            $stmt = $conn_pos->prepare("
                SELECT
                    cc.Cash_Count_ID,
                    cc.Shift_ID,
                    cc.Expected_Cash,
                    cc.Actual_Cash,
                    cc.Cash_Variance,
                    cc.Counted_By,
                    cc.Confirmed_By,
                    cc.Counted_At,
                    cc.Notes,
                    cashier.userName AS CashierName,
                    confirmer.userName AS ConfirmedByName
                FROM cashier_shift_cash_counts AS cc
                LEFT JOIN login.users AS cashier
                    ON cashier.id = cc.Counted_By
                LEFT JOIN login.users AS confirmer
                    ON confirmer.id = cc.Confirmed_By
                ORDER BY cc.Counted_At DESC, cc.Cash_Count_ID DESC
                LIMIT 500
            ");

            $stmt->execute();
            $result = $stmt->get_result();

            $cashCounts = [];

            while ($row = $result->fetch_assoc()) {
                $cashCounts[] = $row;
            }

            $stmt->close();

            respond([
                'success' => true,
                'cash_counts' => $cashCounts
            ]);
        }
        








        if ($action === 'finalize') {
            $rawShiftId = $_POST['shift_id'] ?? '';

            if (
                !is_scalar($rawShiftId) ||
                filter_var($rawShiftId, FILTER_VALIDATE_INT) === false ||
                (int) $rawShiftId <= 0
            ) {
                respond([
                    'success' => false,
                    'message' => 'Invalid shift ID.'
                ], 422);
            }

            $shiftId = (int) $rawShiftId;

            $conn_pos->begin_transaction();
            $transactionStarted = true;

            $stmt = $conn_pos->prepare("
                SELECT Shift_ID, Status
                FROM cashier_shifts
                WHERE Shift_ID = ?
                FOR UPDATE
            ");

            $stmt->bind_param('i', $shiftId);
            $stmt->execute();
            $result = $stmt->get_result();
            $shift = $result->fetch_assoc();
            $stmt->close();

            if (!$shift || $shift['Status'] !== 'Closed') {
                $conn_pos->rollback();
                $transactionStarted = false;

                respond([
                    'success' => false,
                    'message' => 'Only closed shifts can be finalized. Refresh the list and try again.'
                ], 409);
            }

            $stmt = $conn_pos->prepare("
                UPDATE cashier_shifts
                SET Status = 'Finalized',
                    Finalized_By = ?,
                    Finalized_At = NOW()
                WHERE Shift_ID = ?
                  AND Status = 'Closed'
            ");

            $stmt->bind_param('ii', $userId, $shiftId);
            $stmt->execute();
            $updatedRows = $stmt->affected_rows;
            $stmt->close();

            if ($updatedRows !== 1) {
                throw new RuntimeException(
                    'The shift could not be finalized.'
                );
            }

            $conn_pos->commit();
            $transactionStarted = false;

            respond([
                'success' => true,
                'message' => 'Shift finalized successfully.',
                'shift_id' => $shiftId
            ]);
        }
    }

    


    respond([
        'success' => false,
        'message' => 'Invalid shift action.'
    ], 400);

} catch (Throwable $e) {
    if ($transactionStarted) {
        try {
            $conn_pos->rollback();
        } catch (Throwable $ignored) {
            // Preserve the original error.
        }
    }

    error_log('Cashier shift error: ' . $e->getMessage());

    respond([
        'success' => false,
        'message' => 'Unable to process the cashier shift. Check the PHP error log.'
    ], 500);
}
