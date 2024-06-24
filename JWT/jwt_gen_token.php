<?php
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

// Generate JWT
function generateJWT($userId, $privateKeyPath, $algorithm = 'RS256') {
    $issuedAt = time();
    $expirationTime = $issuedAt + (3 * 60 * 60);  // jwt valid for 3 hours from the issued time
    $payload = [
        'iat' => $issuedAt,
        'exp' => $expirationTime,
        'id' => $userId,
        'rol' => $userRole
    ];

    // Load RSA private key
    $privateKey = file_get_contents($privateKeyPath);

    // Generate the JWT
    $jwt = JWT::encode($payload, $privateKey, $algorithm);

    return $jwt;
}

function setJWTInCookie($jwt, $cookieName = 'auth_token', $cookieExpiry = 10800) {
    // Ensure the cookie is sent only over HTTPS and is inaccessible via JavaScript
    $isSecure = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on';
    $cookieParams = [
        'expires' => time() + $cookieExpiry,
        'path' => '/',
        'domain' => 'whattheduck.com',
        'secure' => $isSecure,
        'httponly' => true,
        'samesite' => 'Strict', 
    ];

    setcookie($cookieName, $jwt, $cookieParams);
}

?>