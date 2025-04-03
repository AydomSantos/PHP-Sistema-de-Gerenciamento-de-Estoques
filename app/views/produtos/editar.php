<?php
// Verificar se o usuário está logado
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}
?>

<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Editar Produto</h1>
        <a href="index.php?pagina=produtos" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Voltar
        </a>
    </div>

    <?php if (isset($_SESSION['mensagem'])): ?>
        <div class="alert alert-<?= $_SESSION['tipo_mensagem'] ?> alert-dismissible fade show">
            <?= $_SESSION['mensagem'] ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        <?php 
        unset($_SESSION['mensagem']);
        unset($_SESSION['tipo_mensagem']);
        ?>
    <?php endif; ?>

    <div class="card">
        <div class="card-body">
            <form action="index.php?pagina=produtos&acao=atualizar" method="POST">
                <input type="hidden" name="id" value="<?= $produto['id'] ?>">
                
                <div class="mb-3">
                    <label for="nome" class="form-label">Nome do Produto</label>
                    <input type="text" class="form-control" id="nome" name="nome" value="<?= htmlspecialchars($produto['nome']) ?>" required>
                </div>

                <div class="mb-3">
                    <label for="descricao" class="form-label">Descrição</label>
                    <textarea class="form-control" id="descricao" name="descricao" rows="3"><?= htmlspecialchars($produto['descricao']) ?></textarea>
                </div>

                <div class="mb-3">
                    <label for="categoria_id" class="form-label">Categoria</label>
                    <select class="form-select" id="categoria_id" name="categoria_id" required>
                        <option value="">Selecione uma categoria</option>
                        <?php foreach ($categorias as $categoria): ?>
                            <option value="<?= $categoria['id'] ?>" <?= $produto['categoria_id'] == $categoria['id'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($categoria['nome']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="preco" class="form-label">Preço (R$)</label>
                        <input type="number" class="form-control" id="preco" name="preco" step="0.01" min="0" value="<?= $produto['preco'] ?>" required>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="quantidade" class="form-label">Quantidade em Estoque</label>
                        <input type="number" class="form-control" id="quantidade" name="quantidade" min="0" value="<?= $produto['quantidade'] ?>" required>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Atualizar Produto
                </button>
            </form>
        </div>
    </div>
</div>