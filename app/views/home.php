<div class="container py-4">
    <div class="p-5 mb-4 bg-light rounded-3">
        <div class="container-fluid py-5">
            <h1 class="display-5 fw-bold">Sistema de Gerenciamento de Estoque</h1>
            <p class="col-md-8 fs-4">Bem-vindo ao sistema de gerenciamento de estoque. Utilize o menu lateral para navegar entre as funcionalidades.</p>
        </div>
    </div>

    <div class="row align-items-md-stretch">
        <div class="col-md-6">
            <div class="h-100 p-5 text-white bg-primary rounded-3">
                <h2>Produtos</h2>
                <p>Gerencie o cadastro de produtos, controle quantidades e visualize o histórico de movimentações.</p>
                <a href="index.php?pagina=produtos" class="btn btn-outline-light">Ver Produtos</a>
            </div>
        </div>
        <div class="col-md-6">
            <div class="h-100 p-5 bg-light border rounded-3">
                <h2>Categorias</h2>
                <p>Organize seus produtos em categorias para facilitar a busca e o gerenciamento do estoque.</p>
                <a href="index.php?pagina=categorias" class="btn btn-outline-secondary">Ver Categorias</a>
            </div>
        </div>
    </div>

    <?php if (isset($_SESSION['user_cargo']) && $_SESSION['user_cargo'] == 'admin'): ?>
    <div class="row mt-4">
        <div class="col-md-12">
            <div class="h-100 p-5 bg-info text-white rounded-3">
                <h2>Administração</h2>
                <p>Como administrador, você tem acesso ao gerenciamento de usuários e outras configurações avançadas do sistema.</p>
                <a href="index.php?pagina=usuarios" class="btn btn-outline-light">Gerenciar Usuários</a>
            </div>
        </div>
    </div>
    <?php endif; ?>
</div>