# 📝 COMENTÁRIOS NUMERADOS NOS ARQUIVOS DE CÓDIGO

Este documento contém os mesmos arquivos do projeto, MAS COM COMENTÁRIOS NUMERADOS mostrando a sequência de execução.

---

## 📄 1. routes/web.php (COM COMENTÁRIOS NUMERADOS)

```php
<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PhotoController;

// ==========================================
// 🔑 ROTA DE AUTENTICAÇÃO (SEM PROTEÇÃO)
// ==========================================

// ✅ 1️⃣ Primeira coisa: usuário vai para a raiz "/"
//    Retorna a página de login do aplicativo
Route::get('/', [AuthController::class, 'showLogin'])->name('login');
//                 ↓ 2️⃣ Chama AuthController::showLogin()

// ✅ 3️⃣ Usuário preenche o formulário login.blade.php
//    e clica em "Entrar" → POST /login
Route::post('/login', [AuthController::class, 'login']);
//                      ↓ 4️⃣ Chama AuthController::login()
//                      ├─ Valida campos
//                      ├─ Busca usuário por RA/CPF
//                      ├─ Verifica senha
//                      └─ Se OK → cria sessão e redireciona

// ✅ 5️⃣ Usuário faz logout
//    Destroi sessão e volta para login
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
//                       ↓ 6️⃣ Chama AuthController::logout()


// ==========================================
// 🛡️ ROTAS COM PROTEÇÃO (AUTENTICADAS)
// ==========================================

//  7️⃣ MIDDLEWARE 'auth' → Verifica se está logado
//        Se NÃO está → Redireciona para login
//     └─ Se SIM → continua para próximo middleware
//
//  8️⃣ MIDDLEWARE 'first_access' → Verifica se é atirador SEM email
//        Se SIM (atirador sem email) → Redireciona para /primeiro-acesso
//     └─ Se NÃO (tem email) → continua normal

Route::middleware(['auth', 'first_access'])->group(function () {
    
    // ========== PRIMEIRO ACESSO (Apenas para Atiradores) ==========
    
    // ✅ 9️⃣ Se é atirador SEM email → chega aqui
    //    GET /primeiro-acesso → Renderiza formulário
    Route::get('/primeiro-acesso', [\App\Http\Controllers\PrimeiroAcessoController::class, 'index'])
        ->name('primeiro-acesso');
    //   ↓ 🔟 Chama PrimeiroAcessoController::index()
    //      ├─ Verifica se já tem email
    //      ├─ Se NÃO → mostra formulário
    //      └─ Se SIM → redireciona para dashboard
    
    // ✅ 1️⃣1️⃣ Usuário preenche dados (email, senha) e envia
    //    POST /primeiro-acesso
    Route::post('/primeiro-acesso', [\App\Http\Controllers\PrimeiroAcessoController::class, 'store'])
        ->name('primeiro-acesso.store');
    //   ↓ 1️⃣2️⃣ Chama PrimeiroAcessoController::store()
    //      ├─ Valida email e senha
    //      ├─ Criptografa senha com Hash::make()
    //      ├─ Salva no banco de dados
    //      └─ Redireciona para dashboard

    
    // ========== DASHBOARD (Para todos autenticados) ==========
    
    // ✅ 1️⃣3️⃣ Acessa o dashboard
    //    GET /dashboard
    Route::get('/dashboard', function () {
        // 1️⃣4️⃣ Busca avisos do ano atual
        $announcements = \App\Models\Announcement::where('turma', date('Y'))
                                                ->orderBy('priority', 'desc')
                                                ->orderBy('created_at', 'desc')
                                                ->get();
        //    ↑ Usa Model Announcement para consultar banco
        
        // 1️⃣5️⃣ Passa dados para a view dashboard.blade.php
        return view('dashboard', compact('announcements'));
    })->name('dashboard');

    
    // ========== PERFIL DO USUÁRIO (GET) ==========
    
    // ✅ 1️⃣6️⃣ Usuário acessa seu perfil
    //    GET /perfil
    Route::get('/perfil', [ProfileController::class, 'index'])->name('profile');
    //         ↓ 1️⃣7️⃣ Chama ProfileController::index()
    //            └─ return view('profile')

    // ✅ 1️⃣8️⃣ Usuário edita perfil (email, senha)
    //    PUT /perfil
    Route::put('/perfil', [ProfileController::class, 'update'])->name('profile.update');
    //        ↓ 1️⃣9️⃣ Chama ProfileController::update()
    //           ├─ Valida novo email e senha
    //           ├─ Verifica senha atual
    //           ├─ Atualiza usuário no banco
    //           └─ Redireciona com mensagem de sucesso
    
    
    // ========== FOTO DE PERFIL (Upload) ==========
    
    // ✅ 2️⃣0️⃣ Usuário faz upload de foto
    //    POST /usuarios/{user}/photo
    Route::post('/usuarios/{user}/photo', [PhotoController::class, 'update'])->name('profile.photo');
    //                                      ↓ 2️⃣1️⃣ Chama PhotoController::update()
    //                                         ├─ Valida arquivo (é imagem?)
    //                                         ├─ Salva arquivo em storage/
    //                                         ├─ Atualiza campo 'photo' do usuário
    //                                         └─ Salva no banco

    
    // ==========================================
    // 🔐 ROTAS EXCLUSIVAS PARA INSTRUTORES
    // ==========================================
    
    // 2️⃣2️⃣ MIDDLEWARE 'instructor' → Verifica se é instrutor/master
    //        Se NÃO → Redireciona com erro
    //     └─ Se SIM → Permite acessar rotas abaixo
    
    Route::middleware('instructor')->group(function () {
        
        // ===== GESTÃO DE ATIRADORES =====
        
        // ✅ 2️⃣3️⃣ Instrutor visualiza lista de atiradores
        //    GET /atiradores
        Route::get('/atiradores', [\App\Http\Controllers\AtiradorController::class, 'index'])
            ->name('atiradores.index');
        //   ↓ 2️⃣4️⃣ Busca todos os atiradores do banco
        
        // ✅ 2️⃣5️⃣ Instrutor cria novo atirador
        //    POST /atiradores
        Route::post('/atiradores', [\App\Http\Controllers\AtiradorController::class, 'store'])
            ->name('atiradores.store');
        //   ↓ 2️⃣6️⃣ Valida dados, cria novo User e salva
        
        // ✅ 2️⃣7️⃣ Instrutor edita atirador
        //    PUT /atiradores/{user}
        Route::put('/atiradores/{user}', [\App\Http\Controllers\AtiradorController::class, 'update'])
            ->name('atiradores.update');
        //   ↓ 2️⃣8️⃣ Atualiza dados do atirador no banco
        
        // ✅ 2️⃣9️⃣ Instrutor ativa/desativa CFC de um atirador
        //    PATCH /atiradores/{user}/toggle-cfc
        Route::patch('/atiradores/{user}/toggle-cfc', [\App\Http\Controllers\AtiradorController::class, 'toggleCfc'])
            ->name('atiradores.toggle-cfc');
        //   ↓ 3️⃣0️⃣ Inverte o valor de 'is_cfc' do usuário
        
        // ✅ 3️⃣1️⃣ Instrutor deleta atirador
        //    DELETE /atiradores/{user}
        Route::delete('/atiradores/{user}', [\App\Http\Controllers\AtiradorController::class, 'destroy'])
            ->name('atiradores.destroy');
        //   ↓ 3️⃣2️⃣ Remove usuário do banco

        
        // ===== GESTÃO DE AVISOS =====
        
        // ✅ 3️⃣3️⃣ Resource route para avisos (CRUD completo)
        //    GET    /avisos         → index()   (lista)
        //    POST   /avisos         → store()   (cria)
        //    GET    /avisos/{id}    → show()    (detalhes) [EXCLUÍDO: acessível para todos]
        //    GET    /avisos/{id}/edit → edit()   (formulário edição)
        //    PUT    /avisos/{id}    → update()  (atualiza)
        //    DELETE /avisos/{id}    → destroy() (deleta)
        Route::resource('avisos', \App\Http\Controllers\AnnouncementController::class)->except(['show']);
    });

    // ✅ 3️⃣4️⃣ Rota de VISUALIZAR aviso (acessível para TODOS autenticados)
    //    GET /avisos/{aviso}
    //    Nota: está FORA do middleware 'instructor'
    Route::get('/avisos/{aviso}', [\App\Http\Controllers\AnnouncementController::class, 'show'])
        ->name('avisos.show');
    //   ↓ 3️⃣5️⃣ Qualquer usuário autenticado pode ver avisos
});
```

