# Sistema Web de Gerenciamento de Usuários

Sistema web desenvolvido para praticar desenvolvimento de aplicações web, programação em PHP, integração com banco de dados MySQL e conceitos básicos de segurança da informação.

## Tecnologias utilizadas

- PHP
- MySQL
- HTML5
- CSS3
- XAMPP
- VS Code

## Funcionalidades

- Cadastro de usuários
- Login e autenticação
- Controle de sessão
- Edição de usuários
- Exclusão de usuários
- Pesquisa de usuários
- Dashboard
- Sistema de permissões com `admin` e `user`
- Criptografia de senhas com `password_hash`
- Verificação de senhas com `password_verify`
- Proteção contra CSRF
- Consultas preparadas com PDO/MySQLi
- Prevenção básica contra XSS com `htmlspecialchars`

## Segurança

O projeto utiliza algumas boas práticas básicas de segurança, como:

- Armazenamento seguro de senhas utilizando hash
- Controle de acesso por sessão
- Proteção contra CSRF
- Consultas preparadas para evitar SQL Injection
- Validação de dados
- Controle de permissões entre usuários comuns e administradores
- Escape de dados exibidos na página

## Banco de dados

O sistema utiliza um banco de dados MySQL chamado: `sistema_web`

O banco possui uma tabela `usuarios` com os seguintes campos:

- `id`
- `nome`
- `email`
- `senha`
- `role`

O arquivo `migracao_roles.sql` contém a alteração necessária para adicionar o sistema de permissões entre usuários comuns e administradores.

## Como executar o projeto

1. Instale o XAMPP.
2. Inicie o Apache e o MySQL.
3. Coloque a pasta `sistema-web` dentro de:
    C:\xampp\htdocs\

4. Crie o banco de dados sistema_web no phpMyAdmin.
5. Configure a conexão no arquivo conexao.php.
6. Execute o arquivo migracao_roles.sql.
7. Acesse no navegador:
    http://localhost/sistema-web/

## Objetivo

Este projeto foi desenvolvido para colocar em prática conhecimentos de programação, desenvolvimento web, banco de dados e segurança da informação.

Durante o desenvolvimento, foram praticados conceitos como PHP, MySQL, CRUD, autenticação, sessões, validação de dados e segurança básica de aplicações web.
