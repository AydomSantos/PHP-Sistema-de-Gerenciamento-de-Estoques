<div class="container mt-4">
    <div class="row mb-4">
        <div class="col-md-6">
            <h2>Gerenciamento de Produtos</h2>
        </div>
        <div class="col-md-6 text-end">
            <a href="index.php?pagina=produtos&acao=adicionar" class="btn btn-primary">
                <i class="fas fa-plus"></i> Adicionar Produto
            </a>
        </div>
    </div>

    <?php if (isset($_SESSION['mensagem'])): ?>
        <div class="alert alert-<?= $_SESSION['tipo_mensagem'] ?? 'info' ?>">
            <?= $_SESSION['mensagem'] ?>
        </div>
        <?php unset($_SESSION['mensagem'], $_SESSION['tipo_mensagem']); ?>
    <?php endif; ?>

    <div class="card">
        <div class="card-header">
            <h4>Lista de Produtos</h4>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nome</th>
                            <th>Categoria</th>
                            <th>Preço</th>
                            <th>Quantidade</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($produtos)): ?>
                            <tr>
                                <td colspan="6" class="text-center">Nenhum produto encontrado</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($produtos as $produto): ?>
                                <tr>
                                    <td><?= $produto['id'] ?></td>
                                    <td><?= $produto['nome'] ?></td>
                                    <td><?= $produto['categoria_nome'] ?? 'Sem categoria' ?></td>
                                    <td>R$ <?= number_format($produto['preco'], 2, ',', '.') ?></td>
                                    <td><?= $produto['qtd'] ?></td> <!-- Updated from 'quantidade' to 'qtd' -->
                                    <td>
                                        <a href="index.php?pagina=produtos&acao=editar&id=<?= $produto['id'] ?>" class="btn btn-sm btn-primary">Editar</a>
                                        <a href="index.php?pagina=produtos&acao=excluir&id=<?= $produto['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Tem certeza que deseja excluir este produto?')">Excluir</a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>