<?php

class CategoryModel{
    private $vDb;

    public function __construct($vDb){
        $this->vDb = $vDb;
    }

    // Cadastra uma nova categoria
    public function cadastrarCategoria($pNome, $pDescricao){
        try{
            $vSql = "INSERT INTO categorias (nome, descricao) VALUES (:nome, :descricao)";
            $vStmt = $this->vDb->prepare($vSql); 
            $vStmt->bindParam(':nome', $pNome);
            $vStmt->bindParam(':descricao', $pDescricao);
            $vStmt->execute();
            return $this->vDb->lastInsertId();
        } catch(Exception $e){
            echo "Erro ao cadastrar a categoria: " . $e->getMessage();
            exit(); 
        }
      
    }

    // Busca todas as categorias
    public function getAllCategories(){
        try{
            $vSql = "SELECT * FROM categorias";
            $vStmt = $this->vDb->prepare($vSql);
            $vStmt->execute();
            return $vStmt->fetchAll(PDO::FETCH_ASSOC);
        }catch(Exception $e){
            echo "Erro ao buscar as categorias: ". $e->getMessage();
            exit();
        }
    }

    // Busca categoria por ID
    public function getCategoryById($pId){
        try{
            $vSql = "SELECT * FROM categorias WHERE id = :id";
            $vStmt = $this->vDb->prepare($vSql);
            $vStmt->bindParam(':id', $pId);
            $vStmt->execute();
            return $vStmt->fetch(PDO::FETCH_ASSOC);
        }catch(Exception $e){
            echo "Erro ao buscar a categoria: ". $e->getMessage();
            exit();
        }
    }

    // Edita uma categoria
    public function editarCategoria($pId, $pNome, $pDescricao){
        try{
            $vSql = "UPDATE categorias SET nome = :nome, descricao = :descricao WHERE id = :id";
            $vStmt = $this->vDb->prepare($vSql);
            $vStmt->bindParam(':id', $pId);
            $vStmt->bindParam(':nome', $pNome);
            $vStmt->bindParam(':descricao', $pDescricao);
            $vStmt->execute();
            return $vStmt->rowCount();
        } catch(Exception $e){
            echo "Erro ao editar a categoria: ". $e->getMessage();
            exit();
        }
    }

    // Exclui uma categoria
    public function excluirCategoria($pId){
        try{
            $vSql = "SELECT COUNT(*) as total FROM produtos WHERE categoria_id = :id";
            $vStmt = $this->vDb->prepare($vSql);
            $vStmt->bindParam(':id', $pId);
            $vStmt->execute(); 
            $vResultado = $vStmt->fetch(PDO::FETCH_ASSOC);
            if($vResultado['total'] > 0){
                throw new Exception("Não é possível excluir esta categoria pois existem produtos associados a ela.");
            }
            
            $vSql = "DELETE FROM categorias WHERE id = :id";
            $vStmt = $this->vDb->prepare($vSql);
            $vStmt->bindParam(':id', $pId);
            $vStmt->execute();
            return $vStmt->rowCount();
        } catch(Exception $e){
            echo "Erro ao excluir a categoria: ". $e->getMessage();
            exit(); 
        }
    }
}

?>