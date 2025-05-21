<?php
session_start();

$mysqli = require_once __DIR__ . "/database.php";

if (!isset($_SESSION['user_id']) || $_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(401); 
    echo json_encode(['status' => 'error', 'message' => 'Neautiriziran pristup!']);
    exit;
}

$data = json_decode(file_get_contents("php://input"), true);
$category_id = (int)$data['category_id'];

$sql = "SELECT 1 FROM ITEM WHERE category_id = ?";
$stmt = $mysqli->prepare($sql);
$stmt->bind_param("i", $category_id);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows > 0) {
    echo json_encode(['status' => 'error', 'message' => 'Kategorija se ne može obrisati dok sadrži artikle!']);
    exit;
}

$sql_delete = "DELETE FROM CATEGORY WHERE category_id = ?";
$stmt_delete = $mysqli->prepare($sql_delete);
$stmt_delete->bind_param("i", $category_id);

if ($stmt_delete->execute()) {
    echo json_encode(['status' => 'success', 'message' => 'Kategorija izbrisana uspješno!']);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Greška pri brisanju kategorije!']);
}
?>