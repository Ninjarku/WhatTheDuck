<?php

session_start(); 

// Database connection
function getDatabaseConnection(){
    $config = parse_ini_file('/var/www/private/db-config.ini');
    $conn = new mysqli($config['host'], $config['username'], $config['password'], $config['dbname']);
    if ($conn->connect_error) {
        return null;
    }
    return $conn;
}

if (isset($_GET['action']) && $_GET['action'] == 'deleteCartItem') { 
    $conn = getDatabaseConnection();

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

function getCartCount(){
    $User_ID = $_SESSION['userid'];
    // Create database connection.
    $conn = getDatabaseConnection();
    // Check connection
    if ($conn->connect_error) {
        $errorMsg = "Connection failed: " . $conn->connect_error;
        echo $errorMsg;
        $success = false;
    } else {
        // Prepare the statement:
        $stmt = $conn->prepare("SELECT COUNT(c.Cart_ID) AS cartcount FROM Cart c, User u WHERE c.User_ID = u.User_ID AND u.User_ID = ?;");
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
    $conn = getDatabaseConnection();
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
    $conn = getDatabaseConnection();

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

if (isset($_GET['action']) && $_GET['action'] == 'update_quantity') {  
    $User_ID = $_SESSION['userid'];
    $cart_id = $_GET['cart_id'];
    $quantityaction = $_GET['quantityaction']; 

    $conn = getDatabaseConnection(); 
    if ($conn->connect_error) {
        echo json_encode(['success' => false, 'message' => 'Database connection failed']);
        exit();
    }

    $stmt = $conn->prepare("SELECT Quantity, Price FROM Cart WHERE Cart_ID = ? AND User_ID = ?");
    $stmt->bind_param("ii", $cart_id, $User_ID);
    
    $stmt->execute();
    $stmt->bind_result($quantity, $price);
    $stmt->fetch();
    $stmt->close();

    if ($quantity !== null) {
        if ($quantityaction == 'increase') {
            $newQuantity = $quantity + 1;
        } elseif ($quantityaction == 'decrease' && $quantity > 1) {
            $newQuantity = $quantity - 1;
        } else {
            echo json_encode(['success' => false, 'message' => 'Invalid action']);
            exit();
        }

        $newTotalPrice = $newQuantity * $price;

        $stmt2 = $conn->prepare("UPDATE Cart SET Quantity = ?, Total_Price = ? WHERE Cart_ID = ? AND User_ID = ?");
        $stmt2->bind_param("idii", $newQuantity, $newTotalPrice, $cart_id, $User_ID);
        $stmt2->execute();  
        $stmt2->close();
        
        // Calculate the new subtotal for the entire cart
        $stmt3 = $conn->prepare("SELECT SUM(Total_Price) FROM Cart WHERE User_ID = ?");
        $stmt3->bind_param("i", $User_ID);
        $stmt3->execute();
        $stmt3->bind_result($newSubtotal);
        $stmt3->fetch();
        $stmt3->close();

        echo json_encode(['success' => true, 'new_quantity' => $newQuantity, 'new_total_price' => $newTotalPrice, 'new_subtotal' => $newSubtotal]);
        
    } 
    else {
        echo json_encode(['success' => false, 'message' => 'Item not found']);
    }

    $conn->close();
}

//add item to cart
if (isset($_GET['action']) && $_GET['action'] == 'additem') {  
    $User_ID = $_SESSION['userid'];
    if ($User_ID !== null){
        $product_id = $_GET['productid'];

        //insert to cart table
        $conn = getDatabaseConnection(); 
        if ($conn->connect_error) {
            echo json_encode(['success' => false, 'message' => 'Database connection failed']);
            exit();
        }

        //get from product table the price
        $stmt3 = $conn->prepare("SELECT Price FROM Product WHERE Product_ID = ?;");
        $stmt3->bind_param("i", $product_id);
        $stmt3->execute();
        $stmt3->bind_result($product_price);
        $stmt3->fetch();
        $stmt3->close();
        
        $qty = '1';
        $stmt = $conn->prepare("INSERT INTO Cart(User_ID, Product_ID, Quantity, Price, Total_Price) values (?,?,?,?,?);");
        $stmt->bind_param("iiidd", $User_ID, $product_id,$qty,$product_price,$product_price);
        $stmt->execute();
        $stmt->close();

        if ($product_price !== null) {
            echo "<script>window.location.href = 'cart.php';</script>"; 
        }
    }else{
        echo "<script>window.location.href = 'Login.php';</script>"; 
    }
    
}
?>
