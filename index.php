<?php

session_start();

print_r($_SESSION);

$user_id = (int)$_SESSION['user_id'];
$company_id = (int)$_SESSION['company_id'];

if (isset($_SESSION["user_id"])) {
  $mysqli = require_once __DIR__ ."/database.php";
  $sql = "SELECT * FROM users WHERE id = {$_SESSION["user_id"]}";
  $result = $mysqli->query($sql);
  $users = $result->fetch_assoc();
}
//Dohvaća koji user je prijavljen, koji je potreban da znamo za koju firmu user moze raditi promjene.
?>
<?php include('database.php'); ?>


<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-AU-Combatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="styles/style.css">
  <link rel="stylesheet" href="styles/index.css">
  <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
  <title>Diplomatico</title>
  <link rel="icon" type="image/x-icon" href="photo/company-favicon.png">
  <script defer src="script.js"></script>
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>

<body>
  
  <button type="button" class="button" data-modal-target="#add-category-modal">Dodaj novu kategoriju</button> <!-- new category button -->
  <button type="button" class="button" data-modal-target="#insert-item-modal">Dodaj novi artikal</button> <!-- new category button -->
  

  <table>
    <thead>
      <tr>
        <th>ID</th>
        <th>Kategorija</th>
        <th>Uredi</th>
        <th>Izbriši</th>
      </tr>
    </thead>
    
    <tbody>
      <?php
      $displayCategoryQuery = "SELECT * FROM `category` WHERE `company_id` = $company_id";
      $displayCategoryResult = mysqli_query($mysqli, $displayCategoryQuery);

      if(!$displayCategoryResult){
        die("Query failed".mysqli_error($mysqli));
      }
      else {
        while($row = mysqli_fetch_assoc($displayCategoryResult)){
          ?>
        <tr>
          <td><?php echo $row['category_id'];  ?></td>
          <td><?php echo $row['category_name'];  ?></td>
          <td><button type="button" class="button update-button edit-category" data-modal-target="#edit-category-modal">Uredi</button></td>
          <td><button type="button" class="button delete-button delete-category" data-modal-target="#delete-category-modal">Izbriši</button></td>
        </tr>
          <?php
        }
      }
      ?>
    </tbody>
  </table>

<!-- #################################################################################################################################-->
  <hr>


  <table>
    <thead>
      <tr>
        <th>ID</th>
        <th>Artikal</th>
        <th>Cijena</th>
        <th>Kategorija</th>
        <th>Uredi</th>
        <th>Izbriši</th>
      </tr>
    </thead>
    
    <tbody>
      <?php
      $displayItemQuery = "SELECT * FROM item
      JOIN category ON item.category_id = category.category_id";
      $displayItemResult = mysqli_query($mysqli, $displayItemQuery);

      if(!$displayItemResult){
        die("Query failed".mysqli_error($mysqli));
      }
      else {
        while($row = mysqli_fetch_assoc($displayItemResult)){
          ?>
        <tr>
          <td><?php echo $row['item_id'];  ?></td>
          <td><?php echo $row['item_name'];  ?></td>
          <td><?php echo $row['item_price'], '€';  ?></td>
          <td><?php echo $row['category_name'] ?></td>
          <td><button type="button" class="button update-button edit-item" data-modal-target="#edit-item-modal">Uredi</button></td>
          <td><button type="button" class="button delete-button delete-item" data-modal-target="#delete-item-modal">Izbriši</button></td>
        </tr>
          <?php
        }
      }
      ?>
    </tbody>
  </table>
  
<!-- #################################################################################################################################-->
  <hr>


  <div class="datatable-container">
    <div class="category-container">
      

      <div class="category-container-header">
        <p> ID: 55 </p>
        <p> Kategorija: Topli napitci</p>
      </div>

      <div class="category-container-body">
        <p> zuka </p>
      </div>



    </div>
  </div>















<!-- #################################################################################################################################-->

<!-- Početak obrasca koji koristimo za dodavanje novih kategorija -->

