<?php
session_start();

$user_id = (int)$_SESSION['user_id'];
$company_id = (int)$_SESSION['company_id'];

if (!isset($_SESSION["user_id"])) {
    die("Unauthorized access.");
}

// Include the database connection
$mysqli = require_once __DIR__ . "/database.php";

// Check if this is an AJAX request for receipts by date
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['date'])) {
    $selected_date = $_POST['date']; // Get the selected date from AJAX request

    // Query to fetch receipts for the selected date and company
    $sql = "SELECT receipt_id, table_number, total_price, timestamp 
            FROM receipts 
            WHERE company_id = ? AND DATE(timestamp) = ?
            ORDER BY timestamp DESC";

    $stmt = $mysqli->prepare($sql);
    $stmt->bind_param("is", $company_id, $selected_date);
    $stmt->execute();
    $result = $stmt->get_result();

    // Collect data as an array
    $receipts = [];
    while ($row = $result->fetch_assoc()) {
        $receipts[] = $row;
    }

    // Return JSON response
    header('Content-Type: application/json');
    echo json_encode($receipts);
    exit();
}

// Default behavior: fetch today's receipts for initial page load
$today = date('Y-m-d');
$sql = "SELECT receipt_id, table_number, total_price, timestamp 
        FROM receipts 
        WHERE company_id = ? AND DATE(timestamp) = ?
        ORDER BY timestamp DESC";

$stmt = $mysqli->prepare($sql);
$stmt->bind_param("is", $company_id, $today);
$stmt->execute();
$result = $stmt->get_result();

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
    <script defer src="review.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>

<body>
    <div class="container">
        <div class="table-container">
            <h1>Računi</h1>
            <!-- Date Selector -->
            <div class="date-picker-container">
                <label for="dateSelector">Odaberite datum:</label>
                <input type="date" id="dateSelector" value="<?= $today ?>">
            </div>
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
    </div>
</body>

</html>