<?php
session_start();
require_once "database.php";

header("Content-Type: application/json");

$response = ["success" => false, "message" => ""];

if (!isset($_POST["item_name"], $_POST["item_price"], $_POST["category_id"])) {
    $response["message"] = "Popunite sva polja!";
    echo json_encode($response);
    exit;
}

$item_name = trim($_POST["item_name"]);
$item_price = floatval($_POST["item_price"]);
$category_id = intval($_POST["category_id"]);
$company_id = $_SESSION["company_id"];

if (empty($item_name) || $item_price <= 0 || $category_id <= 0) {
    $response["message"] = "Neispravan unos!";
    echo json_encode($response);
    exit;
}

$sql_check = "
    SELECT item_id 
    FROM ITEM 
    WHERE LOWER(item_name) = LOWER(?) AND company_id = ? AND category_id = ?
";
$stmt_check = $mysqli->prepare($sql_check);
if (!$stmt_check) {
    $response["message"] = "Greška u bazi podataka: " . $mysqli->error;
    echo json_encode($response);
    exit;
}

$stmt_check->bind_param("sii", $item_name, $company_id, $category_id);
$stmt_check->execute();
$stmt_check->store_result();

if ($stmt_check->num_rows > 0) {
    $response["message"] = "Artikal već postoji!";
    echo json_encode($response);
    $stmt_check->close();
    exit;
}
$stmt_check->close();

$sql_category = "SELECT category_name FROM CATEGORY WHERE category_id = ? AND company_id = ?";
$stmt_category = $mysqli->prepare($sql_category);

if (!$stmt_category) {
    $response["message"] = "Greška u bazi podataka: " . $mysqli->error;
    echo json_encode($response);
    exit;
}

$stmt_category->bind_param("ii", $category_id, $company_id);
$stmt_category->execute();
$stmt_category->bind_result($category_name);
$stmt_category->fetch();
$stmt_category->close();

if (empty($category_name)) {
    $response["message"] = "Neispravan ID kategorije!";
    echo json_encode($response);
    exit;
}

$sql = "INSERT INTO ITEM (item_name, item_price, category_id, company_id) VALUES (?, ?, ?, ?)";
$stmt = $mysqli->prepare($sql);

if (!$stmt) {
    $response["message"] = "Greška u bazi podataka: " . $mysqli->error;
    echo json_encode($response);
    exit;
}

$stmt->bind_param("sdii", $item_name, $item_price, $category_id, $company_id);

if ($stmt->execute()) {
    $response["success"] = true;
    $response["message"] = "Artikal dodan uspješno!";
    $response["item_id"] = $stmt->insert_id;
    $response["item_name"] = $item_name;
    $response["item_price"] = $item_price;
    $response["category_name"] = $category_name;
    $response["category_id"] = $category_id;
} else {
    $response["message"] = "Greška pri dodavanju artikla!";
}

$stmt->close();
$mysqli->close();

echo json_encode($response);