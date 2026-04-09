# 🗺️ MAPA MENTAL VISUAL DO SISTEMA

Este arquivo contém **diagramas visuais ASCII** para referência rápida.

---

## 🔄 DIAGRAMA 1: Fluxo Completo De Login a Dashboard

```
┌────────────────────────────────────────────────────────────────┐
│                         NAVEGADOR                              │
│  Usuário digitou: localhost/                                   │
└────────────────────────┬─────────────────────────────────────┘
                         │
                         ▼
        ┌────────────────────────────────────┐
        │  ROUTER encontra GET /             │
        │  ↓ aponta para AuthController      │
        └────────────────┬───────────────────┘
                         │
                         ▼
        ┌────────────────────────────────────┐
        │  showLogin()                       │
        │  return view('login')              │
        └────────────────┬───────────────────┘
                         │
                         ▼
        ┌────────────────────────────────────┐
        │  HTML Renderizado:                 │
        │  ┌──────────────────────────────┐  │
        │  │ Campo RA/CPF: [_______]      │  │
        │  │ Campo Senha:  [_______]      │  │
        │  │ Botão: [ENTRAR]              │  │
        │  └──────────────────────────────┘  │
        └────────────────┬───────────────────┘
                         │
                User preenche e clica
                         │
                         ▼
        ┌────────────────────────────────────┐
        │  POST /login                       │
        │  { user: '201301', password: '**' }│
        └────────────────┬───────────────────┘
                         │
                         ▼
        ┌────────────────────────────────────┐
        │  AuthController::login()           │
        │  1. Valida                         │
        │  2. Busca User no banco            │
        │  3. Verifica Hash                  │
        │  4. Cria Sessão                    │
        │  5. redirect('dashboard')          │
        └────────────────┬───────────────────┘
                         │
                         ▼
        ┌────────────────────────────────────┐
        │  GET /dashboard                    │
        │  [Middleware Stack]                │
        │  1️⃣ 'auth' → Pass ✅              │
        │  2️⃣ 'first_access' →              │
        │     └─ Email vazio? ──┐            │
        │        └─ redirect    │            │
        │           /primeiro   │            │
        │-----OR─────────────┐  │            │
        │        └─ Pass ✅  │  │            │
        └────────────────┬──┴──┘─────────────┘
                    NÃO  YES
                    │    │
           ┌────────▼──┐ │
           │PRIMEIRO   │ │
           │ACESSO ⚙️   │ │
           │FORM. →    │ │
           │POST → SAVE│ │
           │→ REDIRECT │ │
           └────┬──────┘ │
                └────────┼────────────┐
                         │            │
                         ▼            ▼
        ┌────────────────────────────────────┐
        │  Dashboard Renderizado             │
        │  ┌──────────────────────────────┐  │
        │  │ [Sidebar]                    │  │
        │  │ - Início ✓                   │  │
        │  │ - Atiradores (instr only)    │  │
        │  │ - Perfil                     │  │
        │  │ - Escalas                    │  │
        │  └──────────────────────────────┘  │
        │  ┌──────────────────────────────┐  │
        │  │ [Conteúdo]                   │  │
        │  │ 📋 AVISOS (ano 2026)         │  │
        │  │ - Aviso 1                    │  │
        │  │ - Aviso 2                    │  │
        │  │ - Aviso 3                    │  │
        │  └──────────────────────────────┘  │
        │                                    │
        │  ✅ USUÁRIO VÊ DASHBOARD           │
        └────────────────────────────────────┘
```

---

## 👥 DIAGRAMA 2: Modelo de Roles e Permissões

```
                      ┌─────────────────────────┐
                      │   SISTEMA DE ROLES      │
                      └─────────────────────────┘
                               │
                ┌──────────────┼──────────────┐
                │              │              │
                ▼              ▼              ▼
           ┌─────────┐  ┌──────────────┐  ┌────────┐
           │ATIRADOR │  │ INSTRUCTOR   │  │ MASTER │
           └─────────┘  └──────────────┘  └────────┘
                │              │              │
      ┌─────────┴──────┐      │         ┌────┴─────┐
      │ Menos Acesso   │      │         │Total      │
      ├─ Dashboard     │      │         │Acesso     │
      ├─ Perfil        │      │         │(tudo)     │
      ├─ Avisos (R)    │      │         └────────────┘
      ├─ Escalas (D)   │      │
      ├─ 1º Acesso ⚙️  │      │
      └────────────────┘      ▼
                         ┌──────────────────┐
                         │ Acesso MÉDIO     │
                         ├──────────────────┤
                         ├─ Tudo que atirador
                         ├─ Gerenciar Atiradores
                         ├─ CRUD Avisos
                         ├─ Escalas
                         └──────────────────┘


        ┌─────────────────────────────────────┐
        │   MIDDLEWARE PROTECTION             │
        ├─────────────────────────────────────┤
        │ Route::middleware('instructor')     │
        │ ↓ Verifica: role in ['instructor',  │
        │             'master']?              │
        │ ✅ SIM → Continua                  │
        │ ❌ NÃO → Redireciona com erro      │
        └─────────────────────────────────────┘
```

