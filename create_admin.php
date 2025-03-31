<?php
// Database connection configuration
require_once 'config/database.php';

try {
    // Check if user already exists
    $stmt = $conn->prepare('SELECT COUNT(*) FROM users WHERE username = :username');
    $username = 'aydom';
    $stmt->bindParam(':username', $username);
    $stmt->execute();
    
    if($stmt->fetchColumn() > 0) {
        echo "<div style='margin: 20px; padding: 10px; background-color: #f8d7da; color: #721c24; border-radius: 5px;'>
                Admin user 'aydom' already exists. 
                <a href='login.php'>Go to login page</a>
              </div>";
    } else {
        // Create admin user
        $username = 'aydom';
        $email = 'admin@example.com';
        $password = password_hash('123456', PASSWORD_DEFAULT);
        $full_name = 'Aydom Admin';
        $is_admin = 1;
        
        $stmt = $conn->prepare('INSERT INTO users (username, email, password, full_name, is_admin) VALUES (:username, :email, :password, :full_name, :is_admin)');
        $stmt->bindParam(':username', $username);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':password', $password);
        $stmt->bindParam(':full_name', $full_name);
        $stmt->bindParam(':is_admin', $is_admin, PDO::PARAM_INT);
        $stmt->execute();
        
        echo "<div style='margin: 20px; padding: 10px; background-color: #d4edda; color: #155724; border-radius: 5px;'>
                Admin user 'aydom' created successfully! 
                <a href='login.php'>Go to login page</a>
              </div>";
    }
} catch (PDOException $e) {
    echo "<div style='margin: 20px; padding: 10px; background-color: #f8d7da; color: #721c24; border-radius: 5px;'>
            Error: " . $e->getMessage() . "
            <a href='index.php'>Return to home page</a>
          </div>";
}
?>