<?php
session_start();

$mysqli = require_once __DIR__ . "/database.php";

if (!isset($_SESSION['user_id']) || $_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(401); 
    echo json_encode(['status' => 'error', 'message' => 'Neautiriziran pristup!']);
    exit;
}

$company_id = (int)$_SESSION['company_id'];
$item_id = (int)$_POST['item_id'];
$new_item_name = htmlspecialchars(trim($_POST['item_name']), ENT_QUOTES, 'UTF-8');
$new_price = trim($_POST['item_price']);
$new_category_id = (int)$_POST['category_id'];

if (empty($new_item_name) || empty($new_price) || empty($new_category_id)) {
    http_response_code(400); 
    echo json_encode(['status' => 'error', 'message' => 'Unesite sva polja!']);
    exit;
}

if (!is_numeric($new_price) || $new_price <= 0) {
    http_response_code(400); 
    echo json_encode(['status' => 'error', 'message' => 'Cijena mora biti pozitivan broj!']);
    exit;
}

$sql_check = "
    SELECT 1 
    FROM ITEM 
    WHERE LOWER(item_name) = LOWER(?) AND company_id = ? AND item_id != ?
    LIMIT 1
";
$stmt = $mysqli->prepare($sql_check);
$stmt->bind_param("sii", $new_item_name, $company_id, $item_id);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows > 0) {
    http_response_code(409); 
    echo json_encode(['status' => 'error', 'message' => 'Artikal već postoji!']);
    exit;
}

$sql_category_check = "
    SELECT 1 
    FROM CATEGORY 
    WHERE category_id = ? AND company_id = ?
    LIMIT 1
";
$stmt_category_check = $mysqli->prepare($sql_category_check);
$stmt_category_check->bind_param("ii", $new_category_id, $company_id);
$stmt_category_check->execute();
$stmt_category_check->store_result();

if ($stmt_category_check->num_rows === 0) {
    http_response_code(404); 
    echo json_encode(['status' => 'error', 'message' => 'Kategorija nije pronađena!']);
    exit;
}

$sql_update = "
    UPDATE ITEM
    SET item_name = ?, item_price = ?, category_id = ?
    WHERE item_id = ? AND company_id = ?
";
$stmt_update = $mysqli->prepare($sql_update);
$stmt_update->bind_param("sdiii", $new_item_name, $new_price, $new_category_id, $item_id, $company_id);

if ($stmt_update->execute()) {
    http_response_code(200); 
    echo json_encode(['status' => 'success', 'message' => 'Artikal ažuriran uspješno!']);
} else {
    error_log("Database error: " . $mysqli->error);
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => 'Greška pri ažuriranju artikla!']);
}
?>