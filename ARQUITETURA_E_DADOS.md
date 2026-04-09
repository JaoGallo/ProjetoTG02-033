# 🗄️ ARQUITETURA DE DADOS E FLUXO COMPLETO

## 📊 Tabela De Banco De Dados: `users`

```sql
CREATE TABLE users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(255),                    -- Nome completo do usuário
    email VARCHAR(255) UNIQUE NULL,       -- Email (NULL para atirador novo)
    password VARCHAR(255),                -- Senha hasheada (SHA-256)
    ra VARCHAR(255) UNIQUE,               -- Registro Acadêmico (login)
    cpf VARCHAR(255) UNIQUE,              -- CPF (login alternativo)
    role ENUM('atirador', 'instructor', 'master'),  -- Papel/Permissão
    points INT DEFAULT 0,                 -- Pontos acumulados
    faults INT DEFAULT 0,                 -- Contagem de faltas
    photo VARCHAR(255) NULL,              -- Caminho arquivo de foto
    numero INT,                           -- Número do atirador
    turma VARCHAR(255),                   -- Turma/Ano (ex: "2026")
    is_cfc BOOLEAN DEFAULT FALSE,         -- Tem CFC?
    telefone VARCHAR(255),                -- Telefone para contato
    Nome_de_guerra VARCHAR(255) UNIQUE,   -- Codinome único (ex: "Alfa", "Bravo")
    created_at TIMESTAMP,                 -- Data criação (automático)
    updated_at TIMESTAMP                  -- Data última atualização (automático)
);
```

### Exemplo de Dados Na Tabela:

```
┌────┬──────────────┬─────────────────┬──────────┬─────────┬──────────────┬──────────────┬───────┬────────┬──────────┬────────┬──────┬────────┬─────────────┬─────────┐
│ id │ name         │ email           │ password │ ra      │ cpf          │ role         │ points│ faults │ photo    │ numero │turma │is_cfc  │telefone     │ nome_de │
│    │              │                 │          │         │              │              │       │        │          │        │      │        │             │ guerra  │
├────┼──────────────┼─────────────────┼──────────┼─────────┼──────────────┼──────────────┼───────┼────────┼──────────┼────────┼──────┼────────┼─────────────┼─────────┤
│ 1  │ João Silva   │ NULL            │ hash123  │ 202301  │ 123.456.789  │ atirador     │ 0     │ 0      │ NULL     │ 1      │ 2026 │ false  │ 11-98765    │ Alfa    │
│    │              │ (vazio!)        │          │         │              │              │       │        │          │        │      │        │             │         │
├────┼──────────────┼─────────────────┼──────────┼─────────┼──────────────┼──────────────┼───────┼────────┼──────────┼────────┼──────┼────────┼─────────────┼─────────┤
│ 2  │ Pedro Costa  │ pedro@email.com │ hash456  │ 202302  │ 987.654.321  │ atirador     │ 150   │ 2      │ foto.jpg │ 2      │ 2026 │ true   │ 11-99876    │ Bravo   │
│    │              │ (JÁ configurado)│          │         │              │              │       │        │          │        │      │        │             │         │
├────┼──────────────┼─────────────────┼──────────┼─────────┼──────────────┼──────────────┼───────┼────────┼──────────┼────────┼──────┼────────┼─────────────┼─────────┤
│ 3  │ Sgt. Campos  │ campos@email.com│ hash789  │ INSTR01 │ 111.222.333  │ instructor   │ 500   │ 0      │ campos.jpg│ NULL   │ 2026 │ true   │ 11-91111    │ Instrutor│
│    │              │ (configurado)   │          │         │              │              │       │        │          │        │      │        │             │         │
└────┴──────────────┴─────────────────┴──────────┴─────────┴──────────────┴──────────────┴───────┴────────┴──────────┴────────┴──────┴────────┴─────────────┴─────────┘
```

### Estados Possíveis:

```yaml
ATIRADOR NOVO (1º acesso):
  - email: NULL ou "" (vazio)
  - password: hash da senha inicial
  - role: "atirador"
  - MIDDLEWARE REDIRECIONA: para /primeiro-acesso

ATIRADOR CONFIGURADO (após 1º acesso):
  - email: joao@email.com
  - password: hash da nova senha
  - role: "atirador"
  - MIDDLEWARE PERMITE: acesso normal

INSTRUTOR / MASTER:
  - email: sempre preenchido
  - password: hasheada
  - role: "instructor" ou "master"
  - MIDDLEWARE PERMITE: acesso a todas as rotas com 'instructor'
```

