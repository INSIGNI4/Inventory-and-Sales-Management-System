
<?php




header('Content-Type: application/json; charset=utf-8');

require_once 'connect.php';

try {
    $productID = filter_input(
        INPUT_GET,
        'product_id',
        FILTER_VALIDATE_INT
    );

    if (!$productID || $productID <= 0) {
        http_response_code(400);

        echo json_encode([
            'success' => false,
            'message' => 'A valid product_id is required.',
            'data' => []
        ]);
        exit;
    }

    $stmt = $conn->prepare("
        SELECT
            ps.ProductSupplier_ID AS productSupplierId,
            ps.Product_ID AS productId,
            ps.Supplier_ID AS supplierId,
            ps.SupplierPrice AS unitCost,
            s.CompanyName AS supplierName,
            s.ContactPerson AS contactPerson,
            s.Address AS supplierAddress,
            s.City AS supplierCity,
            s.PhoneNumber AS supplierPhone,
            s.Email AS supplierEmail
        FROM product_supplier ps
        INNER JOIN product p
            ON p.Product_ID = ps.Product_ID
        INNER JOIN supplier s
            ON s.Supplier_ID = ps.Supplier_ID
        WHERE ps.Product_ID = ?
          AND ps.IsActive = 1
          AND p.IsArchived = 0
          AND s.IsArchived = 0
        ORDER BY
            ps.SupplierPrice ASC,
            s.CompanyName ASC
    ");

    if (!$stmt) {
        throw new Exception(
            'Unable to prepare supplier offer query.'
        );
    }

    $stmt->bind_param('i', $productID);
    $stmt->execute();

    $result = $stmt->get_result();
    $offers = [];

    while ($row = $result->fetch_assoc()) {
        $row['productSupplierId'] =
            (int) $row['productSupplierId'];

        $row['productId'] =
            (int) $row['productId'];

        $row['supplierId'] =
            (int) $row['supplierId'];

        $row['unitCost'] =
            (float) $row['unitCost'];

        $row['supplierLocation'] = trim(
            ($row['supplierAddress'] ?? '') .
            (
                !empty($row['supplierCity'])
                    ? ', ' . $row['supplierCity']
                    : ''
            )
        );

        $offers[] = $row;
    }

    $stmt->close();

    echo json_encode([
        'success' => true,
        'data' => $offers
    ]);

} catch (Throwable $e) {
    http_response_code(500);

    echo json_encode([
        'success' => false,
        'message' => 'Unable to retrieve supplier offers.',
        'data' => []
    ]);

    error_log(
        'Supplier offers error: ' . $e->getMessage()
    );
}













// header('Content-Type: application/json; charset=utf-8');

// require_once 'connect.php';

// try {
//     $productID = filter_input(
//         INPUT_GET,
//         'product_id',
//         FILTER_VALIDATE_INT
//     );

//     if (!$productID || $productID <= 0) {
//         http_response_code(400);

//         echo json_encode([
//             'success' => false,
//             'message' => 'A valid product_id is required.',
//             'data' => []
//         ]);
//         exit;
//     }

//     $stmt = $conn->prepare("
//         SELECT
//             ps.ProductSupplier_ID AS productSupplierId,
//             ps.Product_ID AS productId,
//             ps.Supplier_ID AS supplierId,
//             ps.SupplierPrice AS unitCost,
//             s.CompanyName AS supplierName,
//             s.ContactPerson AS contactPerson,
//             s.Address AS supplierAddress,
//             s.City AS supplierCity,
//             s.PhoneNumber AS supplierPhone,
//             s.Email AS supplierEmail
//         FROM product_supplier ps
//         INNER JOIN supplier s
//             ON s.Supplier_ID = ps.Supplier_ID
//         WHERE ps.Product_ID = ?
//           AND ps.IsActive = 1
//         ORDER BY
//             ps.SupplierPrice ASC,
//             s.CompanyName ASC
//     ");

//     if (!$stmt) {
//         throw new Exception('Unable to prepare supplier offer query.');
//     }

//     $stmt->bind_param('i', $productID);
//     $stmt->execute();

//     $result = $stmt->get_result();
//     $offers = [];

//     while ($row = $result->fetch_assoc()) {
//         $row['productSupplierId'] = (int)$row['productSupplierId'];
//         $row['productId'] = (int)$row['productId'];
//         $row['supplierId'] = (int)$row['supplierId'];
//         $row['unitCost'] = (float)$row['unitCost'];

//         $row['supplierLocation'] = trim(
//             ($row['supplierAddress'] ?? '') .
//             (
//                 !empty($row['supplierCity'])
//                     ? ', ' . $row['supplierCity']
//                     : ''
//             )
//         );

//         $offers[] = $row;
//     }

//     $stmt->close();

//     echo json_encode([
//         'success' => true,
//         'data' => $offers
//     ]);

// } catch (Throwable $e) {
//     http_response_code(500);

//     echo json_encode([
//         'success' => false,
//         'message' => 'Unable to retrieve supplier offers.',
//         'data' => []
//     ]);

//     error_log('Supplier offers error: ' . $e->getMessage());
// }
?>
