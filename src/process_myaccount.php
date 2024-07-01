<?php
session_start();
header('Content-Type: application/json');

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$response = array(
    "icon" => "error",
    "title" => "Profile update failed!",
    "message" => "Please try again.",
    "redirect" => null
);

function sanitize_input($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

$success = true;
$errorMsg = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $cust_user = $_SESSION['cust_username'];
    $username = sanitize_input($_POST['username_input']);
    $email = sanitize_input($_POST["email_input"]);
    $mobile = sanitize_input($_POST["mobile_input"]);
    $address = sanitize_input($_POST["address_input"]);
    $gender = sanitize_input($_POST["gender_input"]);
    $birthday = sanitize_input($_POST['birthday_input']);
    $profile_image = null;

    // Database connection
    $config = parse_ini_file('/var/www/private/db-config.ini');
    $conn = new mysqli($config['host'], $config['username'], $config['password'], $config['dbname']);

    if ($conn->connect_error) {
        $errorMsg = "Connection failed: " . $conn->connect_error;
        $success = false;
    }

    // Validate email format
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errorMsg .= "Invalid email format.<br>";
        $success = false;
    }

    // Check if email already exists in the database for another user
    if ($success) {
        $stmt = $conn->prepare("SELECT Email FROM User WHERE Email = ? AND Username != ?");
        $stmt->bind_param("ss", $email, $cust_user);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $success = false;
            $errorMsg .= "Email already in use.<br>";
        }
        $stmt->close();
    }

    // Handle profile image upload
    if ($success && isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] == UPLOAD_ERR_OK) {
        $imageData = file_get_contents($_FILES['profile_image']['tmp_name']);
        $profile_image = $imageData;
    }

    // Update user information if validation is successful
    if ($success) {
        if ($profile_image) {
            $stmt = $conn->prepare("UPDATE User SET Username = ?, Email = ?, Mobile_Number = ?, Billing_Address = ?, Gender = ?, DOB = ?, Profile_Image = ? WHERE Username = ?");
            $stmt->bind_param("ssssssss", $username, $email, $mobile, $address, $gender, $birthday, $profile_image, $cust_user);
        } else {
            $stmt = $conn->prepare("UPDATE User SET Username = ?, Email = ?, Mobile_Number = ?, Billing_Address = ?, Gender = ?, DOB = ? WHERE Username = ?");
            $stmt->bind_param("sssssss", $username, $email, $mobile, $address, $gender, $birthday, $cust_user);
        }

        if ($stmt->execute()) {
            $_SESSION['cust_username'] = $username;  // Update session username if changed
            $response["icon"] = "success";
            $response["title"] = "Profile updated successfully!";
            $response["message"] = "Your profile has been updated.";
            $response["redirect"] = "MyAccount.php";
        } else {
            $success = false;
            $errorMsg = "Update failed: " . $stmt->error;
        }
        $stmt->close();
    }

    // Display error message if update fails
    if (!$success) {
        $response["message"] = $errorMsg;
    }

    $conn->close();
} else {
    $response["message"] = "Invalid request! Please submit the form properly.";
}

echo json_encode($response);
?>
