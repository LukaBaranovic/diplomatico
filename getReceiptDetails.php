<?php
session_start();

$mysqli = require_once __DIR__ . "/database.php";

if (!isset($_GET['receipt_id'])) {
    http_response_code(400); 
    echo json_encode(["error" => "ID računa nije dohvaćen!"]);
    exit;
}

$receipt_id = (int)$_GET['receipt_id'];

$sql_receipt = "SELECT receipt_id, table_number, total_price FROM receipts WHERE receipt_id = ?";
$stmt_receipt = $mysqli->prepare($sql_receipt);
$stmt_receipt->bind_param("i", $receipt_id);
$stmt_receipt->execute();
$result_receipt = $stmt_receipt->get_result();

if ($result_receipt->num_rows === 0) {
    http_response_code(404); 
    echo json_encode(["error" => "Račun nije pronađen!"]);
    exit;
}

$receipt = $result_receipt->fetch_assoc();

$sql_items = "SELECT item_name, quantity, total_price FROM receipt_items WHERE receipt_id = ?";
$stmt_items = $mysqli->prepare($sql_items);
$stmt_items->bind_param("i", $receipt_id);
$stmt_items->execute();
$result_items = $stmt_items->get_result();

$items = [];
while ($row = $result_items->fetch_assoc()) {
    $items[] = $row;
}

echo json_encode([
    "receipt" => $receipt,
    "items" => $items,
]);