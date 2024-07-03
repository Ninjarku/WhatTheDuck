<?php
session_start();
include 'includes/navbar.php';
include 'process_product.php';

// Fetch products data
$productsJson = getAllProducts();
$products = json_decode($productsJson, true);

// // Handle JSON decoding errors
// if (json_last_error() !== JSON_ERROR_NONE) {
//     $products = [];
//     error_log("JSON decoding error: " . json_last_error_msg(), 3, "/var/www/logs/error.log");
// }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Duck Shop - Our Products</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css" integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous">
    <style>
        body, html {
            height: 100%;
            margin: 0;
            font-family: Arial, Helvetica, sans-serif;
            background-color: #fff5cc;
        }
        .container {
            margin-top: 20px;
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
            height: 300px;
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
        .btn-buy-now {
            margin-top: auto;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1 class="text-center">Our Products</h1>
        <div class="row justify-content-center">
            <?php if (!empty($products) && isset($products['data']) && is_array($products['data'])): ?>
                <?php foreach ($products['data'] as $product): ?>
                    <div class="col-md-4 d-flex justify-content-center">
                        <div class="product-card">
                            <h2><?php echo htmlspecialchars($product['Product_Name']); ?></h2>
                            <img src="data:image/jpeg;base64,<?php echo base64_encode($product['Product_Image']); ?>" alt="<?php echo htmlspecialchars($product['Product_Name']); ?>">
                            <p><?php echo htmlspecialchars($product['Product_Description']); ?></p>
                            <p class="price">$<?php echo htmlspecialchars($product['Price']); ?></p>
                            <button class="btn btn-primary btn-buy-now">Buy Now</button>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p class="text-center">No products available.</p>
            <?php endif; ?>
        </div>
    </div>

    <?php include 'includes/footer.php'; ?>

    <script defer src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.bundle.min.js" integrity="sha384-6khuMg9gaYr5AxOqhkVIODVIvm9ynTT5J4V1cfthmT+emCG6yVmEZsRHdxlotUnm" crossorigin="anonymous"></script>
</body>
</html>
