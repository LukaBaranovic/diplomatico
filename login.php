<?php
// PHP code for authenticating the user

$is_invalid = false;

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $mysqli = require __DIR__ . "/database.php";

    // Use prepared statements to prevent SQL injection
    $stmt = $mysqli->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->bind_param("s", $_POST["username"]);
    $stmt->execute();
    $result = $stmt->get_result();
    $users = $result->fetch_assoc();

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
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="styles/style.css">
  <link rel="stylesheet" href="styles/login.css">
  <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
  <title>Diplomatico</title>
  <link rel="icon" type="image/x-icon" href="photo/company-favicon.png">
</head>
<body>
  <div class="login-wrapper">
    <div class="login-form-container">
      <div class="login-logo">
        <img src="photo/zukam.png" alt="company logo" loading="lazy">
      </div>

      <!-- Error message for invalid login -->
      <div class="invalid-login <?= $is_invalid ? '' : 'hidden' ?>">
          <em>Invalid login</em>
      </div>

      <!-- Login form -->
      <form class="login-form" method="post" novalidate>
        <div class="login-input-box">
          <input type="text" placeholder="Korisničko ime" name="username" id="username" 
          value="<?= htmlspecialchars($_POST["username"]) ?? "" ?>" required aria-label="Username">
          <i class="bx bxs-user"></i>
        </div>

        <div class="login-input-box">
          <input type="password" placeholder="Lozinka" name="password" id="password" required aria-label="Password">
          <i class="bx bxs-lock-alt" onclick="togglePasswordVisibility()"></i>
        </div>

        <button class="login-submit-button" type="submit">Prijavi se</button>
      </form>
    </div>
  </div>

  <script>
    function togglePasswordVisibility() {
      const passwordInput = document.getElementById("password");
      passwordInput.type = passwordInput.type === "password" ? "text" : "password";
    }
  </script>
</body>
</html>