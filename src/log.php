<?php
require '/var/www/html/jwt/jwt_req.php';

$sec_key = "sidasid";  // Ensure the semicolon at the end of the statement

$payload = [
    'iat' => time(),  // 'iat' should be current time
    'exp' => time() + 3600,  // 'exp' should be current time + 1 hour
    'id' => "1",
    'rol' => "rol"
];

$encode = JWT::encode($payload, $sec_key, 'HS256');  // Correct the arguments

echo $encode;
?>