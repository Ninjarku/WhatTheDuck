<?php


// Add a token to the blacklist
function blacklistToken($token) {
    $pdo = getDbConnection();
    $stmt = $pdo->prepare('INSERT INTO jwt_blacklist (token) VALUES (:token)');
    $stmt->execute(['token' => $token]);
}

// Check if a token is blacklisted
function isTokenBlacklisted($token) {
    $pdo = getDbConnection();
    $stmt = $pdo->prepare('SELECT COUNT(*) FROM jwt_blacklist WHERE token = :token');
    $stmt->execute(['token' => $token]);
    return $stmt->fetchColumn() > 0;
?>