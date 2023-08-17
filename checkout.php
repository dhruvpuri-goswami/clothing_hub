<?php
require 'includes/db_connection.php';
require 'classes/User.php';
require 'classes/Cart.php';
require 'classes/Product.php';
require 'classes/Checkout.php';

session_start();

if (!isset($_SESSION['username'])) {
    header("Location: login.php?redirect=checkout.php");
    exit();
}

$username = $_SESSION['username'];

$cart = new Cart($conn);
$checkout = new Checkout($conn, $cart);

$cartProducts = $checkout->getCartDetails();
$cartTotal = $checkout->getCartTotal();
$cartCount = $checkout->getCartCount();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $address = $_POST['address'];
    $city = $_POST['city'];
    $state = $_POST['state'];
    $zip = $_POST['zip'];

    $orderID = $checkout->storeOrderDetails($username, $address, $city, $state, $zip, $cartTotal);

    if ($orderID) {
        $checkout->clearCart();
        header("Location: success.php?orderID=$orderID");
        exit();
    } else {
        echo "Order processing failed. Please try again.";
    }
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


    <div class="container mt-5">
        <h2 class="text-center mb-4" style="color: #00494d;">Checkout</h2>
        <div class="row">
            <div class="col-md-8">
                <h4>Shipping Details</h4>
                <form id="checkoutForm" method="post" action="">
                    <div class="form-group">
                        <label for="address">Address:</label>
                        <input type="text" class="form-control" id="address" name="address" placeholder="1234 Main St"
                            required>
                    </div>
                    <div class="form-group">
                        <label for="city">City:</label>
                        <input type="text" class="form-control" id="city" name="city" placeholder="City" required>
                    </div>
                    <div class="form-group">
                        <label for="state">State:</label>
                        <input type="text" class="form-control" id="state" name="state" placeholder="State" required>
                    </div>
                    <div class="form-group">
                        <label for="zip">Zip:</label>
                        <input type="text" class="form-control" id="zip" name="zip" placeholder="Zip" required
                            pattern="\d{5}" title="Please enter a valid 5 digit zip code.">
                    </div>
                    <hr>

                    <h4>Credit Card Details</h4>
                    <div class="form-group">
                        <label for="cname">Name on Card:</label>
                        <input type="text" class="form-control" id="cname" name="cname" placeholder="John Doe" required>
                    </div>
                    <div class="form-group">
                        <label for="ccnum">Credit card number:</label>
                        <input type="text" class="form-control" id="ccnum" name="ccnum" placeholder="1111222233334444"
                            required pattern="\d{16}" title="Please enter a valid 16 digit credit card number.">
                    </div>
                    <div class="form-group">
                        <label for="expmonth">Exp Month:</label>
                        <select class="form-control" id="expmonth" name="expmonth" required>
                            <option value="">Select Month</option>
                            <option value="01">January</option>
                            <option value="02">February</option>
                            <option value="03">March</option>
                            <option value="04">April</option>
                            <option value="05">May</option>
                            <option value="06">June</option>
                            <option value="07">July</option>
                            <option value="08">August</option>
                            <option value="09">September</option>
                            <option value="10">October</option>
                            <option value="11">November</option>
                            <option value="12">December</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="expyear">Exp Year:</label>
                        <input type="number" class="form-control" id="expyear" name="expyear" required min="2023"
                            max="2040">
                    </div>
                    <div class="form-group">
                        <label for="cvv">CVV:</label>
                        <input type="text" class="form-control" id="cvv" name="cvv" required pattern="\d{3}"
                            title="Please enter a valid 3 digit CVV.">
                    </div>
                </form>
            </div>
            <div class="col-md-4">
                <h4>Summary</h4>
                <ul class="list-group">
                    <li class="list-group-item d-flex justify-content-between">
                        <span>Total Items:</span>
                        <strong>
                            <?php echo $cartCount; ?>
                        </strong>
                    </li>
                    <li class="list-group-item d-flex justify-content-between">
                        <span>Subtotal:</span>
                        <strong>$
                            <?php echo number_format($cartTotal, 2); ?>
                        </strong>
                    </li>
                    <li class="list-group-item d-flex justify-content-between">
                        <span>Shipping:</span>
                        <strong>$10.00</strong>
                    </li>
                    <li class="list-group-item d-flex justify-content-between">
                        <span><strong>Total:</strong></span>
                        <strong>$
                            <?php echo number_format($cartTotal + 10, 2); ?>
                        </strong>
                    </li>
                </ul>
                <button type="submit" form="checkoutForm" class="btn btn-primary btn-block mt-4"
                    style="background-color: #00494d;">Complete Purchase</button>
            </div>
        </div>
    </div>

    <footer style="background-color: #00494d;" class="text-white mt-5 p-4 text-center">
        &copy; 2023 Clothing Hub. All rights reserved.
    </footer>


</body>
<script>
    document.getElementById('checkoutForm').addEventListener('submit', function (e) {
        let ccnum = document.getElementById('ccnum').value;

        if (!validateCreditCardNumber(ccnum)) {
            alert('Please enter a valid credit card number.');
            e.preventDefault();
        }
    });

    function validateCreditCardNumber(cardNumber) {
        let s = 0;
        let doubleDigit = false;
        for (let i = cardNumber.length - 1; i >= 0; i--) {
            let digit = parseInt(cardNumber[i]);
            if (doubleDigit) {
                digit *= 2;
                if (digit > 9) digit -= 9;
            }
            s += digit;
            doubleDigit = !doubleDigit;
        }
        return (s % 10) === 0;
    }
</script>

</html>