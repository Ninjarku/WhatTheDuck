<?php

// process_product.php

ini_set('display_errors', 1);
error_reporting(E_ALL);

function getAllProducts() {
    $config = parse_ini_file('/var/www/private/db-config.ini');
    $conn = new mysqli($config['host'], $config['username'], $config['password'], $config['dbname']);

    if ($conn->connect_error) {
        error_log("Connection failed: " . $conn->connect_error, 3, "/var/www/logs/error.log");
        header("Location: error_page.php?error_id=6&error=" . urlencode("Connection failed: " . $conn->connect_error));
        exit();
    } else {
        $stmt = $conn->prepare("SELECT Product_ID, Product_Name, Product_Description, Product_Image, Price, Quantity, Product_Category, Product_Available FROM Product ORDER BY Product_ID ASC");
        if (!$stmt) {
            error_log("Prepare failed: " . $conn->error, 3, "/var/www/logs/error.log");
            header("Location: error_page.php?error_id=6&error=" . urlencode("Prepare failed: " . $conn->error));
            exit();
        }

        $stmt->execute();
        $result = $stmt->get_result();

        $arrResult = array();
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $arrResult[] = $row;
            }
        }
        $stmt->close();
        $conn->close();
        return json_encode($arrResult);
    }
}

function deleteProduct($Product_ID) {
    $config = parse_ini_file('/var/www/private/db-config.ini');
    $conn = new mysqli($config['host'], $config['username'], $config['password'], $config['dbname']);

    if ($conn->connect_error) {
        error_log("Connection failed: " . $conn->connect_error, 3, "/var/www/logs/error.log");
        header("Location: error_page.php?error_id=7&error=" . urlencode("Connection failed: " . $conn->connect_error));
        exit();
    } else {
        if (empty($Product_ID)) {
            header("Location: error_page.php?error_id=7&error=" . urlencode("Empty Product ID."));
            exit();
        }

        $stmt = $conn->prepare("DELETE FROM Product WHERE Product_ID = ?");
        if (!$stmt) {
            error_log("Prepare failed: " . $conn->error, 3, "/var/www/logs/error.log");
            header("Location: error_page.php?error_id=7&error=" . urlencode("Prepare failed: " . $conn->error));
            exit();
        }

        $stmt->bind_param("i", $Product_ID);
        if (!$stmt->execute()) {
            error_log("Execute failed: " . $stmt->error, 3, "/var/www/logs/error.log");
            header("Location: error_page.php?error_id=7&error=" . urlencode("Execute failed: " . $stmt->error));
            exit();
        }

        $stmt->close();
        $conn->close();
        return header("Location: error_page.php?error_id=-1&error=" . urlencode("Product deleted successfully"));
    }
}

function getProductById($Product_ID) {
    $config = parse_ini_file('/var/www/private/db-config.ini');
    $conn = new mysqli($config['host'], $config['username'], $config['password'], $config['dbname']);

    if ($conn->connect_error) {
        error_log("Connection failed: " . $conn->connect_error, 3, "/var/www/logs/error.log");
        header("Location: error_page.php?error_id=8&error=" . urlencode("Connection failed: " . $conn->connect_error));
        exit();
    } else {
        if (empty($Product_ID)) {
            header("Location: error_page.php?error_id=8&error=" . urlencode("Empty Product ID."));
            exit();
        }

        $stmt = $conn->prepare("SELECT * FROM Product WHERE Product_ID = ?");
        if (!$stmt) {
            error_log("Prepare failed: " . $conn->error, 3, "/var/www/logs/error.log");
            header("Location: error_page.php?error_id=8&error=" . urlencode("Prepare failed: " . $conn->error));
            exit();
        }

        $stmt->bind_param("i", $Product_ID);
        if (!$stmt->execute()) {
            error_log("Execute failed: " . $stmt->error, 3, "/var/www/logs/error.log");
            header("Location: error_page.php?error_id=8&error=" . urlencode("Execute failed: " . $stmt->error));
        }

        $result = $stmt->get_result();
        $product = $result->fetch_assoc();

        $stmt->close();
        $conn->close();

        return json_encode($product);
    }
}

