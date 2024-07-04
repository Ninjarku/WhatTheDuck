<?php
// This script checks the OTP entered by the user against the stored value
session_start();

$entered_otp = $_POST['otp'];
$correct_otp = $_SESSION['otp'];
$otp_time = $_SESSION['otp_time'];
$otp_expiry = 300; // 5 minutes in seconds

if (time() - $otp_time > $otp_expiry) {
    echo 'OTP has expired. Please request a new one.';
} else {
    if ($entered_otp == $correct_otp) {
        echo 'Yay it works'; // Redirect to reset password page
        exit;
    } else {
        echo 'Incorrect OTP. Please try again.';
    }
}
?>
