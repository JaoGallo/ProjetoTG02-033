@extends('layouts.app')

@section('title', 'Edição de Aditamento')

@section('content')
<div class="dashboard-container">
    @if($errors->any())
    <div class="alert alert-danger" style="margin-bottom: 1.5rem; border-radius: 12px; border-left: 5px solid #ef4444;">
        <ul style="margin: 0; padding-left: 1.5rem; font-weight: 600; font-size: 0.9rem;">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif
    @if(session('success'))
    <div class="alert alert-success" style="margin-bottom: 1.5rem; border-radius: 12px; border-left: 5px solid #10b981;">
        <i class="fa-solid fa-circle-check"></i> {{ session('success') }}
    </div>
    @endif

    <div class="page-header header-responsive">
        <div class="header-titles">
            <a href="{{ route('escalas.index') }}" class="back-link">
                <i class="fa-solid fa-arrow-left"></i> Voltar
            </a>
            <h2>{{ $config->nome }}</h2>
            <p>Selecione um dia abaixo para editar os militares escalados.</p>
        </div>
    </div>

    <!-- Abas dos Dias -->
    <div class="adt-tabs-container">
        @foreach($diasAdt as $dateStr => $dia)
            @php
                $isPast = $dia['data']->isPast() && !$dia['data']->isToday();
                $isActive = $dateStr === $selectedDate;
            @endphp
            <a href="{{ $isPast ? '#' : route('escalas.edit', ['config' => $config->id, 'dia' => $dateStr]) }}" 
               class="adt-tab {{ $isActive ? 'active' : '' }} {{ $isPast ? 'disabled' : '' }}"
               title="{{ $isPast ? 'Dia já passado (bloqueado)' : 'Editar este dia' }}">
                <div class="tab-date">{{ $dia['data']->format('d/m') }}</div>
                <div class="tab-day">{{ strtoupper($dia['data']->translatedFormat('D')) }}</div>
            </a>
        @endforeach
    </div>

    @php
        $currentDayConfig = $diasAdt[$selectedDate];
        $selectedMonIds = $currentDayConfig['monitores_ids'];
        $selectedAtdrIds = $currentDayConfig['atiradores_ids'];
        $allSelectedIds = array_merge($selectedMonIds, $selectedAtdrIds);
        $isTodayOrFuture = !(\Carbon\Carbon::parse($selectedDate)->isPast() && !\Carbon\Carbon::parse($selectedDate)->isToday());
    @endphp

    <form action="{{ route('escalas.update', $config->id) }}" method="POST" id="adtForm">
        @csrf
        @method('PUT')
        <input type="hidden" name="data_edit" value="{{ $selectedDate }}">

        <div class="adt-builder-layout">
        <!-- Sidebar: Tropa -->
        <div class="tropa-sidebar">
            <h3 style="margin-top: 0; font-size: 1rem; color: #333;">Efetivo Disponível</h3>
            <p style="font-size: 0.8rem; color: #666; margin-bottom: 1rem;">Arraste os militares para a escala ao lado.</p>
            
            <!-- Gaveta Monitores -->
            <div class="sidebar-section">
                <div class="drawer-header" onclick="toggleDrawer('mon-drawer')">
                    <h4 style="margin: 0; font-size: 0.75rem;"><i class="fa-solid fa-user-tie"></i> Monitores</h4>
                    <i class="fa-solid fa-chevron-down drawer-arrow" style="transform: rotate(180deg);"></i>
                </div>
                <div id="mon-drawer" class="drawer-content open">
                    <div style="display: flex; justify-content: flex-end; margin: 0.5rem 0;">
                        <div class="search-box">
                            <i class="fa-solid fa-magnifying-glass"></i>
                            <input type="text" class="tropa-search" data-target="pool-monitores" placeholder="Buscar...">
                        </div>
                    </div>
                    <div id="pool-monitores" class="drag-pool">
                        @foreach($monitores as $mon)
                            @if(!in_array($mon->id, $allSelectedIds))
                            <div class="drag-item" data-id="{{ $mon->id }}" data-nome="{{ strtolower($mon->nome_de_guerra) }}" data-numero="{{ $mon->numero }}">
                                <span class="nr">{{ $mon->numero }}</span>
                                <span class="nome">{{ $mon->nome_de_guerra }}</span>
                                <input type="hidden" class="user-id-input" value="{{ $mon->id }}" disabled>
                            </div>
                            @endif
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- Gaveta Atiradores -->
            <div class="sidebar-section" style="margin-top: 1rem;">
                <div class="drawer-header" onclick="toggleDrawer('atdr-drawer')">
                    <h4 style="margin: 0; font-size: 0.75rem;"><i class="fa-solid fa-person-military-rifle"></i> Atiradores</h4>
                    <i class="fa-solid fa-chevron-down drawer-arrow" style="transform: rotate(180deg);"></i>
                </div>
                <div id="atdr-drawer" class="drawer-content open">
                    <div style="display: flex; justify-content: flex-end; margin: 0.5rem 0;">
                        <div class="search-box">
                            <i class="fa-solid fa-magnifying-glass"></i>
                            <input type="text" class="tropa-search" data-target="pool-atiradores" placeholder="Buscar...">
                        </div>
                    </div>
                    <div id="pool-atiradores" class="drag-pool">
                        @foreach($atiradores as $atdr)
                            @if(!in_array($atdr->id, $allSelectedIds))
                            <div class="drag-item" data-id="{{ $atdr->id }}" data-nome="{{ strtolower($atdr->nome_de_guerra) }}" data-numero="{{ $atdr->numero }}">
                                <span class="nr">{{ $atdr->numero }}</span>
                                <span class="nome">{{ $atdr->nome_de_guerra }}</span>
                                <input type="hidden" class="user-id-input" value="{{ $atdr->id }}" disabled>
                            </div>
                            @endif
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        <!-- Document Area -->
        <div class="adt-document-canvas">
            <div class="adt-paper" style="padding: 2.5rem;">
                <div class="adt-header" style="margin-bottom: 1.5rem; border-bottom: none;">
                    <div style="display: flex; justify-content: space-between; align-items: center;">
                        <h3 style="font-size: 1.1rem; color: #333;"><i class="fa-regular fa-calendar-check"></i> Escala do dia {{ \Carbon\Carbon::parse($selectedDate)->format('d/m/Y') }}</h3>
                        
                        @if($isTodayOrFuture)
                        <button type="button" id="btnSubmit" class="btn-primary submit-btn" style="height: auto; padding: 0.5rem 1rem;">
                            <i class="fa-solid fa-save"></i> <span>Salvar Alterações do Dia</span>
                        </button>
                        @endif
                    </div>
                </div>

                <div class="adt-body">
                    <!-- Slot: Comandante -->
                    <div class="adt-section">
                        <label style="font-size: 0.8rem;">1. Comandante da Guarda</label>
                        <div id="slot-comandante" class="drop-slot single-slot">
                            <div class="slot-placeholder" style="font-size: 0.7rem; display: {{ count($selectedMonIds) > 0 ? 'none' : 'block' }};">Arraste no mínimo um comandante da guarda aqui</div>
                            @foreach($selectedMonIds as $uid)
                                @php
                                    $user = $monitores->firstWhere('id', $uid) ?? $atiradores->firstWhere('id', $uid);
                                @endphp
                                @if($user)
                                <div class="drag-item" data-id="{{ $user->id }}" data-nome="{{ strtolower($user->nome_de_guerra) }}" data-numero="{{ $user->numero }}">
                                    <span class="nr">{{ $user->numero }}</span>
                                    <span class="nome">{{ $user->nome_de_guerra }}</span>
                                    <input type="hidden" class="user-id-input" name="dia_monitores[]" value="{{ $user->id }}">
                                </div>
                                @endif
                            @endforeach
                        </div>
                    </div>

                    <!-- Slots: Sentinelas -->
                    <div class="adt-section" style="margin-top: 1.5rem;">
                        <label style="font-size: 0.8rem;">2. Sentinelas (Atiradores/Monitores)</label>
                        <div id="slots-sentinelas" class="drop-slot grid-slots">
                            <div class="slot-placeholder" style="font-size: 0.7rem; display: {{ count($selectedAtdrIds) > 0 ? 'none' : 'block' }};">Arraste no mínimo quatro sentinelas aqui</div>
                            @foreach($selectedAtdrIds as $uid)
                                @php
                                    $user = $monitores->firstWhere('id', $uid) ?? $atiradores->firstWhere('id', $uid);
                                @endphp
                                @if($user)
                                <div class="drag-item" data-id="{{ $user->id }}" data-nome="{{ strtolower($user->nome_de_guerra) }}" data-numero="{{ $user->numero }}">
                                    <span class="nr">{{ $user->numero }}</span>
                                    <span class="nome">{{ $user->nome_de_guerra }}</span>
                                    <input type="hidden" class="user-id-input" name="dia_atiradores[]" value="{{ $user->id }}">
                                </div>
                                @endif
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    </form>
</div>

