<?php
session_start();

// Database connection parameters
$host = 'localhost';
$user = 'root';
$password = 'mani123';
$database = 'hospital_login';

// Create connection
$conn = mysqli_connect($host, $user, $password, $database);

// Check connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}
?>