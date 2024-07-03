<?php
session_start();
header('Content-Type: application/json');
ini_set('display_errors', 1);
error_reporting(E_ALL);

//For sales index page
function getAllProductsSales() {
    $config = parse_ini_file('/var/www/private/db-config.ini');
    $conn = new mysqli($config['host'], $config['username'], $config['password'], $config['dbname']);

    if ($conn->connect_error) {
        return json_encode(['error' => 'Connection failed: ' . $conn->connect_error]);
    }

    $stmt = $conn->prepare("SELECT Product_ID, Product_Name, Product_Description, Price, Quantity, Product_Category, Product_Available FROM Product ORDER BY Product_ID ASC");
    if (!$stmt) {
        return json_encode(['error' => 'Prepare failed: ' . $conn->error]);
    }

    $stmt->execute();
    $result = $stmt->get_result();
    $arrResult = [];

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $arrResult[] = $row;
        }
    }

    $stmt->close();
    $conn->close();
    return json_encode(['icon' => 'success', 'data' => $arrResult]);
}

function deleteProduct($Product_ID) {
    $config = parse_ini_file('/var/www/private/db-config.ini');
    $conn = new mysqli($config['host'], $config['username'], $config['password'], $config['dbname']);

    if ($conn->connect_error) {
        return json_encode(['icon' => 'error', 'title' => 'Database Error', 'message' => 'Connection failed: ' . $conn->connect_error]);
    }

    if (empty($Product_ID)) {
        return json_encode(['icon' => 'error', 'title' => 'Error', 'message' => 'Empty Product ID.']);
    }

    $stmt = $conn->prepare("DELETE FROM Product WHERE Product_ID = ?");
    if (!$stmt) {
        return json_encode(['icon' => 'error', 'title' => 'Database Error', 'message' => 'Prepare failed: ' . $conn->error]);
    }

    $stmt->bind_param("i", $Product_ID);
    if (!$stmt->execute()) {
        return json_encode(['icon' => 'error', 'title' => 'Database Error', 'message' => 'Execute failed: ' . $stmt->error]);
    }

    $stmt->close();
    $conn->close();
    return json_encode(['icon' => 'success', 'title' => 'Product Deleted', 'message' => 'Product deleted successfully']);
}

function getProductById($Product_ID) {
    $config = parse_ini_file('/var/www/private/db-config.ini');
    $conn = new mysqli($config['host'], $config['username'], $config['password'], $config['dbname']);

    if ($conn->connect_error) {
        return json_encode(['icon' => 'error', 'title' => 'Database Error', 'message' => 'Connection failed: ' . $conn->connect_error, 'redirect' => 'product_form.php']);
    }

    if (empty($Product_ID)) {
        return json_encode(['icon' => 'error', 'title' => 'Error', 'message' => 'Empty Product ID.', 'redirect' => 'product_form.php']);
    }

    $stmt = $conn->prepare("SELECT * FROM Product WHERE Product_ID = ?");
    if (!$stmt) {
        return json_encode(['icon' => 'error', 'title' => 'Database Error', 'message' => 'Prepare failed: ' . $conn->error, 'redirect' => 'product_form.php']);
    }

    $stmt->bind_param("i", $Product_ID);
    if (!$stmt->execute()) {
        return json_encode(['icon' => 'error', 'title' => 'Database Error', 'message' => 'Execute failed: ' . $stmt->error, 'redirect' => 'product_form.php']);
    }

    $result = $stmt->get_result();
    $product = $result->fetch_assoc();

    $stmt->close();
    $conn->close();

    return json_encode(['icon' => 'success', 'title' => 'Product Fetched', 'message' => 'Product fetched successfully', 'data' => $product]);
}