---

## 🚫 DIAGRAMA 3: Bloqueios e Redirects

```
┌────────────────────────────────────────────────────────────┐
│                    PROTEÇÃO DE ROTAS                       │
└────────────────────────────────────────────────────────────┘

┌─ Tentativa de Acesso ────────────────────────────────────┐
│                                                           │
│  GET /dashboard (sem estar logado)                       │
│         │                                                 │
│         ▼                                                 │
│  ┌─────────────────────────────┐                         │
│  │ Middleware 'auth'           │                         │
│  │ auth()->check()? NÃO ❌      │                         │
│  │ ↓                           │                         │
│  │ redirect('login')           │                         │
│  └─────────────────────────────┘                         │
│         │                                                 │
│         ▼ (BLOQUEADO)                                    │
│  ┌─────────────────────────────┐                         │
│  │ Página de Login             │                         │
│  │ (Não chega nem ao           │                         │
│  │  primeiro_acesso ou outro) │                         │
│  └─────────────────────────────┘                         │
│                                                           │
└───────────────────────────────────────────────────────────┘

┌─ CASO 2: Atirador sem email tenta ir para /perfil ────────┐
│                                                            │
│  GET /perfil (atirador com email vazio)                  │
│         │                                                  │
│         ▼                                                  │
│  ┌────────────────────────────────┐                      │
│  │ Middleware 'auth'              │                      │
│  │ auth()->check()? SIM ✅         │                      │
│  │ → \$next($request)             │                      │
│  └────────────────┬───────────────┘                      │
│                   │                                       │
│                   ▼                                       │
│  ┌────────────────────────────────┐                      │
│  │ Middleware 'first_access'      │                      │
│  │ role === 'atirador'? SIM ✅    │                      │
│  │ email vazio? SIM ✅             │                      │
│  │ routeIs('perfil')? SIM         │                      │
│  │ (NÃO é exceto)                 │                      │
│  │ ↓                              │                      │
│  │ redirect('/primeiro-acesso')   │                      │
│  └────────────────────────────────┘                      │
│                   │                                       │
│                   ▼ (BLOQUEADO)                          │
│  ┌────────────────────────────────┐                      │
│  │ Formulário de Primeiro Acesso  │                      │
│  │ (Email + Senha)                │                      │
│  └────────────────────────────────┘                      │
│                                                            │
└────────────────────────────────────────────────────────────┘

┌─ CASO 3: Atirador tenta acessar /atiradores (instructor) ┐
│                                                           │
│  GET /atiradores (role = 'atirador')                    │
│         │                                                 │
│         ▼                                                 │
│  ┌─────────────────────────────┐                         │
│  │ Middleware 'auth' → pass ✅ │                         │
│  │ Middleware 'first_access'   │                         │
│  │ → pass ✅                   │                         │
│  └────────────────┬────────────┘                         │
│                   │                                       │
│                   ▼                                       │
│  ┌─────────────────────────────────────┐                │
│  │ Middleware 'instructor'             │                │
│  │ role in                             │                │
│  │ ['master','instructor']? NÃO ❌     │                │
│  │ ↓                                   │                │
│  │ redirect('dashboard')               │                │
│  │ with('error',                       │                │
│  │  'Acesso negado...')               │                │
│  └─────────────────────────────────────┘                │
│                   │                                       │
│                   ▼ (BLOQUEADO)                          │
│  ┌──────────────────────────────┐                        │
│  │ Dashboard + Erro             │                        │
│  │ "Acesso negado. Restrito     │                        │
│  │  a instrutores."             │                        │
│  └──────────────────────────────┘                        │
│                                                           │
└───────────────────────────────────────────────────────────┘
```

---

