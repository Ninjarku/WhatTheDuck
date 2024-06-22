<?php
$servername = "mysql";
$username = "duck";
$password = "DuckYou";
$dbname = "duckdb";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
echo "Connected successfully";
?>
