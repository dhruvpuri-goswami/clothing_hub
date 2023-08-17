<?php
require 'includes/db_connection.php';
require 'classes/Product.php';
require 'classes/Cart.php';
session_start();
$productObj = new Product($conn);
$cartObj = new Cart($conn);
$cartCount = $cartObj->getCartCount();
$categories = $productObj->getCategories();
$prices = $productObj->getDistinctPrices();

$products = [];
if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET['price'])) {
    $products = $productObj->getFilteredProducts($_GET['price'], $_GET['category']);
} else {
    $products = $productObj->getAllProducts();
}


$filters = [
    'price' => $_GET['price'] ?? null,
    'category' => $_GET['category'] ?? null,
    'size' => $_GET['size'] ?? null
];

$products = $productObj->getAllProducts($filters);

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
        <form action="show_product.php" method="GET">
            <div class="row mb-4">
                <div class="col-md-3">
                    <label>Filter by Category:</label>
                    <select name="category" class="form-control">
                        <option value="">Select Category</option>
                        <?php foreach ($categories as $category): ?>
                            <option value="<?php echo $category; ?>"><?php echo $category; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="col-md-3">
                    <label>Filter by Price:</label>
                    <select name="price" class="form-control">
                        <option value="">Select Price Range</option>
                        <option value="0-100">$0 - $100</option>
                        <option value="100-200">$100 - $200</option>
                        <option value="200-300">$200 - $300</option>
                    </select>
                </div>

                <div class="col-md-3 text-right">
                    <button class="btn text-white" style="background-color: #00494d;">Apply Filters</button>
                </div>
            </div>
        </form>

        <div class="row">
            <?php foreach ($products as $product): ?>
                <div class="col-md-4 mb-4">
                    <div class="card">
                        <div style="height: 250px; overflow: hidden;">
                            <img src="<?php echo $product['imageURL']; ?>" class="card-img-top"
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
                            <?php
                            $productQuantityInCart = isset($_SESSION['cart'][$product['productID']]) ? $_SESSION['cart'][$product['productID']] : 0;
                            ?>
                            <div class="input-group mb-3">
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
                            <div class="text-center">
                                <a href="product_details.php?id=<?php echo $product['productID']; ?>" class="btn text-white"
                                    style="background-color: #00494d;">Details</a>
                            </div>

                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
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
