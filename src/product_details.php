<?php
session_start();
include 'includes/navbar.php';

if (!isset($_GET['Product_ID'])) {
    // If no product ID is provided, redirect to the homepage or an error page
    header('Location: index.php');
    exit();
}

$Product_ID = intval($_GET['Product_ID']);

// Function to fetch product details
function getProductDetails($Product_ID)
{
    $config = parse_ini_file('/var/www/private/db-config.ini');
    $conn = new mysqli($config['host'], $config['username'], $config['password'], $config['dbname']);

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    $stmt = $conn->prepare("SELECT * FROM Product WHERE Product_ID = ?");
    $stmt->bind_param("i", $Product_ID);
    $stmt->execute();
    $result = $stmt->get_result();
    $product = $result->fetch_assoc();

    $stmt->close();
    $conn->close();

    return $product;
}

$product = getProductDetails($Product_ID);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title><?php echo htmlspecialchars($product['Product_Name']); ?> - WhatTheDuck</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css"
        integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous">
    <style>
        body,
        html {
            height: 100%;
            margin: 0;
            background-color: #fff5cc;
        }

        .container {
            margin-top: 20px;
        }

        .product-details-card {
            border: 1px solid #ddd;
            border-radius: 10px;
            text-align: center;
            padding: 20px;
            margin: 10px auto;
            background-color: #fff;
            max-width: 600px;
        }

        .product-image {
            max-width: 100%;
            height: auto;
            object-fit: cover;
        }

        .navbar,
        .footer {
            margin-bottom: 20px;
        }
    </style>
</head>

<body>
    <div class="container">
        <h1 class="text-center"><?php echo htmlspecialchars($product['Product_Name']); ?></h1>
        <div class="product-details-card">
            <?php if (!empty($product['Product_Image'])): ?>
                <img src="data:image/jpeg;base64,<?php echo base64_encode($product['Product_Image']); ?>"
                    alt="<?php echo htmlspecialchars($product['Product_Name']); ?>" class="product-image">
            <?php endif; ?>
            <p><?php echo htmlspecialchars($product['Product_Description']); ?></p>
            <p><strong>Price:</strong> $<?php echo htmlspecialchars($product['Price']); ?></p>
            <p><strong>Category:</strong> <?php echo htmlspecialchars($product['Product_Category']); ?></p>
            <form action="PaymentPage.php" method="post">
                <input type="hidden" name="Product_ID" value="<?php echo $product['Product_ID']; ?>">
                <input type="hidden" name="Product_Name"
                    value="<?php echo htmlspecialchars($product['Product_Name']); ?>">
                <input type="hidden" name="Price" value="<?php echo htmlspecialchars($product['Price']); ?>">
                <button type="submit" class="btn btn-primary">Buy Now</button>
            </form>
        </div>
    </div>

    <?php include 'includes/footer.php'; ?>
</body>

</html>