
<?php
header('Content-Type: application/json; charset=utf-8');

include('../connect.php');

$conn_pos = $GLOBALS['conn_pos'] ?? null;

if (!$conn_pos) {
    http_response_code(500);
    echo json_encode([
        'status' => 'error',
        'message' => 'POS database connection is unavailable.'
    ]);
    exit;
}

try {
    $sql = "SELECT
                Tax_ID,
                Tax_Name,
                Tax_Rate,
                Tax_Mode,
                Is_Enabled,
                Updated_By,
                Updated_At
            FROM tax_settings
            ORDER BY Tax_ID ASC
            LIMIT 1";

    $result = $conn_pos->query($sql);

    if (!$result) {
        throw new Exception('Unable to read tax settings.');
    }

    $settings = $result->fetch_assoc();

    if (!$settings) {
        $settings = [
            'Tax_ID' => null,
            'Tax_Name' => 'VAT',
            'Tax_Rate' => '0.00',
            'Tax_Mode' => 'exclusive',
            'Is_Enabled' => 0,
            'Updated_By' => null,
            'Updated_At' => null
        ];
    }

    echo json_encode([
        'status' => 'success',
        'settings' => $settings
    ]);

} catch (Throwable $e) {
    http_response_code(500);

    error_log('Tax settings read error: ' . $e->getMessage());

    echo json_encode([
        'status' => 'error',
        'message' => 'Could not load tax settings.'
    ]);
}
