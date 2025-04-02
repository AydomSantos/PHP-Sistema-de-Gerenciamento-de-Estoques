<?php

class UserController {
    private $vUserModel;
    private $vDb;

    public function __construct($vDb) {
        $this->vDb = $vDb;
        $this->vUserModel = new UserModel($vDb);
    }

    // Exibe a página de login
    public function loginForm() {
        // Verificar se já está logado
       if(isset($_SESSION['user_id'])) {
           header('Location: index.php');
           exit();
       }

       // Verificar se há mensagem na sessão
       $vMensagem = null;
       $vTipoMensagem = null;

       if(isset($_SESSION['mensagem'])) {
           $vMensagem = $_SESSION['mensagem'];
           $vTipoMensagem = $_SESSION['tipo_mensagem'] ?? 'info';

           // Lipar a mensagem da sessão
           unset($_SESSION['mensagem']);
           unset($_SESSION['tipo_mensagem']);
       }

       include_once ROOT_PATH . '/app/views/usuarios/login.php';
    }

    // Processa o formulário de login
    public function login() {
      try{
        if(empty($_POST['email']) || empty($_POST['senha'])) {
            throw new Exception('Preencha todos os campos.');
        }

        $vEmail = trim($_POST['email']);
        $vSenha = $_POST['senha'];

        // Autenticar usuário
        // Corrigido: autenticarUsuario -> authenticateUser
        $vUser = $this->vUserModel->authenticateUser($vEmail, $vSenha);

        if($vUser) {
           // Iniciar a sessão
           $_SESSION['user_id'] = $vUser['id'];
           $_SESSION['user_nome'] = $vUser['nome'];
           $_SESSION['user_email'] = $vUser['email'];
           $_SESSION['user_cargo'] = $vUser['cargo'];

           // Redirecionar para a página inicial
           header('Location: index.php');
           exit();
        }else {
            throw new Exception('Credenciais inválidas.');
        }
      } catch(Exception $e) {
        // Tratar a exceção
        $_SESSION['mensagem'] = $e->getMessage();
        $_SESSION['tipo_mensagem'] = 'danger';
        header('Location: login.php');
        exit();
        
        }
    }

    // Processa o logout
    public function logout() {
        // Destruir a sessão
        session_destroy();

        // Redirecionar para a página de login
        header('Location: login.php');
        exit();
    }

    // Exibe a página de listagem de usuários
    public function listarUsuarios() {
        if(!isset($_SESSION['user_cargo']) || $_SESSION['user_cargo'] !== 'admin') {
            header('Location: index.php');
            exit();
        }

        // Corrigido: listarUsuarios -> getAllUsers
        $vUsuarios = $this->vUserModel->getAllUsers();
        $vMensagem = null;
        $vTipoMensagem = null;

        if(isset($_SESSION['mensagem'])){
            $vMensagem = $_SESSION['mensagem'];
            $vTipoMensagem = $_SESSION['tipo_mensagem'] ?? 'info';

            // Limpar mensagem da sessão
            unset($_SESSION['mensagem']);
            unset($_SESSION['tipo_mensagem']);
        }
        
        include_once ROOT_PATH . '/app/views/usuarios/index.php';
    }

    // Exibe o formulário para adicionar um novo usuário
    public function adicionarUsuarioForm() {
        if(!isset($_SESSION['user_cargo']) || $_SESSION['user_cargo']!== 'admin') {
            header('Location: index.php');
            exit();
        }

        include_once ROOT_PATH. '/app/views/usuarios/adicionar.php';
    }

    // Processa o formulário de adição de usuário
    public function adicionarUsuario() {
       try{
        if(!isset($_SESSION['user_cargo']) || $_SESSION['user_cargo'] !== 'admin'){
            throw new Exception("Acesso negado. Você não tem permissão para realizar esta ação.");
        }

        // Validar dados
        if(empty($_POST['nome']) || empty($_POST['email']) || empty($_POST['senha']) || empty($_POST['cargo'])){
            throw new Exception("Preencha todos os campos.");
        }

        $vNome = trim($_POST['nome']);
        $vEmail = trim($_POST['email']);
        $vSenha = $_POST['senha'];
        $vCargo = isset($_POST['cargo']) ? $_POST['cargo'] : 'usuario';

        // Cadastrar usuário
        // Corrigido: cadastrarUsuario -> createUser
        $vUserId = $this->vUserModel->createUser($vNome, $vEmail, $vSenha, $vCargo);

        if($vUserId) {
            $_SESSION['mensagem'] = "Usuário cadastrado com sucesso!";
            $_SESSION['tipo_mensagem'] = "success";
        }else {
            throw new Exception("Erro ao cadastrar usuário.");
        }

        // Redirecionar para a página de listagem de usuários
        header("Location: index.php?pagina=usuarios");
        exit();

       }catch(Exception $e) {
        $_SESSION['mensagem'] = $e->getMessage();
        $_SESSION['tipo_mensagem'] = "danger";
        header("Location: index.php?pagina=adicionar_usuario");
        exit();
       }
    }

    // Exibe o formulário para editar um usuário
    public function editarUsuarioForm($vUserId) {
      try{
        if(!isset($_SESSION['user_cargo']) || $_SESSION['user_cargo']!== 'admin') {
            throw new Exception("Acesso negado. Você não tem permissão para realizar esta ação.");
        }

        // Corrigido: obterUsuarioPorId -> getUserById
        $vUsuario = $this->vUserModel->getUserById($vUserId);

        if(!$vUsuario) {
            throw new Exception("Usuário não encontrado.");
        }

        include_once ROOT_PATH. '/app/views/usuarios/editar.php';

      } catch(Exception $e) {
        $_SESSION['mensagem'] = $e->getMessage();
        $_SESSION['tipo_mensagem'] = "danger";
        header("Location: index.php?pagina=usuarios");
        exit();
      }
    }

