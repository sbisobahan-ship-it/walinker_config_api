<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "walinker";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Set charset to UTF-8 (utf8mb4 for all languages)
$conn->set_charset("utf8mb4");  // ✅ supports all Unicode languages
?>
