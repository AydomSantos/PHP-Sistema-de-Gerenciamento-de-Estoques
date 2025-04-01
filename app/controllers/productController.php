<?php

require_once __DIR__ . '/../models/ProductModel.php';

class ProductController {
    private $productModel;
    
    public function __construct($db) {
        $this->productModel = new ProductModel($db);
    }
    
    // Validação e cadastro de produtos
    public function cadastrarProduto($dados) {
        $erros = [];
        
        // Validação do nome
        if (empty($dados['nome'])) {
            $erros[] = "O nome do produto é obrigatório";
        } elseif (strlen($dados['nome']) > 100) {
            $erros[] = "O nome do produto deve ter no máximo 100 caracteres";
        }
        
        // Validação da descrição
        if (empty($dados['descricao'])) {
            $erros[] = "A descrição do produto é obrigatória";
        }
        
        // Validação do preço
        if (empty($dados['preco'])) {
            $erros[] = "O preço do produto é obrigatório";
        } elseif (!is_numeric($dados['preco']) || $dados['preco'] <= 0) {
            $erros[] = "O preço deve ser um valor numérico positivo";
        }
        
        // Validação da quantidade
        if (empty($dados['qtd'])) {
            $erros[] = "A quantidade do produto é obrigatória";
        } elseif (!is_numeric($dados['qtd']) || $dados['qtd'] < 0) {
            $erros[] = "A quantidade deve ser um valor numérico não negativo";
        }
        
        // Validação do código
        if (!empty($dados['codigo']) && strlen($dados['codigo']) > 50) {
            $erros[] = "O código do produto deve ter no máximo 50 caracteres";
        }
        
        // Validação da unidade
        if (empty($dados['unidade'])) {
            $erros[] = "A unidade de medida é obrigatória";
        }
        
        // Validação do NCM
        if (!empty($dados['ncm']) && !preg_match('/^\d{8}$/', $dados['ncm'])) {
            $erros[] = "O NCM deve conter 8 dígitos numéricos";
        }
        
        // Se houver erros, retorna os erros
        if (!empty($erros)) {
            return [
                'status' => false,
                'mensagem' => 'Erros de validação',
                'erros' => $erros
            ];
        }
        
        // Se não houver erros, cadastra o produto
        try {
            $id = $this->productModel->cadastrarProduto(
                $dados['nome'],
                $dados['descricao'],
                $dados['preco'],
                $dados['imagem'] ?? null,
                $dados['qtd'],
                $dados['codigo_produto_integracao'] ?? null,
                $dados['codigo'] ?? null,
                $dados['unidade'],
                $dados['ncm'] ?? null,
                $dados['descr_detalhada'] ?? null,
                $dados['obs_internas'] ?? null
            );
            
            if ($id) {
                return [
                    'status' => true,
                    'mensagem' => 'Produto cadastrado com sucesso',
                    'id' => $id
                ];
            } else {
                return [
                    'status' => false,
                    'mensagem' => 'Erro ao cadastrar produto'
                ];
            }
        } catch (Exception $e) {
            return [
                'status' => false,
                'mensagem' => 'Erro ao cadastrar produto: ' . $e->getMessage()
            ];
        }
    }
    
    // Busca todos os produtos com validação
    public function getAllProducts() {
        try {
            $produtos = $this->productModel->getAllProducts();
            
            if ($produtos) {
                return [
                    'status' => true,
                    'produtos' => $produtos
                ];
            } else {
                return [
                    'status' => true,
                    'mensagem' => 'Nenhum produto encontrado',
                    'produtos' => []
                ];
            }
        } catch (Exception $e) {
            return [
                'status' => false,
                'mensagem' => 'Erro ao buscar produtos: ' . $e->getMessage()
            ];
        }
    }
    
    // Busca produto por ID com validação
    public function getProductById($id) {
        // Validação do ID
        if (empty($id) || !is_numeric($id) || $id <= 0) {
            return [
                'status' => false,
                'mensagem' => 'ID de produto inválido'
            ];
        }
        
        try {
            $produto = $this->productModel->getProductById($id);
            
            if ($produto) {
                return [
                    'status' => true,
                    'produto' => $produto
                ];
            } else {
                return [
                    'status' => false,
                    'mensagem' => 'Produto não encontrado'
                ];
            }
        } catch (Exception $e) {
            return [
                'status' => false,
                'mensagem' => 'Erro ao buscar produto: ' . $e->getMessage()
            ];
        }
    }
    
