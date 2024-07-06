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

// Fetch recommended products
$conn = new mysqli($config['host'], $config['username'], $config['password'], $config['dbname']);
$stmt = $conn->prepare("SELECT Product_ID, Product_Name, Product_Image, Price FROM Product WHERE Product_ID != ? ORDER BY RAND() LIMIT 3");
$stmt->bind_param("i", $Product_ID);
$stmt->execute();
$result = $stmt->get_result();
$recommended_products = [];

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $recommended_products[] = $row;
    }
}

$stmt->close();
$conn->close();
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
            font-family: 'Comic Neue', cursive;
            background-color: #fff5cc;
        }

        .container {
            margin-top: 20px;
            margin-bottom: 20px;
        }

        .product-details-container {
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .product-details-image {
            flex: 1;
            text-align: center;
            padding: 20px;
        }

        .product-details-image img {
            max-width: 100%;
            height: auto;
            object-fit: cover;
        }

        .product-details-info {
            flex: 1;
            padding: 20px;
        }

        .product-details-info h2 {
            font-size: 2em;
            color: #ff6347;
        }

        .product-details-info p {
            font-size: 1.2em;
        }

        .product-details-info .price {
            font-size: 1.5em;
            color: #28a745;
        }

        .recommended-products {
            margin-top: 40px;
        }

        .recommended-products h3 {
            text-align: center;
            margin-bottom: 20px;
        }

        .recommended-product-card {
            background-color: #fff;
            text-align: center;
            padding: 20px;
            border-radius: 10px;
            margin: 5px;
            border: 1px solid #ddd;
        }

        .recommended-product-card img {
            max-width: 100%;
            height: 200px;
            object-fit: cover;
            margin-bottom: 10px;
        }

        .recommended-product-card h4 {
            color: #ff6347;
        }

        .recommended-product-card p {
            color: #28a745;
            margin-bottom: 10px;
        }

        .recommended-product-card {
            background-color: #ffcc00;
            border: none;
            color: #fff;
        }

        .recommended-product-card .btn:hover {
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
        <div class="product-details-container">
            <div class="product-details-image">
                <?php if (!empty($product['Product_Image'])): ?>
                    <img src="data:image/jpeg;base64,<?php echo base64_encode($product['Product_Image']); ?>"
                        alt="<?php echo htmlspecialchars($product['Product_Name']); ?>">
                <?php else: ?>
                    <img src="images/default_product.jpg" alt="Default Product Image">
                <?php endif; ?>
            </div>
            <div class="product-details-info">
                <h2><?php echo htmlspecialchars($product['Product_Name']); ?></h2>
                <p><?php echo htmlspecialchars($product['Product_Description']); ?></p>
                <p class="price">$<?php echo htmlspecialchars($product['Price']); ?></p>
                <form action="" method="post"> <!-- Add your add to cart process -->
                    <input type="hidden" name="Product_ID"
                        value="<?php echo htmlspecialchars($product['Product_ID']); ?>">
                    <input type="hidden" name="product_name"
                        value="<?php echo htmlspecialchars($product['Product_Name']); ?>">
                    <input type="hidden" name="product_price"
                        value="<?php echo htmlspecialchars($product['Price']); ?>">
                    <button type="submit" class="btn btn-primary">Add to Cart</button>
                </form>
            </div>
        </div>

        <div class="recommended-products">
            <h3>You May Also Like</h3>
            <div class="row justify-content-center">
                <?php foreach ($recommended_products as $rec_product): ?>
                    <div class="col-md-3 recommended-product-card">
                        <a href="product_details.php?Product_ID=<?php echo $rec_product['Product_ID']; ?>">
                            <?php if (!empty($rec_product['Product_Image'])): ?>
                                <img src="data:image/jpeg;base64,<?php echo base64_encode($rec_product['Product_Image']); ?>"
                                    alt="<?php echo htmlspecialchars($rec_product['Product_Name']); ?>">
                            <?php else: ?>
                                <img src="images/default_product.jpg" alt="Default Product Image">
                            <?php endif; ?>
                            <h4><?php echo htmlspecialchars($rec_product['Product_Name']); ?></h4>
                        </a>
                        <p>$<?php echo htmlspecialchars($rec_product['Price']); ?></p>
                        <a href="product_details.php?Product_ID=<?php echo $rec_product['Product_ID']; ?>"
                            class="btn btn-primary">View Product</a>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>

    <?php include 'includes/footer.php'; ?>

    <script defer src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.bundle.min.js"
        integrity="sha384-6khuMg9gaYr5AxOqhkVIODVIvm9ynTT5J4V1cfthmT+emCG6yVmEZsRHdxlotUnm"
        crossorigin="anonymous"></script>
</body>

</html>