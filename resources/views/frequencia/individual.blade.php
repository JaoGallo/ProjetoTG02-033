@extends('layouts.app')

@section('title', 'Frequência - ' . $user->name)

@section('styles')
    <style>
        .freq-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1rem;
            padding-bottom: 1rem;
            border-bottom: 2px solid var(--border-color);
        }

        .freq-header-left {
            display: flex;
            gap: 1.5rem;
            align-items: flex-start;
        }

        .freq-photo {
            width: 70px;
            height: 90px;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
            flex-shrink: 0;
            background: #f0f4f1;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .freq-photo img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .freq-photo-placeholder {
            width: 100%;
            height: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2rem;
            color: var(--primary-olive-light);
        }

        .freq-info {
            flex: 1;
        }

        .freq-info h2 {
            margin: 0 0 0.5rem 0;
            color: var(--primary-olive-dark);
            font-size: 1.4rem;
            font-weight: 800;
        }

        .freq-info-row {
            display: flex;
            gap: 2rem;
            margin-bottom: 0.5rem;
            font-size: 0.9rem;
        }

        .freq-info-item {
            display: flex;
            gap: 0.5rem;
        }

        .freq-info-label {
            color: var(--text-secondary);
            font-weight: 600;
        }

        .freq-info-value {
            color: var(--text-primary);
            font-weight: 500;
        }

        .freq-back-btn {
            padding: 0.6rem 1.2rem;
            background: var(--primary-olive);
            color: white;
            border: none;
            border-radius: 6px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
        }

        .freq-back-btn:hover {
            background: var(--primary-olive-dark);
        }

        .freq-calendar-section {
            background: white;
            border-radius: 8px;
            border: 1px solid var(--border-color);
            overflow: hidden;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
        }

        .freq-calendar-title {
            padding: 0.6rem 1rem;
            border-bottom: 1px solid var(--border-color);
            background: #f8faf9;
        }

        .freq-calendar-title h3 {
            margin: 0;
            color: var(--primary-olive-dark);
            font-size: 0.95rem;
            font-weight: 700;
        }

        .freq-table-wrapper {
            overflow-x: auto;
        }

        .freq-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 0.75rem;
        }

        .freq-table thead {
            background: #f0f4f1;
            border-bottom: 2px solid var(--border-color);
        }

        .freq-table th {
            padding: 0.3rem;
            text-align: center;
            font-weight: 700;
            color: var(--text-primary);
            border-right: 1px solid #e5e8e6;
            min-width: 28px;
        }

        .freq-table th:first-child {
            text-align: left;
            min-width: 60px;
            background: #e8f0e6;
            font-weight: 800;
            color: var(--primary-olive-dark);
            padding: 0.5rem 0.75rem;
        }

        .freq-table tbody tr {
            border-bottom: 1px solid var(--border-color);
        }

        .freq-table tbody tr:hover {
            background: #f9faf9;
        }

        .freq-table td {
            padding: 0.2rem;
            text-align: center;
            border-right: 1px solid #e5e8e6;
            position: relative;
        }

        .freq-table td:first-child {
            text-align: left;
            font-weight: 600;
            color: var(--primary-olive-dark);
            background: #f8faf9;
            padding: 0.5rem 0.75rem;
            border-right: 2px solid var(--border-color);
        }

        .freq-cell-inputs {
            width: 100%;
            height: 30px;
            display: flex;
            flex-direction: column;
            border: 1px solid #ddd;
            border-radius: 3px;
            background: white;
            overflow: hidden;
        }

        .freq-cell-inputs.weekend {
            background: #fee2e2;
            border-color: #fca5a5;
        }

        .freq-input {
            width: 100%;
            height: 50%;
            border: none;
            background: transparent;
            text-align: center;
            font-size: 0.55rem;
            font-weight: 700;
            text-transform: uppercase;
            padding: 0;
            color: var(--text-primary);
        }

        .freq-input:focus {
            outline: none;
            background: #e8f0e6;
        }

        .freq-input:first-child {
            border-bottom: 1px solid #e5e8e6;
        }



        .freq-totals {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
            padding: 1.5rem;
            background: #f8faf9;
            border-top: 2px solid var(--border-color);
        }

        .freq-total-box {
            background: white;
            padding: 1rem;
            border-radius: 6px;
            border: 1px solid var(--border-color);
            text-align: center;
        }

        .freq-total-box label {
            display: block;
            font-size: 0.75rem;
            color: var(--text-secondary);
            font-weight: 700;
            text-transform: uppercase;
            margin-bottom: 0.5rem;
            letter-spacing: 0.5px;
        }

        .freq-total-box .value {
            font-size: 1.8rem;
            color: var(--primary-olive-dark);
            font-weight: 800;
        }

        .freq-legend {
            display: flex;
            gap: 1rem;
            padding: 0.75rem 1rem;
            background: white;
            border-top: 1px solid var(--border-color);
            font-size: 0.75rem;
            flex-wrap: wrap;
        }

        .freq-legend-item {
            display: flex;
            align-items: center;
            gap: 0.4rem;
        }

        .freq-legend-box {
            width: 22px;
            height: 22px;
            border: 1px solid #ccc;
            border-radius: 3px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.6rem;
            font-weight: 600;
        }

        .freq-legend-box.present {
            background: #dbeafe;
            border-color: #93c5fd;
            color: #1d4ed8;
        }

        .freq-legend-box.absent {
            background: #fecaca;
            border-color: #f87171;
            color: #991b1b;
        }

        .freq-legend-box.weekend {
            background: #fee2e2;
            border-color: #fca5a5;
            color: #991b1b;
        }

        @media (max-width: 1024px) {
            .freq-header {
                flex-direction: column;
                align-items: flex-start;
                gap: 1rem;
            }

            .freq-table {
                font-size: 0.65rem;
            }

            .freq-table th,
            .freq-table td {
                padding: 0.15rem;
                min-width: 20px;
            }

            .freq-cell-inputs {
                height: 26px;
            }

            .freq-input {
                font-size: 0.5rem;
            }
        }

        .legend-text {
            color: var(--text-secondary);
        }
    </style>