---

## 🔗 Relacionamentos Entre Controllers e Models

### Diagrama De Fluxo: Dados → Banco

```
┌─────────────────────────────────┐
│  USER DIGITA NO FORMULÁRIO      │
│  (resources/views/login.blade) │
│  - RA: 202301001                │
│  - Senha: senha123              │
└──────────────┬──────────────────┘
               │
               ▼
┌──────────────────────────────────────┐
│ FORMULÁRIO ENVIA (POST /login)       │
│ $request = [                         │
│   'user' => '202301001',             │
│   'password' => 'senha123'           │
│ ]                                    │
└──────────────┬───────────────────────┘
               │
               ▼
┌──────────────────────────────────────┐
│ AuthController::login()              │
│ ├─ Valida $request                   │
│ ├─ $user = User::where(...)->first() │
│ │  └─ Query SQL: SELECT * FROM users │
│ │     WHERE ra = '202301001'         │
│ │     LIMIT 1                        │
│ └─ Auth::attempt() (verifica hash)   │
└──────────────┬───────────────────────┘
               │
               ▼
┌──────────────────────────────────────┐
│ App/Models/User.php                  │
│ - Estende Authenticatable (Laravel) │
│ - Representa tabela 'users'          │
│ - Métodos: save(), find(), where() │
└──────────────┬───────────────────────┘
               │
               ▼
┌──────────────────────────────────────┐
│ BANCO DE DADOS                       │
│ Tabela: users                        │
│ ┌─────────────────────────────────┐ │
│ │ id: 1                           │ │
│ │ name: João Silva                │ │
│ │ ra: 202301001                   │ │
│ │ email: NULL                     │ │
│ │ password: $2y$12$9qk...         │ │
│ │ role: atirador                  │ │
│ └─────────────────────────────────┘ │
└──────────────┬───────────────────────┘
               │
               ▼ LOGINZ OK, SESSION CRIADA
┌──────────────────────────────────────┐
│ REDIRECIONA PARA /dashboard          │
│ Session criada com ID de sessão      │
└──────────────────────────────────────┘
```

---

## 🔄 Fluxo Da Requisição: Do Cliente Ao Banco

### ETAPA 1: CLIENTE → SERVIDOR

```
1. Usuário digita URL ou clica um link
   └─ Request HTTP é gerada

   Exemplos:
   ├─ GET  https://localhost/dashboard
   ├─ POST https://localhost/login (com body)
   ├─ PUT  https://localhost/perfil (com body)
   ├─ DELETE https://localhost/atiradores/5
   └─ PATCH https://localhost/atiradores/5/toggle-cfc

2. Laravel recebe no arquivo: public/index.php (entry point)
   └─ Carrega bootstrap/app.php
   └─ Inicializa aplicação
   └─ Procura a rota correspondente
```

### ETAPA 2: ROUTING

```
Laravel procura em routes/web.php:

$request->path()     = "/dashboard"
$request->method()   = "GET"

Procura: Route::get('/dashboard', ...)
└─ ENCONTROU! Executa o handler

Se não encontrar → Erro 404 (Not Found)
```

### ETAPA 3: MIDDLEWARES

```
Middlewares são executados INFILEIRAnte:

Route::middleware(['auth', 'first_access'])->group(...)
                      │         │
                      └┬────────┘
                       └─ Ordem de execução

MIDDLEWARE 1: 'auth'
├─ Chama: Illuminate\Auth\Middleware\Authenticate
├─ Verifica: auth()->check() → usuário está logado?
├─ Se NÃO → redireciona para login
└─ Se SIM → $next($request) → continua

MIDDLEWARE 2: 'first_access'
├─ Chama: CheckFirstAccess::handle()
├─ Verifica: role === 'atirador' && empty(email)?
├─ Se SIM → redireciona para /primeiro-acesso
└─ Se NÃO → $next($request) → continua

RESULTADO:
├─ Passou em TODOS → vai para controller
└─ Falhou em qualquer um → redireciona e para aqui
```

### ETAPA 4: CONTROLLER

