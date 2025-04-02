<?php

class ProductModel{
    private $vDb;

    public function __construct($vDb){
        $this->vDb = $vDb;
    }

    // cadastra produtos
    public function cadastrarProduto($pNome, $pDescricao, $pPreco, $pImagem, $pQtd, $pCodigo_produto_integracao, $pCodigo, $pUnidade, $pNcm, $pDescr_detalhada, $pObs_internas, $pCategoriaId = null){
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
                data_cadastro,
                categoria_id
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
                :data_cadastro,
                :categoria_id
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
            $vStmt->bindParam(':categoria_id', $pCategoriaId);

            // Execute the statement
            $vStmt->execute();
            // Return the ID of the newly inserted product
            return $this->vDb->lastInsertId();
        }catch(PDOException $e){
            error_log("Erro ao cadastrar produto: " . $e->getMessage());
            throw new Exception("Erro ao cadastrar produto: " . $e->getMessage());
        }
    }

    // busca todos os produtos
    public function getAllProducts($limit = null, $offset = null, $orderBy = 'data_cadastro DESC'){
        try{
            $vSql = "SELECT p.*, c.nome as categoria_nome 
                    FROM produtos p 
                    LEFT JOIN categorias c ON p.categoria_id = c.id";
            
            if ($orderBy) {
                $vSql .= " ORDER BY " . $orderBy;
            }
            
            if ($limit !== null) {
                $vSql .= " LIMIT :limit";
                if ($offset !== null) {
                    $vSql .= " OFFSET :offset";
                }
            }
            
            $vStmt = $this->vDb->prepare($vSql);
            
            if ($limit !== null) {
                $vStmt->bindParam(':limit', $limit, PDO::PARAM_INT);
                if ($offset !== null) {
                    $vStmt->bindParam(':offset', $offset, PDO::PARAM_INT);
                }
            }
            
            $vStmt->execute();
            return $vStmt->fetchAll(PDO::FETCH_ASSOC);
            
        }catch(PDOException $e){
            error_log("Erro ao buscar produtos: " . $e->getMessage());
            throw new Exception("Erro ao buscar produtos: " . $e->getMessage());
        }
    }

    // conta o total de produtos para paginação
    public function countProducts($filtros = []){
        try{
            $vSql = "SELECT COUNT(*) as total FROM produtos";
            
            $condicoes = [];
            $parametros = [];
            
            if (!empty($filtros)) {
                if (isset($filtros['categoria_id']) && $filtros['categoria_id']) {
                    $condicoes[] = "categoria_id = :categoria_id";
                    $parametros[':categoria_id'] = $filtros['categoria_id'];
                }
                
                if (isset($filtros['busca']) && $filtros['busca']) {
                    $condicoes[] = "(nome LIKE :busca OR codigo LIKE :busca OR descricao LIKE :busca)";
                    $parametros[':busca'] = '%' . $filtros['busca'] . '%';
                }
                
                if (!empty($condicoes)) {
                    $vSql .= " WHERE " . implode(" AND ", $condicoes);
                }
            }
            
            $vStmt = $this->vDb->prepare($vSql);
            
            foreach ($parametros as $param => $valor) {
                $vStmt->bindValue($param, $valor);
            }
            
            $vStmt->execute();
            $resultado = $vStmt->fetch(PDO::FETCH_ASSOC);
            return $resultado['total'];
            
        }catch(PDOException $e){
            error_log("Erro ao contar produtos: " . $e->getMessage());
            throw new Exception("Erro ao contar produtos: " . $e->getMessage());
        }
    }

    // busca produtos por id
    public function getProductById($pId){
        try{
           $vSql = "SELECT p.*, c.nome as categoria_nome 
                   FROM produtos p 
                   LEFT JOIN categorias c ON p.categoria_id = c.id 
                   WHERE p.id = :id";
           $vStmt = $this->vDb->prepare($vSql);
           $vStmt->bindParam(':id', $pId);
           $vStmt->execute(); 
           return $vStmt->fetch(PDO::FETCH_ASSOC);
        } catch(PDOException $e){
            error_log("Erro ao buscar produto por ID: " . $e->getMessage());
            throw new Exception("Erro ao buscar produto por ID: " . $e->getMessage());
        }
    }

    // edita produtos
    public function editarProduto($pId, $pNome, $pDescricao, $pPreco, $pImagem, $pQtd, $pCodigo_produto_integracao, $pCodigo, $pUnidade, $pNcm, $pDescr_detalhada, $pObs_internas, $pCategoriaId = null){
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
        obs_internas = :observacoes_internas,
        categoria_id = :categoria_id
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
        $vStmt->bindParam(':categoria_id', $pCategoriaId);
        $vStmt->bindParam(':id', $pId);
        $vStmt->execute();
        return $vStmt->rowCount();

       }catch(PDOException $e){
            error_log("Erro ao editar produto: " . $e->getMessage());
            throw new Exception("Erro ao editar produto: " . $e->getMessage());
       } 
    }

    // exclui produtos
    public function excluirProduto($pId){
        try{
            $vSql = "DELETE FROM produtos WHERE id = :id";
            $vStmt = $this->vDb->prepare($vSql);
            $vStmt->bindParam(':id', $pId);
            $vStmt->execute();
            return $vStmt->rowCount(); 
        } catch(PDOException $e){
            error_log("Erro ao excluir produto: " . $e->getMessage());
            throw new Exception("Erro ao excluir produto: " . $e->getMessage());
        }
    }
    
    // registra movimentação de estoque
    public function registrarMovimentacao($produtoId, $usuarioId, $tipo, $quantidade, $observacao = null) {
        try {
            $vSql = "INSERT INTO movimentacoes (
                produto_id, 
                usuario_id, 
                tipo, 
                quantidade, 
                observacao
            ) VALUES (
                :produto_id,
                :usuario_id,
                :tipo,
                :quantidade,
                :observacao
            )";
            
            $vStmt = $this->vDb->prepare($vSql);
            $vStmt->bindParam(':produto_id', $produtoId);
            $vStmt->bindParam(':usuario_id', $usuarioId);
            $vStmt->bindParam(':tipo', $tipo);
            $vStmt->bindParam(':quantidade', $quantidade);
            $vStmt->bindParam(':observacao', $observacao);
            $vStmt->execute();
            
            // Atualiza a quantidade do produto
            if ($tipo == 'entrada') {
                $this->atualizarQuantidade($produtoId, $quantidade);
            } else if ($tipo == 'saida') {
                $this->atualizarQuantidade($produtoId, -$quantidade);
            } else if ($tipo == 'ajuste') {
                // Para ajuste, a quantidade já é o valor final
                $this->definirQuantidade($produtoId, $quantidade);
            }
            
            return $this->vDb->lastInsertId();
        } catch(PDOException $e) {
            error_log("Erro ao registrar movimentação: " . $e->getMessage());
            throw new Exception("Erro ao registrar movimentação: " . $e->getMessage());
        }
    }
    
    // atualiza a quantidade do produto (incremento/decremento)
    private function atualizarQuantidade($produtoId, $quantidade) {
        try {
            $vSql = "UPDATE produtos SET qtd = qtd + :quantidade WHERE id = :id";
            $vStmt = $this->vDb->prepare($vSql);
            $vStmt->bindParam(':quantidade', $quantidade);
            $vStmt->bindParam(':id', $produtoId);
            $vStmt->execute();
            return $vStmt->rowCount();
        } catch(PDOException $e) {
            error_log("Erro ao atualizar quantidade: " . $e->getMessage());
            throw new Exception("Erro ao atualizar quantidade: " . $e->getMessage());
        }
    }
    
    // define a quantidade exata do produto
    private function definirQuantidade($produtoId, $quantidade) {
        try {
            $vSql = "UPDATE produtos SET qtd = :quantidade WHERE id = :id";
            $vStmt = $this->vDb->prepare($vSql);
            $vStmt->bindParam(':quantidade', $quantidade);
            $vStmt->bindParam(':id', $produtoId);
            $vStmt->execute();
            return $vStmt->rowCount();
        } catch(PDOException $e) {
            error_log("Erro ao definir quantidade: " . $e->getMessage());
            throw new Exception("Erro ao definir quantidade: " . $e->getMessage());
        }
    }
}
?>