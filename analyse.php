<?php
session_start();

$user_id = (int)$_SESSION['user_id'];
$company_id = (int)$_SESSION['company_id'];

if (isset($_SESSION["user_id"])) {
  $mysqli = require_once __DIR__ . "/database.php";

  $sql = "SELECT * FROM users WHERE id = {$user_id}";
  $result = $mysqli->query($sql);
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

?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="styles/style.css">
  <link rel="stylesheet" href="styles/analyse.css">
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <title>Diplomatico</title>
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

<div class="main-container">
  <div class="date-selection">
    <label for="start-date">Početni datum:</label>
    <input type="date" id="start-date" value="<?php echo date('Y-m-d', strtotime('-7 days')); ?>">

    <label for="end-date">Završni datum:</label>
    <input type="date" id="end-date" value="<?php echo date('Y-m-d'); ?>">
  </div>

  <div class="actions">
    <button id="fetch-items">Dohvati Artikle</button>
    <button id="fetch-categories">Dohvati Kategorije</button>
  </div>

  <div id="results">
    <!-- fetch_items.php ili fetch_categories.php -->
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

<script src="analyse.js"></script>
</body>
</html>