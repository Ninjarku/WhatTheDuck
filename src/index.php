<?php include 'includes/navbar.php';?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>WhatTheDuck - Home</title>
        <link rel="stylesheet"
              href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css"
              integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh"
              crossorigin="anonymous">
        <style>
            body, html {
                height: 100%;
                margin: 0;
                font-family: Arial, Helvetica, sans-serif;
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
                height: 500px; /* Adjusted height to accommodate price */
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
            .btn-buy-now {
                margin-top: auto;
            }
        </style>
    </head>
    <body>
        <?php
//// Enable error reporting for debugging
//        error_reporting(E_ALL);
//        ini_set('display_errors', 1);
//
//// Parse the db-config.ini file
//        $config = parse_ini_file('/var/www/private/db-config.ini');
//
//// Check if the configuration was successfully parsed
//        if (!$config) {
//            die("Error: Unable to load database configuration.");
//        }
//
//// Extract configuration details
//        $servername = $config['servername'];
//        $username = $config['username'];
//        $password = $config['password'];
//        $dbname = $config['dbname'];
//
//// Create the database connection with increased timeout
//        $conn = new mysqli($servername, $username, $password, $dbname);
//
//// Check the connection
//        if ($conn->connect_error) {
//            die("Connection failed: " . $conn->connect_error);
//        } else {
//            echo "Connected successfully to the database.";
//        }
//
//// Close the connection
//        $conn->close();
        ?>
        <div class="container">
            <h1 class="text-center">Our Products</h1>
            <div class="row justify-content-center">
                <div class="col-md-4 d-flex justify-content-center">
                    <div class="product-card">
                        <h2>Duck Plush Toy</h2>
                        <img src="images/duck_plush.jpg" alt="Duck Plush Toy">
                        <p>Soft and cuddly duck plush toy.</p>
                        <p class="price">$19.99</p>
                        <form action="PaymentPage.php" method="post">
                            <input type="hidden" name="product_name" value="Duck Plush Toy">
                            <input type="hidden" name="product_price" value="19.99">
                            <button type="submit" class="btn btn-primary btn-buy-now">Buy Now</button>
                        </form>
                    </div>
                </div>
                <div class="col-md-4 d-flex justify-content-center">
                    <div class="product-card">
                        <h2>Duck Mug</h2>
                        <img src="images/duck_mug.jpg" alt="Duck Mug">
                        <p>Enjoy your drinks with this cute duck mug.</p>
                        <p class="price">$9.99</p>
                        <form action="PaymentPage.php" method="post">
                            <input type="hidden" name="product_name" value="Duck Mug">
                            <input type="hidden" name="product_price" value="9.99">
                            <button type="submit" class="btn btn-primary btn-buy-now">Buy Now</button>
                        </form>
                    </div>
                </div>
                <div class="col-md-4 d-flex justify-content-center">
                    <div class="product-card">
                        <h2>Duck T-shirt</h2>
                        <img src="images/duck_tshirt.jpg" alt="Duck T-shirt">
                        <p>Stylish T-shirt with a duck print.</p>
                        <p class="price">$14.99</p>
                        <form action="PaymentPage.php" method="post">
                            <input type="hidden" name="product_name" value="Duck T-shirt">
                            <input type="hidden" name="product_price" value="14.99">
                            <button type="submit" class="btn btn-primary btn-buy-now">Buy Now</button>
                        </form>
                    </div>
                </div>
                <div class="col-md-4 d-flex justify-content-center">
                    <div class="product-card">
                        <h2>Duck Keychain</h2>
                        <img src="images/duck_keychain.jpg" alt="Duck Keychain">
                        <p>Carry your keys with this adorable duck keychain.</p>
                        <p class="price">$4.99</p>
                        <form action="PaymentPage.php" method="post">
                            <input type="hidden" name="product_name" value="Duck Keychain">
                            <input type="hidden" name="product_price" value="4.99">
                            <button type="submit" class="btn btn-primary btn-buy-now">Buy Now</button>
                        </form>
                    </div>
                </div>
                <div class="col-md-4 d-flex justify-content-center">
                    <div class="product-card">
                        <h2>Duck Pillow</h2>
                        <img src="images/duck_pillow.jpg" alt="Duck Pillow">
                        <p>Comfortable pillow with a cute duck design.</p>
                        <p class="price">$24.99</p>
                        <form action="PaymentPage.php" method="post">
                            <input type="hidden" name="product_name" value="Duck Pillow">
                            <input type="hidden" name="product_price" value="24.99">
                            <button type="submit" class="btn btn-primary btn-buy-now">Buy Now</button>
                        </form>
                    </div>
                </div>
                <div class="col-md-4 d-flex justify-content-center">
                    <div class="product-card">
                        <h2>Duck Hat</h2>
                        <img src="images/duck_hat.jpg" alt="Duck Hat">
                        <p>Fashionable hat with a duck logo.</p>
                        <p class="price">$12.99</p>
                        <form action="PaymentPage.php" method="post">
                            <input type="hidden" name="product_name" value="Duck Hat">
                            <input type="hidden" name="product_price" value="12.99">
                            <button type="submit" class="btn btn-primary btn-buy-now">Buy Now</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <?php include 'includes/footer.php'; ?>

        <script defer
                src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.bundle.min.js"
                integrity="sha384-6khuMg9gaYr5AxOqhkVIODVIvm9ynTT5J4V1cfthmT+emCG6yVmEZsRHdxlotUnm"
        crossorigin="anonymous"></script>
    </body>
</html>
