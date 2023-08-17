<?php 

class Cart {
    private $conn;
    private $productObj;

    public function __construct($conn) {
        $this->conn = $conn;
        $this->productObj = new Product($conn);  

        if (!isset($_SESSION['cart'])) {
            $_SESSION['cart'] = [];
        }
    }

    public function add($productId, $quantity = 1) {
        if (!isset($_SESSION['cart'][$productId])) {
            $_SESSION['cart'][$productId] = 0;
        }
        $_SESSION['cart'][$productId] += $quantity;
    }

    public function remove($productId, $quantity = 1) {
        if (isset($_SESSION['cart'][$productId])) {
            $_SESSION['cart'][$productId] -= $quantity;
            if ($_SESSION['cart'][$productId] <= 0) {
                unset($_SESSION['cart'][$productId]);
            }
        }
    }

    public function getCartCount() {
        return isset($_SESSION['cart']) ? array_sum($_SESSION['cart']) : 0;
    }
    
    public function getItems() {
        $items = [];
        if (isset($_SESSION['cart']) && is_array($_SESSION['cart'])) {
            foreach ($_SESSION['cart'] as $productId => $quantity) {
                $product = $this->productObj->getDetails($productId);  
                if ($product) {
                    $product['quantity'] = $quantity;
                    $items[] = $product;
                }
            }
        }
        return $items;
    }

    public function getCartTotal() {
        $total = 0;
        foreach ($this->getItems() as $item) {
            $total += $item['price'] * $item['quantity'];
        }
        return $total;
    }
}

?>
