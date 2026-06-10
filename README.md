# Tasks Colaborativas — API RESTful

API para gestão de tarefas colaborativas, desenvolvida com PHP 8 seguindo o padrão arquitetural MVC.

## Autora

Keila Fernanda da Silva 

---

## Sumário

- [Visão Geral](#visão-geral)
- [Decisões Arquiteturais](#decisões-arquiteturais)
- [Modelagem de Dados](#modelagem-de-dados)
- [Endpoints](#endpoints)
- [Configuração e Deploy](#configuração-e-deploy)
- [Testes Automatizados](#testes-automatizados)

---

## Visão Geral

Sistema de gestão de tarefas colaborativas que permite a usuários autenticados criar, editar, atribuir e concluir tarefas. Qualquer usuário autenticado pode visualizar, editar e excluir qualquer tarefa, promovendo um ambiente de colaboração aberta.

A API segue o estilo REST, utiliza autenticação via JWT e retorna respostas em JSON.

---

## Decisões Arquiteturais

### Padrão MVC

A aplicação adota o padrão **Model-View-Controller (MVC)**, com separação clara de responsabilidades:

- **Models** (`app/Models/`) — acesso ao banco de dados e regras de negócio de dados
- **Controllers** (`app/Controllers/`) — orquestração das requisições e respostas HTTP
- **Core** (`app/Core/`) — infraestrutura: roteamento, autenticação, conexão com banco

Essa escolha facilita a manutenção, testabilidade e evolução independente de cada camada.

### Autenticação JWT

A autenticação é feita via token JWT (biblioteca `firebase/php-jwt`). O token é gerado no login e deve ser enviado no header `Authorization: Bearer <token>` em todas as rotas protegidas. O `AuthMiddleware` valida o token antes de cada operação autenticada.

### Banco de Dados Relacional (MySQL)

MySQL foi escolhido por ser um banco relacional maduro, com suporte nativo no XAMPP (ambiente de desenvolvimento), e adequado ao modelo de dados estruturado da aplicação (usuários e tarefas com relacionamentos definidos).

### HTTP Foundation (Symfony)

O componente `symfony/http-foundation` é utilizado para abstrair a manipulação de requisições e respostas HTTP, sem a necessidade de um framework completo.

---

## Modelagem de Dados

### Diagrama

```
usuarios
├── id          INT PK AUTO_INCREMENT
├── nome        VARCHAR(255) NOT NULL
├── email       VARCHAR(255) UNIQUE NOT NULL
├── senha       VARCHAR(255) NOT NULL
├── created_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP
└── updated_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP

tarefas
├── id              INT PK AUTO_INCREMENT
├── titulo          VARCHAR(255) NOT NULL
├── descricao       TEXT
├── status          ENUM('pendente', 'em_andamento', 'concluida') DEFAULT 'pendente'
├── criado_por_id   INT FK → usuarios.id ON DELETE SET NULL
├── atribuido_a_id  INT FK → usuarios.id ON DELETE SET NULL
├── created_at      TIMESTAMP DEFAULT CURRENT_TIMESTAMP
└── updated_at      TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
```

### Relações

- Um usuário pode criar várias tarefas (`criado_por_id`)
- Um usuário pode ter várias tarefas atribuídas (`atribuido_a_id`)
- Ao excluir um usuário, suas tarefas ficam sem atribuição (`SET NULL`)

---

## Endpoints

A documentação interativa completa está disponível via Swagger UI em:

```
http://localhost/mvc/docs
```

### Autenticação

| Método | Rota | Descrição | Auth |
|--------|------|-----------|------|
| POST | `/auth/login` | Login, retorna token JWT | Não |
| POST | `/auth/logout` | Logout | Sim |

**Exemplo de login:**
```json
POST /auth/login
{
  "email": "usuario@email.com",
  "senha": "123456"
}
```

**Resposta:**
```json
{
  "message": "Login realizado com sucesso",
  "token": "eyJ...",
  "usuario": { "id": 1, "nome": "Ana", "email": "ana@email.com" }
}
```

---

### Usuários

| Método | Rota | Descrição | Auth |
|--------|------|-----------|------|
| POST | `/users` | Criar usuário | Não |
| GET | `/users` | Listar todos os usuários | Sim |
| GET | `/users/{id}` | Obter usuário por ID | Sim |
| PUT | `/users/{id}` | Atualizar usuário (requer senha atual) | Sim |
| DELETE | `/users/{id}` | Remover próprio usuário | Sim |

**Exemplo de criação:**
```json
POST /users
{
  "nome": "Ana Silva",
  "email": "ana@email.com",
  "senha": "123456"
}
```

**Exemplo de atualização:**
```json
PUT /users/1
Authorization: Bearer <token>
{
  "nome": "Ana Silva Souza",
  "email": "ana.nova@email.com",
  "senha_atual": "123456",
  "senha": "nova_senha"
}
```

---

### Tarefas

| Método | Rota | Descrição | Auth |
|--------|------|-----------|------|
| POST | `/tasks` | Criar tarefa | Sim |
| GET | `/tasks` | Listar todas as tarefas | Sim |
| GET | `/tasks/{id}` | Obter tarefa por ID | Sim |
| GET | `/tasks?assignedTo={userId}` | Listar tarefas de um usuário | Sim |
| PUT | `/tasks/{id}` | Atualizar tarefa | Sim |
| DELETE | `/tasks/{id}` | Remover tarefa | Sim |

**Exemplo de criação:**
```json
POST /tasks
Authorization: Bearer <token>
{
  "titulo": "Implementar login",
  "descricao": "Criar autenticação JWT",
  "atribuido_a_id": 2
}
```

**Exemplo de atualização:**
```json
PUT /tasks/1
Authorization: Bearer <token>
{
  "titulo": "Implementar login e refresh token",
  "status": "em_andamento",
  "atribuido_a_id": 3
}
```

**Status válidos:** `pendente`, `em_andamento`, `concluida`

---

## Configuração e Deploy

### Pré-requisitos

- PHP 8.0+
- MySQL 5.7+
- Composer
- XAMPP (ou servidor Apache equivalente)

### Instalação

**1. Clone o repositório:**
```bash
git clone <url-do-repositorio>
cd mvc
```

**2. Instale as dependências:**
```bash
composer install
```

**3. Configure o ambiente:**

Crie o arquivo `.env` na raiz do projeto:
```env
DB_HOST=localhost
DB_NAME=tasks_colaborativas
DB_USER=root
DB_PASS=
JWT_SECRET=sua_chave_secreta_aqui
```

**4. Crie o banco de dados:**
```sql
CREATE DATABASE tasks_colaborativas CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

**5. Execute as migrations:**
```bash
php migrate.php
```

**6. Configure o Apache:**

Certifique-se de que o `mod_rewrite` está habilitado e que `AllowOverride All` está configurado para o diretório do projeto. O arquivo `.htaccess` já está incluído no repositório.

**7. Acesse:**
```
http://localhost/mvc/
```

### Estrutura do Projeto

```
mvc/
├── app/
│   ├── Controllers/       # Controllers HTTP
│   │   ├── AuthController.php
│   │   ├── TaskController.php
│   │   ├── UserController.php
│   │   └── HomeController.php
│   ├── Core/              # Infraestrutura
│   │   ├── AuthMiddleware.php
│   │   ├── Bootstrap.php
│   │   ├── Controller.php
│   │   ├── Database.php
│   │   ├── Model.php
│   │   └── Router.php
│   ├── Models/            # Modelos de dados
│   │   ├── Tarefa.php
│   │   └── Usuario.php
│   └── views/             # Frontend HTML
├── docs/                  # Swagger UI
│   ├── index.html
│   └── swagger.yaml
├── tests/                 # Testes automatizados
│   ├── ControllersTest.php
│   ├── TarefaTest.php
│   └── UsuarioTest.php
├── vendor/
├── .env
├── .htaccess
├── composer.json
└── index.php
```

---

## Testes Automatizados

### Estratégia

Os testes são implementados com **PHPUnit 9** e cobrem duas camadas:

- **Models** — testes de integração verificando CRUD completo de usuários e tarefas
- **Controllers** — testes unitários com mocks dos models, verificando validações e códigos de resposta HTTP sem dependência de banco

A abordagem de controllers testáveis utiliza uma subclasse que intercepta respostas JSON em vez de chamar `exit`, permitindo testar cada branch de validação de forma isolada.

### Executando os testes

```bash
# Apenas testes
vendor/bin/phpunit

# Com relatório de cobertura no terminal
vendor/bin/phpunit --coverage-text

# Com relatório de cobertura em HTML
vendor/bin/phpunit --coverage-html coverage/
```

> **Requisito:** Xdebug instalado e habilitado com `xdebug.mode=coverage` no `php.ini` para gerar relatórios de cobertura.

O relatório HTML é gerado em `coverage/index.html`.

### Métricas de Cobertura

| Classe | Métodos Cobertos | Linhas Cobertas |
|--------|-----------------|-----------------|
| `App\Models\Usuario` | 75% (6/8) | 93% (40/43) |
| `App\Models\Tarefa` | 62% (5/8) | 82% (52/63) |
| Total geral | ~18% (12/67) | ~24% (93/389) |

> A cobertura está concentrada nos Models, onde reside a lógica de dados crítica. Os Controllers são cobertos via classes testáveis que replicam a lógica de negócio.

### Casos de Teste

**UserController (12 testes):**
- Criação com sucesso, sem nome, sem email, sem senha, email inválido, email duplicado
- Listagem de usuários
- Busca por ID válido, ID inválido, não encontrado
- Exclusão com sucesso, sem permissão, não encontrado

**TaskController (14 testes):**
- Criação com sucesso, sem título, erro interno
- Listagem de tarefas
- Busca por ID válido, ID inválido, não encontrado
- Atualização com sucesso, ID inválido, não encontrada, body vazio, sem alteração
- Exclusão com sucesso, ID inválido, não encontrada

**Models (32 testes):**
- CRUD completo de usuários e tarefas
- Validações de unicidade de email
- Atribuição e desatribuição de tarefas
- Busca por ID, email e listagem geral
