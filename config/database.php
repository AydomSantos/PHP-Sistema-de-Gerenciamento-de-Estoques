<?php
class Database {
    private $db_file;
    private $conn;

    public function __construct() {
        $this->db_file = dirname(__DIR__) . '/database/estoque.db';
    }

    public function getConnection() {
        $this->conn = null;

        try {
            // Create database directory if it doesn't exist
            $db_dir = dirname($this->db_file);
            if (!is_dir($db_dir)) {
                mkdir($db_dir, 0777, true);
            }

            // Connect to SQLite database
            $this->conn = new PDO("sqlite:" . $this->db_file);
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            
            // Create tables if they don't exist
            $this->createTables();
            
            return $this->conn;
        } catch(PDOException $e) {
            // Don't output error directly - log it instead
            error_log("Database connection error: " . $e->getMessage());
            return null;
        }
    }
    
    private function createTables() {
        // Create tables if they don't exist
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
        
        foreach ($queries as $query) {
            $this->conn->exec($query);
        }
        
        // Check if admin user exists, if not create one
        $stmt = $this->conn->query("SELECT COUNT(*) as count FROM usuarios WHERE email = 'admin@sistema.com'");
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($result['count'] == 0) {
            $senha_hash = password_hash('admin123', PASSWORD_DEFAULT);
            $stmt = $this->conn->prepare("INSERT INTO usuarios (nome, email, senha, cargo) VALUES (?, ?, ?, ?)");
            $stmt->execute(['Administrador', 'admin@sistema.com', $senha_hash, 'admin']);
        }
    }
}

// Helper function to get connection quickly
function getConnection() {
    $database = new Database();
    return $database->getConnection();
}
?>