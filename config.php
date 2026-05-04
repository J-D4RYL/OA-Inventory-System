<?php
session_start();

$host = "localhost";
$user = "root";
$pass = "0000";
$dbname = "oa_clothing_inventory";

$conn = mysqli_connect($host, $user, $pass, $dbname);

if (!$conn) {
    die("Database connection failed: " . mysqli_connect_error());
}
?>
