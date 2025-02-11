<?php
// Priprema .php za unos i provjeru podataka

session_start();
$mysqli = require_once __DIR__ . "/database.php";

$category_id = (int)$_POST['category_id'];

// Provjeravamo ima li kategorija koju brišemo children elemenata 
$query_check_for_children = "SELECT COUNT(*) FROM item WHERE category_id = ?";
$stmt_select_check_for_children = $mysqli->prepare($query_check_for_children);
$stmt_select_check_for_children->bind_param("i", $category_id);
$stmt_select_check_for_children->execute();
$stmt_select_check_for_children->bind_result($item_count);
$stmt_select_check_for_children->fetch();
$stmt_select_check_for_children->close();

echo $item_count;

// Ako ima children elemenata, vracamo se na početnu stranicu.
if ($item_count == 0) {

  // Pripremamo query 
  $query = "DELETE FROM `category` WHERE `category_id` = ?";

  // Provjerava imamo li category_id
  if (isset($_POST['category_id'])) {
    
    // Prirpema za brisanje.
    if ($stmt = $mysqli->prepare($query)) {
    $stmt->bind_param("i", $category_id);
  
    // Brisanje.
    if ($stmt->execute()) {
      header('Location: index.php?message=success');
    } else {
      header('Location: index.php?message=error');
    }
    $stmt->close();
    }
  }
    else {
    header('Location: index.php?message=error');
  } 
} else {
  header('Location: index.php?message=has-children-elements');
}

?>

<!-- #### Ova .php datoteka se koristi za brisanje kategorija. U script.js datoteci dobavljamo
 category_id pomoću kojeg biramo element za brisanje.

 Poruke:
 ?message=succes: ako je brisanje uspješno.
 ?message=error: ako je brisanje neuspješno.
 ?message=error-dependency: ako postoji artikli pod ovom kategorijom 

 Poruke dobivamo u url-u, a možda kasnije budu kasnije prikazane.
 Nebitno o uspješnosti brisanja iz baze podataka, vraćamo se na index stranicu, tj. početnu stranicu nakon prijave.

 Napravljeno: 10.2.2025.
 Zadnja promjena: 10.2.2025.
 Napravio: Luka Baranović
-->