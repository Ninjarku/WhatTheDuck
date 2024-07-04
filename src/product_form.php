<?php
session_start(); // Start the session at the beginning of the file
include 'includes/navbar.php';
if ($_SESSION["cust_rol"] !== "Sales Admin") {
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
    'Price' => '',
    'Quantity' => '',
    'Product_Category' => '',
    'Product_Available' => 0,
    'Product_Image' => ''
];

// Include your database connection here
if ($Form_Type == 1 && $action === 'editProduct') {
    $Product_ID = isset($_GET['Product_ID']) ? intval($_GET['Product_ID']) : 0;
    $config = parse_ini_file('/var/www/private/db-config.ini');
    $conn = new mysqli($config['host'], $config['username'], $config['password'], $config['dbname']);

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
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css" integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous">
    <link rel="stylesheet" href="css/main.css">
    <!-- jQuery -->
    <script defer src="https://code.jquery.com/jquery-3.4.1.min.js" integrity="sha256-CSXorXvZcTkaix6Yvo6HppcZGetbYMGWSFlBw8HfCJo=" crossorigin="anonymous"></script>
    <!-- Bootstrap JS -->
    <script defer src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.bundle.min.js" integrity="sha384-6khuMg9gaYr5AxOqhkVIODVIvm9ynTT5J4V1cfthmT+emCG6yVmEZsRHdxlotUnm" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        body, html {
            font-family: 'Comic Neue', cursive;
            background-color: #fff5cc;
            color: black;
        }
        .navbar {
            background-color: #ffcc00;
        }
        .navbar-brand img {
            border-radius: 50%;
        }
        .nav-item .nav-link, .login-link, .cart-link {
            color: black !important;
            font-weight: bold;
            display: inline-block;
            padding: 10px 15px;
        }
        .nav-item .nav-link:hover, .login-link:hover, .cart-link:hover {
            color: #fff !important;
            background-color: #ff6347;
            border-radius: 5px;
            transition: background-color 0.3s ease-in-out;
        }
        .profile-content {
            background-color: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
        }
        .profile-content h1 {
            color: black;
        }
        .profile-content .form-group label {
            font-weight: bold;
        }
        .profile-content .form-group input, .profile-content .form-group select, .profile-content .form-group textarea {
            border: 1px solid #ddd;
            border-radius: 5px;
        }
        .btn-primary {
            background-color: #ffcc00;
            border: none;
            color: black;
        }
        .btn-primary:hover {
            background-color: #ff6347;
            color: white;
        }
        .profile-img-container {
            text-align: center;
            margin-top: 20px;
        }
        .profile-img {
            width: 150px;
            height: 150px;
            border-radius: 50%;
            object-fit: cover;
        }
        .form-container {
            flex: 1;
        }
        .image-container {
            margin-left: 30px;
            display: flex;
            flex-direction: column;
            align-items: center;
        }
        .container {
            margin-top: 50px;
            margin-bottom: 50px;
        }
    </style>
    <script>
        $(document).ready(function () {
    $("#product-form").on("submit", function (event) {
        event.preventDefault();
        var formData = new FormData(this);
        $.ajax({
            type: "POST",
            url: $(this).attr("action"),
            data: formData,
            processData: false,
            contentType: false,
            success: function (response) {
                Swal.fire({
                    icon: response.icon,
                    title: response.title,
                    text: response.message + "\n" + (response.debug || ""),
                    showCloseButton: false,
                    showCancelButton: false,
                    confirmButtonText: response.redirect ? 'OK' : 'OK'
                }).then((result) => {
                    if (result.isConfirmed && response.redirect) {
                        window.location.href = response.redirect;
                    }
                });
            },
            error: function () {
                Swal.fire({
                    icon: 'error',
                    title: 'Error!',
                    text: 'There was a problem with the request. Please try again.',
                    showCloseButton: false,
                    showCancelButton: false,
                    confirmButtonText: 'OK'
                });
            }
        });
    });
});
    </script>
</head>
<body>
    <div class="container">
        <div class="profile-content">
            <form id="product-form" method="post" enctype="multipart/form-data" class="d-flex w-100" action="process_product.php?action=<?php echo $Form_Type == 1 ? 'editProduct' : 'addProduct'; ?>">
                <div class="form-container">
                    <h1><?php echo $Form_Type == 1 ? 'Edit Product' : 'Add Product'; ?></h1>
                    <div class="form-group">
                        <label for="Product_Name">Product Name:</label>
                        <input type="text" class="form-control" id="Product_Name" name="Product_Name" value="<?php echo htmlspecialchars($product['Product_Name']); ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="Product_Description">Product Description:</label>
                        <textarea class="form-control" id="Product_Description" name="Product_Description" required><?php echo htmlspecialchars($product['Product_Description']); ?></textarea>
                    </div>
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
                    <button class="btn btn-primary mt-3" type="submit"><?php echo $Form_Type == 1 ? 'Update' : 'Add'; ?> Product</button>
                </div>
                <div class="image-container">
                    <h3>Product Image</h3>
                    <div class="profile-img-container">
                        <?php
                        if ($product['Product_Image']) {
                            echo '<img src="data:image/jpeg;base64,' . base64_encode($product['Product_Image']) . '" alt="Product Image" class="profile-img">';
                        } else {
                            echo '<img src="images/default_product.jpg" alt="Product Image" class="profile-img">';
                        }
                        ?>
                        <input type="file" name="Product_Image" accept="image/*" class="form-control-file mt-2" <?php echo $Form_Type == 1 ? '' : 'required'; ?>>
                    </div>
                </div>
            </form>
        </div>
    </div>
    <?php include 'includes/footer.php'; ?>
</body>
</html>

