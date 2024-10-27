<?php
$servername = "database-1.c704sumgm4qf.ap-southeast-2.rds.amazonaws.com";
$username = "admin";
$password = "maneclick1";
$dbname = "maneclickv2";

try {
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    // echo "Connected successfully to database.";
} catch (PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
}
