
<?php

ob_start();

ini_set('display_errors', '0');
error_reporting(E_ALL);

date_default_timezone_set('Asia/Manila');

include_once __DIR__ . '/../connect.php';
require_once __DIR__ . '/../tcpdf/tcpdf.php';

$reportTitle = 'NEXT 2 WEEKS DEMAND FORECAST & REORDERING RECOMMENDATION';

$firstForecastDate = date('Y-m-d', strtotime('tomorrow'));
$lastDate = date('Y-m-d', strtotime($firstForecastDate . ' +13 days'));

$predictionQuery = "
    SELECT
        Product_ID,
        ProductName,
        Category,
        SUM(Predicted_Demand) AS Predicted_Demand,
        MIN(ForecastDate) AS ForecastStart,
        MAX(ForecastDate) AS ForecastEnd
    FROM ml_predictions
    WHERE ForecastDate BETWEEN '$firstForecastDate' AND '$lastDate'
    GROUP BY Product_ID, ProductName, Category
    ORDER BY ProductName ASC
";

$predictionResult = mysqli_query($conn_pos, $predictionQuery);

$forecastData = [];

if ($predictionResult) {
    while ($row = mysqli_fetch_assoc($predictionResult)) {

        $productID = mysqli_real_escape_string(
            $conn,
            (string)$row['Product_ID']
        );

        $inventoryQuery = "
            SELECT COALESCE(SUM(Inventory), 0) AS Current_Stock
            FROM inventory
            WHERE Product_ID = '$productID'
        ";

        $inventoryResult = mysqli_query($conn, $inventoryQuery);
        $currentStock = 0;

        if ($inventoryResult) {
            $inventoryRow = mysqli_fetch_assoc($inventoryResult);
            $currentStock = (float)($inventoryRow['Current_Stock'] ?? 0);
        }

        $packSize = 1;

        $packQuery = "
            SELECT Pack_Size
            FROM item_to_order
            WHERE Product_ID = '$productID'
            ORDER BY ItemToOrder_ID DESC
            LIMIT 1
        ";

        $packResult = mysqli_query($conn, $packQuery);

        if ($packResult && mysqli_num_rows($packResult) > 0) {
            $packRow = mysqli_fetch_assoc($packResult);

            if (
                isset($packRow['Pack_Size']) &&
                is_numeric($packRow['Pack_Size']) &&
                (float)$packRow['Pack_Size'] > 0
            ) {
                $packSize = (float)$packRow['Pack_Size'];
            }
        }

        $predictedDemand = (float)$row['Predicted_Demand'];

        $netDemand = max(0, $predictedDemand - $currentStock);

        if ($packSize > 1) {
            $recommendedOrder = ceil($netDemand / $packSize) * $packSize;
        } else {
            $recommendedOrder = ceil($netDemand);
        }

        $row['Current_Stock'] = $currentStock;
        $row['Predicted_Demand'] = $predictedDemand;
        $row['Net_Demand'] = $netDemand;
        $row['Pack_Size'] = $packSize;
        $row['Recommended_Order_Quantity'] = $recommendedOrder;
        $row['Status'] = $recommendedOrder > 0
            ? 'Recommended'
            : 'Stock Sufficient';

        $forecastData[] = $row;
    }
}

$totalProducts = count($forecastData);
$totalPredictedDemand = 0;
$totalRecommended = 0;

foreach ($forecastData as $row) {
    $totalPredictedDemand += $row['Predicted_Demand'];
    $totalRecommended += $row['Recommended_Order_Quantity'];
}

$pdf = new TCPDF('L', 'mm', 'A4', true, 'UTF-8', false);

$pdf->SetCreator('Inventory and Sales Management System');
$pdf->SetAuthor('Inventory and Sales Management System');
$pdf->SetTitle($reportTitle);

$pdf->SetMargins(10, 10, 10);
$pdf->SetAutoPageBreak(true, 15);
$pdf->AddPage('L');

$pdf->SetFont('helvetica', 'B', 16);
$pdf->Cell(0, 10, $reportTitle, 0, 1, 'C');

