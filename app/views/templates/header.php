<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistema de Gerenciamento de Estoque</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .sidebar {
            min-height: calc(100vh - 56px);
            background-color: #f8f9fa;
            padding: 20px 0;
        }
        .sidebar .nav-link {
            color: #333;
            padding: 10px 20px;
        }
        .sidebar .nav-link:hover {
            background-color: #e9ecef;
        }
        .sidebar .nav-link.active {
            background-color: #0d6efd;
            color: white;
        }
        .sidebar .nav-link i {
            margin-right: 10px;
        }
        .content {
            padding: 20px;
        }
        .navbar-brand {
            font-weight: bold;
        }
    </style>
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container-fluid">
            <a class="navbar-brand" href="index.php">Sistema de Estoque</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <?php if (isset($_SESSION['user_id'])): ?>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown">
                                <i class="fas fa-user"></i> <?php echo $_SESSION['user_nome']; ?>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end">
                                <li><a class="dropdown-item" href="index.php?pagina=perfil"><i class="fas fa-id-card"></i> Perfil</a></li>
                                <li><a class="dropdown-item" href="index.php?pagina=alterar_senha"><i class="fas fa-key"></i> Alterar Senha</a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item" href="logout.php"><i class="fas fa-sign-out-alt"></i> Sair</a></li>
                            </ul>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container-fluid">
        <div class="row">
            <?php if (isset($_SESSION['user_id'])): ?>
                <!-- Sidebar -->
                <div class="col-md-3 col-lg-2 d-md-block sidebar collapse">
                    <div class="position-sticky">
                        <ul class="nav flex-column">
                            <li class="nav-item">
                                <a class="nav-link <?php echo $pagina == 'home' ? 'active' : ''; ?>" href="index.php">
                                    <i class="fas fa-home"></i> Início
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link <?php echo $pagina == 'produtos' ? 'active' : ''; ?>" href="index.php?pagina=produtos">
                                    <i class="fas fa-box"></i> Produtos
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link <?php echo $pagina == 'categorias' ? 'active' : ''; ?>" href="index.php?pagina=categorias">
                                    <i class="fas fa-tags"></i> Categorias
                                </a>
                            </li>
                            <?php if (isset($_SESSION['user_cargo']) && $_SESSION['user_cargo'] == 'admin'): ?>
                                <li class="nav-item">
                                    <a class="nav-link <?php echo $pagina == 'usuarios' ? 'active' : ''; ?>" href="index.php?pagina=usuarios">
                                        <i class="fas fa-users"></i> Usuários
                                    </a>
                                </li>
                            <?php endif; ?>
                        </ul>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Main Content -->
            <main class="<?php echo isset($_SESSION['user_id']) ? 'col-md-9 col-lg-10' : 'col-12'; ?> ms-sm-auto px-md-4 content">
                <?php if (isset($vMensagem) && isset($vTipoMensagem)): ?>
                    <div class="alert alert-<?php echo $vTipoMensagem; ?> alert-dismissible fade show mt-3" role="alert">
                        <?php echo $vMensagem; ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php endif; ?>