<?php
session_start();
require_once 'jwt/jwt_cookie.php';
header('Content-Type: application/json');

$response = array(
    "icon" => "error",
    "title" => "Profile update failed!",
    "message" => "Please try again.",
    "redirect" => null
);

$decodedToken = checkAuthentication();
if (!$decodedToken) {
    $response["message"] = "Unauthorized access. Please log in.";
    echo json_encode($response);
    exit();
}

function sanitize_input($data)
{
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

$success = true;
$errorMsg = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $cust_user = $decodedToken['cust_username'];
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

    // Handle profile image upload with validation
    if ($success && isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] == UPLOAD_ERR_OK) {
        $allowed_types = ['image/jpeg', 'image/png'];
        $max_size = 1 * 1024 * 1024; // 1MB
        $file_type = $_FILES['profile_image']['type'];
        $file_size = $_FILES['profile_image']['size'];

        if (!in_array($file_type, $allowed_types)) {
            $errorMsg .= 'Invalid file type. Only JPG and PNG files are allowed.';
            $success = false;
        }

        if ($file_size > $max_size) {
            $errorMsg .= 'File size too large. Maximum allowed size is 1MB.';
            $success = false;
        }

        if ($success) {
            $imageData = file_get_contents($_FILES['profile_image']['tmp_name']);
            $profile_image = $imageData;
        }
    }

    // Update user information if validation is successful
    if ($success) {
        if ($profile_image) {
            $stmt = $conn->prepare("UPDATE User SET Email = ?, Mobile_Number = ?, Billing_Address = ?, Gender = ?, DOB = ?, Profile_Image = ? WHERE User_ID = ?");
            $stmt->bind_param("ssssssi", $email, $mobile, $address, $gender, $birthday, $profile_image, $cust_user_id);
        } else {
            $stmt = $conn->prepare("UPDATE User SET Email = ?, Mobile_Number = ?, Billing_Address = ?, Gender = ?, DOB = ? WHERE User_ID = ?");
            $stmt->bind_param("sssssi", $email, $mobile, $address, $gender, $birthday, $cust_user_id);
        }

        if (!$stmt) {
            $errorMsg = "Prepare failed: " . $conn->error;
            $success = false;
        } else {
            if ($stmt->execute()) {
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