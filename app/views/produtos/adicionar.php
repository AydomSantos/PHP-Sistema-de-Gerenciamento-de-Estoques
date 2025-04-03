<?php
// Verificar se o usuário está logado
if (!isset($_SESSION['usuario_id'])) {
    header('Location: index.php?pagina=login');
    exit();
}
?>

<div class="container mt-4">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h4>Adicionar Novo Produto</h4>
                </div>
                <div class="card-body">
                    <?php if (isset($_SESSION['mensagem'])): ?>
                        <div class="alert alert-<?= $_SESSION['tipo_mensagem'] ?? 'info' ?>">
                            <?= $_SESSION['mensagem'] ?>
                        </div>
                        <?php
                        unset($_SESSION['mensagem']);
                        unset($_SESSION['tipo_mensagem']);
                        ?>
                    <?php endif; ?>
                    
                    <form action="index.php?pagina=produtos&acao=adicionarProduto" method="post">
                        <div class="form-group mb-3">
                            <label for="nome">Nome do Produto</label>
                            <input type="text" class="form-control" id="nome" name="nome" required>
                        </div>
                        
                        <div class="form-group mb-3">
                            <label for="descricao">Descrição</label>
                            <textarea class="form-control" id="descricao" name="descricao" rows="3"></textarea>
                        </div>
                        
                        <div class="form-group mb-3">
                            <label for="categoria_id">Categoria</label>
                            <select class="form-control" id="categoria_id" name="categoria_id" required>
                                <option value="">Selecione uma categoria</option>
                                <?php foreach ($categorias as $categoria): ?>
                                    <option value="<?= $categoria['id'] ?>"><?= $categoria['nome'] ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <div class="form-group mb-3">
                            <label for="preco">Preço</label>
                            <input type="number" class="form-control" id="preco" name="preco" step="0.01" min="0" required>
                        </div>
                        
                        <div class="form-group mb-3">
                            <label for="quantidade">Quantidade</label>
                            <input type="number" class="form-control" id="quantidade" name="quantidade" min="0" required>
                        </div>
                        
                        <div class="form-group">
                            <button type="submit" class="btn btn-primary">Salvar</button>
                            <a href="index.php?pagina=produtos" class="btn btn-secondary">Cancelar</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Seção de produtos cadastrados -->
    <div class="row mt-4">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h4>Produtos Cadastrados</h4>
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
                                            <td><?= $produto['quantidade'] ?></td>
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
    </div>
</div>