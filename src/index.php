<?php
session_start();
require_once 'jwt/jwt_cookie.php';
$decodedToken = checkGuestAccess();
include 'includes/navbar.php';

// Database connection
$config = parse_ini_file('/var/www/private/db-config.ini');
$conn = new mysqli($config['host'], $config['username'], $config['password'], $config['dbname']);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch products
$stmt = $conn->prepare("SELECT Product_ID, Product_Name, Product_Description, Product_Image, Price FROM Product WHERE Product_Available = 1 ORDER BY Product_ID ASC");
if (!$stmt) {
    die("Prepare failed: " . $conn->error);
}

$stmt->execute();
$result = $stmt->get_result();
$products = [];

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $products[] = $row;
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
    <title>WhatTheDuck - Home</title>
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

        .product-card {
            border: 1px solid #ddd;
            border-radius: 10px;
            text-align: center;
            padding: 20px;
            margin: 10px;
            background-color: #fff;
            width: 100%;
            max-width: 300px;
            height: 450px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        .product-card h2 {
            font-size: 1.5em;
            color: #ff6347;
        }

        .product-card img {
            max-width: 100%;
            height: 250px;
            object-fit: cover;
        }

        .product-card p {
            flex-grow: 1;
        }

        .product-card .price {
            font-size: 1.2em;
            color: #28a745;
            margin: 10px 0;
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

        .btn-add-to-cart {
            margin-top: auto;
        }

        .product-link {
            text-decoration: none;
            color: inherit;
        }

        .product-link:hover {
            text-decoration: none;
            color: inherit;
        }

        .btn-disabled {
            background-color: #ccc;
            border: none;
            color: white;
            cursor: not-allowed;
        }
    </style>
</head>

<body>
    <div class="container">
        <h1 class="text-center">Our Products</h1>
        <div class="row justify-content-center">
            <?php if (!empty($products)): ?>
                <?php foreach ($products as $product): ?>
                    <div class="col-md-4 d-flex justify-content-center">
                        <div class="product-card">
                            <a href="product_details.php?Product_ID=<?php echo $product['Product_ID']; ?>" class="product-link">
                                <h2><?php echo htmlspecialchars($product['Product_Name']); ?></h2>
                                <?php if (!empty($product['Product_Image'])): ?>
                                    <img src="data:image/jpeg;base64,<?php echo base64_encode($product['Product_Image']); ?>"
                                        alt="<?php echo htmlspecialchars($product['Product_Name']); ?>">
                                <?php else: ?>
                                    <img src="images/default_product.jpg" alt="Default Product Image">
                                <?php endif; ?>
                            </a>
                            <p class="price">$<?php echo htmlspecialchars($product['Price']); ?></p>
                            <!-- Add to cart button -->
                            <?php if (isset($decodedToken) && ($decodedToken['rol'] === 'Sales Admin' || $decodedToken['rol'] === 'IT Admin')): ?>
                                <button class="btn btn-disabled">Add To Cart</button>
                            <?php else: ?>
                                <form action="process_cart.php?action=additem&productid=<?php echo $product['Product_ID']; ?>"
                                    method="post">
                                    <input type="hidden" name="product_name"
                                        value="<?php echo htmlspecialchars($product['Product_Name']); ?>">
                                    <input type="hidden" name="product_price"
                                        value="<?php echo htmlspecialchars($product['Price']); ?>">
                                    <button type="submit" class="btn btn-primary btn-buy-now">Add To Cart</button>
                                </form>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p class="text-center">No products available.</p>
            <?php endif; ?>
        </div>
    </div>

    <?php include 'includes/footer.php'; ?>

    <script defer src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.bundle.min.js"
        integrity="sha384-6khuMg9gaYr5AxOqhkVIODVIvm9ynTT5J4V1cfthmT+emCG6yVmEZsRHdxlotUnm"
        crossorigin="anonymous"></script>
</body>

</html>