---

## 📄 2. app/Http/Controllers/AuthController.php (COM COMENTÁRIOS)

```php
<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class AuthController extends Controller
{
    // ✅ 2️⃣ Exibe a página de login
    // GET /
    public function showLogin()
    {
        // Retorna o arquivo: resources/views/login.blade.php
        return view('login');
        // ↓ 3️⃣ Usuário vê o formulário de login
    }

    // ✅ 4️⃣ Processa o login (POST)
    // POST /login → vem de login.blade.php
    public function login(Request $request)
    {
        // PASSO 1: Validação dos dados
        // ✅ 5️⃣ Verifica se 'user' e 'password' foram preenchidos
        $credentials = $request->validate([
            'user' => ['required', 'string'],
            'password' => ['required'],
        ]);
        // Se não passarem na validação → erro 422 (formulário retorna com erros)
        
        // PASSO 2: Buscar usuário no banco
        // ✅ 6️⃣ Procura na tabela 'users' por RA OU CPF
        $user = User::where('ra', $credentials['user'])
                    ->orWhere('cpf', $credentials['user'])
                    ->first();
        // Como isso funciona:
        // SELECT * FROM users WHERE ra = '202301001' OR cpf = '123.456.789-00'
        // ↑ Procura por QUALQUER um destes campos
        
        // PASSO 3: Verificação de credenciais
        // ✅ 7️⃣ Se encontrou o usuário E a senha está correta
        if ($user && Auth::attempt(['ra' => $user->ra, 'password' => $credentials['password']])) {
            // Auth::attempt() faz 2 coisas:
            // 1. Verifica se Hash::check($password, $user->password) é true
            // 2. Se correto → cria a sessão de autenticação
            
            // ✅ 8️⃣ Regenera a sessão (segurança contra session fixation)
            $request->session()->regenerate();
            // Nova sessão criada com ID diferente
            
            // ✅ 9️⃣ Redireciona para o dashboard
            return redirect()->intended('dashboard');
            // .intended() = se o usuário estava indo para /perfil e foi redirecionado
            //              para login, volta para /perfil após logar
            // Padrão: vai para 'dashboard'
        }
        
        // ❌ Se errou: Credenciais não conferem
        // ✅ 🔟 Volta para a página de login com mensagem de erro
        return back()
            ->withErrors([
                'user' => 'As credenciais informadas não coincidem com nossos registros.',
            ])
            ->onlyInput('user');  // Mantém o RA/CPF que foi digitado
    }

    // ✅ 5️⃣ Logout (Saída)
    // POST /logout
    public function logout(Request $request)
    {
        // 1️⃣1️⃣ Destroi a sessão do usuário autenticado
        Auth::logout();
        
        // 1️⃣2️⃣ Invalida a sessão de browser existente
        $request->session()->invalidate();
        
        // 1️⃣3️⃣ Regenera o token CSRF (segurança)
        $request->session()->regenerateToken();
        
        // 1️⃣4️⃣ Redireciona para a página inicial (login)
        return redirect('/');
    }
}
```

