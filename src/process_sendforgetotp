<?php
session_start();
require './vendor/autoload.php';
//require '/var/www/html/jwt/jwt_gen_token.php';  // Adjust the path as necessary

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

function sanitizeInput($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

function generateOTP($length = 6) {
    return substr(str_shuffle(str_repeat($x='0123456789', ceil($length/strlen($x)) )),1,$length);
}

function sendOTP($email, $otp) {
    $mail = new PHPMailer(true);
    $smtp_config = parse_ini_file('/var/www/private/smtp-config.ini');
    try {
        // SMTP and email sending configurations
        $mail->isSMTP();
        $mail->Host = $smtp_config['host'];
        $mail->SMTPAuth = true;
        $mail->Username = $smtp_config['username'];
        $mail->Password = $smtp_config['password']; 
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;

        $mail->setFrom($smtp_config['username'], 'The Ducker');
        $mail->addAddress($email);

        $mail->isHTML(true);
        $mail->Subject = 'Your OTP Code for Account Verification';
        $mail->Body    = 'Your OTP code is: <strong>' . htmlspecialchars($otp) . '</strong>';

        $mail->send();
        return true;
    } catch (Exception $e) {
        error_log("Message could not be sent. Mailer Error: {$mail->ErrorInfo}"); // Log error
        return false;
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['email'])) {
    $email = sanitizeInput($_POST['email']); // Sanitize email input

    // Database connection setup
    $config = parse_ini_file('/var/www/private/db-config.ini');
    $conn = new mysqli($config['host'], $config['username'], $config['password'], $config['dbname']);

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    } else {
        $stmt = $conn->prepare("SELECT * FROM User WHERE Email = ?");
        $stmt->bind_param("s", $email); // Bind parameter
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $otp = generateOTP();
            $timeNow = time(); // Get current timestamp

            if (sendOTP($email, $otp)) {
                $_SESSION['otp'] = $otp;
                $_SESSION['otp_time'] = $timeNow;
                $_SESSION['email'] = $email;
                $stmt->close();
                $conn->close();
                header("Location: ForgetVerify.php");
                exit;
            } else {
                echo json_encode(array("success" => false, "message" => "Failed to send OTP."));
            }
        } else {
            echo json_encode(array("success" => false, "message" => "Email not registered."));
        }
        $stmt->close();
    }
    $conn->close();
}
?>
