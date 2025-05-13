<?php
session_start();

$user_id = (int)$_SESSION['user_id'];
$company_id = (int)$_SESSION['company_id'];

if (!isset($_SESSION["user_id"])) {
    die("Unauthorized access.");
}

// Include the database connection
$mysqli = require_once __DIR__ . "/database.php";

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
        <a href="review.php" class="navbar-logo">
            <span>ZukaMaster</span>
        </a>
        <a href="http://localhost/diplomatico/user.php" class="navbar-user">
            <?= htmlspecialchars($users['name']) ?>
        </a>
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
</body>

</html>