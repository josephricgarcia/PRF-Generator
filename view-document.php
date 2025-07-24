<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
include 'db.php';

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("HTTP/1.1 400 Bad Request");
    exit("Invalid document ID");
}

$id = (int)$_GET['id'];

$stmt = $conn->prepare("SELECT file_type, file_content FROM scanned_documents WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    header("HTTP/1.1 404 Not Found");
    exit("Document not found");
}

$doc = $result->fetch_assoc();
$stmt->close();
$conn->close();

header('Content-Type: ' . $doc['file_type']);
echo $doc['file_content'];
?>