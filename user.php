<?php

session_start();

$user_id = (int)$_SESSION['user_id'];
$company_id = (int)$_SESSION['company_id'];

if (isset($_SESSION["user_id"])) {
  $mysqli = require_once __DIR__ ."/database.php";
  $sql = "SELECT * FROM users WHERE id = {$_SESSION["user_id"]}";
  $result = $mysqli->query($sql);
  $users = $result->fetch_assoc();
}

$users = [];
if (isset($_SESSION["user_id"])) {
    $sql = "SELECT * FROM users WHERE id = ?";
    $stmt = $mysqli->prepare($sql);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $users = $result->fetch_assoc();
}

$company_name = '';
if (isset($company_id)) {
  $sql_company = "SELECT company_name FROM company WHERE company_id = {$company_id}";
  $result_company = $mysqli->query($sql_company);

  if ($result_company && $result_company->num_rows > 0) {
      $company_data = $result_company->fetch_assoc();
      $company_name = $company_data['company_name'];
  }
}

$sql = "SELECT * FROM users WHERE id = ?";
$stmt = $mysqli->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $old_password = $_POST["old_password"];
    $new_password = $_POST["new_password"];
    $confirm_password = $_POST["confirm_password"];

    if (password_verify($old_password, $user["password_hash"])) {
        if ($new_password === $confirm_password) {
            $new_password_hash = password_hash($new_password, PASSWORD_DEFAULT);

            $update_sql = "UPDATE users SET password_hash = ? WHERE id = ?";
            $update_stmt = $mysqli->prepare($update_sql);
            $update_stmt->bind_param("si", $new_password_hash, $user_id);
            if ($update_stmt->execute()) {
                $is_success = true;
            }
        } else {
            $is_invalid = "Lozinke se ne podudaraju!";
        }
    } else {
        $is_invalid = "Stara lozinka je netočna!";
    }
}



?>
<?php include('database.php'); ?>


<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-AU-Combatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="styles/style.css">
  <link rel="stylesheet" href="styles/user.css">
  <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
  <title>Diplomatico</title>
  <link rel="icon" type="image/x-icon" href="photo/company-favicon.png">
  <script defer src="script.js"></script>
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>

<body>


    <!-- Navigation Bar -->
    <header class="navbar">
        <a href="index.php" class="navbar-logo">
            <span>ZukaMaster</span>
        </a>
        <nav class="navbar-links">
            <a href="analyse.php" class="navbar-link">Analiza</a>
            <a href="review.php" class="navbar-link">Promet</a>
            <a href="user.php" class="navbar-link">
                <?= htmlspecialchars($users['name']) ?>
            </a>
        </nav>
    </header>

    <div class="profile-wrapper">
        <div class="profile-form-container">
            <div class="profile-header">
                <h4>Promjena Lozinke</h4>
                <p>Bok, <?= htmlspecialchars($users["name"]) ?></p>
            </div>

            <?php if ($is_success): ?>
                <div class="success-message">
                    <em>Lozinka promjenjena!</em>
                </div>
            <?php elseif ($is_invalid): ?>
                <div class="invalid-message">
                    <em><?= htmlspecialchars($is_invalid) ?></em>
                </div>
            <?php endif; ?>

            <form class="profile-form" method="post">
                <div class="input-box">
                    <input type="password" placeholder="Stara lozinka" name="old_password" id="old_password" required>
                    <i class="bx bxs-lock-alt"></i>
                </div>

                <div class="input-box">
                    <input type="password" placeholder="Nova lozinka" name="new_password" id="new_password" required>
                    <i class="bx bxs-lock-alt"></i>
                </div>

                <div class="input-box">
                    <input type="password" placeholder="Potvrdi novu lozinku" name="confirm_password" id="confirm_password" required>
                    <i class="bx bxs-lock-alt"></i>
                </div>

                <button class="submit-button">Promjeni lozinku</button>
            </form>

            <a href="logout.php" class="logout-button">Odjava</a>
        </div>
    </div>

<footer class="footer">
  <div class="footer-company-name">
      <?= htmlspecialchars($company_name) ?>
  </div>
  <div class="footer-rights">
      All rights reserved 2025
  </div>
</footer>

</body>
</html>