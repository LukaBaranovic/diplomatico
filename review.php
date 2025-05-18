<?php
session_start();

$user_id = (int)$_SESSION['user_id'];
$company_id = (int)$_SESSION['company_id'];

if (!isset($_SESSION["user_id"])) {
    die("Unauthorized access.");
}

// Include the database connection
$mysqli = require_once __DIR__ . "/database.php";

// Fetch user data
$users = [];
if (isset($_SESSION["user_id"])) {
    $sql = "SELECT * FROM users WHERE id = ?";
    $stmt = $mysqli->prepare($sql);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $users = $result->fetch_assoc();
}

// Fetch company name
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

// Handle the selected date from the form submission
$selected_date = $_GET['date'] ?? date('Y-m-d'); // Default to today's date if no date is selected

// Query to fetch receipts for the selected date and company
$sql = "SELECT receipt_id, table_number, total_price, timestamp 
        FROM receipts 
        WHERE company_id = ? AND DATE(timestamp) = ?
        ORDER BY timestamp DESC";

$stmt = $mysqli->prepare($sql);
$stmt->bind_param("is", $company_id, $selected_date);
$stmt->execute();
$result = $stmt->get_result();

// Query to calculate the total sum for the selected date
$sql_total = "SELECT SUM(total_price) AS total_sum 
              FROM receipts 
              WHERE company_id = ? AND DATE(timestamp) = ?";
$stmt_total = $mysqli->prepare($sql_total);
$stmt_total->bind_param("is", $company_id, $selected_date);
$stmt_total->execute();
$result_total = $stmt_total->get_result();
$total_sum = $result_total->fetch_assoc()['total_sum'] ?? 0; // Default to 0 if no receipts
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
    <title>Receipts - Diplomatico</title>
    <link rel="icon" type="image/x-icon" href="photo/company-favicon.png">
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

    <div class="container">
        <div class="table-container">
            <h1>Računi</h1>
            <!-- Date Selector -->
            <form method="GET" action="review.php" class="date-picker-container">
                <label for="dateSelector">Odaberite datum:</label>
                <input type="date" id="dateSelector" name="date" value="<?= htmlspecialchars($selected_date) ?>">
                <button type="submit">Prikaži</button>
            </form>

            <!-- Receipts Table -->
            <table id="receiptsTable">
                <thead>
                    <tr>
                        <th>Receipt ID</th>
                        <th>Table Number</th>
                        <th>Total Price</th>
                        <th>Timestamp</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <tr data-receipt-id="<?= htmlspecialchars($row['receipt_id']) ?>">
                            <td><?= htmlspecialchars($row['receipt_id']) ?></td>
                            <td><?= htmlspecialchars($row['table_number']) ?></td>
                            <td><?= htmlspecialchars($row['total_price']) ?></td>
                            <td><?= htmlspecialchars($row['timestamp']) ?></td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>

        <!-- Total Sum Section -->
        <div id="totalSumContainer">
            <h3>Ukupni iznos:</h3>
            <p id="totalSum"><?= number_format($total_sum, 2) ?></p>
        </div>
    </div>

    <!-- Popup for receipt details -->
    <div id="receiptPopup" class="popup">
        <div class="popup-content">
            <!-- Close Button -->
            <span id="popupClose" class="close-btn">&times;</span>

            <!-- ID and Table Number Section -->
            <div class="popup-header">
                <p><strong>ID:</strong> <span class="receipt-id"></span></p>
                <p><strong>Broj Stola:</strong> <span class="table-number"></span></p>
            </div>

            <!-- Items Table -->
            <table class="popup-items">
                <thead>
                    <tr>
                        <th>Artikal</th>
                        <th>Količina</th>
                        <th>Cijena</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- Items will be dynamically inserted here -->
                </tbody>
            </table>
            <p class="popup-total"></p>
            <!-- Bottom Cancel Button -->
            <button id="popupCancel" class="cancel-btn">Otkaži</button>
        </div>
    </div>

    <!-- Footer -->
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