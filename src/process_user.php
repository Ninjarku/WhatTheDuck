<?php
session_start();
ini_set('display_errors', 1);
error_reporting(E_ALL);

$response = array(
    "icon" => "error",
    "title" => "Operation failed!",
    "message" => "Please try again.",
    "redirect" => null
);

function getDatabaseConnection()
{
    $config = parse_ini_file('/var/www/private/db-config.ini');
    $conn = new mysqli($config['host'], $config['username'], $config['password'], $config['dbname']);
    if ($conn->connect_error) {
        return null;
    }
    return $conn;
}

function getAllUsers()
{
    $conn = getDatabaseConnection();
    global $response;
    if (!$conn) {
        $response["message"] = 'Database connection failed';
        return json_encode($response);
    }

    $stmt = $conn->prepare("SELECT User_ID, Username, Email, Mobile_Number, Billing_Address, Gender, DOB, User_Type, Account_Active FROM User ORDER BY User_ID ASC");
    if (!$stmt) {
        $response["message"] = 'Prepare failed: ' . $conn->error;
        return json_encode($response);
    }

    $stmt->execute();
    $result = $stmt->get_result();
    $arrResult = [];

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $arrResult[] = $row;
        }
    }
    $stmt->close();
    $conn->close();
    return json_encode(['icon' => 'success', 'data' => $arrResult]);
}

function getUserbyUserID($User_ID)
{
    $conn = getDatabaseConnection();
    $response = [];
    if (!$conn) {
        $response["message"] = 'Database connection failed';
        return json_encode($response);
    }

    $stmt = $conn->prepare("SELECT * FROM User WHERE User_ID=?");
    if (!$stmt) {
        $response["message"] = 'Prepare failed: ' . $conn->error;
        return json_encode($response);
    }

    $stmt->bind_param("i", $User_ID);
    if (!$stmt->execute()) {
        $response["message"] = 'Execute failed: ' . $stmt->error;
        return json_encode($response);
    }

    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        return json_encode($row);
    } else {
        $response["message"] = 'No user found with the given ID' . $stmt->error;
        return json_encode($response);
    }

    $stmt->close();
    $conn->close();
}

function addUser($userData)
{
    $conn = getDatabaseConnection();
    global $response;
    if (!$conn) {
        $response["message"] = 'Database connection failed';
        return json_encode($response);
    }

    $stmt = $conn->prepare("SELECT * FROM User WHERE Username=?");
    $stmt->bind_param("s", $userData["Username"]);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $response["message"] = 'Username is already taken.';
        return json_encode($response);
    }

    $stmt->close();

    $stmt = $conn->prepare("INSERT INTO User (Username, Password, Email, Mobile_Number, Billing_Address, Gender, DOB, User_Type, Account_Active) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
    if (!$stmt) {
        $response["message"] = 'Prepare failed: ' . $conn->error;
        return json_encode($response);
    }

    $username = sanitize_input($userData["Username"]);
    $hashedPassword = password_hash(sanitize_input($userData["Password"]), PASSWORD_DEFAULT);
    $email = sanitize_input($userData["Email"]);
    $mobileNumber = validate_mobile_number($userData["Mobile_Number"]);
    $billingAddress = sanitize_input($userData["Billing_Address"]);
    $gender = sanitize_input($userData["Gender"]);
    $dob = sanitize_input($userData["DOB"]);
    $userType = sanitize_input($userData["User_Type"]);
    $accountActive = isset($userData["Account_Active"]) && $userData["Account_Active"] == 1 ? 1 : 0;

    if ($mobileNumber === false) {
        $response["message"] = 'Invalid mobile number format.';
        return json_encode($response);
    } else {
        $mobileNumber = sanitize_input($userData["Mobile_Number"]);
    }

    $stmt->bind_param(
        "ssssssssi",
        $username,
        $hashedPassword,
        $email,
        $mobileNumber,
        $billingAddress,
        $gender,
        $dob,
        $userType,
        $accountActive
    );

    if (!$stmt->execute()) {
        $response["message"] = 'Execute failed: ' . $stmt->error;
        return json_encode($response);
    }

    $stmt->close();
    $conn->close();

    $response["icon"] = "success";
    $response["title"] = "User Added";
    $response["message"] = "User added successfully";
    $response["redirect"] = "admin_index.php";
    return json_encode($response);
}

