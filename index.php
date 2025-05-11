<?php
session_start();

// Fetch user_id and company_id from the session
$user_id = (int)$_SESSION['user_id'];
$company_id = (int)$_SESSION['company_id'];

// Ensure the user is logged in
if (isset($_SESSION["user_id"])) {
    // Include database connection
    $mysqli = require_once __DIR__ . "/database.php";

    // Fetch user details (if needed for other parts of the page)
    $sql = "SELECT * FROM users WHERE id = {$user_id}";
    $result = $mysqli->query($sql);
    $users = $result->fetch_assoc();
}

// Fetch categories and their associated type names filtered by company_id
$sql = "
    SELECT 
        c.category_id, 
        c.category_name, 
        t.type_name 
    FROM 
        CATEGORY c
    JOIN 
        TYPE t 
    ON 
        c.type_id = t.type_id
    WHERE 
        c.company_id = {$company_id}
    ORDER BY 
        c.category_name ASC
";
$result = $mysqli->query($sql);

if (!$result) {
    die("Query error: " . $mysqli->error);
}

$categories = $result->fetch_all(MYSQLI_ASSOC);

// Fetch items and their associated category names filtered by company_id
$sql_items = "
    SELECT 
        i.item_id, 
        i.item_name, 
        i.item_price, 
        c.category_name,
        i.category_id
    FROM 
        ITEM i
    JOIN 
        CATEGORY c 
    ON 
        i.category_id = c.category_id
    WHERE 
        i.company_id = {$company_id}
    ORDER BY 
        i.item_name ASC
";
$result_items = $mysqli->query($sql_items);

if (!$result_items) {
    die("Query error: " . $mysqli->error);
}

$items = $result_items->fetch_all(MYSQLI_ASSOC);

// Fetch all types for the dropdown in the modal
$sql_types = "SELECT type_name FROM TYPE ORDER BY type_name ASC";
$result_types = $mysqli->query($sql_types);
$types = $result_types->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="styles/style.css">
    <link rel="stylesheet" href="styles/index.css">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <title>Diplomatico</title>
    <link rel="icon" type="image/x-icon" href="photo/company-favicon.png">
    <script defer src="script.js"></script> <!-- General script -->
    <script defer src="edit-category.js"></script> <!-- Script for editing categories -->
    <script defer src="edit-item.js"></script> <!-- Script for editing items -->
    <script defer src="delete-category.js"></script> <!-- Script for deleting categories -->
    <script defer src="delete-item.js"></script> <!-- Script for deleting items -->
    <script defer src="add-category.js"></script> <!-- Script for adding categories -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script> <!-- jQuery -->
