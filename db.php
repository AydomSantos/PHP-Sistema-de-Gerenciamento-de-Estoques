<?php
// Configuração do banco de dados SQLite
$dbname = 'estoque.db';

// Criar conexão
$conn = new PDO("sqlite:$dbname");

// Verificar conexão
if (!$conn) {
    die("Conexão falhou: " . $conn->errorInfo());
}
?>