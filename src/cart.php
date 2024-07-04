<?php 
session_start(); 
include_once "includes/navbar.php";

if ($_SESSION["cust_login"] != "success") {
    echo "<script>window.location.href = 'Login.php';</script>";
}

$User_ID = $_SESSION['userid'];
$config = parse_ini_file('/var/www/private/db-config.ini');
$conn = new mysqli($config['host'], $config['username'], $config['password'], $config['dbname']);
if ($conn->connect_error) {
    error_log("Connection failed: " . $conn->connect_error, 3, "/var/www/logs/error.log");
    $urlloc = "error_page.php?error_id=6&error=" . urlencode("Connection failed: " . $conn->connect_error);
    echo "<script>window.location.href = '".$urlloc."';</script>"; 
    exit();
} else {
    $stmt = $conn->prepare("SELECT c.Cart_ID, c.Product_ID, c.Quantity, c.Price, c.Total_Price, p.Product_Name, p.Product_Image FROM Cart c, Product p WHERE c.Product_ID = p.Product_ID AND User_ID = ?");
    if (!$stmt) {
        error_log("Prepare failed: " . $conn->error, 3, "/var/www/logs/error.log");
        $urlloc = "error_page.php?error_id=6&error=" . urlencode("Prepare failed: " . $conn->connect_error);
        echo "<script>window.location.href = '".$urlloc."';</script>"; 
        exit();
    }
    $stmt->bind_param("i", $User_ID);
    $stmt->execute();
    $result = $stmt->get_result();

    $cartitem = array(); 
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $cartitem[] = $row;
        }
    }
    $stmt->close();
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shopping Cart</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet"
          href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css"
          integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh"
          crossorigin="anonymous"> 
    <!--jQuery-->
    <script src="js/jquery-3.5.1.js"></script>
    <!--Bootstrap JS-->
    <script defer
            src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.bundle.min.js"
            integrity="sha384-6khuMg9gaYr5AxOqhkVIODVIvm9ynTT5J4V1cfthmT+emCG6yVmEZsRHdxlotUnm"
            crossorigin="anonymous">
    </script>
    <!-- Load SweetAlert2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="js/cart.js" defer></script>

    <link rel="stylesheet" href="css/cart.css">
</head>
<body>
    <header>
        <h1 class="visually-hidden">My Cart</h1>        
    </header>

    <form method="post" id="cartForm" action="checkout.php">
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

                    <div id="cartItems">
                        <!-- Cart items will be loaded here -->
                        <?php if (!empty($cartitem)): ?>
                            <?php foreach ($cartitem as $item):?> 
                            <div class="obj-rows">
                                <div class="checkbox-all-rows">
                                    <label for="checkbox-<?php echo htmlspecialchars($item['Cart_ID']); ?>" class="visually-hidden">Select:</label>
                                    <input type="checkbox" name="selectedCartIds[]" value="<?php echo htmlspecialchars($item['Cart_ID']); ?>" class="cart-checkbox form-check-input" id="checkbox-<?php echo htmlspecialchars($item['Cart_ID']); ?>">
                                </div>
                                <div class="room-obj">
                                    <img src="data:image/jpeg;base64,<?php echo base64_encode($item['Product_Image']); ?>" alt="<?php echo htmlspecialchars($item['Product_Name']); ?>" class="display-images">
                                    <div class="name-labels"><?php echo htmlspecialchars($item['Product_Name']); ?></div>
                                </div>
                                <div class="qty-obj"><?php echo htmlspecialchars($item['Quantity']); ?></div> 
                                <div class="price-obj">$<?php echo htmlspecialchars($item['Price']); ?></div> 
                                <div class="delete">
                                    <button type="button" class="delete-btn" name="delete" value="<?php echo htmlspecialchars($item['Cart_ID']); ?>" data-price="<?php echo htmlspecialchars($item['Price']); ?>">
                                        Delete
                                    </button>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <p class="text-center">No Item in cart.</p>
                        <?php endif; ?>
                    </div>
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
                            <div id="subtotal-header">
                                Total:
                            </div>
                            <div id="subtotal-value">
                                $0.00
                            </div>
                        </div>
                        <div id="checkout-row">
                            <button type="submit" id="checkout-btn" class="btn btn-light" disabled>Proceed to checkout</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>

    <?php include 'includes/footer.php'; ?>
</body>
<noscript>
    <p>This course portal requires JavaScript to verify your identity. Please enable JavaScript to access the course.</p>
</noscript>
</html>
