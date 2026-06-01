@extends('layouts.app')

@section('title', 'Gestão de Escalas e Serviços')

@section('content')
<div class="page-header header-responsive">
    <div>
        <h2>Escalas de Serviço (ADTs)</h2>
        <p>Gerencie os períodos de escala através dos Aditamentos semanais.</p>
    </div>
    @if(in_array(Auth::user()->role, ['master', 'instructor']))
        <div class="page-header-actions">
            <a href="{{ route('escalas.criar') }}" class="btn btn-primary">
                <i class="fa-solid fa-calendar-plus" style="margin-right: 0.5rem;"></i>
                Criar Novo Aditamento
            </a>
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
@php
    $year = now()->year;
    $start = \Carbon\Carbon::create($year, 5, 1);
    $end = \Carbon\Carbon::create($year, 10, 31);
    $days = [];
    for ($d = $start->copy(); $d->lte($end); $d->addDay()) {
        $days[] = $d->copy();
    }
@endphp

<div class="escalas-tabs-wrapper" style="margin-top:1.5rem;">
    <div class="tabs-top" style="display:flex; gap:0.5rem; align-items:center; margin-bottom:0.75rem;">
        <button class="tab-btn active" data-tab="instrutores">Instrutores</button>
        <button class="tab-btn" data-tab="monitores">Monitores</button>
        <button class="tab-btn" data-tab="atiradores">Atiradores</button>
    </div>
    <div style="margin-bottom:0.9rem; color:var(--text-secondary); font-size:0.9rem;">
        Finais de semana são destacados em vermelho. Clique nos dias de semana para marcar/desmarcar feriados.
    </div>

    <div class="tabs-content">
        <div id="instrutores" class="tab-pane active">
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
                                <th class="{{ $d->isWeekend() ? 'weekend-col' : 'weekday-col clickable' }}" data-col="{{ $loop->index }}">{{ $d->format('d/m') }}</th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($monitores as $m)
                        <tr>
                            <td style="white-space:nowrap">{{ $m->numero }}</td>
                            <td style="white-space:nowrap">{{ $m->name }}</td>
                            @foreach($days as $d)
                                <td class="day-cell assignable {{ $d->isWeekend() ? 'weekend-col' : '' }}" data-col="{{ $loop->index }}"></td>
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
                                <th class="{{ $d->isWeekend() ? 'weekend-col' : 'weekday-col clickable' }}" data-col="{{ $loop->index }}">{{ $d->format('d/m') }}</th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($atiradores as $a)
                        <tr>
                            <td style="white-space:nowrap">{{ $a->numero }}</td>
                            <td style="white-space:nowrap">{{ $a->name }}</td>
                            @foreach($days as $d)
                                <td class="day-cell assignable {{ $d->isWeekend() ? 'weekend-col' : '' }}" data-col="{{ $loop->index }}"></td>
                            @endforeach
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

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
</style>

<!-- Carrossel de ADTs -->
<div class="adt-carousel-wrapper">
    <div class="adt-carousel" id="adtCarousel">
        @foreach($adts as $adt)
        <div class="adt-card {{ $adt->data_fim->isPast() ? 'past' : 'active' }}">
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
                    <a href="{{ route('escalas.edit', $adt->id) }}" title="Editar Aditamento" style="color: #3b82f6;">
                        <i class="fa-solid fa-pencil"></i>
                    </a>
                @endif
                
                @if(in_array(Auth::user()->role, ['master', 'instructor']))
                    <form action="{{ route('escalas.destroy', $adt->id) }}" method="POST" style="display:inline;" class="delete-adt-form">
                        @csrf
                        @method('DELETE')
                        <button type="submit" title="Excluir" style="background:none; border:none; color: #ef4444; padding:0; cursor:pointer; font-size:1.1rem; transition: transform 0.2s;">
                            <i class="fa-solid fa-trash"></i>
                        </button>
                    </form>
                @endif

                <a href="{{ route('escalas.aditamento_pdf', $adt->id) }}" title="Gerar Aditamento PDF" style="color: #10b981;">
                    <i class="fa-solid fa-file-pdf"></i>
                </a>
            </div>
        </div>
        @endforeach
    </div>
</div>

<style>
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
    overflow-x: auto;
    padding: 10px 5px 25px 5px;
    margin: 0 -10px;
    scroll-behavior: smooth;
    -webkit-overflow-scrolling: touch;
}

.adt-carousel {
    display: flex;
    gap: 1.5rem;
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
</style>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const carousel = document.getElementById('adtCarousel');
    // Faz o carrossel carregar no final (mais recentes)
    if(window.innerWidth > 768) {
        carousel.parentElement.scrollLeft = carousel.scrollWidth;
    }

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
    tabs.forEach(btn => btn.addEventListener('click', function(){
        const target = this.getAttribute('data-tab');
        document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
        this.classList.add('active');
        document.querySelectorAll('.tab-pane').forEach(p => { p.style.display = (p.id === target) ? '' : 'none'; p.classList.toggle('active', p.id === target); });
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
});
</script>

@endsection
