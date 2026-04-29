@extends('layouts.app')

@section('title', 'Configurar Escala — ' . ($grupo === 'Mon' ? 'Monitores' : 'Atiradores'))

@section('content')
<div class="dashboard-container" style="max-width: 900px;">
    <div class="header-desktop" style="border-bottom: none; margin-bottom: 1rem;">
        <div>
            <a href="{{ route('escalas.index') }}" style="color: var(--primary-olive); font-size: 0.9rem; font-weight: 600; display: flex; align-items: center; gap: 0.5rem; margin-bottom: 1rem;">
                <i class="fa-solid fa-arrow-left"></i> Voltar ao Painel
            </a>
            <h2 style="margin: 0; color: var(--primary-olive-dark);">Configuração: Grupo {{ $grupo }}</h2>
        </div>
    </div>

    @if($errors->any())
    <div class="alert alert-danger">
        <ul>
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <div class="profile-card" style="margin-bottom: 2rem;">
        <form action="{{ route('escalas.salvarConfig', $grupo) }}" method="POST">
            @csrf
            
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem; margin-bottom: 2rem;">
                <div class="input-group">
                    <label>Data de Início</label>
                    <input type="date" name="data_inicio" value="{{ old('data_inicio', $config ? $config->data_inicio->format('Y-m-d') : date('Y-05-01')) }}" required>
                    <small style="color: var(--text-secondary); margin-top: 5px;">Data em que o ano de instrução começa.</small>
                </div>

                <div class="input-group">
                    <label>Data de Término (Sugestão: 6 meses)</label>
                    <input type="date" name="data_fim" value="{{ old('data_fim', $config ? $config->data_fim->format('Y-m-d') : date('Y-11-01')) }}" required>
                </div>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem; margin-bottom: 2rem; padding-top: 1.5rem; border-top: 1px solid var(--border-color);">
                <div class="input-group">
                    <label>Quantidade de Cmt Gd por dia</label>
                    <input type="number" name="qnt_cmt_dia" value="{{ old('qnt_cmt_dia', $config ? $config->qnt_cmt_dia : 1) }}" min="1" required>
                </div>

                <div class="input-group">
                    <label>Quantidade de Sentinelas (Gd) por dia</label>
                    <input type="number" name="qnt_gd_dia" value="{{ old('qnt_gd_dia', $config ? $config->qnt_gd_dia : ($grupo === 'Mon' ? 6 : 8)) }}" min="1" required>
                </div>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem; margin-bottom: 2rem; padding-top: 1.5rem; border-top: 1px solid var(--border-color);">
                <div class="input-group">
                    <label>Dias Iniciais (Adaptação)</label>
                    <input type="number" name="dias_iniciais" value="{{ old('dias_iniciais', $config ? $config->dias_iniciais : ($grupo === 'Mon' ? 4 : 8)) }}" min="0" required>
                    <small style="color: var(--text-secondary); margin-top: 5px;">Dias sem rotação (valor 50, 51...).</small>
                </div>

                <div class="input-group">
                    <label>Valor Inicial</label>
                    <input type="number" name="valor_inicial" value="{{ old('valor_inicial', $config ? $config->valor_inicial : 50) }}" min="0" required>
                </div>
            </div>

            <div style="background: #fff8eb; border: 1px solid #ffe7bd; padding: 1rem; border-radius: 8px; margin-bottom: 2rem; display: flex; gap: 1rem; align-items: flex-start;">
                <i class="fa-solid fa-triangle-exclamation" style="color: #ea580c; margin-top: 3px;"></i>
                <div>
                    <strong style="color: #9a3412; display: block; margin-bottom: 5px;">Atenção!</strong>
                    <p style="margin: 0; font-size: 0.85rem; color: #9a3412;">
                        Salvar esta configuração irá <strong>REGENERAR</strong> toda a escala para o período informado. 
                        Todos os registros manuais de serviço no período serão substituídos pelo algoritmo de rotação circular.
                    </p>
                </div>
            </div>

            <button type="submit" class="btn-primary w-full" style="padding: 1.25rem;">
                <i class="fa-solid fa-rotate"></i> Salvar e Gerar Escala
            </button>
        </form>
    </div>

    <!-- Integrantes do Grupo -->
    <div class="header-desktop" style="border-bottom: none; margin-bottom: 1rem;">
        <h3 style="margin: 0; color: var(--primary-olive-dark);">Integrantes Ativos ({{ $integrantes->count() }})</h3>
    </div>
    <div class="matrix-container">
        <table class="matrix-table">
            <thead>
                <tr>
                    <th style="width: 80px;">Nr</th>
                    <th style="text-align: left;">Nome de Guerra</th>
                    <th>Turma</th>
                    <th>Sub-Grupo</th>
                </tr>
            </thead>
            <tbody>
                @foreach($integrantes as $user)
                <tr>
                    <td>{{ $user->numero }}</td>
                    <td style="text-align: left;">{{ $user->nome_de_guerra }}</td>
                    <td>{{ $user->turma }}</td>
                    <td>
                        @if($user->is_cfc)
                            <span class="badge" style="background: var(--primary-olive);">Monitor</span>
                        @else
                            <span class="badge" style="background: var(--text-secondary);">Atirador</span>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
