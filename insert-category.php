<?php
// Start session
session_start();
$mysqli = require_once __DIR__ . "/database.php";

// Check if 'user_id' and 'company_id' are set in session
if (!isset($_SESSION['user_id']) || empty($_SESSION['company_id'])) {
    die("Session user_id or company_id is not set or is invalid.");
}

$user_id = (int)$_SESSION['user_id'];
$company_id = (int)$_SESSION['company_id'];

// Check if the form was submitted and category name is provided
if (isset($_POST['add_category']) && !empty($_POST['category_name'])) {

    $category_name = trim($_POST['category_name']);
    $category_type = trim($_POST['category_type']); // Retrieve the category type selected by the user

    // Validate the category type by extracting the type_id from the 'type' table
    $sql_type = "SELECT type_id FROM type WHERE type_name = ? LIMIT 1";
    $stmt_type = $mysqli->prepare($sql_type);
    $stmt_type->bind_param("s", $category_type);
    $stmt_type->execute();
    $stmt_type->bind_result($type_id);
    $stmt_type->fetch();
    $stmt_type->close();



    // If no type_id is found, exit with an error message
    if (empty($type_id)) {
        echo "The selected category type does not exist.";
        exit();
    }

    echo "<script>console.log('Type ID: " . $type_id . "');</script>";


    // Check if the category name already exists for the given company
    $sql_check = "SELECT COUNT(*) FROM category WHERE category_name = ? AND company_id = ?";
    $stmt_check = $mysqli->prepare($sql_check);
    $stmt_check->bind_param("si", $category_name, $company_id);
    $stmt_check->execute();
    $stmt_check->bind_result($count);
    $stmt_check->fetch();
    $stmt_check->close();

    echo "<script>
    console.log('Category Name: " . $category_name . "');
    console.log('Type ID: " . $type_id . "');
    console.log('Company ID: " . $company_id . "');
  </script>";

    // If the category already exists, show a duplicate error message
    if ($count > 0) {
        echo "The category '{$category_name}' already exists for this user and company.";
        header("Location: index.php?message=duplicate");
        exit();
    } else {
        echo "<script>console.log('Type ID: " . $type_id . "');</script>";
        // Insert the new category into the 'category' table along with category_type and type_id
        $query = "INSERT INTO `category` (`category_name`, `type_id`, `company_id`) 
                  VALUES ('$category_name', '$type_id', '$company_id')";
        $result = mysqli_query($mysqli, $query);

        // Check if the query was successful
        if (!$result) {
            die("Query failed: " . mysqli_error($mysqli));
        } else {
            header("Location: index.php?message=success");
            exit();
        }
    }
} else {
    header("Location: index.php?message=failed");
    exit();
}
?>
