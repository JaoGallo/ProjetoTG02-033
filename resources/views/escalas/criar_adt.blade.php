@extends('layouts.app')

@section('title', 'Criação de Aditamento')

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

    <form action="{{ route('escalas.store') }}" method="POST" id="adtForm">
        @csrf
        <div class="page-header header-responsive">
            <div class="header-titles">
                <a href="{{ route('escalas.index') }}" class="back-link">
                    <i class="fa-solid fa-arrow-left"></i> Voltar
                </a>
                <h2>Criação de Aditamento</h2>
                <p>Monte a escala inicial arrastando os nomes.</p>
            </div>

            <div class="form-actions-container">
                <div class="input-group">
                    <label>Identificação</label>
                    <div class="adt-input-wrapper">
                        <span>ADT</span>
                        <input type="text" name="nome_suffix" value="{{ date('W/Y') }}" required placeholder="XX/XXXX">
                        <input type="hidden" name="nome" id="full_nome" value="Aditamento {{ date('W/Y') }}">
                    </div>
                </div>

                <div class="input-group">
                    <label>Data de Início</label>
                    <input type="date" name="data_inicio" value="{{ date('Y-m-d') }}" required>
                </div>

                <button type="button" id="btnSubmit" class="btn-primary submit-btn">
                    <i class="fa-solid fa-check"></i> <span>Gerar Aditamento</span>
                </button>
            </div>
        </div>

        <div class="adt-builder-layout">
        <!-- Sidebar: Tropa -->
        <div class="tropa-sidebar">
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
                        <div class="drag-item" data-id="{{ $mon->id }}" data-nome="{{ strtolower($mon->nome_de_guerra) }}" data-numero="{{ $mon->numero }}">
                            <span class="nr">{{ $mon->numero }}</span>
                            <span class="nome">{{ $mon->nome_de_guerra }}</span>
                            <input type="hidden" class="user-id-input" value="{{ $mon->id }}" disabled>
                        </div>
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
                        <div class="drag-item" data-id="{{ $atdr->id }}" data-nome="{{ strtolower($atdr->nome_de_guerra) }}" data-numero="{{ $atdr->numero }}">
                            <span class="nr">{{ $atdr->numero }}</span>
                            <span class="nome">{{ $atdr->nome_de_guerra }}</span>
                            <input type="hidden" class="user-id-input" value="{{ $atdr->id }}" disabled>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        <!-- Document Area -->
        <div class="adt-document-canvas">
            <div class="adt-paper" style="padding: 2.5rem;">
                <div class="adt-header" style="margin-bottom: 1.5rem;">
                    <img src="{{ asset('tg_logo.png') }}" alt="Logo" style="height: 50px; margin-bottom: 5px;">
                    <h3 style="font-size: 1.1rem;">TIRO DE GUERRA 02-033</h3>
                    <p style="font-size: 0.8rem;">São José do Rio Preto - SP</p>
                </div>

                <div class="adt-body">
                    <p style="text-align: center; font-weight: 800; margin: 1rem 0; font-size: 0.9rem;">ESCALA DE SERVIÇO</p>

                    <!-- Slot: Comandante -->
                    <div class="adt-section">
                        <label style="font-size: 0.8rem;">1. Comandante da Guarda</label>
                        <div id="slot-comandante" class="drop-slot single-slot">
                            <div class="slot-placeholder" style="font-size: 0.7rem;">Arraste no mínimo um comandante da guarda aqui</div>
                        </div>
                    </div>

                    <!-- Slots: Sentinelas -->
                    <div class="adt-section" style="margin-top: 1.5rem;">
                        <label style="font-size: 0.8rem;">2. Sentinelas (Atiradores/Monitores)</label>
                        <div id="slots-sentinelas" class="drop-slot grid-slots">
                            <div class="slot-placeholder" style="font-size: 0.7rem;">Arraste no mínimo quatro sentinelas aqui</div>
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
    display: flex;
    align-items: flex-end;
    gap: 2rem;
    flex-wrap: wrap;
}
.header-titles {
    flex: 1;
    min-width: 250px;
}
.header-titles h2 {
    margin: 0;
    color: var(--primary-olive-dark);
    font-size: 1.5rem;
}
.header-titles p {
    margin: 2px 0 0 0;
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
.form-actions-container {
    display: flex;
    gap: 1rem;
    align-items: flex-end;
    flex-wrap: wrap;
}
.form-actions-container .input-group {
    margin-bottom: 0;
    width: 150px;
}
.form-actions-container .input-group:first-child {
    width: 180px;
}
.form-actions-container .input-group label {
    font-size: 0.7rem;
    font-weight: 600;
}
.adt-input-wrapper {
    display: flex;
    align-items: center;
    gap: 0.3rem;
    background: #f1f5f9;
    padding: 0 0.75rem;
    border-radius: 6px;
    border: 1px solid var(--border-color);
}
.adt-input-wrapper span {
    color: var(--text-secondary);
    font-weight: 700;
    font-size: 0.75rem;
}
.adt-input-wrapper input {
    background: transparent;
    border: none;
    padding: 0.5rem 0;
    width: 100%;
    outline: none;
    font-size: 0.85rem;
}
.form-actions-container input[type="date"] {
    padding: 0.5rem;
    font-size: 0.85rem;
    width: 100%;
    box-sizing: border-box;
}
.submit-btn {
    padding: 0.6rem 1.5rem;
    background: #16a34a;
    font-size: 0.85rem;
    height: 38px;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

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

.adt-header { text-align: center; border-bottom: 2px solid #333; padding-bottom: 1rem; margin-bottom: 2rem; }
.adt-header h3 { margin: 0; font-size: 1.3rem; }
.adt-header p { margin: 5px 0 0 0; color: #666; font-size: 0.9rem; }

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
    .header-responsive {
        flex-direction: column;
        align-items: flex-start;
        gap: 1rem;
    }
    .form-actions-container {
        width: 100%;
        flex-direction: column;
        align-items: stretch;
    }
    .form-actions-container .input-group {
        width: 100% !important;
    }
    .submit-btn {
        width: 100%;
        margin-top: 0.5rem;
        justify-content: center;
    }
    .adt-paper {
        padding: 1.5rem !important;
        min-height: 400px !important;
    }
    .grid-slots {
        grid-template-columns: 1fr !important;
    }
    .adt-builder-layout {
        gap: 1rem;
    }
    .tropa-sidebar {
        padding: 1rem;
    }
}
</style>

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

    const suffixInput = document.querySelector('input[name="nome_suffix"]');
    const fullNomeInput = document.getElementById('full_nome');
    if (suffixInput) {
        suffixInput.addEventListener('input', function() {
            fullNomeInput.value = "Aditamento " + this.value;
        });
    }

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
            input.name = "dia1_monitores[]";
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
            // IMPORTANTE: Aqui enviamos como atiradores para o serviço, mesmo se for monitor
            input.name = "dia1_atiradores[]";
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
    document.getElementById('btnSubmit').addEventListener('click', function() {
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

        // Força a ativação e nomeação correta dos inputs no momento do envio
        cmtItems.forEach(input => {
            input.setAttribute('name', 'dia1_monitores[]');
            input.removeAttribute('disabled');
        });
        
        gdItems.forEach(input => {
            input.setAttribute('name', 'dia1_atiradores[]');
            input.removeAttribute('disabled');
        });

        // Submete o form
        document.getElementById('adtForm').submit();
    });
});
</script>
@endsection
