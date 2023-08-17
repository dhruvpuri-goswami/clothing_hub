<?php
require 'includes/db_connection.php';
require 'classes/User.php';
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = $_POST['password'];

    $user = new User($conn);
    $result = $user->register($username, $password, $email);

    if ($result) {
        header("Location: login.php");  
    } else {
        echo "There was an error in registration!";
    }
}

?>