---

## 📄 3. app/Http/Middleware/CheckFirstAccess.php (COM COMENTÁRIOS)

```php
<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckFirstAccess
{
    /**
     * Handle an incoming request.
     * 
     * Este middleware é executado em TODAS AS ROTAS dentro de:
     * Route::middleware(['auth', 'first_access'])->group(...)
     * 
     * É a SEGUNDA verificação após o middleware 'auth'
     */
    public function handle(Request $request, Closure $next): Response
    {
        // ✅ 7️⃣ PRIMEIRA VERIFICAÇÃO: Está autenticado?
        if (auth()->check()
            
            // ✅ 8️⃣ SEGUNDA VERIFICAÇÃO: Tem role === 'atirador'?
            // Instrutores e Masters pulam esta verificação
            && auth()->user()->role === 'atirador'
            
            // ✅ 9️⃣ TERCEIRA VERIFICAÇÃO: Email está vazio?
            // empty() retorna true se NULL, '', 0, false, etc
            && empty(auth()->user()->email)) 
        {
            // Se TODAS as 3 verificações forem TRUE:
            // → Usuário é atirador SEM EMAIL configurado
            
            // EXCEÇÃO: Se está acessando as rotas abaixo, PERMITE passar
            // (pois são rotas do formulário de primeiro acesso)
            if (! $request->routeIs('primeiro-acesso')           // GET /primeiro-acesso (formulário)
                && ! $request->routeIs('primeiro-acesso.store')  // POST /primeiro-acesso (envio)
                && ! $request->routeIs('logout')) {              // POST /logout
                
                // 🔟 Se está tentando acessar QUALQUER OUTRA rota
                // → REDIRECIONA FORÇADAMENTE para /primeiro-acesso
                return redirect()->route('primeiro-acesso');
            }
        }
        
        // 1️⃣1️⃣ Se passou em todas as verificações
        // → Permite continuar para o controller/action
        return $next($request);
    }
}
```

