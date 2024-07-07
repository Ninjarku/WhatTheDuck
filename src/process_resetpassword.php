<?php
session_start();
require 'vendor/autoload.php';  // Make sure this autoloads Predis or your Redis client library
use Predis\Client;

function sanitize_input($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

function password_complexity($pwd){
    $pattern = '/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).+$/';
    if (preg_match($pattern, $pwd)) {
        return true; 
    } else {
        echo "Password must comprise of uppercase, lowercase, and numbers.<br/>";
        return false; 
    }
}

function hasRepetitiveCharacters($pwd) {
    $pattern = '/(.)\1{2}/'; 
    if (preg_match($pattern, $pwd)) {
        echo "Passwords must not contain three or more repetitive characters.<br/>";
        return true; 
    } else {
        return false; 
    }
}

function isPasswordInWordlist($pwd) {
    $wordlistFile = '/var/www/html/pwd_list/list.txt';

    $wordlist = file($wordlistFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

    $existsInList = in_array($pwd, $wordlist);
    
    if ($existsInList) {
        echo "Your chosen password may have been compromised in previous security breaches. <br/>Please choose a new password.<br/>";
        return true;
    }
    
    return false;
}

function passwordLength($pwd) {
    $length = strlen($pwd);
    if ($length >= 12 && $length <= 128) {
        return true;
    }
    echo "Password must be at least 12 characters long.<br/>";
    return false;
}

function confirmPassword($pwd, $confirmpwd) {
    if ($pwd == $confirmpwd) {
        return true;
    }
    echo "Passwords do not match.<br/>";
    return false;
}

function meetPasswordPolicy(){
    $newPassword = sanitize_input($_POST['newPassword']);
    $confirmPassword = sanitize_input($_POST['confirmPassword']);

    $complexity = password_complexity($newPassword);
    $repetitive = hasRepetitiveCharacters($newPassword);
    $inwordlist= isPasswordInWordlist($newPassword);
    $passwordLength = passwordLength($newPassword);
    $passwordSame = confirmPassword($newPassword, $confirmPassword);

    if ($complexity && !$repetitive && !$inwordlist && $passwordLength && $passwordSame) {
        return true;
    }
    
    return false;
}

// Assume Redis client setup
$redis = new Client([
    'scheme' => 'tcp',
    'host'   => 'redis', 
    'port'   => 6379,
]);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['newPassword'], $_POST['confirmPassword']) && meetPasswordPolicy()) {
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
    } 
}
?>
