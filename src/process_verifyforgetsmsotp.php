<?php
session_start();
require_once "vendor/autoload.php";

use Predis\Client;
use Twilio\Rest\Client as TwilioClient;

$redis = new Client([
    'scheme' => 'tcp',
    // 'host'   => 'localhost', // either this or redis
    'host'   => 'redis', // either this or redis
    'port'   => 6379,
]);

$number = $_SESSION['phonenum'];
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


echo($verification_check->status);
