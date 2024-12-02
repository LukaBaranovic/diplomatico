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
    
  <button type="button" class="button" data-modal-target="#add-modal">Dodaj novu kategoriju</button> <!-- new category button -->

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
      $query = "SELECT * FROM category WHERE company_id = $company_id";

      $result = mysqli_query($mysqli, $query);

      if(!$result){
        die("Query failed".mysqli_error($mysqli));
      }
      else {
        while($row = mysqli_fetch_assoc($result)){
          ?>
        <tr>
          <td><?php echo $row['category_id'];  ?></td>
          <td><?php echo $row['category_name'];  ?></td>
          <td><button type="button" class="button update-button" data-modal-target="#edit-modal">Uredi</button></td>
          <td><button type="button" class="button delete-button">Izbriši</button></td>
        </tr>
          <?php
        }
      }
      ?>
    </tbody>

  </table>





<!-- #################################################################################################################################-->

<!-- Početak obrasca koji koristimo za dodavanje novih kategorija -->

  <form action="insert-category.php" method="post">
    <div id="overlay"></div>
    <div>
      <div class="modal" id="add-modal">

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
      <div class="modal" id="edit-modal">

        <input type="hidden" id="category_id_fetched" name="update_id">  <!-- ovisno o ID-u ćemo mjenjati naziv kategorije -->

        <div class="modal-header">
          <div class="title">Uredi naziv kategorije</div>
          <button type="button" data-close-button class="close-button">&times;</button>  <!-- cancel 'x' button -->
        </div>

        <div class="modal-body">
          <div class="input-box">
            <input type="text" id="category_name_fetched" name="category_name" placeholder="Nova kategorija" >  
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

<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>

<script>
  $(document).ready(function (){
  $('.update-button').on('click', function() {
    $tr = $(this).closest('tr');

    var data = $tr.children("td").map(function() {
      return $(this).text();
    }).get();
    console.log(data);

    $('#category_id_fetched').val(data[0]);
    $('#category_name_fetched').val(data[1]);



  });
});

</script>

</body>
</html>