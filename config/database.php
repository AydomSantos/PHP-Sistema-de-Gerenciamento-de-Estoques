<?php
class Database {
    private $host = "localhost";
    private $db_name = "estoque";
    private $username = "root";
    private $password = "";
    private $conn;
    
    public function getConnection() {
        $this->conn = null;
        
        try {
            // For SQLite
            $this->conn = new PDO("sqlite:" . __DIR__ . "/../database/estoque.db");
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->conn->exec("PRAGMA foreign_keys = ON;");
        } catch(PDOException $exception) {
            echo "Connection error: " . $exception->getMessage();
        }
        
        return $this->conn;
    }
}
?>