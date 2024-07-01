<?php
require '/var/www/html/vendor/autoload.php';

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

// Generate JWT
function generateJWT($userId, $userRole, $privateKeyPath, $algorithm = 'RS256') {
    $issuedAt = time();
    $expirationTime = $issuedAt + (60 * 60);  // jwt valid for 1 hour from the issued time
    $payload = [
        'iat' => $issuedAt,
        'exp' => $expirationTime,
        'id' => $userId,
        'rol' => $userRole
    ];

    // Load RSA private key
    $privateKey = file_get_contents($privateKeyPath);
    if ($privateKey === false) {
        throw new Exception("Unable to load private key from $privateKeyPath");
    }

    // Generate the JWT
    $jwt = JWT::encode($payload, $privateKey, $algorithm);

    return $jwt;
}

function setJWTInCookie($jwt, $cookieName = 'auth_token', $cookieExpiry = 3600) {
    // Ensure the cookie is sent only over HTTPS and is inaccessible via JavaScript
    $isSecure = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on';
    // Actual
    // $cookieParams = [
    //     'expires' => time() + $cookieExpiry,
    //     'path' => '/',
    //     'domain' => 'whattheduck.ddns.net',
    //     'secure' => $isSecure,
    //     'httponly' => true,
    // ];

    // Test site only, change on production to top
    $cookieParams = [
        'expires' => time() + $cookieExpiry,
        'path' => '/',
        'secure' => $isSecure,
        'httponly' => true,
    ];

    setcookie($cookieName, $jwt, $cookieParams);
}


// After performing authentication check
function gen_set_cookie($db_Id, $user_rol){
    // $userId = "id queried from DB"
    $privateKeyPath = '/var/www/private/private.pem';
    
    // Creates the cookie and sets it in the user's session
    $jwt = generateJWT($db_Id, $user_rol, $privateKeyPath);
    setJWTInCookie($jwt);
    return $jwt;
}
?>