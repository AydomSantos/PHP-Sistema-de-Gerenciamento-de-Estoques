<?php
// Iniciar sessão
session_start();

// Definir constantes do sistema
define('ROOT_PATH', dirname(__DIR__));
define('APP_PATH', ROOT_PATH . '/app');
define('CONFIG_PATH', ROOT_PATH . '/config');
define('PUBLIC_PATH', ROOT_PATH . '/public');

// Carregar configurações do banco de dados
require_once CONFIG_PATH . '/database.php';

// Obter conexão com o banco de dados
$conn = getConnection();

// Buscar estatísticas de produtos
$totalProdutos = 0;
$emEstoque = 0;
$estoqueBaixo = 0;
$semEstoque = 0;

try {
    // Contar total de produtos
    $stmt = $conn->query("SELECT COUNT(*) as total FROM produtos");
    $totalProdutos = $stmt->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;
    
    // Contar produtos em estoque (quantidade > 10)
    $stmt = $conn->query("SELECT COUNT(*) as total FROM produtos WHERE qtd > 10");
    $emEstoque = $stmt->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;
    
    // Contar produtos com estoque baixo (0 < quantidade <= 10)
    $stmt = $conn->query("SELECT COUNT(*) as total FROM produtos WHERE qtd > 0 AND qtd <= 10");
    $estoqueBaixo = $stmt->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;
    
    // Contar produtos sem estoque (quantidade = 0)
    $stmt = $conn->query("SELECT COUNT(*) as total FROM produtos WHERE qtd = 0");
    $semEstoque = $stmt->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;
    
    // Buscar lista de produtos
    $stmt = $conn->query("SELECT * FROM produtos ORDER BY data_cadastro DESC LIMIT 10");
    $produtos = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    // Em caso de erro, registrar e continuar
    error_log('Erro ao buscar dados: ' . $e->getMessage());
    $produtos = [];
}

// Verificar se há mensagem na sessão
$mensagem = null;
$tipoMensagem = null;