$pdf->SetFont('helvetica', '', 10);

$startFormatted = date('M d, Y', strtotime($firstForecastDate));
$endFormatted = date('M d, Y', strtotime($lastDate));

$pdf->Cell(
    0,
    7,
    'Forecast Period: ' . $startFormatted . ' - ' . $endFormatted,
    0,
    1,
    'C'
);

$pdf->Ln(5);

$summary = '
<table border="1" cellpadding="6">
    <tr>
        <td width="33%">
            <b>Products Forecasted</b><br />
            ' . $totalProducts . '
        </td>
        <td width="33%">
            <b>Total Predicted Demand</b><br />
            ' . number_format($totalPredictedDemand, 2) . '
        </td>
        <td width="34%">
            <b>Total Recommended Order</b><br />
            ' . number_format($totalRecommended, 2) . '
        </td>
    </tr>
</table>
';

$pdf->writeHTML($summary, true, false, true, false, '');
$pdf->Ln(5);

$html = '
<table border="1" cellpadding="4">
    <tr style="background-color:#f2f2f2;">
        <th width="8%">Product ID</th>
        <th width="20%">Product Name</th>
        <th width="11%">Category</th>
        <th width="13%">Predicted Demand</th>
        <th width="11%">Current Stock</th>
        <th width="10%">Net Demand</th>
        <th width="13%">Recommended Order</th>
        <th width="7%">Pack Size</th>
        <th width="7%">Status</th>
    </tr>
';

if (!empty($forecastData)) {
    foreach ($forecastData as $row) {
        $html .= '
        <tr>
            <td>' . htmlspecialchars((string)$row['Product_ID'], ENT_QUOTES, 'UTF-8') . '</td>
            <td>' . htmlspecialchars((string)$row['ProductName'], ENT_QUOTES, 'UTF-8') . '</td>
            <td>' . htmlspecialchars((string)$row['Category'], ENT_QUOTES, 'UTF-8') . '</td>
            <td>' . number_format($row['Predicted_Demand'], 2) . '</td>
            <td>' . number_format($row['Current_Stock'], 0) . '</td>
            <td>' . number_format($row['Net_Demand'], 2) . '</td>
            <td>' . number_format($row['Recommended_Order_Quantity'], 0) . '</td>
            <td>' . number_format($row['Pack_Size'], 0) . '</td>
            <td>' . htmlspecialchars($row['Status'], ENT_QUOTES, 'UTF-8') . '</td>
        </tr>
        ';
    }
} else {
    $html .= '
    <tr>
        <td colspan="9" align="center">
            No demand predictions were found for the next 2 weeks.
        </td>
    </tr>
    ';
}

$html .= '</table>';

$pdf->SetFont('helvetica', '', 8);
$pdf->writeHTML($html, true, false, true, false, '');

$pdf->Ln(5);
$pdf->SetFont('helvetica', 'I', 8);

$pdf->MultiCell(
    0,
    5,
    'The predicted demand is based on the machine learning forecast from tomorrow through the next 14 days. The recommended order quantity is calculated using predicted demand, current inventory, and available pack size.',
    0,
    'L'
);

/* Signatories */
$pdf->Ln(10);
$pdf->SetFont('helvetica', '', 9);

$signatoryHTML = '
<table cellpadding="5" cellspacing="0" border="0">
    <tr>
        <td width="33%" align="center">
            ___________________________<br />
            <b>Prepared by:</b><br />
            Signature Above Name
        </td>
        <td width="34%" align="center">
            ___________________________<br />
            <b>Checked by:</b><br />
            Signature Above Name
        </td>
        <td width="33%" align="center">
            ___________________________<br />
            <b>Approved by:</b><br />
            Signature Above Name
        </td>
    </tr>
</table>
';

$pdf->writeHTML($signatoryHTML, true, false, true, false, '');

/* Clear buffered output before sending the PDF */
while (ob_get_level() > 0) {
    ob_end_clean();
}

$pdf->Output('demand_forecast_reorder_next_2_weeks.pdf', 'I');
exit;