```
Controller recebe o $request:

AuthController::login(Request $request)
├─ Valida: $request->validate([...])
│  └─ Se falhar → volta com erros 422
│
├─ Acessa banco: User::where(...)->first()
│  └─ Envia SQL para banco de dados
│  └─ Recebe resultado
│
├─ Lógica: if ($user && Auth::attempt(...))
│  └─ Executa lógica de negócios
│  └─ Modifica dados se necessário
│
└─ Retorna:
   ├─ view('dashboard') → renderiza HTML
   ├─ redirect('/perfil') → redireciona
   ├─ response()->json([...]) → retorna JSON
   └─ back()->with('error', '...') → volta com dados
```

### ETAPA 5: BANCO DE DADOS

```
SQL Query Executada:

SELECT * FROM users 
WHERE ra = '202301001' 
   OR cpf = '123.456.789-00'
LIMIT 1

Banco retorna:
├─ Se encontrou → um registro (usuário)
├─ Se não encontrou → NULL
└─ Se erro SQL → exception

Resultado é convertido em objeto User (Model):

$user = new User([
    'id' => 1,
    'name' => 'João Silva',
    'ra' => '202301001',
    'email' => null,
    'password' => '$2y$12$9qk...',
    'role' => 'atirador',
    ...
])

Agora o controller pode usar: $user->email, $user->name, etc
```

### ETAPA 6: RESPONSE (RESPOSTA)

```
Controller executa um return:

return view('dashboard', compact('announcements'));

Laravel:
1. Procura arquivo: resources/views/dashboard.blade.php
2. Renderiza Blade template (prépara variáveis PHP)
3. Converte em HTML puro
4. Envia HTTP Response ao cliente:

   HTTP/1.1 200 OK
   Content-Type: text/html; charset=UTF-8
   
   <!DOCTYPE html>
   <html>
   <body>
     <h1>Dashboard</h1>
     ...
   </body>
   </html>

5. Navegador recebe e renderiza a página
```

---

## 🎯 Use Case Completo: Um Atirador Configura Sua Conta

```yaml
INÍCIO:
  Estado do Banco:
    id: 1
    email: NULL  ← VAZIO!
    password: hash_inicial
    role: atirador

PASSO 1: Usuário vai para http://localhost/
  Request:   GET /
  Response:  login.blade.php (formulário)

PASSO 2: Usuário preenche RA e Senha
  Digita:
    RA/CPF: 202301001
    Senha: senha_inicial

PASSO 3: Clica "Entrar"
  Request:   POST /login
  Payload:   { user: '202301001', password: 'senha_inicial' }

PASSO 4: AuthController::login() processa
  ├─ Valida campos ✅
  ├─ Query: SELECT * FROM users WHERE ra='202301001' 
  │  └─ RESULTADO: User com id=1, email=NULL, role=atirador
  ├─ Auth::attempt([ra, password])
  │  └─ Verifica: Hash::check('senha_inicial', $user->password) ✅
  ├─ Cria sessão
  └─ redirect('dashboard')

PASSO 5: GET /dashboard
  ├─ Middleware 'auth': logado? SIM ✅
  ├─ Middleware 'first_access': 
  │  ├─ role === 'atirador'? SIM ✅
  │  ├─ email vazio? SIM ✅
  │  └─ REDIRECIONA: /primeiro-acesso
  └─ User vê: formulário de configuração

PASSO 6: User preenche formulário
  Digita:
    Email: joao@email.com
    Senha: nova_senha_segura_123
    Confirmar: nova_senha_segura_123

PASSO 7: Clica "Salvar e Continuar"
  Request:   POST /primeiro-acesso
  Payload:   { 
    email: 'joao@email.com', 
    password: 'nova_senha_segura_123',
    password_confirmation: 'nova_senha_segura_123'
  }

PASSO 8: PrimeiroAcessoController::store() processa
  ├─ Valida:
  │  ├─ Email é único? SELECT * FROM users WHERE email='joao@email.com'
  │  │  └─ Nenhum resultado → ✅ É único
  │  └─ Senha atende requisitos? ✅ (min 8 chars, etc)
  ├─ $user = auth()->user() → retorna User com id=1
  ├─ $user->email = 'joao@email.com'
  ├─ $user->password = Hash::make('nova_senha_segura_123')
  │  └─ Resultado: "$2y$12$[78 caracteres com salt]"
  ├─ $user->save()
  │  └─ UPDATE users SET email='...', password='...' WHERE id=1
  └─ redirect('dashboard')

BANCO APÓS PASSO 8:
  id: 1
  email: joao@email.com  ← PREENCHIDO!
  password: $2y$12$... ← NOVA SENHA
  role: atirador

PASSO 9: GET /dashboard (novamente)
  ├─ Middleware 'auth': logado? SIM ✅
  ├─ Middleware 'first_access':
  │  ├─ role === 'atirador'? SIM ✅
  │  ├─ email vazio? NÃO ❌ (tem email!)
  │  └─ Continua normalmente...
  ├─ Renderiza dashboard.blade.php
  └─ User vê: Dashboard com avisos

PASSO 10: Próximo login
  POST /login
  Payload: { user: '202301001', password: 'nova_senha_segura_123' }
  
  AuthController::login() verifica:
  ├─ Query: SELECT * FROM users WHERE ra='202301001'
  │  └─ RESULTADO: User com email='joao@email.com'
  ├─ Hash::check('nova_senha_segura_123', $user->password)
  │  └─ RESULTADO: true ✅
  ├─ Cria sessão
  └─ Acessa dashboard direto (sem redirecionar para primeiro acesso!)
```

