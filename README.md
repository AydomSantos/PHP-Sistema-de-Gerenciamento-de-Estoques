# Sistema de Gerenciamento de Estoque

Este é um sistema web para gerenciamento de estoque desenvolvido em PHP, que permite controlar produtos, categorias e gerar relatórios de inventário.

## Requisitos do Sistema

- PHP 7.4 ou superior
- SQLite3
- Servidor web (Apache/Nginx)
- Navegador web moderno

## Instalação

1. Clone ou baixe este repositório para seu ambiente local
2. Certifique-se de que o PHP está instalado em seu sistema
3. Configure seu servidor web para apontar para o diretório do projeto
4. Verifique se as permissões do arquivo do banco de dados (config/estoque.db) estão corretas

## Estrutura do Projeto

```
/
├── api/                    # APIs para dados do dashboard
├── categories/             # Gerenciamento de categorias
├── config/                 # Configurações do sistema
├── controller/             # Controladores
├── includes/               # Arquivos de cabeçalho e rodapé
├── model/                  # Modelos de dados
├── products/               # Gerenciamento de produtos
├── reports/                # Geração de relatórios
└── view/                   # Visualizações
```

## Configuração do Banco de Dados

O sistema utiliza SQLite como banco de dados. O arquivo do banco de dados está localizado em `config/estoque.db`. A conexão é configurada em `config/database.php`:

```php
<?php
try {
    $conn = new PDO("sqlite:" . __DIR__ . "/estoque.db");
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    echo "Erro de conexão: " . $e->getMessage();
}
```

## Funcionalidades Principais

### 1. Gerenciamento de Produtos

- **Adicionar Produto**: Acesse `products/add.php`
  ```php
  // Exemplo de inserção de produto
  $stmt = $conn->prepare("INSERT INTO produtos (nome, descricao, quantidade, preco, user_id) VALUES (?, ?, ?, ?, ?)");
  $stmt->execute([$nome, $descricao, $quantidade, $preco, $user_id]);
  ```

- **Editar Produto**: Acesse `products/edit.php`
  ```php
  // Exemplo de atualização de produto
  $stmt = $conn->prepare("UPDATE produtos SET nome = ?, descricao = ?, quantidade = ?, preco = ? WHERE id = ?");
  $stmt->execute([$nome, $descricao, $quantidade, $preco, $id]);
  ```

### 2. Gerenciamento de Categorias

- **Gerenciar Categorias**: Acesse `categories/manage.php`
  ```php
  // Exemplo de associação de produto com categoria
  $stmt = $conn->prepare("INSERT INTO produto_categorias (produto_id, categoria_id) VALUES (?, ?)");
  $stmt->execute([$produto_id, $categoria_id]);
  ```

### 3. Relatórios

- **Gerar Relatório de Inventário**: Acesse `reports/generate.php`
  ```php
  // Exemplo de consulta para relatório
  $stmt = $conn->prepare("SELECT p.*, c.nome as categoria FROM produtos p LEFT JOIN categorias c ON p.categoria_id = c.id WHERE p.user_id = ?");
  $stmt->execute([$user_id]);
  ```

## Sistema de Autenticação e Administração

### Login e Controle de Acesso

- **Login**: Implementado em `login.php`
  ```php
  // Exemplo de verificação de login
  session_start();
  if (!isset($_SESSION['user_id'])) {
      header('Location: login.php');
      exit();
  }
  ```

### Sistema Administrativo

- **Painel do Administrador**: Acesse `admin/manage_users.php`
  - Gerenciamento completo de usuários
  - Controle de permissões de acesso
  - Monitoramento de atividades do sistema

#### Funcionalidades Administrativas

1. **Gerenciamento de Usuários**:
   - Criar novos usuários
   - Editar informações de usuários existentes
   - Desativar/ativar contas
   - Redefinir senhas

2. **Controle de Permissões**:
   - Definir níveis de acesso (Admin/Usuário)
   - Configurar permissões específicas
   - Gerenciar restrições de acesso

3. **Monitoramento**:
   - Visualizar logs de atividades
   - Acompanhar alterações no sistema
   - Gerar relatórios de uso

#### Criação de Administrador

- Execute o script `create_admin.php` para criar o primeiro usuário administrador
- Utilize credenciais seguras para acesso administrativo
- Mantenha backup das credenciais em local seguro

## Dicas de Uso

1. **Sempre faça logout** ao terminar de usar o sistema
2. **Mantenha o backup** do arquivo do banco de dados
3. **Verifique as permissões** dos arquivos e diretórios
4. **Utilize senhas fortes** para maior segurança

## Solução de Problemas

1. **Erro de conexão com banco de dados**:
   - Verifique se o arquivo estoque.db existe
   - Confirme as permissões do arquivo

2. **Erro ao fazer login**:
   - Limpe os cookies do navegador
   - Verifique se as credenciais estão corretas

3. **Erro ao gerar relatório**:
   - Verifique se há produtos cadastrados
   - Confirme se o usuário tem permissão

## Contribuição

Para contribuir com o projeto:

1. Faça um fork do repositório
2. Crie uma branch para sua feature
3. Faça commit das mudanças
4. Envie um pull request

## Suporte

Em caso de dúvidas ou problemas:

1. Consulte a documentação
2. Verifique os logs de erro
3. Entre em contato com o administrador do sistema

## Kanban do Projeto

### 📋 A Fazer

#### Configuração Inicial
- [ ] Configurar ambiente de desenvolvimento
- [ ] Instalar dependências do PHP
- [ ] Configurar servidor web (Apache/Nginx)
- [ ] Criar estrutura inicial do banco de dados SQLite

#### Backend
- [ ] Implementar sistema de autenticação
- [ ] Desenvolver CRUD de usuários
- [ ] Criar API para produtos
- [ ] Criar API para categorias
- [ ] Implementar sistema de permissões

#### Frontend
- [ ] Desenvolver interface de login
- [ ] Criar dashboard principal
- [ ] Implementar interface de gerenciamento de produtos
- [ ] Implementar interface de gerenciamento de categorias
- [ ] Desenvolver sistema de relatórios

### 🔄 Em Andamento

#### Backend
- [ ] Estruturar conexão com banco de dados
- [ ] Criar modelos de dados (Models)
- [ ] Desenvolver controladores (Controllers)

#### Frontend
- [ ] Definir layout do sistema
- [ ] Implementar templates base

### ✅ Concluído

#### Planejamento
- [x] Definir requisitos do sistema
- [x] Criar estrutura de diretórios
- [x] Documentar setup inicial
- [x] Estabelecer padrões de código