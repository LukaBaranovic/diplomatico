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
$category_name = trim($data['category_name']);
$type_name = trim($data['type_name']);
$company_id = (int)$_SESSION['company_id'];

// Validate inputs
if (empty($category_name) || empty($type_name)) {
    echo json_encode(['status' => 'error', 'message' => 'All fields are required.']);
    exit;
}

// Check for duplicate category name (case-insensitive)
$sql_check = "SELECT 1 FROM CATEGORY WHERE LOWER(category_name) = LOWER(?) AND company_id = ?";
$stmt_check = $mysqli->prepare($sql_check);
$stmt_check->bind_param("si", $category_name, $company_id);
$stmt_check->execute();
$stmt_check->store_result();

if ($stmt_check->num_rows > 0) {
    echo json_encode(['status' => 'error', 'message' => 'Category already exists.']);
    exit;
}

// Fetch type ID based on type name
$sql_type = "SELECT type_id FROM TYPE WHERE type_name = ?";
$stmt_type = $mysqli->prepare($sql_type);
$stmt_type->bind_param("s", $type_name);
$stmt_type->execute();
$result_type = $stmt_type->get_result();
$type = $result_type->fetch_assoc();

if (!$type) {
    echo json_encode(['status' => 'error', 'message' => 'Invalid type selected.']);
    exit;
}

$type_id = $type['type_id'];

// Insert the new category
$sql_insert = "INSERT INTO CATEGORY (category_name, type_id, company_id) VALUES (?, ?, ?)";
$stmt_insert = $mysqli->prepare($sql_insert);
$stmt_insert->bind_param("sii", $category_name, $type_id, $company_id);

if ($stmt_insert->execute()) {
    echo json_encode([
        'status' => 'success',
        'message' => 'Category added successfully.',
        'category_id' => $stmt_insert->insert_id // Return the new category ID
    ]);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Failed to add category.']);
}