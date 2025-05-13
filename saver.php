<?php

session_start();

// Existing code from the original review.php
$user_id = (int)$_SESSION['user_id'];
$company_id = (int)$_SESSION['company_id'];

if (isset($_SESSION["user_id"])) {
  $mysqli = require_once __DIR__ . "/database.php";
  $sql = "SELECT * FROM users WHERE id = {$_SESSION["user_id"]}";
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

// New code for fetching receipts
$selected_date = isset($_GET['date']) ? $_GET['date'] : date('Y-m-d');

$sql_receipts = "
    SELECT receipt_id, table_number, total_price, timestamp 
    FROM receipts 
    WHERE DATE(timestamp) = '{$selected_date}' 
      AND company_id = {$company_id}
    ORDER BY timestamp DESC
";
$result_receipts = $mysqli->query($sql_receipts);

$receipts = [];
if ($result_receipts) {
    $receipts = $result_receipts->fetch_all(MYSQLI_ASSOC);
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="styles/style.css">
  <link rel="stylesheet" href="styles/review.css">
  <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
  <title>Pregled Računa</title>
</head>
<body>
  <header class="navbar">
    <a href="index.php" class="navbar-logo">
      <span>Diplomatico</span>
    </a>
    <a href="#" class="navbar-user"><?= htmlspecialchars($users['name'] ?? 'Guest') ?></a>
  </header>

  <div class="container">
    <h1>Pregled Računa</h1>
    
    <!-- Date Picker -->
    <form method="GET" action="review.php" class="date-picker-form">
      <label for="date">Odaberi Datum:</label>
      <input type="date" id="date" name="date" value="<?= htmlspecialchars($selected_date) ?>">
      <button type="submit">Prikaži</button>
    </form>

    <!-- Receipts Table -->
    <div class="table-container">
      <table>
        <thead>
          <tr>
            <th>Broj Računa</th>
            <th>Broj Stola</th>
            <th>Ukupna Cijena</th>
            <th>Vrijeme</th>
            <th>Pregled</th>
          </tr>
        </thead>
        <tbody>
          <?php if (!empty($receipts)): ?>
            <?php foreach ($receipts as $receipt): ?>
              <tr>
                <td><?= htmlspecialchars($receipt['receipt_id']) ?></td>
                <td><?= htmlspecialchars($receipt['table_number']) ?></td>
                <td><?= htmlspecialchars($receipt['total_price']) ?> kn</td>
                <td><?= htmlspecialchars($receipt['timestamp']) ?></td>
                <td>
                  <button class="view-btn">Pregled</button>
                </td>
              </tr>
            <?php endforeach; ?>
          <?php else: ?>
            <tr>
              <td colspan="5">Nema računa za odabrani datum.</td>
            </tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>

  <footer class="footer">
    <div class="footer-company-name"><?= htmlspecialchars($company_name) ?></div>
    <div class="footer-rights">Sva prava pridržana 2025</div>
  </footer>

</body>
</html>