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

function getAllOrders(){
    $conn = getDatabaseConnection();
    global $response;
    if (!$conn) {
        $response["message"] = 'Database connection failed';
        return json_encode($response);
    }

    $pendingOrdersQuery = "
        SELECT 
            Order_Num,
            SUM(Quantity) AS Number_of_Items,
            ROUND(SUM(Total_Price), 2) AS Total_Amount,
            Payment_Type, 
            Billing_Address, 
            Order_Status 
        FROM `Order`
        WHERE Order_Status IN ('Order Placed', 'Order Shipped')
        GROUP BY Order_Num, Payment_Type, Billing_Address, Order_Status
        ORDER BY Order_Num ASC;
    ";

    $historyOrdersQuery = "
        SELECT 
            Order_Num,
            SUM(Quantity) AS Number_of_Items,
            ROUND(SUM(Total_Price), 2) AS Total_Amount,
            Payment_Type, 
            Billing_Address, 
            Order_Status 
        FROM `Order`
        WHERE Order_Status = 'Order Received'
        GROUP BY Order_Num, Payment_Type, Billing_Address, Order_Status
        ORDER BY Order_Num ASC;
    ";

    $pendingOrders = runOrderQuery($conn, $pendingOrdersQuery);
    $historyOrders = runOrderQuery($conn, $historyOrdersQuery);

    $conn->close();

    return json_encode([
        'icon' => 'success',
        'pendingOrders' => $pendingOrders,
        'historyOrders' => $historyOrders
    ]);
}

function getOrdersByUserID()
{
    $conn = getDatabaseConnection();
    global $response;
    if (!$conn) {
        $response["message"] = 'Database connection failed';
        return json_encode($response);
    }

    if (!isset($_SESSION['userid'])) {
        $response["message"] = 'User not logged in';
        return json_encode($response);
    }

    $userID = $_SESSION['userid'];
    error_log("User ID: " . $userID);

    $pendingOrdersQuery = "
        SELECT 
            Order_Num,
            SUM(Quantity) AS Number_of_Items,
            ROUND(SUM(Total_Price), 2) AS Total_Amount,
            Payment_Type, 
            Billing_Address, 
            Order_Status 
        FROM `Order`
        WHERE User_ID = ? AND Order_Status IN ('Order Placed', 'Order Shipped')
        GROUP BY Order_Num, Payment_Type, Billing_Address, Order_Status
        ORDER BY Order_Num ASC;
    ";

    $historyOrdersQuery = "
        SELECT 
            Order_Num,
            SUM(Quantity) AS Number_of_Items,
            ROUND(SUM(Total_Price), 2) AS Total_Amount,
            Payment_Type, 
            Billing_Address, 
            Order_Status 
        FROM `Order`
        WHERE User_ID = ? AND Order_Status = 'Order Received'
        GROUP BY Order_Num, Payment_Type, Billing_Address, Order_Status
        ORDER BY Order_Num ASC;
    ";

    $pendingOrders = runOrderQuerywUserID($conn, $pendingOrdersQuery, $userID);
    $historyOrders = runOrderQuerywUserID($conn, $historyOrdersQuery, $userID);

    $conn->close();

    return json_encode([
        'icon' => 'success',
        'pendingOrders' => $pendingOrders,
        'historyOrders' => $historyOrders
    ]);
}

function runOrderQuerywUserID($conn, $query, $userID)
{
    $stmt = $conn->prepare($query);
    if (!$stmt) {
        return 'Prepare failed: ' . $conn->error;
    }

    $stmt->bind_param("i", $userID);
    if (!$stmt->execute()) {
        $response["message"] = 'Execute failed: ' . $stmt->error;
        return json_encode($response);
    }
    
    $result = $stmt->get_result();
    $arrResult = [];

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $arrResult[] = $row;
        }
    }

    $stmt->close();
    return $arrResult;
}

function runOrderQuery($conn, $query)
{
    $stmt = $conn->prepare($query);
    if (!$stmt) {
        return 'Prepare failed: ' . $conn->error;
    }

    if (!$stmt->execute()) {
        $response["message"] = 'Execute failed: ' . $stmt->error;
        return json_encode($response);
    }

    $result = $stmt->get_result();
    $arrResult = [];

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $arrResult[] = $row;
        }
    }

    $stmt->close();
    return $arrResult;
}

// Mark order as received
function markAsReceived($Order_Num)
{
    $conn = getDatabaseConnection();
    global $response;
    if (!$conn) {
        $response["message"] = 'Database connection failed';
        return json_encode($response);
    }

    if (empty($Order_Num)) {
        $response["message"] = 'Empty Order Number.';
        return json_encode($response);
    }

    $stmt = $conn->prepare("UPDATE `Order` SET Order_Status = 'Order Received' WHERE Order_Num = ?");
    if (!$stmt) {
        $response["message"] = 'Prepare failed: ' . $conn->error;
        return json_encode($response);
    }

    $stmt->bind_param("s", $Order_Num);
    if (!$stmt->execute()) {
        $response["message"] = 'Execute failed: ' . $stmt->error;
        return json_encode($response);
    }

    $stmt->close();
    $conn->close();
    $response["icon"] = "success";
    $response["title"] = "Order Status Updated";
    $response["message"] = "The order status has been updated to Order Received.";
    return json_encode($response);
}

// Mark order as shipped
function markAsShipped($Order_Num)
{
    $conn = getDatabaseConnection();
    global $response;
    if (!$conn) {
        $response["message"] = 'Database connection failed';
        return json_encode($response);
    }

    if (empty($Order_Num)) {
        $response["message"] = 'Empty Order Number.';
        return json_encode($response);
    }

    $stmt = $conn->prepare("UPDATE `Order` SET Order_Status = 'Order Shipped' WHERE Order_Num = ?");
    if (!$stmt) {
        $response["message"] = 'Prepare failed: ' . $conn->error;
        return json_encode($response);
    }

    $stmt->bind_param("s", $Order_Num);
    if (!$stmt->execute()) {
        $response["message"] = 'Execute failed: ' . $stmt->error;
        return json_encode($response);
    }

    $stmt->close();
    $conn->close();
    $response["icon"] = "success";
    $response["title"] = "Order Status Updated";
    $response["message"] = "The order status has been updated to Order Shipped.";
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
    if ($action === 'getOrdersByUserID') {
        echo getOrdersByUserID();
    } elseif($action === 'getAllOrders'){
        echo getAllOrders();
    }elseif ($action === 'viewOrderDetails' && isset($_GET['Order_Num'])) {
        $orderNum = intval($_GET['Order_Num']);
        $orderDetails = viewOrderDetails($orderNum);
        echo json_encode([
            'icon' => 'success',
            'orderDetails' => $orderDetails
        ]);
    }
} elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if ($action === 'markAsReceived' && isset($_POST['Order_Num'])) {
        echo markAsReceived($_POST['Order_Num']);
    } elseif ($action === 'editOrder') {
        echo editOrder($_POST);
    }
}
?>