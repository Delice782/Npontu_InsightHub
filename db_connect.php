<?php
$servername = "localhost";
$username = "delice.ishimwe";
$dbname = "webtech_fall2024_delice_ishimwe";
$port = 3341;

// Create connection using mysqli
$conn = new mysqli($servername, $username, $password, $dbname, $port);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Set character encoding to avoid issues with special characters
$conn->set_charset("utf8");
?>
