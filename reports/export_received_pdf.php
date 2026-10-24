
<?php
ob_start();

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

date_default_timezone_set('Asia/Manila');

require_once __DIR__ . '/../connect.php';
require_once __DIR__ . '/../tcpdf/tcpdf.php';
require_once __DIR__ . '/report_pdf_helper.php';

try {
    if (!isset($conn) || !($conn instanceof mysqli)) {
        throw new RuntimeException('The login database connection is unavailable.');
    }

    $details = reportGetRequestDetails();
    $dateFrom = $details['date_from'];
    $dateTo = $details['date_to'];

    $sql = "
        SELECT
            r.Orestock_ID,
            r.OrderDate,
            r.Date_Received,
            r.Product_ID,
            p.ProductName,
            s.CompanyName,
            r.Quantity,
            r.TotalReceived,
            r.PurchaseUnitCost,
            COALESCE(r.TotalReceived, 0) *
                COALESCE(r.PurchaseUnitCost, 0) AS ReceivedValue,
            r.Status,
            r.DeliveryStatus
        FROM restock r
        LEFT JOIN product p
            ON p.Product_ID = r.Product_ID
        LEFT JOIN supplier s
            ON s.Supplier_ID = r.Supplier_ID
        WHERE r.Date_Received >= ?
          AND r.Date_Received < DATE_ADD(?, INTERVAL 1 DAY)
        ORDER BY r.Date_Received DESC, r.Orestock_ID DESC
    ";

    $stmt = $conn->prepare($sql);

    if (!$stmt) {
        throw new RuntimeException('Received stock query preparation failed: ' . $conn->error);
    }

    $stmt->bind_param('ss', $dateFrom, $dateTo);

    if (!$stmt->execute()) {
        throw new RuntimeException('Received stock query execution failed: ' . $stmt->error);
    }

    $result = $stmt->get_result();

    $totalOrdered = 0;
    $totalReceived = 0;
    $totalValue = 0;
    $recordCount = 0;

    $html = '
    <table border="1" cellpadding="4">
        <tr style="font-weight:bold;background-color:#eeeeee;">
            <th width="8%">Order ID</th>
            <th width="12%">Received Date</th>
            <th width="20%">Product</th>
            <th width="16%">Supplier</th>
            <th width="9%">Ordered</th>
            <th width="9%">Received</th>
            <th width="12%">Unit Cost</th>
            <th width="14%">Received Value</th>
        </tr>';

    while ($row = $result->fetch_assoc()) {
        $ordered = (float)($row['Quantity'] ?? 0);
        $received = (float)($row['TotalReceived'] ?? 0);
        $unitCost = (float)($row['PurchaseUnitCost'] ?? 0);
        $receivedValue = (float)($row['ReceivedValue'] ?? 0);

        $totalOrdered += $ordered;
        $totalReceived += $received;
        $totalValue += $receivedValue;
        $recordCount++;

        $receivedDate = !empty($row['Date_Received'])
            ? date('M j, Y', strtotime($row['Date_Received']))
            : 'N/A';

        $html .= '
        <tr>
            <td>' . reportEscape($row['Orestock_ID']) . '</td>
            <td>' . reportEscape($receivedDate) . '</td>
            <td>' . reportEscape($row['ProductName'] ?? 'Unknown Product') . '</td>
            <td>' . reportEscape($row['CompanyName'] ?? 'Unknown Supplier') . '</td>
            <td align="right">' . number_format($ordered, 0) . '</td>
            <td align="right">' . number_format($received, 0) . '</td>
            <td align="right">' . reportMoney($unitCost) . '</td>
            <td align="right">' . reportMoney($receivedValue) . '</td>
        </tr>';
    }

    if ($recordCount === 0) {
        $html .= '
        <tr>
            <td colspan="8" align="center">No stock receipts were found for the selected date range.</td>
        </tr>';
    }

    $html .= '
        <tr style="font-weight:bold;background-color:#eeeeee;">
            <td colspan="4">TOTAL</td>
            <td align="right">' . number_format($totalOrdered, 0) . '</td>
            <td align="right">' . number_format($totalReceived, 0) . '</td>
            <td></td>
            <td align="right">' . reportMoney($totalValue) . '</td>
        </tr>
    </table>
    <p><i>Received stock value is calculated as actual received quantity multiplied by PurchaseUnitCost per unit. Only stock with a receiving date within the selected date range is included.</i></p>';

    $stmt->close();

    reportCreatePdf(
        'STOCK RECEIVED REPORT',
        $dateFrom,
        $dateTo,
        $details['generated_by'],
        $details['generated_at'],
        $details['recipient'],
        $html,
        'stock_received_report.pdf'
    );

} catch (Throwable $e) {
    error_log('Stock Received Report Error: ' . $e->getMessage());

    reportFail(
        'Unable to generate the Stock Received Report. Check the server error log for details.',
        500
    );
}