## 🔐 DIAGRAMA 4: Fluxo De Hash de Senha

```
┌────────────────────────────────────────────────┐
│         CRIPTOGRAFIA DE SENHA                  │
└────────────────────────────────────────────────┘

CONFIGURAÇÃO INICIAL (Primeiro Acesso):

  User digita: "nova_senha_segura_123"
              │
              ▼
  Hash::make($password)
  ├─ Usa algoritmo: Bcrypt
  ├─ Adiciona: Salt (random string)
  ├─ Itera: 2^12 vezes (muito processamento)
  └─ Resultado: "$2y$12$9qk.Y3PzJV1eWVQMFfVe..."
              │ (78 caracteres)
              ▼
  Salva no banco:
  UPDATE users SET password = '$2y$12$...' WHERE id=1

  ✅ A senha NUNCA é salva em texto plano!
  ✅ Nem os desenvolvedores conseguem ver a senha!


VERIFICAÇÃO (Login posterior):

  User digita: "nova_senha_segura_123"
              │
              ▼
  Hash::check(
    $password_digitada,
    $hash_no_banco
  )
  ├─ Pega a senha digitada
  ├─ Aplica o MESMO Bcrypt
  ├─ Compara com hash no banco
  └─ Resultado: true ou false
          │
    ┌─────┴─────┐
    │           │
    true        false
    │           │
    ▼           ▼
  LOGIN OK   ERRO
  ✅         Volta login
            com erro ❌
```

---

## 📊 DIAGRAMA 5: Sequência de Chamadas

```
HTTP Request chegado no servidor
          │
          ▼
   public/index.php
          │
          ▼
   bootstrap/app.php
          │
          ▼
   routes/web.php (Router)
          │
  ┌───────┴────────┐
  │                │
  Rota             Rota não
  encontrada?      encontrada?
  SIM              NÃO
  │                │
  ▼                ▼
Aponta para    404 Error
Handler        (Not Found)

  ├─ Closure
  │  function() { ... return ... }
  │
  ├─ Controller Método
  │  [ControllerClass::class, 'method']
  │
  └─ Rota Resource
     [ControllerClass::class] (CRUD)

          │
          ▼
Middlewares Aplicados
└─ Em série, um por um
   ├─ Middleware 1
   │  └─ if condition
   │     └─ reject → Response
   │     └─ accept → $next()
   │
   ├─ Middleware 2 ... etc
   │
   └─ Se chegou aqui
      → Handler executado

          │
          ▼
    Handler/Controller
    ├─ Recebe: $request
    ├─ Faz: lógica de negócios
    ├─ Acessa: Models (BD)
    └─ Retorna: Response
       ├─ view(...) → HTML
       ├─ redirect(...) → 302
       ├─ response()->json() → JSON
       └─ back()->with() → redirect

          │
          ▼
    HTTP Response
    ├─ Status: 200, 301, 404, etc
    ├─ Headers: Content-Type, etc
    ├─ Body: HTML, JSON, etc
    └─ Cookies: session, CSRF

          │
          ▼
    Navegador
    ├─ Parse HTML
    ├─ Load CSS/JS
    ├─ Render visualmente
    └─ User vê a página ✅
```

---

## 💾 DIAGRAMA 6: Onde Os Dados São Armazenados

```
┌────────────────────────────────────────────────────┐
│           ARMAZENAMENTO DE DADOS                   │
└────────────────────────────────────────────────────┘

1️⃣ SERVIDOR - SESSION
┌────────────────┐
│ storage/       │
│ framework/     │
│ sessions/      │
│ abc123def...   │ ← Um arquivo por sessão
└────────────────┘
Conteúdo (serializado):
{
  'login_web_59ba36' => 1,  ← ID user logado
  '_token' => 'csrf token',
  'success' => 'mensag.',
  ...
}
Acessível: auth()->user(), session('key')
Duração: Configurable (default 2h)

2️⃣ CLIENTE - COOKIES
┌────────────────────────────┐
│ XSRF-TOKEN=xyz789...       │  ← Token CSRF
│ laravel_session=abc123...  │  ← ID da sessão
│ (armazenado no navegador)  │
└────────────────────────────┘
Enviado: Cada requisição (no header)
Duração: Até browser fechar ou expirar

3️⃣ BANCO DE DADOS - DADOS PERMANENTES
┌────────────────────────────────────────┐
│ MySQL Database                         │
│ Tabela: users                          │
│  id: 1                                 │
│  name: João Silva                      │
│  email: joao@email.com                 │
│  password: $2y$12$... (hasheada)      │
│  role: atirador                        │
│  ...                                   │
│                                        │
│ Tabela: announcements                  │
│  id: 1                                 │
│  title: Aviso importante               │
│  ...                                   │
└────────────────────────────────────────┘
Acessível: Models, Database queries
Duração: Até deletado ou modificado

4️⃣ SERVIDOR - STORAGE (Arquivos)
┌────────────────────────────────┐
│ storage/                       │
│ ├─ app/                        │
│ │  ├─ public/                  │
│ │  │  ├─ user_1_photo.jpg      │ ← Foto
│ │  │  ├─ user_2_photo.jpg      │   de perfil
│ │  │  └─ ...                   │
│ │  └─ private/                 │
│ └─ logs/                       │
│    └─ laravel.log              │
└────────────────────────────────┘
Acessível: asset(), Storage facades
Durção: Enquanto arquivo existir
```