---

## 📄 4. app/Http/Controllers/PrimeiroAcessoController.php (COM COMENTÁRIOS)

```php
<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class PrimeiroAcessoController extends Controller
{
    // ✅ 🔟 Exibe formulário de primeiro acesso
    // GET /primeiro-acesso
    public function index()
    {
        // 1️⃣1️⃣ Se o usuário JÁ tem email configurado
        if (!empty(auth()->user()->email)) {
            // → Já fez o primeiro acesso antes
            // → Redireciona para dashboard (evita ver o formulário novamente)
            return redirect()->route('dashboard');
        }
        
        // 1️⃣2️⃣ Se não tem email → Mostra o formulário
        return view('primeiro_acesso');
        // Arquivo: resources/views/primeiro_acesso.blade.php
    }

    // ✅ 1️⃣2️⃣ Processa envio do formulário
    // POST /primeiro-acesso (vem de primeiro_acesso.blade.php)
    public function store(Request $request)
    {
        // 1️⃣3️⃣ VERIFICAÇÃO INICIAL: Se já configurou (segurança dupla)
        if (!empty(auth()->user()->email)) {
            return redirect()->route('dashboard');
        }

        // 1️⃣4️⃣ PASSO 1: Validação dos dados
        $request->validate([
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            //         ↓                                           ↓
            //      É preciso?               Deve ser único na tabela users
            
            'password' => ['required', 'confirmed', Password::defaults()],
            //                        ↓                     ↓
            //        password_confirmation deve ser igual   requisitos mínimos da senha
        ]);
        // Se falhar em qualquer validação →erro 422 (formulário retorna com erros)

        // 1️⃣5️⃣ PASSO 2: Pega o usuário autenticado atualmente
        $user = auth()->user();
        // Como já passou pelo middleware 'auth' → sempre vai ter usuário

        // 1️⃣6️⃣ PASSO 3: Atualiza os dados do usuário
        $user->email = $request->email;
        //Exemplo: "joao@email.com"
        
        $user->password = Hash::make($request->password);
        // Hash::make() = Criptografa a senha com SHA-256 + salt
        // Exemplo: 
        //   Senha: "senha123"
        //   Hash: "$2y$12$9qk...[78 caracteres]"
        
        // 1️⃣7️⃣ PASSO 4: Salva as mudanças no banco de dados
        $user->save();
        // UPDATE users SET email = '...', password = '...' WHERE id = 1

        // 1️⃣8️⃣ PASSO 5: Redireciona para dashboard com mensagem de sucesso
        return redirect()->route('dashboard')
            ->with('success', 'Conta configurada com sucesso. Bem-vindo!');
        // A mensagem fica armazenada na sessão
        // Pode ser exibida com {{ session('success') }} no blade
    }
}
```

---

## 📄 5. app/Http/Middleware/CheckInstructorRole.php (COM COMENTÁRIOS)

