<?php
session_start();
require 'vendor/autoload.php'; 
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

header('Content-Type: application/json');

// Fetch the order number from the session
$Order_Num = $_SESSION['Order_Num'];


function sendReceipt($email, $subject, $body) {
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
        $mail->Subject = $subject;
        $mail->Body = $body;

        $mail->send();
        return true;
    } catch (Exception $e) {
        error_log("Message could not be sent. Mailer Error: {$mail->ErrorInfo}"); // Log error
        return false;
    }
}

if ($_SESSION['Order_Num']) {
    $Order_Num = $_SESSION['Order_Num'];
    $config = parse_ini_file('/var/www/private/db-config.ini');
    $conn = new mysqli($config['host'], $config['username'], $config['password'], $config['dbname']);

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    } else {

        $stmt = $conn->prepare("SELECT Billing_Address, User_ID FROM `Order` WHERE Order_Num = ?");
        if (!$stmt) {
            die('Prepare failed: ' . $conn->error);
        }
        $stmt->bind_param("i", $Order_Num);
        $stmt->execute();
        $stmt->bind_result($billingAddress, $userId);
        $stmt->fetch();
        $stmt->close();

        $stmt = $conn->prepare("SELECT Email, Username FROM `User` WHERE User_ID = ?");
        if (!$stmt) {
            die('Prepare failed: ' . $conn->error);
        }
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $stmt->bind_result($email, $username);
        $stmt->fetch();
        $stmt->close();

    }

    $conn->close();

    // Prepare the email content
    $emailContent = "Hello " . $username . ",\n\n";
    $emailContent .= "Thank you for your order. Here are the details:\n";
    $emailContent .= "Billing Address: " . $billingAddress . "\n";
    $emailContent .= "Order ID: " . $Order_Num . "\n\n";

    $emailContent .= "\nThank you for shopping with us.";

    if(sendReceipt($email, "Order Confirmation", $emailContent)) {
        header('Location: index.php');
    }
    else {
        echo('help');
    }
}
?>
