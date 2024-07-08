<?php
session_start();
require 'vendor/autoload.php';
use Predis\Client;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use Twilio\Rest\Client as TwilioClient;

$redis = new Client([
    'scheme' => 'tcp',
    // 'host'   => 'localhost', // either this or redis
    'host'   => 'redis', // either this or redis
    'port'   => 6379,
]);

function sanitizeInput($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

function generateOTP($length = 6) {
    return substr(str_shuffle(str_repeat($x='0123456789', ceil($length/strlen($x)) )),1,$length);
}

function storeOTP($email, $otp, $expiry = 300) { // 300 seconds = 5 minutes
    global $redis;
    $redis->set("otp:$email", $otp, 'ex', $expiry);
}

function sendSMSOTP($number) {
    $twilio_config = parse_ini_file('/var/www/private/twilio-config.ini');
    $sid = $twilio_config['SID'];
    $token = $twilio_config['Token'];
    $twilio = new TwilioClient($sid, $token);
    $fullNum = "+65".$number;

    $verification = $twilio->verify->v2
        ->services($twilio_config['Service'])
        ->verifications->create(
            $fullNum, // to
            "sms" // channel
        );

    if ($verification->status == 'pending') {
        return true;
    }
    
    return false;
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


if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['email']) && isset($_POST['method'])) {
    $email = sanitizeInput($_POST['email']);
    $method =  $_POST['method'];
    // Database connection setup

    $config = parse_ini_file('/var/www/private/db-config.ini');
    $conn = new mysqli($config['host'], $config['username'], $config['password'], $config['dbname']);

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    } else if ($method === 'email'){

        $stmt = $conn->prepare("SELECT * FROM User WHERE Email = ?");
        $stmt->bind_param("s", $email); // Bind parameter
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $otp = generateOTP();
            storeOTP($email, $otp);
            $timeNow = time(); // Get current timestamp

            if (sendOTP($email, $otp)) {
                $_SESSION['otp'] = $otp;
                $_SESSION['otp_time'] = $timeNow;
                $_SESSION['email'] = $email;
                $_SESSION['method'] = $method;
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
        
    } else if ($method === 'sms') {
        $stmt = $conn->prepare("SELECT Mobile_Number FROM User WHERE Email = ?");
        $stmt->bind_param("s", $email); // Bind parameter
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $timeNow = time(); // Get current timestamp
            $row = $result->fetch_assoc();
            $number = $row["Mobile_Number"];
            if (sendSMSOTP($number)) {
                $_SESSION['phonenum'] = $number;
                $_SESSION['method'] = $method;
                $stmt->close();
                $conn->close();
                header("Location: ForgetSMSVerify.php");
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