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

<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Editar Categoria</h1>
        <a href="?pagina=categorias" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Voltar
        </a>
    </div>

    <?php if (isset($_SESSION['form_errors'])): ?>
        <div class="alert alert-danger">
            <ul class="mb-0">
                <?php foreach ($_SESSION['form_errors'] as $error): ?>
                    <li><?= $error ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
        <?php unset($_SESSION['form_errors']); ?>
    <?php endif; ?>

    <div class="card">
        <div class="card-body">
            <form action="?pagina=categorias&acao=atualizar" method="POST">
                <input type="hidden" name="id" value="<?= $categoria['id'] ?>">
                
                <div class="mb-3">
                    <label for="nome" class="form-label">Nome da Categoria</label>
                    <input type="text" class="form-control <?= isset($_SESSION['form_errors']['nome']) ? 'is-invalid' : '' ?>" 
                           id="nome" name="nome" 
                           value="<?= isset($_SESSION['form_data']['nome']) ? htmlspecialchars($_SESSION['form_data']['nome']) : htmlspecialchars($categoria['nome']) ?>" 
                           required>
                    <?php if (isset($_SESSION['form_errors']['nome'])): ?>
                        <div class="invalid-feedback">
                            <?= $_SESSION['form_errors']['nome'] ?>
                        </div>
                    <?php endif; ?>
                    <?php unset($_SESSION['form_data']['nome']); ?>
                </div>

                <div class="mb-3">
                    <label for="descricao" class="form-label">Descrição</label>
                    <textarea class="form-control" id="descricao" name="descricao" rows="3"><?= isset($_SESSION['form_data']['descricao']) ? htmlspecialchars($_SESSION['form_data']['descricao']) : htmlspecialchars($categoria['descricao']) ?></textarea>
                    <?php unset($_SESSION['form_data']['descricao']); ?>
                </div>

                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Atualizar Categoria
                </button>
            </form>
        </div>
    </div>
</div>