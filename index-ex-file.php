  
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
      JOIN category ON item.category_id = category.category_id
      WHERE item.company_id = '$company_id'";

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