<form action="insert-category.php" method="post">
  <div id="overlay"></div>
  <div>
    <div class="modal" id="add-category-modal">

      <div class="modal-header">
        <div class="title">Unesi novu kategoriju</div>
        <button type="button" data-close-button class="close-button">&times;</button>  <!-- cancel 'x' button -->
      </div>

      <div class="modal-body">
        <div class="input-box">
          <input type="text" name="category_name" placeholder="Nova kategorija">
        </div>
      </div>

      <div class="modal-footer">
        <input type="submit" class="button save-button" name="add_category" value="Spremi">    <!-- save button -->
        <button type="button" data-close-button class="button cancel-button">Zatvori</button>  <!-- cancel button -->
      </div>

    </div>
  </div>
</form>

<!-- Kraj obrasca koji koristimo za dodavanje novih kategorija -->

<!-- #################################################################################################################################-->

<!-- Početak obrasca koji koristimo za uređivanje kategorija -->

<form action="edit-category.php" method="post">
  <div id="overlay"></div>
  <div>
    <div class="modal" id="edit-category-modal">

      <input type="hidden" id="edit_category_id_fetched" name="category_id">  <!-- ovisno o ID-u ćemo mjenjati naziv kategorije -->

      <div class="modal-header">
        <div class="title">Uredi naziv kategorije</div>
        <button type="button" data-close-button class="close-button">&times;</button>  <!-- cancel 'x' button -->
      </div>

      <div class="modal-body">
        <div class="input-box">
          <input type="text" id="edit_category_name_fetched" name="category_name" placeholder="Izmjeni kategoriju">  
        </div>
      </div>

      <div class="modal-footer">
        <input type="submit" class="button save-button" name="edit_category" value="Spremi">    <!-- save button -->
        <button type="button" data-close-button class="button cancel-button">Zatvori</button>  <!-- cancel button -->
      </div>

    </div>
  </div>
</form>

<!-- Kraj obrasca koji koristimo za uređivanje kategorija -->

<!-- #################################################################################################################################-->

<!-- Početak obrasca koji koristimo za brisanje kategorije -->

<form action="delete-category.php" method="post">
  <div id="overlay"></div>
  <div>
    <div class="modal" id="delete-category-modal">

      <input type="hidden" id="delete_category_id_fetched" name="category_id">  <!-- ovisno o ID-u ćemo mjenjati brisati kategoriju -->

      <div class="modal-header">
        <div class="title">Jeste li sigurni da želite izbrisati:</div>
        <button type="button" data-close-button class="close-button">&times;</button>  <!-- cancel 'x' button -->
      </div>

      <div class="modal-body" style="padding-top: 30px;">
        <h5 id="delete_category_name_fetched"></h5>
      </div>

      <div class="modal-footer">
        <input type="submit" class="button save-button" name="delete_category_name" value="Da, izbriši">    <!-- save button -->
        <button type="button" data-close-button class="button cancel-button">Zatvori</button>  <!-- cancel button -->
      </div>

    </div>
  </div>
</form>

<!-- Kraj obrasca koji koristimo za brisanje kategorije -->

<!-- #################################################################################################################################-->

<!-- Početak obrasca koji koristimo za dodavanje artikla -->

<form action="insert-item.php" method="post">
  <div id="overlay"></div>
  <div>
    <div class="modal" id="insert-item-modal">

      <div class="modal-header">
        <div class="title">Dodaj novi artikal</div>
        <button type="button" data-close-button class="close-button">&times;</button>  <!-- cancel 'x' button -->
      </div>

      <div class="modal-body">

        <div class="input-box">
          <input type="text" name="item_name" placeholder="Novi artikl">
        </div>

        <div class="input-box">
          <input type="number" name="item_price" placeholder="Cijena artikla" step="0.01" min="0">
        </div>

        <div class="input-box">
          <div class="dropdown-selector">
            <input type="text" name="category_name" class="text-box" placeholder="Odaberi kategoriju" readonly>
            <div class="category-option">

              <?php
                $displayCategoryQuery = "SELECT * FROM category WHERE company_id = $company_id";
                $displayCategoryResult = mysqli_query($mysqli, $displayCategoryQuery);

                if(!$displayCategoryResult){
                  die("Query failed".mysqli_error($mysqli));
                }
                else {
                  while($row = mysqli_fetch_assoc($displayCategoryResult)){
                    ?>
                  <div onclick="showCategory('<?php echo $row['category_name'];  ?>')"><?php echo $row['category_name'];  ?></div>
                    <?php
                  }
                }
                ?>
            </div>
          </div>
        </div>

      </div>

      <div class="modal-footer">
        <input type="submit" class="button save-button" name="add_item" value="Spremi">    <!-- save button -->
        <button type="button" data-close-button class="button cancel-button">Zatvori</button>  <!-- cancel button -->
      </div>

    </div>
  </div>
