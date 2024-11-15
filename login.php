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
  <link rel="stylesheet" href="styles/style.css">
  <link rel="stylesheet" href="styles/login-style.css">
  <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
  <title>diplomatico</title>
</head>


<body class="login-page">



  <?php if ($is_invalid): ?>
    <em>Invalid login</em>
  <?php endif; ?>


  <div class="wrapper">
    <form method="post">
      <h1> Login </h1>
      <div class="input-box">
        <label for="username">username</label>
        <input type="text" placeholder="username" name="username" id="username">
        <i class='bx bxs-user'></i>
      </div>
      <div class="input-box">
        <label for="password">password</label>
        <input type="password" placeholder="password" name="password" id="password">
        <i class='bx bxs-lock-alt'></i>
      </div>

      <button type="submit" class="submit-button">Login</button>
    </form>
  </div>



</body>
</html>