---

## 🔐 Onde Os Dados São Armazenados?

### SESSION (Dados Temporários)

```php
// Servidor armazena em: storage/framework/sessions/
// Um arquivo por seção:
//   /storage/framework/sessions/abc123def456...

// Conteúdo da sessão (serializado):
[
    'login_web_59ba36addc2b2f9401580f14' => 1,  // ID do usuário
    '_token' => 'abc123def456...',               // CSRF token
    'success' => 'Conta configurada com sucesso',
    'error' => null,
    ...
]

// Como acessar na aplicação:
auth()->user()              → Retorna o User object
auth()->check()             → true/false
session('success')          → 'Conta configurada...'
$_SESSION['chave']          → Acesso direto (não recomendado)
```

### BANCO DE DADOS (Dados Permanentes)

```sql
-- Usuário é armazenado PERMANENTEMENTE na tabela users
-- Até que seja deletado ou modificado

-- Exemplo de registro:
SELECT * FROM users WHERE id = 1;

id: 1
name: João Silva
email: joao@email.com
password: $2y$12$9qk...  ← Hasheada (não pode ser revertida)
ra: 202301001
cpf: 123.456.789-00
role: atirador
created_at: 2026-04-08 10:30:00
updated_at: 2026-04-09 14:45:00
```

### COOKIES (Dados do Cliente)

```
Laravel envia Cookie ao navegador:

Set-Cookie: XSRF-TOKEN=abc123...; Path=/; HttpOnly
Set-Cookie: laravel_session=xyz789...; Path=/; HttpOnly; SameSite=Lax

Navegador armazena e envia em cada requisição:

Cookie: XSRF-TOKEN=abc123...
Cookie: laravel_session=xyz789...

Laravel extrai esses valores e reconstrói a sessão.
```

### STORAGE (Arquivos)

```
Fotos de perfil são salvas em:
storage/app/public/

Exemplo:
user_1_photo.jpg
user_2_photo.jpg
user_3_profile.png

Reference no banco:
users.photo = "user_1_photo.jpg"

No blade:
<img src="{{ asset('storage/' . auth()->user()->photo) }}">
```

---

## 📱 Fluxo Full-Stack: De URL a HTML

