@extends('layouts.app')

@section('title', 'Frequência')

@section('styles')
    <style>
        .frequencia-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 16px;
        }

        .frequencia-header-left h2 {
            font-weight: 800;
            color: var(--primary-olive-dark);
            font-size: 1.5rem;
            margin: 0 0 2px 0;
        }

        .frequencia-header-left p {
            color: var(--text-secondary);
            font-size: 0.9rem;
            margin: 0;
        }

        .turma-selector {
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .turma-selector label {
            font-weight: 600;
            font-size: 0.9rem;
            color: var(--text-secondary);
            text-transform: uppercase;
            letter-spacing: 0.5px;
            white-space: nowrap;
        }

        .turma-select {
            width: 140px;
            padding: 6px 10px;
            background: #fff;
            border: 1px solid var(--border-color);
            color: var(--text-primary);
            border-radius: 8px;
            font-size: 0.9rem;
            font-weight: 500;
            font-family: var(--font-body);
            transition: all 0.2s;
            cursor: pointer;
            appearance: none;
            -webkit-appearance: none;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 12 12'%3E%3Cpath fill='%234a5c48' d='M6 8.825L.575 3.4l.85-.85L6 7.125 10.575 2.55l.85.85z'/%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right 14px center;
            padding-right: 36px;
        }

        .turma-select:focus {
            border-color: var(--primary-olive);
            box-shadow: 0 0 0 3px rgba(74, 92, 72, 0.1);
            outline: none;
        }

        .turma-select:hover {
            border-color: var(--primary-olive-light);
        }

        /* Estilo para linhas clicáveis */
        .frequencia-table-row {
            cursor: pointer;
            transition: background-color 0.15s ease;
        }

        .frequencia-table-row:hover {
            background-color: #f0f4f1 !important;
        }

        .frequencia-table-row-icon {
            display: none;
            margin-right: 0.5rem;
            color: var(--primary-olive);
        }

        .frequencia-table-row:hover .frequencia-table-row-icon {
            display: inline-block;
        }

        /* Conteúdo vazio / placeholder */
        .frequencia-empty {
            background: white;
            border: 1px solid var(--border-color);
            border-radius: 8px;
            padding: 12px;
            text-align: center;
            box-shadow: 0 2px 4px -1px rgba(0, 0, 0, 0.05);
        }

        .frequencia-empty-icon {
            width: 80px;
            height: 80px;
            background: #f0f4f1;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
            font-size: 2rem;
            color: var(--primary-olive);
        }

        .frequencia-empty h3 {
            font-weight: 700;
            color: var(--text-primary);
            font-size: 1.15rem;
            margin: 0 0 8px 0;
        }

        .frequencia-empty p {
            color: var(--text-secondary);
            font-size: 0.9rem;
            margin: 0;
            max-width: 400px;
            margin: 0 auto;
            line-height: 1.5;
        }

        @media (max-width: 1200px) {
            .frequencia-header {
                flex-direction: column;
                align-items: flex-start;
                gap: 8px;
                margin-bottom: 12px;
            }

            .turma-selector {
                width: 100%;
                gap: 4px;
            }

            .turma-select {
                flex: 1;
                min-width: 100px;
                width: auto;
            }

            .frequencia-empty {
                padding: 8px;
            }
        }
    </style>
@endsection

@section('content')
    <div class="frequencia-header">
        <div class="frequencia-header-left">
            <h2>Frequência</h2>
            <p>Gerenciamento de Frequência da {{ $turma == '1' ? '1ª Turma' : '2ª Turma' }}</p>
        </div>

        <div class="turma-selector">
            <form action="{{ route('frequencia.index') }}" method="GET" id="turmaForm"
                style="display:flex;gap:8px;align-items:center;">
                <label for="anoSelect" style="font-weight:600;font-size:0.8rem;color:var(--text-secondary);">Ano:</label>
                <select name="ano" id="anoSelect" class="turma-select" onchange="this.form.submit()">
                    @php
                        $current = date('Y');
                        $start = $current - 4;
                    @endphp
                    @for($y = $current; $y >= $start; $y--)
                        <option value="{{ $y }}" {{ (isset($ano) && $ano == $y) ? 'selected' : '' }}>{{ $y }}</option>
                    @endfor
                </select>

                <label for="turmaSelect">Turma:</label>
                <select name="turma" id="turmaSelect" class="turma-select" onchange="this.form.submit()">
                    <option value="1" {{ $turma == '1' ? 'selected' : '' }}>1ª Turma</option>
                    <option value="2" {{ $turma == '2' ? 'selected' : '' }}>2ª Turma</option>
                </select>
            </form>
        </div>
    </div>

    <div class="frequencia-empty" style="padding:16px;">
        @if(session('success'))
            <div
                style="padding:6px 10px;background:#e6f6ea;border:1px solid #cfe9d0;border-radius:6px;margin-bottom:8px;color:#1a6b2a;font-size:0.75rem;">
                {{ session('success') }}
            </div>
        @endif

        <form method="POST" action="{{ route('frequencia.salvar') }}">
            @csrf
            <input type="hidden" name="turma" value="{{ $turma }}">
            <input type="hidden" name="ano" value="{{ $ano ?? date('Y') }}">
            <div style="overflow:auto;">
                <table class="table" style="width:100%;border-collapse:collapse;min-width:900px;font-size:0.8rem;">
                    <thead>
                        <tr style="background:#f7f9f7;border-bottom:1px solid var(--border-color);">
                            <th
                                style="text-align:center;padding:2px;border-bottom:1px solid var(--border-color);font-weight:600;font-size:0.8rem;">
                                Nr Atdr</th>
                            <th
                                style="text-align:center;padding:2px;border-bottom:1px solid var(--border-color);font-weight:600;font-size:0.8rem;">
                                Nome</th>
                            <th
                                style="text-align:center;padding:2px;border-bottom:1px solid var(--border-color);font-weight:600;font-size:0.8rem;">
                                Horas Serviço</th>
                            <th
                                style="text-align:center;padding:2px;border-bottom:1px solid var(--border-color);font-weight:600;font-size:0.8rem;">
                                Treinamento</th>
                            <th
                                style="text-align:center;padding:2px;border-bottom:1px solid var(--border-color);font-weight:600;font-size:0.8rem;">
                                Marchas e Estac</th>
                            <th
                                style="text-align:center;padding:2px;border-bottom:1px solid var(--border-color);font-weight:600;font-size:0.8rem;">
                                Aç Comunitárias</th>
                            <th
                                style="text-align:center;padding:2px;border-bottom:1px solid var(--border-color);font-weight:600;font-size:0.8rem;">
                                Horas CFC</th>
                            <th
                                style="text-align:center;padding:2px;border-bottom:1px solid var(--border-color);font-weight:600;font-size:0.8rem;">
                                Pontos</th>
                            <th
                                style="text-align:center;padding:2px;border-bottom:1px solid var(--border-color);font-weight:600;font-size:0.8rem;">
                                Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @for($i = $numInicio; $i <= $numFim; $i++)
                            @php
                                $u = $atiradores->get($i);
                            @endphp
                            <tr class="frequencia-table-row" @if($u) data-user-id="{{ $u->id }}" @endif style="border-bottom:1px solid #f1f3f1;height:26px;">
                                <td style="padding:4px 6px;">
                                    <span class="frequencia-table-row-icon" title="Ver frequência individual">
                                        <i class="fa-solid fa-calendar-days"></i>
                                    </span>
                                    {{ $i }}
                                </td>
                                <td
                                    style="padding:4px 6px;max-width:140px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">
                                    {{ $u ? $u->name : '-' }}
                                </td>
                                <td style="padding:2px 4px;text-align:center;">
                                    @if($u)
                                        <input type="number" name="data[{{ $u->id }}][servicos_escala]"
                                            value="{{ old('data.' . $u->id . '.servicos_escala', $u->servicos_escala ?? 0) }}"
                                            class="form-input"
                                            style="width:55px;padding:3px;border:1px solid var(--border-color);border-radius:4px;text-align:center;font-size:0.8rem;">
                                    @else
                                        0
                                    @endif
                                </td>
                                <td style="padding:2px 4px;text-align:center;">
                                    @if($u)
                                        <input type="number" name="data[{{ $u->id }}][treinamentos]"
                                            value="{{ old('data.' . $u->id . '.treinamentos', $u->treinamentos ?? 0) }}"
                                            class="form-input"
                                            style="width:55px;padding:3px;border:1px solid var(--border-color);border-radius:4px;text-align:center;font-size:0.8rem;">
                                    @else
                                        0
                                    @endif
                                </td>
                                <td style="padding:2px 4px;text-align:center;">
                                    @if($u)
                                        <input type="number" name="data[{{ $u->id }}][marchas_estac_eld]"
                                            value="{{ old('data.' . $u->id . '.marchas_estac_eld', $u->marchas_estac_eld ?? 0) }}"
                                            class="form-input"
                                            style="width:55px;padding:3px;border:1px solid var(--border-color);border-radius:4px;text-align:center;font-size:0.8rem;">
                                    @else
                                        0
                                    @endif
                                </td>
                                <td style="padding:2px 4px;text-align:center;">
                                    @if($u)
                                        <input type="number" name="data[{{ $u->id }}][acoes_comunitarias]"
                                            value="{{ old('data.' . $u->id . '.acoes_comunitarias', $u->acoes_comunitarias ?? 0) }}"
                                            class="form-input"
                                            style="width:55px;padding:3px;border:1px solid var(--border-color);border-radius:4px;text-align:center;font-size:0.8rem;">
                                    @else
                                        0
                                    @endif
                                </td>
                                <td style="padding:2px 4px;text-align:center;">
                                    @if($u)
                                        <input type="number" name="data[{{ $u->id }}][tempo_cfc]"
                                            value="{{ old('data.' . $u->id . '.tempo_cfc', $u->tempo_cfc ?? 0) }}"
                                            class="form-input"
                                            style="width:55px;padding:3px;border:1px solid var(--border-color);border-radius:4px;text-align:center;font-size:0.8rem;">
                                    @else
                                        0
                                    @endif
                                </td>
                                <td style="padding:2px 4px;text-align:center;">
                                    @if($u)
                                        <input type="number" name="data[{{ $u->id }}][pontos_perdidos]"
                                            value="{{ old('data.' . $u->id . '.pontos_perdidos', $u->pontos_perdidos ?? 0) }}"
                                            class="form-input"
                                            style="width:55px;padding:3px;border:1px solid var(--border-color);border-radius:4px;text-align:center;font-size:0.8rem;">
                                    @else
                                        0
                                    @endif
                                </td>
                                <td style="padding:2px 4px;">
                                    @if($u)
                                        <input type="text" name="data[{{ $u->id }}][status]"
                                            value="{{ old('data.' . $u->id . '.status', $u->status ?? '') }}" class="form-input"
                                            style="width:110px;padding:3px;border:1px solid var(--border-color);border-radius:4px;font-size:0.8rem;">
                                    @else
                                        -
                                    @endif
                                </td>
                            </tr>
                        @endfor
                    </tbody>
                </table>
            </div>

            <div style="display:flex;justify-content:flex-end;margin-top:8px;gap:6px;">
                <a href="{{ route('frequencia.index', ['turma' => $turma, 'ano' => $ano ?? date('Y')]) }}" class="btn"
                    style="background:#f3f6f3;border:1px solid var(--border-color);padding:6px 10px;border-radius:6px;color:var(--text-primary);text-decoration:none;font-size:0.8rem;">Cancelar</a>
                <button type="submit" class="btn btn-primary"
                    style="background:var(--primary-olive);color:#fff;padding:6px 10px;border-radius:6px;border:none;font-size:0.8rem;">Salvar</button>
            </div>
        </form>
    </div>

<script>
    // Abrir frequência individual ao clicar na linha
    document.querySelectorAll('.frequencia-table-row[data-user-id]').forEach(row => {
        row.addEventListener('click', function(e) {
            // Não abrir se clicou em um input ou botão
            if (e.target.closest('input, button, a')) return;

            const userId = this.getAttribute('data-user-id');
            if (userId) {
                window.location.href = `/frequencia/atirador/${userId}`;
            }
        });
    });
</script>

@endsection