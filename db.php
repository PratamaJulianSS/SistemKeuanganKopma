<?php
// db.php
session_start();

$DB_HOST = "localhost";
$DB_USER = "root";
$DB_PASS = ""; // ganti jika diperlukan
$DB_NAME = "db_kopma";

$conn = mysqli_connect($DB_HOST, $DB_USER, $DB_PASS, $DB_NAME);
if (!$conn) {
    die("Database connection failed: " . mysqli_connect_error());
}

// helper: escape
function e($s) {
    return htmlspecialchars($s, ENT_QUOTES, 'UTF-8');
}

?>
