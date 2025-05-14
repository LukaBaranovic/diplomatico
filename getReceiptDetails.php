<?php
session_start();

// Include the database connection
$mysqli = require_once __DIR__ . "/database.php";

// Check if receipt_id is provided
if (!isset($_GET['receipt_id'])) {
    http_response_code(400); // Bad Request
    echo json_encode(["error" => "Receipt ID is required."]);
    exit;
}

$receipt_id = (int)$_GET['receipt_id'];

// Fetch receipt details
$sql_receipt = "SELECT receipt_id, table_number, total_price FROM receipts WHERE receipt_id = ?";
$stmt_receipt = $mysqli->prepare($sql_receipt);
$stmt_receipt->bind_param("i", $receipt_id);
$stmt_receipt->execute();
$result_receipt = $stmt_receipt->get_result();

if ($result_receipt->num_rows === 0) {
    http_response_code(404); // Not Found
    echo json_encode(["error" => "Receipt not found."]);
    exit;
}

$receipt = $result_receipt->fetch_assoc();

// Fetch receipt items
$sql_items = "SELECT item_name, quantity, total_price FROM receipt_items WHERE receipt_id = ?";
$stmt_items = $mysqli->prepare($sql_items);
$stmt_items->bind_param("i", $receipt_id);
$stmt_items->execute();
$result_items = $stmt_items->get_result();

$items = [];
while ($row = $result_items->fetch_assoc()) {
    $items[] = $row;
}

// Return receipt details and items as JSON
echo json_encode([
    "receipt" => $receipt,
    "items" => $items,
]);