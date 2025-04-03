<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Gerenciamento de Categorias</h1>
        <a href="index.php?pagina=categorias&acao=adicionar" class="btn btn-primary">
            <i class="fas fa-plus"></i> Nova Categoria
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
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nome</th>
                            <th>Descrição</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($categorias)): ?>
                            <?php foreach ($categorias as $categoria): ?>
                            <tr>
                                <td><?= $categoria['id'] ?></td>
                                <td><?= htmlspecialchars($categoria['nome']) ?></td>
                                <td><?= htmlspecialchars($categoria['descricao']) ?></td>
                                <td>
                                    <a href="?pagina=categorias&acao=editar&id=<?= $categoria['id'] ?>" 
                                       class="btn btn-sm btn-warning" title="Editar">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <a href="?pagina=categorias&acao=excluir&id=<?= $categoria['id'] ?>" 
                                       class="btn btn-sm btn-danger" 
                                       title="Excluir"
                                       onclick="return confirm('Tem certeza que deseja excluir esta categoria?')">
                                        <i class="fas fa-trash"></i>
                                    </a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="4" class="text-center">Nenhuma categoria encontrada</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>