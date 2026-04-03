<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TG - Tiro de Guerra</title>
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
        <div class="login-card">
            <div class="login-header">
                <img src="{{ asset('tg_logo.png') }}" alt="Logo Exército Brasileiro" class="logo">
                <h1>Tiro de Guerra</h1>
                <p>Sistema de Administração e Escalas</p>
            </div>
            
            <form class="login-form" action="{{ url('/login') }}" method="POST">
                @csrf
                
                @if($errors->any())
                    <div style="color: #cc4a4a; font-size: 0.85rem; margin-bottom: 1rem; text-align: center; border: 1px solid #eccaca; padding: 0.5rem; border-radius: 4px; background: #fff5f5;">
                        {{ $errors->first() }}
                    </div>
                @endif
                
                <div class="input-group">
                    <label for="user">RA / CPF</label>
                    <input type="text" id="user" name="user" placeholder="Digite sua identificação" value="{{ old('user') }}" required>
                </div>
                
                <div class="input-group password-group">
                    <label for="password">Senha</label>
                    <input type="password" id="password" name="password" placeholder="Digite sua senha" required>
                    <button type="button" id="togglePassword" class="toggle-password">
                        <i class="fa-solid fa-eye" id="eyeIcon"></i>
                    </button>
                </div>
                
                <button type="submit" class="btn-primary w-full mt-4">Entrar</button>
            </form>
        </div>
    </main>

    <script>
        const togglePassword = document.querySelector('#togglePassword');
        const password = document.querySelector('#password');
        const eyeIcon = document.querySelector('#eyeIcon');

        togglePassword.addEventListener('click', function (e) {
            const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
            password.setAttribute('type', type);
            
            // Alternar ícone do Font Awesome
            eyeIcon.classList.toggle('fa-eye');
            eyeIcon.classList.toggle('fa-eye-slash');
        });
    </script>
</body>
</html>
