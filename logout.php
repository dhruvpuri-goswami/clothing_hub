<?php
require 'includes/db_connection.php';
require 'classes/User.php';

session_start();

$user = new User($conn); // Pass the database connection to the User object

$user->logout();

header("Location: index.php"); // Redirect to login page after logout
exit();
?>