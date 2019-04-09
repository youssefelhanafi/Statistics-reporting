<?php
$servername = "localhost";
$username = "youssef";
$password = "password";
$database = "moodle";

// Create connection
$conn = mysqli_connect($servername, $username, $password, $database);

// Check connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}
//echo "Connected successfully";
?> 