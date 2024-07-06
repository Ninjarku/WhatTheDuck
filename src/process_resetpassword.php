<?php
session_start();
require 'vendor/autoload.php';  // Make sure this autoloads Predis or your Redis client library
use Predis\Client;

// Assume Redis client setup
$redis = new Client([
    'scheme' => 'tcp',
    'host'   => 'redis', 
    'port'   => 6379,
]);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['newPassword'], $_POST['confirmPassword']) && $_POST['newPassword'] === $_POST['confirmPassword']) {
        $newPassword = $_POST['newPassword']; 
        $email = $_SESSION['email'];  

        // Hash password
        $hashedPassword = password_hash($newPassword, PASSWORD_ARGON2ID);

        // Database connection and update query
        $config = parse_ini_file('/var/www/private/db-config.ini');
        $conn = new mysqli($config['host'], $config['username'], $config['password'], $config['dbname']);
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }

        $stmt = $conn->prepare("UPDATE User SET Password = ? WHERE Email = ?");
        $stmt->bind_param("ss", $hashedPassword, $email);

        if ($stmt->execute()) {
            // If password update is successful, delete user-related data from Redis
            $redis->del(["otp:$email"]);  // Adjust key as necessary to match how you've stored the OTP or session data

            $_SESSION = [];
            session_destroy();
            header('Location: Login.php');
            exit;
        } else {
            echo "Error updating password: " . $stmt->error;
        }

        $stmt->close();
        $conn->close();
    } else {
        echo "Passwords do not match.";
    }
}
?>
