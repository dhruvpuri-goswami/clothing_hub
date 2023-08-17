<?php
require 'includes/db_connection.php';
require 'classes/User.php';
require 'classes/Cart.php';
require 'classes/Product.php';

session_start();

$cart = new Cart($conn);
$productObj = new Product($conn);

$cartProducts = $cart->getItems();
$cartTotal = 0;

foreach ($cartProducts as $product) {
    $cartTotal += $product['price'] * $product['quantity'];
}

$cartCount = array_sum($_SESSION['cart']);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Clothing Hub</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.1/css/all.min.css">
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
</head>

<body>

<nav class="navbar navbar-expand-lg" style="background-color: #00494d;">
    <a class="navbar-brand text-white" href="index.php" style="font-size: 24px;">
        <i class="fas fa-tshirt"></i> Clothing Hub
    </a>
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNav">
        <ul class="navbar-nav ml-auto">
            <!-- Check if the user is logged in -->
            <?php if (isset($_SESSION['username'])): ?>
                <li class="nav-item">
                    <span class="navbar-text text-white mr-3">Welcome, <?php echo $_SESSION['username']; ?>!</span>
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
                    <span class="badge badge-warning"><?php echo $cartCount; ?></span>
                </a>
            </li>
        </ul>
    </div>
</nav>


<div class="container mt-5">
    <h2 class="text-center mb-4" style="color: #00494d;">Your Cart</h2>
    <div class="row">
        <?php foreach ($cartProducts as $product) : ?>
            <div class="col-md-4 mb-4">
                <div class="card h-100 shadow-sm border-top-primary">
                    <img src="<?php echo $product['imageURL']; ?>" class="card-img-top" alt="<?php echo $product['productName']; ?>" style="height: 200px; object-fit: cover;">
                    <div class="card-body">
                        <h5 class="card-title"><?php echo $product['productName']; ?></h5>
                        <p><strong>Quantity:</strong> <span class="badge badge-info" style="background-color: #00494d;"><?php echo $product['quantity']; ?></span></p>
                        <p><strong>Price:</strong> $<?php echo number_format($product['price'], 2); ?></p>
                        <?php if ($product['quantity'] > 0): ?>
                        <p><i class="fas fa-calculator"></i><strong> Total:</strong> $<?php echo number_format($product['price'] * $product['quantity'], 2); ?></p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
    <div class="text-right mt-3">
        <h4>Total: $<?php echo number_format($cartTotal, 2); ?></h4>
    </div>
    <div class="text-center mt-5">
        <a href="checkout.php" class="btn text-white" style="background-color: #00494d;">Checkout</a>
    </div>
</div>



    <div style="position: fixed; bottom: 20px; right: 20px; z-index: 1000;" class="animated">
        <a href="cart.php">
            <div style="background-color: #00494d;" class="rounded p-3 text-white">
                <i class="fas fa-shopping-cart fa-2x"></i>
                <span class="badge badge-warning"><?php echo $cartCount; ?></span>
            </div>
        </a>
    </div>

    <footer style="background-color: #00494d;" class="text-white mt-5 p-4 text-center">
        &copy; 2023 Clothing Hub. All rights reserved.
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script>
$(document).ready(function(){
    $('.quantity-increase, .quantity-decrease').on('click', function(){
        let productId = $(this).data('product-id');
        let action = $(this).hasClass('quantity-increase') ? 'add' : 'remove';
        
        let quantityInput = $(this).closest('.input-group').find('input');

        $.ajax({
            type: 'POST',
            url: 'update_cart.php',
            data: {
                product_id: productId,
                action: action
            },
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    $('.fa-shopping-cart + .badge').text(response.cartCount);
                    
                    let currentQuantity = parseInt(quantityInput.val());
                    if (action == 'add') {
                        quantityInput.val(currentQuantity + 1);
                    } else {
                        if(currentQuantity == 0){
                            
                        }else{
                            quantityInput.val(currentQuantity - 1);
                        }
                    }
                } else {
                    console.error("Failed to update cart via AJAX");
                }
            },
            error: function(jqXHR, textStatus, errorThrown) {
                console.error("AJAX error: " + textStatus + ' : ' + errorThrown);
            }
        });
    });
});

</script>
</body>
</html>