```
┌────────────────────────────────────────────────────────────────────┐
│                         NAVEGADOR                                  │
│  Usuário digita: localhost/dashboard                               │
└────────────────┬─────────────────────────────────────────────────┘
                 │
                 │ HTTP Request:
                 │ GET /dashboard HTTP/1.1
                 │ Host: localhost
                 │ Cookie: laravel_session=xyz789...
                 │
                 ▼
┌────────────────────────────────────────────────────────────────────┐
│                    SERVIDOR WEB (Apache/Nginx)                    │
│  Recebe request na porta 80                                        │
│  Encaminha para: public/index.php                                  │
└────────────────┬─────────────────────────────────────────────────┘
                 │
                 ▼
┌────────────────────────────────────────────────────────────────────┐
│         public/index.php (Entry Point do Laravel)                  │
│  require 'bootstrap/app.php';                                      │
└────────────────┬─────────────────────────────────────────────────┘
                 │
                 ▼
┌────────────────────────────────────────────────────────────────────┐
│              bootstrap/app.php (Inicialização)                     │
│  └─ Registra middlewares                                           │
│  └─ Carrega providers                                              │
│  └─ Inicializa aplicação                                           │
└────────────────┬─────────────────────────────────────────────────┘
                 │
                 ▼
┌────────────────────────────────────────────────────────────────────┐
│             routes/web.php (Roteamento)                            │
│  Route::middleware(['auth', 'first_access'])->group(...)          │
│      Route::get('/dashboard', function() {...});                   │
│                                   ↑                                 │
│                          ROTA ENCONTRADA ✅                        │
└────────────────┬─────────────────────────────────────────────────┘
                 │
                 ▼
┌────────────────────────────────────────────────────────────────────┐
│           Middleware Stack (Executados em Série)                   │
│                                                                    │
│  ┌─ middleware 1: 'auth'                                          │
│  │  ├─ auth()->check() → SIM ✅                                   │
│  │  └─ return $next($request)                                     │
│  │                                                                │
│  ├─ middleware 2: 'first_access'                                 │
│  │  ├─ Verifica role === 'atirador' && email vazio               │
│  │  ├─ NÃO (email está preenchido)                              │
│  │  └─ return $next($request)                                    │
│  │                                                                │
│  └─ ✅ Todos passaram! Vai para o handler                       │
└────────────────┬─────────────────────────────────────────────────┘
                 │
                 ▼
┌────────────────────────────────────────────────────────────────────┐
│              Route Handler (Closure Function)                       │
│  function() {                                                       │
│    $announcements = Announcement::where(...)->get();               │
│          ↓                                                          │
│         [Query ao banco de dados]                                  │
│          ↓                                                          │
│    return view('dashboard', compact('announcements'));             │
│  }                                                                  │
└────────────────┬─────────────────────────────────────────────────┘
                 │
                 ▼
┌────────────────────────────────────────────────────────────────────┐
│            BANCO DE DADOS (MySQL/PostgreSQL)                       │
│  Query SQL Executada:                                              │
│  SELECT * FROM announcements                                       │
│  WHERE turma = '2026'                                              │
│  ORDER BY priority DESC, created_at DESC                           │
│                                                                    │
│  Resultado:                                                        │
│  [                                                                 │
│    {id: 1, title: 'Aviso 1'...},                                  │
│    {id: 2, title: 'Aviso 2'...},                                  │
│  ]                                                                 │
└────────────────┬─────────────────────────────────────────────────┘
                 │ Resultado retorna
                 ▼
┌────────────────────────────────────────────────────────────────────┐
│           View Engine (Blade Renderer)                             │
│  resources/views/dashboard.blade.php                               │
│  @foreach ($announcements as $announcement)                        │
│    <div class="card">                                              │
│      <h2>{{ $announcement->title }}</h2>                           │
│      <p>{{ $announcement->content }}</p>                           │
│    </div>                                                          │
│  @endforeach                                                       │
│                                                                    │
│  Blade processa template e gera HTML:                             │
│  <div class="card">                                                │
│    <h2>Aviso 1</h2>                                                │
│    <p>Conteúdo do aviso 1</p>                                      │
│  </div>                                                            │
│  <div class="card">                                                │
│    <h2>Aviso 2</h2>                                                │
│    <p>Conteúdo do aviso 2</p>                                      │
│  </div>                                                            │
└────────────────┬─────────────────────────────────────────────────┘
                 │ HTML gerado
                 ▼
┌────────────────────────────────────────────────────────────────────┐
│           HTTP Response Construída                                  │
│  HTTP/1.1 200 OK                                                   │
│  Content-Type: text/html; charset=UTF-8                            │
│  Content-Length: 18456                                             │
│  Set-Cookie: laravel_session=...                                   │
│                                                                    │
│  <!DOCTYPE html>                                                   │
│  <html>                                                            │
│    <head>                                                          │
│      <title>Dashboard</title>                                      │
│      ...                                                           │
│    </head>                                                         │
│    <body>                                                          │
│      <h1>DASHBOARD</h1>                                            │
│      <div class="card">                                            │
│        <h2>Aviso 1</h2>                                            │
│        ...                                                         │
│      </div>                                                        │
│    </body>                                                         │
│  </html>                                                           │
└────────────────┬─────────────────────────────────────────────────┘
                 │ Response enviada
                 ▼
┌────────────────────────────────────────────────────────────────────┐
│                      NAVEGADOR                                     │
│  Recebe response HTTP 200                                          │
│  Parse HTML                                                        │
│  Carrega CSS (style.css)                                           │
│  Executa JavaScript (app.js)                                       │
│  Renderiza página visualmente                                      │
│                                                                    │
│  ┌────────────────────────────────────┐                           │
│  │ [Sidebar] DASHBOARD                │                           │
│  │ - Início                           │                           │
│  │ - Atiradores (instrutor)           │ ← Aqui fica email         │
│  │ - Perfil                           │   e password              │
│  │                                    │                           │
│  │ [Conteúdo]                         │                           │
│  │ 📋 AVISOS                          │                           │
│  │                                    │                           │
│  │ ┌─ Aviso 1                        │                           │
│  │ │ Conteúdo do aviso 1             │                           │
│  │ │ Data: 09/04/2026                │                           │
│  │ └                                  │                           │
│  │                                    │                           │
│  │ ┌─ Aviso 2                        │                           │
│  │ │ Conteúdo do aviso 2             │                           │
│  │ │ Data: 08/04/2026                │                           │
│  │ └                                  │                           │
│  └────────────────────────────────────┘                           │
│                                                                    │
│  ✅ Usuário vê a página renderizada!                              │
└────────────────────────────────────────────────────────────────────┘
```

