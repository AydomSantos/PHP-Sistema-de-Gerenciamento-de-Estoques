<?php
// Incluir arquivo de configuração do banco de dados
require_once 'config/database.php';

// Obter conexão com o banco de dados
$conn = getConnection();

if (!$conn) {
    echo "Erro: Não foi possível conectar ao banco de dados.<br>";
    exit;
}

// Habilitar chaves estrangeiras
$conn->exec('PRAGMA foreign_keys = ON');

// Criar tabelas
$queries = [
    // Categorias table
    "CREATE TABLE IF NOT EXISTS categorias (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        nome TEXT NOT NULL,
        descricao TEXT,
        data_cadastro DATETIME DEFAULT CURRENT_TIMESTAMP
    )",
    
    // Usuarios table
    "CREATE TABLE IF NOT EXISTS usuarios (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        nome TEXT NOT NULL,
        email TEXT NOT NULL UNIQUE,
        senha TEXT NOT NULL,
        cargo TEXT DEFAULT 'usuario',
        ativo INTEGER DEFAULT 1,
        data_cadastro DATETIME DEFAULT CURRENT_TIMESTAMP
    )",
    
    // Produtos table
    "CREATE TABLE IF NOT EXISTS produtos (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        nome TEXT NOT NULL,
        descricao TEXT,
        preco REAL NOT NULL DEFAULT 0,
        qtd INTEGER NOT NULL DEFAULT 0,
        imagem TEXT,
        codigo TEXT,
        codigo_produto_integracao TEXT,
        unidade TEXT DEFAULT 'UN',
        ncm TEXT,
        descr_detalhada TEXT,
        obs_internas TEXT,
        categoria_id INTEGER,
        data_cadastro DATETIME DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (categoria_id) REFERENCES categorias(id)
    )",
    
    // Movimentacoes table
    "CREATE TABLE IF NOT EXISTS movimentacoes (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        produto_id INTEGER NOT NULL,
        usuario_id INTEGER,
        tipo TEXT NOT NULL,
        quantidade INTEGER NOT NULL,
        observacao TEXT,
        data_movimentacao DATETIME DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (produto_id) REFERENCES produtos(id),
        FOREIGN KEY (usuario_id) REFERENCES usuarios(id)
    )"
];

echo "<h2>Criando tabelas...</h2>";

foreach ($queries as $query) {
    try {
        $conn->exec($query);
        echo "✅ Tabela criada com sucesso.<br>";
    } catch (PDOException $e) {
        echo "❌ Erro ao criar tabela: " . $e->getMessage() . "<br>";
    }
}

// Verificar se admin existe, se não, criar
$stmt = $conn->query("SELECT COUNT(*) as count FROM usuarios WHERE email = 'admin@sistema.com'");
$result = $stmt->fetch(PDO::FETCH_ASSOC);

if ($result['count'] == 0) {
    $senha_hash = password_hash('admin123', PASSWORD_DEFAULT);
    $stmt = $conn->prepare("INSERT INTO usuarios (nome, email, senha, cargo) VALUES (?, ?, ?, ?)");
    $stmt->execute(['Administrador', 'admin@sistema.com', $senha_hash, 'admin']);
    echo "✅ Usuário admin criado com sucesso!<br>";
}

echo "<h2>Configuração do banco de dados concluída!</h2>";
echo "<p>Você pode agora <a href='index.php'>acessar o sistema</a>.</p>";
?>