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

        $stmt = $conn->prepare("SELECT 
                                o.Total_Price, 
                                o.Billing_Address, 
                                u.Email, 
                                u.Username,
                                p.Product_ID,
                                p.Product_Name,
                                p.Product_Description,
                                p.Price
                                FROM Orders o
                                JOIN User u ON o.User_ID = u.User_ID
                                JOIN Product p ON o.Product_ID = p.Product_ID
                                WHERE o.Order_Num = ?"
                                );
        $stmt->bind_param("i", $orderNumber);
        $stmt->execute();
        $stmt->bind_result($totalPrice, $billingAddress, $email, $name, $productId, $productName, $productDescription, $productPrice);

        $orderDetails = [];
        while ($stmt->fetch()) {
            if (!isset($orderDetails['order'])) {
                $orderDetails['order'] = [
                    'email' => $email,
                    'name' => $name,
                    'totalPrice' => $totalPrice,
                    'billingAddress' => $billingAddress,
                    'products' => []
                ];
            }
    
            $orderDetails['order']['products'][] = [
                'productId' => $productId,
                'productName' => $productName,
                'productDescription' => $productDescription,
                'productPrice' => $productPrice
            ];
        }        
    }

    $stmt->close();
    $conn->close();

    // Prepare the email content
    $emailContent = "Hello " . $orderDetails['name'] . ",\n\n";
    $emailContent .= "Thank you for your order. Here are the details:\n";
    $emailContent .= "Billing Address: " . $orderDetails['billingAddress'] . "\n";
    $emailContent .= "Total Price: $" . $orderDetails['totalPrice'] . "\n\n";
    $emailContent .= "Products Ordered:\n";

    foreach ($orderDetails['products'] as $product) {
        $emailContent .= "- " . $product['productName'] . " (" . $product['productDescription'] . ") - $" . $product['productPrice'] . "\n";
    }

    $emailContent .= "\nThank you for shopping with us.";

    if(sendReceipt($orderDetails['email'], "Order Confirmation", $emailContent)) {
        header('Location: index.php');
    }
    else {
        echo('help');
    }
}
?>
