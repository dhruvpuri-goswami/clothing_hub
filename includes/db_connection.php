<?php
$host = 'localhost:8111';       // Your database host (usually localhost)
$dbname = "clothing_db";
$user = 'root';            // The username for the database
$pass = '';                // The password for the database user

try {
    $conn = new PDO("mysql:host=$host;dbname=$dbname", $user, $pass);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
    echo $conn->error;

}

?>
