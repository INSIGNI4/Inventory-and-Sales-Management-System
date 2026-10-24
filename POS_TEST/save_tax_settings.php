
<?php
session_start();

header('Content-Type: application/json; charset=utf-8');

require_once '../connect.php';

$conn_pos = $GLOBALS['conn_pos'] ?? null;

function sendJson(int $statusCode, array $data): void
{
    http_response_code($statusCode);
    echo json_encode($data);
    exit;
}

// Only Manager and Super Admin can change tax settings.
$accountType = strtolower(trim($_SESSION['accountType'] ?? ''));

if (!isset($_SESSION['user_id']) ||
    !in_array($accountType, ['manager', 'super admin'], true)) {
    sendJson(403, [
        'status' => 'error',
        'message' => 'You are not authorized to change tax settings.'
    ]);
}

if (!$conn_pos) {
    sendJson(500, [
        'status' => 'error',
        'message' => 'POS database connection is unavailable.'
    ]);
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    sendJson(405, [
        'status' => 'error',
        'message' => 'POST requests are required.'
    ]);
}

$input = json_decode(file_get_contents('php://input'), true);

if (!is_array($input)) {
    sendJson(400, [
        'status' => 'error',
        'message' => 'Invalid request data.'
    ]);
}

$taxName = trim($input['Tax_Name'] ?? '');
$taxRate = $input['Tax_Rate'] ?? null;
$taxMode = $input['Tax_Mode'] ?? '';
$isEnabled = $input['Is_Enabled'] ?? null;

if ($taxName === '' || strlen($taxName) > 50) {
    sendJson(400, [
        'status' => 'error',
        'message' => 'Enter a tax name of up to 50 characters.'
    ]);
}

if (!is_numeric($taxRate) || (float)$taxRate < 0 || (float)$taxRate > 100) {
    sendJson(400, [
        'status' => 'error',
        'message' => 'Tax rate must be between 0 and 100.'
    ]);
}

$taxRate = round((float)$taxRate, 2);

if (!in_array($taxMode, ['exclusive', 'inclusive'], true)) {
    sendJson(400, [
        'status' => 'error',
        'message' => 'Invalid tax calculation mode.'
    ]);
}

if (!in_array($isEnabled, [0, 1, '0', '1', true, false], true)) {
    sendJson(400, [
        'status' => 'error',
        'message' => 'Invalid tax enabled setting.'
    ]);
}

$isEnabled = (int)(bool)$isEnabled;
$updatedBy = (int)$_SESSION['user_id'];

try {
    $stmt = $conn_pos->prepare(
        "UPDATE tax_settings
         SET Tax_Name = ?,
             Tax_Rate = ?,
             Tax_Mode = ?,
             Is_Enabled = ?,
             Updated_By = ?
         WHERE Tax_ID = (
             SELECT Tax_ID FROM (
                 SELECT Tax_ID
                 FROM tax_settings
                 ORDER BY Tax_ID ASC
                 LIMIT 1
             ) AS selected_tax
         )"
    );

    if (!$stmt) {
        throw new Exception('Could not prepare tax settings update.');
    }

    $stmt->bind_param(
        'sdsii',
        $taxName,
        $taxRate,
        $taxMode,
        $isEnabled,
        $updatedBy
    );

    if (!$stmt->execute()) {
        throw new Exception('Could not update tax settings.');
    }

    if ($stmt->affected_rows === 0) {
        // Zero can mean values were unchanged, so verify a row exists.
        $check = $conn_pos->query(
            "SELECT Tax_ID FROM tax_settings ORDER BY Tax_ID ASC LIMIT 1"
        );

        if (!$check || $check->num_rows === 0) {
            sendJson(500, [
                'status' => 'error',
                'message' => 'No tax configuration exists to update.'
            ]);
        }
    }

    $stmt->close();

    sendJson(200, [
        'status' => 'success',
        'message' => 'Tax settings saved successfully.'
    ]);

} catch (Throwable $e) {
    error_log('Save tax settings error: ' . $e->getMessage());

    sendJson(500, [
        'status' => 'error',
        'message' => 'Unable to save tax settings.'
    ]);
}
