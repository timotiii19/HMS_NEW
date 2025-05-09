<?php
$host = "localhost";
$dbname = "charles_hms"; // change this to your db name
$username = "root";
$password = "root";

// Create connection
$conn = new mysqli($host, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
