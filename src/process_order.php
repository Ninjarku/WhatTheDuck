<?php
session_start();
header('Content-Type: application/json');
ini_set('display_errors', 1);
error_reporting(E_ALL);

$response = array(
    "icon" => "error",
    "title" => "Operation failed!",
    "message" => "Please try again.",
    "redirect" => null
);

// Database connection
function getDatabaseConnection()
{
    $config = parse_ini_file('/var/www/private/db-config.ini');
    $conn = new mysqli($config['host'], $config['username'], $config['password'], $config['dbname']);
    if ($conn->connect_error) {
        return null;
    }
    return $conn;
}

// Get all orders with aggregated totals
function getAllOrders()
{
    $conn = getDatabaseConnection();
    global $response;
    if (!$conn) {
        $response["message"] = 'Database connection failed';
        return json_encode($response);
    }

    // Get the current user's ID from the session
    $userID = $_SESSION['userid'];
    console.log($userID);

    $stmt = $conn->prepare("
        SELECT 
            Order_Num, 
            User_ID, 
            SUM(Quantity) AS Total_Items, 
            SUM(Total_Price) AS Total_Price, 
            Payment_Type, 
            Billing_Address, 
            Order_Status 
        FROM `Order`
        WHERE User_ID = ?
        GROUP BY Order_Num, User_ID, Payment_Type, Billing_Address, Order_Status
        ORDER BY Order_Num ASC
    ");

    $stmt->bind_param("i", $userID);
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

// Mark order as received
function markAsReceived($orderNum)
{
    $conn = getDatabaseConnection();
    global $response;
    if (!$conn) {
        $response["message"] = 'Database connection failed';
        return json_encode($response);
    }

    if (empty($orderNum)) {
        $response["message"] = 'Empty Order Number.';
        return json_encode($response);
    }

    $stmt = $conn->prepare("UPDATE `Order` SET Order_Status = 'Received' WHERE Order_Num = ?");
    if (!$stmt) {
        $response["message"] = 'Prepare failed: ' . $conn->error;
        return json_encode($response);
    }

    $stmt->bind_param("s", $orderNum);
    if (!$stmt->execute()) {
        $response["message"] = 'Execute failed: ' . $stmt->error;
        return json_encode($response);
    }

    $stmt->close();
    $conn->close();
    $response["icon"] = "success";
    $response["title"] = "Order Status Updated";
    $response["message"] = "The order status has been updated to Received.";
    return json_encode($response);
}

// Edit order (for example, update order details)
function editOrder($orderData)
{
    $conn = getDatabaseConnection();
    global $response;
    if (!$conn) {
        $response["message"] = 'Database connection failed';
        return json_encode($response);
    }

    $stmt = $conn->prepare("UPDATE `Order` SET User_ID = ?, Product_ID = ?, Quantity = ?, Total_Price = ?, Payment_Type = ?, Billing_Address = ?, Order_Status = ? WHERE Order_Num = ?");
    if (!$stmt) {
        $response["message"] = 'Prepare failed: ' . $conn->error;
        return json_encode($response);
    }

    $userID = filter_var($orderData['User_ID'], FILTER_VALIDATE_INT);
    $productID = filter_var($orderData['Product_ID'], FILTER_VALIDATE_INT);
    $quantity = filter_var($orderData['Quantity'], FILTER_VALIDATE_INT);
    $totalPrice = filter_var($orderData['Total_Price'], FILTER_VALIDATE_FLOAT);
    $paymentType = sanitize_input($orderData['Payment_Type']);
    $billingAddress = sanitize_input($orderData['Billing_Address']);
    $orderStatus = sanitize_input($orderData['Order_Status']);
    $orderNum = sanitize_input($orderData['Order_Num']);

    $stmt->bind_param("iiidssss", $userID, $productID, $quantity, $totalPrice, $paymentType, $billingAddress, $orderStatus, $orderNum);

    if (!$stmt->execute()) {
        $response["message"] = 'Execute failed: ' . $stmt->error;
        return json_encode($response);
    }

    $stmt->close();
    $conn->close();

    $response["icon"] = "success";
    $response["title"] = "Order Updated";
    $response["message"] = "Order updated successfully";
    $response["redirect"] = "order_management.php";
    return json_encode($response);
}

// Sanitize input
function sanitize_input($data)
{
    $data = trim($data);
    $data = stripslashes($data);
    return $data;
}

// Handle actions
$action = isset($_GET['action']) ? $_GET['action'] : (isset($_POST['action']) ? $_POST['action'] : null);

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    if ($action === 'getAllOrders') {
        echo getAllOrders();
    }
} elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if ($action === 'markAsReceived' && isset($_POST['Order_Num'])) {
        echo markAsReceived($_POST['Order_Num']);
    } elseif ($action === 'editOrder') {
        echo editOrder($_POST);
    }
}
?>