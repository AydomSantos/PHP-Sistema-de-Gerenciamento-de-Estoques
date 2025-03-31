<?php
// Database connection configuration
require_once '../config/database.php';

// Start the session
session_start();

// Check if user is logged in and is admin
if (!isset($_SESSION['user_id']) || !isset($_SESSION['is_admin']) || !$_SESSION['is_admin']) {
    header('Location: ../index.php');
    $_SESSION['error'] = 'Acesso restrito. Apenas administradores podem adicionar produtos.';
    exit();
}

$user_id = $_SESSION['user_id'];
$message = '';
$error = '';

// Check if user has any categories
try {
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
        
        foreach ($default_categories as $category) {
            // Check if category already exists for this user
            $check_stmt = $conn->prepare("SELECT COUNT(*) as count FROM categorias WHERE nome = :nome AND user_id = :user_id");
            $check_stmt->bindParam(':nome', $category[0]);
            $check_stmt->bindParam(':user_id', $user_id);
            $check_stmt->execute();
            $exists = $check_stmt->fetch(PDO::FETCH_ASSOC)['count'] > 0;
            
            // Only insert if it doesn't exist
            if (!$exists) {
                $insert_stmt = $conn->prepare("INSERT INTO categorias (nome, descricao, user_id) VALUES (:nome, :descricao, :user_id)");
                $insert_stmt->bindParam(':nome', $category[0]);
                $insert_stmt->bindParam(':descricao', $category[1]);
                $insert_stmt->bindParam(':user_id', $user_id);
                $insert_stmt->execute();
            }
        }
    }
} catch (PDOException $e) {
    $error = "Error checking categories: " . $e->getMessage();
}

// Get categories for dropdown
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
            
            // Check if produto_categorias table exists, if not create it
            $stmt = $conn->query("SELECT name FROM sqlite_master WHERE type='table' AND name='produto_categorias'");
            if ($stmt->fetchColumn() === false) {
                $conn->exec("CREATE TABLE produto_categorias (
                    id INTEGER PRIMARY KEY AUTOINCREMENT,
                    produto_id INTEGER NOT NULL,
                    categoria_id INTEGER NOT NULL,
                    FOREIGN KEY (produto_id) REFERENCES produtos(id) ON DELETE CASCADE,
                    FOREIGN KEY (categoria_id) REFERENCES categorias(id) ON DELETE CASCADE,
                    UNIQUE(produto_id, categoria_id)
                )");
            }
            
            // Get current timestamp for SQLite
            $current_time = date('Y-m-d H:i:s');
            
            // Insert product
            $stmt = $conn->prepare("INSERT INTO produtos (nome, descricao, quantidade, preco, user_id, created_at) 
                                   VALUES (:nome, :descricao, :quantidade, :preco, :user_id, :created_at)");
            $stmt->bindParam(':nome', $nome);
            $stmt->bindParam(':descricao', $descricao);
            $stmt->bindParam(':quantidade', $quantidade);
            $stmt->bindParam(':preco', $preco);
            $stmt->bindParam(':user_id', $user_id);
            $stmt->bindParam(':created_at', $current_time);
            $stmt->execute();
            
            $product_id = $conn->lastInsertId();
            
            // Insert product categories
            $stmt = $conn->prepare("INSERT INTO produto_categorias (produto_id, categoria_id) VALUES (:produto_id, :categoria_id)");
            
            foreach ($categoria_ids as $categoria_id) {
                $stmt->bindParam(':produto_id', $product_id);
                $stmt->bindParam(':categoria_id', $categoria_id);
                $stmt->execute();
            }
            
            // Commit transaction
            $conn->commit();
            
            $message = "Product added successfully!";
            
            // Clear form data
            $nome = $descricao = '';
            $quantidade = $preco = 0;
            $categoria_ids = [];
            
        } catch (PDOException $e) {
            // Rollback transaction on error
            $conn->rollBack();
            $error = "Error adding product: " . $e->getMessage();
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
                    <h2>Add New Product</h2>
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
                            <input type="text" class="form-control" id="nome" name="nome" value="<?php echo isset($nome) ? htmlspecialchars($nome) : ''; ?>" required>
                        </div>
                        
                        <div class="mb-3">
                            <label for="descricao" class="form-label">Description</label>
                            <textarea class="form-control" id="descricao" name="descricao" rows="3"><?php echo isset($descricao) ? htmlspecialchars($descricao) : ''; ?></textarea>
                        </div>
                        
                        <div class="mb-3">
                            <label for="quantidade" class="form-label">Quantity</label>
                            <input type="number" class="form-control" id="quantidade" name="quantidade" value="<?php echo isset($quantidade) ? $quantidade : '0'; ?>" min="0" required>
                        </div>
                        
                        <div class="mb-3">
                            <label for="preco" class="form-label">Price</label>
                            <div class="input-group">
                                <span class="input-group-text">$</span>
                                <input type="number" class="form-control" id="preco" name="preco" value="<?php echo isset($preco) ? $preco : '0.00'; ?>" min="0.01" step="0.01" required>
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
                                                <?php echo (isset($categoria_ids) && in_array($category['id'], $categoria_ids)) ? 'checked' : ''; ?>>
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
                            <a href="../index.php" class="btn btn-secondary">Cancel</a>
                            <button type="submit" class="btn btn-primary">Add Product</button>
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