</head>
<body>
    <div class="container">
        <h1>Category and Item Management</h1>
        <p>Welcome, <?= htmlspecialchars($users['name']) ?>!</p>

        <!-- View Toggle Buttons -->
        <div class="view-toggle">
            <button id="btnCategories" class="toggle-btn active">Prikaz kategorija</button>
            <button id="btnItems" class="toggle-btn">Prikaz Artikala</button>
            <button id="btnAddCategory" class="toggle-btn">Dodaj kategoriju</button>
        </div>

        <!-- Categories Section -->
        <div id="categoriesSection">
            <div class="table-container">
                <input type="text" id="searchBarCategories" placeholder="Search categories..." onkeyup="filterTable('categoryTable', 'searchBarCategories')">
                <table id="categoryTable">
                    <thead>
                        <tr>
                            <th>Category ID</th>
                            <th>Category Name</th>
                            <th>Type Name</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($categories as $category): ?>
                            <tr>
                                <td><?= htmlspecialchars($category['category_id']) ?></td>
                                <td><?= htmlspecialchars($category['category_name']) ?></td>
                                <td><?= htmlspecialchars($category['type_name']) ?></td>
                                <td>
                                    <button class="edit-btn" data-id="<?= $category['category_id'] ?>" data-name="<?= htmlspecialchars($category['category_name']) ?>" data-type="<?= htmlspecialchars($category['type_name']) ?>">Uredi</button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Items Section -->
        <div id="itemsSection" style="display: none;">
            <div class="table-container">
                <input type="text" id="searchBarItems" placeholder="Search items..." onkeyup="filterTable('itemTable', 'searchBarItems')">
                <table id="itemTable">
                    <thead>
                        <tr>
                            <th>Item ID</th>
                            <th>Item Name</th>
                            <th>Item Price</th>
                            <th>Category Name</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($items as $item): ?>
                            <tr>
                                <td><?= htmlspecialchars($item['item_id']) ?></td>
                                <td><?= htmlspecialchars($item['item_name']) ?></td>
                                <td><?= htmlspecialchars($item['item_price']) ?></td>
                                <td data-category-id="<?= $item['category_id'] ?>"><?= htmlspecialchars($item['category_name']) ?></td>
                                <td>
                                    <button class="edit-btn" data-id="<?= $item['item_id'] ?>" data-name="<?= htmlspecialchars($item['item_name']) ?>" data-price="<?= htmlspecialchars($item['item_price']) ?>" data-category-id="<?= $item['category_id'] ?>">Uredi</button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Edit Category Modal -->
        <div id="editCategoryModal" class="modal" style="display: none;">
            <div class="modal-content">
                <span class="close-btn">&times;</span>
                <h2>Edit Category</h2>
                <form id="editCategoryForm">
                    <input type="hidden" id="categoryId" name="category_id">
                    <div class="form-group">
                        <label for="categoryName">Category Name:</label>
                        <input type="text" id="categoryName" name="category_name" required>
                    </div>
                    <div class="form-group">
                        <label for="typeName">Type:</label>
                        <select id="typeName" name="type_name" required>
                            <?php foreach ($types as $type): ?>
                                <option value="<?= htmlspecialchars($type['type_name']) ?>"><?= htmlspecialchars($type['type_name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div id="errorMessage" style="color: red; display: none;"></div>
                    <button type="button" class="confirm-btn">Confirm</button>
                    <button type="button" class="delete-btn">Delete</button>
                    <button type="button" class="cancel-btn">Cancel</button>
                </form>
            </div>
        </div>

        <!-- Edit Item Modal -->
        <div id="editItemModal" class="modal" style="display: none;">
            <div class="modal-content">
                <span class="close-btn">&times;</span>
                <h2>Edit Item</h2>
                <form id="editItemForm">
                    <input type="hidden" id="itemId" name="item_id">
                    <div class="form-group">
                        <label for="itemName">Item Name:</label>
                        <input type="text" id="itemName" name="item_name" required>
                    </div>
                    <div class="form-group">
                        <label for="itemPrice">Item Price:</label>
                        <input type="number" id="itemPrice" name="item_price" step="0.1" min="0.1" required>
                    </div>
                    <div class="form-group">
                        <label for="itemCategory">Category:</label>
                        <select id="itemCategory" name="category_id" required>
                            <?php foreach ($categories as $category): ?>
                                <option value="<?= $category['category_id'] ?>"><?= htmlspecialchars($category['category_name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div id="itemErrorMessage" style="color: red; display: none;"></div>
                    <button type="button" class="confirm-btn">Confirm</button>
                    <button type="button" class="delete-btn">Delete</button>
                    <button type="button" class="cancel-btn">Cancel</button>
                </form>
            </div>
        </div>

        <!-- Add Category Modal -->
        <div id="addCategoryModal" class="modal" style="display: none;">
            <div class="modal-content">
                <span class="close-btn">&times;</span>
                <h2>Dodaj Kategoriju</h2>
                <form id="addCategoryForm">
                    <div class="form-group">
                        <label for="newCategoryName">Category Name:</label>
                        <input type="text" id="newCategoryName" name="category_name" required>
                    </div>
                    <div class="form-group">
                        <label for="newType">Type:</label>
                        <select id="newType" name="type_id" required>
                            <?php foreach ($types as $type): ?>
                                <option value="<?= htmlspecialchars($type['type_name']) ?>"><?= htmlspecialchars($type['type_name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div id="addCategoryErrorMessage" style="color: red; display: none;"></div>
                    <div id="addCategorySuccessMessage" style="color: green; display: none;"></div>
                    <button type="button" class="confirm-btn">Add Category</button>
                    <button type="button" class="cancel-btn">Cancel</button>
                </form>
            </div>
        </div>
    </div>
</body>
</html>