```php
<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckInstructorRole
{
    /**
     * Handle an incoming request.
     * 
     * Este middleware é executado em TODAS AS ROTAS dentro de:
     * Route::middleware('instructor')->group(...)
     * 
     * Verifica se o usuário tem permissão de INSTRUTOR ou MASTER
     */
    public function handle(Request $request, Closure $next): Response
    {
        // ✅ 2️⃣2️⃣ VERIFICAÇÃO 1: Está autenticado?
        if (auth()->check()
            
            // ✅ 2️⃣3️⃣ VERIFICAÇÃO 2: Role NÃO está em ['master', 'instructor']?
            // in_array() retorna:
            //   true  se encontrou o valor no array
            //   false se NÃO encontrou
            // ! (negação) inverte o resultado
            && !in_array(auth()->user()->role, ['master', 'instructor'])) 
        {
            // Se as 2 verificações forem TRUE:
            // → Usuário está logado MAS é um "atirador"
            
            // ❌ 2️⃣4️⃣ Bloqueia acesso e redireciona com mensagem de erro
            return redirect()
                ->route('dashboard')
                ->with('error', 'Acesso negado. Ação restrita a instrutores.');
        }
        
        // 2️⃣5️⃣ Se passou na verificação → Permite continuar
        return $next($request);
    }
}
```

---

## 📄 6. app/Http/Controllers/ProfileController.php (COM COMENTÁRIOS)

```php
<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class ProfileController extends Controller
{
    // ✅ 1️⃣6️⃣ Exibe página de perfil
    // GET /perfil
    public function index()
    {
        // 1️⃣7️⃣ Retorna a view com dados do perfil
        return view('profile');
        // Dentro da view: {{ auth()->user()->email }}, {{ auth()->user()->name }}, etc
    }

    // ✅ 1️⃣8️⃣ Atualiza os dados do perfil
    // PUT /perfil (enviado por formulário em profile.blade.php)
    public function update(Request $request)
    {
        // 1️⃣9️⃣ Pega o usuário autenticado
        /** @var \App\Models\User $user */
        $user = Auth::user();
        
        // 2️⃣0️⃣ Validações
        $request->validate([
            'email' => ['required', 'email', 'unique:users,email,' . $user->id],
            //                                  ↓
            //                Permite o PRÓPRIO email (não reclama que já existe)
            
            'current_password' => ['nullable', 'required_with:password'],
            //                                 ↓
            //                Se preencheu 'password' → 'current_password' é obrigatório
            
            'password' => ['nullable', 'min:6', 'confirmed'],
            //                                  ↓
            //                Deve ter 'password_confirmation' igual
        ], [
            // Mensagens de erro personalizadas em português
            'email.unique' => 'Este e-mail já está em uso.',
            'password.confirmed' => 'As senhas não coincidem.',
            'password.min' => 'A nova senha deve ter no mínimo 6 caracteres.',
        ]);
        
        // 2️⃣1️⃣ Atualiza o email
        $user->email = $request->email;

        // 2️⃣2️⃣ Se preencheu nova senha
        if ($request->filled('password')) {
            // 2️⃣3️⃣ SEGURANÇA: Verifica se a senha ATUAL está correta
            if (!Hash::check($request->current_password, $user->password)) {
                // Hash::check($senha_digitada, $hash_no_banco)
                // Retorna: true se correto, false se errado
                
                // Se errou a senha atual → erro
                return back()->withErrors(
                    ['current_password' => 'A senha atual informada está incorreta.']
                );
            }
            
            // 2️⃣4️⃣ Criptografa e atualiza a nova senha
            $user->password = Hash::make($request->password);
        }

        // 2️⃣5️⃣ Salva as mudanças no banco
        $user->save();

        // 2️⃣6️⃣ Redireciona com mensagem de sucesso
        return back()->with('success', 'Perfil atualizado com sucesso!');
    }
}
```

---

## 📄 7. app/Models/User.php (COM COMENTÁRIOS)

