
<?php

// Load local database configuration.
$configFile = __DIR__ . '/config.local.php';

if (!file_exists($configFile)) {
    error_log('Database configuration file is missing.');
    http_response_code(500);
    die('Database configuration is not available.');
}

$dbConfig = require $configFile;



if (!is_array($dbConfig)) {
    error_log('Database configuration must return an array.');
    http_response_code(500);
    die('Database configuration is invalid.');
}

// Validate required configuration.
$requiredKeys = [
    'host',
    'username',
    'password',
    'inventory_database',
    'pos_database'
];

foreach ($requiredKeys as $key) {
    if (!array_key_exists($key, $dbConfig)) {
        error_log("Missing database configuration: {$key}");
        http_response_code(500);
        die('Database configuration is incomplete.');
    }
}

try {

    // 1. Connect to the inventory database.
    $conn = new mysqli(
        $dbConfig['host'],
        $dbConfig['username'],
        $dbConfig['password'],
        $dbConfig['inventory_database']
    );

    $conn->set_charset('utf8mb4');

    // Preserve global connection access.
    $GLOBALS['conn'] = $conn;


    // 2. Connect to the POS database.
    $conn_pos = new mysqli(
        $dbConfig['host'],
        $dbConfig['username'],
        $dbConfig['password'],
        $dbConfig['pos_database']
    );

    $conn_pos->set_charset('utf8mb4');

    // Preserve global connection access.
    $GLOBALS['conn_pos'] = $conn_pos;

} catch (Throwable $e) {

    error_log('Database connection error: ' . $e->getMessage());

    // Close any connection that was successfully established.
    if (isset($conn) && $conn instanceof mysqli) {
        $conn->close();
    }

    if (isset($conn_pos) && $conn_pos instanceof mysqli) {
        $conn_pos->close();
    }

    http_response_code(500);
    die('Unable to connect to the database. Please contact the administrator.');
}

?>
