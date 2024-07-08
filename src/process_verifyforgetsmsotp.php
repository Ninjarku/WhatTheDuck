<?php
session_start();
require_once "vendor/autoload.php";

use Twilio\Rest\Client as TwilioClient;


$number = "+65".$_SESSION['phonenum'];
$code = $_POST['otp'];
$twilio_config = parse_ini_file('/var/www/private/twilio-config.ini');
$sid = $twilio_config['SID'];
$token = $twilio_config['Token'];
$twilio = new TwilioClient($sid, $token);

$verification_check = $twilio->verify->v2
    ->services($twilio_config['Service'])
    ->verificationChecks->create([
        "to" => $number,
        "code" => $code ,
    ]);


if ($verification_check->status == 'approved' && $verification_check->valid) {
    header("Location: ResetPassword.php");
}
