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
if (isset($_POST['edit_category']) && !empty($_POST['category_name'])) {

    $category_name = mysqli_real_escape_string($mysqli, $_POST['category_name']); 
    $category_id = mysqli_real_escape_string($mysqli, $_POST['category_id']);

    // Provjerava je li vrijednost unosa korisnika već postoji u bazi podataka.
    $sql_check = "SELECT COUNT(*) FROM category WHERE category_name = ? AND company_id = ?";
    $stmt_check = $mysqli->prepare($sql_check);
    $stmt_check->bind_param("si", $category_name, $company_id);
    $stmt_check->execute();
    $stmt_check->bind_result($count);
    $stmt_check->fetch();
    $stmt_check->close();

    // Ako postoji duplikat, count ide na >0 i izlazimo iz datoteke.
    if ($count > 0) {
      echo "The category '{$category_name}' already exists for this user and company.";
      header("Location: index.php?message=duplicate");
  } else {
    
    // Ažurira podatke u bazi podataka, ovisno o company_id-u mjenjamo taj red.
    $query = "UPDATE `category` SET `category_name` = ? WHERE `category_id` = ?";
    $stmt = $mysqli->prepare($query);
    $stmt->bind_param("si", $category_name, $category_id);  
    $stmt->execute();
    
    // Provjerava je li stvarno došlo do promjene.
    if ($stmt->affected_rows > 0) {
      $stmt->close();
      header("Location: index.php?message=success");
      exit();
    } else {
      $stmt->close();
      header("Location: index.php?message=error");
      exit();
    }
  }
} else {
  header("Location: index.php?message=failed");
}
?>

<!-- #### Ova .php datoteka se koristi za ažuriranje vrijednosti u bazi podataka, tablica category u bazi diplomatico.
 Korisnik ažurira podatke prilikom klika na 'Uredi'. Tada preko AJAX-a (pomoću js skripte) dohvaćamo koji red u tablici smo 
 klikli i tada nam u obrascu bude već prikazana vrijednost spremna na ažuriranje. 
 
 Klikom na spremi se vrši provjera postoji li duplikat te ako postoji ne dolazi do promjene već korisnik mora unijeti unikatnu
 kategoriju. Ako je kategorija unikatna dolazi do izmjene tablice.

 Poruke:
 ?message=duplicate: ako je izmjena duplikat već postojeće kategorije
 ?message=success: ako je promjena uspješno obavljena
 ?message=error: ako je došlo do pogreške prilikom promjene u bazi podataka, tj. ako se nijedan red nije izmjenio
 ?message=failed: ako ništa nije uneseno u obrazac, tj. ako idemo spremiti prazno polje

 Poruke dobivamo u url-u, a možda kasnije budu kasnije prikazane.
 Nebitno o uspješnosti unosa u bazu podatak, vraćamo se na index stranicu, tj. početnu stranicu nakon prijave.

 Napravljeno: 10.2.2025.
 Zadnja promjena: 10.2.2025.
 Napravio: Luka Baranović
-->