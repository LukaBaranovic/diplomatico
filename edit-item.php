<?php
session_start();

// Include database connection
$mysqli = require_once __DIR__ . "/database.php";

// Ensure the user is logged in and the request is POST
if (!isset($_SESSION['user_id']) || $_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized request']);
    exit;
}

// Fetch POST data and sanitize inputs
$company_id = (int)$_SESSION['company_id'];
$item_id = (int)$_POST['item_id'];
$new_item_name = trim($_POST['item_name']);
$new_price = trim($_POST['item_price']);
$new_category_id = (int)$_POST['category_id'];

// Validate inputs
if (empty($new_item_name) || empty($new_price) || empty($new_category_id)) {
    echo json_encode(['status' => 'error', 'message' => 'All fields are required']);
    exit;
}

if (!is_numeric($new_price) || $new_price <= 0) {
    echo json_encode(['status' => 'error', 'message' => 'Price must be a positive number greater than 0']);
    exit;
}

// Check if the new item name already exists for the same company
$sql_check = "
    SELECT 1 
    FROM ITEM 
    WHERE item_name = ? AND company_id = ? AND item_id != ?
    LIMIT 1
";
$stmt = $mysqli->prepare($sql_check);
$stmt->bind_param("sii", $new_item_name, $company_id, $item_id);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows > 0) {
    echo json_encode(['status' => 'error', 'message' => 'Item name already exists']);
    exit;
}

// Update the item in the database
$sql_update = "
    UPDATE ITEM
    SET item_name = ?, item_price = ?, category_id = ?
    WHERE item_id = ? AND company_id = ?
";
$stmt_update = $mysqli->prepare($sql_update);
$stmt_update->bind_param("sdiii", $new_item_name, $new_price, $new_category_id, $item_id, $company_id);

if ($stmt_update->execute()) {
    echo json_encode(['status' => 'success', 'message' => 'Item updated successfully']);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Failed to update item']);
}
?>