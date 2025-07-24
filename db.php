<?php
// Database configuration
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "prf_db";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Initialize variables
$success = "";
$error = "";



// In db.php, ensure the table uses LONGBLOB for images
$createTableQuery = "CREATE TABLE IF NOT EXISTS scanned_documents (
    id INT AUTO_INCREMENT PRIMARY KEY,
    prf_no VARCHAR(50) NOT NULL,
    file_name VARCHAR(255) NOT NULL,
    file_type VARCHAR(50) NOT NULL,
    file_content LONGBLOB NOT NULL,  // Use LONGBLOB for large images
    upload_date DATETIME NOT NULL
)";

?>