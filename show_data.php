<?php
include('connect.php');


$table = isset($_GET['table']) ? $_GET['table'] : 'product';


/**
 * @param mysqli $conn
 * @return array
 */

function getAllTables($conn) {
    $tables = [];
    $query = $conn->query("SHOW TABLES");

    if ($query) {
        while ($row = $query->fetch_array()) {
            $tables[] = $row[0];
        } 
    }  else {
        die("Failed to show data" . $conn->error);
    }
    
    return $tables;
}
/**
 * @param mysqli $conn
 * @param string $table
 * @return array
 */


// function fetchTableData($conn, $table) {
//     $valid_tables = getAllTables($conn);

//     if (!in_array($table, $valid_tables)){
//         return [];
//     }

//     $sql = "SELECT * FROM `$table`";
//     $result = $conn->query($sql);
    
//     if (!$result) {
//         return [];
//     }

//     return $result->num_rows > 0 ? $result->fetch_all(MYSQLI_ASSOC) : [];
// }


function fetchTableData($conn, $table) {

    $valid_tables = getAllTables($conn);

    if (!in_array($table, $valid_tables)) {
        return [];
    }

    // Special query for Restock
    // Adds Product Name and Supplier Name
    // if ($table === 'restock') {

     



    // Special query for Restock
    // Adds Product Name, Supplier Details, and Historical Purchase Costs
    if ($table === 'restock') {

        $sql = "
            SELECT
                r.*,
                p.ProductName,
                s.Email,
                s.PhoneNumber,
                s.Address,
                s.City,
                s.CompanyName AS SupplierName,
                s.ContactPerson,

                -- Saved supplier purchase cost at order creation
                r.PurchaseUnitCost,

                -- Total purchase cost for the ordered quantity
                CASE
                    WHEN r.PurchaseUnitCost IS NOT NULL
                    THEN r.Quantity * r.PurchaseUnitCost
                    ELSE NULL
                END AS OrderCost

            FROM restock r

            LEFT JOIN product p
                ON p.Product_ID = r.Product_ID

            LEFT JOIN supplier s
                ON s.Supplier_ID = r.Supplier_ID

            ORDER BY r.Orestock_ID DESC
        ";

    }elseif ($table === 'sales_item_test') {

        $sql = "
            SELECT *
            FROM sales_item_test
            ORDER BY SalesItem_ID DESC
            LIMIT 50
        ";

    } elseif ($table === 'sales_test') {

        $sql = "
            SELECT *
            FROM sales_test
            ORDER BY Sales_ID DESC
            LIMIT 50
        ";

    }
    elseif ($table === 'inventory') {

        $sql = "
            SELECT
                r.*,
                offers.SupplierName,
                offers.Email,
                offers.PhoneNumber,
                offers.Address,
                offers.City,
                offers.ContactPerson
            FROM inventory r
            LEFT JOIN (
                SELECT
                    ps.Product_ID,
                    GROUP_CONCAT(
                        DISTINCT s.CompanyName
                        ORDER BY s.CompanyName
                        SEPARATOR ', '
                    ) AS SupplierName,
                    GROUP_CONCAT(
                        DISTINCT s.Email
                        ORDER BY s.CompanyName
                        SEPARATOR ', '
                    ) AS Email,
                    GROUP_CONCAT(
                        DISTINCT s.PhoneNumber
                        ORDER BY s.CompanyName
                        SEPARATOR ', '
                    ) AS PhoneNumber,
                    GROUP_CONCAT(
                        DISTINCT s.Address
                        ORDER BY s.CompanyName
                        SEPARATOR ', '
                    ) AS Address,
                    GROUP_CONCAT(
                        DISTINCT s.City
                        ORDER BY s.CompanyName
                        SEPARATOR ', '
                    ) AS City,
                    GROUP_CONCAT(
                        DISTINCT s.ContactPerson
                        ORDER BY s.CompanyName
                        SEPARATOR ', '
                    ) AS ContactPerson
                FROM product_supplier ps
                INNER JOIN supplier s
                    ON s.Supplier_ID = ps.Supplier_ID
                WHERE ps.IsActive = 1
                    AND s.IsArchived = 0
                GROUP BY ps.Product_ID
            ) offers
                ON offers.Product_ID = r.Product_ID
                WHERE r.Product_ID IN (
                SELECT Product_ID
                FROM product
                WHERE IsArchived = 0
)    
        ";

    
    
    // elseif ($table === 'product') {

    //     $sql = "
    //         SELECT
    //             r.*,
                
    //             s.Email,
    //             s.PhoneNumber,
    //             s.Address,
    //             s.City,
    //             s.CompanyName AS SupplierName,
    //             s.ContactPerson
    //         FROM product r
    //         LEFT JOIN supplier s
    //             ON s.Supplier_ID = r.Supplier_ID
            
    //     ";

    // } elseif ($table === 'product') {

        // $sql = "
        //     SELECT
        //         p.*,
        //         offers.SupplierName
        //     FROM product p
        //     LEFT JOIN (
        //         SELECT
        //             ps.Product_ID,
        //             GROUP_CONCAT(
        //                 DISTINCT s.CompanyName
        //                 ORDER BY s.CompanyName
        //                 SEPARATOR ', '
        //             ) AS SupplierName
        //         FROM product_supplier ps
        //         INNER JOIN supplier s
        //             ON s.Supplier_ID = ps.Supplier_ID
        //         WHERE ps.IsActive = 1
        //         GROUP BY ps.Product_ID
        //     ) offers
        //         ON offers.Product_ID = p.Product_ID
        // ";



    } elseif ($table === 'product') {

        $sql = "
            SELECT
                p.*,
                offers.SupplierName
            FROM product p
            LEFT JOIN (
                SELECT
                    ps.Product_ID,
                    GROUP_CONCAT(
                        DISTINCT s.CompanyName
                        ORDER BY s.CompanyName
                        SEPARATOR ', '
                    ) AS SupplierName
                FROM product_supplier ps
                INNER JOIN supplier s
                    ON s.Supplier_ID = ps.Supplier_ID
                WHERE ps.IsActive = 1
                AND s.IsArchived = 0
                GROUP BY ps.Product_ID
            ) offers
                ON offers.Product_ID = p.Product_ID
            WHERE p.IsArchived = 0
        ";



    } elseif ($table === 'supplier') {

        // Show active suppliers only.
        // Archived suppliers remain available in Archive & Restore.
        $sql = "
            SELECT *
            FROM supplier
            WHERE IsArchived = 0
            ORDER BY CompanyName ASC
        ";



    } else {

        // Keep the original behavior for all other tables

        $sql = "SELECT * FROM `$table`";
    }

    $result = $conn->query($sql);

    if (!$result) {
        return [];
    }

    return $result->num_rows > 0
        ? $result->fetch_all(MYSQLI_ASSOC)
        : [];
}



return fetchTableData($conn, $table);

// $sql = "SELECT * FROM users";
// $result = $conn->query($sql);

// if (!$result) {
//     die("Query failed: " . $conn->error);
// }

// return $result->num_rows > 0 ? $result->fetch_all(MYSQLI_ASSOC) : [];

?>
    