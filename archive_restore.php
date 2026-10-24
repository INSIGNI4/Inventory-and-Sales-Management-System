<?php
declare(strict_types=1);

session_start();
header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . '/connect.php';

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);


function respond(array $data, int $status = 200): void
{
    http_response_code($status);
    echo json_encode($data);
    exit;
}

function requireManager(): void
{
    $role = strtolower(trim((string)($_SESSION['accountType'] ?? '')));

    if (!in_array($role, ['manager', 'super admin'], true)) {
        respond([
            'success' => false,
            'message' => 'Access denied. Manager or Super Admin access is required.'
        ], 403);
    }

    if ((int)($_SESSION['user_id'] ?? 0) <= 0) {
        respond([
            'success' => false,
            'message' => 'Your session is invalid. Please log in again.'
        ], 401);
    }
}

function getRecordConfig(string $type): array
{
    $config = [
        'product' => [
            'table' => 'product',
            'id' => 'Product_ID',
            'name' => 'ProductName',
            'flag' => 'IsArchived',
            'label' => 'Product'
        ],
        'supplier' => [
            'table' => 'supplier',
            'id' => 'Supplier_ID',
            'name' => 'CompanyName',
            'flag' => 'IsArchived',
            'label' => 'Supplier'
        ],
        'user' => [
            'table' => 'users',
            'id' => 'id',
            'name' => 'userName',
            'flag' => 'is_active',
            'label' => 'User'
        ]
    ];

    if (!isset($config[$type])) {
        respond([
            'success' => false,
            'message' => 'Invalid record type.'
        ], 400);
    }

    return $config[$type];
}

