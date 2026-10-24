
<?php
date_default_timezone_set('Asia/Manila');

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

require_once __DIR__ . '/../connect.php';

$accountType = strtolower(trim($_SESSION['accountType'] ?? ''));
$userId = (int) ($_SESSION['user_id'] ?? 0);

if (
    $userId <= 0 ||
    !in_array($accountType, ['manager', 'super admin'], true)
) {
    http_response_code($userId <= 0 ? 401 : 403);
    exit('You are not authorized to view this report. Please log in with an authorized account.');
}

$rawShiftId = $_GET['shift_id'] ?? '';

if (
    !is_scalar($rawShiftId) ||
    filter_var($rawShiftId, FILTER_VALIDATE_INT) === false ||
    (int) $rawShiftId <= 0
) {
    http_response_code(400);
    exit('Invalid shift ID.');
}

$shiftId = (int) $rawShiftId;

function h($value): string
{
    return htmlspecialchars(
        (string) ($value ?? ''),
        ENT_QUOTES,
        'UTF-8'
    );
}

function money($value): string
{
    return '₱' . number_format((float) ($value ?? 0), 2);
}

function displayDate($value): string
{
    if (!$value) {
        return '—';
    }

    $timestamp = strtotime((string) $value);

    return $timestamp === false
        ? '—'
        : date('M d, Y h:i A', $timestamp);
}