---

## 🔒 Segurança: Hash De Senha

### Como Funciona:

```php
// PASSWORDHASH:
// Quando usuário configura senha: "nova_senha_segura_123"

$senha = "nova_senha_segura_123";
$hash = Hash::make($senha);
// Resultado: $2y$12$9qk.Y3PzJVpHKH.sHz/w2eWVQMFfVeA2xDGHmzjPMqT3XcP7o8iWS

// Na próxima vez que user tenta fazer login com:
$senha_digitada = "nova_senha_segura_123";

Hash::check($senha_digitada, $hash);
// Resultado: true ✅

// Se digita errado:
$senha_digitada = "outra_senha_123";

Hash::check($senha_digitada, $hash);
//resultado: false ❌
```

### Por que não apenas salvar em texto plano?

```
❌ ERRADO (texto plano):
SELECT password FROM users WHERE id=1;
→ Resultado: "nova_senha_segura_123"

Se banco for hackeado → TODOS as senhas foram expostas!
Usuário pode usar mesma senha em outros sites → risco massivo!

✅ CERTO (hasheada):
SELECT password FROM users WHERE id=1;
→ Resultado: "$2y$12$9qk.Y3PzJVpHKH.sHz/w2e..."

Se banco for hackeado → hashes não podem ser revertidos para senha original
Mesmo que hacker tenha hash, não consegue fazer login (precisa da senha original)

EXTRA: Laravel usa Bcrypt (algoritmo de hashing forte)
├─ Adiciona "salt" (random string)
├─ Usa múltiplas iterações
└─ Muito mais seguro que MD5 ou SHA1
```

---

## 📋 Resumo: Estrutura De Requisições Por Tipo

### GET Requests (Buscar dados)

```
GET /dashboard
GET /perfil
GET /primeiro-acesso
GET /avisos/{id}
GET /atiradores

Middleware:     auth, first_access, instructor (alguns)
Tipo Resposta:  HTML View
Banco:          SELECT queries (leitura)
Body:           Vazio
```

### POST Requests (Criar dados)

```
POST /login
POST /primeiro-acesso
POST /atiradores
POST /usuarios/{user}/photo
POST /avisos

Middleware:     auth, first_access, instructor
Tipo Resposta:  Redirect ou View
Banco:          INSERT queries (criar)
Body:           Form data
```

### PUT Requests (Atualizar completamente)

```
PUT /perfil
PUT /atiradores/{user}
PUT /avisos/{id}

Middleware:     auth, first_access, instructor (alguns)
Tipo Resposta:  Redirect com mensagem
Banco:          UPDATE queries (modificar)
Body:           Form data completa
```

### PATCH Requests (Atualizar parcialmente)

```
PATCH /atiradores/{user}/toggle-cfc

Middleware:     auth, first_access, instructor
Tipo Resposta:  Redirect
Banco:          UPDATE queries (uma coluna)
Body:           Form data parcial
```

### DELETE Requests (Deletar dados)

```
DELETE /atiradores/{user}
DELETE /avisos/{id}

Middleware:     auth, first_access, instructor
Tipo Resposta:  Redirect com mensagem
Banco:          DELETE queries
Body:           Vazio ou Form data
```

---

**Este documento completa a compreensão total da arquitetura!**
