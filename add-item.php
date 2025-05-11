<?php
session_start();
require_once "database.php";

header("Content-Type: application/json"); // Set content type to JSON

$response = ["success" => false, "message" => ""];

// Validate input
if (!isset($_POST["item_name"], $_POST["item_price"], $_POST["category_id"])) {
    $response["message"] = "All fields are required.";
    echo json_encode($response);
    exit;
}

$item_name = trim($_POST["item_name"]);
$item_price = floatval($_POST["item_price"]);
$category_id = intval($_POST["category_id"]);
$company_id = $_SESSION["company_id"];

// Ensure fields are valid
if (empty($item_name) || $item_price <= 0 || $category_id <= 0) {
    $response["message"] = "Invalid inputs.";
    echo json_encode($response);
    exit;
}

// Check if the item already exists (case-insensitive)
$sql_check = "
    SELECT item_id 
    FROM ITEM 
    WHERE LOWER(item_name) = LOWER(?) AND company_id = ? AND category_id = ?
";
$stmt_check = $mysqli->prepare($sql_check);
if (!$stmt_check) {
    $response["message"] = "Database error: " . $mysqli->error;
    echo json_encode($response);
    exit;
}

$stmt_check->bind_param("sii", $item_name, $company_id, $category_id);
$stmt_check->execute();
$stmt_check->store_result();

if ($stmt_check->num_rows > 0) {
    $response["message"] = "Item already exists!";
    echo json_encode($response);
    $stmt_check->close();
    exit;
}
$stmt_check->close();

// Fetch category name from the database
$sql_category = "SELECT category_name FROM CATEGORY WHERE category_id = ? AND company_id = ?";
$stmt_category = $mysqli->prepare($sql_category);

if (!$stmt_category) {
    $response["message"] = "Database error: " . $mysqli->error;
    echo json_encode($response);
    exit;
}

$stmt_category->bind_param("ii", $category_id, $company_id);
$stmt_category->execute();
$stmt_category->bind_result($category_name);
$stmt_category->fetch();
$stmt_category->close();

// Check if category_name was found
if (empty($category_name)) {
    $response["message"] = "Invalid category ID.";
    echo json_encode($response);
    exit;
}

// Insert the new item into the database
$sql = "INSERT INTO ITEM (item_name, item_price, category_id, company_id) VALUES (?, ?, ?, ?)";
$stmt = $mysqli->prepare($sql);

if (!$stmt) {
    $response["message"] = "Database error: " . $mysqli->error;
    echo json_encode($response);
    exit;
}

$stmt->bind_param("sdii", $item_name, $item_price, $category_id, $company_id);

if ($stmt->execute()) {
    $response["success"] = true;
    $response["message"] = "Item added successfully!";
    $response["item_id"] = $stmt->insert_id;
    $response["item_name"] = $item_name;
    $response["item_price"] = $item_price;
    $response["category_name"] = $category_name; // Use the fetched category name
    $response["category_id"] = $category_id;
} else {
    $response["message"] = "Failed to add item.";
}

$stmt->close();
$mysqli->close();

echo json_encode($response);