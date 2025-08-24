<?php
$dbuser = "root";       // MySQL default user
$dbpass = "";           // no password for root in XAMPP
$host   = "localhost";  
$db     = "myhostel";   // database name

// Create connection
$conn = new mysqli($host, $dbuser, $dbpass, $db);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
