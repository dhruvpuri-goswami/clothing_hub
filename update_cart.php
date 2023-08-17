<?php
session_start();

$response = [
    'success' => false,
    'cartCount' => 0
];

if (isset($_POST['product_id']) && isset($_POST['action'])) {
    $productId = $_POST['product_id'];
    $action = $_POST['action'];

    if ($action == 'add') {
        $quantity = isset($_SESSION['cart'][$productId]) ? $_SESSION['cart'][$productId] + 1 : 1;
        $_SESSION['cart'][$productId] = $quantity;
    }

    if ($action == 'remove') {
        $quantity = isset($_SESSION['cart'][$productId]) ? $_SESSION['cart'][$productId] - 1 : 0;
        if ($quantity <= 0) {
            unset($_SESSION['cart'][$productId]);
        } else {
            $_SESSION['cart'][$productId] = $quantity;
        }
    }

    $response['success'] = true;
    $response['cartCount'] = array_sum($_SESSION['cart']);
}

echo json_encode($response);
?>