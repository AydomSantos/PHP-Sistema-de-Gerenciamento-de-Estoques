<?php
// Include the database configuration file
require_once 'config/database.php';

// Create a new Database instance
$database = new Database();

// Get the database connection
$db = $database->getConnection();

if (!$db) {
    throw new Exception("Failed to connect to the database.");
}

// SQL to create tables
$sql = "
CREATE TABLE IF NOT EXISTS categorias (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    nome TEXT NOT NULL,
    descricao TEXT
);

CREATE TABLE IF NOT EXISTS produtos (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    nome TEXT NOT NULL,
    descricao TEXT,
    preco REAL NOT NULL,
    qtd INTEGER NOT NULL,
    categoria_id INTEGER,
    FOREIGN KEY (categoria_id) REFERENCES categorias(id)
);

CREATE TABLE IF NOT EXISTS usuarios (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    nome TEXT NOT NULL,
    email TEXT NOT NULL UNIQUE,
    senha TEXT NOT NULL,
    cargo TEXT NOT NULL
);
";

// Execute the SQL
$db->exec($sql);

echo "Database setup completed successfully.";
?>