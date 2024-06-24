<?php
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

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
        $decoded = JWT::decode($jwt, $publicKey, [$algorithm]);
        $decoded_array = (array) $decoded;

        // Verify IP and User-Agent
        if ($decoded_array['ip'] !== $_SERVER['REMOTE_ADDR'] || $decoded_array['ua'] !== $_SERVER['HTTP_USER_AGENT']) {
            throw new Exception("Invalid token: IP/User-Agent mismatch.");
        }

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
        return null; // Invalid token
    }
}


?>