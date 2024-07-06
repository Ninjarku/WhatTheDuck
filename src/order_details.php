<?php
session_start();
include 'includes/navbar.php';

// Database connection
$config = parse_ini_file('/var/www/private/db-config.ini');
$conn = new mysqli($config['host'], $config['username'], $config['password'], $config['dbname']);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch order details
$Order_ID = isset($_GET['Order_ID']) ? intval($_GET['Order_ID']) : 0;
$stmt = $conn->prepare("
    SELECT o.Order_ID, o.Order_Num, o.User_ID, o.Payment_Type, o.Billing_Address, o.Order_Status,
           op.Product_ID, p.Product_Name, p.Product_Description, p.Product_Image, op.Quantity, op.Total_Price
    FROM `Order` o
    JOIN Order_Product op ON o.Order_ID = op.Order_ID
    JOIN Product p ON op.Product_ID = p.Product_ID
    WHERE o.Order_ID = ?
");
if (!$stmt) {
    die("Prepare failed: " . $conn->error);
}

$stmt->bind_param("i", $Order_ID);
$stmt->execute();
$result = $stmt->get_result();
$order = [];
$products = [];

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $order = [
            'Order_ID' => $row['Order_ID'],
            'Order_Num' => $row['Order_Num'],
            'User_ID' => $row['User_ID'],
            'Payment_Type' => $row['Payment_Type'],
            'Billing_Address' => $row['Billing_Address'],
            'Order_Status' => $row['Order_Status']
        ];
        $products[] = [
            'Product_ID' => $row['Product_ID'],
            'Product_Name' => $row['Product_Name'],
            'Product_Description' => $row['Product_Description'],
            'Product_Image' => $row['Product_Image'],
            'Quantity' => $row['Quantity'],
            'Total_Price' => $row['Total_Price']
        ];
    }
}

$stmt->close();
$conn->close();

if (!$order) {
    die("Order not found.");
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order #<?php echo htmlspecialchars($order['Order_Num']); ?> - WhatTheDuck</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css"
        integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous">
    <style>
        body,
        html {
            height: 100%;
            margin: 0;
            font-family: 'Comic Neue', cursive;
            background-color: #fff5cc;
        }

        .container {
            margin-top: 20px;
            margin-bottom: 20px;
        }

        .order-details-container {
            display: flex;
            justify-content: center;
            align-items: center;
            flex-direction: column;
        }

        .order-details-info {
            padding: 20px;
            width: 100%;
        }

        .order-details-info h2 {
            font-size: 2em;
            color: #ff6347;
        }

        .order-details-info p {
            font-size: 1.2em;
        }

        .order-details-info .price {
            font-size: 1.5em;
            color: #28a745;
        }

        .product-details {
            margin-top: 40px;
        }

        .product-details h3 {
            text-align: center;
            margin-bottom: 20px;
        }

        .product-card {
            background-color: #fff;
            text-align: center;
            padding: 20px;
            border-radius: 10px;
            margin: 5px;
            border: 1px solid #ddd;
        }

        .product-card img {
            max-width: 100%;
            height: 200px;
            object-fit: cover;
            margin-bottom: 10px;
        }

        .product-card h4 {
            color: #ff6347;
        }

        .product-card p {
            color: #28a745;
            margin-bottom: 10px;
        }

        .product-card .btn {
            background-color: #ffcc00;
            border: none;
            color: #fff;
        }

        .product-card .btn:hover {
            background-color: #ff6347;
        }

        .navbar {
            margin-bottom: 20px;
        }

        .footer {
            margin-top: 20px;
            text-align: center;
            padding: 10px;
            background-color: #f1f1f1;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="order-details-container">
            <div class="order-details-info">
                <h2>Order #<?php echo htmlspecialchars($order['Order_Num']); ?></h2>
                <p>Order Status: <?php echo htmlspecialchars($order['Order_Status']); ?></p>
                <p>Payment Type: <?php echo htmlspecialchars($order['Payment_Type']); ?></p>
                <p>Billing Address: <?php echo htmlspecialchars($order['Billing_Address']); ?></p>
            </div>

            <div class="product-details">
                <h3>Products in this Order</h3>
                <div class="row justify-content-center">
                    <?php foreach ($products as $product): ?>
                        <div class="col-md-3 product-card">
                            <a href="product_details.php?Product_ID=<?php echo $product['Product_ID']; ?>">
                                <?php if (!empty($product['Product_Image'])): ?>
                                    <img src="data:image/jpeg;base64,<?php echo base64_encode($product['Product_Image']); ?>"
                                        alt="<?php echo htmlspecialchars($product['Product_Name']); ?>">
                                <?php else: ?>
                                    <img src="images/default_product.jpg" alt="Default Product Image">
                                <?php endif; ?>
                                <h4><?php echo htmlspecialchars($product['Product_Name']); ?></h4>
                            </a>
                            <p><?php echo htmlspecialchars($product['Product_Description']); ?></p>
                            <p>Quantity: <?php echo htmlspecialchars($product['Quantity']); ?></p>
                            <p>Total Price: $<?php echo htmlspecialchars($product['Total_Price']); ?></p>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>

    <?php include 'includes/footer.php'; ?>

    <script defer src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.bundle.min.js"
        integrity="sha384-6khuMg9gaYr5AxOqhkVIODVIvm9ynTT5J4V1cfthmT+emCG6yVmEZsRHdxlotUnm"
        crossorigin="anonymous"></script>
</body>

</html>
