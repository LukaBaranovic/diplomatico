<?php
session_start();
require_once "database.php";

header("Content-Type: application/json");

$response = ["status" => "error", "message" => "Greška!"];

$data = json_decode(file_get_contents("php://input"), true);
if (!isset($data["category_name"], $data["type_name"])) {
    $response["message"] = "Neispravan unos!";
    echo json_encode($response);
    exit;
}

$category_name = trim($data["category_name"]);
$type_name = trim($data["type_name"]);

if (empty($category_name) || empty($type_name)) {
    $response["message"] = "Popunite sva polja!";
    echo json_encode($response);
    exit;
}

$company_id = $_SESSION["company_id"];

$sql_check = "
    SELECT category_id 
    FROM CATEGORY 
    WHERE LOWER(category_name) = LOWER(?) AND company_id = ?
";
$stmt_check = $mysqli->prepare($sql_check);
if (!$stmt_check) {
    $response["message"] = "Greška u bazi podataka: " . $mysqli->error;
    echo json_encode($response);
    exit;
}

$stmt_check->bind_param("si", $category_name, $company_id);
$stmt_check->execute();
$stmt_check->store_result();

if ($stmt_check->num_rows > 0) {
    $response["message"] = "Kategorija već postoji!";
    echo json_encode($response);
    $stmt_check->close();
    exit;
}
$stmt_check->close();

$sql = "INSERT INTO CATEGORY (category_name, type_id, company_id) 
        SELECT ?, t.type_id, ? 
        FROM TYPE t 
        WHERE t.type_name = ?";
$stmt = $mysqli->prepare($sql);

if (!$stmt) {
    $response["message"] = "Greška u bazi podataka: " . $mysqli->error;
    echo json_encode($response);
    exit;
}

$stmt->bind_param("sis", $category_name, $company_id, $type_name);
if ($stmt->execute()) {
    $response["status"] = "success";
    $response["message"] = "Kategorija dodana uspješno!";
    $response["category_id"] = $stmt->insert_id;
} else {
    $response["message"] = "Greška pri dodavanju kategorije!";
}

$stmt->close();
$mysqli->close();

echo json_encode($response);