function addProduct($productData) {
    $config = parse_ini_file('/var/www/private/db-config.ini');
    $conn = new mysqli($config['host'], $config['username'], $config['password'], $config['dbname']);

    // Check connection
    if ($conn->connect_error) {
        error_log("Connection failed: " . $conn->connect_error, 3, "/var/www/logs/error.log");
        header("Location: error_page.php?error_id=9&error=" . urlencode("Connection failed: " . $conn->connect_error));
        exit();
    } else {
        // Check if the product name is already taken
        $stmt = $conn->prepare("SELECT * FROM Product WHERE Product_Name=?");
        $stmt->bind_param("s", $productData["Product_Name"]);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            header("Location: error_page.php?error_id=9&error=" . urlencode("Product name is already taken.")); // Redirect to login page
            exit();
        } else {
            $stmt->close();

            // Prepare statement to insert product details
            $stmt = $conn->prepare("INSERT INTO Product (Product_Name, Product_Description, Price, Quantity, Product_Category, Product_Available) VALUES (?, ?, ?, ?, ?, ?)");
            // $stmt = $conn->prepare("INSERT INTO Product (Product_Name, Product_Description, Product_Image, Price, Quantity, Product_Category, Product_Available) VALUES (?, ?, ?, ?, ?, ?, ?)");
            if (!$stmt) {
                error_log("Prepare failed: " . $conn->error, 3, "/var/www/logs/error.log");
                header("Location: error_page.php?error_id=9&error=" . urlencode("Prepare failed: " . $conn->error));
                exit();
            }

            // Sanitize inputs
            $name = sanitize_input($productData['Product_Name']);
            $description = sanitize_input($productData['Product_Description']);
            // $image = sanitize_input($productData['Product_Image']);
            $price = sanitize_input($productData['Price']);
            $quantity = sanitize_input($productData['Quantity']);
            $category = sanitize_input($productData['Product_Category']);
            $available = isset($productData["Product_Available"]) && $productData["Product_Available"] == 1 ? 1 : 0;

            // Bind parameters
            $stmt->bind_param("ssdisi", // update the parameter
                    $name,
                    $description,
                    // $image,
                    $price,
                    $quantity,
                    $category,
                    $available
            );

            if (!$stmt->execute()) {
                error_log("Execute failed: " . $stmt->error, 3, "/var/www/logs/error.log");
                header("Location: error_page.php?error_id=9&error=" . urlencode("Execute failed: " . $stmt->error));
            }
        }
    }
    $stmt->close();
    $conn->close();

    return header("Location: error_page.php?error_id=-1&error=" . urlencode("Product added successfully"));
}

function editProduct($productData) {
    $config = parse_ini_file('/var/www/private/db-config.ini');
    $conn = new mysqli($config['host'], $config['username'], $config['password'], $config['dbname']);

    if ($conn->connect_error) {
        error_log("Connection failed: " . $conn->connect_error, 3, "/var/www/logs/error.log");
        header("Location: error_page.php?error_id=10&error=" . urlencode("Connection failed: " . $conn->connect_error));
        exit();
    } else {
        $stmt = $conn->prepare("UPDATE Product SET Product_Name = ?, Product_Description = ?, Price = ?, Quantity = ?, Product_Category = ?, Product_Available = ? WHERE Product_ID = ?");
        // $stmt = $conn->prepare("UPDATE Product SET Product_Name = ?, Product_Description = ?, Product_Image = ?, Price = ?, Quantity = ?, Product_Category = ?, Product_Available = ? WHERE Product_ID = ?");
        
        if (!$stmt) {
            error_log("Prepare failed: " . $conn->error, 3, "/var/www/logs/error.log");
            header("Location: error_page.php?error_id=10&error=" . urlencode("Prepare failed: " . $conn->error));
            exit();
        }

        // Sanitize inputs
        $name = sanitize_input($productData['Product_Name']);
        $description = sanitize_input($productData['Product_Description']);
        // $image = sanitize_input($productData['Product_Image']);
        $price = sanitize_input($productData['Price']);
        $quantity = sanitize_input($productData['Quantity']);
        $category = sanitize_input($productData['Product_Category']);
        $available = isset($productData["Product_Available"]) && $productData["Product_Available"] == 1 ? 1 : 0;

        // Bind parameters
        $stmt->bind_param("ssssdisi",
                $name,
                $description,
                // $image,
                $price,
                $quantity,
                $category,
                $available,
                $productData["Product_ID"]
        );

        if (!$stmt->execute()) {
            error_log("Execute failed: " . $stmt->error, 3, "/var/www/logs/error.log");
            header("Location: error_page.php?error_id=10&error=" . urlencode("Execute failed: " . $stmt->error));
        }

        $stmt->close();
        $conn->close();

        return header("Location: error_page.php?error_id=-1&error=" . urlencode("Product updated successfully"));
    }
}

function sanitize_input($data) {
    $data = trim($data);
    $data = stripslashes($data);
    return $data;
}

// Main execution
$action = isset($_GET['action']) ? $_GET['action'] : (isset($_POST['action']) ? $_POST['action'] : null);

if ($action === 'getAllProducts') {
    header('Content-Type: application/json');
    echo getAllProducts();
} elseif ($action === 'deleteProduct' && isset($_GET['Product_ID'])) {
    header('Content-Type: application/json');
    echo deleteProduct($_GET['Product_ID']);
} elseif ($action === 'getProduct' && isset($_GET['Product_ID'])) {
    header('Content-Type: application/json');
    echo getProductById($_GET['Product_ID']);
} elseif ($action === 'addProduct' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    header('Content-Type: application/json');
    echo addProduct($_POST);
} elseif ($action === 'editProduct' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    header('Content-Type: application/json');
    echo editProduct($_POST);
}
