<?php
// Start output buffering at the very beginning
ob_start();

// Iniciar sessão
session_start();

// Definir constantes
define('ROOT_PATH', __DIR__);
define('PUBLIC_PATH', __DIR__ . '/public');
define('APP_PATH', __DIR__ . '/app');
define('CONFIG_PATH', __DIR__ . '/config');

// Função para verificar se um arquivo existe
function verificarArquivo($caminho) {
    if (!file_exists($caminho)) {
        echo "<div style='color:red; padding:20px; background-color:#ffeeee; border:1px solid #ffaaaa; margin:20px;'>";
        echo "<h3>Erro: Arquivo não encontrado</h3>";
        echo "<p>O arquivo <strong>$caminho</strong> não foi encontrado.</p>";
        echo "<p>Verifique se todos os arquivos necessários foram criados.</p>";
        echo "</div>";
        return false;
    }
    return true;
}

// Verificar e incluir arquivos necessários
$arquivosNecessarios = [
    'config/database.php',
    'app/models/UserModel.php',
    'app/models/CategoryModel.php',
    'app/models/ProductModel.php',
    'app/controllers/UserController.php',
    'app/controllers/CategoryController.php',
    'app/controllers/ProductController.php'
];

$todosArquivosExistem = true;
foreach ($arquivosNecessarios as $arquivo) {
    if (!verificarArquivo($arquivo)) {
        $todosArquivosExistem = false;
    } else {
        require_once $arquivo;
    }
}

if (!$todosArquivosExistem) {
    exit();
}

// Criar conexão com o banco de dados
try {
    $database = new Database();
    $db = $database->getConnection();
    
    if (!$db) {
        throw new Exception("Não foi possível conectar ao banco de dados.");
    }
} catch (Exception $e) {
    echo "<div style='color:red; padding:20px; background-color:#ffeeee; border:1px solid #ffaaaa; margin:20px;'>";
    echo "<h3>Erro de Conexão</h3>";
    echo "<p>" . $e->getMessage() . "</p>";
    echo "<p>Verifique se o banco de dados foi configurado corretamente.</p>";
    echo "<p>Execute o script de criação de tabelas:</p>";
    echo "<code>php setup_database.php</code>";
    echo "</div>";
    exit();
}

// Verificar se o usuário está logado
$logado = isset($_SESSION['user_id']);

// Definir a página a ser exibida
$pagina = isset($_GET['pagina']) ? $_GET['pagina'] : 'home';

// Se não estiver logado, redirecionar para a página de login
if (!$logado && $pagina != 'login') {
    header('Location: login.php');
    exit();
}

// Verificar se o arquivo de cabeçalho existe
if (verificarArquivo('app/views/templates/header.php')) {
    include_once 'app/views/templates/header.php';
}

// Exibir a página solicitada
try {
    switch ($pagina) {
        case 'home':
            if (verificarArquivo('app/views/home.php')) {
                include_once 'app/views/home.php';
            }
            break;
        case 'categorias':
            $categoryController = new CategoryController($db);
            
            // Verificar se há uma ação específica
            if (isset($_GET['acao'])) {
                $acao = $_GET['acao'];
                
                switch ($acao) {
                    case 'adicionar':
                        $categoryController->adicionar();
                        break;
                        
                    case 'salvar':
                        $categoryController->adicionarCategoria();
                        break;
                        
                    case 'editar':
                        if (isset($_GET['id'])) {
                            $categoryController->editarCategoriaForm($_GET['id']);
                        } else {
                            $_SESSION['mensagem'] = "ID da categoria não fornecido";
                            $_SESSION['tipo_mensagem'] = "danger";
                            header("Location: index.php?pagina=categorias");
                            exit();
                        }
                        break;
                        
                    case 'atualizar':
                        if (isset($_POST['id'])) {
                            $categoryController->editarCategoria($_POST['id']);
                        } else {
                            $_SESSION['mensagem'] = "ID da categoria não fornecido";
                            $_SESSION['tipo_mensagem'] = "danger";
                            header("Location: index.php?pagina=categorias");
                            exit();
                        }
                        break;
                        
                    case 'excluir':
                        if (isset($_GET['id'])) {
                            $categoryController->excluirCategoria($_GET['id']);
                        } else {
                            $_SESSION['mensagem'] = "ID da categoria não fornecido";
                            $_SESSION['tipo_mensagem'] = "danger";
                            header("Location: index.php?pagina=categorias");
                            exit();
                        }
                        break;
                        
                    default:
                        $categoryController->listarCategorias();
                        break;
                }
            } else {
                $categoryController->listarCategorias();
            }
            break;
        case 'produtos':
            $productController = new ProductController($db);
            
            // Verificar se há uma ação específica
            if (isset($_GET['acao'])) {
                $acao = $_GET['acao'];
                
                switch ($acao) {
                    case 'adicionar':
                        $productController->adicionar();
                        break;
                        
                    case 'salvar':
                        $productController->adicionarProduto();
                        break;
                    
                    case 'editar':
                        if (isset($_GET['id'])) {
                            $productController->editarProdutoForm($_GET['id']);
                        } else {
                            $_SESSION['mensagem'] = "ID do produto não fornecido";
                            $_SESSION['tipo_mensagem'] = "danger";
                            header("Location: index.php?pagina=produtos");
                            exit();
                        }
                        break;
                        
                    case 'atualizar':
                        if (isset($_POST['id'])) {
                            $productController->editarProduto($_POST['id']);
                        } else {
                            $_SESSION['mensagem'] = "ID do produto não fornecido";
                            $_SESSION['tipo_mensagem'] = "danger";
                            header("Location: index.php?pagina=produtos");
                            exit();
                        }
                        break;
                        
                    case 'excluir':
                        if (isset($_GET['id'])) {
                            $productController->excluirProduto($_GET['id']);
                        } else {
                            $_SESSION['mensagem'] = "ID do produto não fornecido";
                            $_SESSION['tipo_mensagem'] = "danger";
                            header("Location: index.php?pagina=produtos");
                            exit();
                        }
                        break;
                        
                    default:
                        $productController->getAllProducts();
                        break;
                }
            } else {
                $productController->getAllProducts();
            }
            break;
        case 'usuarios':
            $userController = new UserController($db);
            $userController->listarUsuarios();
            break;
        default:
            if (verificarArquivo('app/views/home.php')) {
                include_once 'app/views/home.php';
            }
            break;
    }
} catch (Exception $e) {
    echo "<div class='alert alert-danger'>";
    echo "<h4>Erro ao carregar a página</h4>";
    echo "<p>" . $e->getMessage() . "</p>";
    echo "</div>";
}

// Verificar se o arquivo de rodapé existe
if (verificarArquivo('app/views/templates/footer.php')) {
    include_once 'app/views/templates/footer.php';
}