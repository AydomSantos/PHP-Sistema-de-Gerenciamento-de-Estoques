<?php


// Define the path to the SQLite database file
$dbPath = __DIR__ . '/estoque.db';

// PDO connection options
$options = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES => false,
];

// Create DSN (Data Source Name) for SQLite
$dsn = "sqlite:" . $dbPath;

// Establish database connection
try {
    $conn = new PDO($dsn, null, null, $options);
    
    // Enable foreign keys support in SQLite
    $conn->exec('PRAGMA foreign_keys = ON;');
} catch (PDOException $e) {
    // Log error and display user-friendly message
    error_log('Database Connection Error: ' . $e->getMessage());
    die('Erro ao conectar ao banco de dados. Por favor, contate o administrador do sistema.');
}

// Function to get database connection
function getConnection() {
    global $conn;
    return $conn;
}
?>