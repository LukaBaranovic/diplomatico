<?php
// Priprema .php za unos i provjeru podataka

session_start();
$mysqli = require_once __DIR__ . "/database.php";

$user_id = (int)$_SESSION['user_id'];
$company_id = (int)$_SESSION['company_id'];

// Provjerava jesu li vrijednosti 'user_id' i 'company_id' upisane u sesiju.
if (!isset($_SESSION['user_id']) || empty($_SESSION['company_id'])) {
    die("Session user_id or company_id is not set or is invalid.");
}

// Provjerava jesmo li uopće išta unijeli u obrazac
if (isset($_POST['edit_item']) && !empty($_POST['item_id']) && !empty($_POST['item_name']) && !empty($_POST['item_price']) && !empty($_POST['category_name'])) {

    // dobavljamo podatke iz obrasca 
    $item_id = mysqli_real_escape_string($mysqli, $_POST['item_id']); 
    $category_name = mysqli_real_escape_string($mysqli, $_POST['category_name']); 
    $item_name = trim($_POST['item_name']);  
    $item_price = floatval($_POST['item_price']);

    echo $item_id, $category_name, $item_name, $item_price;

    // dobavljamo category_id od category_name
    $sql_get_category_id = "SELECT category_id FROM category WHERE category_name = ? AND company_id = ?";
    $stmt_get_category_id = $mysqli->prepare($sql_get_category_id);
    $stmt_get_category_id->bind_param('si', $category_name, $company_id);
    $stmt_get_category_id->execute();
    $result_get_category_id = $stmt_get_category_id->get_result();
    $category = $result_get_category_id->fetch_assoc();
    $category_id = $category['category_id'];

    // Provjerava je li vrijednost unosa korisnika već postoji u bazi podataka.
    $sql_check = "SELECT COUNT(*) FROM item WHERE item_name = ? AND company_id = ?";
    $stmt_check = $mysqli->prepare($sql_check);
    $stmt_check->bind_param("si", $item_name, $company_id);
    $stmt_check->execute();
    $stmt_check->bind_result($count);
    $stmt_check->fetch();
    $stmt_check->close();
    // Ako postoji vrijednost, count ide na 1, u protivnom ostaje na 0

    // Prvo ažuriramo cijenu i kategoriju, a za naziv se prvo radi provjera da nismo unijeli artikal istog imena 
    $query_update = "UPDATE `item` SET `item_price` = ?, `category_id` = ? WHERE `item_id` = ? AND company_id = ?";
    $stmt_update = $mysqli->prepare($query_update);
    $stmt_update->bind_param("diii", $item_price, $category_id, $item_id, $company_id);  
    $stmt_update->execute();

    // Ako postoji duplikat, count ide na >0 i izlazimo iz datoteke.
    if ($count > 0) {
      echo "The item '{$item_name}' already exists for this user and company.";
      header("Location: index.php?message=duplicate");
  } else {
    
    // Ažurira podatke u bazi podataka, ovisno o company_id-u mjenjamo taj red.
    $query_update_name = "UPDATE `item` SET `item_name` = ?  WHERE `item_id` = ?";
    $stmt_update_name = $mysqli->prepare($query_update_name);
    $stmt_update_name->bind_param("si", $item_name, $item_id);  
    $stmt_update_name->execute();
    
    // Provjerava je li stvarno došlo do promjene.
    if ($stmt_update_name->affected_rows > 0) {
      $stmt_update_name->close();
      header("Location: index.php?message=success");
      exit();
    } else {
      $stmt_update_name->close();
      header("Location: index.php?message=error");
      exit();
    }
  }
} else {
  header("Location: index.php?message=failed");
}
?>

<!-- #### Ova .php datoteka se koristi za ažuriranje vrijednosti u bazi podataka, tablica item u bazi diplomatico.
 Korisnik ažurira podatke prilikom klika na 'Uredi'. Tada preko AJAX-a (pomoću js skripte) dohvaćamo koji red u tablici smo 
 klikli i tada nam u obrascu bude već prikazan naziv, cijena i kategorija spremni na ažuriranje. 
 
 Klikom na spremi se vrši provjera postoji li duplikat te ako postoji ne dolazi do promjene već korisnik mora unijeti unikatni
 artikal. Ako je artikal unikatna dolazi do izmjene tablice, a cijena i kategorija se svakako ažuriraju.

 Poruke:
 ?message=duplicate: ako je izmjena duplikat već postojeće kategorije
 ?message=success: ako je promjena uspješno obavljena
 ?message=error: ako je došlo do pogreške prilikom promjene u bazi podataka, tj. ako se nijedan red nije izmjenio
 ?message=failed: ako ništa nije uneseno u obrazac, tj. ako idemo spremiti prazno polje

 Poruke dobivamo u url-u (na dan 2.12.2024.), a možda kasnije budu kasnije prikazane.
 Nebitno o uspješnosti unosa u bazu podatak, vraćamo se na index stranicu, tj. početnu stranicu nakon prijave.

 Napravljeno: 10.2.2025.
 Zadnja promjena: 10.2.2025.
 Napravio: Luka Baranović
-->