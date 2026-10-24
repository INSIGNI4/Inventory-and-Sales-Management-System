<?php
include('pos_sale_test.php');

$receipt_id = $_GET['receipt_id'] ?? null;
if (!$receipt_id) {
    die("Error: No receipt ID provided.");
}

$sale_data = getSale($receipt_id);

if (!$sale_data || !$sale_data['sales']) {
    die("Error: Sale record not found for Receipt ID: " . htmlspecialchars($receipt_id));
}

$sale = $sale_data['sales'];
// $customer_data = $sale_data['customer'];
$items = $sale_data['items'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Receipt #<?php echo htmlspecialchars($sale['ReferenceNo']); ?></title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            margin: 0;
            padding: 15px;
            color: #000;
        }
        .receipt-box {
            max-width: 300px;
            margin: auto;
            border: 1px solid #ddd;
            padding: 10px;
        }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { padding: 4px 0; }
        .border-top { border-top: 1px dashed #000; }
        .border-bottom { border-bottom: 1px dashed #000; }

        @media print {
            body { padding: 0; }
            .receipt-box { border: none; width: 100%; }
        }
    </style>
</head>
<body>

<div class="receipt-box">
    <div class="text-center">
        <h2>SALE RECEIPT</h2>
        <p>Receipt #: <?php echo htmlspecialchars($sale['ReferenceNo']); ?></p>
        <p>Date: <?php echo htmlspecialchars($sale['date_created']); ?></p>

    <!-- REVISION FOR NAME -->
        <span>
        <p>
            Cashier ID:
            <?php echo htmlspecialchars((string)($sale['User_ID'] ?? 'N/A')); ?> 
            NAME: 
            <?php echo htmlspecialchars($sale['CashierName'] ?? 'Unknown'); ?>
        </p>
        <p>
            
        </p>
        </span>
        


    </div>

    <div class="border-top border-bottom" style="margin: 8px 0; padding: 4px 0;">
        <!-- <strong>Customer:</strong> 
        <?php 
            $c_name = trim(($customer['first_name'] ?? '') . ' ' . ($customer['last_name'] ?? ''));
            echo htmlspecialchars($c_name !== '' ? $c_name : 'Walk-in Customer');
        ?> -->
    </div>

    <table>
        <thead>
            <tr class="border-bottom">
                <th style="text-align: left;">Item</th>
                <th class="text-center">Qty</th>
                <th class="text-right">Price</th>
                <!-- <th class="text-right">Total</th> -->
            </tr>
        </thead>
        <tbody>
            <?php foreach ($items as $item): ?>
                <tr>
                    <td><?php echo htmlspecialchars($item['ProductName']); ?></td>                    
                    <td class="text-center"><?php echo (int)$item['Quantity']; ?></td>
                    <td class="text-right"><?php echo number_format($item['Unit_Price'], 2); ?></td>
                    <!-- <td class="text-right"><?php echo number_format($item['TotalPrice'], 2); ?></td> -->
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <!-- <div class="border-top" style="margin-top: 10px; padding-top: 5px;">
        <table>
            <tr>
                <td><strong>Total Amount:</strong></td>
                <td class="text-right"><strong>₱<?php echo number_format($sale['total_amount'], 2); ?></strong></td>
            </tr>
            <tr>
                <td>Tendered:</td>
                <td class="text-right">₱<?php echo number_format($sale['amount_tendered'], 2); ?></td>
            </tr>
            <tr>
                <td>Change:</td>
                <td class="text-right">₱<?php echo number_format($sale['change_amt'], 2); ?></td>
            </tr>
        </table>
    </div> -->


    <!-- REVISION FOR TAX -->


    <div class="border-top" style="margin-top: 10px; padding-top: 5px;">
        <table>
            <tr>
                <td>Subtotal:</td>
                <td class="text-right">
                    ₱<?php echo number_format((float)($sale['subtotal'] ?? $sale['total_amount']), 2); ?>
                </td>
            </tr>

            <?php
                $taxRate = (float)($sale['tax_rate'] ?? 0);
                $taxAmount = (float)($sale['tax_amount'] ?? 0);
                $taxName = trim((string)($sale['tax_name'] ?? ''));
            ?>

            <?php if ($taxName !== '' && $taxRate > 0): ?>
                <tr>
                    <td>
                        <?php
                            echo htmlspecialchars($taxName);
                            echo ' (' . number_format($taxRate, 2) . '%)';
                        ?>:
                    </td>
                    <td class="text-right">
                        ₱<?php echo number_format($taxAmount, 2); ?>
                    </td>
                </tr>
            <?php endif; ?>

            <tr>
                <td><strong>Total Amount:</strong></td>
                <td class="text-right">
                    <strong>₱<?php echo number_format((float)$sale['total_amount'], 2); ?></strong>
                </td>
            </tr>

            <tr>
                <td>Tendered:</td>
                <td class="text-right">
                    ₱<?php echo number_format((float)$sale['amount_tendered'], 2); ?>
                </td>
            </tr>

            <tr>
                <td>Change:</td>
                <td class="text-right">
                    ₱<?php echo number_format((float)$sale['change_amt'], 2); ?>
                </td>
            </tr>
        </table>
    </div>






    <div class="text-center" style="margin-top: 15px;">
        <p>Thank you for your purchase!</p>
    </div>
</div>

<script>
    // Delay print dialog slightly to ensure the layout is fully rendered
    window.addEventListener('load', function() {
        setTimeout(function() {
            window.print();
        }, 200);
    });
</script>

</body>
</html>
