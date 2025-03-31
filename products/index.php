<?php
// Database connection configuration
require_once '../config/database.php';

// Start the session
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: ../login.php');
    exit();
}

$user_id = $_SESSION['user_id'];
$message = '';
$error = '';

// Delete product if requested
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $product_id = $_GET['delete'];
    
    try {
        $stmt = $conn->prepare("DELETE FROM produtos WHERE id = :id AND user_id = :user_id");
        $stmt->bindParam(':id', $product_id);
        $stmt->bindParam(':user_id', $user_id);
        $stmt->execute();
        
        if ($stmt->rowCount() > 0) {
            $message = "Product deleted successfully!";
        } else {
            $error = "Product not found or you don't have permission to delete it.";
        }
    } catch (PDOException $e) {
        $error = "Error deleting product: " . $e->getMessage();
    }
}

// Get all products with their categories
// Get all products
try {
    $stmt = $conn->prepare("
        SELECT id, nome, descricao, quantidade, preco
        FROM produtos
        WHERE user_id = :user_id
        ORDER BY nome
    ");
    $stmt->bindParam(':user_id', $user_id);
    $stmt->execute();
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Get categories for each product
    $product_categories = [];
    foreach ($products as $product) {
        $cat_stmt = $conn->prepare("
            SELECT c.nome
            FROM categorias c
            JOIN produto_categorias pc ON c.id = pc.categoria_id
            WHERE pc.produto_id = :produto_id
        ");
        $cat_stmt->bindParam(':produto_id', $product['id']);
        $cat_stmt->execute();
        $categories = $cat_stmt->fetchAll(PDO::FETCH_COLUMN);
        $product_categories[$product['id']] = !empty($categories) ? implode(', ', $categories) : 'Uncategorized';
    }
} catch (PDOException $e) {
    $error = "Error loading products: " . $e->getMessage();
}

// Include header
include_once '../includes/header.php';
?>

<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Products</h1>
        <a href="add.php" class="btn btn-primary">Add New Product</a>
    </div>
    
    <?php if ($message): ?>
        <div class="alert alert-success"><?php echo $message; ?></div>
    <?php endif; ?>
    
    <?php if ($error): ?>
        <div class="alert alert-danger"><?php echo $error; ?></div>
    <?php endif; ?>
    
    <?php if (empty($products)): ?>
        <div class="alert alert-info">No products found. Add your first product!</div>
    <?php else: ?>
        <div class="table-responsive">
            <table class="table table-striped table-hover">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Description</th>
                        <th>Category</th>
                        <th>Quantity</th>
                        <th>Price</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($products as $product): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($product['nome'] ?? ''); ?></td>
                            <td><?php echo htmlspecialchars($product['descricao'] ?? ''); ?></td>
                            <td><?php echo htmlspecialchars($product_categories[$product['id']] ?? 'Uncategorized'); ?></td>
                            <td><?php echo $product['quantidade']; ?></td>
                            <td>$<?php echo number_format($product['preco'], 2); ?></td>
                            <td>
                                <a href="edit.php?id=<?php echo $product['id']; ?>" class="btn btn-sm btn-primary">Edit</a>
                                <a href="index.php?delete=<?php echo $product['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this product?')">Delete</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
    
    <div class="mt-3">
        <a href="../index.php" class="btn btn-secondary">Back to Dashboard</a>
    </div>
</div>

<?php
// Include footer
include_once '../includes/footer.php';
?>