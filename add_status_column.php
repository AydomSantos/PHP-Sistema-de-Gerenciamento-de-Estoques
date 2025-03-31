<?php
// Database connection configuration
require_once 'config/database.php';

try {
    // Add status column to users table
    $conn->exec("ALTER TABLE users ADD COLUMN status VARCHAR(20) DEFAULT 'pending'");
    echo "<div style='margin: 20px; padding: 10px; background-color: #d4edda; color: #155724; border-radius: 5px;'>
            Successfully added status column to users table. 
            <a href='index.php'>Return to home page</a>
          </div>";
} catch (PDOException $e) {
    echo "<div style='margin: 20px; padding: 10px; background-color: #f8d7da; color: #721c24; border-radius: 5px;'>
            Error: " . $e->getMessage() . "
            <a href='index.php'>Return to home page</a>
          </div>";
}
?>