<style>
.header-responsive {
    border-bottom: 1px solid var(--border-color);
    padding-bottom: 1.5rem;
    margin-bottom: 1.5rem;
}
.header-titles h2 {
    margin: 0;
    color: var(--primary-olive-dark);
    font-size: 1.5rem;
}
.header-titles p {
    margin: 5px 0 0 0;
    color: var(--text-secondary);
    font-size: 0.85rem;
}
.back-link {
    color: var(--primary-olive);
    font-size: 0.8rem;
    font-weight: 600;
    display: flex;
    align-items: center;
    gap: 0.5rem;
    margin-bottom: 0.5rem;
}

/* Tabs */
.adt-tabs-container {
    display: flex;
    gap: 0.5rem;
    margin-bottom: 2rem;
    overflow-x: auto;
    padding-bottom: 0.5rem;
}
.adt-tab {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    min-width: 80px;
    padding: 0.75rem 1rem;
    border-radius: 8px;
    background: #f1f5f9;
    color: #64748b;
    text-decoration: none;
    border: 2px solid transparent;
    transition: all 0.2s;
}
.adt-tab:hover:not(.disabled) {
    background: #e2e8f0;
}
.adt-tab.active {
    background: var(--primary-olive-light);
    color: var(--primary-olive-dark);
    border-color: var(--primary-olive);
}
.adt-tab.disabled {
    opacity: 0.5;
    cursor: not-allowed;
}
.tab-date {
    font-size: 1.1rem;
    font-weight: 800;
}
.tab-day {
    font-size: 0.7rem;
    font-weight: 600;
}

