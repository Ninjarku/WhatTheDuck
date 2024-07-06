<?php
session_start();
require 'vendor/autoload.php';  // Assuming you use an autoload file for database connection utilities

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['newPassword'], $_POST['confirmPassword']) && $_POST['newPassword'] === $_POST['confirmPassword']) {
        $newPassword = $_POST['newPassword']; 
        $email = $_SESSION['email'];  

        // Hash password (you might choose a different method or options)
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
