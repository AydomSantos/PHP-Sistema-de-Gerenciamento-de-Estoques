<?php

class ProductModel{
    private $vDb;

    public function __construct($vDb){
        $this->vDb = $vDb;
    }

    // cadastra produtos
    public function cadastrarProduto($pNome, $pDescricao, $pPreco, $pImagem, $pQtd, $pCodigo_produto_integracao, $pCodigo, $pUnidade, $pNcm, $pDescr_detalhada, $pObs_internas){
        try{
            // Prepare the SQL statement for inserting a new product
            $vSql = "INSERT INTO produtos (
                nome,
                descricao,
                preco,
                imagem,
                qtd,
                codigo_produto_integracao,
                codigo,
                unidade,
                ncm,
                descr_detalhada,
                obs_internas,
                data_cadastro
            )VALUES (
                :nome, 
                :descricao, 
                :preco, 
                :imagem, 
                :quantidade, 
                :codigo_produto_integracao, 
                :codigo, 
                :unidade, 
                :ncm, 
                :descricao_detalhada, 
                :observacoes_internas,
                :data_cadastro
            
            )";

            // Prepare the statement
            $vStmt = $this->vDb->prepare($vSql);

            // Bind the parameters
            $vStmt->bindParam(':nome', $pNome);
            $vStmt->bindParam(':descricao', $pDescricao);
            $vStmt->bindParam(':preco', $pPreco);
            $vStmt->bindParam(':imagem', $pImagem);
            $vStmt->bindParam(':quantidade', $pQtd);
            $vStmt->bindParam(':codigo_produto_integracao', $pCodigo_produto_integracao);
            $vStmt->bindParam(':codigo', $pCodigo);
            $vStmt->bindParam(':unidade', $pUnidade);
            $vStmt->bindParam(':ncm', $pNcm);
            $vStmt->bindParam(':descricao_detalhada', $pDescr_detalhada);
            $vStmt->bindParam(':observacoes_internas', $pObs_internas);
            $vDataCadastro = date('Y-m-d H:i:s');
            $vStmt->bindParam(':data_cadastro', $vDataCadastro);

            // Execute the statement
            $vStmt->execute();
            // Return the ID of the newly inserted product
            return $this->vDb->lastInsertId();
        }catch(PDOException $e){
            echo $e->getMessage();
            exit();
        }
    }

    // busca todos os produtos
    public function getAllProducts(){
        try{
            $vSql = "SELECT * FROM produtos";
            $vStmt = $this->vDb->prepare($vSql);
            $vStmt->execute();
            
        }catch(PDOException $e){
            echo $e->getMessage();
            exit();
        }
    }

    // busca produtos por id
    public function getProductById($pId){
        try{
           $vSql = "SELECT * FROM produtos WHERE id = :id";
           $vStmt = $this->vDb->prepare($vSql);
           $vStmt->bindParam(':id', $pId);
           $vStmt->execute(); 
        } catch(PDOException $e){
            echo $e->getMessage();
            exit();
        }
    }

    // edita produtos
    public function editarPoducts($pId, $pNome, $pDescricao, $pPreco, $pImagem, $pQtd, $pCodigo_produto_integracao, $pCodigo, $pUnidade, $pNcm, $pDescr_detalhada, $pObs_internas){
       try{
        $vSql = "UPDATE produtos SET
        nome = :nome,
        descricao = :descricao,
        preco = :preco,
        imagem = :imagem,
        qtd = :quantidade,
        codigo_produto_integracao = :codigo_produto_integracao,
        codigo = :codigo,
        unidade = :unidade,
        ncm = :ncm,
        descr_detalhada = :descricao_detalhada,
        obs_internas = :observacoes_internas
        WHERE id = :id";

        $vStmt = $this->vDb->prepare($vSql);
        $vStmt->bindParam(':nome', $pNome);
        $vStmt->bindParam(':descricao', $pDescricao);
        $vStmt->bindParam(':preco', $pPreco);
        $vStmt->bindParam(':imagem', $pImagem);
        $vStmt->bindParam(':quantidade', $pQtd);
        $vStmt->bindParam(':codigo_produto_integracao', $pCodigo_produto_integracao);
        $vStmt->bindParam(':codigo', $pCodigo);
        $vStmt->bindParam(':unidade', $pUnidade);
        $vStmt->bindParam(':ncm', $pNcm);
        $vStmt->bindParam(':descricao_detalhada', $pDescr_detalhada);
        $vStmt->bindParam(':observacoes_internas', $pObs_internas);
        $vStmt->bindParam(':id', $pId);
        $vStmt->execute();
        return $vStmt->rowCount();

       }catch(PDOException $e){
            echo $e->getMessage();
            exit();
       } 
    }

    // exclui produtos
    public function excluirProduct($pId){
        try{
            $vSql = "DELETE FROM produtos WHERE id = :id";
            $vStmt = $this->vDb->prepare($vSql);
            $vStmt->bindParam(':id', $pId);
            $vStmt->execute();
            return $vStmt->rowCount(); 
        } catch(PDOException $e){
            echo $e->getMessage();
            exit();
        }
    }
}

?>