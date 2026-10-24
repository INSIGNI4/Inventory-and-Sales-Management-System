
<?php

date_default_timezone_set('Asia/Manila');

include "connect.php";

session_start();




if (empty($_SESSION['email'])) {
    header('Location: homepage2.php');
    exit;
}



// Get the logged-in manager's details from the database.
$manager = null;

if (
    strtolower(trim($_SESSION['accountType'] ?? '')) === 'Manager' || 'Super Admin'
) {
    $managerStmt = $conn->prepare("
        SELECT id, userName
        FROM users
        WHERE email = ?
        LIMIT 1
    ");

    $managerStmt->bind_param('s', $_SESSION['email']);
    $managerStmt->execute();

    $manager = $managerStmt->get_result()->fetch_assoc();

    $managerStmt->close();
}




$order = $_SESSION['purchase_order'] ?? null;

if (
    !$order ||
    empty($order['items']) ||
    !is_array($order['items'])
) {
    $_SESSION['response'] = [
        'success' => false,
        'message' => 'No printable purchase order was found.'
    ];

    header('Location: homepage2.php');
    exit;
}

function h($value): string
{
    return htmlspecialchars((string) ($value ?? ''), ENT_QUOTES, 'UTF-8');
}

function peso($amount): string
{
    return '₱' . number_format((float) $amount, 2);
}

// Group items by supplier.
$suppliers = [];

foreach ($order['items'] as $item) {
    $supplierID = (int) $item['Supplier_ID'];

    if (!isset($suppliers[$supplierID])) {
        $suppliers[$supplierID] = [
            'details' => $item,
            'items' => [],
            'subtotal' => 0
        ];
    }

    $suppliers[$supplierID]['items'][] = $item;
    $suppliers[$supplierID]['subtotal'] += (float) $item['ItemTotal'];
}

$orderDate = date(
    'F d, Y h:i A',
    strtotime($order['created_at'])
);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Purchase Order</title>

    <style>
        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            padding: 24px;
            font-family: Arial, sans-serif;
            color: #222;
            background: #f3f4f6;
         
        }

        .toolbar {
            max-width: 900px;
            margin: 0 auto 20px;
            display: flex;
            gap: 10px;
        }

        .toolbar button,
        .toolbar a {
            padding: 10px 16px;
            border: 0;
            border-radius: 5px;
            font-size: 14px;
            text-decoration: none;
            cursor: pointer;
        }

        .print-button {
            background: #018239;
            color: #fff;
        }

        .back-button {
            background: #ba0101;
            color: #fffefe;
        }

        .purchase-order {
            max-width: 900px;
            margin: 0 auto 24px;
            padding: 36px;
            background: #fff;
            border: 1px solid #ddd;
        }

        .document-heading {
            text-align: center;
            margin-bottom: 28px;
        }

        .document-heading h1 {
            margin: 0 0 8px;
            font-size: 26px;
            letter-spacing: 1px;
        }

        .document-heading p {
            margin: 4px 0;
            color: #555;
        }

        .supplier-details {
            margin: 20px 0;
            padding: 14px;
            border: 1px solid #ccc;
            line-height: 1.7;
        }

        .supplier-details h2 {
            margin: 0 0 8px;
            font-size: 18px;
        }

        .order-meta {
            margin: 18px 0;
            line-height: 1.8;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 18px;
            font-size: 13px;
        }

        th,
        td {
            border: 1px solid #ccc;
            padding: 10px 8px;
            text-align: left;
        }

        th {
            background: #f0f0f0;
        }

        .number {
            text-align: right;
            white-space: nowrap;
        }

        .subtotal {
            margin-top: 18px;
            text-align: right;
            font-size: 18px;
            font-weight: bold;
        }

        .note {
            margin-top: 24px;
            font-size: 12px;
            color: #555;
        }

        .signatures {
            display: flex;
            justify-content: space-between;
            gap: 40px;
            margin-top: 65px;
        }

  

        .page-break {
            page-break-after: always;
            break-after: page;
        }



        .signature {
            width: 45%;
            padding-top: 8px;
            text-align: center;
            font-size: 12px;
        }

        .signature p {
            margin: 8px 0;
        }

        .signature-line {
            margin-top: 35px;
            padding-top: 6px;
            border-top: 1px solid #333;
        }






        @media print {
            body {
                padding: 0;
                background: #fff;
            }

            .toolbar {
                display: none;
            }

            .purchase-order {
                max-width: none;
                margin: 0;
                padding: 12mm;
                border: none;
            }

            .page-break {
                page-break-after: always;
                break-after: page;
            }

            table,
            .supplier-details,
            .subtotal,
            .signatures {
                break-inside: avoid;
            }










        }
    </style>
