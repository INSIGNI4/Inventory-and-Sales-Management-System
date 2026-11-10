
<?php

include(__DIR__ . '/connect.php');

// Get form data
$username = $_POST['username'] ?? '';
$password = $_POST['password'] ?? '';

// Hash the password securely
$password_hash = password_hash($password, PASSWORD_DEFAULT);

// Insert into database
$stmt = $conn->prepare(
    "INSERT INTO users (username, password) VALUES (?, ?)"
);

if (!$stmt) {
    error_log('User registration prepare failed: ' . $conn->error);
    http_response_code(500);
    exit('Unable to process registration.');
}

$stmt->bind_param("ss", $username, $password_hash);

if ($stmt->execute()) {
    echo "User registered successfully!";
} else {
    error_log('User registration failed: ' . $stmt->error);
    http_response_code(500);
    echo "Unable to register user.";
}

$stmt->close();

?>
