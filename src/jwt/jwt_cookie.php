<?php
require '/var/www/html/vendor/autoload.php';

use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Predis\Client;

$redis = new Client([
    'scheme' => 'tcp',
    'host'   => 'localhost', // either this or redis
    // 'host'   => 'redis', // either this or redis
    'port'   => 6379,
]);

// Validate JWT
function validateJWT($jwt, $publicKeyPath, $algorithm = 'RS256') {
    // Load RSA public key
    $publicKey = file_get_contents($publicKeyPath);
    
    // Check if the token is blacklisted
    if (isTokenBlacklisted($jwt)) {
        throw new Exception('Token is blacklisted.');
    }

    if ($publicKey === false) {
        throw new Exception("Public key not found at path: $publicKeyPath");
    }

    try {
        // $decoded = JWT::decode($jwt, new Key($publicKey, $algorithm));
        $decoded = JWT::decode($jwt, new Key($publicKey, 'RS256'));
        $decoded_array = (array) $decoded;

        // Verify expiration
        if ($decoded_array['exp'] < time()) {
            // throw new Exception("Invalid token: Token has expired.");
            return null; // Invalid token
        }

        // Verify issued at time
        if ($decoded_array['iat'] > time()) {
            // throw new Exception("Invalid token: Token issued in the future.");
            return null; // Invalid token
        }

        return $decoded_array; // Valid token
    } catch (Exception $e) {
        // echo $e;
        return null; // Invalid token
    }
}


function getJWTFromCookie($cookieName = 'auth_token') {
    if (isset($_COOKIE[$cookieName])) {
        // echo "JWT found in cookie: " . $_COOKIE[$cookieName];
        return $_COOKIE[$cookieName];
    } else {
        // echo "No JWT found in cookie.";
        return null;
    }
}

function authenticationCheck(){
    try {
        $publicKeyPath = '/var/www/private/public.pem';

        $jwt = getJWTFromCookie();

        if ($jwt) {
            $decodedToken = validateJWT($jwt, $publicKeyPath);
            if ($decodedToken) {
                echo 'Valid JWT: ', json_encode($decodedToken);
                
                if ($decodedToken['rol'] == "SalesAdmin" or $decodedToken['rol'] == "ITAdmin"){
                    // Permit access to salesadmin and itadmin stuff
                    // allow customer access
                }
                else {
                    // Check if its customer has access
                    //do customer stuff 
                }
            } else {
                echo 'Invalid JWT.';
            }
        } else {
            echo 'No JWT found in cookie.';
            // Do redirection to login etc
        }
    } catch (Exception $e) {
        echo 'Error: ', $e->getMessage(), "\n";
    }    
}

function blacklistToken($token){
    global $redis; // Make sure $redis is accessible
    $payload = validateJWT($token, "/var/www/private/public.pem");

    if ($payload) {
        $redis->set("bl_$token", "true");
        $redis->expireAt("bl_$token", $payload['exp']);
        echo json_encode(['message' => 'Token invalidated']);
    } else {
        echo json_encode(['message' => 'Invalid token']);
    }
}

function isTokenBlacklisted($token) {
    global $redis; // Make sure $redis is accessible
    return $redis->exists("bl_$token");
}


function unsetJWTInCookie($cookieName = 'auth_token') {
    // Ensure the cookie is sent only over HTTPS and is inaccessible via JavaScript
    $isSecure = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on';
    $cookieParams = [
        'expires' => time() - 3600,  // Set the expiration time to 1 hour in the past
        'path' => '/',
        'domain' => 'whattheduck.com',
        'secure' => $isSecure,
        'httponly' => true,
        'samesite' => 'Strict', 
    ];

    // for test
    // $cookieParams = [
    //     'expires' => time() - 3600,  // Set the expiration time to 1 hour in the past
    //     'path' => '/',
    //     'secure' => $isSecure,
    //     'httponly' => true,
    // ];

    // Set the cookie with an empty value and past expiration time
    setcookie($cookieName, '', $cookieParams);
}
?>