    // Processa o formulário de edição de usuário
    public function editarUsuario($vUserId) {
      try{
        if(!isset($_SESSION['user_cargo']) || $_SESSION['user_cargo']!== 'admin') {
            throw new Exception("Acesso negado. Você não tem permissão para realizar esta ação.");
        }

        // Validar dados
        if(empty($_POST['nome']) || empty($_POST['email']) || empty($_POST['cargo'])){
            throw new Exception("Preencha todos os campos obrigatórios.");
        }

        $vNome = trim($_POST['nome']);
        $vEmail = trim($_POST['email']);
        $vCargo = $_POST['cargo'];
        $vAtivo = isset($_POST['ativo']) ? 1 : 0;

        // Atualizar usuário
        // Corrigido: atualizarUsuario -> updateUser
        $vResult = $this->vUserModel->updateUser($vUserId, $vNome, $vEmail, $vCargo, null);

        if($vResult) {
            $_SESSION['mensagem'] = "Usuário atualizado com sucesso!";
            $_SESSION['tipo_mensagem'] = "success";
        }else {
            throw new Exception("Erro ao atualizar usuário.");
        }

        // Redirecionar para a página de listagem de usuários
        header("Location: index.php?pagina=usuarios");
        exit();

      }catch(Exception $e) {
        $_SESSION['mensagem'] = $e->getMessage();
        $_SESSION['tipo_mensagem'] = "danger";
        header("Location: index.php?pagina=editar_usuario&id=$vUserId");
        exit();
      }
    }

    // Exibe o formulário para alterar senha
    public function alterarSenhaForm() {
        include_once ROOT_PATH . '/app/views/usuarios/alterar_senha.php';
    }

    // Processa o formulário de alteração de senha
    public function alterarSenha() {
        try{
            // Verificar se está logado
            if(!isset($_SESSION['user_id'])) {
                throw new Exception("Acesso negado. Você não está logado.");
            }

            // Validar dados
            if(empty($_POST['senha_atual']) || empty($_POST['nova_senha']) || empty($_POST['confirmar_senha'])) {
                throw new Exception("Preencha todos os campos.");
            }

            if($_POST['nova_senha'] !== $_POST['confirmar_senha']) {
                throw new Exception("As novas senhas não coincidem.");
            }

            $vUserId = $_SESSION['user_id'];
            $vSenhaAtual = $_POST['senha_atual'];
            $vNovaSenha = $_POST['nova_senha'];

            // Verificar se a senha atual está correta e alterar senha
            // Implementação simplificada já que não temos o método alterarSenha
            $vUser = $this->vUserModel->getUserById($vUserId);
            
            if(!$vUser || !password_verify($vSenhaAtual, $vUser['senha'])) {
                throw new Exception("Senha atual incorreta.");
            }
            
            // Atualizar a senha
            $vResult = $this->vUserModel->updateUser($vUserId, $vUser['nome'], $vUser['email'], $vUser['cargo'], $vNovaSenha);

            if($vResult) {
                $_SESSION['mensagem'] = "Senha alterada com sucesso!";
                $_SESSION['tipo_mensagem'] = "success";
                header("Location: index.php?pagina=perfil");
                exit();
            }else {
                throw new Exception("Erro ao alterar senha.");
            }
        } catch(Exception $e) {
            $_SESSION['mensagem'] = $e->getMessage();
            $_SESSION['tipo_mensagem'] = "danger";
            header("Location: index.php?pagina=alterar_senha");
            exit();
        }
    }

    // Processa a exclusão de um usuário
    public function excluirUsuario($vUserId) {
       try{
        if(!isset($_SESSION['user_cargo']) || $_SESSION['user_cargo']!== 'admin') {
            throw new Exception("Acesso negado. Você não tem permissão para realizar esta ação.");
        }

        // Verificar se o usuário a ser excluído é diferente do usuário logado
        if($vUserId == $_SESSION['user_id']) {
            throw new Exception("Você não pode excluir seu próprio usuário.");
        }

        // Excluir usuário
        // Corrigido: excluirUsuario -> deleteUser
        $vResult = $this->vUserModel->deleteUser($vUserId);

        if($vResult) {
            $_SESSION['mensagem'] = "Usuário excluído com sucesso!";
            $_SESSION['tipo_mensagem'] = "success";
        }else{
            throw new Exception("Erro ao excluir usuário.");
        }
       }catch(Exception $e) {
        $_SESSION['mensagem'] = $e->getMessage();
        $_SESSION['tipo_mensagem'] = "danger";
       } 
       header("Location: index.php?pagina=usuarios");
       exit();
    }

    // Exibir perfil do usuário
    public function perfil() {
        if(!isset($_SESSION['user_id'])) {
            header('Location: login.php');
            exit();
        } 

        // Corrigido: obterUsuarioPorId -> getUserById
        $vUsuario = $this->vUserModel->getUserById($_SESSION['user_id']);
        
        if(!$vUsuario) {
            $_SESSION['mensagem'] = "Erro ao carregar perfil.";
            $_SESSION['tipo_mensagem'] = "danger";
            header('Location: index.php');
            exit();
        }
        
        $vMensagem = null;
        $vTipoMensagem = null;

        if(isset($_SESSION['mensagem'])) {
            $vMensagem = $_SESSION['mensagem'];
            $vTipoMensagem = $_SESSION['tipo_mensagem'] ?? 'info';
            
            // Limpar mensagem da sessão
            unset($_SESSION['mensagem']);
            unset($_SESSION['tipo_mensagem']);
        }
        
        include_once ROOT_PATH . '/app/views/usuarios/perfil.php';
    }
}

?>