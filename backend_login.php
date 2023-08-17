<?php
require 'includes/db_connection.php';
require 'classes/User.php';
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $user = new User($conn);
    $result = $user->login($username, $password);

    if ($result) {
        $_SESSION['username'] = $username;  // Store the username in the session
        if (isset($_GET['redirect'])) {
            header("Location: " . $_GET['redirect']);  
        } else {
            header("Location: index.php");  
        }
    } else {
        echo "Invalid credentials!";
    }
}

?>
