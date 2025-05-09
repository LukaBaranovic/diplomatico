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

// Decode the JSON input
$data = json_decode(file_get_contents("php://input"), true);
$item_id = (int)$data['item_id'];

// Check if the item exists
$sql_check = "SELECT 1 FROM ITEM WHERE item_id = ?";
$stmt_check = $mysqli->prepare($sql_check);
$stmt_check->bind_param("i", $item_id);
$stmt_check->execute();
$stmt_check->store_result();

if ($stmt_check->num_rows === 0) {
    echo json_encode(['status' => 'error', 'message' => 'Item not found.']);
    exit;
}

// Delete the item
$sql_delete = "DELETE FROM ITEM WHERE item_id = ?";
$stmt_delete = $mysqli->prepare($sql_delete);
$stmt_delete->bind_param("i", $item_id);

if ($stmt_delete->execute()) {
    echo json_encode(['status' => 'success', 'message' => 'Item deleted successfully.']);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Failed to delete item.']);
}
?>