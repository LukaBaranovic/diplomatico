<?php
session_start();

// Enable error reporting to debug errors
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Check if company_id is set in the session
if (!isset($_SESSION['company_id'])) {
    echo "Company ID is not set.";
    exit;
}

// Retrieve company_id and date range
$company_id = (int)$_SESSION['company_id'];
$start_date = $_POST['start_date'] ?? date('Y-m-01'); // Default: Start of the current month
$end_date = $_POST['end_date'] ?? date('Y-m-d');      // Default: Today

// Connect to the database
$mysqli = require_once __DIR__ . "/database.php";

// SQL Query to Fetch Categories and Aggregate Item Quantities
$sql = "
    SELECT 
        c.category_name, 
        SUM(ri.quantity) AS total_quantity
    FROM 
        receipt_items ri
    INNER JOIN 
        receipts r ON ri.receipt_id = r.receipt_id
    INNER JOIN 
        item i ON ri.item_name = i.item_name AND i.company_id = r.company_id
    INNER JOIN 
        category c ON i.category_id = c.category_id AND c.company_id = r.company_id
    WHERE 
        r.company_id = ? AND DATE(r.timestamp) BETWEEN ? AND ?
    GROUP BY 
        c.category_name
";

// Prepare and execute the query
$stmt = $mysqli->prepare($sql);

if (!$stmt) {
    echo "SQL error: " . $mysqli->error;
    exit;
}

$stmt->bind_param("iss", $company_id, $start_date, $end_date);
$stmt->execute();
$result = $stmt->get_result();

// Display the results
if ($result->num_rows > 0) {
    echo "<table>";
    echo "<tr><th>Category Name</th><th>Total Quantity</th></tr>";
    while ($row = $result->fetch_assoc()) {
        $category_name = $row['category_name'] ?? 'Uncategorized';
        $total_quantity = $row['total_quantity'] ?? 0;
        echo "<tr><td>{$category_name}</td><td>{$total_quantity}</td></tr>";
    }
    echo "</table>";
} else {
    echo "No categories found for the selected date range.";
}