</form>

<!-- Kraj obrasca koji koristimo za dodavanje artikla -->

<!-- #################################################################################################################################-->

<!-- Početak obrasca koji koristimo za uređivanje artikla -->

<form action="edit-item.php" method="post">
  <div id="overlay"></div>
  <div>
    <div class="modal" id="edit-item-modal">

      <input type="hidden" id="edit_item_id_fetched" name="item_id">  <!-- ovisno o ID-u ćemo mjenjati naziv artikla, cijenu i kategoriju -->

      <div class="modal-header">
        <div class="title">Uredi artikal</div>
        <button type="button" data-close-button class="close-button">&times;</button>  <!-- cancel 'x' button -->
      </div>

      <div class="modal-body">

        <div class="input-box">
          <input type="text" id="edit_item_name_fetched" name="item_name" placeholder="Izmjeni artikal">  
        </div>

        <div class="input-box">
          <input type="number" id="edit_item_price_fetched"  name="item_price" placeholder="Cijena artikla" step="0.01" min="0">
        </div>

        <div class="input-box">
          <div class="dropdown-selector" >
            <input type="text" id="edit_item_category_fetched" name="category_name" class="text-box" placeholder="Odaberi kategoriju" readonly>
            <div class="category-option">

              <?php
                $displayCategoryQuery = "SELECT * FROM category WHERE company_id = $company_id";
                $displayCategoryResult = mysqli_query($mysqli, $displayCategoryQuery);

                if(!$displayCategoryResult){
                  die("Query failed".mysqli_error($mysqli));
                }
                else {
                  while($row = mysqli_fetch_assoc($displayCategoryResult)){
                    ?>
                  <div onclick="showCategory('<?php echo $row['category_name'];  ?>')"><?php echo $row['category_name'];  ?></div>
                    <?php
                  }
                }
                ?>
            </div>
          </div>
        </div>
      </div>

      <div class="modal-footer">
        <input type="submit" class="button save-button" name="edit_item" value="Spremi">    <!-- save button -->
        <button type="button" data-close-button class="button cancel-button">Zatvori</button>  <!-- cancel button -->
      </div>

    </div>
  </div>
</form>

<!-- Kraj obrasca koji koristimo za uređivanje artikla -->

<!-- #################################################################################################################################-->

<!-- Početak obrasca koji koristimo za brisanje artikla -->

<form action="delete-item.php" method="post">
    <div id="overlay"></div>
    <div>
      <div class="modal" id="delete-item-modal">

        <input type="hidden" id="delete_item_id_fetched" name="item_id">  <!-- ovisno o ID-u ćemo mjenjati brisati artikal -->

        <div class="modal-header">
          <div class="title">Jeste li sigurni da želite izbrisati:</div>
          <button type="button" data-close-button class="close-button">&times;</button>  <!-- cancel 'x' button -->
        </div>

        <div class="modal-body">

          <div class="input-box">
            <h5 id="delete_item_name_fetched"></h5>
          </div>

        </div>

        <div class="modal-footer">
          <input type="submit" class="button save-button" name="delete_item_name" value="Da, izbriši">    <!-- save button -->
          <button type="button" data-close-button class="button cancel-button">Zatvori</button>  <!-- cancel button -->
        </div>

      </div>
    </div>
</form>

<!-- Kraj obrasca koji koristimo za brisanje artikla -->




<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
</body>
</html>