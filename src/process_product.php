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
function getDatabaseConnection()
{
    $config = parse_ini_file('/var/www/private/db-config.ini');
    $conn = new mysqli($config['host'], $config['username'], $config['password'], $config['dbname']);
    if ($conn->connect_error) {
        return null;
    }
    return $conn;
}

// Get all products for sales index
function getAllProductsSales()
{
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

// Add product function
function addProduct($productData)
{
    $conn = getDatabaseConnection();
    global $response;
    if (!$conn) {
        $response["message"] = 'Database connection failed';
        return json_encode($response);
    }

    // Debugging: Check received product data
    $response["debug"] = "Received product data: " . json_encode($productData);

    $stmt = $conn->prepare("SELECT * FROM Product WHERE Product_Name=?");
    $stmt->bind_param("s", $productData["Product_Name"]);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $response["message"] = 'Product name is already taken.';
        return json_encode($response);
    }

    $stmt->close();

    $stmt = $conn->prepare("INSERT INTO Product (Product_Name, Product_Description, Product_Image, Price, Quantity, Product_Category, Product_Available) VALUES (?, ?, ?, ?, ?, ?, ?)");
    if (!$stmt) {
        $response["message"] = 'Prepare failed: ' . $conn->error;
        $response["debug"] = 'Prepare failed: ' . $conn->error;
        return json_encode($response);
    }
    
    $name = sanitize_input($productData['Product_Name']);
    $description = sanitize_input($productData['Product_Description']);
    $price = filter_var($productData['Price'], FILTER_VALIDATE_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
    if ($price === false) {
        $response["message"] = 'Invalid price format.';
        return json_encode($response);
    }
    $quantity = filter_var($productData['Quantity'], FILTER_VALIDATE_INT);
    $category = sanitize_input($productData['Product_Category']);
    $available = isset($productData["Product_Available"]) && $productData["Product_Available"] == 1 ? 1 : 0;
    
    $image = null;
    if (isset($_FILES['Product_Image']) && $_FILES['Product_Image']['error'] == UPLOAD_ERR_OK) {
        $image = file_get_contents($_FILES['Product_Image']['tmp_name']);
    }
    
    // Debugging: Output the SQL query
    $sql_query = "INSERT INTO Product (Product_Name, Product_Description, Product_Image, Price, Quantity, Product_Category, Product_Available) VALUES ('$name', '$description', '$image', '$price', '$quantity', '$category', '$available')";
    $response["debug"] .= " | SQL Query: $sql_query";
    
    $stmt->bind_param("ssdisis", $name, $description, $image, $price, $quantity, $category, $available);
    
    if (!$stmt->execute()) {
        $response["message"] = 'Execute failed: ' . $stmt->error;
        $response["debug"] .= ' | Execute failed: ' . $stmt->error;
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
function deleteProduct($Product_ID)
{
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


// Edit product function
function editProduct($productData, $files)
{
    $conn = getDatabaseConnection();
    global $response;
    if (!$conn) {
        $response["message"] = 'Database connection failed';
        return json_encode($response);
    }

    $productID = isset($productData['Product_ID']) ? intval($productData['Product_ID']) : null;
    if (!$productID) {
        $response["message"] = 'Invalid product ID';
        return json_encode($response);
    }

    $name = sanitize_input($productData['Product_Name']);
    $description = sanitize_input($productData['Product_Description']);
    $price = filter_var($productData['Price'], FILTER_VALIDATE_FLOAT);
    $quantity = filter_var($productData['Quantity'], FILTER_VALIDATE_INT);
    $category = sanitize_input($productData['Product_Category']);
    $available = isset($productData["Product_Available"]) && $productData["Product_Available"] == 1 ? 1 : 0;

    if ($price === false) {
        $response["message"] = 'Invalid price format';
        return json_encode($response);
    }

    if ($quantity === false) {
        $response["message"] = 'Invalid quantity format';
        return json_encode($response);
    }

    $query = "UPDATE Product SET Product_Name = ?, Product_Description = ?, Price = ?, Quantity = ?, Product_Category = ?, Product_Available = ?";
    $params = [$name, $description, $price, $quantity, $category, $available];
    $paramTypes = "ssdisi";

    if (isset($files['Product_Image']) && $files['Product_Image']['error'] == UPLOAD_ERR_OK) {
        $image = file_get_contents($files['Product_Image']['tmp_name']);
        $query .= ", Product_Image = ?";
        $params[] = $image;
        $paramTypes .= "b";
    }

    $query .= " WHERE Product_ID = ?";
    $params[] = $productID;
    $paramTypes .= "i";

    $stmt = $conn->prepare($query);
    if (!$stmt) {
        $response["message"] = 'Prepare failed: ' . $conn->error;
        return json_encode($response);
    }

    // Bind parameters dynamically
    $stmt->bind_param($paramTypes, ...$params);

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

// Sanitize input function
function sanitize_input($data)
{
    $data = trim($data);
    $data = stripslashes($data);
    return $data;
}


// Sanitize input
function sanitize_input($data)
{
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
