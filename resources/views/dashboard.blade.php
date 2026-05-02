@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
    <div class="welcome-section" style="padding: 1.5rem; margin-bottom: 1.5rem;">
        <div style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 1rem;">
            <div style="display: flex; align-items: center; gap: 1rem;">
                <div class="dash-avatar" style="width: 60px; height: 60px;">
                    @if(Auth::user()->photo)
                        <img src="{{ asset('storage/' . Auth::user()->photo) }}" alt="Avatar" style="width: 100%; height: 100%; object-fit: cover;">
                    @else
                        <i class="fa-solid fa-user" style="font-size: 1.5rem;"></i>
                    @endif
                </div>
                <div>
                    <h2 style="margin: 0; font-size: 1.5rem;">{{ Auth::user()->name }}</h2>
                    <div style="display: flex; align-items: center; gap: 0.75rem; margin-top: 0.25rem;">
                        <span class="badge" style="font-size: 0.65rem; padding: 2px 8px;">{{ strtoupper(Auth::user()->role) }}</span>
                        <span style="color: var(--text-secondary); font-size: 0.8rem; font-weight: 600;">RA: {{ Auth::user()->ra }}</span>
                    </div>
                </div>
            </div>

            @if(Auth::user()->role === 'atirador' || Auth::user()->role === 'monitor')
                <div style="display: flex; gap: 1rem;">
                    <div style="text-align: right; border-right: 1px solid var(--border-color); padding-right: 1rem;">
                        <span style="display: block; font-size: 0.65rem; color: var(--text-secondary); font-weight: 800; text-transform: uppercase;">Faltas</span>
                        <span style="font-size: 1.2rem; font-weight: 800; color: var(--primary-olive-dark);">{{ Auth::user()->faults }}</span>
                    </div>
                    <div style="text-align: right;">
                        <span style="display: block; font-size: 0.65rem; color: var(--text-secondary); font-weight: 800; text-transform: uppercase;">Pontos</span>
                        <span style="font-size: 1.2rem; font-weight: 800; color: {{ Auth::user()->points >= 100 ? '#c53030' : 'var(--primary-olive-dark)' }};">
                            {{ Auth::user()->points }}<small style="font-size: 0.7rem; opacity: 0.5;">/120</small>
                        </span>
                    </div>
                </div>
            @endif
        </div>
    </div>

    <!-- Alerta de Proximidade do Limite -->
    @if(Auth::user()->points >= 100)
        <div class="alert alert-danger" style="margin-bottom: 2rem;">
            <i class="fa-solid fa-circle-exclamation"></i>
            <div>
                <strong>Alerta Crítico:</strong> Você atingiu {{ Auth::user()->points }} pontos. O limite é 120 antes da dispensa.
            </div>
        </div>
    @endif

    <style>
        .carousel-wrapper {
            position: relative;
            margin: 0 -0.5rem;
        }

        .dashboard-carousel {
            display: flex;
            gap: 1.25rem;
            overflow-x: auto;
            padding: 0.5rem 0.5rem 1rem 0.5rem;
            scroll-snap-type: x mandatory;
            scrollbar-width: none;
            -ms-overflow-style: none;
            -webkit-overflow-scrolling: touch;
            scroll-behavior: smooth;
        }

        .dashboard-carousel::-webkit-scrollbar {
            display: none;
        }

        .carousel-item {
            flex: 0 0 310px;
            scroll-snap-align: start;
        }

        .carousel-item-large {
            flex: 0 0 340px;
            scroll-snap-align: start;
        }

        /* Minimalist Carousel Arrows */
        .carousel-nav-btn {
            position: absolute;
            top: 50%;
            transform: translateY(-50%);
            width: 36px;
            height: 36px;
            background: rgba(255, 255, 255, 0.8);
            backdrop-filter: blur(4px);
            border: 1px solid rgba(0, 0, 0, 0.05);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            z-index: 10;
            color: var(--primary-olive);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            box-shadow: 0 2px 8px rgba(0,0,0,0.05);
        }

        .carousel-nav-btn:hover {
            background: var(--primary-olive);
            color: white;
            transform: translateY(-50%) scale(1.1);
            box-shadow: 0 4px 12px rgba(67, 83, 52, 0.2);
        }

        .carousel-nav-prev { left: -5px; }
        .carousel-nav-next { right: -5px; }

        @media (max-width: 768px) {
            .carousel-item, .carousel-item-large {
                flex: 0 0 88%;
            }
            .carousel-nav-btn { display: none; }
        }
    </style>

    <script>
        function scrollCarousel(id, direction) {
            const container = document.getElementById(id);
            const scrollAmount = container.offsetWidth * 0.8;
            container.scrollBy({
                left: direction * scrollAmount,
                behavior: 'smooth'
            });
        }
    </script>

    <!-- Mural de Avisos -->
    <div style="margin-bottom: 3rem;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
            <div style="display: flex; align-items: center; gap: 0.75rem;">
                <h2 style="font-weight: 800; color: var(--primary-olive-dark); margin: 0;">Mural Digital</h2>
                <span style="font-size: 0.7rem; color: var(--text-secondary); background: #f1f5f9; padding: 2px 8px; border-radius: 4px; font-weight: 600;">{{ count($announcements) }} AVISOS</span>
            </div>
            @if(in_array(Auth::user()->role, ['master', 'instructor']))
                <a href="{{ route('avisos.index') }}" class="badge" style="background: var(--primary-olive); color: white; text-decoration: none; padding: 6px 12px;">GERENCIAR</a>
            @endif
        </div>

        <div class="carousel-wrapper">
            <button class="carousel-nav-btn carousel-nav-prev" onclick="scrollCarousel('carousel-avisos', -1)"><i class="fa-solid fa-chevron-left"></i></button>
            <button class="carousel-nav-btn carousel-nav-next" onclick="scrollCarousel('carousel-avisos', 1)"><i class="fa-solid fa-chevron-right"></i></button>
            
            <div class="dashboard-carousel" id="carousel-avisos">
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
                    
                    <div class="carousel-item">
                        <a href="{{ route('avisos.show', $aviso->id) }}" style="text-decoration: none; color: inherit; display: block; height: 100%;">
                            <div class="stat-card" style="border-left: 4px solid {{ $aviso->priority ? '#ef4444' : $c['border'] }}; margin: 0; min-height: 150px; height: 100%; display: flex; flex-direction: column; justify-content: space-between; position: relative; padding: 1rem; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.03);">
                                @if($isNew)
                                    <span style="position: absolute; top: 12px; right: 12px; width: 8px; height: 8px; background: #ef4444; border-radius: 50%; box-shadow: 0 0 0 2px white;"></span>
                                @endif
                                
                                <div>
                                    <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 0.5rem;">
                                        <span style="font-size: 0.6rem; font-weight: 800; color: {{ $c['text'] }}; text-transform: uppercase; letter-spacing: 0.5px; opacity: 0.7;">
                                            {{ $aviso->category }}
                                        </span>
                                    </div>
                                    <h4 style="margin: 0; font-size: 0.95rem; line-height: 1.4; font-weight: 700; color: var(--text-primary);">{{ $aviso->title }}</h4>
                                    <p style="font-size: 0.8rem; color: var(--text-secondary); margin: 0.5rem 0; overflow: hidden; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; line-height: 1.5;">
                                        {{ $aviso->content }}
                                    </p>
                                </div>
                                
                                <div style="display: flex; justify-content: space-between; align-items: center; border-top: 1px solid #f8fafc; padding-top: 8px; margin-top: auto;">
                                    <span style="font-size: 0.7rem; color: #adb5bd; font-weight: 600;">{{ $aviso->created_at->translatedFormat('d M') }}</span>
                                    @if($aviso->attachment)
                                        <i class="fa-solid fa-paperclip" style="color: #dee2e6; font-size: 0.75rem;"></i>
                                    @endif
                                </div>
                            </div>
                        </a>
                    </div>
                @empty
                    <div style="width: 100%; background: #fff; padding: 2rem; border-radius: 12px; border: 1px solid var(--border-color); text-align: center; color: var(--text-secondary);">
                        Sem avisos recentes para a sua turma.
                    </div>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Escalas e Serviços -->
    <div style="margin-bottom: 2rem;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.25rem;">
            <div style="display: flex; align-items: center; gap: 0.75rem;">
                <h2 style="font-weight: 800; color: var(--primary-olive-dark); margin: 0; font-size: 1.25rem;">Escalas de Serviço</h2>
                <span style="font-size: 0.65rem; color: var(--text-secondary); background: #f8fafc; border: 1px solid #f1f5f9; padding: 2px 8px; border-radius: 4px; font-weight: 600; text-transform: uppercase;">Geral</span>
            </div>
        </div>

        <div class="carousel-wrapper">
            <button class="carousel-nav-btn carousel-nav-prev" onclick="scrollCarousel('carousel-escalas', -1)"><i class="fa-solid fa-chevron-left"></i></button>
            <button class="carousel-nav-btn carousel-nav-next" onclick="scrollCarousel('carousel-escalas', 1)"><i class="fa-solid fa-chevron-right"></i></button>

            <div class="dashboard-carousel" id="carousel-escalas">
                @forelse($nextScales as $data => $servicos)
                    @php 
                        $dataCarbon = \Carbon\Carbon::parse($data);
                        $isToday = $dataCarbon->isToday();
                        $isPast = $dataCarbon->isPast() && !$isToday;
                    @endphp
                    <div class="carousel-item-large">
                        <div class="stat-card" style="margin: 0; padding: 0; overflow: hidden; display: flex !important; flex-direction: column !important; align-items: stretch !important; border: 1px solid {{ $isToday ? 'var(--primary-olive)' : 'var(--border-color)' }}; {{ $isToday ? 'box-shadow: 0 4px 20px rgba(67, 83, 52, 0.08);' : 'box-shadow: 0 2px 10px rgba(0,0,0,0.03);' }}; border-radius: 10px; height: 100%; opacity: {{ $isPast ? '0.6' : '1' }}; background: white;">
                            <div style="background: {{ $isToday ? 'var(--primary-olive)' : '#f8fafc' }}; padding: 0.6rem 0.75rem; border-bottom: 1px solid {{ $isToday ? 'var(--primary-olive)' : 'var(--border-color)' }}; display: flex; justify-content: space-between; align-items: center;">
                                <span style="font-weight: 800; color: {{ $isToday ? 'white' : '#64748b' }}; font-size: 0.7rem; text-transform: uppercase; letter-spacing: 0.5px;">
                                    {{ $dataCarbon->translatedFormat('d \d\e M') }} — {{ $dataCarbon->translatedFormat('D') }}
                                </span>
                                @if($isToday)
                                    <span style="background: white; color: var(--primary-olive); font-size: 0.6rem; padding: 1px 6px; border-radius: 4px; font-weight: 900;">HOJE</span>
                                @endif
                            </div>
                            
                            <div style="padding: 0.75rem; display: flex; flex-direction: column; gap: 0.35rem; flex: 1;">
                                @foreach($servicos as $servico)
                                    @php
                                        $isMe = Auth::id() === $servico->user_id;
                                        $isCmt = $servico->funcao == 'comandante';
                                    @endphp
                                    <div style="display: flex; align-items: center; justify-content: space-between; padding: 0.35rem 0.5rem; border-radius: 6px; background: {{ $isMe ? 'var(--primary-olive-light)' : '#fcfcfc' }}; border: 1px solid {{ $isMe ? 'var(--primary-olive)' : '#f1f5f9' }};">
                                        <div style="display: flex; align-items: center; gap: 0.6rem;">
                                            <span style="font-size: 0.6rem; font-weight: 900; color: {{ $isCmt ? '#3b82f6' : '#22c55e' }}; text-transform: uppercase;">
                                                {{ $isCmt ? 'Cmt' : 'Gd' }}
                                            </span>
                                            <span style="font-size: 0.8rem; font-weight: 600; color: {{ $isMe ? 'white' : 'var(--text-primary)' }};">
                                                {{ $servico->user->nome_de_guerra ?? $servico->user->name }}
                                            </span>
                                        </div>
                                        @if($isMe)
                                            <i class="fa-solid fa-star" style="font-size: 0.65rem; color: #fbbf24;"></i>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                            
                            <a href="{{ route('escalas.boletim', $data) }}" style="display: block; padding: 0.4rem; background: #f8fafc; border-top: 1px solid #f1f5f9; text-align: center; font-size: 0.65rem; color: #94a3b8; text-decoration: none; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px;">
                                Detalhes do Boletim
                            </a>
                        </div>
                    </div>
                @empty
                    <div style="width: 100%; background: #fff; padding: 3rem; border-radius: 12px; border: 1px solid var(--border-color); text-align: center; color: var(--text-secondary);">
                        <i class="fa-solid fa-calendar-day" style="font-size: 2rem; opacity: 0.2; margin-bottom: 1rem; display: block;"></i>
                        Nenhuma escala publicada.
                    </div>
                @endforelse
            </div>
        </div>
    </div>
@endsection