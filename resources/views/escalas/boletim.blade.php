@extends('layouts.app')

@section('title', 'Boletim de Serviço — ' . $carbon->format('d/m/Y'))

@section('content')
<div class="dashboard-container" style="max-width: 800px;">
    <div class="header-desktop" style="border-bottom: none; margin-bottom: 2rem;">
        <div>
            <a href="{{ route('escalas.index') }}" style="color: var(--primary-olive); font-size: 0.9rem; font-weight: 600; display: flex; align-items: center; gap: 0.5rem; margin-bottom: 1rem;">
                <i class="fa-solid fa-arrow-left"></i> Voltar ao Painel
            </a>
            <h2 style="margin: 0; color: var(--primary-olive-dark);">Boletim de Serviço</h2>
            <p style="margin: 5px 0 0 0; color: var(--text-secondary);">
                {{ $carbon->translatedFormat('l, d \d\e F \d\e Y') }}
            </p>
        </div>
        
        <div>
            <a href="{{ route('escalas.pdf', $carbon->format('Y-m-d')) }}" class="btn-primary" style="background: #ef4444;">
                <i class="fa-solid fa-file-pdf"></i> Exportar PDF
            </a>
        </div>
    </div>

    <div class="profile-card" style="padding: 3rem;">
        <div class="boletim-header">
            <h3 style="margin: 0; text-transform: uppercase; letter-spacing: 1px;">Escala de Serviço para o dia {{ $carbon->format('d/m/Y') }}</h3>
            <p style="margin: 10px 0 0 0; font-weight: 700; color: var(--primary-olive-dark);">ORDEM DE SERVIÇO Nº ______ / {{ $carbon->year }}</p>
        </div>

        <h4 style="border-left: 4px solid var(--primary-olive); padding-left: 10px; margin: 2rem 0 1rem 0; color: var(--primary-olive-dark);">1. COMANDANTE DA GUARDA</h4>
        <table class="boletim-table">
            <thead>
                <tr>
                    <th style="width: 100px;">Grupo</th>
                    <th style="width: 80px;">Nr</th>
                    <th>Nome de Guerra</th>
                </tr>
            </thead>
            <tbody>
                @forelse($dados['mon']->where('funcao', 'comandante') as $reg)
                    <tr>
                        <td>Monitor</td>
                        <td>{{ $reg->user->numero }}</td>
                        <td style="font-weight: 700;">{{ $reg->user->nome_de_guerra }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="3" style="text-align: center; color: var(--text-secondary);">Nenhum Monitor em serviço hoje.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <h4 style="border-left: 4px solid var(--primary-olive); padding-left: 10px; margin: 2rem 0 1rem 0; color: var(--primary-olive-dark);">2. SENTINELAS (ATIRADORES)</h4>
        <table class="boletim-table">
            <thead>
                <tr>
                    <th style="width: 80px;">Nr</th>
                    <th>Nome de Guerra</th>
                    <th>Posto / Turno</th>
                </tr>
            </thead>
            <tbody>
                @php $count = 1; @endphp
                @forelse($dados['atdr'] as $reg)
                    <tr>
                        <td>{{ $reg->user->numero }}</td>
                        <td style="font-weight: 700;">{{ $reg->user->nome_de_guerra }}</td>
                        <td>{{ $count++ }}º Quarto de Hora</td>
                    </tr>
                @empty
                    @forelse($dados['mon']->where('funcao', 'guarda') as $reg)
                        <tr>
                            <td>{{ $reg->user->numero }}</td>
                            <td style="font-weight: 700;">{{ $reg->user->nome_de_guerra }}</td>
                            <td>Monitor (Reforço)</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" style="text-align: center; color: var(--text-secondary);">Nenhum Atirador em serviço hoje.</td>
                        </tr>
                    @endforelse
                @endforelse
            </tbody>
        </table>

        <div style="margin-top: 4rem; text-align: center;">
            <div style="height: 1px; width: 250px; background: #000; margin: 0 auto 10px auto;"></div>
            <p style="margin: 0; font-weight: 700; text-transform: uppercase; font-size: 0.8rem;">Instrutor de Dia</p>
            <p style="margin: 0; font-size: 0.75rem; color: var(--text-secondary);">Tiro de Guerra 02-033</p>
        </div>
    </div>
</div>
@endsection
