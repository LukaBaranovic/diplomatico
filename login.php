<?php
$is_invalid = false; 

if ($_SERVER["REQUEST_METHOD"] === "POST") {

  $mysqli = require __DIR__ . "/database.php";
  $sql = sprintf("SELECT * FROM users WHERE username = '%s'", $mysqli->real_escape_string($_POST["username"]));

  $result = $mysqli->query($sql);
  $users = $result ->fetch_assoc();

  if ($users) {
    if (password_verify($_POST["password"], $users["password_hash"])) {
      session_start();
      session_regenerate_id();  
      $_SESSION["user_id"] = $users["id"];
      $_SESSION["company_id"] = $users["company_id"];
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
  <link rel="stylesheet" href="styles/login.css">
  <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
  <title>Diplomatico - Prijava</title>
  <link rel="icon" type="image/x-icon" href="photo/company-favicon.png">
</head>

<body>
    
  <div class="login-wrapper">
    <div class="login-form-container">

      <div class="login-logo">
        <img src="photo/zukam.png" alt="company logo">
      </div>

      <div class="invalid-login">
          <?php if ($is_invalid): ?>
            <em>Neuspješna prijava</em>
          <?php endif; ?>
      </div>

      <form class="login-form" method="post">
        <div class="login-input-box">
          <input type="text" placeholder="Korisničko ime" name="username" id="username" 
          value="<?= htmlspecialchars($_POST["username"]) ?? "" ?>">
          <!-- <i class="bx bxs-user"></i> -->
        </div>

        <div class="login-input-box">
          <input type="password" placeholder="Lozinka" name="password" id="password">
          <!-- <i class="bx bxs-lock-alt"></i> -->
        </div>

        <button class="login-submit-button">Prijavi se</button>
      </form>
    </div>
  </div>

</body>
</html>