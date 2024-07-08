<?php
session_start();
header('Content-Type: application/json');

require_once 'jwt/jwt_cookie.php';

$response = array(
    "icon" => "error",
    "title" => "Operation failed!",
    "message" => "Please try again.",
    "redirect" => null
);

$decodedToken = checkAuthentication('Sales Admin');
if (!$decodedToken) {
    echo json_encode($response);
    exit();
}

function getDatabaseConnection()
{
    $config = parse_ini_file('/var/www/private/db-config.ini');
    $conn = new mysqli($config['host'], $config['username'], $config['password'], $config['dbname']);
    if ($conn->connect_error) {
        return null;
    }
    return $conn;
}

function sanitize_input($data)
{
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
    return $data;
}

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
            $row['Product_Image'] = base64_encode($row['Product_Image']);
            $arrResult[] = $row;
        }
    }

    $stmt->close();
    $conn->close();
    return json_encode(['icon' => 'success', 'data' => $arrResult]);
}

function addProduct($productData)
{
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
    $price = filter_var($productData['Price'], FILTER_VALIDATE_FLOAT);
    $quantity = filter_var($productData['Quantity'], FILTER_VALIDATE_INT);
    $category = sanitize_input($productData['Product_Category']);
    $available = isset($productData["Product_Available"]) && $productData["Product_Available"] == 1 ? 1 : 0;

    $stmt->bind_param("ssdisi", $name, $description, $price, $quantity, $category, $available);

    if (!$stmt->execute()) {
        $response["message"] = 'Execute failed: ' . $stmt->error;
        return json_encode($response);
    }

    $stmt->close();
    $conn->close();

    
    $response["title"] = "Product Added";
    $response["message"] = "Product added successfully";
    $response["redirect"] = "sales_index.php";
    return json_encode($response);
}

function editProduct($productData)
{
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
    $price = filter_var($productData['Price'], FILTER_VALIDATE_FLOAT);
    $quantity = filter_var($productData['Quantity'], FILTER_VALIDATE_INT);
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

function uploadProductImage($productData)
{
    $conn = getDatabaseConnection();
    global $response;
    if (!$conn) {
        $response["message"] = 'Database connection failed';
        return json_encode($response);
    }

    $Product_ID = sanitize_input($productData['Product_ID']);

    // Validate image file
    if (isset($_FILES['Product_Image']) && $_FILES['Product_Image']['error'] == UPLOAD_ERR_OK) {
        $allowed_extensions = ['jpg', 'jpeg', 'png'];
        $max_size = 1 * 1024 * 1024; // 1MB
        $file_extension = strtolower(pathinfo($_FILES['Product_Image']['name'], PATHINFO_EXTENSION));
        $file_size = $_FILES['Product_Image']['size'];

        // Use Fileinfo to get MIME type
        $finfo = new finfo(FILEINFO_MIME_TYPE);
        $mime_type = $finfo->file($_FILES['Product_Image']['tmp_name']);

        if (!in_array($file_extension, $allowed_extensions) || !in_array($mime_type, ['image/jpeg', 'image/png'])) {
            $response["message"] = 'Invalid file type. Only JPG and PNG files are allowed.';
            return json_encode($response);
        }

        if ($file_size > $max_size) {
            $response["message"] = 'File size too large. Maximum allowed size is 1MB.';
            return json_encode($response);
        }

        $image = file_get_contents($_FILES['Product_Image']['tmp_name']);
    } else {
        $response["message"] = 'Image upload failed';
        return json_encode($response);
    }

    if (!$image) {
        $response["message"] = 'Image upload failed';
        return json_encode($response);
    }

    $stmt = $conn->prepare("UPDATE Product SET Product_Image = ? WHERE Product_ID = ?");
    if (!$stmt) {
        $response["message"] = 'Prepare failed: ' . $conn->error;
        return json_encode($response);
    }

    // Bind image data as a blob
    $null = NULL;
    $stmt->bind_param("bi", $null, $Product_ID);
    $stmt->send_long_data(0, $image);

    if (!$stmt->execute()) {
        $response["message"] = 'Execute failed: ' . $stmt->error;
        return json_encode($response);
    }

    $stmt->close();
    $conn->close();

    $response["icon"] = "success";
    $response["title"] = "Image Uploaded";
    $response["message"] = "Product image uploaded successfully";
    return json_encode($response);
}


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
    } elseif ($action === 'uploadImage' && isset($_POST['Product_ID'])) {
        echo uploadProductImage($_POST);
    }
}
?>