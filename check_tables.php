<?php
// Incluir arquivo de configuração do banco de dados
require_once 'config/database.php';

// Obter conexão com o banco de dados
$conn = getConnection();

if (!$conn) {
    echo "Erro: Não foi possível conectar ao banco de dados.";
    exit;
}

// Verificar tabelas existentes
$tables = [
    'categorias' => 'Categorias',
    'usuarios' => 'Usuários',
    'produtos' => 'Produtos',
    'movimentacoes' => 'Movimentações'
];

echo "<h2>Verificação de Estrutura do Banco de Dados</h2>";

foreach ($tables as $table => $label) {
    // Verificar se a tabela existe
    $stmt = $conn->query("SELECT name FROM sqlite_master WHERE type='table' AND name='$table'");
    $exists = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($exists) {
        echo "<p>✅ Tabela <strong>$label</strong> existe.</p>";
        
        // Listar colunas da tabela
        $stmt = $conn->query("PRAGMA table_info($table)");
        $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        echo "<ul>";
        foreach ($columns as $column) {
            echo "<li>{$column['name']} ({$column['type']})</li>";
        }
        echo "</ul>";
    } else {
        echo "<p>❌ Tabela <strong>$label</strong> não existe!</p>";
    }
}

// Verificar relacionamentos
echo "<h2>Verificação de Relacionamentos</h2>";

// Verificar relacionamento produtos -> categorias
$stmt = $conn->query("PRAGMA foreign_key_list(produtos)");
$relations = $stmt->fetchAll(PDO::FETCH_ASSOC);

if (count($relations) > 0) {
    echo "<p>✅ Relacionamento entre Produtos e Categorias está configurado.</p>";
    foreach ($relations as $relation) {
        echo "<ul>";
        echo "<li>Tabela: {$relation['table']}</li>";
        echo "<li>Coluna local: {$relation['from']}</li>";
        echo "<li>Coluna referenciada: {$relation['to']}</li>";
        echo "</ul>";
    }
} else {
    echo "<p>❌ Relacionamento entre Produtos e Categorias não está configurado!</p>";
}

// Verificar relacionamento movimentacoes -> produtos e usuarios
$stmt = $conn->query("PRAGMA foreign_key_list(movimentacoes)");
$relations = $stmt->fetchAll(PDO::FETCH_ASSOC);

if (count($relations) > 0) {
    echo "<p>✅ Relacionamentos da tabela Movimentações estão configurados.</p>";
    foreach ($relations as $relation) {
        echo "<ul>";
        echo "<li>Tabela: {$relation['table']}</li>";
        echo "<li>Coluna local: {$relation['from']}</li>";
        echo "<li>Coluna referenciada: {$relation['to']}</li>";
        echo "</ul>";
    }
} else {
    echo "<p>❌ Relacionamentos da tabela Movimentações não estão configurados!</p>";
}

// Verificar se as chaves estrangeiras estão habilitadas
$stmt = $conn->query("PRAGMA foreign_keys");
$fk_enabled = $stmt->fetch(PDO::FETCH_ASSOC);

echo "<h2>Configuração de Chaves Estrangeiras</h2>";
if ($fk_enabled && $fk_enabled['foreign_keys'] == 1) {
    echo "<p>✅ Chaves estrangeiras estão habilitadas no SQLite.</p>";
} else {
    echo "<p>❌ Chaves estrangeiras não estão habilitadas no SQLite!</p>";
    echo "<p>Recomendação: Adicione o seguinte código ao conectar com o banco:</p>";
    echo "<pre>\$conn->exec('PRAGMA foreign_keys = ON');</pre>";
}
?>