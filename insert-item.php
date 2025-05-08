<?php
// Priprema .php za unos i provjeru podataka

session_start();
$mysqli = require_once __DIR__ . "/database.php";

// Provjerava jesu li vrijednosti 'user_id' i 'company_id' upisane u sesiju.
if (!isset($_SESSION['user_id']) || empty($_SESSION['company_id'])) {
    die("Session user_id or company_id is not set or is invalid.");
}

$user_id = (int)$_SESSION['user_id'];
$company_id = (int)$_SESSION['company_id'];

// Provjerava jesmo li sve  unijeli u obrazac
if (isset($_POST['add_item']) && !empty($_POST['item_name']) && !empty($_POST['item_price']) && !empty($_POST['category_name'])) {

    // Dobavljamo podatke iz obrasca
    $category_name = mysqli_real_escape_string($mysqli, $_POST['category_name']); 
    $item_name = trim($_POST['item_name']);  
    $item_price = floatval($_POST['item_price']);

    // Dobavljamo category_id od category_name
    $sql_get_category_id = "SELECT category_id FROM category WHERE category_name = ? AND company_id = ?";
    $stmt_get_category_id = $mysqli->prepare($sql_get_category_id);
    $stmt_get_category_id->bind_param('si', $category_name, $company_id);
    $stmt_get_category_id->execute();
    $result_get_category_id = $stmt_get_category_id->get_result();
    $category = $result_get_category_id->fetch_assoc();
    $category_id = $category['category_id'];

    // Provjeravamo postoji li duplikat
    $sql_check = "SELECT COUNT(*) FROM item WHERE item_name = ? AND company_id = ?";
    $stmt_check = $mysqli->prepare($sql_check);
    $stmt_check->bind_param("si", $item_name, $company_id);
    $stmt_check->execute();
    $stmt_check->bind_result($count);
    $stmt_check->fetch();
    $stmt_check->close();

    // Ako postoji duplikat, ne unosimo ništa i izlazimo 
    if ($count > 0) {
      echo "The category '{$category_name}' already exists for this user and company.";
      header("Location: index.php?message=duplicate");
    } else {
      $query = "INSERT INTO `item` (`item_name`, `item_price`, `category_id`, `company_id`) VALUES ('$item_name', '$item_price', '$category_id', '$company_id')";
      $result = mysqli_query($mysqli, $query);
      header("Location: index.php?message=success");
    }
  } else {
    header("Location: index.php?message=failed");
}
  ?>
