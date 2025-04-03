<?php
// Start output buffering at the beginning of the file
ob_start();

class ProductController {
    private $productModel;
    private $categoryModel;
    private $db;

    public function __construct($db) {
        $this->db = $db;
        $this->productModel = new ProductModel($db);
        $this->categoryModel = new CategoryModel($db);
    }

    // Exibe a página de listagem de produtos
    public function getAllProducts() {
        $produtos = $this->productModel->getAllProducts();
        $categorias = $this->categoryModel->getAllCategories();
        
        include_once ROOT_PATH . '/app/views/produtos/index.php';
    }

    // Exibe o formulário para adicionar um novo produto
    public function adicionar() {
        $categorias = $this->categoryModel->getAllCategories();
        $produtos = $this->productModel->getAllProducts(); // Buscar todos os produtos
        
        // Debug
        error_log("Método adicionar chamado. Produtos encontrados: " . count($produtos));
        
        include_once ROOT_PATH . '/app/views/produtos/adicionar.php';
    }

    // Processa o formulário de adição de produto
    public function adicionarProduto() {
        try {
            // Validar dados
            if(empty($_POST['nome'])) {
                throw new Exception("O nome do produto é obrigatório");
            }
            
            if(empty($_POST['categoria_id'])) {
                throw new Exception("A categoria do produto é obrigatória");
            }
            
            if(!is_numeric($_POST['preco']) || $_POST['preco'] < 0) {
                throw new Exception("O preço deve ser um valor numérico positivo");
            }
            
            if(!is_numeric($_POST['quantidade']) || $_POST['quantidade'] < 0) {
                throw new Exception("A quantidade deve ser um valor numérico positivo");
            }
            
            $nome = trim($_POST['nome']);
            $descricao = trim($_POST['descricao'] ?? '');
            $categoriaId = (int)$_POST['categoria_id'];
            $preco = (float)$_POST['preco'];
            $quantidade = (int)$_POST['quantidade'];
            
            // Add debugging
            error_log("Tentando cadastrar produto: " . $nome);
            
            // Cadastrar produto
            $produtoId = $this->productModel->adicionarProduto($nome, $descricao, $categoriaId, $preco, $quantidade);
            
            if($produtoId) {
                $_SESSION['mensagem'] = "Produto cadastrado com sucesso!";
                $_SESSION['tipo_mensagem'] = "success";
                // Redirecionar para a lista de produtos
                header("Location: index.php?pagina=produtos");
                exit();
            } else {
                throw new Exception("Erro ao cadastrar produto.");
            }
            
            // Redirecionar
            header("Location: index.php?pagina=produtos&acao=adicionar");
            exit();
            
        } catch(Exception $e) {
            $_SESSION['mensagem'] = $e->getMessage();
            $_SESSION['tipo_mensagem'] = "danger";
            header("Location: index.php?pagina=produtos&acao=listar");
            exit();
        }
    }

    // Exibe o formulário para editar um produto
    public function editarProdutoForm($produtoId) {
        try {
            $produto = $this->productModel->getProductById($produtoId);
            
            if(!$produto) {
                throw new Exception("Produto não encontrado");
            }
            
            $categorias = $this->categoryModel->getAllCategories();
            
            include_once ROOT_PATH . '/app/views/produtos/editar.php';
            
        } catch(Exception $e) {
            $_SESSION['mensagem'] = $e->getMessage();
            $_SESSION['tipo_mensagem'] = "danger";
            header("Location: index.php?pagina=produtos&acao=listar");
            exit();
        }
    }

    // Processa o formulário de edição de produto
    public function editarProduto($produtoId) {
        try {
            // Validar dados
            if(empty($_POST['nome'])) {
                throw new Exception("O nome do produto é obrigatório");
            }
            
            if(empty($_POST['categoria_id'])) {
                throw new Exception("A categoria do produto é obrigatória");
            }
            
            if(!is_numeric($_POST['preco']) || $_POST['preco'] < 0) {
                throw new Exception("O preço deve ser um valor numérico positivo");
            }
            
            if(!is_numeric($_POST['quantidade']) || $_POST['quantidade'] < 0) {
                throw new Exception("A quantidade deve ser um valor numérico positivo");
            }
            
            $nome = trim($_POST['nome']);
            $descricao = trim($_POST['descricao'] ?? '');
            $categoriaId = (int)$_POST['categoria_id'];
            $preco = (float)$_POST['preco'];
            $quantidade = (int)$_POST['quantidade'];
            
            // Atualizar produto
            $result = $this->productModel->editarProduto($produtoId, $nome, $descricao, $categoriaId, $preco, $quantidade);
            
            if($result) {
                $_SESSION['mensagem'] = "Produto atualizado com sucesso!";
                $_SESSION['tipo_mensagem'] = "success";
            } else {
                $_SESSION['mensagem'] = "Nenhuma alteração realizada.";
                $_SESSION['tipo_mensagem'] = "info";
            }
            
            // Redirecionar
            header("Location: index.php?pagina=produtos&acao=listar");
            exit();
            
        } catch(Exception $e) {
            $_SESSION['mensagem'] = $e->getMessage();
            $_SESSION['tipo_mensagem'] = "danger";
            header("Location: index.php?pagina=produtos&acao=editar&id=$produtoId");
            exit();
        }
    }

    // Processa a exclusão de um produto
    public function excluirProduto($produtoId) {
        try {
            // Verificar se o produto existe
            $produto = $this->productModel->getProductById($produtoId);
            if(!$produto) {
                throw new Exception("Produto não encontrado");
            }
            
            // Excluir produto
            $result = $this->productModel->excluirProduto($produtoId);
            
            if($result) {
                $_SESSION['mensagem'] = "Produto excluído com sucesso!";
                $_SESSION['tipo_mensagem'] = "success";
            } else {
                $_SESSION['mensagem'] = "Erro ao excluir produto.";
                $_SESSION['tipo_mensagem'] = "danger";
            }
            
        } catch(Exception $e) {
            $_SESSION['mensagem'] = $e->getMessage();
            $_SESSION['tipo_mensagem'] = "danger";
        }
        
        // Redirecionar
        header("Location: index.php?pagina=produtos&acao=listar");
        exit();
    }

    // Method to get a product by its ID
    public function getProductById($produtoId) {
        try {
            $produto = $this->productModel->getProductById($produtoId);
            
            if (!$produto) {
                throw new Exception("Produto não encontrado");
            }
            
            return $produto;
        } catch (Exception $e) {
            $_SESSION['mensagem'] = $e->getMessage();
            $_SESSION['tipo_mensagem'] = "danger";
            return null;
        }
    }
}

// Flush the output buffer
ob_end_flush();
?>