try {
    requireManager();

    $method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
    $action = (string)($_POST['action'] ?? $_GET['action'] ?? 'list');

    /*
     * LIST RECORDS
     *
     * status=active   -> active records
     * status=archived -> archived/deactivated records
     * status=all      -> both
     *
     * type=product, supplier, or user
     */
    if ($action === 'list') {
        $type = (string)($_GET['type'] ?? 'product');
        $status = (string)($_GET['status'] ?? 'active');

        $config = getRecordConfig($type);

        if (!in_array($status, ['active', 'archived', 'all'], true)) {
            respond([
                'success' => false,
                'message' => 'Invalid status filter.'
            ], 400);
        }

        $table = $config['table'];
        $id = $config['id'];
        $name = $config['name'];
        $flag = $config['flag'];

        $where = '';

        // if ($status === 'active') {
        //     $where = "WHERE `$flag` = " . ($type === 'user' ? '1' : '0');
        // } elseif ($status === 'archived') {
        //     $where = "WHERE `$flag` = " . ($type === 'user' ? '0' : '1');
        // }



        if ($type === 'user') {
            if ($status === 'active') {
                $where = "WHERE is_active = 1";
            } elseif ($status === 'archived') {
                $where = "WHERE is_active = 0";
            }
        } else {
            if ($status === 'active') {
                $where = "WHERE IsArchived = 0";
            } elseif ($status === 'archived') {
                $where = "WHERE IsArchived = 1";
            }
        }


        if ($type === 'product') {
            $sql = "
                SELECT
                    Product_ID AS Record_ID,
                    ProductName AS Record_Name,
                    Type AS Details,
                    Barcode AS Identifier,
                    IsArchived AS Is_Archived
                FROM login.product
                $where
                ORDER BY ProductName ASC
            ";
        } elseif ($type === 'supplier') {
            $sql = "
                SELECT
                    Supplier_ID AS Record_ID,
                    CompanyName AS Record_Name,
                    ContactPerson AS Details,
                    Email AS Identifier,
                    IsArchived AS Is_Archived
                FROM login.supplier
                $where
                ORDER BY CompanyName ASC
            ";
        } else {
            $sql = "
                SELECT
                    id AS Record_ID,
                    userName AS Record_Name,
                    accountType AS Details,
                    email AS Identifier,
                    is_active AS Is_Active
                FROM login.users
                $where
                ORDER BY userName ASC
            ";
        }

        $result = $conn->query($sql);
        $records = [];

        while ($row = $result->fetch_assoc()) {
            $row['Record_ID'] = (int)$row['Record_ID'];
            // $row['Is_Archived'] = (int)$row['Is_Archived'];
            if ($type === 'user') {
                $row['Is_Active'] = (int)$row['Is_Active'];
            } else {
                $row['Is_Archived'] = (int)$row['Is_Archived'];
            }
            $records[] = $row;
        }

        respond([
            'success' => true,
            'type' => $type,
            'status' => $status,
            'records' => $records
        ]);
    }

    /*
     * ARCHIVE / DEACTIVATE / RESTORE / REACTIVATE
     */
    if (in_array($action, ['archive', 'restore'], true)) {
        if ($method !== 'POST') {
            respond([
                'success' => false,
                'message' => 'This action requires a POST request.'
            ], 405);
        }

        $type = trim((string)($_POST['type'] ?? ''));
        $recordId = filter_var(
            $_POST['record_id'] ?? null,
            FILTER_VALIDATE_INT
        );

        if (!$recordId || $recordId < 1) {
            respond([
                'success' => false,
                'message' => 'Invalid record ID.'
            ], 400);
        }

        $config = getRecordConfig($type);

        $table = $config['table'];
        $idColumn = $config['id'];
        $flagColumn = $config['flag'];
        $label = $config['label'];

        $isUser = ($type === 'user');

        // Users use is_active; products and suppliers use IsArchived.
        $newValue = $isUser
            ? ($action === 'archive' ? 0 : 1)
            : ($action === 'archive' ? 1 : 0);

        // Prevent an administrator from deactivating their own account.
        if ($isUser && $action === 'archive'
            && $recordId === (int)$_SESSION['user_id']) {
            respond([
                'success' => false,
                'message' => 'You cannot deactivate your own account.'
            ], 400);
        }

        $conn->begin_transaction();

        try {
            // Lock the target record before checking/updating it.
            $stmt = $conn->prepare(
                "SELECT `$idColumn`, `$flagColumn`"
                . ($isUser ? ", accountType" : "")
                . " FROM login.`$table` WHERE `$idColumn` = ? FOR UPDATE"
            );

            $stmt->bind_param('i', $recordId);
            $stmt->execute();
            $record = $stmt->get_result()->fetch_assoc();
            $stmt->close();

            if (!$record) {
                throw new RuntimeException("$label not found.");
            }

            $currentValue = (int)$record[$flagColumn];

            if ($currentValue === $newValue) {
                throw new RuntimeException(
                    $action === 'archive'
                        ? "$label is already archived or inactive."
                        : "$label is already active."
                );
            }

            /*
             * Prevent deactivation of the last active Manager or
             * Super Admin. Use a transaction and lock the user rows
             * to reduce the chance of concurrent administrator changes.
             */
            if ($isUser && $action === 'archive') {
                $targetRole = strtolower(
                    trim((string)$record['accountType'])
                );

                if (in_array(
                    $targetRole,
                    ['manager', 'super admin'],
                    true
                )) {
                    $lockResult = $conn->query("
                        SELECT id
                        FROM login.users
                        WHERE is_active = 1
                          AND accountType IN ('Manager', 'Super Admin')
                        FOR UPDATE
                    ");

                    if ($lockResult->num_rows <= 1) {
                        throw new RuntimeException(
                            'Cannot deactivate the last active Manager or Super Admin.'
                        );
                    }
                }
            }

            $update = $conn->prepare(
                "UPDATE login.`$table`
                 SET `$flagColumn` = ?
                 WHERE `$idColumn` = ?"
            );

            $update->bind_param('ii', $newValue, $recordId);
            $update->execute();
            $update->close();

            $conn->commit();

            $message = $isUser
                ? ($action === 'archive'
                    ? 'User account deactivated successfully.'
                    : 'User account reactivated successfully.')
                : ($action === 'archive'
                    ? "$label archived successfully."
                    : "$label restored successfully.");

            respond([
                'success' => true,
                'message' => $message,
                'type' => $type,
                'record_id' => $recordId,
                'status' => $action === 'archive'
                    ? 'archived'
                    : 'active'
            ]);
        } catch (Throwable $e) {
            $conn->rollback();
            throw $e;
        }
    }

    respond([
        'success' => false,
        'message' => 'Unsupported action.'
    ], 400);

} catch (Throwable $e) {
    error_log('Archive/Restore error: ' . $e->getMessage());

    $status = $e instanceof RuntimeException ? 400 : 500;

    respond([
        'success' => false,
        'message' => $status === 400
            ? $e->getMessage()
            : 'A server error occurred. Check the PHP error log.'
    ], $status);
}