---

## 🔄 DIAGRAMA 7: MVC - Model, View, Controller

```
         HTTP Request
              │
              ▼
         ┌─────────┐
         │ ROUTER  │ ← routes/web.php
         └────┬────┘
              │ Encontra rota
              ▼
    ┌────────────────────┐
    │   CONTROLLER       │ ← app/Http/Controllers/
    │                    │
    │ AuthController {   │
    │   login() {        │
    │     $credentials = │
    │     $request->     │
    │     validate();    │
    │                    │
    │     $user = User:: │  ← Chama Model
    │     where(...)     │
    │     ->first();     │
    │                    │
    │     if ($user) {   │
    │       Auth::       │
    │       attempt();   │
    │                    │
    │       return       │
    │       view(...);  │  ← Chama View
    │     }              │
    │   }                │
    │ }                  │
    └────────┬───────────┘
             │
    ┌────────┴──────────────────┬──────────────┐
    │                           │              │
    ▼                           ▼              ▼
 ┌──────────┐           ┌────────────────┐  ┌────────┐
 │  MODEL   │           │     VIEW       │  │ BANCO  │
 │          │           │                │  │        │
 │ User.php │           │ dashboard.     │  │ Select │
 │ ├─ find()│           │ blade.php      │  │ Insert │
 │ ├─ where│           │ ├─ @foreach    │  │ Update │
 │ ├─ save()│           │ ├─ {{ $var }}  │  │ Delete │
 │ └─ ...   │           │ ├─ @if/@else   │  │        │
 │          │           │ └─ HTML        │  │ Dados  │
 └──────────┘           └────────────────┘  │ Perm.  │
    │                           │            │        │
    └───────────► Fetch ◄───────┘            └────────┘
                                             
                     │
                     ▼
            ┌─────────────────┐
            │  HTML Response  │
            │  (Renderizado)  │
            └────────┬────────┘
                     │
                     ▼
              ┌──────────────┐
              │   NAVEGADOR  │
              │ (Usuário)    │
              └──────────────┘
```

---

## 📋 DIAGRAMA 8: Estados De Um Usuário

```
┌──────────────────────────────────────────────────────┐
│             CICLO DE VIDA DE UM USUÁRIO              │
└──────────────────────────────────────────────────────┘

1️⃣ NOVO (Criado por Instrutor)
   Database:
   - email: NULL ← VAZIO!
   - password: hash_inicial
   - role: atirador
   - Nome: João Silva
   - RA: 202301001
   ↓ Status: Nunca fez login

2️⃣ PRIMEIRO LOGIN (Faz login com RA + senha)
   ├─ Digita: RA 202301001 + senha_inicial
   ├─ Autentica OK ✅
   ├─ Middleware first_access: email vazio? SIM ✅
   └─ REDIRECIONA para /primeiro-acesso
   ↓ Status: Vendo formulário

3️⃣ CONFIGURANDO (Preenchendo primeiro acesso)
   ├─ Digita: email + nova_senha
   ├─ Valida tudo
   ├─ Hash::make(nova_senha)
   ├─ UPDATE banco:
   │  - email: joao@email.com
   │  - password: $2y$12$... (nova)
   └─ Redireciona para dashboard
   ↓ Status: Primeiro acesso completado

4️⃣ ATIVO (Usa o system normalmente)
   ├─ Database:
   │  - email: joao@email.com
   │  - password: $2y$12$... (nova)
   │  - role: atirador
   ├─ Login: pode usar email ou RA
   ├─ Acesso: Dashboard, Perfil, Avisos
   └─ Restrições: Não pode gerenciar atiradores

5️⃣ INSTRUTOR (Se role for mudado para 'instructor')
   ├─ Database:
   │  - role: instructor ← MUDOU!
   ├─ Middleware 'instructor' agora PERMITE:
   │  └─ GET /atiradores
   │  └─ POST /atiradores
   │  └─ Gerenciar avisos
   └─ Acesso: Tudo que atirador + gestão

6️⃣ INATIVO/DELETADO
   ├─ DELETE FROM users WHERE id=X
   └─ Não pode mais fazer login
```

