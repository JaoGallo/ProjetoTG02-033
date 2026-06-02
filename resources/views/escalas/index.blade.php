@extends('layouts.app')

@section('title', 'Gestão de Escalas e Serviços')

@section('content')

@php
    $year = now()->year;
    $start = \Carbon\Carbon::create($year, 5, 1);
    $end = \Carbon\Carbon::create($year, 10, 31);
    $days = [];
    for ($d = $start->copy(); $d->lte($end); $d->addDay()) {
        $days[] = $d->copy();
    }
@endphp

<style>
    .tab-btn {
        display:inline-block;
        padding:0.5rem 0.9rem;
        border-radius:8px;
        border:1px solid var(--border-color);
        background:white;
        cursor:pointer;
        font-weight:700;
    }
    .tab-btn + .tab-btn { margin-left:0.4rem; }
    .tab-btn.active {
        background:var(--primary-olive);
        color:white;
        border-color:var(--primary-olive-dark);
    }
    .table-wrapper{overflow-x:auto; border:1px solid var(--border-color); border-radius:8px;}
    .escala-table{border-collapse:collapse; width:100%; min-width: {{ 200 + (count($days) * 48) }}px;}
    .escala-table th, .escala-table td{border-right:1px solid var(--border-color); padding:6px 8px; text-align:center; font-size:0.8rem; min-width:48px}
    .escala-table td.day-cell{min-width:60px; cursor:pointer;}
    .escala-table thead th{position:sticky; top:0; background:var(--bg); z-index:2;}
    .escala-table th:first-child, .escala-table td:first-child{ text-align:left; position:sticky; left:0; background:white; z-index:4; min-width:120px;}
    .escala-table th:nth-child(2), .escala-table td:nth-child(2){ text-align:left; position:sticky; left:120px; background:white; z-index:3; min-width:200px;}
    .escala-table .weekend-col{background:#fee2e2; color:#991b1b;}
    .escala-table .weekday-col.clickable{cursor:pointer;}
    .escala-table .holiday-col{background:#fde68a;}
    .escala-table td.assigned-gd{background:#dbeafe; color:#1d4ed8; font-weight:700;}
    .escala-table td.assigned-cmt{background:#d1fae5; color:#047857; font-weight:700;}

    .header-responsive {
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 16px;
        border-bottom: 1px solid var(--border-color);
        padding-bottom: 1.5rem;
        margin-bottom: 1.5rem;
    }
    .header-responsive h2 {
        margin: 0;
        color: var(--primary-olive-dark);
        font-size: 1.5rem;
    }
    .header-responsive p {
        margin: 5px 0 0 0;
        color: var(--text-secondary);
        font-size: 0.85rem;
    }
    .page-header-actions {
        display: flex;
        justify-content: flex-end;
    }
    .btn-header {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 0.5rem;
        padding: 0.75rem 1rem;
        border-radius: 999px;
        font-size: 0.9rem;
    }
    @media (max-width: 768px) {
        .header-responsive {
            padding-bottom: 1rem;
            margin-bottom: 1rem;
        }
        .page-header-actions {
            width: 100%;
            justify-content: flex-start;
        }
        .btn-header {
            width: 100%;
        }
    }

    .adt-carousel-wrapper {
        padding: 10px 5px 25px 5px;
        margin: 0;
    }

    .adt-carousel {
        display: flex;
        gap: 1.5rem;
        flex-wrap: wrap;
        padding-bottom: 10px;
    }

    .adt-card {
        min-width: 200px;
        height: 180px;
        background: white;
        border: 1px solid var(--border-color);
        border-radius: 16px;
        padding: 1.5rem;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        transition: all 0.3s ease;
        box-shadow: 0 4px 6px rgba(0,0,0,0.02);
        text-decoration: none;
        color: inherit;
        position: relative;
        cursor: pointer;
    }

    .adt-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 15px -3px rgba(0,0,0,0.1);
        border-color: var(--primary-olive);
    }

    .adt-card.create-card {
        background: #f8fafc;
        border: 2px dashed #cbd5e1;
        align-items: center;
        justify-content: center;
        gap: 1rem;
        color: #64748b;
    }

    .adt-card.create-card:hover {
        background: #f1f5f9;
        border-color: var(--primary-olive);
        color: var(--primary-olive);
    }

    .create-icon {
        font-size: 2.5rem;
        opacity: 0.5;
    }

    .adt-card.past {
        opacity: 0.7;
        background: #f1f5f9;
    }

    .adt-info h4 {
        margin: 0;
        font-size: 1.1rem;
        color: var(--primary-olive-dark);
    }

    .adt-info p {
        margin: 5px 0 0 0;
        font-size: 0.85rem;
        color: var(--text-secondary);
        font-weight: 600;
    }

    .adt-actions {
        display: flex;
        gap: 1rem;
        border-top: 1px solid var(--border-color);
        padding-top: 1rem;
    }

    .adt-actions a {
        color: var(--primary-olive);
        font-size: 1.1rem;
        transition: transform 0.2s;
    }

    .adt-actions a:hover {
        transform: scale(1.2);
    }

    .adt-carousel-wrapper::-webkit-scrollbar {
        height: 8px;
    }

    .adt-carousel-wrapper::-webkit-scrollbar-track {
        background: #f1f1f1;
        border-radius: 10px;
    }

    .adt-carousel-wrapper::-webkit-scrollbar-thumb {
        background: #cbd5e1;
        border-radius: 10px;
    }

    .adt-carousel-wrapper::-webkit-scrollbar-thumb:hover {
        background: #94a3b8;
    }

    @media (max-width: 768px) {
        .adt-carousel {
            flex-direction: column;
            padding-bottom: 0;
            gap: 1rem;
        }
        .adt-carousel-wrapper {
            overflow-x: visible;
            padding: 0;
            margin: 0;
        }
        .adt-card {
            width: 100%;
            height: auto;
            min-height: 150px;
        }
        .header-responsive {
            text-align: center;
        }
    }

    .modal-overlay {
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(0, 0, 0, 0.5);
        display: flex;
        justify-content: center;
        align-items: center;
        z-index: 1000;
        backdrop-filter: blur(2px);
    }
    .modal-content {
        background: white;
        border-radius: 12px;
        width: 90%;
        max-width: 500px;
        box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
        animation: modalPop 0.3s ease-out forwards;
    }
    .modal-header {
        padding: 1.25rem 1.5rem;
        border-bottom: 1px solid var(--border-color);
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    .modal-header h3 {
        margin: 0;
        color: var(--primary-olive-dark);
        font-size: 1.2rem;
    }
    .close-modal {
        background: none;
        border: none;
        font-size: 1.2rem;
        color: #64748b;
        cursor: pointer;
        transition: color 0.2s;
    }
    .close-modal:hover {
        color: #ef4444;
    }
    .modal-body {
        padding: 1.5rem;
    }
    .modal-footer {
        padding: 1.25rem 1.5rem;
        border-top: 1px solid var(--border-color);
        background: #f8fafc;
        border-bottom-left-radius: 12px;
        border-bottom-right-radius: 12px;
        display: flex;
        justify-content: flex-end;
        gap: 1rem;
    }
    @keyframes modalPop {
        0% { transform: scale(0.95); opacity: 0; }
        100% { transform: scale(1); opacity: 1; }
    }
</style>

<div class="page-header header-responsive">
    <div>
        <h2>Escalas de Serviço (ADTs)</h2>
        <p>Gerencie os períodos de escala através dos Aditamentos semanais.</p>
    </div>
    @if(in_array(Auth::user()->role, ['master', 'instructor']))
        <div class="page-header-actions">
            <button type="button" class="btn btn-primary" onclick="openCreateAdtModal()">
                <i class="fa-solid fa-calendar-plus" style="margin-right: 0.5rem;"></i>
                Criar Novo Aditamento
            </button>
        </div>
    @endif
</div>

@if(session('success'))
<div class="alert alert-success" style="margin-bottom: 2rem;">
    <i class="fa-solid fa-circle-check"></i>
    {{ session('success') }}
</div>
@endif

<!-- Abas horizontais: Instrutores / Monitores / Atiradores -->

<div class="escalas-tabs-wrapper" style="margin-top:1.5rem;">
    <div class="tabs-top" style="display:flex; justify-content:space-between; align-items:center; margin-bottom:0.75rem; flex-wrap: wrap; gap: 1rem;">
        <div style="display:flex; gap:0.5rem; align-items:center;">
            <button class="tab-btn active" data-tab="aditamentos">Aditamentos</button>
            <button class="tab-btn" data-tab="instrutores">Instrutores</button>
            <button class="tab-btn" data-tab="monitores">Monitores</button>
            <button class="tab-btn" data-tab="atiradores">Atiradores</button>
        </div>
        
        <div class="filter-group" style="display:flex; align-items:center; gap:0.5rem;">
            <label style="font-size: 0.8rem; font-weight: 700; color: var(--text-secondary); text-transform: uppercase; margin:0;">Turma:</label>
            <form id="form-turma" method="GET" action="{{ route('escalas.index') }}" style="margin:0;">
                <input type="hidden" name="tab" id="active-tab-input" value="{{ request('tab', 'aditamentos') }}">
                <select name="turma" id="turma-filter" class="form-select" onchange="document.getElementById('form-turma').submit()" style="padding: 0.4rem 0.8rem; font-size: 0.85rem; border-radius: 6px; border: 1px solid var(--border-color); background: white; outline: none; cursor: pointer; min-width: 100px;">
                    @foreach($turmasDisponiveis ?? [] as $t)
                        <option value="{{ $t }}" {{ $t == $turma ? 'selected' : '' }}>Turma de {{ $t }}</option>
                    @endforeach
                </select>
            </form>
        </div>
    </div>
    <div style="margin-bottom:0.9rem; color:var(--text-secondary); font-size:0.9rem;">
        Finais de semana são destacados em vermelho. Clique nos dias de semana para marcar/desmarcar feriados.
    </div>

    <div class="tabs-content">
        <div id="aditamentos" class="tab-pane active">
            <!-- Grid de ADTs -->
            <div class="adt-carousel-wrapper">
                <div class="adt-carousel" id="adtCarousel">
                    @foreach($adts as $adt)
                    <div class="adt-card {{ $adt->data_fim->isPast() ? 'past' : 'active' }}" @if(in_array(Auth::user()->role, ['master', 'instructor'])) onclick="window.location='{{ route('escalas.edit', $adt->id) }}'" @endif>
                        <div class="adt-status">
                            @if($adt->data_fim->isPast())
                                <span class="badge" style="background: #6b7280;">Finalizado</span>
                            @else
                                <span class="badge" style="background: #10b981;">Em Andamento</span>
                            @endif
                        </div>
                        
                        <div class="adt-info">
                            <h4>{{ $adt->nome }}</h4>
                            <p>{{ $adt->data_inicio->format('d/m') }} — {{ $adt->data_fim->format('d/m') }}</p>
                        </div>

                        <div class="adt-actions">
                            @if(in_array(Auth::user()->role, ['master', 'instructor']))
                                <a href="{{ route('escalas.edit', $adt->id) }}" title="Editar Aditamento" style="color: #3b82f6;" onclick="event.stopPropagation()">
                                    <i class="fa-solid fa-pencil"></i>
                                </a>
                            @endif
                            
                            @if(in_array(Auth::user()->role, ['master', 'instructor']))
                                <form action="{{ route('escalas.destroy', $adt->id) }}" method="POST" style="display:inline;" class="delete-adt-form" onclick="event.stopPropagation()">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" title="Excluir" style="background:none; border:none; color: #ef4444; padding:0; cursor:pointer; font-size:1.1rem; transition: transform 0.2s;">
                                        <i class="fa-solid fa-trash"></i>
                                    </button>
                                </form>
                            @endif

                            <a href="{{ route('escalas.aditamento_pdf', $adt->id) }}" title="Gerar Aditamento PDF" style="color: #10b981;" onclick="event.stopPropagation()">
                                <i class="fa-solid fa-file-pdf"></i>
                            </a>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>

        <div id="instrutores" class="tab-pane" style="display:none;">
            <div class="table-wrapper">
                <table class="escala-table">
                    <thead>
                        <tr>
                            <th>Graduação</th>
                            <th>Nome</th>
                            @foreach($days as $d)
                                <th class="{{ $d->isWeekend() ? 'weekend-col' : 'weekday-col clickable' }}" data-col="{{ $loop->index }}">{{ $d->format('d/m') }}</th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($instrutores as $instr)
                        @php
                            $parts = explode(' ', $instr->name, 2);
                            $grad = $parts[0] ?? '';
                            $nome = $parts[1] ?? $instr->name;
                        @endphp
                        <tr>
                            <td style="white-space:nowrap">{{ $grad }}</td>
                            <td style="white-space:nowrap">{{ $nome }}</td>
                            @foreach($days as $d)
                                <td class="{{ $d->isWeekend() ? 'weekend-col' : '' }}" data-col="{{ $loop->index }}"></td>
                            @endforeach
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <div id="monitores" class="tab-pane" style="display:none;">
            <div class="table-wrapper">
                <table class="escala-table">
                    <thead>
                        <tr>
                            <th>Número</th>
                            <th>Nome</th>
                            @foreach($days as $d)
                                <th class="{{ $d->isWeekend() ? 'weekend-col' : 'weekday-col clickable' }}" data-col="{{ $loop->index }}" data-date="{{ $d->format('Y-m-d') }}">{{ $d->format('d/m') }}</th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($monitores as $m)
                        <tr data-user-id="{{ $m->id }}">
                            <td style="white-space:nowrap">{{ $m->numero }}</td>
                            <td style="white-space:nowrap">{{ $m->name }}</td>
                            @foreach($days as $d)
                                <td class="day-cell assignable {{ $d->isWeekend() ? 'weekend-col' : '' }}" data-col="{{ $loop->index }}" data-date="{{ $d->format('Y-m-d') }}"></td>
                            @endforeach
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <div id="atiradores" class="tab-pane" style="display:none;">
            <div class="table-wrapper">
                <table class="escala-table">
                    <thead>
                        <tr>
                            <th>Número</th>
                            <th>Nome</th>
                            @foreach($days as $d)
                                <th class="{{ $d->isWeekend() ? 'weekend-col' : 'weekday-col clickable' }}" data-col="{{ $loop->index }}" data-date="{{ $d->format('Y-m-d') }}">{{ $d->format('d/m') }}</th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($atiradores as $a)
                        <tr data-user-id="{{ $a->id }}">
                            <td style="white-space:nowrap">{{ $a->numero }}</td>
                            <td style="white-space:nowrap">{{ $a->name }}</td>
                            @foreach($days as $d)
                                <td class="day-cell assignable {{ $d->isWeekend() ? 'weekend-col' : '' }}" data-col="{{ $loop->index }}" data-date="{{ $d->format('Y-m-d') }}"></td>
                            @endforeach
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>



<!-- Modal de Criação Lote de Aditamento -->
<div id="createAdtModal" class="modal-overlay" style="display: none;">
    <div class="modal-content" style="max-width: 800px; max-height: 90vh; overflow-y: auto;">
        <div class="modal-header">
            <h3><i class="fa-solid fa-calendar-plus" style="margin-right: 0.5rem; color: var(--primary-olive);"></i> Criar Novo Aditamento</h3>
            <button type="button" class="close-modal" onclick="closeCreateAdtModal()"><i class="fa-solid fa-xmark"></i></button>
        </div>
        
        <form action="{{ route('escalas.storeLote') }}" method="POST" id="formCreateAdtLote">
            @csrf
            <div class="modal-body" style="display: flex; flex-direction: column; gap: 1.5rem;">
                
                <div style="background: #f8fafc; padding: 1.5rem; border-radius: 8px; border: 1px solid var(--border-color);">
                    <div style="display: flex; gap: 1rem; flex-wrap: wrap;">
                        <div class="input-group" style="flex: 1; min-width: 200px; margin-bottom: 0;">
                            <label style="font-size: 0.8rem; font-weight: 700; color: var(--text-secondary); display: block; margin-bottom: 0.3rem;">Identificação do ADT</label>
                            <div style="display: flex; align-items: center; gap: 0.5rem; background: white; padding: 0 0.75rem; border-radius: 6px; border: 1px solid var(--border-color);">
                                <span style="color: var(--text-secondary); font-weight: 700; font-size: 0.85rem;">Aditamento</span>
                                <input type="text" name="nome_suffix" value="{{ date('W/Y') }}" required placeholder="XX/XXXX" style="background: transparent; border: none; padding: 0.6rem 0; width: 100%; outline: none; font-size: 0.9rem;">
                                <input type="hidden" name="nome" id="lote_full_nome" value="Aditamento {{ date('W/Y') }}">
                            </div>
                        </div>
                    </div>
                    
                    <div style="margin-top: 1.5rem;">
                        <h4 style="font-size: 0.9rem; color: var(--primary-olive-dark); border-bottom: 1px solid var(--border-color); padding-bottom: 0.5rem; margin-bottom: 1rem;">Dias a serem anexados</h4>
                        <p style="font-size: 0.8rem; color: var(--text-secondary); margin-bottom: 1rem;">Selecione abaixo os dias marcados na tabela que você deseja incluir neste aditamento.</p>
                        
                        <div id="modal-days-list" style="display: flex; flex-direction: column; gap: 0.5rem; max-height: 200px; overflow-y: auto; padding-right: 0.5rem;">
                            <!-- Preenchido via JS -->
                        </div>
                    </div>
                </div>

                <div class="adt-paper" style="background: white; border: 1px solid #d1d5db; padding: 1.5rem; border-radius: 8px;">
                    <div class="adt-section" style="margin-bottom: 1.5rem;">
                        <label style="font-weight: 700; font-size: 0.9rem; margin-bottom: 0.5rem; display: block;">2ª PARTE - INSTRUÇÃO</label>
                        <textarea name="part2_instrucao" class="adt-textarea" placeholder="Digite aqui os detalhes da instrução..." style="width: 100%; min-height: 80px; padding: 0.75rem; border: 1px solid #e2e8f0; border-radius: 6px; font-family: monospace; resize: vertical;"></textarea>
                    </div>

                    <div class="adt-section" style="margin-bottom: 1.5rem;">
                        <label style="font-weight: 700; font-size: 0.9rem; margin-bottom: 0.5rem; display: block;">3ª PARTE - ASSUNTOS GERAIS</label>
                        <textarea name="part3_assuntos_gerais" class="adt-textarea" placeholder="Digite aqui os assuntos gerais e administrativos..." style="width: 100%; min-height: 80px; padding: 0.75rem; border: 1px solid #e2e8f0; border-radius: 6px; font-family: monospace; resize: vertical;"></textarea>
                    </div>

                    <div class="adt-section">
                        <label style="font-weight: 700; font-size: 0.9rem; margin-bottom: 0.5rem; display: block;">4ª PARTE - JUSTIÇA E DISCIPLINA</label>
                        <textarea name="part4_justica_disciplina" class="adt-textarea" placeholder="Digite aqui os assuntos de justiça e disciplina..." style="width: 100%; min-height: 80px; padding: 0.75rem; border: 1px solid #e2e8f0; border-radius: 6px; font-family: monospace; resize: vertical;"></textarea>
                    </div>
                </div>
            </div>
            
            <input type="hidden" name="lote_dados" id="lote_dados_input">

            <div class="modal-footer" style="margin-top: 1.5rem; display: flex; justify-content: flex-end; gap: 1rem;">
                <button type="button" class="btn btn-secondary" onclick="closeCreateAdtModal()">Cancelar</button>
                <button type="submit" class="btn btn-primary" id="btnSubmitLote">Gerar Aditamento</button>
            </div>
        </form>
    </div>
</div>




<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const carousel = document.getElementById('adtCarousel');
    // Não é mais carrossel horizontal, agora é grid normal na aba


    const deleteForms = document.querySelectorAll('.delete-adt-form');
    deleteForms.forEach(form => {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            Swal.fire({
                title: 'Atenção!',
                text: 'Tem certeza que deseja excluir este aditamento? Todas as escalas e o histórico deste período serão apagados permanentemente.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ef4444',
                cancelButtonColor: '#6b7280',
                confirmButtonText: 'Sim, excluir',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                }
            });
        });
    });
});
</script>

