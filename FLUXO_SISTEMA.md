# 🏗️ ANÁLISE COMPLETA DO SISTEMA LARAVEL - TIRO DE GUERRA

## 📋 Índice
1. [Estrutura Geral Do Laravel](#estrutura-geral-do-laravel)
2. [Fluxo De Autenticação E Primeiro Acesso](#fluxo-de-autenticação-e-primeiro-acesso)
3. [Sistema De Roles E Permissões](#sistema-de-roles-e-permissões)
4. [Estrutura De Middlewares](#estrutura-de-middlewares)
5. [Fluxo De Requisições Completo](#fluxo-de-requisições-completo)

---

## 🔧 Estrutura Geral Do Laravel

### O que é Laravel?
Laravel é um framework PHP que segue o padrão **MVC (Model-View-Controller)**, facilitando a construção de aplicações web estruturadas e seguras.

### Estrutura De Pastas Do Projeto:
```
app/
  ├── Http/
  │   ├── Controllers/      ← Lógica da aplicação
  │   ├── Middleware/       ← Filtros de requisição
  │   └── ...
  ├── Models/               ← Classes de dados (Banco de dados)
  └── ...

routes/
  └── web.php              ← Definição de rotas

resources/
  └── views/               ← Templates HTML (Blade)

config/
  └── app.php, auth.php    ← Configurações da aplicação

bootstrap/
  └── app.php              ← Inicialização (Middleware registration)

database/
  ├── migrations/          ← Criação de tabelas
  └── seeders/             ← Preenchimento de dados iniciais
```

---

## 🔐 Fluxo De Autenticação E Primeiro Acesso

### 📍 ETAPA 1️⃣: USUÁRIO ACESSA A APLICAÇÃO

**1. Usuário abre o navegador e vai para `http://localhost/`**

   **Arquivo:** [routes/web.php](routes/web.php#L1)
   ```php
   Route::get('/', [AuthController::class, 'showLogin'])->name('login');
   //                                                      ↓
   //        Chama o método showLogin() do AuthController
   ```

   **Fluxo:** 
   - O Laravel intercepta a requisição GET na rota "/"
   - Procura pelo método `showLogin()` no `AuthController`

---

### 📍 ETAPA 2️⃣: O AUTHCONTROLLER RETORNA A PÁGINA DE LOGIN

   **Arquivo:** [app/Http/Controllers/AuthController.php](app/Http/Controllers/AuthController.php#L9)
   ```php
   public function showLogin()
   {
       return view('login');  // 1️⃣ Retorna a view 'login.blade.php'
   }
   ```

   **Fluxo:**
   - O método retorna a view `login.blade.php`
   - O Laravel renderiza o HTML e envia para o navegador

---

### 📍 ETAPA 3️⃣: FORMULÁRIO DE LOGIN RENDERIZADO

   **Arquivo:** [resources/views/login.blade.php](resources/views/login.blade.php#L1)
   ```html
   <form class="login-form" action="{{ url('/login') }}" method="POST">
       @csrf
       
       <input type="text" name="user" placeholder="Digite sua identificação">
       <!-- Campo aceita RA (Registro Acadêmico) ou CPF -->
       
       <input type="password" name="password" placeholder="Digite sua senha">
       
       <button type="submit">Entrar</button>
   </form>
   ```

   **Fluxo:**
   - O HTML mostra o formulário de login no navegador
   - Usuário digita RA/CPF e senha
   - Clica no botão "Entrar"

---

### 📍 ETAPA 4️⃣: ENVIO DOS DADOS DE LOGIN (POST)

   **Arquivo:** [routes/web.php](routes/web.php#L8)
   ```php
   Route::post('/login', [AuthController::class, 'login']);
   //                                            ↓
   //          Chama o método login() do AuthController
   ```

   **Fluxo:**
   - Formulário envia POST para `/login`
   - Laravel chama o método `login()` no `AuthController`

---

### 📍 ETAPA 5️⃣: VALIDAÇÃO E AUTENTICAÇÃO

   **Arquivo:** [app/Http/Controllers/AuthController.php](app/Http/Controllers/AuthController.php#L14)
   ```php
   public function login(Request $request)
   {
       // PASSO 1: Validação
       $credentials = $request->validate([
           'user' => ['required', 'string'],
           'password' => ['required'],
       ]);
       // ✅ Verifica se os campos foram preenchidos
       
       // PASSO 2: Buscar usuário por RA ou CPF
       $user = User::where('ra', $credentials['user'])
                   ->orWhere('cpf', $credentials['user'])
                   ->first();
       // 🔍 Procura na tabela 'users' por RA OU CPF
       
       // PASSO 3: Verificação de Credenciais
       if ($user && Auth::attempt(['ra' => $user->ra, 'password' => $credentials['password']])) {
           // ✅ Se usuário existe E senha está correta:
           
           $request->session()->regenerate();
           // 🔐 Cria uma nova sessão (segurança)
           
           return redirect()->intended('dashboard');
           // 🚀 Redireciona para o dashboard
       }
       
       // ❌ Se não conseguiu autenticar:
       return back()->withErrors([
           'user' => 'As credenciais informadas não coincidem com nossos registros.',
       ])->onlyInput('user');
       // Volta para a página de login com mensagem de erro
   }
   ```

   **Fluxo:**
   - Valida se os campos foram preenchidos
   - Busca o usuário no banco de dados pela RA ou CPF
   - Verifica se a senha está correta (usando hash)
   - Se tudo OK → redireciona para dashboard
   - Se falhar → volta para login com erro

---

### 📍 ETAPA 6️⃣: MIDDLEWARE `auth` E `first_access`

   **Arquivo:** [routes/web.php](routes/web.php#L11)
   ```php
   Route::middleware(['auth', 'first_access'])->group(function () {
       //  ↓                  ↓
       //  Middleware 1      Middleware 2
       // (Autenticação)    (Verifica primeiro acesso)
   ```

   Esses middlewares funcionam como **FILTROS** que verificam as permissões antes de acessar as rotas.

   **Ordem de execução quando usuário acessa uma rota protegida:**

   #### 1️⃣ **Middleware `auth`** (Configurado no Laravel)
   ```php
   // Verifica se o usuário está autenticado
   if (!auth()->check()) {  // Se NÃO está logado
       return redirect('login');  // Redireciona para login
   }
   ```
   
   ✅ **Se passou:** Continua para o próximo middleware

   #### 2️⃣ **Middleware `first_access`** 
   
   **Arquivo:** [app/Http/Middleware/CheckFirstAccess.php](app/Http/Middleware/CheckFirstAccess.php#L1)
   ```php
   public function handle(Request $request, Closure $next): Response
   {
       // VERIFICAÇÃO 1: Se está autenticado
       if (auth()->check() 
           // VERIFICAÇÃO 2: Se é um "atirador" (role)
           && auth()->user()->role === 'atirador' 
           // VERIFICAÇÃO 3: Se não tem email configurado
           && empty(auth()->user()->email)) 
       {
           // Se passou em TODAS as 3 verificações:
           
           if (! $request->routeIs('primeiro-acesso') 
               && ! $request->routeIs('primeiro-acesso.store') 
               && ! $request->routeIs('logout')) {
               // Se está tentando acessar qualquer coisa ALÉM do 
               // formulário de primeiro acesso ou logout:
               
               return redirect()->route('primeiro-acesso');
               // 🚀 Redireciona FORÇADAMENTE para primeira configuração
           }
       }
       return $next($request);
       // Permite continuar o fluxo normal
   }
   ```

   **O que esse middleware faz?**
   - ✅ Verifica se é um "atirador" SEM EMAIL
   - ✅ Se encontrar, redireciona OBRIGATORIAMENTE para configurar email e senha
   - ✅ Impede que acesse qualquer coisa até completar o primeiro acesso

---

### 📍 ETAPA 7️⃣: ROTA DE PRIMEIRO ACESSO

   **Arquivo:** [routes/web.php](routes/web.php#L12)
   ```php
   Route::get('/primeiro-acesso', [PrimeiroAcessoController::class, 'index'])->name('primeiro-acesso');
   //                                                                   ↓
   //                                    Chama método index()
   ```

   **Arquivo:** [app/Http/Controllers/PrimeiroAcessoController.php](app/Http/Controllers/PrimeiroAcessoController.php#L1)
   ```php
   public function index()
   {
       // VERIFICAÇÃO: Se já tem email configurado
       if (!empty(auth()->user()->email)) {
           return redirect()->route('dashboard');
           // ✅ Se sim → já completou, vai para dashboard
       }
       
       // ❌ Se não tem email → mostra formulário
       return view('primeiro_acesso');
   }
   ```

   **Arquivo:** [resources/views/primeiro_acesso.blade.php](resources/views/primeiro_acesso.blade.php#L1)
   ```html
   <form action="{{ route('primeiro-acesso.store') }}" method="POST">
       @csrf
       
       <input type="email" name="email" placeholder="Digite seu e-mail" required>
       <input type="password" name="password" placeholder="Mínimo 8 caracteres" required minlength="8">
       <input type="password" name="password_confirmation" placeholder="Confirme sua senha" required>
       
       <button type="submit">Salvar e Continuar</button>
   </form>
   ```

   **Fluxo:**
   - Formulário pede email e nova senha (confirmada)
   - Usuário preenche e clica "Salvar e Continuar"

---

### 📍 ETAPA 8️⃣: SALVAMENTO DE DADOS DE PRIMEIRO ACESSO

   **Arquivo:** [routes/web.php](routes/web.php#L13)
   ```php
   Route::post('/primeiro-acesso', [PrimeiroAcessoController::class, 'store'])->name('primeiro-acesso.store');
   //                                                                  ↓
   //                                                    Chama método store()
   ```

   **Arquivo:** [app/Http/Controllers/PrimeiroAcessoController.php](app/Http/Controllers/PrimeiroAcessoController.php#L16)
   ```php
   public function store(Request $request)
   {
       // VERIFICAÇÃO 1: Se já está configurado (segurança)
       if (!empty(auth()->user()->email)) {
           return redirect()->route('dashboard');
       }
       
       // PASSO 1: Validação dos dados
       $request->validate([
           'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
           // ✅ Email é obrigatório, válido e único
           'password' => ['required', 'confirmed', Password::defaults()],
           // ✅ Senha é obrigatória, confirmada e com requisitos mínimos
       ]);
       
       // PASSO 2: Pega o usuário autenticado
       $user = auth()->user();
       // 👤 Pega dados do usuário logado
       
       // PASSO 3: Salva email e senha (hasheada)
       $user->email = $request->email;
       $user->password = Hash::make($request->password);
       // 🔐 Hash::make() criptografa a senha (unidirecional)
       
       $user->save();
       // 💾 Salva as mudanças no banco de dados
       
       return redirect()->route('dashboard')->with('success', 'Conta configurada com sucesso. Bem-vindo!');
       // 🚀 Redireciona para o dashboard com mensagem de sucesso
   }
   ```

   **O que acontece aqui?**
   - ✅ Valida email e senha
   - ✅ Criptografa a senha com `Hash::make()`
   - ✅ Salva no banco de dados
   - ✅ Redireciona para dashboard

   **Próxima vez que fizer login:** A senha será comparada usando `Auth::attempt()` (que verifica o hash)

---

### 📍 ETAPA 9️⃣: ACESSO AO DASHBOARD

   **Arquivo:** [routes/web.php](routes/web.php#L16)
   ```php
   Route::get('/dashboard', function () {
       $announcements = \App\Models\Announcement::where('turma', date('Y'))
                                               ->orderBy('priority', 'desc')
                                               ->orderBy('created_at', 'desc')
                                               ->get();
       return view('dashboard', compact('announcements'));
   })->name('dashboard');
   ```

   **Fluxo:**
   - ✅ Middleware `auth` → verifica se está logado (SIM)
   - ✅ Middleware `first_access` → verifica se já configurou email (SIM)
   - ✅ Pega avisos/anúncios do ano atual do banco
   - ✅ Retorna a view do dashboard com os anúncios

---

## 👥 Sistema De Roles E Permissões

O sistema tem 3 **ROLES** (papéis):

### 1. `atirador` (Atirador)
- Primeiro acesso obrigatório
- Acesso ao dashboard e perfil
- Visualiza avisos

### 2. `instructor` (Instrutor)
- Acesso completo de gestão
- Pode criar/editar/deletar atiradores
- Pode criar/editar avisos

### 3. `master` (Master/Admin)
- Acesso completo
- Pode fazer tudo que o instrutor faz

---

## 🚦 Estrutura De Middlewares

### O que é Middleware?
Middleware são **FILTROS** que interceptam requisições ANTES delas chegarem aos controllers. Facilitam a verificação de permissões.

### Middlewares Registrados

**Arquivo:** [bootstrap/app.php](bootstrap/app.php#L1)
```php
->withMiddleware(function (Middleware $middleware): void {
    $middleware->alias([
        'instructor' => \App\Http\Middleware\CheckInstructorRole::class,
        //           ↓  Apelido que usamos nas rotas
        
        'first_access' => \App\Http\Middleware\CheckFirstAccess::class,
    ]);
})
```

**O que significa?**
- `'instructor'` é um atalho para `\App\Http\Middleware\CheckInstructorRole::class`
- `'first_access'` é um atalho para `\App\Http\Middleware\CheckFirstAccess::class`

### Middleware `instructor`

**Arquivo:** [app/Http/Middleware/CheckInstructorRole.php](app/Http/Middleware/CheckInstructorRole.php#L1)
```php
public function handle(Request $request, Closure $next): Response
{
    if (auth()->check() && !in_array(auth()->user()->role, ['master', 'instructor'])) {
        //                   ↓                                ↓
        //            Se está logado         Se NÃO É master E NÃO É instructor
        
        return redirect()->route('dashboard')->with('error', 'Acesso negado. Ação restrita a instrutores.');
        // 🚫 Bloqueia acesso e redireciona
    }
    return $next($request);
    // ✅ Se passou na verificação, continua
}
```

**Como usar nas rotas:**

**Arquivo:** [routes/web.php](routes/web.php#L29)
```php
Route::middleware('instructor')->group(function () {
    // Todas as rotas dentro deste grupo PASSAM pelo middleware 'instructor'
    
    Route::get('/atiradores', [\App\Http\Controllers\AtiradorController::class, 'index'])->name('atiradores.index');
    Route::post('/atiradores', [\App\Http\Controllers\AtiradorController::class, 'store'])->name('atiradores.store');
    Route::put('/atiradores/{user}', [\App\Http\Controllers\AtiradorController::class, 'update'])->name('atiradores.update');
    // ... outras rotas
});
```

---

## 🔄 Fluxo De Requisições Completo

### Diagrama Visual Do Fluxo Completo:

```
┌─────────────────────────────────────────────────────────────────────┐
│ 1️⃣ USUÁRIO ABRE O NAVEGADOR E VAI PARA http://localhost            │
└──────────────────────────────┬──────────────────────────────────────┘
                               │
                               ▼
                    ┌──────────────────────┐
                    │  Router do Laravel   │
                    │  Procura rota "/"    │
                    └──────────┬───────────┘
                               │
                               ▼
              ┌────────────────────────────────────┐
              │ 2️⃣ AuthController::showLogin()    │
              │    return view('login');           │
              └──────────────┬─────────────────────┘
                             │
                             ▼
              ┌────────────────────────────────────┐
              │ 3️⃣ Blade renderiza login.blade.php│
              │    HTML é enviado ao navegador     │
              └──────────────┬─────────────────────┘
                             │
                             ▼
              ┌────────────────────────────────────┐
              │ 4️⃣ USUÁRIO PREENCHE E ENVIA FORM  │
              │    POST /login                     │
              │    { user: 'RA123', password: '**' }
              └──────────────┬─────────────────────┘
                             │
                             ▼
                    ┌──────────────────────┐
                    │  Router do Laravel   │
                    │  Procura rota POST   │
                    └──────────┬───────────┘
                               │
                               ▼
              ┌────────────────────────────────────┐
              │ 5️⃣ AuthController::login()        │
              │    ├─ Valida campos               │
              │    ├─ Busca User por RA/CPF      │
              │    ├─ Verifica senha              │
              │    └─ Cria sessão                │
              └──────────────┬─────────────────────┘
                             │
                    ✅ Credenciais OK? SIM
                             │
                             ▼
              ┌────────────────────────────────────┐
              │ 6️⃣ Redireciona: /dashboard        │
              └──────────────┬─────────────────────┘
                             │
                             ▼
              ┌────────────────────────────────────┐
              │ 7️⃣ MIDDLEWARE 'auth'              │
              │    checked() → SIM ✅             │
              │    Continua...                    │
              └──────────────┬─────────────────────┘
                             │
                             ▼
              ┌────────────────────────────────────┐
              │ 8️⃣ MIDDLEWARE 'first_access'      │
              │    role === 'atirador'?           │
              │                                   │
              │    └─ SIM: email is empty?       │
              │         SIM: REDIRECIONA PARA    │
              │             /primeiro-acesso      │
              │                                   │
              │    └─ SIM: email preenchido?     │
              │         ✅ CONTINUA...            │
              └──────────────┬─────────────────────┘
                             │
                        🔄 FLUXO 1
                        (Email vazio)
                             │
        ┌────────────────────┴─────────────────────┐
        │                                          │
        ▼                                          ▼
┌─────────────────────────────┐    ┌──────────────────────────────┐
│ 9️⃣ /primeiro-acesso         │    │ 9️⃣ /dashboard (normal)      │
│ PrimeiroAcessoCtrl::index()│    │ Renderiza dashboard         │
│ return view('primeiro_acesso')
└───────────┬─────────────────┘    └──────────────────────────────┘
            │
            ▼
    ┌──────────────────────┐
    │ 🔟 FORMULÁRIO        │
    │ - Email: [____]      │
    │ - Senha: [____]      │
    │ - Confirmar: [____]  │
    │ [Salvar e Continuar] │
    └───────────┬──────────┘
                │
                ▼
        ┌──────────────────────┐
        │ 1️⃣1️⃣ POST /primeiro-acesso
        │ PrimeiroAcessoCtrl::store()
        └───────────┬──────────┘
                    │
                    ▼
        ┌──────────────────────┐
        │ 1️⃣2️⃣ VALIDA EMAIL    │
        │    ├─ Email é único? │
        │    ├─ CSV é válido?  │
        │    └─ Senha ok?      │
        └───────────┬──────────┘
                    │
                    ▼
        ┌──────────────────────┐
        │ 1️⃣3️⃣ SALVA NO BD      │
        │ $user->email = ...  │
        │ $user->password =   │
        │   Hash::make(...)   │
        │ $user->save()       │
        └───────────┬──────────┘
                    │
                    ▼
        ┌──────────────────────┐
        │ 1️⃣4️⃣ Redireciona     │
        │ /dashboard           │
        │ Primeira acesso OK! ✅
        └──────────────────────┘
```

---

## 🗄️ Camadas Do Sistema Explicadas

### 1. **ROUTES** (`routes/web.php`)
```php
Route::get('/perfil', [ProfileController::class, 'index'])->name('profile');
//      ↓                          ↓                        ↓
//   Método HTTP         Aponta para Controller      Nome da rota
```
- Define o "caminho" das requisições
- Aponta para qual controller/método executar
- Aplica middlewares

### 2. **CONTROLLERS** (`app/Http/Controllers/`)
```php
public function index()
{
    return view('profile');  // ← Retorna uma view
}

public function update(Request $request)
{
    $user = Auth::user();    // ← Pega dados autenticados
    $user->save();           // ← Salva mudanças no BD
}
```
- Recebem dados das requisições
- Fazem lógica de negócios
- Chamam Models para banco de dados
- Retornam Views ou Redirects

### 3. **MODELS** (`app/Models/`)
```php
class User extends Authenticatable
{
    protected $fillable = ['email', 'password', 'role', ...];
    // ↑ Define quais campos podem ser preenchidos
}
```
- Representam tabelas do banco de dados
- Facilitam queries e operações no BD

### 4. **VIEWS** (`resources/views/`)
```html
@if(auth()->check())
    <p>Bem-vindo {{ auth()->user()->name }}!</p>
@endif
<!-- ↑ Blade: Template do Laravel com PHP embutido -->
```
- Templates HTML renderizados
- Podem ter variáveis PHP dinâmicas
- Suportam loops, condições, etc.

### 5. **MIDDLEWARE** (`app/Http/Middleware/`)
```php
if (!auth()->check()) {
    return redirect('login');  // ← Bloqueia e redireciona
}
return $next($request);        // ← Permite continuar
```
- Intercepta e filtra requisições
- Verifica permissões
- Redireciona se necessário

### 6. **DATABASE** (`database/migrations/`)
```php
Schema::create('users', function (Blueprint $table) {
    $table->id();              // Cria coluna 'id' auto-incremento
    $table->string('email');   // Cria coluna 'email'
    $table->string('password');
    $table->string('role');
    $table->timestamps();      // Cria 'created_at' e 'updated_at'
});
```
- Define estrutura e schema do banco
- Versionam o banco de dados

### 7. **BOOTSTRAP** (`bootstrap/app.php`)
```php
return Application::configure(...)
    ->withRouting(web: __DIR__.'/../routes/web.php', ...)
    // ↓ Define arquivos principais
    ->withMiddleware(...)
    // ↓ Registra middlewares
    ->create();
```
- Inicializa a aplicação
- Registra configurações globais
- Define middlewares e rotas

---

## 📱 Exemplo Real: Autenticação Completa De Um "Atirador"

### Paso a Paso:

**1. Usuário novo é criado (sem email)**
```php
// No banco de dados, um novo usuário é criado com:
- id: 1
- name: "João Silva"
- ra: "202301001"
- cpf: "123.456.789-00"
- email: NULL  ← Vazio!
- password: "hashed_password_initial"
- role: "atirador"
```

**2. Usuário acessa a aplicação**
```
Clica em: http://localhost
↓
GET /
↓
AuthController::showLogin()
↓
Vê página de login
```

**3. Faz login com RA/CPF e senha inicial**
```
Preenche:
- Campo "RA / CPF": 202301001
- Campo "Senha": senha_inicial

Clica: "Entrar"
↓
POST /login
↓
AuthController::login()
  ├─ Busca User onde ra = '202301001'
  ├─ Encontra! (email é NULL)
  ├─ Verifica senha → está correta ✅
  ├─ Cria sessão
  └─ Redireciona para /dashboard
```

**4. Middleware `auth` verifica**
```
auth()->check() → SIM ✅ (está logado)
↓
Continua para próximo middleware
```

**5. Middleware `first_access` verifica**
```
auth()->check() → SIM ✅
auth()->user()->role === 'atirador' → SIM ✅
empty(auth()->user()->email) → SIM ✅ (email é NULL)

CONDIÇÃO: Todos true → REDIRECIONA FORÇADAMENTE
↓
return redirect()->route('primeiro-acesso');
```

**6. Usuário vê formulário de primeiro acesso**
```
GET /primeiro-acesso
↓
PrimeiroAcessoController::index()
↓
Vê formulário pedindo:
- Email: [_______________]
- Nova Senha: [_______________]
- Confirmar Senha: [_______________]
- [Salvar e Continuar]
```

**7. Usuário preenche formulário**
```
- Email: joao@email.com
- Nova Senha: nova_senha_segura_123
- Confirmar: nova_senha_segura_123

Clica: "Salvar e Continuar"
↓
POST /primeiro-acesso
↓
PrimeiroAcessoController::store()
  ├─ Valida email (único, válido, etc)
  ├─ Valida senha (confirmada, requisitos mínimos)
  ├─ Pega $user = auth()->user() (João)
  ├─ $user->email = "joao@email.com"
  ├─ $user->password = Hash::make("nova_senha_segura_123")
  ├─ $user->save() ← Salva no banco
  └─ Redireciona para /dashboard com mensagem de sucesso
```

**8. Middleware `first_access` verifica novamente**
```
Auth()->check() → SIM ✅
auth()->user()->role === 'atirador' → SIM ✅
empty(auth()->user()->email) → NÃO ❌ (agora tem email!)

CONDIÇÃO: Falhou (email não é mais vazio)
↓
return $next($request);  ← Permite continuar
```

**9. Acessa dashboard**
```
✅ Dashboard renderizado
✅ Vê avisos/anúncios
✅ Pode navegar normalmente
```

**10. Próxima vez que faz login**
```
POST /login
  ├─ Busca User onde ra = '202301001'
  ├─ Encontra seu novo email: joao@email.com
  ├─ TENTA: Auth::attempt(['ra' => 'RA', 'password' => 'senha'])
  ├─ Verifica hash: 'senha_digitada' vs 'nova_senha_segura_123' (hasheada)
  ├─ Se correto → ✅ Loga
  ├─ Se errado → ❌ Erro "Credenciais não coincidem"
  └─ Middleware first_access NÃO redireciona (email já configurado)
```

---

## 🎯 Fluxo De Usuário Instrutor

### Diferenças Para Um Instrutor:

**1. Primeiro acesso diferente?**
```
❌ NÃO
Middleware first_access apenas redireciona se:
  ├─ role === 'atirador'   ← Instrutor? NÃO
  └─ email é vazio

Instrutores já têm email configurado → Vão direto para dashboard
```

**2. Acesso a rotas especiais**
```php
Route::middleware('instructor')->group(function () {
    Route::get('/atiradores', [\App\Http\Controllers\AtiradorController::class, 'index']);
    // ↓ Instr...
```

**Fluxo:**
```
GET /atiradores
↓
Middleware 'instructor':
  ├─ auth()->check() → SIM ✅
  ├─ in_array(auth()->user()->role, ['master', 'instructor'])
  │  ├─ role = 'instructor' → SIM ✅
  │  └─ role = 'master' → SIM ✅
  │  └─ role = 'atirador' → NÃO ❌
  │
  └─ Se passou → Continua
    └─ Se falhou → Redireciona com erro
```

**3. Acesso negado para Atirador**
```
Atirador tenta acessar: GET /atiradores
↓
Middleware 'instructor':
  ├─ auth()->check() → SIM ✅ (está logado)
  ├─ in_array(auth()->user()->role, ['master', 'instructor'])
  │  └─ role = 'atirador' → NÃO ❌
  │
  └─ Falhou! → Redireciona para dashboard com:
     "Acesso negado. Ação restrita a instrutores."
```

---

## 🔗 Relacionamentos Na View

**Arquivo:** [resources/views/layouts/app.blade.php](resources/views/layouts/app.blade.php#L35)
```html
<nav class="sidebar-nav">
    <a href="{{ route('dashboard') }}" class="nav-item">
        <i class="fa-solid fa-house"></i>
        <span>Início</span>
    </a>
    
    @if(in_array(auth()->user()->role, ['master', 'instructor']))
    <!--                           ↓↓↓ Verifica role dinamicamente -->
    <a href="{{ route('atiradores.index') }}" class="nav-item">
        <i class="fa-solid fa-person-military-rifle"></i>
        <span>Atiradores</span>  ← Só aparece para instrutores
    </a>
    @endif
</nav>
```

**O que isso faz?**
- ✅ Se é instrutor → Mostra link "Atiradores"
- ❌ Se é atirador → Escon de o link

---

## 💾 Banco De Dados - Tabela Users

```sql
CREATE TABLE users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(255),
    email VARCHAR(255) UNIQUE,
    password VARCHAR(255),
    ra VARCHAR(255) UNIQUE,
    cpf VARCHAR(255) UNIQUE,
    role ENUM('atirador', 'instructor', 'master'),
    points INT DEFAULT 0,
    faults INT DEFAULT 0,
    photo VARCHAR(255) NULL,
    numero INT,
    turma VARCHAR(255),
    is_cfc BOOLEAN DEFAULT FALSE,
    telefone VARCHAR(255),
    created_at TIMESTAMP,
    updated_at TIMESTAMP
);
```

**Campos importantes:**
- `email` → Vazio para "atirador" novo, preenchido después no primeiro acesso
- `password` → Hash da senha (nunca salva em texto plano)
- `role` → Define permissões (atirador, instrutor, master)
- `is_cfc` → Boolean para controle de CFC

---

## 🔐 Segurança Do Sistema

### 1. **CSRF Protection**
```html
<form action="{{ route('login') }}" method="POST">
    @csrf  ← Token CSRF que não pode ser falsificado
</form>
```

### 2. **Password Hashing**
```php
$user->password = Hash::make($request->password);
// Hash::make() → Criptografa a senha (SHA-256 com salt)
// A senha NUNCA é salva em texto plano
```

### 3. **Session Regeneration**
```php
$request->session()->regenerate();
// Cria nova sessão após login (previne session fixation)
```

### 4. **Middleware Auth**
```php
// Todas as rotas precisam estar autenticadas
Route::middleware('auth')->group(...);
```

### 5. **Role-Based Access Control (RBAC)**
```php
// Middleware verifica o 'role' do usuário
if (!in_array(auth()->user()->role, ['master', 'instructor'])) {
    // Bloqueia acesso
}
```

---

## 📊 Resumo Visual Da Estrutura

```yaml
Laravel Application
├── bootstrap/app.php (INICIALIZAÇÃO)
│   └── Registra middlewares e rotas
│
├── routes/web.php (ROTEAMENTO)
│   └── Define endpoints e middlewares
│
├── app/Http/
│   ├── Controllers/ (LÓGICA)
│   │   ├── AuthController.php
│   │   ├── PrimeiroAcessoController.php
│   │   ├── ProfileController.php
│   │   └── AtiradorController.php
│   │
│   └── Middleware/ (FILTROS)
│       ├── CheckFirstAccess.php
│       └── CheckInstructorRole.php
│
├── app/Models/ (DADOS)
│   └── User.php
│
├── resources/views/ (APRESENTAÇÃO)
│   ├── login.blade.php
│   ├── primeiro_acesso.blade.php
│   ├── layouts/app.blade.php
│   └── ...
│
└── database/
    ├── migrations/ (SCHEMA)
    │   └── *_create_users_table.php
    │
    └── seeders/ (DADOS INICIAIS)
        └── UserSeeder.php
```

---

## 🚀 Conclusão

**O sistema funciona assim:**

1. **ROTA** recebe a requisição → aponta para **CONTROLLER**
2. **CONTROLLER** recebe dados → chama **MIDDLEWARE** (se necessário)
3. **MIDDLEWARE** verifica permissões → libera ou bloqueia
4. **CONTROLLER** faz lógica → usa **MODEL** para acessar banco
5. **MODEL** acessa banco → retorna dados
6. **CONTROLLER** retorna **VIEW** → browser renderiza HTML

**Use este documento como referência para entender o fluxo completo!**

---

**Criado em:** 9 de Abril de 2026
**Sistema:** Tiro de Guerra (TG) - Administração e Escalas