if (isset($_SESSION['mensagem'])) {
    $mensagem = $_SESSION['mensagem'];
    $tipoMensagem = $_SESSION['tipo_mensagem'] ?? 'info';
    
    // Limpar mensagem da sessão
    unset($_SESSION['mensagem']);
    unset($_SESSION['tipo_mensagem']);
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistema de Gerenciamento de Estoque</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary-color: #0d6efd;
            --primary-dark: #0a58ca;
            --secondary-color: #212529;
            --light-color: #f8f9fa;
            --border-radius: 0.5rem;
        }
        
        body {
            background-color: #f0f2f5;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }
        
        .navbar {
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            background: linear-gradient(135deg, var(--secondary-color), #000000) !important;
        }
        
        .navbar-brand {
            font-weight: 700;
            letter-spacing: 0.5px;
        }
        
        .nav-link {
            font-weight: 500;
            transition: all 0.3s;
            position: relative;
            padding: 0.5rem 1rem;
            margin: 0 0.2rem;
        }
        
        .nav-link:after {
            content: '';
            position: absolute;
            width: 0;
            height: 2px;
            background-color: var(--primary-color);
            bottom: 0;
            left: 50%;
            transform: translateX(-50%);
            transition: width 0.3s;
        }
        
        .nav-link:hover:after, .nav-link.active:after {
            width: 80%;
        }
        
        .card {
            border: none;
            border-radius: var(--border-radius);
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s, box-shadow 0.3s;
        }
        
        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 15px rgba(0, 0, 0, 0.1);
        }
        
        .card-header {
            background-color: white;
            border-bottom: 1px solid rgba(0, 0, 0, 0.05);
            border-top-left-radius: var(--border-radius) !important;
            border-top-right-radius: var(--border-radius) !important;
        }
        
        .btn-primary {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
            border-radius: var(--border-radius);
            padding: 0.5rem 1.5rem;
            font-weight: 500;
            transition: all 0.3s;
        }
        
        .btn-primary:hover {
            background-color: var(--primary-dark);
            border-color: var(--primary-dark);
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        
        .btn-sm {
            border-radius: 0.4rem;
            font-size: 0.8rem;
            padding: 0.25rem 0.7rem;
        }
        
        .table {
            border-radius: var(--border-radius);
            overflow: hidden;
        }
        
        .table thead {
            background: linear-gradient(135deg, var(--primary-color), var(--primary-dark));
        }
        
        .table thead th {
            color: white;
            font-weight: 500;
            border: none;
            padding: 1rem 0.75rem;
        }
        
        .table tbody tr {
            transition: background-color 0.3s;
        }
        
        .table tbody tr:hover {
            background-color: rgba(13, 110, 253, 0.05);
        }
        
        .table td {
            vertical-align: middle;
            padding: 0.75rem;
        }
        
        .pagination .page-link {
            color: var(--primary-color);
            border-radius: 0.3rem;
            margin: 0 0.2rem;
            border: none;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        }
        
        .pagination .page-item.active .page-link {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
        }
        
        .pagination .page-link:hover {
            background-color: rgba(13, 110, 253, 0.1);
            transform: translateY(-1px);
        }
        
        .stat-card {
            border-radius: var(--border-radius);
            padding: 1.5rem;
            height: 100%;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }
        
        .stat-card .card-title {
            font-size: 1rem;
            font-weight: 500;
            margin-bottom: 0.5rem;
        }
        
        .stat-card .card-text {
            font-size: 2rem;
            font-weight: 700;
            margin-bottom: 0;
        }
        
        .stat-card .icon {
            font-size: 2.5rem;
            opacity: 0.2;
            position: absolute;
            bottom: 1rem;
            right: 1rem;
        }
        
        footer {
            background-color: var(--secondary-color);
            color: rgba(255, 255, 255, 0.7);
            padding: 1.5rem 0;
            margin-top: auto;
        }
        
        .content-wrapper {
            flex: 1 0 auto;
        }
        
        .alert {
            border-radius: var(--border-radius);
            border: none;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container">
            <a class="navbar-brand" href="index.php">
                <i class="fas fa-boxes me-2"></i>StockPro
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link active" href="index.php">
                            <i class="fas fa-tachometer-alt me-1"></i>Dashboard
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="produtos.php">
                            <i class="fas fa-box me-1"></i>Produtos
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="categorias.php">
                            <i class="fas fa-tags me-1"></i>Categorias
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="relatorios.php">
                            <i class="fas fa-chart-bar me-1"></i>Relatórios
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        <?php if ($mensagem): ?>
        <div class="alert alert-<?= $tipoMensagem ?> alert-dismissible fade show" role="alert">
            <?= htmlspecialchars($mensagem) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fechar"></button>
        </div>
        <?php endif; ?>

        <div class="row">
            <div class="col-md-3 mb-4">
                <div class="card h-100">
                    <div class="card-body stat-card bg-primary text-white">
                        <h5 class="card-title">Total de Produtos</h5>
                        <p class="card-text"><?= $totalProdutos ?></p>
                        <i class="fas fa-boxes icon"></i>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-4">
                <div class="card h-100">
                    <div class="card-body stat-card bg-success text-white">
                        <h5 class="card-title">Em Estoque</h5>
                        <p class="card-text"><?= $emEstoque ?></p>
                        <i class="fas fa-check-circle icon"></i>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-4">
                <div class="card h-100">
                    <div class="card-body stat-card bg-warning text-white">
                        <h5 class="card-title">Estoque Baixo</h5>
                        <p class="card-text"><?= $estoqueBaixo ?></p>
                        <i class="fas fa-exclamation-triangle icon"></i>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-4">
                <div class="card h-100">
                    <div class="card-body stat-card bg-danger text-white">
                        <h5 class="card-title">Sem Estoque</h5>
                        <p class="card-text"><?= $semEstoque ?></p>
                        <i class="fas fa-times-circle icon"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="card mt-4">
            <div class="card-header d-flex justify-content-between align-items-center py-3">
                <h5 class="card-title mb-0">
                    <i class="fas fa-list me-2"></i>Lista de Produtos
                </h5>
                <a href="produto_form.php" class="btn btn-primary">
                    <i class="fas fa-plus me-1"></i>Novo Produto
                </a>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
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
                            <?php if (count($produtos) > 0): ?>
                                <?php foreach ($produtos as $produto): ?>
                                <tr>
                                    <td><?= htmlspecialchars($produto['codigo'] ?? 'N/A') ?></td>
                                    <td><?= htmlspecialchars($produto['nome']) ?></td>
                                    <td>R$ <?= number_format($produto['preco'], 2, ',', '.') ?></td>
                                    <td>
                                        <span class="badge bg-<?= $produto['qtd'] > 10 ? 'success' : ($produto['qtd'] > 0 ? 'warning' : 'danger') ?> rounded-pill">
                                            <?= $produto['qtd'] ?>
                                        </span>
                                    </td>
                                    <td><?= htmlspecialchars($produto['unidade']) ?></td>
                                    <td><?= htmlspecialchars($produto['ncm'] ?? 'N/A') ?></td>
                                    <td><?= date('d/m/Y', strtotime($produto['data_cadastro'])) ?></td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <a href="produto_editar.php?id=<?= $produto['id'] ?>" class="btn btn-sm btn-primary">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <a href="produto_excluir.php?id=<?= $produto['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Tem certeza que deseja excluir este produto?')">
                                                <i class="fas fa-trash"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="8" class="text-center py-4">
                                        <i class="fas fa-box-open text-muted mb-3" style="font-size: 3rem;"></i>
                                        <p class="mb-0">Nenhum produto cadastrado</p>
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>

                <?php if (count($produtos) > 0): ?>
                <nav aria-label="Navegação de página">
                    <ul class="pagination justify-content-center mt-4">
                        <li class="page-item disabled">
                            <a class="page-link" href="#" tabindex="-1">
                                <i class="fas fa-chevron-left"></i>
                            </a>
                        </li>
                        <li class="page-item active"><a class="page-link" href="#">1</a></li>
                        <li class="page-item"><a class="page-link" href="#">2</a></li>
                        <li class="page-item"><a class="page-link" href="#">3</a></li>
                        <li class="page-item">
                            <a class="page-link" href="#">
                                <i class="fas fa-chevron-right"></i>
                            </a>
                        </li>
                    </ul>
                </nav>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <footer class="text-center py-4">
        <div class="container">
            <p class="mb-0">Sistema de Gerenciamento de Estoque &copy; <?= date('Y') ?></p>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
