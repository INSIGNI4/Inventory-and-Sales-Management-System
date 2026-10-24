
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
    if (
        !isset($conn, $conn_pos) ||
        !($conn instanceof mysqli) ||
        !($conn_pos instanceof mysqli)
    ) {
        throw new RuntimeException('The login or point_of_sale database connection is unavailable.');
    }

    $details = reportGetRequestDetails();
    $dateFrom = $details['date_from'];
    $dateTo = $details['date_to'];

    $summarySql = "
        SELECT
            COUNT(*) AS transaction_count,
            COALESCE(SUM(total_amount), 0) AS total_revenue
        FROM sales_test
        WHERE date_created >= ?
          AND date_created < DATE_ADD(?, INTERVAL 1 DAY)
    ";

    $summaryStmt = $conn_pos->prepare($summarySql);

    if (!$summaryStmt) {
        throw new RuntimeException('Sales summary query preparation failed: ' . $conn_pos->error);
    }

    $summaryStmt->bind_param('ss', $dateFrom, $dateTo);

    if (!$summaryStmt->execute()) {
        throw new RuntimeException('Sales summary query execution failed: ' . $summaryStmt->error);
    }

    $summaryResult = $summaryStmt->get_result();
    $summary = $summaryResult->fetch_assoc();

    $transactionCount = (int)($summary['transaction_count'] ?? 0);
    $totalRevenue = (float)($summary['total_revenue'] ?? 0);

    $summaryResult->free();
    $summaryStmt->close();

    $unitsSql = "
        SELECT COALESCE(SUM(si.Quantity), 0) AS total_units
        FROM sales_item_test si
        INNER JOIN sales_test sh
            ON sh.Sales_ID = si.Sales_ID
        WHERE sh.date_created >= ?
          AND sh.date_created < DATE_ADD(?, INTERVAL 1 DAY)
    ";

    $unitsStmt = $conn_pos->prepare($unitsSql);

    if (!$unitsStmt) {
        throw new RuntimeException('Units sold query preparation failed: ' . $conn_pos->error);
    }

    $unitsStmt->bind_param('ss', $dateFrom, $dateTo);

    if (!$unitsStmt->execute()) {
        throw new RuntimeException('Units sold query execution failed: ' . $unitsStmt->error);
    }

    $unitsResult = $unitsStmt->get_result();
    $unitsRow = $unitsResult->fetch_assoc();
    $totalUnits = (float)($unitsRow['total_units'] ?? 0);

    $unitsResult->free();
    $unitsStmt->close();

    $productSql = "
        SELECT
            si.Product_ID,
            COALESCE(p.ProductName, 'Unknown Product') AS ProductName,
            COALESCE(SUM(si.Quantity), 0) AS UnitsSold,
            COALESCE(
                SUM(COALESCE(si.`Unit_Price`, 0) * COALESCE(si.Quantity, 0)),
                0
            ) AS ProductRevenue
        FROM sales_item_test si
        INNER JOIN sales_test sh
            ON sh.Sales_ID = si.Sales_ID
        LEFT JOIN login.product p
            ON p.Product_ID = si.Product_ID
        WHERE sh.date_created >= ?
          AND sh.date_created < DATE_ADD(?, INTERVAL 1 DAY)
        GROUP BY si.Product_ID, p.ProductName
        ORDER BY UnitsSold DESC, ProductRevenue DESC
    ";

    $productStmt = $conn_pos->prepare($productSql);

    if (!$productStmt) {
        throw new RuntimeException('Product sales query preparation failed: ' . $conn_pos->error);
    }

    $productStmt->bind_param('ss', $dateFrom, $dateTo);

    if (!$productStmt->execute()) {
        throw new RuntimeException('Product sales query execution failed: ' . $productStmt->error);
    }

    $productResult = $productStmt->get_result();

    $productHtml = '
    <h3>Product Sales Breakdown</h3>
    <table border="1" cellpadding="4">
        <tr style="font-weight:bold;background-color:#eeeeee;">
            <th width="12%">Product ID</th>
            <th width="48%">Product</th>
            <th width="20%">Units Sold</th>
            <th width="20%">Sales Revenue</th>
        </tr>';

    $productCount = 0;

    while ($row = $productResult->fetch_assoc()) {
        $productCount++;

        $productHtml .= '
        <tr>
            <td>' . reportEscape($row['Product_ID']) . '</td>
            <td>' . reportEscape($row['ProductName']) . '</td>
            <td align="right">' . number_format((float)$row['UnitsSold'], 0) . '</td>
            <td align="right">' . reportMoney($row['ProductRevenue']) . '</td>
        </tr>';
    }

    if ($productCount === 0) {
        $productHtml .= '
        <tr>
            <td colspan="4" align="center">No product sales were found for the selected date range.</td>
        </tr>';
    }

    $productHtml .= '</table>';

    $productResult->free();
    $productStmt->close();

    $dailySql = "
        SELECT
            DATE(date_created) AS SalesDate,
            COUNT(*) AS TransactionCount,
            COALESCE(SUM(total_amount), 0) AS DailyRevenue
        FROM sales_test
        WHERE date_created >= ?
          AND date_created < DATE_ADD(?, INTERVAL 1 DAY)
        GROUP BY DATE(date_created)
        ORDER BY DATE(date_created) ASC
    ";

    $dailyStmt = $conn_pos->prepare($dailySql);

    if (!$dailyStmt) {
        throw new RuntimeException('Daily sales query preparation failed: ' . $conn_pos->error);
    }

    $dailyStmt->bind_param('ss', $dateFrom, $dateTo);

    if (!$dailyStmt->execute()) {
        throw new RuntimeException('Daily sales query execution failed: ' . $dailyStmt->error);
    }

    $dailyResult = $dailyStmt->get_result();

    $dailyHtml = '
    <h3>Daily Sales Summary</h3>
    <table border="1" cellpadding="4">
        <tr style="font-weight:bold;background-color:#eeeeee;">
            <th width="40%">Date</th>
            <th width="30%">Transactions</th>
            <th width="30%">Sales Revenue</th>
        </tr>';

    $dailyCount = 0;

    while ($row = $dailyResult->fetch_assoc()) {
        $dailyCount++;

        $dailyDate = !empty($row['SalesDate'])
            ? date('M j, Y', strtotime($row['SalesDate']))
            : 'N/A';

        $dailyHtml .= '
        <tr>
            <td>' . reportEscape($dailyDate) . '</td>
            <td align="right">' . number_format((int)$row['TransactionCount']) . '</td>
            <td align="right">' . reportMoney($row['DailyRevenue']) . '</td>
        </tr>';
    }

    if ($dailyCount === 0) {
        $dailyHtml .= '
        <tr>
            <td colspan="3" align="center">No sales transactions were found for the selected date range.</td>
        </tr>';
    }

    $dailyHtml .= '</table>';

    $dailyResult->free();
    $dailyStmt->close();

    $html = '
    <table border="1" cellpadding="6">
        <tr style="font-weight:bold;background-color:#eeeeee;">
            <th width="50%">Metric</th>
            <th width="50%">Value</th>
        </tr>
        <tr>
            <td>Total Sales Revenue</td>
            <td align="right">' . reportMoney($totalRevenue) . '</td>
        </tr>
        <tr>
            <td>Total Transactions</td>
            <td align="right">' . number_format($transactionCount) . '</td>
        </tr>
        <tr>
            <td>Total Units Sold</td>
            <td align="right">' . number_format($totalUnits, 0) . '</td>
        </tr>
    </table>
    <br>' .
    $productHtml .
    '<br>' .
    $dailyHtml .
    '<br>
    <p><i>Total sales revenue and transaction count are based on sales headers dated within the selected period. Product sales and units sold are calculated from the corresponding sales items. This report summarizes sales and does not calculate profit.</i></p>';

    reportCreatePdf(
        'SALES SUMMARY REPORT',
        $dateFrom,
        $dateTo,
        $details['generated_by'],
        $details['generated_at'],
        $details['recipient'],
        $html,
        'sales_summary_report.pdf'
    );

} catch (Throwable $e) {
    error_log('Sales Summary Report Error: ' . $e->getMessage());

    reportFail(
        'Unable to generate the Sales Summary Report. Check the server error log for details.',
        500
    );
}
