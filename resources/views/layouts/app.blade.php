<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Tiro de Guerra')</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700;900&display=swap" rel="stylesheet">

    <!-- Importação Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    @yield('styles')
</head>

<body>
    <div class="app-wrapper">
        <!-- Sidebar -->
        <aside class="sidebar" id="sidebar">
            <div class="sidebar-header">
                <img src="{{ asset('tg_logo.png') }}" alt="Logo"
                    style="height: 80px; margin-bottom: 1rem; filter: drop-shadow(0 0 10px rgba(255,255,255,0.2));">
                <div style="text-align: center;">
                    <div style="font-weight: 900; letter-spacing: 1px; font-size: 1.1rem;">TIRO DE GUERRA</div>
                    <div style="font-size: 0.7rem; opacity: 0.6; text-transform: uppercase;">S. J. Do Rio Preto</div>
                </div>
            </div>

            <nav class="sidebar-nav">
                <a href="{{ route('dashboard') }}" class="nav-item {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                    <i class="fa-solid fa-house"></i>
                    <span>Início</span>
                </a>
                @if(in_array(auth()->user()->role, ['master', 'instructor']))
                <a href="{{ route('atiradores.index') }}" class="nav-item {{ request()->routeIs('atiradores.*') ? 'active' : '' }}">
                    <i class="fa-solid fa-person-military-rifle"></i>
                    <span>Atiradores</span>
                </a>
                @endif
                <a href="#" class="nav-item">
                    <i class="fa-solid fa-calendar-days"></i>
                    <span>Escalas (QTS)</span>
                </a>
                <a href="#" class="nav-item">
                    <i class="fa-solid fa-users"></i>
                    <span>Instruções</span>
                </a>
                <a href="#" class="nav-item">
                    <i class="fa-solid fa-file-invoice"></i>
                    <span>Relatórios</span>
                </a>
                <a href="{{ route('profile') }}" class="nav-item {{ request()->routeIs('profile') ? 'active' : '' }}">
                    <i class="fa-solid fa-user-gear"></i>
                    <span>Perfil</span>
                </a>
            </nav>

            <div class="sidebar-footer">
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="logout-btn-sidebar">
                        <i class="fa-solid fa-right-from-bracket"></i>
                        <span>Sair</span>
                    </button>
                </form>
            </div>
        </aside>

        <!-- Overlay for mobile sidebar -->
        <div class="sidebar-overlay" id="sidebarOverlay"></div>

        <!-- Main Content Area -->
        <div class="main-content">
            <!-- Mobile Top Bar -->
            <header class="mobile-header">
                <button class="menu-toggle" id="menuToggle">
                    <i class="fa-solid fa-bars"></i>
                </button>
                <div style="font-weight: 700; color: var(--primary-olive-dark);">@yield('title', 'DASHBOARD')</div>
                <img src="{{ asset('tg_logo.png') }}" alt="Logo" style="height: 35px;">
            </header>

            <div class="dashboard-container">
                @yield('content')
            </div>
        </div>
    </div>

    <!-- Toggle Sidebar Script -->
    <script>
        const menuToggle = document.getElementById('menuToggle');
        const sidebar = document.getElementById('sidebar');
        const overlay = document.getElementById('sidebarOverlay');

        function toggleMenu() {
            sidebar.classList.toggle('open');
            overlay.classList.toggle('active');
            document.body.style.overflow = sidebar.classList.contains('open') ? 'hidden' : 'auto';
        }

        if (menuToggle) menuToggle.addEventListener('click', toggleMenu);
        if (overlay) overlay.addEventListener('click', toggleMenu);

        // Close menu when clicking a nav item (on mobile)
        document.querySelectorAll('.nav-item').forEach(item => {
            item.addEventListener('click', () => {
                if (window.innerWidth <= 768) {
                    toggleMenu();
                }
            });
        });
    </script>
    @yield('scripts')
</body>

</html>
