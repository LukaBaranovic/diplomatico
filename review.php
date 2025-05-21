<?php
session_start();

$user_id = (int)$_SESSION['user_id'];
$company_id = (int)$_SESSION['company_id'];

if (!isset($_SESSION["user_id"])) {
    die("Neautiriziran pristup!");
}

$mysqli = require_once __DIR__ . "/database.php";

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
    $sql_company = "SELECT company_name FROM company WHERE company_id = ?";
    $stmt_company = $mysqli->prepare($sql_company);
    $stmt_company->bind_param("i", $company_id);
    $stmt_company->execute();
    $result_company = $stmt_company->get_result();

    if ($result_company && $result_company->num_rows > 0) {
        $company_data = $result_company->fetch_assoc();
        $company_name = $company_data['company_name'];
    }
}

$selected_date = $_GET['date'] ?? date('Y-m-d'); 

$sql = "SELECT receipt_id, table_number, total_price, timestamp 
        FROM receipts 
        WHERE company_id = ? AND DATE(timestamp) = ?
        ORDER BY timestamp DESC";

$stmt = $mysqli->prepare($sql);
$stmt->bind_param("is", $company_id, $selected_date);
$stmt->execute();
$result = $stmt->get_result();

$sql_total = "SELECT SUM(total_price) AS total_sum 
              FROM receipts 
              WHERE company_id = ? AND DATE(timestamp) = ?";
$stmt_total = $mysqli->prepare($sql_total);
$stmt_total->bind_param("is", $company_id, $selected_date);
$stmt_total->execute();
$result_total = $stmt_total->get_result();
$total_sum = $result_total->fetch_assoc()['total_sum'] ?? 0; 
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
    <title>Diplomatico - Promet</title>
    <link rel="icon" type="image/x-icon" href="photo/company-favicon.png">
</head>

<body>
  
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

    <div class="container">
        <div class="table-container">
            <h1>Računi</h1>
          
            <form method="GET" action="review.php" class="date-picker-container">
                <label for="dateSelector">Odaberite datum:</label>
                <input type="date" id="dateSelector" name="date" value="<?= htmlspecialchars($selected_date) ?>">
                <button class="show-button" type="submit">Prikaži</button>
            </form>

            <table id="receiptsTable">
                <thead>
                    <tr>
                        <th>ID Računa</th>
                        <th>Broj Stola</th>
                        <th>Ukupno</th>
                        <th>Vrijeme</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <tr data-receipt-id="<?= htmlspecialchars($row['receipt_id']) ?>">
                            <td><?= htmlspecialchars($row['receipt_id']) ?></td>
                            <td><?= htmlspecialchars($row['table_number']) ?></td>
                            <td><?= htmlspecialchars($row['total_price']) ?> €</td>
                            <td><?= htmlspecialchars(date('H:i', strtotime($row['timestamp']))) ?></td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>

        <div id="totalSumContainer">
            <h3>Ukupni iznos:</h3>
            <p id="totalSum"><?= number_format($total_sum, 2) ?> €</p>
        </div>
    </div>

    <div id="receiptPopup" class="popup">
        <div class="popup-content">
           
            <span id="popupClose" class="close-btn">&times;</span>

            <div class="popup-header"></div>

            <table class="popup-items">
                <thead>
                    <tr>
                        <th>Artikal</th>
                        <th>Količina</th>
                        <th>Cijena</th>
                    </tr>
                </thead>
                <tbody>
                    <!--  -->
                </tbody>
            </table>
            <p class="popup-total"></p>
         
            <button id="popupCancel" class="cancel-btn">Otkaži</button>
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

    <script src="review.js" defer></script>
</body>

</html>