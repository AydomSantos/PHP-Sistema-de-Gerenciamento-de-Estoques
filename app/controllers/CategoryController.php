<?php

class CategoryController {
    private $vCategoryModel;
    private $vDb;

    public function __construct($vDb) {
        $this->vDb = $vDb;
        $this->vCategoryModel = new CategoryModel($vDb);
    }

    // Exibe a página de listagem de categorias
    public function listarCategorias() {
        $vCategorias = $this->vCategoryModel->getAllCategories();
        
        // Verificar se há mensagem na sessão
        $vMensagem = null;
        $vTipoMensagem = null;

        if(isset($_SESSION['mensagem'])) {
            $vMensagem = $_SESSION['mensagem'];
            $vTipoMensagem = $_SESSION['tipo_mensagem'] ?? 'info';
            
            // Limpar mensagem da sessão
            unset($_SESSION['mensagem']);
            unset($_SESSION['tipo_mensagem']);
        }
        
        include_once ROOT_PATH . '/app/views/categorias/index.php';
    }

    // Exibe o formulário para adicionar uma nova categoria
    public function adicionarCategoriaForm() {
        include_once ROOT_PATH . '/app/views/categorias/adicionar.php';
    }

    // Processa o formulário de adição de categoria
    public function adicionarCategoria() {
        try {
            // Validar dados
            if(empty($_POST['nome'])) {
                throw new Exception("O nome da categoria é obrigatório");
            }
            
            $vNome = trim($_POST['nome']);
            $vDescricao = trim($_POST['descricao'] ?? '');
            
            // Cadastrar categoria
            $vCategoriaId = $this->vCategoryModel->cadastrarCategoria($vNome, $vDescricao);
            
            if($vCategoriaId) {
                $_SESSION['mensagem'] = "Categoria cadastrada com sucesso!";
                $_SESSION['tipo_mensagem'] = "success";
            } else {
                throw new Exception("Erro ao cadastrar categoria.");
            }
            
            // Redirecionar
            header("Location: categoria_list.php");
            exit();
            
        } catch(Exception $e) {
            $_SESSION['mensagem'] = $e->getMessage();
            $_SESSION['tipo_mensagem'] = "danger";
            header("Location: categoria_form.php");
            exit();
        }
    }

    // Exibe o formulário para editar uma categoria
    public function editarCategoriaForm($vCategoriaId) {
        try {
            $vCategoria = $this->vCategoryModel->getCategoryById($vCategoriaId);
            
            if(!$vCategoria) {
                throw new Exception("Categoria não encontrada");
            }
            
            include_once ROOT_PATH . '/app/views/categorias/editar.php';
            
        } catch(Exception $e) {
            $_SESSION['mensagem'] = $e->getMessage();
            $_SESSION['tipo_mensagem'] = "danger";
            header("Location: categoria_list.php");
            exit();
        }
    }

    // Processa o formulário de edição de categoria
    public function editarCategoria($vCategoriaId) {
        try {
            // Validar dados
            if(empty($_POST['nome'])) {
                throw new Exception("O nome da categoria é obrigatório");
            }
            
            $vNome = trim($_POST['nome']);
            $vDescricao = trim($_POST['descricao'] ?? '');
            
            // Verificar se a categoria existe
            $vCategoria = $this->vCategoryModel->getCategoryById($vCategoriaId);
            if(!$vCategoria) {
                throw new Exception("Categoria não encontrada");
            }
            
            // Atualizar categoria
            $vResult = $this->vCategoryModel->editarCategoria($vCategoriaId, $vNome, $vDescricao);
            
            if($vResult) {
                $_SESSION['mensagem'] = "Categoria atualizada com sucesso!";
                $_SESSION['tipo_mensagem'] = "success";
            } else {
                $_SESSION['mensagem'] = "Nenhuma alteração realizada.";
                $_SESSION['tipo_mensagem'] = "info";
            }
            
            // Redirecionar
            header("Location: categoria_list.php");
            exit();
            
        } catch(Exception $e) {
            $_SESSION['mensagem'] = $e->getMessage();
            $_SESSION['tipo_mensagem'] = "danger";
            header("Location: categoria_editar.php?id=$vCategoriaId");
            exit();
        }
    }

    // Processa a exclusão de uma categoria
    public function excluirCategoria($vCategoriaId) {
        try {
            // Verificar se a categoria existe
            $vCategoria = $this->vCategoryModel->getCategoryById($vCategoriaId);
            if(!$vCategoria) {
                throw new Exception("Categoria não encontrada");
            }
            
            // Excluir categoria
            $vResult = $this->vCategoryModel->excluirCategoria($vCategoriaId);
            
            if($vResult) {
                $_SESSION['mensagem'] = "Categoria excluída com sucesso!";
                $_SESSION['tipo_mensagem'] = "success";
            } else {
                $_SESSION['mensagem'] = "Nenhuma alteração realizada.";
                $_SESSION['tipo_mensagem'] = "info";
            }
            
        } catch(Exception $e) {
            $_SESSION['mensagem'] = $e->getMessage();
            $_SESSION['tipo_mensagem'] = "danger";
        }
        
        // Redirecionar
        header("Location: categoria_list.php");
        exit();
    }
    
    // Busca categoria por ID (para uso em APIs ou outros controllers)
    public function obterCategoria($vCategoriaId) {
        return $this->vCategoryModel->getCategoryById($vCategoriaId);
    }
    
    // Retorna todas as categorias (para uso em APIs ou outros controllers)
    public function obterTodasCategorias() {
        return $this->vCategoryModel->getAllCategories();
    }
}

?>