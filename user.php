<?php

session_start();

// print_r($_SESSION);

$user_id = (int)$_SESSION['user_id'];
$company_id = (int)$_SESSION['company_id'];

if (isset($_SESSION["user_id"])) {
  $mysqli = require_once __DIR__ ."/database.php";
  $sql = "SELECT * FROM users WHERE id = {$_SESSION["user_id"]}";
  $result = $mysqli->query($sql);
  $users = $result->fetch_assoc();
}
//Dohvaća koji user je prijavljen, koji je potreban da znamo za koju firmu user moze raditi promjene.

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
            $is_invalid = "New passwords do not match!";
        }
    } else {
        $is_invalid = "Old password is incorrect!";
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
  <link rel="stylesheet" href="styles/index.css">
  <link rel="stylesheet" href="styles/user.css">
  <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
  <title>Diplomatico</title>
  <link rel="icon" type="image/x-icon" href="photo/company-favicon.png">
  <script defer src="script.js"></script>
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>

<body>

    <div class="profile-wrapper">
        <div class="profile-form-container">
            <div class="profile-header">
                <h4>Promjena Lozinke</h4>
                <p>Hello, <?= htmlspecialchars($users["name"]) ?></p>
            </div>

            <?php if ($is_success): ?>
                <div class="success-message">
                    <em>Password changed successfully!</em>
                </div>
            <?php elseif ($is_invalid): ?>
                <div class="invalid-message">
                    <em><?= htmlspecialchars($is_invalid) ?></em>
                </div>
            <?php endif; ?>

            <form class="profile-form" method="post">
                <div class="input-box">
                    <input type="password" placeholder="Old Password" name="old_password" id="old_password" required>
                    <i class="bx bxs-lock-alt"></i>
                </div>

                <div class="input-box">
                    <input type="password" placeholder="New Password" name="new_password" id="new_password" required>
                    <i class="bx bxs-lock-alt"></i>
                </div>

                <div class="input-box">
                    <input type="password" placeholder="Confirm New Password" name="confirm_password" id="confirm_password" required>
                    <i class="bx bxs-lock-alt"></i>
                </div>

                <button class="submit-button">Change Password</button>
            </form>

            <a href="logout.php" class="logout-button">Logout</a>
        </div>
    </div>



</body>
</html>