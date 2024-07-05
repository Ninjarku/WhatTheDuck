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

// Fetch product details
$Product_ID = isset($_GET['Product_ID']) ? intval($_GET['Product_ID']) : 0;
$stmt = $conn->prepare("SELECT Product_ID, Product_Name, Product_Description, Product_Image, Price, Quantity, Product_Category FROM Product WHERE Product_ID = ?");
if (!$stmt) {
    die("Prepare failed: " . $conn->error);
}

$stmt->bind_param("i", $Product_ID);
$stmt->execute();
$result = $stmt->get_result();
$product = null;

if ($result->num_rows > 0) {
    $product = $result->fetch_assoc();
}

$stmt->close();
$conn->close();

if (!$product) {
    die("Product not found.");
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($product['Product_Name']); ?> - WhatTheDuck</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css"
        integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous">
    <style>
        body,
        html {
            height: 100%;
            margin: 0;
            font-family: Arial, Helvetica, sans-serif;
            background-color: #fff5cc;
        }

        .container {
            margin-top: 20px;
            margin-bottom: 20px;
        }

        .product-details-card {
            border: 1px solid #ddd;
            border-radius: 10px;
            padding: 20px;
            margin: 10px auto;
            background-color: #fff;
            max-width: 800px;
        }

        .product-details-card img {
            max-width: 100%;
            height: auto;
            object-fit: cover;
        }

        .product-details-card h2 {
            font-size: 2em;
            color: #ff6347;
        }

        .product-details-card p {
            font-size: 1.2em;
        }

        .product-details-card .price {
            font-size: 1.5em;
            color: #28a745;
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
        <div class="product-details-card">
            <h2><?php echo htmlspecialchars($product['Product_Name']); ?></h2>
            <?php if (!empty($product['Product_Image'])): ?>
                <img src="data:image/jpeg;base64,<?php echo base64_encode($product['Product_Image']); ?>"
                    alt="<?php echo htmlspecialchars($product['Product_Name']); ?>">
            <?php else: ?>
                <img src="images/default_product.jpg" alt="Default Product Image">
            <?php endif; ?>
            <p><?php echo htmlspecialchars($product['Product_Description']); ?></p>
            <p class="price">$<?php echo htmlspecialchars($product['Price']); ?></p>
            <form action="" method="post"> <!-- Add your add to cart process -->
                <input type="hidden" name="Product_ID" value="<?php echo htmlspecialchars($product['Product_ID']); ?>">
                <input type="hidden" name="product_name"
                    value="<?php echo htmlspecialchars($product['Product_Name']); ?>">
                <input type="hidden" name="product_price" value="<?php echo htmlspecialchars($product['Price']); ?>">
                <button type="submit" class="btn btn-primary">Add to Cart</button>
            </form>
        </div>
    </div>

    <?php include 'includes/footer.php'; ?>

    <script defer src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.bundle.min.js"
        integrity="sha384-6khuMg9gaYr5AxOqhkVIODVIvm9ynTT5J4V1cfthmT+emCG6yVmEZsRHdxlotUnm"
        crossorigin="anonymous"></script>
</body>

</html>