---

## 🎯 DIAGRAMA 9: Decisões De Rota

```
GET request arrive
       │
       ▼
    ROUTER
  ├─ / ┐
  │    ├─ GET             ├─ AuthController::showLogin() ✅
  │    └─ POST (login)    └─ AuthController::login() ✅
  │
  ├─ /login (similar to /)
  │
  ├─ /logout
  │    └─ POST            └─ AuthController::logout() → Form destruction
  │
  ├─ /dashboard
  │    └─ GET
  │         └─ [auth] ✓
  │         └─ [first_access] ⚙️
  │              ├─ Email vazio? SIM → /primeiro-acesso
  │              └─ Email vazio? NÃO → Continue
  │                    └─ [HANDLER] Dashboard
  │
  ├─ /primeiro-acesso
  │    ├─ GET            ← Show form
  │    └─ POST           ← Process and save
  │
  ├─ /perfil
  │    ├─ GET  → ProfileController::index()
  │    └─ PUT  → ProfileController::update()
  │
  ├─ /atiradores
  │    ├─ GET  (list)    ├─ [instructor] ✅
  │    ├─ POST (create)  └─ AtiradorController::index()
  │    ├─ PUT  (update)
  │    ├─ PATCH (toggle)
  │    └─ DELETE
  │
  └─ /avisos
       ├─ GET (list)     ├─ [instructor]
       ├─ POST (create)  └─ AnnouncementController
       ├─ GET (edit)         CRUD
       ├─ PUT (update)     (except show)
       ├─ DELETE
       └─ GET (show)      ← [auth] only (visible to ALL)
```

---

## 🧠 DIAGRAMA 10: Checkpoints (Verdade/Falso)

```
┌─────────────────────────────────────────────────────┐
│     VERIFICAÇÕES QUE O SISTEMA FAZ                 │
└─────────────────────────────────────────────────────┘

Autenticação:
├─ auth()->check()
│  ├─ VERDADE → Usuário está logado ✅
│  └─ FALSO   → Usuário NÃO está logado ❌

Role (Papel):
├─ auth()->user()->role === 'atirador'
│  ├─ VERDADE → É um atirador
│  └─ FALSO   → É instrutor ou master
│
├─ in_array(auth()->user()->role, ['master', 'instructor'])
│  ├─ VERDADE → É instrutor ou master
│  └─ FALSO   → É um atirador

Email (Primeiro Acesso):
├─ empty(auth()->user()->email)
│  ├─ VERDADE → Email é vazio (NULL)
│  └─ FALSO   → Email preenchido

Combinações Importantes:
│
├─ auth()->check() + empty(email) + role=='atirador'?
│  ├─ VERDADE → Redireciona para /primeiro-acesso
│  └─ FALSO   → Continua normalmente
│
├─ auth()->check() + !in_array(role, [...])
│  ├─ VERDADE → Redireciona com erro (acesso negado)
│  └─ FALSO   → Continua normalmente
│
└─ Hash::check($password, $hash)
   ├─ VERDADE → Senha está correta ✅
   └─ FALSO   → Senha está errada ❌
```

---

## 📖 Como Usar Este Arquivo

1. **Entenda a estrutura visual** dos diagramas
2. **Compare com sua compreensão** do código
3. **Use como referência rápida** durante desenvolvimento
4. **Ensine a outros** usando esses diagramas

Exemplo:
- Você quer entender o fluxo? → Acesse **DIAGRAMA 1**
- Você quer entender roles? → Acesse **DIAGRAMA 2**
- Alguém quer entender middlewares? → Acesse **DIAGRAMA 3**

---

**Criado em:** 9 de Abril de 2026
**Para:** Tiro de Guerra - Sistema de Administração e Escalas

