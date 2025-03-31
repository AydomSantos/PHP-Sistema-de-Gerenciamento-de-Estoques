<?php
session_start();

class Auth {
    private $db;

    public function __construct($conn) {
        $this->db = $conn;
    }

    public function login($username, $password) {
        try {
            $stmt = $this->db->prepare("SELECT * FROM users WHERE username = ?");
            $stmt->execute([$username]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($user && password_verify($password, $user['password'])) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['is_admin'] = $user['is_admin'];
                return true;
            }
            return false;
        } catch(PDOException $e) {
            error_log("Erro no login: " . $e->getMessage());
            return false;
        }
    }

    public function register($username, $password, $email, $full_name, $is_admin = 0) {
        try {
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $this->db->prepare("INSERT INTO users (username, password, email, full_name, is_admin) VALUES (?, ?, ?, ?, ?)");
            return $stmt->execute([$username, $hashed_password, $email, $full_name, $is_admin]);
        } catch(PDOException $e) {
            error_log("Erro no registro: " . $e->getMessage());
            return false;
        }
    }

    public function isLoggedIn() {
        return isset($_SESSION['user_id']);
    }

    public function isAdmin() {
        return isset($_SESSION['is_admin']) && $_SESSION['is_admin'] == 1;
    }

    public function logout() {
        session_unset();
        session_destroy();
    }

    public function requireLogin() {
        if (!$this->isLoggedIn()) {
            header('Location: /login.php');
            exit();
        }
    }

    public function requireAdmin() {
        $this->requireLogin();
        if (!$this->isAdmin()) {
            header('Location: /index.php');
            exit();
        }
    }

    public function getCurrentUserId() {
        return $_SESSION['user_id'] ?? null;
    }
}