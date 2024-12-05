<?php
$servername = "localhost";  // Your MySQL server (often 'localhost' for local setup)
$username = "root";         // Your MySQL username (default is 'root' for local)
$password = "";             // Your MySQL password (leave empty if none is set)
$dbname = "mykhs";          // The name of your database

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
