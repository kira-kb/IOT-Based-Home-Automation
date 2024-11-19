<?php
$servername = "localhost";
$dbname = "iot_based_home_automation";
$username = "root";
$password = "";

$con = mysqli_connect($servername, $username, $password, $dbname);

if (mysqli_connect_errno()) {
    die("Database connection failed: " . mysqli_connect_error());
}

// mysql_connect()

// Establish database connection, e.g., using PDO
// $dsn = 'mysql:host=localhost;dbname=iot_based_home_automation';
// $username = "root";
// $password = "";

// try {
//     $con = new PDO($dsn, $username, $password);
//     $con->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
// } catch (PDOException $e) {
//     die("Database connection failed: " . $e->getMessage());
// }