</head>

<body style="background-image: url(bglogin.png);background-repeat: no-repeat;background-size: cover;">

<div class="toolbar">
    <button style="font-weight: bolder" class="print-button" onclick="window.print()">
        Print / Save as PDF
    </button>

    <a style="font-weight: bolder" class="back-button" href="homepage2.php">
        Return to Inventory
    </a>
</div>

<?php
$supplierCount = count($suppliers);
$currentSupplier = 0;

foreach ($suppliers as $supplierGroup):
    $currentSupplier++;
    $supplier = $supplierGroup['details'];
?>

<section class="purchase-order <?= $currentSupplier < $supplierCount ? 'page-break' : '' ?>" style="opacity: 95%;">

    <div class="document-heading">
        <h1>PURCHASE ORDER</h1>
        <p>Supplier Order Copy</p>
        <p>Order Date: <?= h($orderDate) ?></p>
    </div>

    <div class="supplier-details">
        <h2><?= h($supplier['CompanyName']) ?></h2>

        <strong>Contact Person:</strong>
        <?= h($supplier['ContactPerson']) ?><br>
<!-- 
        <strong>Address:</strong>
        <?= h($supplier['Address']) ?>,
        <?= h($supplier['City']) ?><br> -->


        <strong>Address:</strong>
        <?= h(implode(', ', array_filter([
            $supplier['Address'] ?? '',
            $supplier['City'] ?? ''
        ]))) ?>
        <br>        

        <strong>Phone:</strong>
        <?= h($supplier['PhoneNumber']) ?><br>

        <strong>Email:</strong>
        <?= h($supplier['Email']) ?>
    </div>

    <div class="order-meta">
        <strong>Expected Delivery Date:</strong>
        <?= h(date(
            'F d, Y',
            strtotime($supplier['Expected_Receive_Date'])
        )) ?>
    </div>

    <table>
        <thead>
            <tr>
                <th>Product</th>
                <th>Pack Size</th>
                <th>Quantity Ordered</th>
                <th class="number">Unit Cost</th>
                <th class="number">Item Total</th>
            </tr>
        </thead>

        <tbody>
        <?php foreach ($supplierGroup['items'] as $item): ?>
            <tr>
                <td><?= h($item['ProductName']) ?></td>
                <td><?= h($item['Pack_Size']) ?></td>
                <td><?= h($item['Quantity']) ?></td>
                <td class="number">
                    <?= peso($item['PurchaseUnitCost']) ?>
                </td>
                <td class="number">
                    <?= peso($item['ItemTotal']) ?>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>

    <div class="subtotal">
        Total Order Amount:
        <?= peso($supplierGroup['subtotal']) ?>
    </div>

    <p class="note">
        Please use this purchase order to verify the products and quantities
        delivered. Record any missing, damaged, or incorrect items during receiving.
    </p>

    <div class="signatures">
        <div class="signature">
            <strong>Prepared / Authorized By</strong>

            <?php if ($manager): ?>
                <p>
                    Manager Name:
                    <?= h($manager['userName']) ?>
                </p>

                <p>
                    Manager ID:
                    <?= h($manager['id']) ?>
                </p>
            <?php else: ?>
                <p>Manager details unavailable</p>
            <?php endif; ?>

            <div class="signature-line">Signature</div>
        </div>
        <div class="signature">Supplier Acknowledgment</div>
    </div>

</section>

<?php endforeach; ?>

</body>
</html>
