
<?php

header('Content-Type: application/json');

require_once __DIR__ . '/connect.php';

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

try {
    /*
    |--------------------------------------------------------------------------
    | AI RECOMMENDATIONS - CURRENT 14-DAY FORECAST
    |--------------------------------------------------------------------------
    | Uses:
    | - Future predictions for the next 14 days
    | - Current physical stock from login.expiration
    | - Active supplier offers from login.product_supplier
    | - Lowest-priced active offer as the default supplier
    | - Pack-size multiples for recommended quantities
    |--------------------------------------------------------------------------
    */

    $sql = "
        SELECT
            p.Product_ID,
            p.ProductName,
            p.Pack_Size,

            ps.ProductSupplier_ID,
            ps.Supplier_ID,
            ps.SupplierPrice,

            s.CompanyName AS SupplierName,

            CONCAT_WS(
                ', ',
                NULLIF(s.Address, ''),
                NULLIF(s.City, '')
            ) AS SupplierLocation,

            s.PhoneNumber,
            s.Email,

            COALESCE(inv.CurrentStock, 0) AS CurrentStock,

            ROUND(
                SUM(mp.Predicted_Demand),
                2
            ) AS PredictedDemand

        FROM point_of_sale.ml_predictions mp

        INNER JOIN login.product p
            ON p.Product_ID = mp.Product_ID

        /*
        |--------------------------------------------------------------------------
        | SELECT ONE ACTIVE SUPPLIER OFFER PER PRODUCT
        |--------------------------------------------------------------------------
        | Lowest purchase price wins.
        | ProductSupplier_ID breaks ties consistently.
        |--------------------------------------------------------------------------
        */

        INNER JOIN login.product_supplier ps
            ON ps.ProductSupplier_ID = (
                SELECT ps2.ProductSupplier_ID
                FROM login.product_supplier ps2
                WHERE
                    ps2.Product_ID = p.Product_ID
                    AND ps2.IsActive = 1
                ORDER BY
                    ps2.SupplierPrice ASC,
                    ps2.ProductSupplier_ID ASC
                LIMIT 1
            )

        INNER JOIN login.supplier s
            ON s.Supplier_ID = ps.Supplier_ID

        /*
        |--------------------------------------------------------------------------
        | CURRENT PHYSICAL STOCK
        |--------------------------------------------------------------------------
        */

        LEFT JOIN (
            SELECT
                Product_ID,
                SUM(Quantity) AS CurrentStock
            FROM login.expiration
            GROUP BY Product_ID
        ) inv
            ON inv.Product_ID = p.Product_ID

        /*
        |--------------------------------------------------------------------------
        | CURRENT 14-DAY FORECAST
        |--------------------------------------------------------------------------
        */

        WHERE
            mp.ForecastDate > CURDATE()
            AND mp.ForecastDate <= DATE_ADD(
                CURDATE(),
                INTERVAL 14 DAY
            )

            /*
            |--------------------------------------------------------------------------
            | DO NOT SHOW PRODUCTS ALREADY BEING HANDLED BY RESTOCK
            |--------------------------------------------------------------------------
            */

            AND NOT EXISTS (
                SELECT 1
                FROM login.restock r
                WHERE
                    r.Product_ID = p.Product_ID
                    AND r.Status IN (
                        'Requested',
                        'Partially-Received',
                        'Backorder-Fulfillment'
                    )
            )

        GROUP BY
            p.Product_ID,
            p.ProductName,
            p.Pack_Size,
            ps.ProductSupplier_ID,
            ps.Supplier_ID,
            ps.SupplierPrice,
            s.CompanyName,
            s.Address,
            s.City,
            s.PhoneNumber,
            s.Email,
            inv.CurrentStock

        /*
        |--------------------------------------------------------------------------
        | ONLY PRODUCTS WITH A SHORTAGE
        |--------------------------------------------------------------------------
        */

        HAVING
            PredictedDemand > COALESCE(inv.CurrentStock, 0)

        ORDER BY
            (
                PredictedDemand
                - COALESCE(inv.CurrentStock, 0)
            ) DESC
    ";

    $result = mysqli_query($conn, $sql);

    $data = [];
    $estimatedGrandTotal = 0.0;

    while ($row = mysqli_fetch_assoc($result)) {

        $currentStock = (float) $row['CurrentStock'];
        $predictedDemand = (float) $row['PredictedDemand'];

        /*
        |--------------------------------------------------------------------------
        | NET DEMAND
        |--------------------------------------------------------------------------
        */

        $netDemand = $predictedDemand - $currentStock;

        /*
        |--------------------------------------------------------------------------
        | PACK SIZE
        |--------------------------------------------------------------------------
        */

        $packSize = (int) $row['Pack_Size'];

        if ($packSize <= 0) {
            $packSize = 1;
        }

        /*
        |--------------------------------------------------------------------------
        | RECOMMENDED QUANTITY
        |--------------------------------------------------------------------------
        */

        $recommendedQuantity =
            (int) (
                ceil($netDemand / $packSize) * $packSize
            );

        /*
        |--------------------------------------------------------------------------
        | SUPPLIER PURCHASE PRICE AND ESTIMATED COST
        |--------------------------------------------------------------------------
        */

        $unitCost = (float) $row['SupplierPrice'];

        $estimatedTotal = round(
            $recommendedQuantity * $unitCost,
            2
        );

        $estimatedGrandTotal = round(
            $estimatedGrandTotal + $estimatedTotal,
            2
        );

        /*
        |--------------------------------------------------------------------------
        | RESPONSE DATA
        |--------------------------------------------------------------------------
        */

        $data[] = [
            'productId' => (int) $row['Product_ID'],
            'productName' => $row['ProductName'],

            'productSupplierId' =>
                (int) $row['ProductSupplier_ID'],

            'supplierId' =>
                (int) $row['Supplier_ID'],

            'supplierName' =>
                $row['SupplierName'] ?? 'No Supplier',

            'supplierLocation' =>
                $row['SupplierLocation'] ?? '',

            'supplierPhone' =>
                $row['PhoneNumber'] ?? '',

            'supplierEmail' =>
                $row['Email'] ?? '',

            'currentStock' => $currentStock,

            'predictedDemand' => $predictedDemand,

            'netDemand' => round($netDemand, 2),

            'packSize' => $packSize,

            'recommendedQuantity' => $recommendedQuantity,

            // Manager starts with the AI recommendation.
            'orderedQuantity' => $recommendedQuantity,

            // Purchase price from the selected active supplier offer.
            'unitCost' => $unitCost,

            // Estimated cost for this recommended quantity.
            'estimatedTotal' => $estimatedTotal
        ];
    }

    mysqli_free_result($result);

    echo json_encode([
        'success' => true,
        'data' => $data,
        'estimatedGrandTotal' => $estimatedGrandTotal,
        'recommendationCount' => count($data)
    ]);

} catch (Throwable $e) {

    http_response_code(500);

    error_log(
        'AI recommendations error: ' . $e->getMessage()
    );

    echo json_encode([
        'success' => false,
        'error' => 'Unable to load AI recommendations. Check the PHP error log.'
    ]);
}
?>
