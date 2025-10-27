<?php
// Database connection settings
$servername = "localhost";
$username = "";
$password = "";
$dbname = "=";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Use $conn in other files by requiring this file
?>