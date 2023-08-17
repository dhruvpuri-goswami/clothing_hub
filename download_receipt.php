<?php
require 'includes/db_connection.php';
require 'classes/Cart.php';
require 'classes/Product.php';
require 'classes/Checkout.php';

require "pdfcrowd.php";

session_start();

$checkout = new Checkout($conn, new Cart($conn));

$orderDetails = $checkout->getOrderDetails($_SESSION['username']);

$transactionID = uniqid("TRANS-");

// Generate the HTML for the receipt
$html_content = "<!DOCTYPE html>
<html>
<head>
    <style>
        body {
            font-family: Arial, sans-serif;
        }
        h1, h3 {
            color: #00494d;
        }
        p {
            margin-bottom: 10px;
        }
        .order-details, .payment-details {
            border: 1px solid #ddd;
            padding: 15px;
            margin-bottom: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }
    </style>
</head>
<body>
<h1>Clothing Hub Receipt</h1>";

if (isset($orderDetails['orderID'])) {
    $html_content .= "<p><strong>Order ID:</strong> {$orderDetails['orderID']}</p>";
} else {
    $html_content .= "<p><strong>Order ID:</strong> Not available</p>";
}

$html_content .= "<p><strong>Username:</strong> {$_SESSION['username']}</p>";
$html_content .= "<div class='order-details'><h3>Order Details:</h3>";

if (isset($orderDetails['products']) && is_array($orderDetails['products']) && count($orderDetails['products']) > 0) {
    $html_content .= "<table>
                        <thead>
                            <tr>
                                <th>Product</th>
                                <th>Quantity</th>
                                <th>Price</th>
                            </tr>
                        </thead>
                        <tbody>";
    foreach ($orderDetails['products'] as $product) {
        $html_content .= "<tr>
                            <td>{$product['name']}</td>
                            <td>{$product['quantity']}</td>
                            <td>{$product['price']}</td>
                          </tr>";
    }
    $html_content .= "</tbody></table>";
} else {
    $html_content .= "<p>No products found in the order.</p>";
}
$html_content .= "</div>";

$html_content .= "<div class='payment-details'><h3>Payment Details:</h3>";
if (isset($orderDetails['totalAmount'])) {
    $html_content .= "<p><strong>Amount:</strong> {$orderDetails['totalAmount']}</p>";
} else {
    $html_content .= "<p><strong>Amount:</strong> Not available</p>";
}

$html_content .= "<p><strong>Payment Option:</strong> Credit Card</p>";  // Assuming this is static for now
$html_content .= "<p><strong>Transaction ID:</strong> $transactionID</p>";
$html_content .= "</div>";

$html_content .= "</body></html>";

try {
    // create the API client instance
    $client = new \Pdfcrowd\HtmlToPdfClient("clothinghub", "0dc125a5f53c4c967b2dab91ac10b6bb");

    // Convert the HTML string to PDF
    $pdf_data = $client->convertString($html_content);

    // Set headers and send the PDF for download
    header('Content-Type: application/pdf');
    header('Content-Disposition: inline; filename="receipt.pdf"'); // changed to inline for display in browser
    echo $pdf_data;

} catch(\Pdfcrowd\Error $why) {
    error_log("Pdfcrowd Error: {$why}\n");
    echo "Failed to generate PDF. Please try again later.";
}
?>
