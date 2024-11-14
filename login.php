<?php

$is_invalid = false; 

if ($_SERVER["REQUEST_METHOD"] === "POST") {

  $mysqli = require __DIR__ . "/database.php";

  $sql = sprintf("SELECT * FROM user WHERE username = '%s'", $mysqli->real_escape_string($_POST["username"]));

  //trazi korisnika koji se poklapa sa unesenim korisnikom

  $result = $mysqli->query($sql);

  $user = $result ->fetch_assoc();

  if ($user) {

    if (password_verify($_POST["password"], $user["password_hash"])) {

      session_start();

      session_regenerate_id();  

      $_SESSION["user_id"] = $user["id"];

      header("Location: index.php");
      exit;
    }
  }

  $is_invalid = true;

}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-AU-Combatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="style.css">
  <title>diplomatico</title>
</head>


<body>

  <h1> Login </h1>

  <?php if ($is_invalid): ?>
    <em>Invalid login</em>
  <?php endif; ?>

  <form method="post">
    <label for="username">username</label>
    <input type="text" name="username" id="username">

    <label for="password">password</label>
    <input type="password" name="password" id="password">

    <button>Log in</button>
  </form>



</body>
</html>