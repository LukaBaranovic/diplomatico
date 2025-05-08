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
