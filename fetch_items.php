<?php
session_start();

if (!isset($_SESSION['company_id'])) {
  echo "Company ID is not set.";
  exit;
}

$company_id = (int)$_SESSION['company_id'];
$start_date = $_POST['start_date'] ?? date('Y-m-d', strtotime('-7 days'));
$end_date = $_POST['end_date'] ?? date('Y-m-d');

$mysqli = require_once __DIR__ . "/database.php";

$sql = "SELECT ri.item_name, SUM(ri.quantity) AS total_quantity, SUM(ri.total_price) AS total_price
        FROM receipt_items ri
        JOIN receipts r ON ri.receipt_id = r.receipt_id
        WHERE r.company_id = ? AND DATE(r.timestamp) BETWEEN ? AND ?
        GROUP BY ri.item_name";
$stmt = $mysqli->prepare($sql);
$stmt->bind_param("iss", $company_id, $start_date, $end_date);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
  echo "<table>";
  echo "<tr><th>Item Name</th><th>Total Quantity</th><th>Total Price</th></tr>";
  while ($row = $result->fetch_assoc()) {
    echo "<tr><td>{$row['item_name']}</td><td>{$row['total_quantity']}</td><td>{$row['total_price']}</td></tr>";
  }
  echo "</table>";
} else {
  echo "No items found for the selected date range.";
}