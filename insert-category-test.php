<?php

session_start();
$mysqli = require_once __DIR__ . "/database.php";

$user_id = (int)$_SESSION['user_id'];
$company_id = (int)$_SESSION['company_id'];

if (!isset($_SESSION['user_id']) || empty($_SESSION['company_id'])) {
  die("Session user_ or company_id is not set or is invalid.");
}






?>