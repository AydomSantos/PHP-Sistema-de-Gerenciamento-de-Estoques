<?php
class ProductModel {
    private $conn;
    
    public function __construct($db) {
        $this->conn = $db;
    }
    
    // Adicionar novo produto
    public function adicionarProduto($nome, $descricao, $categoriaId, $preco, $quantidade) {
        try {
            $query = "INSERT INTO produtos (nome, descricao, categoria_id, preco, quantidade) 
                      VALUES (:nome, :descricao, :categoria_id, :preco, :quantidade)";
            
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':nome', $nome);
            $stmt->bindParam(':descricao', $descricao);
            $stmt->bindParam(':categoria_id', $categoriaId);
            $stmt->bindParam(':preco', $preco);
            $stmt->bindParam(':quantidade', $quantidade);
            
            if($stmt->execute()) {
                return $this->conn->lastInsertId();
            }
            
            return false;
        } catch(PDOException $e) {
            error_log("Erro ao adicionar produto: " . $e->getMessage());
            return false;
        }
    }
    
    // Obter todos os produtos
    public function getAllProducts() {
        try {
            $query = "SELECT p.*, c.nome as categoria_nome 
                      FROM produtos p
                      LEFT JOIN categorias c ON p.categoria_id = c.id
                      ORDER BY p.nome";
            
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            // Debug
            error_log("Produtos encontrados: " . count($result));
            
            return $result;
        } catch(PDOException $e) {
            error_log("Erro ao buscar produtos: " . $e->getMessage());
            return [];
        }
    }
    
    // Obter produto por ID
    public function getProductById($id) {
        try {
            $query = "SELECT p.*, c.nome as categoria_nome 
                      FROM produtos p
                      LEFT JOIN categorias c ON p.categoria_id = c.id
                      WHERE p.id = :id";
            
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':id', $id);
            $stmt->execute();
            
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch(PDOException $e) {
            error_log("Erro ao buscar produto por ID: " . $e->getMessage());
            return false;
        }
    }
    
    // Editar produto existente
    public function editarProduto($id, $nome, $descricao, $categoriaId, $preco, $quantidade) {
        try {
            $query = "UPDATE produtos 
                      SET nome = :nome, 
                          descricao = :descricao, 
                          categoria_id = :categoria_id, 
                          preco = :preco, 
                          quantidade = :quantidade, 
                          data_atualizacao = CURRENT_TIMESTAMP
                      WHERE id = :id";
            
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':id', $id);
            $stmt->bindParam(':nome', $nome);
            $stmt->bindParam(':descricao', $descricao);
            $stmt->bindParam(':categoria_id', $categoriaId);
            $stmt->bindParam(':preco', $preco);
            $stmt->bindParam(':quantidade', $quantidade);
            
            $stmt->execute();
            
            return $stmt->rowCount() > 0;
        } catch(PDOException $e) {
            error_log("Erro ao editar produto: " . $e->getMessage());
            return false;
        }
    }
    
    // Excluir produto
    public function excluirProduto($id) {
        try {
            $query = "DELETE FROM produtos WHERE id = :id";
            
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':id', $id);
            
            $stmt->execute();
            
            return $stmt->rowCount() > 0;
        } catch(PDOException $e) {
            error_log("Erro ao excluir produto: " . $e->getMessage());
            return false;
        }
    }
}
?>