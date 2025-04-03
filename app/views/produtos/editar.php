<?php
// Verificar se o usuário está logado
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

// Verificar se o ID do produto foi fornecido
if (!isset($_GET['id'])) {
    $_SESSION['error_message'] = 'ID do produto não fornecido.';
    header('Location: ?pagina=produtos');
    exit();
}

// Obter dados do produto
$produto_id = $_GET['id'];
$productModel = new ProductModel($db);
$produto = $productModel->getProductById($produto_id);

// Verificar se o produto existe
if (!$produto) {
    $_SESSION['error_message'] = 'Produto não encontrado.';
    header('Location: ?pagina=produtos');
    exit();
}

// Obter categorias para o select
$categoryModel = new CategoryModel($db);
$categorias = $categoryModel->getAllCategories();
?>

<div class="container mt-4">
    <div class="row mb-4">
        <div class="col-md-6">
            <h2>Editar Produto</h2>
        </div>
        <div class="col-md-6 text-end">
            <a href="?pagina=produtos" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Voltar
            </a>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <form action="?pagina=produtos&acao=atualizar" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="id" value="<?php echo $produto['id']; ?>">
                
                <div class="mb-3">
                    <label for="nome" class="form-label">Nome do Produto</label>
                    <input type="text" class="form-control" id="nome" name="nome" value="<?php echo htmlspecialchars($produto['nome']); ?>" required>
                </div>

                <div class="mb-3">
                    <label for="categoria_id" class="form-label">Categoria</label>
                    <select class="form-select" id="categoria_id" name="categoria_id" required>
                        <option value="">Selecione uma categoria</option>
                        <?php foreach ($categorias as $categoria): ?>
                            <option value="<?php echo $categoria['id']; ?>" <?php echo ($categoria['id'] == $produto['categoria_id']) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($categoria['nome']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="mb-3">
                    <label for="descricao" class="form-label">Descrição</label>
                    <textarea class="form-control" id="descricao" name="descricao" rows="3"><?php echo htmlspecialchars($produto['descricao']); ?></textarea>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="quantidade" class="form-label">Quantidade</label>
                            <input type="number" class="form-control" id="quantidade" name="quantidade" min="0" value="<?php echo $produto['quantidade']; ?>" required>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="preco" class="form-label">Preço</label>
                            <div class="input-group">
                                <span class="input-group-text">R$</span>
                                <input type="number" class="form-control" id="preco" name="preco" step="0.01" min="0" value="<?php echo $produto['preco']; ?>" required>
                            </div>
                        </div>
                    </div>
                </div>

                <?php if (!empty($produto['imagem'])): ?>
                    <div class="mb-3">
                        <label class="form-label">Imagem Atual</label>
                        <div>
                            <img src="uploads/produtos/<?php echo htmlspecialchars($produto['imagem']); ?>" alt="Imagem do Produto" style="max-width: 200px; height: auto;" class="img-thumbnail">
                        </div>
                    </div>
                <?php endif; ?>

                <div class="mb-3">
                    <label for="imagem" class="form-label">Nova Imagem</label>
                    <input type="file" class="form-control" id="imagem" name="imagem" accept="image/*">
                    <small class="text-muted">Deixe em branco para manter a imagem atual</small>
                </div>

                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Atualizar Produto
                </button>
            </form>
        </div>
    </div>
</div>