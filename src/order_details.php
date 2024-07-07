<?php
session_start();
include "includes/navbar.php";
require_once '/var/www/html/jwt/jwt_cookie.php';

// Check if customer is logged in
if (!isset($_SESSION["cust_rol"]) || ($_SESSION["cust_rol"] !== "Customer" && $_SESSION["cust_rol"] !== "Sales Admin")) {
    ?>
    <script>
        window.location.href = 'error_page.php?error_id=0&error=' + encodeURIComponent('Please login!!');
    </script>
    <?php
    exit();
}


// Fetch order details from the database
$config = parse_ini_file('/var/www/private/db-config.ini');
$conn = new mysqli($config['host'], $config['username'], $config['password'], $config['dbname']);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$orderNum = isset($_GET['Order_Num']) ? intval($_GET['Order_Num']) : 0;
$stmt = $conn->prepare("
    SELECT o.Order_Num, o.Order_Status, o.Billing_Address, o.Payment_Type, p.Product_ID, p.Product_Name, p.Product_Image, o.Quantity, o.Total_Price, o.User_ID
    FROM `Order` o 
    JOIN `Product` p ON o.Product_ID = p.Product_ID 
    WHERE o.Order_Num = ?");
$stmt->bind_param("i", $orderNum);
$stmt->execute();
$result = $stmt->get_result();

$orderDetails = [];
while ($row = $result->fetch_assoc()) {
    $orderDetails[] = $row;
    $User_ID = $row['User_ID'];
}

authenticationCheckWithOrderValidation($User_ID);
$stmt->close();
$conn->close();

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Details</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css"
        integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous">
    <link rel="stylesheet" href="css/main.css">
    <style>
        body,
        html {
            font-family: 'Comic Neue', cursive;
            background-color: #fff5cc;
            color: black;
        }

        .container {
            margin-top: 50px;
            margin-bottom: 50px;
        }

        .order-details {
            background-color: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        .order-details h1 {
            text-align: center;
            margin-bottom: 20px;
        }

        .order-details .order-info {
            margin-bottom: 20px;
        }

        .order-details .items {
            margin-top: 20px;
            border-top: 1px solid #ddd;
            padding-top: 20px;
        }

        .order-details .item {
            display: flex;
            align-items: center;
            margin-bottom: 20px;
        }

        .order-details .item img {
            width: 100px;
            height: 100px;
            margin-right: 20px;
            object-fit: cover;
            border-radius: 5px;
        }

        .order-details .item .details {
            flex: 1;
        }

        .order-details .item .details p {
            margin: 0;
        }

        .order-details .item .details .price {
            font-weight: bold;
        }

        .btn-close {
            display: block;
            width: 100px;
            margin: 0 auto;
            text-align: center;
            background-color: #ffcc00;
            border: none;
            color: black;
            font-weight: bold;
            border-radius: 5px;
            padding: 10px;
            text-decoration: none;
            margin-top: 20px;
        }

        .btn-close:hover {
            background-color: #ff6347;
            color: white;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="order-details">
            <h1>Order Details</h1>
            <?php if (!empty($orderDetails)): ?>
                <div class="order-info">
                    <p>Order No: <?php echo htmlspecialchars($orderDetails[0]['Order_Num']); ?></p>
                    <p>Order Status: <?php echo htmlspecialchars($orderDetails[0]['Order_Status']); ?></p>
                    <p>Billing Address: <?php echo htmlspecialchars($orderDetails[0]['Billing_Address']); ?></p>
                    <p>Payment Type: <?php echo htmlspecialchars($orderDetails[0]['Payment_Type']); ?></p>
                </div>
                <div class="items">
                    <h3>Items</h3>
                    <?php foreach ($orderDetails as $item): ?>
                        <div class="item">
                            <img src="data:image/jpeg;base64,<?php echo base64_encode($item['Product_Image']); ?>"
                                alt="Item Image">
                            <div class="details">
                                <p>Product ID: <?php echo htmlspecialchars($item['Product_ID']); ?></p>
                                <p>Product Name: <?php echo htmlspecialchars($item['Product_Name']); ?></p>
                                <p>Quantity: <?php echo htmlspecialchars($item['Quantity']); ?></p>
                                <p class="price">Total Price:
                                    $<?php echo htmlspecialchars(number_format($item['Total_Price'], 2)); ?></p>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
                <?php
                $closeUrl = $_SESSION['role'] === 'Sales Admin' ? 'my_orders.php' : 'sales_order.php';
                ?>
                <a href="<?php echo $closeUrl; ?>" class="btn-close">Close</a>
            <?php else: ?>
                <p>Order not found.</p>
                <a href="'error_page.php?error_id=0&error=' + encodeURIComponent('Order Not Found!!');" class="btn-close">Close</a>
            <?php endif; ?>
        </div>
    </div>
</body>

</html>