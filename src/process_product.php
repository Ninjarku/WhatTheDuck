<?php
session_start();
header('Content-Type: application/json');
ini_set('display_errors', 1);
error_reporting(E_ALL);

$response = array(
    "icon" => "error",
    "title" => "Operation failed!",
    "message" => "Please try again.",
    "redirect" => null
);

// Database connection
function getDatabaseConnection() {
    $config = parse_ini_file('/var/www/private/db-config.ini');
    $conn = new mysqli($config['host'], $config['username'], $config['password'], $config['dbname']);
    if ($conn->connect_error) {
        return null;
    }
    return $conn;
}

// Get all products for sales index
function getAllProductsSales() {
    $conn = getDatabaseConnection();
    global $response;
    if (!$conn) {
        $response["message"] = 'Database connection failed';
        return json_encode($response);
    }

    $stmt = $conn->prepare("SELECT Product_ID, Product_Name, Product_Description, Product_Image, Price, Quantity, Product_Category, Product_Available FROM Product ORDER BY Product_ID ASC");
    if (!$stmt) {
        $response["message"] = 'Prepare failed: ' . $conn->error;
        return json_encode($response);
    }

    $stmt->execute();
    $result = $stmt->get_result();
    $arrResult = [];

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $row['Product_Image'] = base64_encode($row['Product_Image']); // Encode the image data
            $arrResult[] = $row;
        }
    }

    $stmt->close();
    $conn->close();
    return json_encode(['icon' => 'success', 'data' => $arrResult]);
}

// Add product
function addProduct($productData) {
    $conn = getDatabaseConnection();
    global $response;
    if (!$conn) {
        $response["message"] = 'Database connection failed';
        return json_encode($response);
    }

    $stmt = $conn->prepare("SELECT * FROM Product WHERE Product_Name=?");
    $stmt->bind_param("s", $productData["Product_Name"]);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $response["message"] = 'Product name is already taken.';
        return json_encode($response);
    }

    $stmt->close();

    $stmt = $conn->prepare("INSERT INTO Product (Product_Name, Product_Description, Price, Quantity, Product_Category, Product_Available) VALUES (?, ?, ?, ?, ?, ?)");
    if (!$stmt) {
        $response["message"] = 'Prepare failed: ' . $conn->error;
        return json_encode($response);
    }

    $name = sanitize_input($productData['Product_Name']);
    $description = sanitize_input($productData['Product_Description']);
    $price = sanitize_input($productData['Price']);
    $quantity = sanitize_input($productData['Quantity']);
    $category = sanitize_input($productData['Product_Category']);
    $available = isset($productData["Product_Available"]) && $productData["Product_Available"] == 1 ? 1 : 0;

    $stmt->bind_param("ssdisi", $name, $description, $price, $quantity, $category, $available);

    if (!$stmt->execute()) {
        $response["message"] = 'Execute failed: ' . $stmt->error;
        return json_encode($response);
    }

    $stmt->close();
    $conn->close();

    $response["icon"] = "success";
    $response["title"] = "Product Added";
    $response["message"] = "Product added successfully";
    $response["redirect"] = "sales_index.php";
    return json_encode($response);
}

// Delete product
function deleteProduct($Product_ID) {
    $conn = getDatabaseConnection();
    global $response;
    if (!$conn) {
        $response["message"] = 'Database connection failed';
        return json_encode($response);
    }

    if (empty($Product_ID)) {
        $response["message"] = 'Empty Product ID.';
        return json_encode($response);
    }

    $stmt = $conn->prepare("DELETE FROM Product WHERE Product_ID = ?");
    if (!$stmt) {
        $response["message"] = 'Prepare failed: ' . $conn->error;
        return json_encode($response);
    }

    $stmt->bind_param("i", $Product_ID);
    if (!$stmt->execute()) {
        $response["message"] = 'Execute failed: ' . $stmt->error;
        return json_encode($response);
    }

    $stmt->close();
    $conn->close();
    $response["icon"] = "success";
    $response["title"] = "Product Deleted";
    $response["message"] = "Product deleted successfully";
    return json_encode($response);
}

// Edit product
function editProduct($productData) {
    $conn = getDatabaseConnection();
    global $response;
    if (!$conn) {
        $response["message"] = 'Database connection failed';
        return json_encode($response);
    }

    $stmt = $conn->prepare("UPDATE Product SET Product_Name = ?, Product_Description = ?, Price = ?, Quantity = ?, Product_Category = ?, Product_Available = ? WHERE Product_ID = ?");
    if (!$stmt) {
        $response["message"] = 'Prepare failed: ' . $conn->error;
        return json_encode($response);
    }

    $name = sanitize_input($productData['Product_Name']);
    $description = sanitize_input($productData['Product_Description']);
    $price = sanitize_input($productData['Price']);
    $quantity = sanitize_input($productData['Quantity']);
    $category = sanitize_input($productData['Product_Category']);
    $available = isset($productData["Product_Available"]) && $productData["Product_Available"] == 1 ? 1 : 0;

    $stmt->bind_param("ssdisii", $name, $description, $price, $quantity, $category, $available, $productData["Product_ID"]);

    if (!$stmt->execute()) {
        $response["message"] = 'Execute failed: ' . $stmt->error;
        return json_encode($response);
    }

    $stmt->close();
    $conn->close();

    $response["icon"] = "success";
    $response["title"] = "Product Updated";
    $response["message"] = "Product updated successfully";
    $response["redirect"] = "sales_index.php";
    return json_encode($response);
}

// Sanitize input
function sanitize_input($data) {
    $data = trim($data);
    $data = stripslashes($data);
    return $data;
}

// Handle actions
$action = isset($_GET['action']) ? $_GET['action'] : (isset($_POST['action']) ? $_POST['action'] : null);

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    if ($action === 'getAllProductsSales') {
        echo getAllProductsSales();
    }
} elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if ($action === 'addProduct') {
        echo addProduct($_POST);
    } elseif ($action === 'editProduct') {
        echo editProduct($_POST);
    } elseif ($action === 'deleteProduct' && isset($_POST['Product_ID'])) {
        echo deleteProduct($_POST['Product_ID']);
    }
}
?>