```php
<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    // HasFactory: permite usar factory para tests
    // Notifiable: permite enviar notificações (email, SMS, etc)
    use HasFactory, Notifiable;

    /**
     * Atributos que podem ser preenchidos com mass assignment
     * Exemplo: User::create(['name' => '...', 'email' => '...']);
     * 
     * ESTES campos podem ser preenchidos
     */
    protected $fillable = [
        'name',              // Nome completo
        'nome_de_guerra',    // Codinome (ex: "Alfa", "Bravo")
        'email',             // Email do usuário
        'password',          // Senha (hasheada)
        'ra',                // Registro Acadêmico
        'cpf',               // CPF (Cadastro de Pessoa Física)
        'role',              // Papel (atirador, instructor, master)
        'points',            // Pontos acumulados
        'faults',            // Faltas
        'photo',             // Caminho para foto de perfil
        'numero',            // Número do atirador
        'turma',             // Turma/Ano
        'is_cfc',            // Boolean: tem CFC?
        'telefone',          // Telefone para contato
    ];

    /**
     * Atributos que NÃO devem ser retornados em serializações
     * Exemplo: quando faz $user->toJson(), senha NÃO aparece
     */
    protected $hidden = [
        'password',          // Nunca serializa password
        'remember_token',    // Token antigo (compatibilidade)
    ];

    /**
     * Define conversões de tipo para certos campos
     * 
     * Sem casting:
     *   $user->is_cfc retorna "1" ou "0" (string)
     * 
     * Com casting 'boolean':
     *   $user->is_cfc retorna true ou false (boolean PHP)
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',  // Converte para DateTime
            'password' => 'hashed',             // Usa hidden (não mostra)
            'is_cfc' => 'boolean',              // Converte para bool true/false
        ];
    }
}
```

---

## 📄 8. bootstrap/app.php (COM COMENTÁRIOS)

```php
<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

// ✅ 1️⃣ Aplicação começa aqui
// O Laravel carrega este arquivo na inicialização

return Application::configure(basePath: dirname(__DIR__))
    
    // ✅ 2️⃣ Configuração de Roteamento
    ->withRouting(
        // Define arquivo de rotas web
        web: __DIR__.'/../routes/web.php',
        // ↓ Procura por rotas em routes/web.php
        
        commands: __DIR__.'/../routes/console.php',
        // ↓ Procura por comandos em routes/console.php
        
        health: '/up',
        // ↓ Health check em GET /up
    )
    
    // ✅ 3️⃣ Configuração de Middlewares
    ->withMiddleware(function (Middleware $middleware): void {
        // REGISTROS DE MIDDLEWARES
        // Define atalhos para usar nas rotas
        
        $middleware->alias([
            // Nome           Classe real
            'instructor' => \App\Http\Middleware\CheckInstructorRole::class,
            // ↓ Quando usar Route::middleware('instructor')
            //   Laravel carrega CheckInstructorRole
            
            'first_access' => \App\Http\Middleware\CheckFirstAccess::class,
            // ↓ Quando usar Route::middleware('first_access')
            //   Laravel carrega CheckFirstAccess
        ]);
    })
    
    // ✅ 4️⃣ Configuração de Exceções
    ->withExceptions(function (Exceptions $exceptions): void {
        // Aqui define como tratar erros
        // Por padrão: mostra página de erro padrão do Laravel
    })
    
    // ✅ 5️⃣ Cria a aplicação com todas as configurações acima
    ->create();
```

---

## 📊 RESUMO VISUAL: De Login até Dashboard

