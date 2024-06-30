<?php

// process_user.php

ini_set('display_errors', 1);
error_reporting(E_ALL);

function getAllUsers() {
    $config = parse_ini_file('/var/www/private/db-config.ini');
    $conn = new mysqli($config['host'], $config['username'], $config['password'], $config['dbname']);

    if ($conn->connect_error) {
        error_log("Connection failed: " . $conn->connect_error, 3, "/var/www/logs/error.log");
        header("Location: error_page.php?error_id=1&error=" . urlencode("Connection failed: " . $conn->connect_error));
        exit();
    } else {
        $stmt = $conn->prepare("SELECT User_ID, Username, Email, Mobile_Number, Billing_Address, Gender, DOB, User_Type, Account_Active FROM User ORDER BY User_ID ASC");
        if (!$stmt) {
            error_log("Prepare failed: " . $conn->error, 3, "/var/www/logs/error.log");
            header("Location: error_page.php?error_id=1&error=" . urlencode("Prepare failed: " . $conn->error));
            exit();
        }

        $stmt->execute();
        $result = $stmt->get_result();

        $arrResult = array();
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $arrResult[] = $row;
            }
        }
        $stmt->close();
        $conn->close();
        return json_encode($arrResult);
    }
}

function deleteUser($User_ID) {
    $config = parse_ini_file('/var/www/private/db-config.ini');
    $conn = new mysqli($config['host'], $config['username'], $config['password'], $config['dbname']);

    if ($conn->connect_error) {
        error_log("Connection failed: " . $conn->connect_error, 3, "/var/www/logs/error.log");
        header("Location: error_page.php?error_id=2&error=" . urlencode("Connection failed: " . $conn->connect_error));
        exit();
    } else {
        if (empty($User_ID)) {
            header("Location: error_page.php?error_id=2&error=" . urlencode("Empty User ID."));
            exit();
        }

        $stmt = $conn->prepare("DELETE FROM User WHERE User_ID = ?");
        if (!$stmt) {
            error_log("Prepare failed: " . $conn->error, 3, "/var/www/logs/error.log");
            header("Location: error_page.php?error_id=2&error=" . urlencode("Prepare failed: " . $conn->error));
            exit();
        }

        $stmt->bind_param("i", $User_ID);
        if (!$stmt->execute()) {
            error_log("Execute failed: " . $stmt->error, 3, "/var/www/logs/error.log");
            header("Location: error_page.php?error_id=2&error=" . urlencode("Execute failed: " . $stmt->error));
            exit();
        }

        $stmt->close();
        $conn->close();
        return header("Location: error_page.php?error_id=-1&error=" . urlencode("User deleted successfully"));
    }
}

function getUserById($User_ID) {
    $config = parse_ini_file('/var/www/private/db-config.ini');
    $conn = new mysqli($config['host'], $config['username'], $config['password'], $config['dbname']);

    if ($conn->connect_error) {
        error_log("Connection failed: " . $conn->connect_error, 3, "/var/www/logs/error.log");
        header("Location: error_page.php?error_id=3&error=" . urlencode("Connection failed: " . $conn->connect_error));
        exit();
    } else {
        if (empty($User_ID)) {
            header("Location: error_page.php?error_id=3&error=" . urlencode("Empty User ID."));
            exit();
        }

        $stmt = $conn->prepare("SELECT * FROM User WHERE User_ID = ?");
        if (!$stmt) {
            error_log("Prepare failed: " . $conn->error, 3, "/var/www/logs/error.log");
            header("Location: error_page.php?error_id=3&error=" . urlencode("Prepare failed: " . $conn->error));
            exit();
        }

        $stmt->bind_param("i", $User_ID);
        if (!$stmt->execute()) {
            error_log("Execute failed: " . $stmt->error, 3, "/var/www/logs/error.log");
            header("Location: error_page.php?error_id=3&error=" . urlencode("Execute failed: " . $stmt->error));
        }

        $result = $stmt->get_result();
        $user = $result->fetch_assoc();

        $stmt->close();
        $conn->close();

        return json_encode($user);
    }
}

