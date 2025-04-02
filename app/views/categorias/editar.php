<?php
// Verificar se o usuário está logado
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

// Verificar se o ID da categoria foi fornecido
if (!isset($_GET['id'])) {
    $_SESSION['error_message'] = 'ID da categoria não fornecido.';
    header('Location: ?pagina=categorias');
    exit();
}

// Obter dados da categoria
$categoria_id = $_GET['id'];
$categoryModel = new CategoryModel($db);
$categoria = $categoryModel->getCategoryById($categoria_id);

// Verificar se a categoria existe
if (!$categoria) {
    $_SESSION['error_message'] = 'Categoria não encontrada.';
    header('Location: ?pagina=categorias');
    exit();
}
?>

<div class="container mt-4">
    <div class="row mb-4">
        <div class="col-md-6">
            <h2>Editar Categoria</h2>
        </div>
        <div class="col-md-6 text-end">
            <a href="?pagina=categorias" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Voltar
            </a>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <form action="?pagina=categorias&acao=atualizar" method="POST">
                <input type="hidden" name="id" value="<?php echo $categoria['id']; ?>">
                
                <div class="mb-3">
                    <label for="nome" class="form-label">Nome da Categoria</label>
                    <input type="text" class="form-control" id="nome" name="nome" value="<?php echo htmlspecialchars($categoria['nome']); ?>" required>
                </div>

                <div class="mb-3">
                    <label for="descricao" class="form-label">Descrição</label>
                    <textarea class="form-control" id="descricao" name="descricao" rows="3"><?php echo htmlspecialchars($categoria['descricao']); ?></textarea>
                </div>

                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Atualizar Categoria
                </button>
            </form>
        </div>
    </div>
</div>