@extends('layouts.app')

@section('title', 'Matriz de Escala — Grupo ' . $grupo)

@section('styles')
<style>
    .matrix-nav {
        display: flex;
        justify-content: space-between;
        align-items: center;
        background: #fff;
        padding: 1rem 1.5rem;
        border-radius: 12px;
        margin-bottom: 1.5rem;
        border: 1px solid var(--border-color);
    }

    .matrix-cell {
        cursor: pointer;
        transition: filter 0.2s;
        position: relative;
    }

    .matrix-cell:hover {
        filter: brightness(0.9);
    }

    .legend-container {
        display: flex;
        flex-wrap: wrap;
        gap: 1.5rem;
        margin-top: 1.5rem;
        padding: 1rem;
        background: #fff;
        border-radius: 12px;
        border: 1px solid var(--border-color);
        font-size: 0.8rem;
    }

    .legend-item {
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .legend-box {
        width: 16px;
        height: 16px;
        border-radius: 4px;
        border: 1px solid rgba(0,0,0,0.1);
    }
</style>
@endsection

@section('content')
<div class="header-desktop" style="border-bottom: none; margin-bottom: 0;">
    <div>
        <a href="{{ route('escalas.index') }}" style="color: var(--primary-olive); font-size: 0.9rem; font-weight: 600; display: flex; align-items: center; gap: 0.5rem; margin-bottom: 1rem;">
            <i class="fa-solid fa-arrow-left"></i> Painel de Escalas
        </a>
        <h2 style="margin: 0; color: var(--primary-olive-dark);">Escala de {{ $grupo === 'Mon' ? 'Monitores' : 'Atiradores' }}</h2>
        <p style="margin: 5px 0 0 0; color: var(--text-secondary);">Matriz de rotação e serviços.</p>
    </div>
    
    <div style="display: flex; gap: 0.75rem;">
        <a href="{{ route('escalas.configurar', $grupo) }}" class="btn-primary" style="background: var(--text-secondary);">
            <i class="fa-solid fa-gear"></i> Reconfigurar
        </a>
    </div>
</div>

<div class="matrix-nav" style="margin-top: 1.5rem;">
    <div style="display: flex; gap: 0.5rem;">
        @if($canPrev)
            <a href="?inicio={{ $prevInicio->format('Y-m-d') }}" class="btn-primary" style="padding: 0.5rem 1rem; padding-top: 0.75rem;">
                <i class="fa-solid fa-chevron-left"></i> Anterior
            </a>
        @else
            <button class="btn-primary" disabled style="opacity: 0.3; padding: 0.5rem 1rem; padding-top: 0.75rem;">
                <i class="fa-solid fa-chevron-left"></i>
            </button>
        @endif

        @if($canNext)
            <a href="?inicio={{ $nextInicio->format('Y-m-d') }}" class="btn-primary" style="padding: 0.5rem 1rem; padding-top: 0.75rem;">
                Próximo <i class="fa-solid fa-chevron-right"></i>
            </a>
        @else
            <button class="btn-primary" disabled style="opacity: 0.3; padding: 0.5rem 1rem; padding-top: 0.75rem;">
                <i class="fa-solid fa-chevron-right"></i>
            </button>
        @endif
    </div>

    <div style="font-weight: 700; color: var(--primary-olive-dark);">
        {{ $inicio->format('d/m') }} — {{ $fim->format('d/m') }} ({{ $inicio->year }})
    </div>

    <div>
        <span style="font-size: 0.8rem; color: var(--text-secondary);">Gerada em: {{ $config->gerada_em?->format('d/m/Y H:i') ?? 'Nunca' }}</span>
    </div>
</div>

@if(session('success'))
<div class="alert alert-success">
    <i class="fa-solid fa-circle-check"></i>
    {{ session('success') }}
</div>
@endif

<div class="matrix-container">
    <table class="matrix-table">
        <thead>
            <tr>
                <th>Nr</th>
                <th>Nome de Guerra</th>
                @foreach($datas as $data)
                    <th style="{{ $data->isToday() ? 'border: 2px solid var(--primary-olive); background: #edf2ef;' : '' }}">
                        <a href="{{ route('escalas.boletim', $data->format('Y-m-d')) }}" style="color: inherit; display: block;">
                            <div style="font-size: 0.7rem; opacity: 0.6; text-transform: uppercase;">{{ $data->translatedFormat('D') }}</div>
                            <div style="font-size: 1rem;">{{ $data->format('d/m') }}</div>
                        </a>
                    </th>
                @endforeach
            </tr>
            <tr class="row-totalizer">
                <td colspan="2" style="text-align: right; padding-right: 1.5rem;">Qnt em Serviço:</td>
                @foreach($datas as $data)
                    <td>{{ $totaisDia[$data->toDateString()] ?? 0 }}</td>
                @endforeach
            </tr>
        </thead>
        <tbody>
            @foreach($integrantes as $user)
                <tr>
                    <td>{{ $user->numero }}</td>
                    <td>{{ $user->nome_de_guerra }}</td>
                    @foreach($datas as $data)
                        @php
                            $dataStr = $data->toDateString();
                            $key = $user->id . '_' . $dataStr;
                            $reg = $registros[$key][0] ?? null;
                            $classe = $reg ? $reg->cor_css : '';
                            $valor = $reg ? $reg->valor : '';
                            
                            // Sobrepõe feriado se houver
                            if (isset($feriados[$dataStr]) && (!$reg || $reg->funcao === 'feriado')) {
                                $classe = 'cell-feriado';
                                $valor = 'F';
                            }
                        @endphp
                        <td class="matrix-cell {{ $classe }}" 
                            title="{{ isset($feriados[$dataStr]) ? $feriados[$dataStr] : '' }}"
                            onclick="openSwapModal('{{ $dataStr }}', '{{ $user->id }}', '{{ $user->nome_de_guerra }}', '{{ $valor }}', '{{ $reg ? $reg->funcao : '' }}')">
                            {{ $valor }}
                        </td>
                    @endforeach
                </tr>
            @endforeach
        </tbody>
    </table>
</div>

<div class="legend-container">
    <div class="legend-item"><div class="legend-box cell-gd"></div> Sentinela (Gd)</div>
    <div class="legend-item"><div class="legend-box cell-cmt"></div> Comandante (Cmt)</div>
    <div class="legend-item"><div class="legend-box cell-proximo"></div> Próximo (Fila 1)</div>
    <div class="legend-item"><div class="legend-box cell-inicial"></div> Adaptação</div>
    <div class="legend-item"><div class="legend-box cell-feriado"></div> Feriado / Congelado</div>
    <div class="legend-item"><em>* Clique em uma célula para trocar serviço.</em></div>
</div>

<!-- Modal de Troca -->
<div class="modal-overlay" id="swapModal">
    <div class="modal-card">
        <div class="modal-header">
            <h3 style="margin: 0; color: var(--primary-olive-dark);">Troca Manual de Serviço</h3>
            <button class="modal-close" onclick="closeSwapModal()">&times;</button>
        </div>
        
        <form action="{{ route('escalas.swap') }}" method="POST">
            @csrf
            <input type="hidden" name="data" id="modalData">
            <input type="hidden" name="integrante_origem_id" id="modalOrigemId">
            
            <div style="margin-bottom: 1.5rem; background: #f8faf9; padding: 1rem; border-radius: 8px; border: 1px solid var(--border-color);">
                <div style="font-size: 0.8rem; color: var(--text-secondary); text-transform: uppercase; margin-bottom: 5px;">Data e Integrante</div>
                <div style="font-weight: 700; font-size: 1.1rem; color: var(--primary-olive-dark);">
                    <span id="modalDataDisplay"></span> — <span id="modalNomeDisplay"></span>
                </div>
                <div id="modalStatusBadge" style="margin-top: 5px;"></div>
            </div>

            <div class="input-group">
                <label>Trocar serviço com:</label>
                <select name="integrante_destino_id" class="w-full" style="padding: 0.75rem; border: 1px solid var(--border-color); border-radius: 4px; background: white;" required>
                    <option value="">Selecione o substituto...</option>
                    @foreach($integrantes as $dest)
                        <option value="{{ $dest->id }}">Nr {{ $dest->numero }} — {{ $dest->nome_de_guerra }}</option>
                    @endforeach
                </select>
            </div>

            <div class="input-group" style="margin-top: 1rem;">
                <label>Motivo da Troca</label>
                <textarea name="motivo" rows="3" class="w-full" placeholder="Ex: Motivo médico, Permuta..." style="padding: 0.75rem; border: 1px solid var(--border-color); border-radius: 4px; background: white; font-family: inherit; resize: none;"></textarea>
            </div>

            <div style="margin-top: 2rem; display: flex; gap: 1rem;">
                <button type="button" class="btn-primary" style="flex: 1; background: var(--text-secondary);" onclick="closeSwapModal()">Cancelar</button>
                <button type="submit" class="btn-primary" style="flex: 2;">Confirmar Troca</button>
            </div>
        </form>
    </div>
</div>

@endsection

@section('scripts')
<script>
    function openSwapModal(data, userId, nome, valor, funcao) {
        // Apenas permitir troca se estiver de serviço (Gd ou Cmt)
        // Ou se for necessário trocar posição na fila? 
        // O spec foca em escala de serviço. Vamos permitir trocar qualquer um.
        
        document.getElementById('modalData').value = data;
        document.getElementById('modalOrigemId').value = userId;
        
        // Formatar data
        const dateObj = new Date(data + 'T12:00:00');
        document.getElementById('modalDataDisplay').innerText = dateObj.toLocaleDateString('pt-BR');
        document.getElementById('modalNomeDisplay').innerText = nome;
        
        const badge = document.getElementById('modalStatusBadge');
        if (valor === 'Gd') {
            badge.innerHTML = '<span class="badge" style="background: #ef4444;">Guarda (Sentinela)</span>';
        } else if (valor === 'Cmt') {
            badge.innerHTML = '<span class="badge" style="background: #ea580c;">Comandante</span>';
        } else {
            badge.innerHTML = '<span class="badge" style="background: #94a3b8;">Status: ' + valor + '</span>';
        }

        document.getElementById('swapModal').classList.add('active');
    }

    function closeSwapModal() {
        document.getElementById('swapModal').classList.remove('active');
    }

    // Fechar modal ao clicar fora
    window.onclick = function(event) {
        const modal = document.getElementById('swapModal');
        if (event.target == modal) {
            closeSwapModal();
        }
    }
</script>
@endsection
