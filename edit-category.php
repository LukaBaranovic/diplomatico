<?php
session_start();

$mysqli = require_once __DIR__ . "/database.php";

if (!isset($_SESSION['user_id']) || $_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['status' => 'error', 'message' => 'Neautiriziran pristup!']);
    exit;
}

$company_id = (int)$_SESSION['company_id'];
$category_id = (int)$_POST['category_id'];
$new_category_name = trim($_POST['category_name']);
$new_type_name = trim($_POST['type_name']);

if (empty($new_category_name) || empty($new_type_name)) {
    echo json_encode(['status' => 'error', 'message' => 'Unesite sva polja!']);
    exit;
}

$sql_check = "
    SELECT 1 
    FROM CATEGORY 
    WHERE category_name = ? AND company_id = ? AND category_id != ?
    LIMIT 1
";
$stmt = $mysqli->prepare($sql_check);
$stmt->bind_param("sii", $new_category_name, $company_id, $category_id);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows > 0) {
    echo json_encode(['status' => 'error', 'message' => 'Kategorija već postoji!']);
    exit;
}

$sql_update = "
    UPDATE CATEGORY
    SET category_name = ?, type_id = (SELECT type_id FROM TYPE WHERE type_name = ?)
    WHERE category_id = ? AND company_id = ?
";
$stmt_update = $mysqli->prepare($sql_update);
$stmt_update->bind_param("ssii", $new_category_name, $new_type_name, $category_id, $company_id);

if ($stmt_update->execute()) {
    echo json_encode(['status' => 'success', 'message' => 'Kategorija ažurirana uspješno!']);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Greška pri ažuriranju kategorije!']);
}
?>