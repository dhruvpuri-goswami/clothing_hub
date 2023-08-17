<?php
require 'includes/db_connection.php';
require 'classes/User.php'; // If required
require 'classes/Cart.php';
require 'classes/Product.php'; // If required
require 'classes/Checkout.php';

session_start();

$checkout = new Checkout($conn, new Cart($conn));
$cartCount = $checkout->getCartCount();


if (!$checkout->isLoggedIn()) {
    $checkout->redirectToLogin();
}

$orderID = $_GET['orderID'] ?? '';
$orderDetails = $checkout->getOrderData($orderID);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thank You - Clothing Hub</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.1/css/all.min.css">
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
</head>

<body>

    <nav class="navbar navbar-expand-lg" style="background-color: #00494d;">
        <a class="navbar-brand text-white" href="index.php" style="font-size: 24px;">
            <i class="fas fa-tshirt"></i> Clothing Hub
        </a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav"
            aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ml-auto">
                <!-- Check if the user is logged in -->
                <?php if (isset($_SESSION['username'])): ?>
                    <li class="nav-item">
                        <span class="navbar-text text-white mr-3">Welcome,
                            <?php echo $_SESSION['username']; ?>!
                        </span>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-white" href="logout.php">Logout</a>
                    </li>
                <?php else: ?>
                    <li class="nav-item">
                        <a class="nav-link text-white" href="login.php">Login</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-white" href="register.php">Register</a>
                    </li>
                <?php endif; ?>

                <!-- Cart icon -->
                <li class="nav-item">
                    <a class="nav-link text-white" href="cart.php">
                        <i class="fas fa-shopping-cart"></i>
                        <span class="badge badge-warning">
                            <?php echo $cartCount; ?>
                        </span>
                    </a>
                </li>
            </ul>
        </div>
    </nav>

    <div class="container mt-5 text-center">
        <h2>Payment Successful!</h2>
        <div>
            <i class="fas fa-check-circle fa-5x" style="color: green;"></i>
        </div>

        <div class="mt-5">
            <h4>Your Order Details:</h4>
            <p><strong>Order ID:</strong>
                <?= $orderID ?>
            </p>
            <p><strong>Total Amount:</strong> $
                <?= number_format($orderDetails['totalAmount'], 2) ?>
            </p>

            <div class="mt-4">
                <a href="download_receipt.php?orderID=<?= $orderID ?>" class="btn btn-primary"
                    style="background-color: #00494d;">Download Receipt</a>
            </div>
        </div>
    </div>

    <footer style="background-color: #00494d;" class="text-white mt-5 p-4 text-center">
        &copy; 2023 Clothing Hub. All rights reserved.
    </footer>

</body>

</html>