<?php
//Database credentials
$dbHost     = '10.9.121.157';
$dbUsername = '';
$dbPassword = '';
$dbName     = 'moodle';

//Connect and select the database
$db = new mysqli($dbHost, $dbUsername, $dbPassword, $dbName);

if ($db->connect_error) {
    die("Connection failed: " . $db->connect_error);
}
?>