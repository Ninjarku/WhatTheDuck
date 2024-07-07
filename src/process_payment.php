<?php
session_start();
header('Content-Type: application/json');

$User_ID = $_SESSION['userid'];

$response = array(
    "icon" => "error",
    "title" => "Signup failed!",
    "message" => "",
    "redirect" => null
);

function sanitize_input($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

function checkInputSuccess($success) {
    if (!$success) {
        $response["message"] = "Please fill in the fields properly.";
        echo json_encode($response);
        exit();
    }
}

function checkProcessSuccess($success) {
    if (!$success) {
        $response["message"] = "Something went wrong, please try again later.";
        echo json_encode($response);
        exit();
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST") { 
    $success = true;
    #Ensure input is not empty
    if (empty($_POST["fullName"]) || empty($_POST["phoneNumber"]) || empty($_POST["address"]) || empty($_POST["postalCode"]) || empty($_POST["paymentMethod"])) {
        #Input empty
        $success = false;
    }
    else {
        $paymentMethod = $_POST["paymentMethod"];
        $Payment_Type;
        if ($paymentMethod == "card") {
            # If card, make sure relevant fields are not empty
            if (empty($_POST["cardName"]) || empty($_POST["cardNumber"]) || empty($_POST["expiryDate"]) || empty($_POST["cvv"])) {
                #Input empty
                $success = false;
            }
        }
        if ($paymentMethod == "card") {
            $Payment_Type = "Card";
        }

        if ($paymentMethod == "cod") {
            $Payment_Type = "Cash";
        }
    }

    if ($success = true) {
        $fullName = sanitize_input($_POST["fullName"]);
        $phoneNumber = sanitize_input($_POST["phoneNumber"]);
        $Billing_Address = sanitize_input($_POST["address"]);
        $postalCode = sanitize_input($_POST["postalCode"]);
        if (!empty($_POST["unitNo"])) $unitNo = sanitize_input($_POST["unitNo"]);

        if (!preg_match("/^[0-9]{8}$/", $phoneNumber)) {
            $response["message"] .= "<br/>Invalid mobile number format. Please enter exactly 8 digits.";
            $success = false;
        }

        if (!preg_match("/^[0-9]{6}$/", $postalCode)) {
            $success = false;
        }

        if ($paymentMethod == "card") {
            $cardName = sanitize_input($_POST["cardName"]);
            $cardNumber = sanitize_input($_POST["cardNumber"]);
            $expiryDate = sanitize_input($_POST["expiryDate"]);
            $cvv = sanitize_input($_POST["cvv"]);

            if (!preg_match("/^[0-9]{16}$/", $cardNumber)) {
                $success = false;
            }

            if (!preg_match("/^(0[1-9]|1[0-2])\/\d{2}$/", $expiryDate)) {
                $success = false;
            } else {
                list($expMonth, $expYear) = explode('/', $expiryDate);
                $expYear = '20' . $expYear; 
                $currentMonth = date('m');
                $currentYear = date('Y');
                
                if ($expYear < $currentYear || ($expYear == $currentYear && $expMonth < $currentMonth)) {
                    $success = false;
                }
            }

            if (!preg_match("/^[0-9]{3}$/", $cvv)) {
                $success = false;
            }

        }
    }

    checkInputSuccess($success);

    if ($success) {
        if (isset($_SESSION['selectedCartIds'])){
            $cartids = $_SESSION['selectedCartIds'];
            unset($_SESSION['selectedCartIds']);
        }
        else {
            $success = false;
        }

        $config = parse_ini_file('/var/www/private/db-config.ini');
        $conn = new mysqli($config['host'], $config['username'], $config['password'], $config['dbname']);

        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }

        # Get highest order number
        $stmt = $conn->prepare("SELECT MAX(Order_Num) AS Order_Num FROM Order");
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $Order_Num = $row['Order_Num'];
            $Order_Num += 1;
        } else {
            $success = false;
        }
        $stmt->close();
        # Create order for each cart id
        foreach ($cartids as $cartid) {
            # Read cart object
            $stmt = $conn->prepare("SELECT Cart.Cart_ID, Cart.Product_ID, Cart.Quantity, Product.Price
            FROM Cart
            JOIN Product
            ON Cart.Product_ID = Product.Product_ID
            WHERE Cart.Cart_ID = ?");

            $stmt->bind_param('i', $cartid);
            $stmt->execute();
            $result = $stmt->get_result();
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    $Cart_ID = $row['Cart_ID'];
                    $Product_ID = $row['Product_ID'];
                    $Quantity = $row['Quantity'];
                    $Price = $row['Price'];
                    $Total_Price = $Quantity * $Price;
                }
            }
            else {
                $success = false;
            }
            $stmt->close();
            # Create order
            $Order_Status = "Order Placed";
            $stmt = $conn->prepare("INSERT INTO Order (Order_Num, User_ID, Product_ID, Quantity, Total_Price, Payment_Type, Billing_Address, Order_Status) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->bind_param('iiiidsss', $Order_Num, $User_ID, $Product_ID, $Quantity, $Total_Price, $Payment_Type, $Billing_Address, $Order_Status);
            if (!$stmt->execute())
            {
                $success = false;
            }
            $stmt->close();


            # Delete cart object
            $stmt = $conn->prepare("DELETE FROM Cart WHERE Cart_ID = ?");
            $stmt->bind_param('i', $Cart_ID);
            $stmt->execute();

            if ($stmt->affected_rows < 1) {
                echo "Error deleting record";
                $success = false;
            }

            $stmt->close();
        }
    }

    checkProcessSuccess($success);

    if ($success) {
        # Create sesson object, store order number 
        $_SESSION['Order_Num'] = $Order_Num;

        # Redirect to zhenyu
        $response["redirect"] = "/zhenyufunction.php";
    }
    
    echo json_encode($response);
}
?>