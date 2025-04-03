<?php
class CategoryModel {
    private $conn;
    
    public function __construct($db) {
        $this->conn = $db;
    }
    
    // Cadastrar nova categoria
    public function cadastrarCategoria($nome, $descricao) {
        try {
            $query = "INSERT INTO categorias (nome, descricao) VALUES (:nome, :descricao)";
            
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':nome', $nome);
            $stmt->bindParam(':descricao', $descricao);
            
            if($stmt->execute()) {
                return $this->conn->lastInsertId();
            }
            
            return false;
        } catch(PDOException $e) {
            error_log("Erro ao cadastrar categoria: " . $e->getMessage());
            return false;
        }
    }
    
    // Obter todas as categorias
    public function getAllCategories() {
        try {
            $query = "SELECT * FROM categorias ORDER BY nome";
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch(PDOException $e) {
            error_log("Erro ao buscar categorias: " . $e->getMessage());
            return [];
        }
    }
    
    // Obter categoria por ID
    public function getCategoryById($id) {
        try {
            $query = "SELECT * FROM categorias WHERE id = :id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':id', $id);
            $stmt->execute();
            
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch(PDOException $e) {
            error_log("Erro ao buscar categoria por ID: " . $e->getMessage());
            return false;
        }
    }
    
    // Editar categoria existente
    public function editarCategoria($id, $nome, $descricao) {
        try {
            $query = "UPDATE categorias SET nome = :nome, descricao = :descricao WHERE id = :id";
            
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':id', $id);
            $stmt->bindParam(':nome', $nome);
            $stmt->bindParam(':descricao', $descricao);
            
            $stmt->execute();
            
            return $stmt->rowCount() > 0;
        } catch(PDOException $e) {
            error_log("Erro ao editar categoria: " . $e->getMessage());
            return false;
        }
    }
    
    // Excluir categoria
    public function excluirCategoria($id) {
        try {
            $query = "DELETE FROM categorias WHERE id = :id";
            
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':id', $id);
            
            $stmt->execute();
            
            return $stmt->rowCount() > 0;
        } catch(PDOException $e) {
            error_log("Erro ao excluir categoria: " . $e->getMessage());
            return false;
        }
    }
}
?>