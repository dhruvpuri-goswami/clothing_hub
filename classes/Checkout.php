<?php

class Checkout {
    private $conn;
    private $cart;

    public function __construct($conn, $cart) {
        $this->conn = $conn;
        $this->cart = $cart;
    }

    public function isLoggedIn() {
        return isset($_SESSION['username']);
    }

    public function redirectToLogin() {
        header("Location: login.php?redirect=checkout.php");
        exit();
    }

    public function getCartDetails() {
        return $this->cart->getItems();
    }

    public function getCartTotal() {
        return $this->cart->getCartTotal();
    }

    public function getCartCount() {
        return $this->cart->getCartCount();
    }

    public function storeOrderDetails($username, $address, $city, $state, $zip, $totalAmount) {
        $orderDate = date('Y-m-d H:i:s');  
        $stmt = $this->conn->prepare("INSERT INTO orders (username, address, city, state, zip, totalAmount, orderDate) VALUES (:username, :address, :city, :state, :zip, :totalAmount, :orderDate)");
        $stmt->bindParam(':username', $username);
        $stmt->bindParam(':address', $address);
        $stmt->bindParam(':city', $city);
        $stmt->bindParam(':state', $state);
        $stmt->bindParam(':zip', $zip);
        $stmt->bindParam(':totalAmount', $totalAmount);
        $stmt->bindParam(':orderDate', $orderDate);
        $stmt->execute();
    
        $orderID = $this->conn->lastInsertId();
    
        foreach ($this->cart->getItems() as $product) {
            $stmtProduct = $this->conn->prepare("INSERT INTO product_order (orderID, productID, quantity, priceAtTimeOfPurchase) VALUES (:orderID, :productID, :quantity, :priceAtTimeOfPurchase)");
            $stmtProduct->bindParam(':orderID', $orderID);
            $stmtProduct->bindParam(':productID', $product['productID']);
            $stmtProduct->bindParam(':quantity', $product['quantity']);
            $stmtProduct->bindParam(':priceAtTimeOfPurchase', $product['price']);
            $stmtProduct->execute();
        }
    
        return $orderID;
    }
    
    public function getOrderDetails($username) {
        $sql = "SELECT 
        o.orderID, 
        o.totalAmount, 
        po.productID, 
        po.quantity, 
        po.priceAtTimeOfPurchase,
        p.productName
    FROM orders o 
    JOIN product_order po ON o.orderID = po.orderID 
    JOIN products p ON po.productID = p.productID 
    WHERE o.username = :username";



        
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':username', $username, PDO::PARAM_STR);
        $stmt->execute();
    
        $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
        if (!$orders) {
            return null;
        }
    
        $result = [
            'orderID' => $orders[0]['orderID'],
            'totalAmount' => $orders[0]['totalAmount'],
            'products' => []
        ];
    
        foreach ($orders as $order) {
            $result['products'][] = [
                'productID' => $order['productID'],
                'name' => $order['productName'], 
                'quantity' => $order['quantity'],
                'price' => $order['priceAtTimeOfPurchase']
            ];
        }
    
        return $result;
    }
    
    
    public function clearCart() {
        unset($_SESSION['cart']);
    }

    public function getOrderData($orderID) {
        $sql = "SELECT * FROM orders WHERE orderID = :orderID";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':orderID', $orderID, PDO::PARAM_STR);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}

?>
