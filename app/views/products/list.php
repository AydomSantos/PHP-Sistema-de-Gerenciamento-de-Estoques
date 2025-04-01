<?php
// Include header
require_once __DIR__ . '/../../includes/header.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: /login.php');
    exit();
}

// Get products from controller
require_once __DIR__ . '/../../controllers/ProductController.php';
require_once __DIR__ . '/../../config/database.php';

$productController = new ProductController($conn);
$result = $productController->getAllProducts();
$products = $result['produtos'] ?? [];

// Handle search
$search = $_GET['search'] ?? '';
if (!empty($search)) {
    // Filter products by search term (this would be better handled in the controller)
    $filteredProducts = [];
    foreach ($products as $product) {
        if (stripos($product['nome'], $search) !== false || 
            stripos($product['descricao'], $search) !== false || 
            stripos($product['codigo'], $search) !== false) {
            $filteredProducts[] = $product;
        }
    }
    $products = $filteredProducts;
}
?>

<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Gerenciamento de Produtos</h1>
        <a href="/app/views/products/add.php" class="btn btn-primary">
            <i class="fas fa-plus"></i> Novo Produto
        </a>
    </div>

    <?php if (isset($_SESSION['message'])): ?>
        <div class="alert alert-<?= $_SESSION['message_type'] ?> alert-dismissible fade show">
            <?= $_SESSION['message'] ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        <?php 
        // Clear the message after displaying
        unset($_SESSION['message']); 
        unset($_SESSION['message_type']);
        ?>
    <?php endif; ?>

    <div class="card mb-4">
        <div class="card-header bg-light">
            <form action="" method="GET" class="row g-3">
                <div class="col-md-6">
                    <div class="input-group">
                        <input type="text" class="form-control" placeholder="Buscar produtos..." name="search" value="<?= htmlspecialchars($search) ?>">
                        <button class="btn btn-outline-secondary" type="submit">
                            <i class="fas fa-search"></i>
                        </button>
                    </div>
                </div>
                <div class="col-md-6 text-end">
                    <a href="?export=excel" class="btn btn-success">
                        <i class="fas fa-file-excel"></i> Exportar Excel
                    </a>
                    <a href="?export=pdf" class="btn btn-danger">
                        <i class="fas fa-file-pdf"></i> Exportar PDF
                    </a>
                </div>
            </form>
        </div>
        <div class="card-body">
            <?php if (empty($products)): ?>
                <div class="alert alert-info">
                    Nenhum produto encontrado. <a href="/app/views/products/add.php">Adicionar um novo produto</a>.
                </div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead class="table-dark">
                            <tr>
                                <th>Código</th>
                                <th>Nome</th>
                                <th>Preço</th>
                                <th>Quantidade</th>
                                <th>Unidade</th>
                                <th>NCM</th>
                                <th>Data Cadastro</th>
                                <th>Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($products as $product): ?>
                                <tr>
                                    <td><?= htmlspecialchars($product['codigo'] ?? 'N/A') ?></td>
                                    <td><?= htmlspecialchars($product['nome']) ?></td>
                                    <td>R$ <?= number_format($product['preco'], 2, ',', '.') ?></td>
                                    <td><?= number_format($product['qtd'], 0, ',', '.') ?></td>
                                    <td><?= htmlspecialchars($product['unidade']) ?></td>
                                    <td><?= htmlspecialchars($product['ncm'] ?? 'N/A') ?></td>
                                    <td><?= date('d/m/Y H:i', strtotime($product['data_cadastro'])) ?></td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <a href="/app/views/products/view.php?id=<?= $product['id'] ?>" class="btn btn-sm btn-info" title="Visualizar">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="/app/views/products/edit.php?id=<?= $product['id'] ?>" class="btn btn-sm btn-warning" title="Editar">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <button type="button" class="btn btn-sm btn-danger" 
                                                    data-bs-toggle="modal" 
                                                    data-bs-target="#deleteModal" 
                                                    data-id="<?= $product['id'] ?>"
                                                    data-name="<?= htmlspecialchars($product['nome']) ?>"
                                                    title="Excluir">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                
                <!-- Pagination -->
                <nav aria-label="Page navigation">
                    <ul class="pagination justify-content-center">
                        <li class="page-item disabled">
                            <a class="page-link" href="#" tabindex="-1">Anterior</a>
                        </li>
                        <li class="page-item active"><a class="page-link" href="#">1</a></li>
                        <li class="page-item"><a class="page-link" href="#">2</a></li>
                        <li class="page-item"><a class="page-link" href="#">3</a></li>
                        <li class="page-item">
                            <a class="page-link" href="#">Próximo</a>
                        </li>
                    </ul>
                </nav>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title" id="deleteModalLabel">Confirmar Exclusão</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Tem certeza que deseja excluir o produto <strong id="productName"></strong>?</p>
                <p class="text-danger"><small>Esta ação não pode ser desfeita.</small></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <form id="deleteForm" action="/app/controllers/product_delete.php" method="POST">
                    <input type="hidden" name="id" id="productId">
                    <button type="submit" class="btn btn-danger">Excluir</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    // Set product data in delete modal
    document.addEventListener('DOMContentLoaded', function() {
        const deleteModal = document.getElementById('deleteModal');
        if (deleteModal) {
            deleteModal.addEventListener('show.bs.modal', function(event) {
                const button = event.relatedTarget;
                const id = button.getAttribute('data-id');
                const name = button.getAttribute('data-name');
                
                document.getElementById('productId').value = id;
                document.getElementById('productName').textContent = name;
            });
        }
    });
</script>

<?php
// Include footer
require_once __DIR__ . '/../../includes/footer.php';
?>