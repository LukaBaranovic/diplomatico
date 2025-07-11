<?php

session_start();
$user_id = (int)$_SESSION['user_id'];
$company_id = (int)$_SESSION['company_id'];

if (isset($_SESSION["user_id"])) {
    $mysqli = require_once __DIR__ . "/database.php";

    $sql = "SELECT * FROM users WHERE id = {$user_id}";
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
    <script defer src="script.js"></script> 
    <script defer src="edit-category.js"></script> 
    <script defer src="edit-item.js"></script> 
    <script defer src="delete-category.js"></script> 
    <script defer src="delete-item.js"></script> 
    <script defer src="add-category.js"></script> 
    <script defer src="add-item.js"></script> 
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script> 
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

        <div class="view-toggle">
            <div class="view-toggle-row">
                <button id="btnCategories" class="toggle-btn active">Prikaz kategorija</button>
                <button id="btnItems" class="toggle-btn">Prikaz Artikala</button>
            </div>
            <div class="view-toggle-row">
                <button id="btnAddCategory" class="toggle-btn">Dodaj kategoriju</button>
                <button id="btnAddItem" class="toggle-btn">Dodaj artikal</button>
            </div>
        </div>
        
        <div id="categoriesSection">
            <div class="table-container">
                <input type="text" id="searchBarCategories" placeholder="Pretraži kategorije..." onkeyup="filterTable('categoryTable', 'searchBarCategories')">
                <table id="categoryTable">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Kategorija</th>
                            <th>Tip</th>
                            <th></th>
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

        <div id="itemsSection" style="display: none;">
            <div class="table-container">
                <input type="text" id="searchBarItems" placeholder="Pretraži artikle..." onkeyup="filterTable('itemTable', 'searchBarItems')">
                <table id="itemTable">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Artikal</th>
                            <th>Cijena</th>
                            <th>Kategorija</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($items as $item): ?>
                            <tr>
                                <td><?= htmlspecialchars($item['item_id']) ?></td>
                                <td><?= htmlspecialchars($item['item_name']) ?></td>
                                <td><?= number_format((float)$item['item_price'], 2, '.', '') ?> €</td>
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

        <div id="editCategoryModal" class="modal" style="display: none;">
            <div class="modal-content">
                <button class="close-btn" type="button">&times;</button>
                <div class="modal-header-title">Uredi kategoriju</div>
                <hr class="modal-header-divider" />
                <form id="editCategoryForm">
                    <div class="modal-fields">
                        <input type="hidden" id="categoryId" name="category_id">
                        <div class="form-group">
                            <label for="categoryName">Naziv kategorije:</label>
                            <input type="text" id="categoryName" name="category_name" required>
                        </div>
                        <div class="form-group">
                            <label for="typeName">Tip:</label>
                            <select id="typeName" name="type_name" required>
                                <?php foreach ($types as $type): ?>
                                    <option value="<?= htmlspecialchars($type['type_name']) ?>"><?= htmlspecialchars($type['type_name']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div id="errorMessage" style="display: none;"></div>
                        <div id="successMessage" style="display: none;"></div>
                    </div>
                    <hr class="modal-header-divider modal-fields-divider" />
                    <div class="modal-buttons">
                        <button type="button" id="confirmEdit" class="confirm-btn">Potvrdi</button>
                        <button type="button" id="deleteCategoryBtn" class="delete-btn">Izbriši</button>
                        <button type="button" class="cancel-btn">Otkaži</button>
                    </div>
                </form>
            </div>
        </div>

        <div id="editItemModal" class="modal" style="display: none;">
            <div class="modal-content">
                <button class="close-btn" type="button">&times;</button>
                <div class="modal-header-title">Uredi artikal</div>
                <hr class="modal-header-divider" />
                <form id="editItemForm">
                    <div class="modal-fields">
                        <input type="hidden" id="itemId" name="item_id">
                        <div class="form-group">
                            <label for="itemName">Naziv artikla:</label>
                            <input type="text" id="itemName" name="item_name" required>
                        </div>
                        <div class="form-group">
                            <label for="itemPrice">Cijena artikla:</label>
                            <input type="number" id="itemPrice" name="item_price" step="0.1" min="0.1" required>
                        </div>
                        <div class="form-group">
                            <label for="itemCategory">Kategorija:</label>
                            <select id="itemCategory" name="category_id" required>
                                <?php foreach ($categories as $category): ?>
                                    <option value="<?= $category['category_id'] ?>"><?= htmlspecialchars($category['category_name']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div id="itemErrorMessage" style="display: none;"></div>
                        <div id="itemSuccessMessage" style="display: none;"></div>
                    </div>
                    <hr class="modal-header-divider modal-fields-divider" />
                    <div class="modal-buttons">
                        <button type="button" id="confirmEditItem" class="confirm-btn">Potvrdi</button>
                        <button type="button" id="deleteItemBtn" class="delete-btn">Izbriši</button>
                        <button type="button" class="cancel-btn">Otkaži</button>
                    </div>
                </form>
            </div>
        </div>

        <div id="addCategoryModal" class="modal" style="display: none;">
            <div class="modal-content">
                <button class="close-btn" type="button">&times;</button>
                <div class="modal-header-title">Dodaj kategoriju</div>
                <hr class="modal-header-divider" />
                <form id="addCategoryForm">
                    <div class="modal-fields">
                        <div class="form-group">
                            <label for="newCategoryName">Naziv kategorije:</label>
                            <input type="text" id="newCategoryName" name="category_name" required>
                        </div>
                        <div class="form-group">
                            <label for="newType">Tip:</label>
                            <select id="newType" name="type_id" required>
                                <?php foreach ($types as $type): ?>
                                    <option value="<?= htmlspecialchars($type['type_name']) ?>"><?= htmlspecialchars($type['type_name']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div id="addCategoryErrorMessage" style="display: none;"></div>
                        <div id="addCategorySuccessMessage" style="display: none;"></div>
                    </div>
                    <hr class="modal-header-divider modal-fields-divider" />
                    <div class="modal-buttons">
                        <button type="button" id="confirmAddCategory" class="confirm-btn">Dodaj kategoriju</button>
                        <button type="button" class="cancel-btn">Otkaži</button>
                    </div>
                </form>
            </div>
        </div>

        <div id="addItemModal" class="modal" style="display: none;">
            <div class="modal-content">
                <button class="close-btn" type="button">&times;</button>
                <div class="modal-header-title">Dodaj artikal</div>
                <hr class="modal-header-divider" />
                <form id="addItemForm">
                    <div class="modal-fields">
                        <div class="form-group">
                            <label for="itemName">Naziv artikla:</label>
                            <input type="text" id="itemName" name="item_name" required>
                        </div>
                        <div class="form-group">
                            <label for="itemPrice">Cijena artikla:</label>
                            <input type="number" id="itemPrice" name="item_price" step="0.1" min="0.1" required>
                        </div>
                        <div class="form-group">
                            <label for="itemCategory">Kategorija:</label>
                            <select id="itemCategory" name="category_id" required>
                                <?php foreach ($categories as $category): ?>
                                    <option value="<?= $category['category_id'] ?>"><?= htmlspecialchars($category['category_name']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div id="addItemErrorMessage" style="display: none;"></div>
                        <div id="addItemSuccessMessage" style="display: none;"></div>
                    </div>
                    <hr class="modal-header-divider modal-fields-divider" />
                    <div class="modal-buttons">
                        <button type="submit" class="confirm-btn">Dodaj artikal</button>
                        <button type="button" class="cancel-btn">Otkaži</button>
                    </div>
                </form>
            </div>
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
</body>
</html>