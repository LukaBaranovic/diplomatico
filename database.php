<?php

$host="localhost";
$dbname="diplomatico";
$username="root";
$password= "";

$mysqli = new mysqli($host,   
                     $username,      
                     $password, 
                     $dbname);

if ($mysqli->connect_error) {
  die( "Connection error" . $mysqli->connect_error);
}

return $mysqli;

// #### Ova .php datoteka se koristi za spajanje na bazu podataka 'diplomatico', koju ćemo koristiti kroz cijeli projekt više puta.
// ####
// #### Host:            "localhost"
// #### Naziv baze:      "diplomatico"
// #### Korisnik:        "root"
// #### Lozinka:         ""
// ####
// #### Napravljeno:     20.11.2024.
// #### Zadnja promjena: 2.12.2024.
// #### Napravio: Luka Baranović