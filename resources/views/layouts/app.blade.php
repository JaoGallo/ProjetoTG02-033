<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Tiro de Guerra')</title>
    <link rel="icon" type="image/png" href="{{ asset('tg_logo.png') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700;900&display=swap" rel="stylesheet">

    <!-- Importação Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    @yield('styles')

    <!-- Pre-render Sidebar State -->
    <script>
        (function () {
            localStorage.removeItem('sidebarState');
        })();
    </script>
</head>

<body>
    <div class="app-wrapper">
        {{-- Sidebar desativada: o menu principal agora fica no pop-up do menu-toggle.
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
                <a href="{{ route('dashboard') }}"
                    class="nav-item {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                    <i class="fa-solid fa-house"></i>
                    <span>Início</span>
                </a>
                @if(in_array(auth()->user()->role, ['master', 'instructor']))
                    <a href="{{ route('atiradores.index') }}"
                        class="nav-item {{ request()->routeIs('atiradores.*') ? 'active' : '' }}">
                        <i class="fa-solid fa-person-military-rifle"></i>
                        <span>Atiradores</span>
                    </a>
                    <a href="{{ route('avisos.index') }}"
                        class="nav-item {{ request()->routeIs('avisos.*') ? 'active' : '' }}">
                        <i class="fa-solid fa-bullhorn"></i>
                        <span>Avisos</span>
                    </a>
                    <a href="{{ route('frequencia.index') }}"
                        class="nav-item {{ request()->routeIs('frequencia.*') ? 'active' : '' }}">
                        <i class="fa-solid fa-calendar-check"></i>
                        <span>Frequência</span>
                    </a>
                @endif
                <a href="{{ route('escalas.index') }}"
                    class="nav-item {{ request()->routeIs('escalas.*') ? 'active' : '' }}">
                    <i class="fa-solid fa-calendar-days"></i>
                    <span>Escalas e Serviços</span>
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
        --}}

        <!-- Overlay for mobile sidebar -->
        <div class="sidebar-overlay" id="sidebarOverlay"></div>

        <!-- Main Content Area -->
        <div class="main-content" id="mainContent">
            <!-- Unified Top Bar -->
            <header class="app-header">
                <button class="menu-toggle" id="menuToggle" title="Abrir menu">
                    <i class="fa-solid fa-bars"></i>
                </button>
                <div class="quick-menu-popover" id="quickMenuPopover" aria-hidden="true">
                    <div class="quick-menu-sidebar-header">
                        <img src="{{ asset('tg_logo.png') }}" alt="Logo">
                        <div>
                            <strong>TIRO DE GUERRA</strong>
                            <span>S. J. Do Rio Preto</span>
                        </div>
                    </div>

                    <nav class="quick-menu-nav">
                        <a href="{{ route('dashboard') }}"
                            class="quick-menu-nav-item {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                            <i class="fa-solid fa-house"></i>
                            <span>Início</span>
                        </a>
                        @if(in_array(auth()->user()->role, ['master', 'instructor']))
                            <a href="{{ route('atiradores.index') }}"
                                class="quick-menu-nav-item {{ request()->routeIs('atiradores.*') ? 'active' : '' }}">
                                <i class="fa-solid fa-person-military-rifle"></i>
                                <span>Atiradores</span>
                            </a>
                            <a href="{{ route('avisos.index') }}"
                                class="quick-menu-nav-item {{ request()->routeIs('avisos.*') ? 'active' : '' }}">
                                <i class="fa-solid fa-bullhorn"></i>
                                <span>Avisos</span>
                            </a>
                            <a href="{{ route('frequencia.index') }}"
                                class="quick-menu-nav-item {{ request()->routeIs('frequencia.*') ? 'active' : '' }}">
                                <i class="fa-solid fa-calendar-check"></i>
                                <span>Frequência</span>
                            </a>
                        @endif
                        <a href="{{ route('escalas.index') }}"
                            class="quick-menu-nav-item {{ request()->routeIs('escalas.*') ? 'active' : '' }}">
                            <i class="fa-solid fa-calendar-days"></i>
                            <span>Escalas e Serviços</span>
                        </a>
                        <a href="#" class="quick-menu-nav-item">
                            <i class="fa-solid fa-file-invoice"></i>
                            <span>Relatórios</span>
                        </a>
                        <a href="{{ route('profile') }}"
                            class="quick-menu-nav-item {{ request()->routeIs('profile') ? 'active' : '' }}">
                            <i class="fa-solid fa-user-gear"></i>
                            <span>Perfil</span>
                        </a>
                    </nav>

                    <form action="{{ route('logout') }}" method="POST" class="quick-menu-logout">
                        @csrf
                        <button type="submit">
                            <i class="fa-solid fa-right-from-bracket"></i>
                            <span>Sair</span>
                        </button>
                    </form>
                </div>
                @hasSection('header-left')
                    @yield('header-left')
                @else
                    <div style="font-weight: 700; color: var(--primary-olive-dark); font-size: 1.1rem; letter-spacing: -0.5px;">
                        @yield('title', 'DASHBOARD')
                    </div>
                @endif

                <div class="app-header-actions">
                    @hasSection('header-right')
                        @yield('header-right')
                    @endif

                    <a href="{{ route('profile') }}" class="header-user-summary" title="Abrir perfil">
                        @if(auth()->user()->role === 'atirador' || auth()->user()->role === 'monitor')
                            <div class="header-user-metrics">
                                <div class="header-user-metric">
                                    <span>Faltas</span>
                                    <strong>{{ auth()->user()->faults }}</strong>
                                </div>
                                <div class="header-user-metric">
                                    <span>Pontos</span>
                                    <strong class="{{ auth()->user()->points >= 100 ? 'danger' : '' }}">
                                        {{ auth()->user()->points }}<small>/120</small>
                                    </strong>
                                </div>
                            </div>
                        @endif

                        <div class="header-user-info">
                            <h2>{{ auth()->user()->name }}</h2>
                            <div>
                                <span class="badge">{{ strtoupper(auth()->user()->role) }}</span>
                                @if(auth()->user()->ra)
                                    <span>RA: {{ auth()->user()->ra }}</span>
                                @endif
                            </div>
                        </div>

                        <div class="dash-avatar header-user-avatar">
                            @if(auth()->user()->photo)
                                <img src="{{ asset('storage/' . auth()->user()->photo) }}" alt="Avatar">
                            @else
                                <i class="fa-solid fa-user"></i>
                            @endif
                        </div>
                    </a>
                </div>
            </header>

            <div class="dashboard-container">
                @yield('content')
            </div>
        </div>
    </div>

    <!-- Toggle Sidebar Script -->
    <script>
        const menuToggle = document.getElementById('menuToggle');
        const mainContent = document.getElementById('mainContent');
        const overlay = document.getElementById('sidebarOverlay');
        const quickMenuPopover = document.getElementById('quickMenuPopover');

        // Apply initial state from localStorage
        function applyInitialState() {
            mainContent.classList.add('full-width');
        }

        function toggleMenu() {
            toggleQuickMenu();
        }

        function toggleQuickMenu() {
            if (!quickMenuPopover) return;

            const isOpen = quickMenuPopover.classList.toggle('active');
            quickMenuPopover.setAttribute('aria-hidden', isOpen ? 'false' : 'true');
            menuToggle.classList.toggle('active', isOpen);
        }

        function closeQuickMenu() {
            if (!quickMenuPopover) return;

            quickMenuPopover.classList.remove('active');
            quickMenuPopover.setAttribute('aria-hidden', 'true');
            menuToggle.classList.remove('active');
        }

        if (menuToggle) menuToggle.addEventListener('click', toggleMenu);
        if (overlay) overlay.addEventListener('click', closeQuickMenu);

        document.addEventListener('click', (event) => {
            if (!quickMenuPopover || !menuToggle) return;
            if (!quickMenuPopover.contains(event.target) && !menuToggle.contains(event.target)) {
                closeQuickMenu();
            }
        });

        document.addEventListener('keydown', (event) => {
            if (event.key === 'Escape') {
                closeQuickMenu();
            }
        });

        // Apply state on load
        applyInitialState();

        // Close menu when clicking a nav item
        document.querySelectorAll('.nav-item, .quick-menu-nav-item').forEach(item => {
            item.addEventListener('click', () => {
                closeQuickMenu();
            });
        });

        // Handle window resize
        window.addEventListener('resize', () => {
            closeQuickMenu();
            overlay.classList.remove('active');
            document.body.style.overflow = 'auto';
            applyInitialState();
        });
    </script>
    @yield('scripts')
</body>

</html>
