<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);


include 'connect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    if (isset($_POST['signup'])) {
        $userName = trim($_POST['userName']);
// $lastName = trim($_POST['lName']);
        $email = trim($_POST['email']);
        $phone = trim($_POST['phone']);
        $accountType = trim($_POST['accountType']);
        $password = trim($_POST['password']);
        $password = md5($password);


    

// Validate phone format
        if (!preg_match("/^[0-9]{11}$/", $phone)) {
            die("Invalid phone number format.");
        }

// Check if email already exists
        $checkEmail = "SELECT * FROM users WHERE email = ?";
        $stmt = $conn->prepare($checkEmail);
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            // echo "Email Address Already Exists!";

            echo "<script>
                alert('Email address already exists!');
                window.location.href = 'index.php';
            </script>";
        } else {

            $activation_token = bin2hex(random_bytes(16));

            $activation_token_hash =hash("sha256", $activation_token);

            $insertQuery = "INSERT INTO users (userName, email, phone, password, accountType, account_activation_hash) VALUES (?, ?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($insertQuery);
            $stmt->bind_param("ssssss", $userName, $email, $phone, $password, $accountType, $activation_token_hash );

            if ($stmt->execute()) {

                $mail = require __DIR__ . "/mailer.php";

                $mail->setFrom("noreply@example.com");
                $mail->addAddress($_POST["email"]);
                $mail->Subject = "Account Activation";
                $mail->Body = <<<END

                Click <a href="http://localhost/login/activate-account.php?token=$activation_token">here</a>
                to activate your account.


                END;

                try {
                    $mail->send();

                }catch (Exception $e) {

                    echo "Message could not be sent. Mailer error:  {$mail->ErrorInfo}";
                    exit;
                }

                echo "
                <script>
                    
                    window.location.href = 'homepage2.php';

                </script>";
                // alert('Signup Successful!');
                // header("Location: index.php");
                // exit();
            } else {
                echo "Error: " . $stmt->error;
            }
        }

        $stmt->close();
    }

 
    // if (isset($_POST['signIn'])) {
    //     $email = trim($_POST['email']);
    //     $password = md5($_POST['loginPassword']);

    //     $sql = "SELECT * FROM users WHERE email = ? AND password = ?";
    //     $stmt = $conn->prepare($sql);
    //     $stmt->bind_param("ss", $email, $password);
    //     $stmt->execute();
    //     $result = $stmt->get_result();

    //     if ($result->num_rows > 0) {
    //         session_start();
    //         $row = $result->fetch_assoc();
    //         $_SESSION['email'] = $row['email'];
            
    //         header("Location: homepage2.php");
    //         echo "Registered Successfully!";
    //         exit();
    //     } else {
    //         echo "Not Found, Incorrect Email or Password";
    //     }

    //     $stmt->close();
    // }

    if (isset($_POST['signIn'])) {
        $email = trim($_POST['email']);
        $password = md5($_POST['loginPassword']);

        $sql = "SELECT * FROM users WHERE email = ? AND password = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ss", $email, $password);
        $stmt->execute();
        $result = $stmt->get_result();


        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();

            // Check whether the account is active.
            if ((int)($row['is_active'] ?? 1) !== 1) {
                echo "<script>
                    alert('This account has been deactivated. Please contact the administrator.');
                    window.location.href = 'index.php';
                </script>";
                exit();
            }

            // Check if account is activated.
            if (is_null($row['account_activation_hash'])) {






        // if ($result->num_rows > 0) {
        //     $row = $result->fetch_assoc();

        //     // Check if account is activated
        //     if (is_null($row['account_activation_hash'])) {

                session_start();

                $_SESSION['email'] = $row['email'];
                $_SESSION['user_id'] = $row['id'];
                $_SESSION['accountType'] = $row['accountType'];

                // Redirect based on account type
                if ($row['accountType'] === 'Manager') {

                    header("Location: homepage2.php");
                    exit();

                } elseif ($row['accountType'] === 'Super Admin') {

                    header("Location: homepage2.php");
                    exit();

                } elseif ($row['accountType'] === 'Cashier') {

                    header("Location: POS_TEST/pos_test.php");
                    exit();

                } else {

                    echo "<script>
                        alert('Account type is not assigned. Please contact the administrator.');
                        window.location.href = 'index.php';
                    </script>";
                    exit();
                }

            } else {

                // Account has not been activated
                echo "<script>
                    alert('Please activate your account first using the activation link sent to your email.');
                    window.location.href = 'index.php';
                </script>";
                exit();
            }

        } else {

            // Invalid credentials
            echo "<script>
                alert('Account Not Found, Incorrect Email or Password!');

                if (window.history.replaceState) {
                    window.history.replaceState({}, document.title, window.location.pathname);
                }

                window.location.href = 'index.php';
            </script>";
            exit();
        }

        $stmt->close();
    }



    $conn->close();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    <!-- <h1 class="emailerror">echo</h1> -->
</body>
</html>