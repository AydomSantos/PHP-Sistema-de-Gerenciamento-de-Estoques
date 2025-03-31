<?php
// Database connection configuration
require_once '../config/database.php';

// Start the session
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Content-Type: application/json');
    echo json_encode(['error' => 'Not authenticated']);
    exit();
}

$user_id = $_SESSION['user_id'];
$response = [];

try {
    // Check if user has any categories
    $stmt = $conn->prepare("SELECT COUNT(*) as total FROM categorias WHERE user_id = :user_id");
    $stmt->bindParam(':user_id', $user_id);
    $stmt->execute();
    $category_count = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
    
    // If user has no categories, create default ones
    if ($category_count == 0) {
        $default_categories = [
            ['Eletrônicos', 'Produtos eletrônicos em geral'],
            ['Informática', 'Equipamentos e acessórios para computadores'],
            ['Escritório', 'Material de escritório e papelaria'],
            ['Móveis', 'Móveis para escritório e residência']
        ];
        
        $stmt = $conn->prepare("INSERT INTO categorias (nome, descricao, user_id) VALUES (:nome, :descricao, :user_id)");
        
        foreach ($default_categories as $category) {
            $stmt->bindParam(':nome', $category[0]);
            $stmt->bindParam(':descricao', $category[1]);
            $stmt->bindParam(':user_id', $user_id);
            $stmt->execute();
        }
    }
    
    // Get total products count
    $stmt = $conn->prepare("SELECT COUNT(*) as total FROM produtos WHERE user_id = :user_id");
    $stmt->bindParam(':user_id', $user_id);
    $stmt->execute();
    $response['total_products'] = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
    
    // Get low stock items (less than 5 in quantity)
    $stmt = $conn->prepare("SELECT COUNT(*) as total FROM produtos WHERE user_id = :user_id AND quantidade < 5");
    $stmt->bindParam(':user_id', $user_id);
    $stmt->execute();
    $response['low_stock'] = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
    
    // Get total categories
    $stmt = $conn->prepare("SELECT COUNT(*) as total FROM categorias WHERE user_id = :user_id");
    $stmt->bindParam(':user_id', $user_id);
    $stmt->execute();
    $response['total_categories'] = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
    
    // Get recent activities (latest 5 products added)
    $stmt = $conn->prepare("SELECT nome, created_at FROM produtos WHERE user_id = :user_id ORDER BY id DESC LIMIT 5");
    $stmt->bindParam(':user_id', $user_id);
    $stmt->execute();
    $response['recent_activities'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Return JSON response
    header('Content-Type: application/json');
    echo json_encode($response);
    
} catch (PDOException $e) {
    header('Content-Type: application/json');
    echo json_encode(['error' => $e->getMessage()]);
}
?>