```yaml
1️⃣ Usuário acessa http://localhost/
   ↓
2️⃣ Router procura rota GET "/"
   ↓
3️⃣ Encontra: Route::get('/', [AuthController::class, 'showLogin'])
   ↓
4️⃣ Chama: AuthController::showLogin()
   ↓
5️⃣ Retorna: view('login')
   ↓
6️⃣ Navegador renderiza: login.blade.php
   ↓
7️⃣ Usuário vê formulário de login e preenche:
   - RA/CPF: 202301001
   - Senha: senha123
   ↓
8️⃣ Usuário clica "Entrar"
   ↓
9️⃣ Formulário envia: POST /login
   ↓
🔟 Router procura rota POST "/login"
   ↓
1️⃣1️⃣ Encontra: Route::post('/login', [AuthController::class, 'login'])
   ↓
1️⃣2️⃣ Chama: AuthController::login(Request)
   ├─ Valida formário ✅
   ├─ Busca User por RA/CPF ✅
   ├─ Verifica senha ✅
   ├─ Cria sessão ✅
   └─ Redireciona para: /dashboard
   ↓
1️⃣3️⃣ Router procura rota GET "/dashboard"
   ↓
1️⃣4️⃣ Encontra nesta ordem:
   Route::middleware(['auth', 'first_access'])->group(function () {
       Route::get('/dashboard', ...);
   })
   ↓
1️⃣5️⃣ Executa Middleware: 'auth'
   └─ auth()->check() → SIM ✅ (está logado)
   └─ Continua...
   ↓
1️⃣6️⃣ Executa Middleware: 'first_access'
   └─ auth()->check() → SIM ✅
   └─ role === 'atirador' → Depende do role
   ├─ Se SIM + email vazio → Redireciona para /primeiro-acesso
   │  ├─ Mostra formulário
   │  ├─ Usuário preenche email e senha
   │  ├─ Salva no banco
   │  └─ Redireciona de volta para /dashboard
   │
   └─ Se email preenchido → Continua...
   ↓
1️⃣7️⃣ Executa controller/closure:
   Route::get('/dashboard', function () {
       $announcements = Announcement::where(...)->get();
       return view('dashboard', compact('announcements'));
   })
   ↓
1️⃣8️⃣ Navegador renderiza: dashboard.blade.php com avisos
   ↓
1️⃣9️⃣ ✅ Usuário vê o dashboard!
```

---

## 🎯 Fluxo Alternativo: Instrutor Acessando Tela De Atiradores

```yaml
1️⃣ Usuário clica em "Atiradores" (sidebar)
   ↓
2️⃣ Vai para: GET /atiradores
   ↓
3️⃣ Router procura rota GET "/atiradores"
   ↓
4️⃣ Encontra:
   Route::middleware(['auth', 'first_access'])->group(function () {
       Route::middleware('instructor')->group(function () {
           Route::get('/atiradores', [AtiradorController::class, 'index']);
       })
   })
   ↓
5️⃣ Executa Middleware: 'auth'
   └─ auth()->check() → SIM ✅
   └─ Continua...
   ↓
6️⃣ Executa Middleware: 'first_access'
   └─ role === 'atirador' → NÃO ❌ (é 'instructor')
   └─ Não intercepta, continua...
   ↓
7️⃣ Executa Middleware: 'instructor'
   └─ auth()->check() → SIM ✅
   └─ role in ['master', 'instructor'] → SIM ✅
   └─ Continua...
   ↓
8️⃣ Executa controller:
   AtiradorController::index()
   ├─ $atiradores = User::where('role', 'atirador')->get();
   └─ return view('atiradores/index', compact('atiradores'));
   ↓
9️⃣ Navegador renderiza: atiradores/index.blade.php com lista
   ↓
🔟 ✅ Instrutor vê lista de atiradores!
```

---

## ❌ Fluxo Alternativo: Atirador Tenta Acessar Tela De Atiradores

```yaml
1️⃣ Atirador tenta acessar: GET /atiradores (por URL direta)
   ↓
2️⃣ Router procura rota GET "/atiradores"
   ↓
3️⃣ Encontra a rota dentro de:
   Route::middleware('instructor')->group(...)
   ↓
4️⃣ Executa Middleware: 'auth'
   └─ auth()->check() → SIM ✅ (está logado)
   └─ Continua...
   ↓
5️⃣ Executa Middleware: 'first_access'
   └─ role === 'atirador' → SIM ✅
   └─ empty(email) → Depende (pode ter feito primeiro acesso)
   ├─ Se email vazio → Redireciona para /primeiro-acesso
   └─ Se email preenchido → Continua...
   ↓
6️⃣ Executa Middleware: 'instructor'
   └─ auth()->check() → SIM ✅ (está logado)
   └─ in_array(role, ['master', 'instructor']) → NÃO ❌
   └─ role = 'atirador' (NÃO está na lista)
   ↓
7️⃣ Middleware BLOQUEIA:
   return redirect()->route('dashboard')->with('error', 'Acesso negado...');
   ↓
8️⃣ Redireciona para: /dashboard
   ↓
9️⃣ Exibe mensagem de erro: "Acesso negado. Ação restrita a instrutores."
   ↓
🔟 ❌ Atirador NÃO conseguiu acessar a página!
```

---

**Este documento mostra exatamente a sequência numerada de execução de cada parte!**
