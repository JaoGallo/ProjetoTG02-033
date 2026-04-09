@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
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

    <!-- Mural de Avisos -->
    <div style="margin-bottom: 2.5rem;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
            <h2 style="font-weight: 800; color: var(--primary-olive-dark); margin: 0;">Mural Digital</h2>
            @if(in_array(Auth::user()->role, ['master', 'instructor']))
                <a href="{{ route('avisos.index') }}" class="badge" style="background: var(--primary-olive); color: white; text-decoration: none; padding: 6px 12px;">GERENCIAR AVISOS</a>
            @endif
        </div>

        <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 1.5rem;">
            @forelse($announcements as $aviso)
                @php
                    $colors = [
                        'geral' => ['bg' => '#f3f4f6', 'text' => '#4b5563', 'border' => '#d1d5db'],
                        'urgente' => ['bg' => '#fee2e2', 'text' => '#b91c1c', 'border' => '#f87171'],
                        'escala' => ['bg' => '#eff6ff', 'text' => '#1e40af', 'border' => '#60a5fa'],
                        'instrucao' => ['bg' => '#ecfdf5', 'text' => '#047857', 'border' => '#34d399'],
                    ];
                    $c = $colors[$aviso->category] ?? $colors['geral'];
                    $isNew = !Auth::user()->instructor && !$aviso->readers()->where('user_id', Auth::id())->exists();
                @endphp
                
                <a href="{{ route('avisos.show', $aviso->id) }}" style="text-decoration: none; color: inherit; display: block;">
                    <div class="stat-card" style="border-left: 6px solid {{ $aviso->priority ? '#ef4444' : $c['border'] }}; margin: 0; min-height: 160px; display: flex; flex-direction: column; justify-content: space-between; position: relative;">
                        @if($isNew)
                            <span style="position: absolute; top: 12px; right: 12px; background: #ef4444; color: white; font-size: 0.6rem; font-weight: 900; padding: 2px 6px; border-radius: 4px;">NOVO</span>
                        @endif
                        
                        <div>
                            <span class="badge" style="background: {{ $c['bg'] }}; color: {{ $c['text'] }}; font-size: 0.7rem; padding: 4px 10px; margin-bottom: 0.75rem;">
                                {{ strtoupper($aviso->category) }}
                            </span>
                            <h4 style="margin: 0.5rem 0; font-size: 1.1rem; line-height: 1.3; font-weight: 700;">{{ $aviso->title }}</h4>
                            <p style="font-size: 0.85rem; color: var(--text-secondary); margin: 0.5rem 0; overflow: hidden; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical;">
                                {{ $aviso->content }}
                            </p>
                        </div>
                        
                        <div style="display: flex; justify-content: space-between; align-items: center; border-top: 1px solid #f3f4f6; padding-top: 10px; margin-top: 10px;">
                            <span style="font-size: 0.7rem; color: #9ca3af;"><i class="fa-regular fa-clock"></i> {{ $aviso->created_at->diffForHumans() }}</span>
                            @if($aviso->attachment)
                                <i class="fa-solid fa-paperclip" style="color: #9ca3af; font-size: 0.8rem;"></i>
                            @endif
                        </div>
                    </div>
                </a>
            @empty
                <div style="grid-column: 1 / -1; background: #fff; padding: 2rem; border-radius: 12px; border: 1px solid var(--border-color); text-align: center; color: var(--text-secondary);">
                    Sem avisos recentes para a sua turma.
                </div>
            @endforelse
        </div>
    </div>

    <!-- Próximas Escalas (Em breve) -->
    <div style="background: #fff; padding: 2rem; border-radius: 12px; border: 1px solid var(--border-color); text-align: center; color: var(--text-secondary);">
        <h3 style="margin-bottom: 0.5rem; color: var(--text-primary); font-size: 1.1rem;">Próximas Escalas</h3>
        <p style="margin: 0; font-size: 0.9rem;">Módulo de escalas em breve.</p>
    </div>
@endsection