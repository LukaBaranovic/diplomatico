<?php
// Priprema .php za unos i provjeru podataka

session_start();
$mysqli = require_once __DIR__ . "/database.php";
$item_id = (int)$_POST['item_id'];

// Pripremamo query 
$query = "DELETE FROM `item` WHERE `item_id` = ?";

// Provjerava imamo li item_id
if (isset($_POST['item_id'])) {
  
  // Prirpema za brisanje.
  if ($stmt = $mysqli->prepare($query)) {
  $stmt->bind_param("i", $item_id);

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


?>

<!-- #### Ova .php datoteka se koristi za brisanje artikala.

 Poruke:
 ?message=succes: ako je brisanje uspješno.
 ?message=error: ako je brisanje neuspješno.
 ?message=error-dependency: ako postoji artikli pod ovom kategorijom 

 Poruke dobivamo u url-u (na dan 2.12.2024.), a možda kasnije budu kasnije prikazane.
 Nebitno o uspješnosti brisanja iz baze podataka, vraćamo se na index stranicu, tj. početnu stranicu nakon prijave.

 Napravljeno: 27.11.2024.
 Zadnja promjena: 2.12.2024
 Napravio: Luka Baranović
-->