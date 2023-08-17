<?php
require 'includes/db_connection.php';
require 'classes/Product.php';
require 'classes/Cart.php';

$productObj = new Product($conn);
$cartObj = new Cart($conn);
$cartCount = $cartObj->getCartCount();
if (isset($_GET['id'])) {
    $productID = $_GET['id'];
    $productDetails = $productObj->getProductByID($productID);
    if (!$productDetails) {
        header('Location: show_products.php');
        exit();
    }
} else {
    header('Location: show_products.php');
    exit();
}
$productQuantityInCart = isset($_SESSION['cart'][$productID]) ? $_SESSION['cart'][$productID] : 0;
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>
        <?php echo $productDetails['productName']; ?> - Details
    </title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.1/css/all.min.css">
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <style>
        .product-image {
            transition: transform .3s ease-in-out;
            cursor: pointer;
        }

        .product-image:hover {
            transform: scale(1.05);
        }

        .zoom-hint {
            display: none;
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background: rgba(0, 0, 0, 0.6);
            color: white;
            padding: 10px 20px;
            border-radius: 5px;
        }

        .product-image-container:hover .zoom-hint {
            display: block;
        }
    </style>
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

    <div class="container mt-5">
        <div class="row">
            <div class="col-md-3 position-relative">
                <div class="product-image-container">
                    <img src="<?php echo $productDetails['imageURL']; ?>"
                        alt="<?php echo $productDetails['productName']; ?>" class="img-fluid product-image rounded"
                        data-toggle="modal" data-target="#productImageModal">
                </div>
            </div>
            <div class="col-md-6">
                <h2>
                    <?php echo $productDetails['productName']; ?>
                </h2>
                <p class="text-muted">Category:
                    <?php echo $productDetails['category']; ?>
                </p>
                <p class="lead">Price: $
                    <?php echo $productDetails['price']; ?>
                </p>
                <p>
                    <?php echo $productDetails['description']; ?>
                </p>

                <div class="input-group mb-3">
                    <div class="input-group-prepend">
                        <button class="btn btn-outline-secondary quantity-decrease" type="button"
                            data-product-id="<?php echo $productID; ?>">-</button>
                    </div>
                    <input type="text" class="form-control text-center" value="<?php echo $productQuantityInCart; ?>"
                        readonly>
                    <div class="input-group-append">
                        <button class="btn btn-outline-secondary quantity-increase" type="button"
                            data-product-id="<?php echo $productID; ?>">+</button>
                    </div>
                </div>
            </div>
        </div>
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
                                quantityInput.val(currentQuantity > 0 ? currentQuantity - 1 : 0);
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