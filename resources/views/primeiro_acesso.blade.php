<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TG - Configuração de Acesso</title>
    <link rel="icon" type="image/png" href="{{ asset('tg_logo.png') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
    
    <!-- Importação Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <style>
        .password-group { position: relative; }
        .toggle-password { 
            position: absolute; 
            right: 10px; 
            top: 36px; 
            background: none; 
            border: none; 
            cursor: pointer; 
            color: var(--text-secondary);
            font-size: 0.9rem;
            padding: 5px;
        }
    </style>
</head>
<body>
    <main class="login-container">
        <div class="login-card" style="max-width: 450px;">
            <div class="login-header">
                <img src="{{ asset('tg_logo.png') }}" alt="Logo Exército Brasileiro" class="logo">
                <h1>Primeiro Acesso</h1>
                <p>Configure seu e-mail e nova senha para continuar</p>
            </div>
            
            <form class="login-form" action="{{ route('primeiro-acesso.store') }}" method="POST">
                @csrf
                
                @if(session('warning'))
                    <div style="color: #664d03; background-color: #fff3cd; border-color: #ffecb5; font-size: 0.85rem; margin-bottom: 1rem; text-align: center; border: 1px solid; padding: 0.8rem; border-radius: 4px;">
                        {{ session('warning') }}
                    </div>
                @endif

                @if($errors->any())
                    <div style="color: #cc4a4a; font-size: 0.85rem; margin-bottom: 1rem; text-align: left; border: 1px solid #eccaca; padding: 0.8rem; border-radius: 4px; background: #fff5f5;">
                        <ul style="margin: 0; padding-left: 20px;">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
                
                <div class="input-group">
                    <label for="email">E-mail</label>
                    <input type="email" id="email" name="email" placeholder="Digite seu e-mail" value="{{ old('email') }}" required>
                </div>
                
                <div class="input-group password-group">
                    <label for="password">Nova Senha</label>
                    <input type="password" id="password" name="password" placeholder="Mínimo 8 caracteres" required minlength="8">
                    <button type="button" id="togglePassword1" class="toggle-password">
                        <i class="fa-solid fa-eye eyeIcon"></i>
                    </button>
                </div>

                <div class="input-group password-group">
                    <label for="password_confirmation">Confirmar Nova Senha</label>
                    <input type="password" id="password_confirmation" name="password_confirmation" placeholder="Confirme sua senha" required minlength="8">
                    <button type="button" id="togglePassword2" class="toggle-password">
                        <i class="fa-solid fa-eye eyeIcon"></i>
                    </button>
                </div>
                
                <button type="submit" class="btn-primary w-full mt-4">Salvar e Continuar</button>
            </form>
            
            <form action="{{ route('logout') }}" method="POST" style="margin-top: 15px; text-align: center;">
                @csrf
                <button type="submit" style="background: none; border: none; color: var(--text-secondary); cursor: pointer; text-decoration: underline;">
                    Sair e configurar depois
                </button>
            </form>
        </div>
    </main>

    <script>
        function setupToggle(toggleId, inputId) {
            const toggleBtn = document.getElementById(toggleId);
            const inputField = document.getElementById(inputId);
            const icon = toggleBtn.querySelector('.eyeIcon');

            toggleBtn.addEventListener('click', function () {
                const type = inputField.getAttribute('type') === 'password' ? 'text' : 'password';
                inputField.setAttribute('type', type);
                
                icon.classList.toggle('fa-eye');
                icon.classList.toggle('fa-eye-slash');
            });
        }

        setupToggle('togglePassword1', 'password');
        setupToggle('togglePassword2', 'password_confirmation');
    </script>
</body>
</html>
