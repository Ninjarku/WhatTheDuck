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
$productID = isset($_GET['product_id']) ? intval($_GET['product_id']) : 0;
$stmt = $conn->prepare("SELECT * FROM Product WHERE Product_ID = ?");
if (!$stmt) {
    die("Prepare failed: " . $conn->error);
}
$stmt->bind_param("i", $productID);
$stmt->execute();
$result = $stmt->get_result();
$product = $result->fetch_assoc();

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
    <title><?php echo htmlspecialchars($product['Product_Name']); ?> - Product Details</title>
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
            display: flex;
            flex-direction: row;
            justify-content: space-between;
        }

        .product-image {
            max-width: 50%;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .product-image img {
            max-width: 100%;
            border-radius: 10px;
        }

        .product-details {
            max-width: 45%;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .product-details h1 {
            font-size: 2.5em;
            color: #ff6347;
        }

        .product-details p {
            font-size: 1.2em;
        }

        .product-details .price {
            font-size: 1.8em;
            color: #28a745;
            margin: 20px 0;
        }

        .product-details .btn-buy-now {
            background-color: #ffcc00;
            border: none;
            color: black;
            padding: 10px 20px;
            font-size: 1.2em;
            cursor: pointer;
        }

        .product-details .btn-buy-now:hover {
            background-color: #ff6347;
            color: white;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="product-image">
            <?php if (!empty($product['Product_Image'])): ?>
                <img src="data:image/jpeg;base64,<?php echo base64_encode($product['Product_Image']); ?>"
                    alt="<?php echo htmlspecialchars($product['Product_Name']); ?>">
            <?php else: ?>
                <img src="images/default_product.jpg" alt="Default Product Image">
            <?php endif; ?>
        </div>
        <div class="product-details">
            <h1><?php echo htmlspecialchars($product['Product_Name']); ?></h1>
            <p><?php echo nl2br(htmlspecialchars($product['Product_Description'])); ?></p>
            <p class="price">$<?php echo htmlspecialchars($product['Price']); ?></p>
            <form action="cart.php" method="post">
                <input type="hidden" name="product_id" value="<?php echo htmlspecialchars($product['Product_ID']); ?>">
                <button type="submit" class="btn btn-primary btn-buy-now">Add to Cart</button>
            </form>
        </div>
    </div>

    <?php include 'includes/footer.php'; ?>

    <script defer src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.bundle.min.js"
        integrity="sha384-6khuMg9gaYr5AxOqhkVIODVIvm9ynTT5J4V1cfthmT+emCG6yVmEZsRHdxlotUnm"
        crossorigin="anonymous"></script>
</body>

</html>