<?php
// Incluir arquivo de configuração do banco de dados
include_once '../config/database.php';

// Criar conexão com o banco de dados
$database = new Database();
$db = $database->getConnection();

try {
    // Criar tabela de categorias
    $query = "
    CREATE TABLE IF NOT EXISTS categorias (
        id INT AUTO_INCREMENT PRIMARY KEY,
        nome VARCHAR(100) NOT NULL,
        descricao TEXT,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    )";
    $db->exec($query);
    
    // Criar tabela de usuários
    $query = "
    CREATE TABLE IF NOT EXISTS usuarios (
        id INT AUTO_INCREMENT PRIMARY KEY,
        nome VARCHAR(100) NOT NULL,
        email VARCHAR(100) NOT NULL UNIQUE,
        senha VARCHAR(255) NOT NULL,
        cargo ENUM('usuario', 'admin') DEFAULT 'usuario',
        ativo TINYINT(1) DEFAULT 1,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    )";
    $db->exec($query);
    
    // Criar tabela de produtos
    $query = "
    CREATE TABLE IF NOT EXISTS produtos (
        id INT AUTO_INCREMENT PRIMARY KEY,
        nome VARCHAR(100) NOT NULL,
        descricao TEXT,
        preco DECIMAL(10, 2) NOT NULL DEFAULT 0.00,
        qtd INT NOT NULL DEFAULT 0,
        imagem VARCHAR(255),
        codigo VARCHAR(50),
        codigo_produto_integracao VARCHAR(50),
        unidade VARCHAR(10) DEFAULT 'UN',
        ncm VARCHAR(20),
        descr_detalhada TEXT,
        obs_internas TEXT,
        categoria_id INT,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        FOREIGN KEY (categoria_id) REFERENCES categorias(id) ON DELETE SET NULL
    )";
    $db->exec($query);
    
    // Criar tabela de movimentações de estoque
    $query = "
    CREATE TABLE IF NOT EXISTS movimentacoes (
        id INT AUTO_INCREMENT PRIMARY KEY,
        produto_id INT NOT NULL,
        usuario_id INT,
        tipo ENUM('entrada', 'saida') NOT NULL,
        quantidade INT NOT NULL,
        observacao TEXT,
        data_movimentacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (produto_id) REFERENCES produtos(id) ON DELETE CASCADE,
        FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE SET NULL
    )";
    $db->exec($query);
    
    // Criar usuário admin padrão se não existir
    $query = "SELECT COUNT(*) as total FROM usuarios WHERE email = 'admin@sistema.com'";
    $stmt = $db->prepare($query);
    $stmt->execute();
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($row['total'] == 0) {
        $senha_hash = password_hash('admin123', PASSWORD_DEFAULT);
        $query = "INSERT INTO usuarios (nome, email, senha, cargo) VALUES ('Administrador', 'admin@sistema.com', :senha, 'admin')";
        $stmt = $db->prepare($query);
        $stmt->bindParam(':senha', $senha_hash);
        $stmt->execute();
    }
    
    echo "Banco de dados configurado com sucesso!";
    
} catch(PDOException $e) {
    echo "Erro ao configurar o banco de dados: " . $e->getMessage();
}
?>