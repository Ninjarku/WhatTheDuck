<?php

session_start();
header('Content-Type: application/json');

function sanitize_input($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

$response = array(
    "icon" => "error",
    "title" => "Signup failed!",
    "message" => "Please try again.",
    "redirect" => null
);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $success = true;

    if (empty($_POST["signup_mobile_number"]) || empty($_POST["signup_email"]) || empty($_POST["signup_birthday"]) || empty($_POST["signup_username"]) || empty($_POST["signup_pwd"]) || empty($_POST["signup_pwdconfirm"]) || !isset($_POST["agree"])) {
        $response["message"] = "Please fill in all required fields and agree to terms and conditions.";
        $success = false;
    } else {
        $mobile = $_POST["signup_mobile_number"];
        $email = sanitize_input($_POST["signup_email"]);
        $birthdaystr = sanitize_input($_POST["signup_birthday"]);
        $birthday = sanitize_input(date('Y-m-d H:i:s', strtotime($birthdaystr)));
        $username = sanitize_input($_POST["signup_username"]);
        $pwd = sanitize_input($_POST["signup_pwd"]);
        $pwd_confirm = sanitize_input($_POST["signup_pwdconfirm"]);
        $agree = sanitize_input($_POST["agree"]);
        $user_type = 'Customer';

        if (!filter_var($username, FILTER_SANITIZE_FULL_SPECIAL_CHARS)) {
            $response["message"] = "Invalid username format.";
            $success = false;
        }
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $response["message"] = "Invalid email format.";
            $success = false;
        }
        if (!preg_match("/^[0-9]{8}$/", $mobile)) {
            $response["message"] = "Invalid mobile number format. Please enter exactly 8 digits.";
            $success = false;
        }
        if ($pwd !== $pwd_confirm) {
            $response["message"] = "Passwords do not match.";
            $success = false;
        }
    }

    if ($success) {
        $config = parse_ini_file('/var/www/private/db-config.ini');
        $conn = new mysqli($config['servername'], $config['username'], $config['password'], $config['dbname']);

        if ($conn->connect_error) {
            $response["message"] = "Connection failed: " . $conn->connect_error;
        } else {
            // Check if username, email, or phone number already exists
            $stmt0 = $conn->prepare("SELECT Username, Email, Mobile_Number FROM User WHERE Username = ? OR Email = ? OR Mobile_Number = ?");
            $stmt0->bind_param("sss", $username, $email, $mobile);
            $stmt0->execute();
            $stmt0->store_result();
            if ($stmt0->num_rows > 0) {
                $stmt0->bind_result($existing_username, $existing_email, $existing_mobile);
                $stmt0->fetch();
                if ($existing_username === $username) {
                    $response["message"] = "Username already exists.";
                } elseif ($existing_email === $email) {
                    $response["message"] = "Email already exists.";
                } elseif ($existing_mobile === $mobile) {
                    $response["message"] = "Phone number already exists.";
                }
            } else {
                $pwd_hashed = password_hash($pwd, PASSWORD_DEFAULT);
                $stmt1 = $conn->prepare("INSERT INTO User (Username, Email, DOB, Mobile_Number, Password, User_Type) VALUES (?, ?, ?, ?, ?, ?)");
                $stmt1->bind_param("ssssss", $username, $email, $birthday, $mobile, $pwd_hashed, $user_type);
                if ($stmt1->execute()) {
                    $_SESSION["signup_success"] = "Account created successfully!";
                    $response["icon"] = "success";
                    $response["title"] = "Signup successful!";
                    $response["message"] = "Please Login!";
                    $response["redirect"] = "Login.php";
                } else {
                    $response["message"] = "Signup failed. Please try again.";
                }
                $stmt1->close();
            }
            $stmt0->close();
        }
        $conn->close();
    }
}

echo json_encode($response);
?>
