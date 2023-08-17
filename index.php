<?php
require 'includes/db_connection.php';
require 'classes/User.php';
require 'classes/Cart.php';
require 'classes/Product.php';

session_start();

$productObj = new Product($conn);
$cartObj = new Cart($conn);

$products = $productObj->getFeaturedProducts();
$cartCount = $cartObj->getCartCount();

if (isset($_POST['details'])) {
}

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
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav"
            aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ml-auto">
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



    <div id="carouselExampleControls" class="carousel slide" data-ride="carousel">
        <div class="carousel-inner">
            <div class="carousel-item active" style="height: 550px;">
                <img src="assets/images/banner3.jpg" class="d-block w-100 h-150" alt="Banner 1"
                    style="object-fit: cover;">
            </div>
            <div class="carousel-item" style="height: 550px;">
                <img src="assets/images/banner2.jpg" class="d-block w-100 h-150" alt="Banner 2"
                    style="object-fit: cover;">
            </div>
            <div class="carousel-item" style="height: 550px;">
                <img src="assets/images/banner1.jpg" class="d-block w-100 h-150" alt="Banner 3"
                    style="object-fit: cover;">
            </div>
        </div>
        <a class="carousel-control-prev" href="#carouselExampleControls" role="button" data-slide="prev">
            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
            <span class="sr-only">Previous</span>
        </a>
        <a class="carousel-control-next" href="#carouselExampleControls" role="button" data-slide="next">
            <span class="carousel-control-next-icon" aria-hidden="true"></span>
            <span class="sr-only">Next</span>
        </a>
    </div>

    <div class="container mt-5">
        <h2 class="text-center mb-4" style="color: #00494d;">Featured Products</h2><br>
        <div class="row">
            <?php foreach ($products as $product): ?>
                <div class="col-md-3 mb-4">
                    <div class="card h-100 border-primary">
                        <div style="height: 200px; overflow: hidden;">
                            <img src="<?php echo $product['imageURL']; ?>" class="card-img-top img-fluid"
                                alt="<?php echo $product['productName']; ?>"
                                style="object-fit: cover; height: 100%; width: 100%;">
                        </div>
                        <div class="card-body">
                            <h5 class="card-title">
                                <?php echo $product['productName']; ?>
                            </h5>
                            <p class="card-text">$
                                <?php echo $product['price']; ?>
                            </p>
                        </div>
                        <div class="card-footer">
                            <?php
                            $productQuantityInCart = isset($_SESSION['cart'][$product['productID']]) ? $_SESSION['cart'][$product['productID']] : 0;
                            ?>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <button class="btn btn-outline-secondary quantity-decrease" type="button"
                                        data-product-id="<?php echo $product['productID']; ?>">-</button>
                                </div>
                                <input type="text" class="form-control text-center"
                                    value="<?php echo $productQuantityInCart; ?>" readonly>
                                <div class="input-group-append">
                                    <button class="btn btn-outline-secondary quantity-increase" type="button"
                                        data-product-id="<?php echo $product['productID']; ?>">+</button>
                                </div>
                            </div>
                            <a href="product_details.php?id=<?php echo $product['productID']; ?>"
                                class="btn btn-block text-white mt-2" style="background-color: #00494d;">Details</a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
    <div class="text-center mt-4">
        <a href="show_product.php" class="btn btn-lg text-white" style="background-color: #00494d;">Show All
            Products</a>
    </div>

    <div style="position: fixed; bottom: 20px; right: 20px; z-index: 1000;" class="animated">
        <a href="cart.php">
            <div style="background-color: #00494d;" class="rounded p-3 text-white">
                <i class="fas fa-shopping-cart fa-2x"></i>
                <span class="badge badge-warning">
                    <?php echo $cartCount; ?>
                </span>
            </div>
        </a>
    </div>

    <footer style="background-color: #00494d;" class="text-white mt-5 p-4 text-center">
        &copy; 2023 Clothing Hub. All rights reserved.
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script>
        $(document).ready(function () {
            $('.quantity-increase, .quantity-decrease').on('click', function () {
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
                    success: function (response) {
                        if (response.success) {
                            $('.fa-shopping-cart + .badge').text(response.cartCount);

                            let currentQuantity = parseInt(quantityInput.val());
                            if (action == 'add') {
                                quantityInput.val(currentQuantity + 1);
                            } else {
                                if (currentQuantity == 0) {

                                } else {
                                    quantityInput.val(currentQuantity - 1);
                                }
                            }
                        } else {
                            console.error("Failed to update cart via AJAX");
                        }
                    },
                    error: function (jqXHR, textStatus, errorThrown) {
                        console.error("AJAX error: " + textStatus + ' : ' + errorThrown);
                    }
                });
            });
        });

    </script>

</body>

</html>