.submit-btn {
    padding: 0.6rem 1.5rem;
    background: #16a34a;
    font-size: 0.85rem;
    height: 38px;
    display: flex;
    align-items: center;
    gap: 0.5rem;
    border: none;
    border-radius: 6px;
    color: white;
    cursor: pointer;
    font-weight: 600;
    transition: background 0.2s;
}
.submit-btn:hover { background: #15803d; }

.search-box {
    position: relative;
    display: flex;
    align-items: center;
    background: white;
    border: 1px solid var(--border-color);
    border-radius: 6px;
    padding: 0 0.5rem;
    width: 120px;
}
.search-box i { font-size: 0.7rem; color: var(--text-secondary); }
.tropa-search {
    border: none;
    background: transparent;
    padding: 0.3rem;
    font-size: 0.75rem;
    width: 100%;
    outline: none;
}

.adt-builder-layout {
    display: grid;
    grid-template-columns: 350px 1fr;
    gap: 2rem;
    align-items: start;
}

.tropa-sidebar {
    background: #f8fafc;
    border: 1px solid var(--border-color);
    border-radius: 12px;
    padding: 1.5rem;
    position: sticky;
    top: 100px;
}

.sidebar-section h4 { font-size: 0.85rem; color: var(--primary-olive-dark); text-transform: uppercase; }

.drag-pool { display: flex; flex-direction: column; gap: 0.4rem; min-height: 50px; }

.drag-item {
    background: white;
    border: 1px solid var(--border-color);
    padding: 0.5rem 0.75rem;
    border-radius: 6px;
    display: flex;
    align-items: center;
    gap: 0.75rem;
    cursor: grab;
    transition: all 0.2s;
    user-select: none;
}

.drag-item:hover { border-color: var(--primary-olive); background: #f0f4f1; }
.drag-item.sortable-ghost { opacity: 0.4; background: var(--primary-olive-light); }

.drag-item .nr { font-weight: 800; color: var(--primary-olive); min-width: 25px; font-size: 0.85rem; }
.drag-item .nome { font-size: 0.85rem; font-weight: 500; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }

.adt-paper {
    background: white;
    border: 1px solid #d1d5db;
    box-shadow: 0 10px 25px rgba(0,0,0,0.05);
    padding: 3rem;
    min-height: auto;
    border-radius: 4px;
}

.adt-section label {
    display: block;
    font-weight: 800;
    text-decoration: underline;
    margin-bottom: 1rem;
    text-transform: uppercase;
    font-size: 0.8rem;
}

.drop-slot {
    border: 2px dashed #cbd5e1;
    border-radius: 12px;
    min-height: 50px;
    padding: 0.75rem;
    background: #fdfdfd;
    transition: all 0.2s;
    position: relative;
}

.drop-slot.invalid {
    border-color: #ef4444 !important;
    background: #fef2f2 !important;
    box-shadow: 0 0 0 4px rgba(239, 68, 68, 0.1);
}

.slot-placeholder {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    color: #94a3b8;
    font-size: 0.7rem;
    pointer-events: none;
}

.grid-slots { display: grid; grid-template-columns: 1fr 1fr; gap: 0.5rem; min-height: 120px; }
.single-slot { min-height: 60px; }

.drawer-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    cursor: pointer;
    padding: 0.75rem;
    background: #e2e8f0;
    border-radius: 6px;
    user-select: none;
}
.drawer-header:hover {
    background: #cbd5e1;
}
.drawer-arrow {
    transition: transform 0.3s ease;
    font-size: 0.8rem;
    color: var(--text-secondary);
}
.drawer-content {
    display: none;
    padding-top: 0.5rem;
}
.drawer-content.open {
    display: block;
}

@media (max-width: 1024px) {
    .adt-builder-layout { grid-template-columns: 1fr; }
    .tropa-sidebar { height: auto; position: static; max-height: 400px; }
}

@media (max-width: 768px) {
    .adt-paper {
        padding: 1.5rem !important;
        min-height: 400px !important;
    }
    .grid-slots {
        grid-template-columns: 1fr !important;
    }
}
</style>

@if($isTodayOrFuture)
<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
window.toggleDrawer = function(id) {
    const drawer = document.getElementById(id);
    const arrow = drawer.previousElementSibling.querySelector('.drawer-arrow');
    
    if (drawer.classList.contains('open')) {
        drawer.classList.remove('open');
        arrow.style.transform = 'rotate(0deg)';
    } else {
        drawer.classList.add('open');
        arrow.style.transform = 'rotate(180deg)';
    }
};

document.addEventListener('DOMContentLoaded', function() {
    // Busca e Prefixo
    document.querySelectorAll('.tropa-search').forEach(search => {
        search.addEventListener('input', function() {
            const term = this.value.toLowerCase();
            const targetId = this.dataset.target;
            const items = document.getElementById(targetId).querySelectorAll('.drag-item');
            items.forEach(item => {
                const text = item.dataset.nome + item.dataset.numero;
                item.style.display = text.includes(term) ? 'flex' : 'none';
            });
        });
    });

    // Sortables
    const poolMonitores = document.getElementById('pool-monitores');
    const poolAtiradores = document.getElementById('pool-atiradores');
    const slotComandante = document.getElementById('slot-comandante');
    const slotsSentinelas = document.getElementById('slots-sentinelas');

    // Grupos: Monitores podem ir para Cmt ou Sentinela. Atiradores apenas para Sentinela.
    new Sortable(poolMonitores, { group: { name: 'monitores', put: true, pull: true }, animation: 150 });
    new Sortable(poolAtiradores, { group: { name: 'atiradores', put: true, pull: true }, animation: 150 });

    new Sortable(slotComandante, {
        group: { name: 'monitores', put: ['monitores'] },
        animation: 150,
        onAdd: (evt) => {
            const input = evt.item.querySelector('.user-id-input');
            input.name = "dia_monitores[]";
            input.disabled = false;
            slotComandante.classList.remove('invalid');
            checkPlaceholder(slotComandante);
        },
        onRemove: (evt) => {
            evt.item.querySelector('.user-id-input').disabled = true;
            checkPlaceholder(slotComandante);
        }
    });

    new Sortable(slotsSentinelas, {
        group: { name: 'sentinelas', put: ['monitores', 'atiradores'] },
        animation: 150,
        onAdd: (evt) => {
            const input = evt.item.querySelector('.user-id-input');
            input.name = "dia_atiradores[]";
            input.disabled = false;
            slotsSentinelas.classList.remove('invalid');
            checkPlaceholder(slotsSentinelas);
        },
        onRemove: (evt) => {
            evt.item.querySelector('.user-id-input').disabled = true;
            checkPlaceholder(slotsSentinelas);
        }
    });

    function checkPlaceholder(slot) {
        const placeholder = slot.querySelector('.slot-placeholder');
        if (placeholder) placeholder.style.display = slot.querySelectorAll('.drag-item').length > 0 ? 'none' : 'block';
    }

    // Validação Popup
    const btnSubmit = document.getElementById('btnSubmit');
    if(btnSubmit) {
        btnSubmit.addEventListener('click', function() {
            const cmtItems = slotComandante.querySelectorAll('.drag-item .user-id-input');
            const gdItems = slotsSentinelas.querySelectorAll('.drag-item .user-id-input');
            let msg = "";

            if (cmtItems.length < 1) {
                msg += "• Adicione o <b>Comandante da Guarda</b>.<br>";
                slotComandante.classList.add('invalid');
            }
            if (gdItems.length < 4) {
                msg += "• Adicione pelo menos <b>4 Sentinelas</b>.<br>";
                slotsSentinelas.classList.add('invalid');
            }

            if (msg) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Escala Incompleta',
                    html: `<div style="text-align: left;">${msg}</div>`,
                    confirmButtonColor: 'var(--primary-olive)',
                    confirmButtonText: 'Entendido'
                });
                return;
            }

            cmtItems.forEach(input => {
                input.setAttribute('name', 'dia_monitores[]');
                input.removeAttribute('disabled');
            });
            
            gdItems.forEach(input => {
                input.setAttribute('name', 'dia_atiradores[]');
                input.removeAttribute('disabled');
            });

            document.getElementById('adtForm').submit();
        });
    }
});
</script>
@endif

@endsection
