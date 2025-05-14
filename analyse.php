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
<h1>Analyse Data</h1>

<!-- Date Range Inputs -->
<label for="start-date">Start Date:</label>
<input type="date" id="start-date" value="<?php echo date('Y-m-01'); ?>"> <!-- Default: Start of the current month -->

<label for="end-date">End Date:</label>
<input type="date" id="end-date" value="<?php echo date('Y-m-d'); ?>"> <!-- Default: Today -->

<!-- Buttons for Fetching Data -->
<button id="fetch-items">Fetch Items</button>
<button id="fetch-categories">Fetch Categories</button>

<div id="results">
  <!-- Items or Categories will be listed here -->
</div>

<script src="analyse.js"></script>
</body>
</html>