try {
    $stmt = $conn_pos->prepare("
        SELECT
            cs.Shift_ID,
            cs.User_ID,
            cashier.userName AS CashierName,
            cs.Opening_Float,
            cs.Opened_At,
            cs.Closed_At,
            cs.Expected_Cash,
            cs.Actual_Cash,
            cs.Cash_Variance,
            cs.Status,
            cs.Closed_By,
            closer.userName AS ClosedByName,
            cs.Finalized_By,
            finalizer.userName AS FinalizedByName,
            cs.Finalized_At,
            cs.Notes,
            COALESCE(s.ShiftSales, 0) AS ShiftSales
        FROM cashier_shifts cs
        LEFT JOIN login.users cashier
            ON cashier.id = cs.User_ID
        LEFT JOIN login.users closer
            ON closer.id = cs.Closed_By
        LEFT JOIN login.users finalizer
            ON finalizer.id = cs.Finalized_By
        LEFT JOIN (
            SELECT
                Shift_ID,
                SUM(total_amount) AS ShiftSales
            FROM sales_test
            WHERE Shift_ID IS NOT NULL
            GROUP BY Shift_ID
        ) s
            ON s.Shift_ID = cs.Shift_ID
        WHERE cs.Shift_ID = ?
          AND cs.Status IN ('Closed', 'Finalized')
        LIMIT 1
    ");

    $stmt->bind_param('i', $shiftId);
    $stmt->execute();

    $result = $stmt->get_result();
    $shift = $result->fetch_assoc();

    $stmt->close();

    if (!$shift) {
        http_response_code(404);
        exit('The requested shift was not found, or it has not been closed yet.');
    }
} catch (Throwable $e) {
    error_log('Z-Report error: ' . $e->getMessage());

    http_response_code(500);
    exit('Unable to generate the Z-Report. Check the PHP error log.');
}

$variance = (float) ($shift['Cash_Variance'] ?? 0);

$varianceLabel = $variance > 0
    ? 'Over'
    : ($variance < 0 ? 'Short' : 'Balanced');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Z-Report - Shift #<?= h($shift['Shift_ID']) ?></title>

    <style>
        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            padding: 30px 16px;
            background: #f3f4f6;
            color: #222;
            font-family: Arial, Helvetica, sans-serif;
            font-size: 14px;
        }

        .report {
            width: 100%;
            max-width: 820px;
            margin: 0 auto;
            padding: 35px;
            background: #fff;
            border: 1px solid #ddd;
            border-radius: 8px;
        }

        .report-header {
            padding-bottom: 22px;
            margin-bottom: 24px;
            text-align: center;
            border-bottom: 2px solid #263b55;
        }

        .report-header h1 {
            margin: 0 0 8px;
            font-size: 27px;
            letter-spacing: 1px;
        }

        .report-header p {
            margin: 5px 0;
            color: #555;
        }

        .report-section {
            margin-bottom: 25px;
        }

        .report-section h2 {
            padding-bottom: 9px;
            margin: 0 0 12px;
            color: #263b55;
            font-size: 16px;
            border-bottom: 1px solid #ddd;
        }

        .details {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 12px 24px;
        }

        .detail {
            min-width: 0;
        }

        .detail-label {
            display: block;
            margin-bottom: 4px;
            color: #666;
            font-size: 12px;
        }

        .detail-value {
            font-weight: 600;
            overflow-wrap: anywhere;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th, td {
            padding: 12px 10px;
            border-bottom: 1px solid #e5e7eb;
            text-align: left;
        }

        th {
            background: #f5f6f8;
            font-weight: 600;
        }

        td.amount, th.amount {
            text-align: right;
            white-space: nowrap;
        }

        .variance {
            font-weight: 700;
        }

        .variance.over {
            color: #15803d;
        }

        .variance.short {
            color: #dc2626;
        }

        .variance.balanced {
            color: #374151;
        }

        .status {
            display: inline-block;
            padding: 5px 10px;
            border-radius: 5px;
            background: #eef2ff;
            color: #263b55;
            font-weight: 700;
        }

        .report-notes {
            min-height: 35px;
            white-space: pre-wrap;
            overflow-wrap: anywhere;
        }

        .report-footer {
            padding-top: 18px;
            margin-top: 25px;
            border-top: 1px solid #ddd;
            color: #666;
            font-size: 12px;
            text-align: center;
        }

        .report-actions {
            display: flex;
            justify-content: center;
            gap: 10px;
            margin: 20px auto;
        }

        .report-actions button {
            padding: 10px 18px;
            border: 0;
            border-radius: 5px;
            background: #263b55;
            color: #fff;
            cursor: pointer;
            font-weight: 600;
        }

        .report-actions .back-button {
            background: #6b7280;
        }

        @media (max-width: 600px) {
            body {
                padding: 12px;
            }

            .report {
                padding: 20px 15px;
            }

            .details {
                grid-template-columns: 1fr;
            }

            th, td {
                padding: 9px 6px;
            }
        }

        @media print {
            @page {
                size: A4;
                margin: 14mm;
            }

            body {
                padding: 0;
                background: #fff;
                font-size: 11px;
                background-image: url("../bglogin.png");
                background-repeat: no-repeat;
                background-size: cover;
            }

            .report {
                max-width: none;
                padding: 0;
                border: 0;
                border-radius: 0;
            }

            .report-actions {
                display: none !important;
            }

            .report-section {
                break-inside: avoid;
            }

            th {
                background: #eee !important;
                print-color-adjust: exact;
                -webkit-print-color-adjust: exact;
            }
        }
    </style>
</head>
<body style="background-image: url(../bglogin.png);background-repeat: no-repeat;background-size: cover;">

<div class="report" style="opacity: 95%;">

    <header class="report-header">
        <h1>Z-REPORT</h1>
        <p>Cashier Shift Reconciliation</p>
        <p>
            Shift #<?= h($shift['Shift_ID']) ?>
            &nbsp;|&nbsp;
            Generated <?= h(date('M d, Y h:i A')) ?>
        </p>
    </header>

    <section class="report-section">
        <h2>Shift Information</h2>

        <div class="details">
            <div class="detail">
                <span class="detail-label">Shift ID</span>
                <span class="detail-value">
                    #<?= h($shift['Shift_ID']) ?>
                </span>
            </div>

            <div class="detail">
                <span class="detail-label">Cashier</span>
                <span class="detail-value">
                    <?= h($shift['CashierName'] ?: 'Unknown') ?>
                </span>
            </div>

            <div class="detail">
                <span class="detail-label">Date and Time Opened</span>
                <span class="detail-value">
                    <?= h(displayDate($shift['Opened_At'])) ?>
                </span>
            </div>

            <div class="detail">
                <span class="detail-label">Date and Time Closed</span>
                <span class="detail-value">
                    <?= h(displayDate($shift['Closed_At'])) ?>
                </span>
            </div>

            <div class="detail">
                <span class="detail-label">Shift Status</span>
                <span class="status"><?= h($shift['Status']) ?></span>
            </div>

            <div class="detail">
                <span class="detail-label">Closed By</span>
                <span class="detail-value">
                    <?= h($shift['ClosedByName'] ?: 'Unknown') ?>
                </span>
            </div>
        </div>
    </section>

    <section class="report-section">
        <h2>Cash Reconciliation</h2>

        <table>
            <thead>
                <tr>
                    <th>Item</th>
                    <th class="amount">Amount</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>Opening Float</td>
                    <td class="amount"><?= h(money($shift['Opening_Float'])) ?></td>
                </tr>

                <tr>
                    <td>Total Cash Sales Assigned to Shift</td>
                    <td class="amount"><?= h(money($shift['ShiftSales'])) ?></td>
                </tr>

                <tr>
                    <td><strong>Expected Cash</strong></td>
                    <td class="amount">
                        <strong><?= h(money($shift['Expected_Cash'])) ?></strong>
                    </td>
                </tr>

                <tr>
                    <td>Actual Cash Counted</td>
                    <td class="amount"><?= h(money($shift['Actual_Cash'])) ?></td>
                </tr>

                <tr>
                    <td>
                        <strong>Cash Variance (<?= h($varianceLabel) ?>)</strong>
                    </td>
                    <td class="amount variance <?= $variance > 0 ? 'over' : ($variance < 0 ? 'short' : 'balanced') ?>">
                        <?= $variance > 0 ? '+' : '' ?><?= h(money($variance)) ?>
                    </td>
                </tr>
            </tbody>
        </table>

        <p>
            <strong>Variance interpretation:</strong>
            Over means actual cash is higher than expected;
            Short means actual cash is lower than expected;
            Balanced means the amounts match.
        </p>
    </section>

    <section class="report-section">
        <h2>Manager Review and Finalization</h2>

        <div class="details">
            <div class="detail">
                <span class="detail-label">Finalized By</span>
                <span class="detail-value">
                    <?= h($shift['FinalizedByName'] ?: 'Not finalized') ?>
                </span>
            </div>

            <div class="detail">
                <span class="detail-label">Finalized At</span>
                <span class="detail-value">
                    <?= h(displayDate($shift['Finalized_At'])) ?>
                </span>
            </div>
        </div>
    </section>

    <section class="report-section">
        <h2>Shift Notes</h2>
        <div class="report-notes"><?= h($shift['Notes'] ?: 'No notes recorded.') ?></div>
    </section>

    <footer class="report-footer">
        End of Z-Report · Shift #<?= h($shift['Shift_ID']) ?>
    </footer>


    <div class="report-actions">
    <button type="button" class="btn btn-danger" onclick="window.print()">
        Print Report
    </button>

    <button type="button" class="back-button" onclick="window.close()">
        Close Report
    </button>
</div>

</div>

<!-- <div class="report-actions">
    <button type="button" onclick="window.print()">
        Print Report
    </button>

    <button type="button" class="back-button" onclick="window.close()">
        Close Report
    </button>
</div> -->

</body>
</html>
