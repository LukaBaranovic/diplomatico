<?php
// Priprema .php za unos i provjeru podataka

session_start();
$mysqli = require_once __DIR__ . "/database.php";

// Provjerava jesu li vrijednosti 'user_id' i 'company_id' upisane u sesiju.
if (!isset($_SESSION['user_id']) || empty($_SESSION['company_id'])) {
    die("Session user_id or company_id is not set or is invalid.");
}

$user_id = (int)$_SESSION['user_id'];
$company_id = (int)$_SESSION['company_id'];

// Provjerava jesmo li uopće išta unijeli u obrazac
if (isset($_POST['add_category']) && !empty($_POST['category_name'])) {

    $category_name = trim($_POST['category_name']);  

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
    
    // Unosi podatke u bazu podataka, Stavljamo company_id da možemo razlikovati više bazi podataka.
    $query = "INSERT INTO `category` (`category_name`, `company_id`) VALUES ('$category_name', '$company_id')";
    $result = mysqli_query($mysqli, $query);

    // Provjerava je li sve uspješno obavljeno.
    if (!$result) {
        die("Query failed: " . mysqli_error($mysqli)); 
    } else {
        header("Location: index.php?message=success");
        exit();
    }
  }
} else {
    header("Location: index.php?message=failed");
}
?>

<!-- #### Ova .php datoteka se koristi za unos vrijednosti u bazu podataka, tablica category u bazi diplomatico,
 koju korisnik unosi prilikom dodavanje nove kategorije. Vrši se provjera je li unos duplikat već postojeće
 kategorije, je li unos prazan, i ako je sve u redu unosi podatke u bazu podataka.

 Poruke:
 ?message=failed: ako je došlo do neke pogreške prilikom unosa u bazu podataka
 ?message=success: ako je unos uspješno obavljen
 ?message=duplicate: ako je unos duplikat već postojeće kategorije

 Poruke dobivamo u url-u, a možda kasnije budu kasnije prikazane.
 Nebitno o uspješnosti unosa u bazu podatak, vraćamo se na index stranicu, tj. početnu stranicu nakon prijave.

 Napravljeno: 10.2.2025.
 Zadnja promjena: 10.2.2025
 Napravio: Luka Baranović
-->