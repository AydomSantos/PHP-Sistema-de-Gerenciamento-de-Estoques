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

// Process form submission for adding a category
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'add') {
    $nome = trim($_POST['nome']);
    $descricao = trim($_POST['descricao']);
    
    // Validate input
    if (empty($nome)) {
        $error = "Category name is required";
    } else {
        try {
            $stmt = $conn->prepare("INSERT INTO categorias (nome, descricao, user_id) VALUES (:nome, :descricao, :user_id)");
            $stmt->bindParam(':nome', $nome);
            $stmt->bindParam(':descricao', $descricao);
            $stmt->bindParam(':user_id', $user_id);
            $stmt->execute();
            
            $message = "Category added successfully!";
            
            // Clear form data
            $nome = $descricao = '';
        } catch (PDOException $e) {
            $error = "Error adding category: " . $e->getMessage();
        }
    }
}

// Process category deletion
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'delete') {
    $category_id = (int)$_POST['category_id'];
    
    try {
        // Check if category has products
        $stmt = $conn->prepare("SELECT COUNT(*) FROM produtos WHERE categoria_id = :categoria_id AND user_id = :user_id");
        $stmt->bindParam(':categoria_id', $category_id);
        $stmt->bindParam(':user_id', $user_id);
        $stmt->execute();
        
        if ($stmt->fetchColumn() > 0) {
            $error = "Cannot delete category because it has products associated with it";
        } else {
            $stmt = $conn->prepare("DELETE FROM categorias WHERE id = :id AND user_id = :user_id");
            $stmt->bindParam(':id', $category_id);
            $stmt->bindParam(':user_id', $user_id);
            $stmt->execute();
            
            $message = "Category deleted successfully!";
        }
    } catch (PDOException $e) {
        $error = "Error deleting category: " . $e->getMessage();
    }
}

// Get all categories
try {
    $stmt = $conn->prepare("SELECT id, nome, descricao FROM categorias WHERE user_id = :user_id ORDER BY nome");
    $stmt->bindParam(':user_id', $user_id);
    $stmt->execute();
    $categories = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $error = "Error loading categories: " . $e->getMessage();
}

// Include header
include_once '../includes/header.php';
?>

<div class="container mt-4">
    <div class="row">
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h2>Add Category</h2>
                </div>
                <div class="card-body">
                    <?php if ($message): ?>
                        <div class="alert alert-success"><?php echo $message; ?></div>
                    <?php endif; ?>
                    
                    <?php if ($error): ?>
                        <div class="alert alert-danger"><?php echo $error; ?></div>
                    <?php endif; ?>
                    
                    <form method="post">
                        <input type="hidden" name="action" value="add">
                        <div class="mb-3">
                            <label for="nome" class="form-label">Category Name</label>
                            <input type="text" class="form-control" id="nome" name="nome" value="<?php echo isset($nome) ? htmlspecialchars($nome) : ''; ?>" required>
                        </div>
                        
                        <div class="mb-3">
                            <label for="descricao" class="form-label">Description</label>
                            <textarea class="form-control" id="descricao" name="descricao" rows="3"><?php echo isset($descricao) ? htmlspecialchars($descricao) : ''; ?></textarea>
                        </div>
                        
                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary">Add Category</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h2>Categories</h2>
                </div>
                <div class="card-body">
                    <?php if (empty($categories)): ?>
                        <p>No categories found. Add your first category!</p>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Name</th>
                                        <th>Description</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($categories as $category): ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($category['nome']); ?></td>
                                            <td><?php echo htmlspecialchars($category['descricao']); ?></td>
                                            <td>
                                                <form method="post" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this category?');">
                                                    <input type="hidden" name="action" value="delete">
                                                    <input type="hidden" name="category_id" value="<?php echo $category['id']; ?>">
                                                    <button type="submit" class="btn btn-sm btn-danger">Delete</button>
                                                </form>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
    
    <div class="mt-3">
        <a href="../index.php" class="btn btn-secondary">Back to Dashboard</a>
    </div>
</div>

<?php
// Include footer
include_once '../includes/footer.php';
?>