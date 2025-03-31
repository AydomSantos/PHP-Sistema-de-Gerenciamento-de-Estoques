<?php
// Database connection configuration
require_once '../config/database.php';

// Start the session
session_start();

// Check if user is logged in and is admin
if (!isset($_SESSION['user_id']) || !isset($_SESSION['is_admin']) || !$_SESSION['is_admin']) {
    header('Location: ../index.php');
    $_SESSION['error'] = 'Acesso restrito. Apenas administradores podem editar produtos.';
    exit();
}

$user_id = $_SESSION['user_id'];
$message = '';
$error = '';

// Check if product ID is provided
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header('Location: index.php');
    exit();
}

$product_id = $_GET['id'];

// Get product data
try {
    $stmt = $conn->prepare("SELECT * FROM produtos WHERE id = :id AND user_id = :user_id");
    $stmt->bindParam(':id', $product_id);
    $stmt->bindParam(':user_id', $user_id);
    $stmt->execute();
    $product = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$product) {
        header('Location: index.php');
        exit();
    }
    
    // Get selected categories for this product
    $cat_stmt = $conn->prepare("
        SELECT categoria_id 
        FROM produto_categorias 
        WHERE produto_id = :produto_id
    ");
    $cat_stmt->bindParam(':produto_id', $product_id);
    $cat_stmt->execute();
    $selected_categories = $cat_stmt->fetchAll(PDO::FETCH_COLUMN);
    
} catch (PDOException $e) {
    $error = "Error loading product: " . $e->getMessage();
}

// Get all categories
try {
    $stmt = $conn->prepare("SELECT id, nome FROM categorias WHERE user_id = :user_id ORDER BY nome");
    $stmt->bindParam(':user_id', $user_id);
    $stmt->execute();
    $categories = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $error = "Error loading categories: " . $e->getMessage();
}

// Process form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = trim($_POST['nome']);
    $descricao = trim($_POST['descricao']);
    $quantidade = (int)$_POST['quantidade'];
    $preco = (float)$_POST['preco'];
    $categoria_ids = isset($_POST['categoria_ids']) ? $_POST['categoria_ids'] : [];
    
    // Validate input
    if (empty($nome)) {
        $error = "Product name is required";
    } elseif ($quantidade < 0) {
        $error = "Quantity cannot be negative";
    } elseif ($preco <= 0) {
        $error = "Price must be greater than zero";
    } elseif (empty($categoria_ids)) {
        $error = "Please select at least one category";
    } else {
        try {
            // Start transaction
            $conn->beginTransaction();
            
            // Update product
            $stmt = $conn->prepare("
                UPDATE produtos 
                SET nome = :nome, descricao = :descricao, quantidade = :quantidade, preco = :preco 
                WHERE id = :id AND user_id = :user_id
            ");
            $stmt->bindParam(':nome', $nome);
            $stmt->bindParam(':descricao', $descricao);
            $stmt->bindParam(':quantidade', $quantidade);
            $stmt->bindParam(':preco', $preco);
            $stmt->bindParam(':id', $product_id);
            $stmt->bindParam(':user_id', $user_id);
            $stmt->execute();
            
            // Delete existing category relationships
            $stmt = $conn->prepare("DELETE FROM produto_categorias WHERE produto_id = :produto_id");
            $stmt->bindParam(':produto_id', $product_id);
            $stmt->execute();
            
            // Insert new category relationships
            $stmt = $conn->prepare("INSERT INTO produto_categorias (produto_id, categoria_id) VALUES (:produto_id, :categoria_id)");
            
            foreach ($categoria_ids as $categoria_id) {
                $stmt->bindParam(':produto_id', $product_id);
                $stmt->bindParam(':categoria_id', $categoria_id);
                $stmt->execute();
            }
            
            // Commit transaction
            $conn->commit();
            
            $message = "Product updated successfully!";
            
            // Refresh product data
            $stmt = $conn->prepare("SELECT * FROM produtos WHERE id = :id AND user_id = :user_id");
            $stmt->bindParam(':id', $product_id);
            $stmt->bindParam(':user_id', $user_id);
            $stmt->execute();
            $product = $stmt->fetch(PDO::FETCH_ASSOC);
            
            // Refresh selected categories
            $cat_stmt = $conn->prepare("
                SELECT categoria_id 
                FROM produto_categorias 
                WHERE produto_id = :produto_id
            ");
            $cat_stmt->bindParam(':produto_id', $product_id);
            $cat_stmt->execute();
            $selected_categories = $cat_stmt->fetchAll(PDO::FETCH_COLUMN);
            
        } catch (PDOException $e) {
            // Rollback transaction on error
            $conn->rollBack();
            $error = "Error updating product: " . $e->getMessage();
        }
    }
}

// Include header
include_once '../includes/header.php';
?>

<div class="container mt-4">
    <div class="row">
        <div class="col-md-8 offset-md-2">
            <div class="card">
                <div class="card-header">
                    <h2>Edit Product</h2>
                </div>
                <div class="card-body">
                    <?php if ($message): ?>
                        <div class="alert alert-success"><?php echo $message; ?></div>
                    <?php endif; ?>
                    
                    <?php if ($error): ?>
                        <div class="alert alert-danger"><?php echo $error; ?></div>
                    <?php endif; ?>
                    
                    <form method="post">
                        <div class="mb-3">
                            <label for="nome" class="form-label">Product Name</label>
                            <input type="text" class="form-control" id="nome" name="nome" value="<?php echo htmlspecialchars($product['nome'] ?? ''); ?>" required>
                        </div>
                        
                        <div class="mb-3">
                            <label for="descricao" class="form-label">Description</label>
                            <textarea class="form-control" id="descricao" name="descricao" rows="3"><?php echo htmlspecialchars($product['descricao'] ?? ''); ?></textarea>
                        </div>
                        
                        <div class="mb-3">
                            <label for="quantidade" class="form-label">Quantity</label>
                            <input type="number" class="form-control" id="quantidade" name="quantidade" value="<?php echo $product['quantidade'] ?? 0; ?>" min="0" required>
                        </div>
                        
                        <div class="mb-3">
                            <label for="preco" class="form-label">Price</label>
                            <div class="input-group">
                                <span class="input-group-text">$</span>
                                <input type="number" class="form-control" id="preco" name="preco" value="<?php echo $product['preco'] ?? 0.00; ?>" min="0.01" step="0.01" required>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Categories</label>
                            <div class="card p-3">
                                <div class="row">
                                    <?php foreach ($categories as $category): ?>
                                    <div class="col-md-6 mb-2">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="categoria_ids[]" 
                                                value="<?php echo $category['id']; ?>" 
                                                id="category_<?php echo $category['id']; ?>"
                                                <?php echo (in_array($category['id'], $selected_categories)) ? 'checked' : ''; ?>>
                                            <label class="form-check-label" for="category_<?php echo $category['id']; ?>">
                                                <?php echo htmlspecialchars($category['nome']); ?>
                                            </label>
                                        </div>
                                    </div>
                                    <?php endforeach; ?>
                                </div>
                                <?php if (empty($categories)): ?>
                                <div class="alert alert-warning mb-0">
                                    No categories available. <a href="../categories/manage.php">Add categories</a> first.
                                </div>
                                <?php endif; ?>
                            </div>
                        </div>
                        
                        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                            <a href="index.php" class="btn btn-secondary">Cancel</a>
                            <button type="submit" class="btn btn-primary">Update Product</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
// Include footer
include_once '../includes/footer.php';
?>