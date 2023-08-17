<?php
class User {
    private $conn;

    public function __construct($conn) {
        $this->conn = $conn;
    }
    

    public function isLoggedIn() {
        return isset($_SESSION['username']);
    }

    public function register($username, $password, $email) {
        // Check for existing user
        $stmt = $this->conn->prepare("SELECT * FROM users WHERE username = :username OR email = :email");
        $stmt->execute([':username' => $username, ':email' => $email]);
        if ($stmt->rowCount() > 0) {
            return false;  // User or email already exists
        }

        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $this->conn->prepare("INSERT INTO users (username, password, email) VALUES (:username, :password, :email)");
        return $stmt->execute([':username' => $username, ':password' => $hashedPassword, ':email' => $email]);
    }

    public function login($username, $password) {
        $stmt = $this->conn->prepare("SELECT * FROM users WHERE username = :username");
        $stmt->execute([':username' => $username]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($user && password_verify($password, $user['password'])) {
            return true;
        }
        return false;
    }

    public function logout() {
        if ($this->isLoggedIn()) {
            session_unset();
            session_destroy();
        }
    }
}

?>
