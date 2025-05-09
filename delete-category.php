<?php
session_start();

// Include database connection
$mysqli = require_once __DIR__ . "/database.php";

// Ensure the user is logged in and the request is POST
if (!isset($_SESSION['user_id']) || $_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(401); // Unauthorized
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized request']);
    exit;
}

// Get category ID from request
$data = json_decode(file_get_contents("php://input"), true);
$category_id = (int)$data['category_id'];

// Check if the category has associated items
$sql = "SELECT 1 FROM ITEM WHERE category_id = ?";
$stmt = $mysqli->prepare($sql);
$stmt->bind_param("i", $category_id);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows > 0) {
    echo json_encode(['status' => 'error', 'message' => 'This category has items and cannot be deleted.']);
    exit;
}

// Delete the category
$sql_delete = "DELETE FROM CATEGORY WHERE category_id = ?";
$stmt_delete = $mysqli->prepare($sql_delete);
$stmt_delete->bind_param("i", $category_id);

if ($stmt_delete->execute()) {
    echo json_encode(['status' => 'success', 'message' => 'Category deleted successfully.']);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Failed to delete category.']);
}
?>