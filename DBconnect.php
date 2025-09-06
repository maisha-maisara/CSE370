<?php
$servername = "localhost";
$username = "root";  // Default username
$password = "";  // Default password
$dbname = "mental_health";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}
?>