<script>
document.addEventListener('DOMContentLoaded', function(){
    const tabs = document.querySelectorAll('.tab-btn');
    const tabInput = document.getElementById('active-tab-input');
    
    // Restaura a aba ativa após o reload
    if (tabInput) {
        const initialTab = tabInput.value || 'aditamentos';
        const initialBtn = document.querySelector(`.tab-btn[data-tab="${initialTab}"]`);
        if(initialBtn) {
            document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
            initialBtn.classList.add('active');
            document.querySelectorAll('.tab-pane').forEach(p => { 
                p.style.display = (p.id === initialTab) ? '' : 'none'; 
                p.classList.toggle('active', p.id === initialTab); 
            });
        }
    }

    tabs.forEach(btn => btn.addEventListener('click', function(){
        const target = this.getAttribute('data-tab');
        document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
        this.classList.add('active');
        document.querySelectorAll('.tab-pane').forEach(p => { 
            p.style.display = (p.id === target) ? '' : 'none'; 
            p.classList.toggle('active', p.id === target); 
        });
        
        // Salva a aba selecionada no input escondido do form
        if (tabInput) {
            tabInput.value = target;
        }
    }));

    document.querySelectorAll('.escala-table thead th.clickable').forEach(th => {
        th.addEventListener('click', function() {
            const col = this.dataset.col;
            const isHoliday = this.classList.toggle('holiday-col');
            document.querySelectorAll(`.escala-table td[data-col="${col}"]`).forEach(td => td.classList.toggle('holiday-col', isHoliday));
        });
    });

    function refreshTableCounters(table) {
        table.querySelectorAll('tbody tr').forEach(row => {
            let count = 50;
            row.querySelectorAll('td.day-cell').forEach(cell => {
                if (cell.classList.contains('assigned-gd')) {
                    cell.textContent = 'Gd';
                    count = 1;
                } else if (cell.classList.contains('assigned-cmt')) {
                    cell.textContent = 'Cmt';
                    count = 1;
                } else {
                    cell.textContent = count;
                    count += 1;
                }
            });
        });
    }

    document.querySelectorAll('.escala-table').forEach(table => refreshTableCounters(table));

    document.querySelectorAll('.escala-table td.assignable').forEach(cell => {
        cell.addEventListener('click', function() {
            const isGd = this.classList.contains('assigned-gd');
            const isCmt = this.classList.contains('assigned-cmt');
            this.classList.remove('assigned-gd', 'assigned-cmt');
            if (!isGd && !isCmt) {
                this.classList.add('assigned-gd');
            } else if (isGd) {
                this.classList.add('assigned-cmt');
            }
            const table = this.closest('table');
            if (table) {
                refreshTableCounters(table);
            }
        });
    });

    // Lógica Modal Lote Aditamento
    const suffixInput = document.querySelector('input[name="nome_suffix"]');
    const fullNomeInput = document.getElementById('lote_full_nome');
    if (suffixInput) {
        suffixInput.addEventListener('input', function () {
            fullNomeInput.value = "Aditamento " + this.value;
        });
    }

    window.openCreateAdtModal = function() {
        // Coleta todos os dias que tem alguém marcado
        const diasMarcados = {};

        document.querySelectorAll('.escala-table tbody tr').forEach(tr => {
            const userId = tr.getAttribute('data-user-id');
            if (!userId) return;

            tr.querySelectorAll('td.assignable').forEach(td => {
                if (td.classList.contains('assigned-gd') || td.classList.contains('assigned-cmt')) {
                    const data = td.getAttribute('data-date');
                    if (!diasMarcados[data]) {
                        diasMarcados[data] = { dateStr: data, dateFormatted: '', cmt: [], gd: [] };
                        // Get the formatted date from the TH
                        const th = td.closest('table').querySelector(`th[data-date="${data}"]`);
                        if (th) diasMarcados[data].dateFormatted = th.textContent;
                    }

                    if (td.classList.contains('assigned-cmt')) {
                        diasMarcados[data].cmt.push(userId);
                    } else if (td.classList.contains('assigned-gd')) {
                        diasMarcados[data].gd.push(userId);
                    }
                }
            });
        });

        const diasArray = Object.values(diasMarcados).sort((a, b) => a.dateStr.localeCompare(b.dateStr));
        const listContainer = document.getElementById('modal-days-list');
        listContainer.innerHTML = '';

        if (diasArray.length === 0) {
            listContainer.innerHTML = '<div style="color: #ef4444; font-size: 0.9rem;"><i class="fa-solid fa-triangle-exclamation"></i> Você não marcou nenhum serviço na tabela. Marque as sentinelas e comandantes antes de gerar o aditamento.</div>';
            document.getElementById('btnSubmitLote').disabled = true;
        } else {
            document.getElementById('btnSubmitLote').disabled = false;
            diasArray.forEach(dia => {
                const checked = dia.gd.length >= 1 || dia.cmt.length >= 1 ? 'checked' : '';
                const html = `
                    <label style="display: flex; align-items: center; gap: 0.75rem; padding: 0.75rem; background: white; border: 1px solid var(--border-color); border-radius: 6px; cursor: pointer; transition: all 0.2s;">
                        <input type="checkbox" name="dias_selecionados[]" value="${dia.dateStr}" ${checked} class="dia-checkbox" style="width: 18px; height: 18px; accent-color: var(--primary-olive);">
                        <div style="flex: 1; display: flex; justify-content: space-between; align-items: center;">
                            <span style="font-weight: 700; font-size: 0.95rem; color: var(--primary-olive-dark);">Dia ${dia.dateFormatted}</span>
                            <div style="display: flex; gap: 1rem; font-size: 0.8rem;">
                                <span style="background: #d1fae5; color: #047857; padding: 2px 6px; border-radius: 4px; font-weight: 700;">${dia.cmt.length} Cmt</span>
                                <span style="background: #dbeafe; color: #1d4ed8; padding: 2px 6px; border-radius: 4px; font-weight: 700;">${dia.gd.length} Gd</span>
                            </div>
                        </div>
                    </label>
                `;
                listContainer.insertAdjacentHTML('beforeend', html);
            });
        }

        // Armazenar os dados brutos em um input hidden JSON para o form
        document.getElementById('lote_dados_input').value = JSON.stringify(diasMarcados);
        document.getElementById('createAdtModal').style.display = 'flex';
    };

    window.closeCreateAdtModal = function() {
        document.getElementById('createAdtModal').style.display = 'none';
    };

    document.getElementById('formCreateAdtLote').addEventListener('submit', function(e) {
        // Atualizar o JSON apenas com os dias que estão com checkbox selecionado
        const diasMarcados = JSON.parse(document.getElementById('lote_dados_input').value);
        const diasSelecionados = Array.from(document.querySelectorAll('.dia-checkbox:checked')).map(cb => cb.value);
        
        const loteFinal = {};
        diasSelecionados.forEach(d => {
            if (diasMarcados[d]) {
                loteFinal[d] = diasMarcados[d];
            }
        });

        if (Object.keys(loteFinal).length === 0) {
            e.preventDefault();
            Swal.fire('Atenção', 'Selecione pelo menos um dia para gerar o aditamento.', 'warning');
            return;
        }

        document.getElementById('lote_dados_input').value = JSON.stringify(loteFinal);
    });
});
</script>



@endsection
