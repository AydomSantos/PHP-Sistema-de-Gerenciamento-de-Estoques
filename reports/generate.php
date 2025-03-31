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
$error = '';

// Get all products with their categories for the current user
try {
    $stmt = $conn->prepare("
        SELECT p.id, p.nome, p.descricao, p.quantidade, p.preco
        FROM produtos p
        WHERE p.user_id = :user_id
        ORDER BY p.nome
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
    
    // Calculate totals
    $total_products = count($products);
    $total_items = 0;
    $total_value = 0;
    
    foreach ($products as $product) {
        $total_items += $product['quantidade'];
        $total_value += $product['quantidade'] * $product['preco'];
    }
    
} catch (PDOException $e) {
    $error = "Error generating report: " . $e->getMessage();
}

// Include header
include_once '../includes/header.php';
?>

<div class="container mt-4">
    <div class="card mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h2>Inventory Report</h2>
            <button id="printButton" class="btn btn-primary">Print Report</button>
        </div>
        <div class="card-body">
            <?php if ($error): ?>
                <div class="alert alert-danger"><?php echo $error; ?></div>
            <?php else: ?>
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Product</th>
                            <th>Description</th>
                            <th>Category</th>
                            <th class="text-end">Quantity</th>
                            <th class="text-end">Price</th>
                            <th class="text-end">Total Value</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($products as $product): ?>
                            <?php $item_total = $product['quantidade'] * $product['preco']; ?>
                            <tr>
                                <td><?php echo htmlspecialchars($product['nome'] ?? ''); ?></td>
                                <td><?php echo htmlspecialchars($product['descricao'] ?? ''); ?></td>
                                <td><?php echo htmlspecialchars($product_categories[$product['id']] ?? 'Uncategorized'); ?></td>
                                <td class="text-end"><?php echo $product['quantidade']; ?></td>
                                <td class="text-end">$<?php echo number_format($product['preco'], 2); ?></td>
                                <td class="text-end">$<?php echo number_format($item_total, 2); ?></td>
                            </tr>
                        <?php endforeach; ?>
                        <tr class="table-dark">
                            <th colspan="3">Totals</th>
                            <th class="text-end"><?php echo $total_items; ?> items</th>
                            <th></th>
                            <th class="text-end">$<?php echo number_format($total_value, 2); ?></th>
                        </tr>
                    </tbody>
                </table>
                
                <div class="row mt-5">
                    <h3>Inventory Summary</h3>
                    <div class="col-md-4">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title">Total Products</h5>
                                <p class="card-text fs-2"><?php echo $total_products; ?></p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title">Total Items</h5>
                                <p class="card-text fs-2"><?php echo $total_items; ?></p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title">Total Inventory Value</h5>
                                <p class="card-text fs-2">$<?php echo number_format($total_value, 2); ?></p>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
    
    <div class="mb-3">
        <a href="../index.php" class="btn btn-secondary">Back to Dashboard</a>
    </div>
</div>

<script>
document.getElementById('printButton').addEventListener('click', function() {
    window.print();
});
</script>

<?php
// Include footer
include_once '../includes/footer.php';
?>