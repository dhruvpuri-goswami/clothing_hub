<?php 

class Product {
    private $conn;

    public function __construct($conn) {
        $this->conn = $conn;
    }

    public function getFeaturedProducts() {
        try {
            $stmt = $this->conn->prepare("SELECT * FROM Products LIMIT 8");
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            die("Error: " . $e->getMessage());
        }
    }

    public function getAllProducts($filters = []) {
        $sql = "SELECT * FROM products";
        
        $conditions = [];
        if (isset($filters['category']) && $filters['category']) {
            $conditions[] = "category = :category";
        }
        if (isset($filters['size']) && $filters['size']) {
            $conditions[] = "size = :size";
        }
        // Assuming price is a range like "100-200"
        if (isset($filters['price']) && $filters['price']) {
            $priceRange = explode('-', $filters['price']);
            $conditions[] = "price BETWEEN :priceMin AND :priceMax";
        }
    
        if ($conditions) {
            $sql .= " WHERE " . implode(' AND ', $conditions);
        }
    
        $stmt = $this->conn->prepare($sql);
        
        if (isset($filters['category']) && $filters['category']) {
            $stmt->bindParam(':category', $filters['category']);
        }
        if (isset($filters['size']) && $filters['size']) {
            $stmt->bindParam(':size', $filters['size']);
        }
        if (isset($filters['price']) && $filters['price']) {
            $stmt->bindParam(':priceMin', $priceRange[0]);
            $stmt->bindParam(':priceMax', $priceRange[1]);
        }
    
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    

    public function getDetails($productId) {
        try {
            $stmt = $this->conn->prepare("SELECT * FROM Products WHERE productID = :productID");
            $stmt->bindParam(':productID', $productId);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            die("Error: " . $e->getMessage());
        }
    }

    public function getCategories() {
        $sql = "SELECT DISTINCT category FROM products";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }
    
    public function getDistinctPrices() {
        $sql = "SELECT DISTINCT price FROM products ORDER BY price ASC";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_COLUMN, 0);
    }

    public function getProductByID($productID) {
        $stmt = $this->conn->prepare("SELECT * FROM products WHERE productID = :productID");
        $stmt->bindParam(':productID', $productID);
        $stmt->execute();
    
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    

    public function getFilteredProducts($priceRange = null, $category = null) {
        $sql = "SELECT * FROM products WHERE 1"; // The "WHERE 1" is a trick to easily append conditions
        
        if ($priceRange) {
            list($minPrice, $maxPrice) = explode('-', $priceRange);
            $sql .= " AND price BETWEEN :minPrice AND :maxPrice";
        }
        
        if ($category) {
            $sql .= " AND category = :category";
        }
        
        $stmt = $this->conn->prepare($sql);
        
        if ($priceRange) {
            $stmt->bindParam(':minPrice', $minPrice, PDO::PARAM_INT);
            $stmt->bindParam(':maxPrice', $maxPrice, PDO::PARAM_INT);
        }
        
        if ($category) {
            $stmt->bindParam(':category', $category, PDO::PARAM_STR);
        }
        
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    
}



?>