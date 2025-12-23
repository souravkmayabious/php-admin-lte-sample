<?php
$host = "localhost";
$user = "root";
$password = "";
$database = "test_sample_database";
date_default_timezone_set("Asia/Kolkata");
// Create connection
$conn = mysqli_connect($host, $user, $password, $database);

// Check connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

?>
