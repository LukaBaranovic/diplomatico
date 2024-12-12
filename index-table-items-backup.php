

<table class="content-table">
    <thead>
      <tr>
        <th>ID </th>
        <th colspan="3">Kategorija</th>
       
      
      </tr>
      <tr>
        <th>ID</th>
        <th>Artikal</th>
        <th>Cijena</th>
        <th>Kategorija</th>
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
        </tr>
          <?php
        }
      }
      ?>
    

          
        
    
      
    </tbody>
  </table>