<?php
session_start(); // Start the session at the beginning of the file
// Check if the admin is logged in
if ($_SESSION["admin_login"] !== "success") {
    header("Location: error_page.php?error_id=0&error=" . urlencode("Please login!!"));
    exit();
}

$Form_Type = isset($_GET['Form_Type']) ? intval($_GET['Form_Type']) : 0;
$action = isset($_GET['action']) ? $_GET['action'] : '';

// Initialize an empty product array for adding a new product
$product = [
    'Product_ID' => '',
    'Product_Name' => '',
    'Product_Description' => '',
    'Product_Image' => '',
    'Price' => '',
    'Quantity' => '',
    'Product_Category' => '',
    'Product_Available' => 0
];

// Include your database connection here
if ($Form_Type == 1 && $action === 'editProduct') {
    $Product_ID = isset($_GET['Product_ID']) ? intval($_GET['Product_ID']) : 0;
    $config = parse_ini_file('/var/www/private/db-config.ini');
    $conn = new mysqli($config['servername'], $config['username'], $config['password'], $config['dbname']);

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Fetch product data for editing
    $stmt = $conn->prepare("SELECT * FROM Product WHERE Product_ID = ?");
    $stmt->bind_param("i", $Product_ID);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        $product = $result->fetch_assoc();
    }
    $stmt->close();
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <title><?php echo $Form_Type == 1 ? 'Edit Product' : 'Add Product'; ?></title>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">  

        <!-- START OF THE LINK -->
        <!-- Bootstrap CSS -->
        <link rel="stylesheet"
              href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css"
              integrity=
              "sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh"
              crossorigin="anonymous"> 
        <!-- jQuery -->
        <script src="js/jquery-3.5.1.js" type="text/javascript"></script>
        <!--Bootstrap JS-->
        <script defer
                src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.bundle.min.js"
                integrity="sha384-6khuMg9gaYr5AxOqhkVIODVIvm9ynTT5J4V1cfthmT+emCG6yVmEZsRHdxlotUnm"
                crossorigin="anonymous">
        </script> 
        <!-- DataTables JS -->
        <script defer src="js/datatables.min.js" type="text/javascript"></script>  
        <!-- DataTables CSS -->
        <link href="css/datatables.min.css" rel="stylesheet" type="text/css"/>
        <!-- FontAwesome for Icons -->
        <script src="https://kit.fontawesome.com/70ab820747.js" crossorigin="anonymous"></script>
        <!-- SweetAlert2 for Popups -->
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <!-- END OF THE LINK -->
    </head>
    <body>
        <div class="container">
            <h2><?php echo $Form_Type == 1 ? 'Edit Product' : 'Add Product'; ?></h2>
            <form id="product-form" method="post" action="process_product.php">
                <input type="hidden" name="Product_ID" value="<?php echo htmlspecialchars($product['Product_ID']); ?>">
                <input type="hidden" name="action" value="<?php echo $Form_Type == 1 ? 'editProduct' : 'addProduct'; ?>">

                <div class="form-group">
                    <label for="Product_Name">Product Name:</label>
                    <input type="text" class="form-control" id="Product_Name" name="Product_Name" value="<?php echo htmlspecialchars($product['Product_Name']); ?>" required>
                </div>
                <div class="form-group">
                    <label for="Product_Description">Product Description:</label>
                    <textarea class="form-control" id="Product_Description" name="Product_Description" required><?php echo htmlspecialchars($product['Product_Description']); ?></textarea>
                </div>
                <!--
                <div class="form-group">
                    <label for="Product_Image">Product Image URL:</label>
                    <input type="text" class="form-control" id="Product_Image" name="Product_Image" value="<?php echo htmlspecialchars($product['Product_Image']); ?>" required>
                </div>
                -->
                <div class="form-group">
                    <label for="Price">Price:</label>
                    <input type="number" step="0.01" class="form-control" id="Price" name="Price" value="<?php echo htmlspecialchars($product['Price']); ?>" required>
                </div>
                <div class="form-group">
                    <label for="Quantity">Quantity:</label>
                    <input type="number" class="form-control" id="Quantity" name="Quantity" value="<?php echo htmlspecialchars($product['Quantity']); ?>" required>
                </div>
                <div class="form-group">
                    <label for="Product_Category">Product Category:</label>
                    <input type="text" class="form-control" id="Product_Category" name="Product_Category" value="<?php echo htmlspecialchars($product['Product_Category']); ?>" required>
                </div>
                <div class="form-check">
                    <input type="checkbox" class="form-check-input" id="Product_Available" name="Product_Available" value="1" <?php echo $product['Product_Available'] == 1 ? 'checked' : ''; ?>>
                    <label class="form-check-label" for="Product_Available">Product Available</label>
                </div>

                <button type="submit" class="btn btn-primary"><?php echo $Form_Type == 1 ? 'Update' : 'Add'; ?> Product</button>
            </form>
        </div>

    </body>
</html>
