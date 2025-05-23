<?php
$host = "sql3.freesqldatabase.com";
$dbname = "sql3780760";
$username = "your_username";
$password = "your_password";

$conn = new mysqli($host, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
