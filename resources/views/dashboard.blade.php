@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
    <!-- Desktop Header Info (Hidden on Mobile) -->
    <div class="header-desktop">
        <div>
            <h1 style="margin: 0; color: var(--primary-olive-dark);">Dashboard</h1>
            <p style="margin: 0; color: var(--text-secondary); font-size: 0.9rem;">Sistema de Gestão - TG 02-033</p>
        </div>
    </div>

    <div class="welcome-section">
        <div style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 1.5rem;">
            <div style="display: flex; align-items: center; gap: 1.25rem;">
                <div class="dash-avatar">
                    @if(Auth::user()->photo)
                        <img src="{{ asset('storage/' . Auth::user()->photo) }}" alt="Avatar" style="width: 100%; height: 100%; object-fit: cover;">
                    @else
                        <i class="fa-solid fa-user"></i>
                    @endif
                </div>
                <div>
                    <span style="color: var(--text-secondary); font-size: 0.9rem;">Bem-vindo(a)</span>
                    <h2 style="margin: 0.25rem 0; font-size: 1.8rem;">{{ Auth::user()->name }}</h2>
                </div>
            </div>
            <span class="badge">{{ Auth::user()->role }}</span>
        </div>
    </div>

    <section class="stats-grid">
        <div class="stat-card">
            <div class="stat-icon"><i class="fa-solid fa-user-check"></i></div>
            <div class="stat-content">
                <h3>Total de Faltas</h3>
                <div class="value">{{ Auth::user()->faults }}</div>
            </div>
        </div>
        
        <div class="stat-card" style="border-bottom: 4px solid #c53030;">
            <div class="stat-icon"><i class="fa-solid fa-triangle-exclamation" style="color: #c53030;"></i></div>
            <div class="stat-content">
                <h3>Pontos de Falta</h3>
                <div class="value" style="color: #c53030;">{{ Auth::user()->points }} <small style="font-size: 0.8rem; opacity: 0.6;">/ 120</small></div>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon"><i class="fa-solid fa-id-card"></i></div>
            <div class="stat-content">
                <h3>Registro (RA)</h3>
                <div class="value">{{ Auth::user()->ra }}</div>
            </div>
        </div>
    </section>

    <!-- Alerta de Proximidade do Limite -->
    @if(Auth::user()->points >= 100)
        <div class="alert alert-danger" style="margin-bottom: 2rem;">
            <i class="fa-solid fa-circle-exclamation"></i>
            <div>
                <strong>Alerta Crítico:</strong> Você atingiu {{ Auth::user()->points }} pontos. O limite é 120 antes da dispensa.
            </div>
        </div>
    @endif

    <div style="background: #fff; padding: 4rem 2rem; border-radius: 12px; border: 2px dashed var(--border-color); text-align: center; color: var(--text-secondary);">
        <i class="fa-solid fa-calendar-check" style="font-size: 4rem; margin-bottom: 1.5rem; color: var(--primary-olive); opacity: 0.2;"></i>
        <h3 style="margin-bottom: 0.5rem; color: var(--text-primary);">Próximas Escalas</h3>
        <p style="max-width: 400px; margin: 0 auto 1.5rem;">O módulo de escalas inteligentes está sendo
            configurado para o seu pelotão.</p>
        <span class="badge" style="background: #e9ece9; color: var(--primary-olive-dark);">Em breve</span>
    </div>
@endsection