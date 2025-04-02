# Sistema de Gerenciamento de Estoque

## Sobre o Projeto
Este sistema de gerenciamento de estoque foi desenvolvido para facilitar o controle de produtos, categorias e usuários em um ambiente empresarial. Utilizando uma arquitetura MVC (Model-View-Controller) em PHP, o sistema oferece uma interface intuitiva e funcionalidades robustas para o gerenciamento completo do estoque.

## Funcionalidades Principais

### Autenticação e Controle de Acesso
- Sistema de login seguro com controle de sessão
- Níveis de acesso diferenciados (Administrador e Usuário)
- Proteção contra acessos não autorizados
- Gerenciamento de sessões de usuário

### Gerenciamento de Usuários
- Cadastro e manutenção de usuários do sistema
- Definição de níveis de acesso
- Alteração de senhas com criptografia
- Controle de status de usuários (Ativo/Inativo)

### Controle de Produtos
- Cadastro completo de produtos
- Categorização de itens
- Controle de estoque
- Histórico de movimentações

### Gestão de Categorias
- Organização hierárquica de produtos
- Cadastro e manutenção de categorias
- Relatórios por categoria

## Tecnologias Utilizadas

### Backend
- PHP 7.4+
- MySQL/MariaDB
- Arquitetura MVC

### Frontend
- HTML5
- CSS3
- Bootstrap 5
- JavaScript
- Font Awesome (ícones)

## Requisitos do Sistema
- Servidor Web (Apache/Nginx)
- PHP 7.4 ou superior
- MySQL 5.7 ou superior
- Extensões PHP:
  - PDO
  - PDO_MySQL
  - mbstring

## Instalação

1. Clone o repositório:
```bash
git clone [URL_DO_REPOSITÓRIO]
cd sistema-gerenciamento-estoque
```

2. Configure o banco de dados:
- Crie um banco de dados MySQL
- Copie o arquivo `config/database.example.php` para `config/database.php`
- Configure as credenciais do banco no arquivo `config/database.php`

3. Execute o script de instalação:
```bash
php setup_database.php
```

4. Configure o servidor web:
- Configure o documento root para a pasta do projeto
- Certifique-se que o mod_rewrite está habilitado (Apache)

5. Acesse o sistema:
- Abra o navegador e acesse `http://seu-dominio.com`

## Usuário Padrão
Após a instalação, você pode acessar o sistema com as seguintes credenciais:

- **Email:** admin@sistema.com
- **Senha:** admin123

**Importante:** Altere a senha do administrador após o primeiro acesso!

## Estrutura do Projeto
```
sistema-gerenciamento-estoque/
├── app/
│   ├── controllers/    # Controladores do sistema
│   ├── models/         # Modelos de dados
│   ├── views/          # Interfaces do usuário
│   └── helpers/        # Funções auxiliares
├── config/            # Arquivos de configuração
├── public/            # Arquivos públicos (CSS, JS, imagens)
├── vendor/            # Dependências
└── index.php          # Ponto de entrada da aplicação
```

## Segurança
- Proteção contra SQL Injection
- Validação de dados de entrada
- Sanitização de saída
- Controle de sessão seguro
- Senhas criptografadas

## Próximas Atualizações

### Em Desenvolvimento
- [ ] Dashboard com gráficos e estatísticas
- [ ] Sistema de relatórios avançados
- [ ] Exportação de dados para CSV/PDF
- [ ] Controle de lotes e validade

### Planejado
- [ ] API REST para integração
- [ ] Aplicativo mobile
- [ ] Sistema de notificações
- [ ] Backup automático

## Contribuição
Contribuições são bem-vindas! Para contribuir:

1. Faça um Fork do projeto
2. Crie uma branch para sua feature (`git checkout -b feature/AmazingFeature`)
3. Commit suas mudanças (`git commit -m 'Add some AmazingFeature'`)
4. Push para a branch (`git push origin feature/AmazingFeature`)
5. Abra um Pull Request

## Suporte
Para suporte e dúvidas, por favor abra uma issue no repositório do projeto.

## Licença
Este projeto está sob a licença MIT. Veja o arquivo `LICENSE` para mais detalhes.

## Autores
Desenvolvido por [Seu Nome/Equipe]

---

**Nota:** Este é um projeto em desenvolvimento ativo. Novas funcionalidades e melhorias são adicionadas regularmente.