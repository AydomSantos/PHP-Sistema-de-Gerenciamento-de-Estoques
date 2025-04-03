<?php

// Inclui o controller de produtos
require_once __DIR__ . '/../controllers/ProductController.php';
require_once __DIR__ . '/../../config/database.php';

// Inicializa o controller com a conexão do banco de dados
$database = new Database();
$connection = $database->getConnection();
$productController = new ProductController($connection);

// Define a URL base para as rotas de produtos
$productBasePath = '/produtos';

// Captura a URL atual
$requestUri = $_SERVER['REQUEST_URI'];
$requestMethod = $_SERVER['REQUEST_METHOD'];

// Remove parâmetros de query string da URL
if (($pos = strpos($requestUri, '?')) !== false) {
    $requestUri = substr($requestUri, 0, $pos);
}

// Remove trailing slash se existir (exceto para a raiz)
if ($requestUri !== '/' && substr($requestUri, -1) === '/') {
    $requestUri = rtrim($requestUri, '/');
}

// Verifica se a URL começa com o caminho base de produtos
if (strpos($requestUri, $productBasePath) === 0) {
    // Extrai a parte da URL após o caminho base
    $route = substr($requestUri, strlen($productBasePath));

    // Rota padrão (lista de produtos)
    if ($route === '' || $route === '/') {
        if ($requestMethod === 'GET') {
            // Exibe a lista de produtos
            include __DIR__ . '/../views/products/list.php';
            exit;
        }
    }

    // Rota para adicionar produto
    elseif ($route === '/adicionar') {
        if ($requestMethod === 'GET') {
            // Exibe o formulário de adição
            include __DIR__ . '/../views/products/add.php';
            exit;
        } elseif ($requestMethod === 'POST') {
            // Processa o formulário de adição
            $resultado = $productController->adicionarProduto($_POST);

            if ($resultado['status']) {
                // Redireciona com mensagem de sucesso
                $_SESSION['message'] = $resultado['mensagem'];
                $_SESSION['message_type'] = 'success';
                header('Location: ' . $productBasePath);
                exit;
            } else {
                // Retorna ao formulário com erros
                $_SESSION['form_data'] = $_POST;
                $_SESSION['form_errors'] = $resultado['erros'] ?? [$resultado['mensagem']];
                header('Location: ' . $productBasePath . '/adicionar');
                exit;
            }
        }
    }

    // Rota para visualizar produto
    elseif (preg_match('#^/visualizar/(\d+)$#', $route, $matches)) {
        $id = $matches[1];

        if ($requestMethod === 'GET') {
            // Armazena o ID do produto na sessão para uso na view
            $_SESSION['product_id'] = $id;
            include __DIR__ . '/../views/products/view.php';
            exit;
        }
    }

    // Rota para editar produto
    elseif (preg_match('#^/editar/(\d+)$#', $route, $matches)) {
        $id = $matches[1];

        if ($requestMethod === 'GET') {
            // Armazena o ID do produto na sessão para uso na view
            $_SESSION['product_id'] = $id;
            include __DIR__ . '/../views/products/edit.php';
            exit;
        } elseif ($requestMethod === 'POST') {
            // Processa o formulário de edição
            $resultado = $productController->editarProduto($id, $_POST);

            if ($resultado['status']) {
                // Redireciona com mensagem de sucesso
                $_SESSION['message'] = $resultado['mensagem'];
                $_SESSION['message_type'] = 'success';
                header('Location: ' . $productBasePath);
                exit;
            } else {
                // Retorna ao formulário com erros
                $_SESSION['form_data'] = $_POST;
                $_SESSION['form_errors'] = $resultado['erros'] ?? [$resultado['mensagem']];
                header('Location: ' . $productBasePath . '/editar/' . $id);
                exit;
            }
        }
    }

    // Rota para excluir produto
    elseif (preg_match('#^/excluir/(\d+)$#', $route, $matches)) {
        $id = $matches[1];

        if ($requestMethod === 'POST') {
            // Processa a exclusão
            $resultado = $productController->excluirProduto($id);

            if ($resultado['status']) {
                $_SESSION['message'] = $resultado['mensagem'];
                $_SESSION['message_type'] = 'success';
            } else {
                $_SESSION['message'] = $resultado['mensagem'];
                $_SESSION['message_type'] = 'danger';
            }

            // Redireciona para a lista de produtos
            header('Location: ' . $productBasePath);
            exit;
        }
    }

    // Rota para API de produtos (formato JSON)
    elseif ($route === '/api') {
        header('Content-Type: application/json');

        if ($requestMethod === 'GET') {
            // Retorna todos os produtos em formato JSON
            echo json_encode($productController->getAllProducts());
            exit;
        }
    }

    // Rota para API de produto específico (formato JSON)
    elseif (preg_match('#^/api/(\d+)$#', $route, $matches)) {
        $id = $matches[1];
        header('Content-Type: application/json');

        if ($requestMethod === 'GET') {
            // Retorna um produto específico em formato JSON
            echo json_encode($productController->getProductById($id));
            exit;
        }
    }
}
