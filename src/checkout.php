<?php 
    session_start(); 
    include_once "includes/navbar.php";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Check out</title>
    <link rel="stylesheet"
          href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css"
          integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh"
          crossorigin="anonymous"> 
    <!--jQuery-->
    <script src="js/jquery-3.5.1.js" type="text/javascript"></script>
    <!--Bootstrap JS-->
    <script defer
            src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.bundle.min.js"
            integrity="sha384-6khuMg9gaYr5AxOqhkVIODVIvm9ynTT5J4V1cfthmT+emCG6yVmEZsRHdxlotUnm"
            crossorigin="anonymous">
    </script>
    <script src="js/cart.js" defer></script>
    <link rel="stylesheet" href="css/cart.css">
</head>
<body>
    <?php   
    if (isset($_POST['selectedCartIds'])) {
        $cart_ids = $_POST['selectedCartIds'];
        
        $totalprice = 0;

        // Create database connection.
        $config = parse_ini_file('/var/www/private/db-config.ini');
        $conn = new mysqli($config['host'], $config['username'], $config['password'], $config['dbname']);

        // Check connection
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }
        $arrResult = [];
        // An array to hold the rows of data
        $rows = array();
        // Loop through each cart ID and retrieve the record from the database
        
        for ($x = 0; $x < sizeof($cart_ids); $x++) { 
            $stmt = $conn->prepare("SELECT c.Cart_ID, c.Quantity, c.Price, p.Product_Name, p.Product_Image 
                                FROM Cart c
                                JOIN Product p ON c.Product_ID = p.Product_ID 
                                WHERE c.Cart_ID = ?");
            $stmt->bind_param('i', $cart_ids[$x]);
            $stmt->execute();
            $result = $stmt->get_result();
            $cartitem = array(); 
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    $cartitem[] = $row;
                }
            }
        }
        
        $_SESSION['selectedCartIds'] = $cart_ids;
    ?>
    <header>
        <h1 class="visually-hidden">Checkout</h1>        
    </header>
    <!-- change action to payment.php -->
    <form action="payment.php" method="post" id="cartForm">
        <div class="container my-3 main-container">
            <main>
                <div class="container left-container">
                    <!-- Header row -->
                    <div class="hdr-row">
                        <div class="checkbox-all-rows"></div>
                        <div id="room-hdr">Item</div>
                        <div id="qty-hdr">Quantity</div>  
                        <div id="price-hdr">Price</div> 
                    </div>

                    <?php 
                    foreach ($cartitem as $row) {
                        $totalprice += $row['Price'] * $row['Quantity'];
                    ?>
                        <div class="obj-rows">
                            <div class="checkbox-all-rows"></div>
                            <div class="room-obj">
                                <!-- Image & Name -->
                                <img src="data:image/jpeg;base64,<?php echo base64_encode($row['Product_Image']); ?>" alt="<?php echo htmlspecialchars($row['Product_Name']); ?>" class="display-images">
                                <label for="object1" class="name-labels"><?php echo htmlspecialchars($row['Product_Name']); ?></label>
                            </div>
                            <div class="qty-obj"><?php echo htmlspecialchars($row['Quantity']); ?></div> 
                            <div class="price-obj">$<?php echo htmlspecialchars($row['Price']); ?></div> 
                        </div>
                    <?php
                    }
                    ?>
                </div> 
            </main>
            <!-- Checkout summary box -->
            <div class="container right-container" role="complementary">
                <div class="right-inner-container py-3 px-1">
                    <div id="order-summary-header">
                        Order summary
                    </div>
                    <div id="order-content-container">
                        <div id="subtotal-row">
                            <div id="subtotal-header">Total:</div>
                            <div id="subtotal-value">$<?php echo number_format($totalprice, 2); ?></div>
                        </div>
                        <div id="checkout-row">
                            <button type="submit" id="checkout-btn" class="btn btn-light">Proceed to payment</button>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </form>
    <?php 
        include 'includes/footer.php'; 
        $conn->close();
    } else {
        // Redirect back to cart if no items selected
        echo "<script>
            alert('No items selected for checkout.');
            window.location.href = 'cart.php';
        </script>";
        exit;
    }
    ?>
</body>
</html>
