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

// Retrieve company_id, date range, and sorting order
$company_id = (int)$_SESSION['company_id'];
$start_date = $_POST['start_date'] ?? date('Y-m-01'); // Default: Start of the current month
$end_date = $_POST['end_date'] ?? date('Y-m-d');      // Default: Today
$sort_order = strtoupper($_POST['sort_order'] ?? 'DESC'); // Default to DESC

// Validate sort order (only ASC or DESC are allowed)
if (!in_array($sort_order, ['ASC', 'DESC'])) {
    $sort_order = 'DESC';
}

// Connect to the database
$mysqli = require_once __DIR__ . "/database.php";

// SQL Query to Fetch Categories, Total Quantity, and Total Price
$sql = "
    SELECT 
        c.category_name, 
        SUM(ri.quantity) AS total_quantity,
        SUM(ri.total_price) AS total_price
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
    ORDER BY 
        total_quantity $sort_order
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

// Display the results in table format
if ($result->num_rows > 0) {
    echo '<div class="table-container">';
    echo '<table>';
    echo '<thead>';
    echo '<tr>';
    echo '<th>Kategorija</th>';
    echo '<th>
            Ukupna Količina
            <label for="category-sort-order" style="margin-left: 10px;">Sort:</label>
            <select id="category-sort-order" onchange="updateCategorySortOrder()">
                <option value="DESC"' . ($sort_order === 'DESC' ? ' selected' : '') . '>Descending</option>
                <option value="ASC"' . ($sort_order === 'ASC' ? ' selected' : '') . '>Ascending</option>
            </select>
          </th>';
    echo '<th>Ukupna Cijena</th>'; // Total Price Column
    echo '</tr>';
    echo '</thead>';
    echo '<tbody>';
    while ($row = $result->fetch_assoc()) {
        echo '<tr>';
        echo '<td>' . htmlspecialchars($row['category_name']) . '</td>';
        echo '<td>' . htmlspecialchars($row['total_quantity']) . '</td>';
        echo '<td>' . htmlspecialchars(number_format($row['total_price'], 2)) . '</td>'; // Format total price
        echo '</tr>';
    }
    echo '</tbody>';
    echo '</table>';
    echo '</div>';
} else {
    echo "No categories found for the selected date range.";
}