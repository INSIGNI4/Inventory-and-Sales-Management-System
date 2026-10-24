
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
            r.Product_ID,
            p.ProductName,
            s.CompanyName,
            r.Quantity,
            r.PurchaseUnitCost,
            COALESCE(r.Quantity, 0) * COALESCE(r.PurchaseUnitCost, 0) AS OrderCost,
            r.Status
        FROM restock r
        LEFT JOIN product p
            ON p.Product_ID = r.Product_ID
        LEFT JOIN supplier s
            ON s.Supplier_ID = r.Supplier_ID
        WHERE r.OrderDate >= ?
          AND r.OrderDate < DATE_ADD(?, INTERVAL 1 DAY)
        ORDER BY r.OrderDate DESC, r.Orestock_ID DESC
    ";

    $stmt = $conn->prepare($sql);

    if (!$stmt) {
        throw new RuntimeException('Purchase query preparation failed: ' . $conn->error);
    }

    $stmt->bind_param('ss', $dateFrom, $dateTo);

    if (!$stmt->execute()) {
        throw new RuntimeException('Purchase query execution failed: ' . $stmt->error);
    }

    $result = $stmt->get_result();

    $totalOrdered = 0;
    $totalCost = 0;
    $recordCount = 0;

    $html = '
    <table border="1" cellpadding="4">
        <tr style="font-weight:bold;background-color:#eeeeee;">
            <th width="8%">Order ID</th>
            <th width="13%">Order Date</th>
            <th width="20%">Product</th>
            <th width="17%">Supplier</th>
            <th width="9%">Ordered</th>
            <th width="13%">Unit Cost</th>
            <th width="12%">Order Cost</th>
            <th width="8%">Status</th>
        </tr>';

    while ($row = $result->fetch_assoc()) {
        $quantity = (float)($row['Quantity'] ?? 0);
        $unitCost = (float)($row['PurchaseUnitCost'] ?? 0);
        $orderCost = (float)($row['OrderCost'] ?? 0);

        $totalOrdered += $quantity;
        $totalCost += $orderCost;
        $recordCount++;

        $orderDate = !empty($row['OrderDate'])
            ? date('M j, Y', strtotime($row['OrderDate']))
            : 'N/A';

        $html .= '
        <tr>
            <td>' . reportEscape($row['Orestock_ID']) . '</td>
            <td>' . reportEscape($orderDate) . '</td>
            <td>' . reportEscape($row['ProductName'] ?? 'Unknown Product') . '</td>
            <td>' . reportEscape($row['CompanyName'] ?? 'Unknown Supplier') . '</td>
            <td align="right">' . number_format($quantity, 0) . '</td>
            <td align="right">' . reportMoney($unitCost) . '</td>
            <td align="right">' . reportMoney($orderCost) . '</td>
            <td>' . reportEscape($row['Status'] ?? 'N/A') . '</td>
        </tr>';
    }

    if ($recordCount === 0) {
        $html .= '
        <tr>
            <td colspan="8" align="center">No purchase orders were found for the selected date range.</td>
        </tr>';
    }

    $html .= '
        <tr style="font-weight:bold;background-color:#eeeeee;">
            <td colspan="4">TOTAL</td>
            <td align="right">' . number_format($totalOrdered, 0) . '</td>
            <td></td>
            <td align="right">' . reportMoney($totalCost) . '</td>
            <td></td>
        </tr>
    </table>
    <p><i>Order cost is calculated as ordered quantity multiplied by PurchaseUnitCost per unit. Only orders placed within the selected date range are included.</i></p>';

    $stmt->close();

    reportCreatePdf(
        'PURCHASE ORDER REPORT',
        $dateFrom,
        $dateTo,
        $details['generated_by'],
        $details['generated_at'],
        $details['recipient'],
        $html,
        'purchase_order_report.pdf'
    );

} catch (Throwable $e) {
    error_log('Purchase Order Report Error: ' . $e->getMessage());

    reportFail(
        'Unable to generate the Purchase Order Report. Check the server error log for details.',
        500
    );
}