@endsection

@section('content')
    <!-- Cabeçalho com Dados do Atirador -->
    <div class="freq-header">
        <a href="{{ route('frequencia.index') }}" class="freq-back-btn">
            <i class="fa-solid fa-arrow-left"></i> Voltar
        </a>

        <div class="freq-header-left">
            <div class="freq-info" style="text-align: right;">
                @php
                    $nome = mb_strtoupper($user->name);
                    $guerra = mb_strtoupper($user->nome_de_guerra ?? '');
                    if ($guerra && str_contains($nome, $guerra)) {
                        $nomeFormatado = str_replace($guerra, '<strong style="color: var(--primary-olive-dark);">' . $guerra . '</strong>', $nome);
                    } else {
                        $nomeFormatado = $nome . ($guerra ? ' <strong style="color: var(--primary-olive-dark);">' . $guerra . '</strong>' : '');
                    }
                    $turmaCalculada = $user->numero < 50 ? 1 : 2;
                @endphp
                <h2 style="font-size: 1.25rem; font-weight: 600; color: #4b5563; margin-bottom: 0.2rem;">
                    {{ str_pad($user->numero, 2, '0', STR_PAD_LEFT) }} - {!! $nomeFormatado !!}
                </h2>

                <div style="font-size: 0.85rem; color: var(--text-secondary); font-weight: 600; margin-bottom: 0.2rem;">
                    RA: {{ $user->ra }}
                </div>

                <div style="font-size: 0.85rem; color: var(--text-secondary); font-weight: 600;">
                    Ano: {{ $ano }} &nbsp;|&nbsp; Turma: {{ $turmaCalculada }}
                </div>
            </div>

            <div class="freq-photo">
                @if($user->photo)
                    <img src="{{ asset('storage/' . $user->photo) }}" alt="Foto do {{ $user->name }}">
                @else
                    <div class="freq-photo-placeholder">
                        <i class="fa-solid fa-user-tie"></i>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Calendário Interativo -->
    <div class="freq-calendar-section">
        <div class="freq-calendar-title">
            <h3><i class="fa-solid fa-calendar-days"></i> Frequência - {{ $ano }}</h3>
        </div>

        <div class="freq-table-wrapper">
            <table class="freq-table">
                <thead>
                    <tr>
                        <th class="total-th">MESES</th>
                        @for($d = 1; $d <= 31; $d++)
                            <th>{{ str_pad($d, 2, '0', STR_PAD_LEFT) }}</th>
                        @endfor
                    </tr>
                </thead>
                <tbody>
                    @foreach($meses as $mes)
                        <tr>
                            <td class="total-cell">{{ $mes['nome'] }}</td>
                            @for($d = 1; $d <= 31; $d++)
                                @php
                                    $dia = collect($mes['dias'])->firstWhere('dia', $d);
                                    if ($dia) {
                                        $isWeekend = in_array($dia['diaSemana'], ['Saturday', 'Sunday']);
                                        $dataAttr = $dia['data'];
                                    } else {
                                        $isWeekend = false;
                                        $dataAttr = null;
                                    }
                                @endphp
                                @if($dia)
                                    <td style="padding: 0;">
                                        <div class="freq-cell-inputs {{ $isWeekend ? 'weekend' : '' }}" data-date="{{ $dataAttr }}">
                                            <input type="text" class="freq-input" maxlength="3" title="Tempo 1">
                                            <input type="text" class="freq-input" maxlength="3" title="Tempo 2">
                                        </div>
                                    </td>
                                @else
                                    <td style="background: #f5f5f5; border: none;"></td>
                                @endif
                            @endfor
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Legenda -->
        <div class="freq-legend" style="flex-direction: column; gap: 0.5rem; align-items: flex-start;">
            <div
                style="font-size: 0.75rem; color: var(--text-secondary); line-height: 1.5; display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 1rem; width: 100%;">
                <div>
                    <strong>FALTAS (1 a 4 tempos):</strong><br> Justificadas: J1, J2, J3 e J4 | Não Justificadas: N1, N2, N3
                    e N4<br>
                    <strong>DISPENSAS:</strong> D1, D2, D3 e D4<br>
                    <strong>ADVERTÊNCIA:</strong> A1 a A10<br>
                    <strong>REPREENSÃO (no dia da publicação em ADT):</strong> R11 a R15
                </div>
                <div>
                    <strong>GUARDAS:</strong> P6 (6 h); P10 (10 h); P12 (12 h); G10 (10 h); G12 (12 h); G22 (22 h), G24 (24
                    h)<br>
                    <strong>EXTRAS:</strong> SE1 (1 h) a SE8 (8 h)<br>
                    <strong>TREINAMENTO:</strong> T1 (2 h), T2 (4 h) (até 64 h = 8 dias)<br>
                    <strong>MARCHAS, ESTACIONAMENTOS E ELD:</strong> ME1 (2 h), ME2 (4 h), ME3 (8 h), ME4 (12 h) e ME5 (24
                    h) (até 72 h = 9 dias)<br>
                    <strong>AÇÕES COMUNITÁRIAS:</strong> AC1 (1 h) a AC8 (8 h)
                </div>
            </div>
        </div>

@endsection