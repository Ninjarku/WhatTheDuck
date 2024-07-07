<?php
require '/var/www/html/vendor/autoload.php';

use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Predis\Client;

$redis = new Client([
    'scheme' => 'tcp',
    // 'host'   => 'localhost', // either this or redis
    'host' => 'redis', // either this or redis
    'port' => 6379,
]);

// Validate JWT
function validateJWT($jwt, $publicKeyPath, $algorithm = 'RS256')
{
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


function getJWTFromCookie($cookieName = 'auth_token')
{
    if (isset($_COOKIE[$cookieName])) {
        return $_COOKIE[$cookieName];
    } else {
        return null;
    }
}

function checkAuthentication($requiredRole = null)
{
    $jwt = getJWTFromCookie();

    if (!$jwt) {
        header('Location: Login.php');
        exit();
    }

    $publicKeyPath = '/var/www/private/public.pem';
    $decodedToken = validateJWT($jwt, $publicKeyPath);

    if (!$decodedToken) {
        header('Location: Login.php');
        exit();
    }

    // Check if the user role matches the required role
    if ($requiredRole && $decodedToken['rol'] !== $requiredRole) {
        ?>
        <script>
            window.location.href = 'error_page.php?error_id=0&error=' + encodeURIComponent('Unauthorized access');
        </script>
        <?php
        exit();
    }

    return $decodedToken; // Return decoded token if valid and role matches
}

function blacklistToken($token)
{
    global $redis; // Make sure $redis is accessible
    $payload = validateJWT($token, "/var/www/private/public.pem");

    if ($payload) {
        $redis->set("bl_$token", "true");
        $redis->expireAt("bl_$token", $payload['exp']);
        // echo json_encode(['message' => 'Token invalidated']);
    } else {
        // echo json_encode(['message' => 'Invalid token']);
    }
}

function isTokenBlacklisted($token)
{
    global $redis; // Make sure $redis is accessible
    return $redis->exists("bl_$token");
}


function unsetJWTInCookie($cookieName = 'auth_token')
{
    // Ensure the cookie is sent only over HTTPS and is inaccessible via JavaScript
    $isSecure = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on';
    $cookieParams = [
        'expires' => time() - 3600,  // Set the expiration time to 1 hour in the past
        'path' => '/',
        'domain' => 'whattheduck.ddns.net',
        'secure' => $isSecure,
        'httponly' => true,
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