function addProduct($productData) {
    $config = parse_ini_file('/var/www/private/db-config.ini');
    $conn = new mysqli($config['host'], $config['username'], $config['password'], $config['dbname']);

    if ($conn->connect_error) {
        return json_encode(['icon' => 'error', 'title' => 'Database Error', 'message' => 'Connection failed: ' . $conn->connect_error, 'redirect' => 'product_form.php']);
    }

    // Check if the product name is already taken
    $stmt = $conn->prepare("SELECT * FROM Product WHERE Product_Name=?");
    $stmt->bind_param("s", $productData["Product_Name"]);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        return json_encode(['icon' => 'error', 'title' => 'Error', 'message' => 'Product name is already taken.', 'redirect' => 'product_form.php']);
    }

    $stmt->close();

    $stmt = $conn->prepare("INSERT INTO Product (Product_Name, Product_Description, Product_Image, Price, Quantity, Product_Category, Product_Available) VALUES (?, ?, ?, ?, ?, ?, ?)");
    if (!$stmt) {
        return json_encode(['icon' => 'error', 'title' => 'Database Error', 'message' => 'Prepare failed: ' . $conn->error, 'redirect' => 'product_form.php']);
    }

    $name = sanitize_input($productData['Product_Name']);
    $description = sanitize_input($productData['Product_Description']);
    $price = sanitize_input($productData['Price']);
    $quantity = sanitize_input($productData['Quantity']);
    $category = sanitize_input($productData['Product_Category']);
    $available = isset($productData["Product_Available"]) && $productData["Product_Available"] == 1 ? 1 : 0;

    // Handle image upload
    $image = null;
    if (isset($_FILES['Product_Image']) && $_FILES['Product_Image']['error'] == UPLOAD_ERR_OK) {
        $image = file_get_contents($_FILES['Product_Image']['tmp_name']);
    }

    $stmt->bind_param("ssdisis", $name, $description, $image, $price, $quantity, $category, $available);

    if (!$stmt->execute()) {
        return json_encode(['icon' => 'error', 'title' => 'Database Error', 'message' => 'Execute failed: ' . $stmt->error, 'redirect' => 'product_form.php']);
    }

    $stmt->close();
    $conn->close();

    return json_encode(['icon' => 'success', 'title' => 'Product Added', 'message' => 'Product added successfully', 'redirect' => 'sales_index.php']);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $productData = $_POST;
    if (isset($_FILES['Product_Image'])) {
        $productData['Product_Image'] = $_FILES['Product_Image'];
    }

    $response = addProduct($productData);
    echo $response;
}

function editProduct($productData) {
    $config = parse_ini_file('/var/www/private/db-config.ini');
    $conn = new mysqli($config['host'], $config['username'], $config['password'], $config['dbname']);

    if ($conn->connect_error) {
        return json_encode(['icon' => 'error', 'title' => 'Database Error', 'message' => 'Connection failed: ' . $conn->connect_error, 'redirect' => 'product_form.php']);
    }

    $stmt = $conn->prepare("UPDATE Product SET Product_Name = ?, Product_Description = ?, Product_Image = ?, Price = ?, Quantity = ?, Product_Category = ?, Product_Available = ? WHERE Product_ID = ?");
    if (!$stmt) {
        return json_encode(['icon' => 'error', 'title' => 'Database Error', 'message' => 'Prepare failed: ' . $conn->error, 'redirect' => 'product_form.php']);
    }

    $name = sanitize_input($productData['Product_Name']);
    $description = sanitize_input($productData['Product_Description']);
    $price = sanitize_input($productData['Price']);
    $quantity = sanitize_input($productData['Quantity']);
    $category = sanitize_input($productData['Product_Category']);
    $available = isset($productData["Product_Available"]) && $productData["Product_Available"] == 1 ? 1 : 0;
    $image = null;

    if (isset($_FILES['Product_Image']) && $_FILES['Product_Image']['error'] == UPLOAD_ERR_OK) {
        $image = file_get_contents($_FILES['Product_Image']['tmp_name']);
    }

    $stmt->bind_param("sssdisii", $name, $description, $image, $price, $quantity, $category, $available, $productData["Product_ID"]);
    if (!$stmt->execute()) {
        return json_encode(['icon' => 'error', 'title' => 'Database Error', 'message' => 'Execute failed: ' . $stmt->error, 'redirect' => 'product_form.php']);
    }

    $stmt->close();
    $conn->close();

    return json_encode(['icon' => 'success', 'title' => 'Product Updated', 'message' => 'Product updated successfully', 'redirect' => 'sales_index.php']);
}

function sanitize_input($data) {
    $data = trim($data);
    $data = stripslashes($data);
    return $data;
}

$action = isset($_GET['action']) ? $_GET['action'] : (isset($_POST['action']) ? $_POST['action'] : null);

if ($action === 'getAllProductsSales') {
    echo getAllProductsSales();
} elseif ($action === 'deleteProduct' && isset($_GET['Product_ID'])) {
    echo deleteProduct($_GET['Product_ID']);
} elseif ($action === 'addProduct' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    echo addProduct($_POST);
} elseif ($action === 'editProduct' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    echo editProduct($_POST);
}
?>
