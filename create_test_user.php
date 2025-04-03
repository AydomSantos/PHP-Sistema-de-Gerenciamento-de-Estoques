<?php
// Iniciar sessão
session_start();

// Definir constantes
define('ROOT_PATH', __DIR__);
define('PUBLIC_PATH', __DIR__ . '/public');
define('APP_PATH', __DIR__ . '/app');
define('CONFIG_PATH', __DIR__ . '/config');

// Incluir arquivos necessários
require_once 'config/database.php';
require_once 'app/models/UserModel.php';

// Criar conexão com o banco de dados
$database = new Database();
$db = $database->getConnection();

// Verificar conexão
if (!$db) {
    die("Erro: Não foi possível conectar ao banco de dados.");
}

try {
    // Criar instância do modelo de usuário
    $userModel = new UserModel($db);
    
    // Dados do usuário de teste
    $nome = "Aydom";
    $email = "CREATE TABLE IF NOT EXISTS usuarios (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    nome TEXT NOT NULL,
    email TEXT NOT NULL UNIQUE,
    senha TEXT NOT NULL,
    cargo TEXT NOT NULL
);";
    $senha = "123456";
    $cargo = "admin"; // Definindo como admin para ter acesso completo
    
    // Verificar se o usuário já existe
    $stmt = $db->prepare("SELECT COUNT(*) as count FROM usuarios WHERE email = ?");
    $stmt->execute([$email]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($result['count'] > 0) {
        echo "<h3>Usuário de teste já existe!</h3>";
        echo "<p>Email: $email</p>";
        echo "<p>Senha: $senha</p>";
        echo "<p><a href='login.php'>Ir para a página de login</a></p>";
    } else {
        // Criar o usuário
        $userId = $userModel->createUser($nome, $email, $senha, $cargo);
        
        if ($userId) {
            echo "<h3>Usuário de teste criado com sucesso!</h3>";
            echo "<p>Nome: $nome</p>";
            echo "<p>Email: $email</p>";
            echo "<p>Senha: $senha</p>";
            echo "<p>Cargo: $cargo</p>";
            echo "<p><a href='login.php'>Ir para a página de login</a></p>";
        } else {
            echo "<h3>Erro ao criar usuário de teste.</h3>";
        }
    }
} catch (Exception $e) {
    echo "<h3>Erro:</h3>";
    echo "<p>" . $e->getMessage() . "</p>";
}
?>