function addUser($userData) {
    $config = parse_ini_file('/var/www/private/db-config.ini');
    $conn = new mysqli($config['host'], $config['username'], $config['password'], $config['dbname']);

    // Check connection
    if ($conn->connect_error) {
        error_log("Connection failed: " . $conn->connect_error, 3, "/var/www/logs/error.log");
        header("Location: error_page.php?error_id=4&error=" . urlencode("Connection failed: " . $conn->connect_error));
        exit();
    } else {
        // Check if the username is already taken
        $stmt = $conn->prepare("SELECT * FROM User WHERE Username=?");
        $stmt->bind_param("s", $userData["Username"]);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            header("Location: error_page.php?error_id=4&error=" . urlencode("Username is already taken.")); // Redirect to login page
            exit();
        } else {
            $stmt->close();

            // Prepare statement to insert user details
            $stmt = $conn->prepare("INSERT INTO User (Username, Password, Email, Mobile_Number, Billing_Address, Gender, DOB, User_Type, Account_Active) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
            if (!$stmt) {
                error_log("Prepare failed: " . $conn->error, 3, "/var/www/logs/error.log");
                header("Location: error_page.php?error_id=4&error=" . urlencode("Prepare failed: " . $conn->error));
                exit();
            }

            // Sanitize inputs
            $username = sanitize_input($userData["Username"]);
            $hashedPassword = password_hash(sanitize_input($userData["Password"]), PASSWORD_DEFAULT);
            $email = sanitize_input($userData["Email"]);
            $mobileNumber = validate_mobile_number($userData["Mobile_Number"]); // Assuming this function validates and returns a valid mobile number
            $billingAddress = sanitize_input($userData["Billing_Address"]);
            $gender = sanitize_input($userData["Gender"]);
            $dob = sanitize_input($userData["DOB"]);
            $userType = sanitize_input($userData["User_Type"]);
            $accountActive = isset($userData["Account_Active"]) && $userData["Account_Active"] == 1 ? 1 : 0;

            // Check if validation failed for mobile number
            if ($mobileNumber === false) {
                header("Location: error_page.php?error_id=4&error=" . urlencode("Invalid mobile number format."));
                exit();
            } else {
                $mobileNumber = sanitize_input($userData["Mobile_Number"]);
            }

            // Bind parameters
            $stmt->bind_param("ssssssssi",
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
                error_log("Execute failed: " . $stmt->error, 3, "/var/www/logs/error.log");
                header("Location: error_page.php?error_id=4&error=" . urlencode("Execute failed: " . $stmt->error));
            }
        }
    }
    $stmt->close();
    $conn->close();

    return header("Location: error_page.php?error_id=-1&error=" . urlencode("User added successfully"));
}

function editUser($userData) {
    $config = parse_ini_file('/var/www/private/db-config.ini');
    $conn = new mysqli($config['host'], $config['username'], $config['password'], $config['dbname']);

    if ($conn->connect_error) {
        error_log("Connection failed: " . $conn->connect_error, 3, "/var/www/logs/error.log");
        header("Location: error_page.php?error_id=5&error=" . urlencode("Connection failed: " . $conn->connect_error));
        exit();
    } else {
        $stmt = $conn->prepare("UPDATE User SET Username=?, Email=?, Mobile_Number=?, Billing_Address=?, Gender=?, DOB=?, User_Type=?, Account_Active=? WHERE User_ID=?");
        if (!$stmt) {
            error_log("Prepare failed: " . $conn->error, 3, "/var/www/logs/error.log");
            header("Location: error_page.php?error_id=5&error=" . urlencode("Prepare failed: " . $conn->error));
            exit();
        }

        // Sanitize inputs
        $username = sanitize_input($userData["Username"]);
        $email = sanitize_input($userData["Email"]);
        $mobileNumber = validate_mobile_number($userData["Mobile_Number"]); // Assuming this function validates and returns a valid mobile number
        $billingAddress = sanitize_input($userData["Billing_Address"]);
        $gender = sanitize_input($userData["Gender"]);
        $dob = sanitize_input($userData["DOB"]);
        $userType = sanitize_input($userData["User_Type"]);
        $accountActive = isset($userData["Account_Active"]) && $userData["Account_Active"] == 1 ? 1 : 0;

        // Check if validation failed for mobile number
        if ($mobileNumber === false) {
            header("Location: error_page.php?error_id=4&error=" . urlencode("Invalid mobile number format."));
            exit();
        } else {
            $mobileNumber = sanitize_input($userData["Mobile_Number"]);
        }

        // Bind parameters
        $stmt->bind_param("ssssssssi",
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
            error_log("Execute failed: " . $stmt->error, 3, "/var/www/logs/error.log");
            header("Location: error_page.php?error_id=5&error=" . urlencode("Execute failed: " . $stmt->error));
        }

        $stmt->close();
        $conn->close();

        return header("Location: error_page.php?error_id=-1&error=" . urlencode("User updated successfully"));       
    }
}

function sanitize_input($data) {
    $data = trim($data);
    $data = stripslashes($data);
    return $data;
}

function validate_mobile_number($mobileNumber) {
    // Implement your validation logic here
    // Example: Ensure it is an 8-digit number
    return preg_match('/^\d{8}$/', $mobileNumber) ? $mobileNumber : false;
}

// Main execution
$action = isset($_GET['action']) ? $_GET['action'] : (isset($_POST['action']) ? $_POST['action'] : null);

if ($action === 'getAllUsers') {
    header('Content-Type: application/json');
    echo getAllUsers();
} elseif ($action === 'deleteUser' && isset($_GET['User_ID'])) {
    header('Content-Type: application/json');
    echo deleteUser($_GET['User_ID']);
} elseif ($action === 'getUser' && isset($_GET['User_ID'])) {
    header('Content-Type: application/json');
    echo getUserById($_GET['User_ID']);
} elseif ($action === 'addUser' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    header('Content-Type: application/json');
    echo addUser($_POST);
} elseif ($action === 'editUser' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    header('Content-Type: application/json');
    echo editUser($_POST);
}
