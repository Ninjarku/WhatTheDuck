<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

if (isset($_GET['action']) && $_GET['action'] == 'deleteCartItem') {
    $config = parse_ini_file('/var/www/private/db-config.ini');
    $conn = new mysqli($config['host'], $config['username'], $config['password'], $config['dbname']);

    $cartId = $_GET['cartid']; // Get the cart ID from the POST data

    // Validate the cart ID
    if (!empty($cartId) && is_numeric($cartId)) {
        $cartId = intval($cartId);
        // Prepare the statement
        if ($stmt = $conn->prepare("DELETE FROM Cart WHERE Cart_ID = ?")) {
            $stmt->bind_param("i", $cartId);
            
            if ($stmt->execute()) {
                // Success response
                return json_encode([
                    'success' => true,
                    'message' => 'Item deleted successfully.'
                ]);
            } else {
                // Error response
                return json_encode([
                    'success' => false,
                    'message' => 'Error deleting item.'
                ]);
            }

            $stmt->close();
        } else {
            // Error response
            return json_encode([
                'success' => false,
                'message' => 'Database error.'
            ]);
        }
    } else {
        // Invalid cart ID response
        return json_encode([
            'success' => false,
            'message' => 'Invalid cart ID.'
        ]);
    }

    $conn->close(); 
}

function getCartItemByUserId() {
    $User_ID = $_SESSION['userid'];
    $config = parse_ini_file('/var/www/private/db-config.ini');
    $conn = new mysqli($config['host'], $config['username'], $config['password'], $config['dbname']);

    if ($conn->connect_error) {
        error_log("Connection failed: " . $conn->connect_error, 3, "/var/www/logs/error.log");
        header("Location: error_page.php?error_id=6&error=" . urlencode("Connection failed: " . $conn->connect_error));
        exit();
    } else {
        $stmt = $conn->prepare("SELECT c.Cart_ID, c.Product_ID, c.Quantity, c.Price, c.Total_Price, p.Product_Name, p.Product_Image FROM ict2216db.Cart c, ict2216db.Product p WHERE c.Product_ID = p.Product_ID AND User_ID = ?");
        if (!$stmt) {
            error_log("Prepare failed: " . $conn->error, 3, "/var/www/logs/error.log");
            header("Location: error_page.php?error_id=6&error=" . urlencode("Prepare failed: " . $conn->error));
            exit();
        }
        $stmt->bind_param("i", $User_ID);
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

function getCartCount(){
    $User_ID = $_SESSION['userid'];
    // Create database connection.
    $config = parse_ini_file('/var/www/private/db-config.ini');
    $conn = new mysqli($config['host'], $config['username'],
            $config['password'], $config['dbname']);
    // Check connection
    if ($conn->connect_error) {
        $errorMsg = "Connection failed: " . $conn->connect_error;
        echo $errorMsg;
        $success = false;
    } else {
        // Prepare the statement:
        $stmt = $conn->prepare("SELECT COUNT(c.Cart_ID) AS cartcount FROM ict2216db.Cart c, ict2216db.User u WHERE c.User_ID = u.User_ID AND u.User_ID = ?;");
        $stmt->bind_param("s", $User_ID);
        $stmt->execute();
        $result = $stmt->get_result();
        $arrResult=array(); 
        if ($result->num_rows > 0) { 
            while($row = $result->fetch_assoc()) {
                $arrResult[] = $row;  
            }
        }
        echo json_encode($arrResult);

        $stmt->close();
    }
    $conn->close();
}
function updateCartCount(){
    global $cartcount;
    $checksuccess = true;
    $userid = $_SESSION['userid'];
    $config = parse_ini_file('/var/www/private/db-config.ini');
    $conn = new mysqli($config['host'], $config['username'],
            $config['password'], $config['dbname']);
    // Check connection
    if ($conn->connect_error) {
        $errorMsg = "Connection failed: " . $conn->connect_error;
        $checksuccess = false;
    } else {
        // Prepare the statement:
        $stmt = $conn->prepare("SELECT count(Cart_ID) AS cartcount FROM Cart WHERE User_ID = ?;");
        $stmt->bind_param("s", $userid);
        $stmt->execute();
        $result = $stmt->get_result();
        $arrResult=array(); 
        if ($result->num_rows > 0) { 
            while($row = $result->fetch_assoc()) {
                $arrResult[] = $row;  
                $cartcount = $row['cartcount'];
                if ($cartcount != ""){
                    $checksuccess = true;
                }else{ 
                    $checksuccess = false; 
                }
            }
        }else{
            $checksuccess = false;
        } 

        $stmt->close();
    }
    $conn->close();
}

function getSelectedCartItem($cart_ids){
    $totalprice = 0;

    // Create database connection.
    $config = parse_ini_file('/var/www/private/db-config.ini');
    $conn = new mysqli($config['host'], $config['username'], $config['password'], $config['dbname']);

    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
    $arrResult = [];
    // An array to hold the rows of data
    $rows = array();
    // Loop through each cart ID and retrieve the record from the database
    for ($x = 0; $x <= sizeof($cart_ids); $x++) { 
        $stmt = $conn->prepare("SELECT c.Cart_ID, c.Quantity, c.Price, p.Product_Name, p.Product_Image 
                                FROM Cart c
                                JOIN Product p ON c.Product_ID = p.Product_ID 
                                WHERE c.Cart_ID = ?");
        $stmt->bind_param('i', $cart_ids[$x]);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $arrResult[] = $row;  
            }
        }
    }
    return json_encode($arrResult);
}

?>