function deleteUser($User_ID)
{
    $conn = getDatabaseConnection();
    global $response;
    if (!$conn) {
        $response["message"] = 'Database connection failed';
        return json_encode($response);
    }

    if (empty($User_ID)) {
        $response["message"] = 'Empty User ID.';
        return json_encode($response);
    }

    $stmt = $conn->prepare("DELETE FROM User WHERE User_ID = ?");
    if (!$stmt) {
        $response["message"] = 'Prepare failed: ' . $conn->error;
        return json_encode($response);
    }

    $stmt->bind_param("i", $User_ID);
    if (!$stmt->execute()) {
        $response["message"] = 'Execute failed: ' . $stmt->error;
        return json_encode($response);
    }

    $stmt->close();
    $conn->close();

    $response["icon"] = "success";
    $response["title"] = "User Deleted";
    $response["message"] = "User deleted successfully";
    return json_encode($response);
}

function editUser($userData)
{
    $conn = getDatabaseConnection();
    global $response;
    if (!$conn) {
        $response["message"] = 'Database connection failed';
        return json_encode($response);
    }

    $stmt = $conn->prepare("UPDATE User SET Username=?, Email=?, Mobile_Number=?, Billing_Address=?, Gender=?, DOB=?, User_Type=?, Account_Active=? WHERE User_ID=?");
    if (!$stmt) {
        $response["message"] = 'Prepare failed: ' . $conn->error;
        return json_encode($response);
    }

    $username = sanitize_input($userData["Username"]);
    $email = sanitize_input($userData["Email"]);
    $mobileNumber = validate_mobile_number($userData["Mobile_Number"]);
    $billingAddress = sanitize_input($userData["Billing_Address"]);
    $gender = sanitize_input($userData["Gender"]);
    $dob = sanitize_input($userData["DOB"]);
    $userType = sanitize_input($userData["User_Type"]);
    $accountActive = isset($userData["Account_Active"]) && $userData["Account_Active"] == 1 ? 1 : 0;

    if ($mobileNumber === false) {
        $response["message"] = 'Invalid mobile number format.';
        return json_encode($response);
    } else {
        $mobileNumber = sanitize_input($userData["Mobile_Number"]);
    }

    $stmt->bind_param(
        "ssssssssi",
        $username,
        $email,
        $mobileNumber,
        $billingAddress,
        $gender,
        $dob,
        $userType,
        $accountActive,
        $userData["User_ID"]
    );

    if (!$stmt->execute()) {
        $response["message"] = 'Execute failed: ' . $stmt->error;
        return json_encode($response);
    }

    $stmt->close();
    $conn->close();

    $response["icon"] = "success";
    $response["title"] = "User Updated";
    $response["message"] = "User updated successfully";
    $response["redirect"] = "admin_index.php";
    return json_encode($response);
}

function sanitize_input($data)
{
    $data = trim($data);
    $data = stripslashes($data);
    return $data;
}

function validate_mobile_number($mobileNumber)
{
    return preg_match('/^\d{8}$/', $mobileNumber) ? $mobileNumber : false;
}

$action = isset($_GET['action']) ? $_GET['action'] : (isset($_POST['action']) ? $_POST['action'] : null);

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    if ($action === 'getAllUsers') {
        echo getAllUsers();
    } elseif ($action === 'getUserbyUserID' && isset($_GET['User_ID'])) {
        $userID = intval($_GET['User_ID']);
        $userData = getUserbyUserID($userID);
        echo $userData;
    }
} elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if ($action === 'addUser') {
        echo addUser($_POST);
    } elseif ($action === 'editUser') {
        echo editUser($_POST);
    } elseif ($action === 'deleteUser' && isset($_POST['User_ID'])) {
        echo deleteUser($_POST['User_ID']);
    }
}
?>