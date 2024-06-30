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
    "title" => "Login failed!",
    "message" => "Please try again.",
    "redirect" => null
);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $success = true;

    if (empty($_POST["cust_username"]) || empty($_POST["cust_pass"])) {
        $response["message"] = "Please fill in all required fields.";
        $success = false;
    } else {
        $cust_username = sanitize_input($_POST["cust_username"]);
        $cust_pw = $_POST["cust_pass"];

        if (!filter_var($cust_username, FILTER_SANITIZE_FULL_SPECIAL_CHARS)) {
            $response["message"] = "Invalid username format.";
            $success = false;
        }
    }

    if ($success) {
        $config = parse_ini_file('/var/www/private/db-config.ini');
        $conn = new mysqli($config['servername'], $config['username'], $config['password'], $config['dbname']);

        if ($conn->connect_error) {
            $response["message"] = "Connection failed: " . $conn->connect_error;
        } else {
            $stmt = $conn->prepare("SELECT * FROM User WHERE Username=?");
            $stmt->bind_param("s", $cust_username);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                $row = $result->fetch_assoc();
                $db_username = $row["Username"];
                $db_password = $row["Password"];
                $cust_id = $row["User_ID"];

                if (password_verify($cust_pw, $db_password)) {
                    $_SESSION["cust_login"] = "success";
                    $_SESSION["cust_username"] = $cust_username;
                    $_SESSION["userid"] = $cust_id;
                    $_SESSION["cust_id"] = $cust_id;

                    $response["icon"] = "success";
                    $response["title"] = "Login successful!";
                    $response["message"] = "Welcome back, " . htmlspecialchars($cust_username);
                    $response["redirect"] = "index.php";
                } else {
                    $response["message"] = "Invalid username or password.";
                }
            } else {
                $response["message"] = "Invalid username or password.";
            }

            $stmt->close();
        }

        $conn->close();
    }
}

echo json_encode($response);
?>
