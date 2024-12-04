<?php
// Priprema .php za unos i provjeru podataka

session_start();
$mysqli = require_once __DIR__ . "/database.php";

$category_id = $_POST['category_id'];
$category_id = mysqli_real_escape_string($mysqli, $category_id);

// Pripremamo query za brisanje.
$query = "DELETE FROM `category` WHERE `category_id` = ?";

echo $category_id;

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
  } else {
    header('Location: index.php?message=error');
}
?>

<!-- #### Ova .php datoteka se koristi za brisanje kategorija. U script.js datoteci dobavljamo
 category_id pomoću kojeg biramo element za brisanje.

 Poruke:
 ?message=succes: ako je brisanje uspješno.
 ?message=error: ako je brisanje neuspješno.

 Poruke dobivamo u url-u (na dan 2.12.2024.), a možda kasnije budu kasnije prikazane.
 Nebitno o uspješnosti brisanja iz baze podataka, vraćamo se na index stranicu, tj. početnu stranicu nakon prijave.

 Napravljeno: 27.11.2024.
 Zadnja promjena: 2.12.2024
 Napravio: Luka Baranović
-->