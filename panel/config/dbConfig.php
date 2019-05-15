<?php
//Database credentials
$dbHost     = 'localhost';
$dbUsername = 'youssef';
$dbPassword = 'password';
$dbName     = 'moodle352';

//Connect and select the database
$db = new mysqli($dbHost, $dbUsername, $dbPassword, $dbName);

if ($db->connect_error) {
    die("Connection failed: " . $db->connect_error);
}
?>