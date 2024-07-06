<?php
// This script checks the OTP entered by the user against the stored value
session_start();

require 'vendor/autoload.php';
use Predis\Client;

$redis = new Client([
    'scheme' => 'tcp',
    // 'host'   => 'localhost', // either this or redis
    'host'   => 'redis', // either this or redis
    'port'   => 6379,
]);

function verifyOTP($email, $entered_otp) {
    global $redis;
    $correct_otp = $redis->get("otp:$email");
    if ($correct_otp === false) {
        return "OTP has expired or does not exist.";
    } elseif ($correct_otp === $entered_otp) {
        return "OTP is correct.";
    } else {
        return "Incorrect OTP. Please try again.";
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['otp'])) {
    $entered_otp = $_POST['otp'];
    $email = $_SESSION['email']; // Assuming email is still stored in session or retrieve it some other way
    $result = verifyOTP($email, $entered_otp);
    echo $result;
}

?>
