<?php
// Iniciar sessão
session_start();

// Definir constantes
define('ROOT_PATH', __DIR__);
define('PUBLIC_PATH', __DIR__ . '/public');
define('APP_PATH', __DIR__ . '/app');
define('CONFIG_PATH', __DIR__ . '/config');

// Verificar se já está logado
if (isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit();
}

// Incluir arquivos necessários
require_once 'config/database.php';
require_once 'app/models/UserModel.php';
require_once 'app/controllers/UserController.php';

// Criar conexão com o banco de dados
$database = new Database();
$db = $database->getConnection();

// Criar instância do controlador de usuários
$userController = new UserController($db);

// Verificar se é uma requisição de login
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $userController->login();
} else {
    // Exibir formulário de login
    $userController->loginForm();
}
?>