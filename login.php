<?php

$is_invalid = false; 

if ($_SERVER["REQUEST_METHOD"] === "POST") {

  $mysqli = require __DIR__ . "/database.php";
  $sql = sprintf("SELECT * FROM users WHERE username = '%s'", $mysqli->real_escape_string($_POST["username"]));

  //trazi korisnika koji se poklapa sa unesenim korisnikom

  $result = $mysqli->query($sql);
  $users = $result ->fetch_assoc();

  if ($users) {
    if (password_verify($_POST["password"], $users["password_hash"])) {
      session_start();
      session_regenerate_id();  
      $_SESSION["user_id"] = $users["id"];
      $_SESSION["company_id"] = $users["company_id"];
      header("Location: home.php");
      exit;
    }
  }

  $is_invalid = true;

  //php kod koji sluzi za prijavu korisnika u bazu podataka
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-AU-Combatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="styles/style.css">
  <link rel="stylesheet" href="styles/login.php">
  <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
  <title>Diplomatico</title>
  <link rel="icon" type="image/x-icon" href="photo/company-favicon.png">
</head>
<body>
  
</body>
</html>