    // Edita produto com validação
    public function editarProduto($id, $dados) {
        // Validação do ID
        if (empty($id) || !is_numeric($id) || $id <= 0) {
            return [
                'status' => false,
                'mensagem' => 'ID de produto inválido'
            ];
        }
        
        $erros = [];
        
        // Validação do nome
        if (empty($dados['nome'])) {
            $erros[] = "O nome do produto é obrigatório";
        } elseif (strlen($dados['nome']) > 100) {
            $erros[] = "O nome do produto deve ter no máximo 100 caracteres";
        }
        
        // Validação da descrição
        if (empty($dados['descricao'])) {
            $erros[] = "A descrição do produto é obrigatória";
        }
        
        // Validação do preço
        if (empty($dados['preco'])) {
            $erros[] = "O preço do produto é obrigatório";
        } elseif (!is_numeric($dados['preco']) || $dados['preco'] <= 0) {
            $erros[] = "O preço deve ser um valor numérico positivo";
        }
        
        // Validação da quantidade
        if (empty($dados['qtd'])) {
            $erros[] = "A quantidade do produto é obrigatória";
        } elseif (!is_numeric($dados['qtd']) || $dados['qtd'] < 0) {
            $erros[] = "A quantidade deve ser um valor numérico não negativo";
        }
        
        // Validação do código
        if (!empty($dados['codigo']) && strlen($dados['codigo']) > 50) {
            $erros[] = "O código do produto deve ter no máximo 50 caracteres";
        }
        
        // Validação da unidade
        if (empty($dados['unidade'])) {
            $erros[] = "A unidade de medida é obrigatória";
        }
        
        // Validação do NCM
        if (!empty($dados['ncm']) && !preg_match('/^\d{8}$/', $dados['ncm'])) {
            $erros[] = "O NCM deve conter 8 dígitos numéricos";
        }
        
        // Se houver erros, retorna os erros
        if (!empty($erros)) {
            return [
                'status' => false,
                'mensagem' => 'Erros de validação',
                'erros' => $erros
            ];
        }
        
        // Se não houver erros, edita o produto
        try {
            $resultado = $this->productModel->editarPoducts(
                $id,
                $dados['nome'],
                $dados['descricao'],
                $dados['preco'],
                $dados['imagem'] ?? null,
                $dados['qtd'],
                $dados['codigo_produto_integracao'] ?? null,
                $dados['codigo'] ?? null,
                $dados['unidade'],
                $dados['ncm'] ?? null,
                $dados['descr_detalhada'] ?? null,
                $dados['obs_internas'] ?? null
            );
            
            if ($resultado > 0) {
                return [
                    'status' => true,
                    'mensagem' => 'Produto atualizado com sucesso'
                ];
            } else {
                return [
                    'status' => false,
                    'mensagem' => 'Nenhuma alteração realizada ou produto não encontrado'
                ];
            }
        } catch (Exception $e) {
            return [
                'status' => false,
                'mensagem' => 'Erro ao atualizar produto: ' . $e->getMessage()
            ];
        }
    }
    
    // Exclui produto com validação
    public function excluirProduto($id) {
        // Validação do ID
        if (empty($id) || !is_numeric($id) || $id <= 0) {
            return [
                'status' => false,
                'mensagem' => 'ID de produto inválido'
            ];
        }
        
        try {
            $resultado = $this->productModel->excluirProduct($id);
            
            if ($resultado > 0) {
                return [
                    'status' => true,
                    'mensagem' => 'Produto excluído com sucesso'
                ];
            } else {
                return [
                    'status' => false,
                    'mensagem' => 'Produto não encontrado'
                ];
            }
        } catch (Exception $e) {
            return [
                'status' => false,
                'mensagem' => 'Erro ao excluir produto: ' . $e->getMessage()
            ];
        }
    }
}
?>