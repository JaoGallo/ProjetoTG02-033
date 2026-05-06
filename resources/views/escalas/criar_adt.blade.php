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
        <div class="page-header header-responsive" style="display: flex; justify-content: space-between; align-items: flex-end; flex-wrap: wrap; gap: 1rem;">
            <div class="header-titles" style="flex: 1;">
                <a href="{{ route('escalas.index') }}" class="back-link">
                    <i class="fa-solid fa-arrow-left"></i> Voltar
                </a>
                <h2 style="margin: 0;">Criação de Aditamento</h2>
                <p style="margin: 5px 0 0 0; color: var(--text-secondary); font-size: 0.85rem;">Monte a escala do primeiro dia para gerar o Aditamento.</p>
            </div>

            <div class="form-actions-container" style="display: flex; gap: 1rem; align-items: flex-end; flex-wrap: wrap;">
                <div class="input-group" style="width: 180px; margin-bottom: 0;">
                    <label style="font-size: 0.7rem; font-weight: 600; color: var(--text-secondary); display: block; margin-bottom: 0.3rem;">Identificação</label>
                    <div class="adt-input-wrapper" style="display: flex; align-items: center; gap: 0.3rem; background: #f1f5f9; padding: 0 0.75rem; border-radius: 6px; border: 1px solid var(--border-color);">
                        <span style="color: var(--text-secondary); font-weight: 700; font-size: 0.75rem;">ADT</span>
                        <input type="text" name="nome_suffix" value="{{ date('W/Y') }}" required placeholder="XX/XXXX" style="background: transparent; border: none; padding: 0.5rem 0; width: 100%; outline: none; font-size: 0.85rem;">
                        <input type="hidden" name="nome" id="full_nome" value="Aditamento {{ date('W/Y') }}">
                    </div>
                </div>

                <div class="input-group" style="width: 150px; margin-bottom: 0;">
                    <label style="font-size: 0.7rem; font-weight: 600; color: var(--text-secondary); display: block; margin-bottom: 0.3rem;">Data de Início</label>
                    <input type="date" name="data_inicio" value="{{ date('Y-m-d') }}" required style="padding: 0.5rem; font-size: 0.85rem; width: 100%; border-radius: 6px; border: 1px solid var(--border-color);">
                </div>

                <button type="button" id="btnSubmit" class="btn-primary" style="display: flex; align-items: center; gap: 0.5rem; height: 42px;">
                    <i class="fa-solid fa-check"></i> <span>Gerar Aditamento</span>
                </button>
            </div>
        </div>

            <div class="adt-builder-layout">
                <!-- Sidebar: Tropa -->
                <div class="tropa-sidebar" id="sidebar-tropa">
                    <div style="margin-bottom: 1rem;">
                        <h3 style="margin-top: 0; font-size: 0.9rem; color: #333; text-transform: uppercase; font-weight: 800; border-bottom: 1px solid #e2e8f0; padding-bottom: 0.5rem; margin-bottom: 1rem;">Efetivo Disponível</h3>
                        
                        <div class="filter-group" style="margin-bottom: 1rem;">
                            <label style="font-size: 0.7rem; font-weight: 700; color: var(--text-secondary); text-transform: uppercase; display: block; margin-bottom: 0.3rem;">Filtrar por Turma</label>
                            <select id="turma-filter" class="form-select" style="width: 100%; padding: 0.5rem; font-size: 0.85rem; border-radius: 6px; border: 1px solid var(--border-color); background: white; outline: none; cursor: pointer;">
                                @foreach($turmasDisponiveis as $t)
                                    <option value="{{ $t }}" {{ $t == $turma ? 'selected' : '' }}>Turma de {{ $t }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="search-box" style="width: 100%; margin-bottom: 1rem;">
                            <i class="fa-solid fa-magnifying-glass"></i>
                            <input type="text" id="global-tropa-search" placeholder="Buscar por nome ou número..." style="border: none; background: transparent; padding: 0.5rem; font-size: 0.85rem; width: 100%; outline: none;">
                        </div>
                    </div>
                    <!-- Gaveta Monitores -->
                    <div class="sidebar-section">
                        <div class="drawer-header" onclick="toggleDrawer('mon-drawer')">
                            <h4 style="margin: 0; font-size: 0.75rem;"><i class="fa-solid fa-user-tie"></i> Monitores</h4>
                            <i class="fa-solid fa-chevron-down drawer-arrow" style="transform: rotate(180deg);"></i>
                        </div>
                        <div id="mon-drawer" class="drawer-content open">
                            <div id="pool-monitores" class="drag-pool">
                                @foreach($monitores as $mon)
                                    <div class="drag-item" data-id="{{ $mon->id }}" data-type="mon"
                                        data-nome="{{ strtolower($mon->nome_de_guerra) }}" data-numero="{{ $mon->numero }}">
                                        <span class="nr">{{ $mon->numero }}</span>
                                        <span class="nome">{{ $mon->nome_de_guerra }}</span>
                                        
                                        @php
                                            $folga = $mon->folga_dias;
                                            $color = '#ef4444'; 
                                            if ($folga >= 7) $color = '#f59e0b'; 
                                            if ($folga >= 14) $color = '#10b981'; 
                                            if ($folga == 999) $color = '#6366f1'; 
                                            if ($folga == -1) $color = '#10b981'; 
                                        @endphp
                                        <span class="folga-badge" style="background: {{ $color }};" title="{{ $folga == -1 ? 'Próximo' : 'Último' }} serviço: {{ $mon->ultima_escala }}">
                                            @if($folga == 999) Novo @elseif($folga == -1) Escalado @else {{ $folga }}d @endif
                                        </span>

                                        <button type="button" class="remove-item-btn" onclick="removeItem(this)">
                                            <i class="fa-solid fa-xmark"></i>
                                        </button>
                                        <input type="hidden" class="user-id-input" value="{{ $mon->id }}" disabled>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    <!-- Gaveta Atiradores -->
                    <div class="sidebar-section" style="margin-top: 1rem;">
                        <div class="drawer-header" onclick="toggleDrawer('atdr-drawer')">
                            <h4 style="margin: 0; font-size: 0.75rem;"><i class="fa-solid fa-person-military-rifle"></i>
                                Atiradores</h4>
                            <i class="fa-solid fa-chevron-down drawer-arrow" style="transform: rotate(180deg);"></i>
                        </div>
                        <div id="atdr-drawer" class="drawer-content open">
                            <div id="pool-atiradores" class="drag-pool">
                                @foreach($atiradores as $atdr)
                                    <div class="drag-item" data-id="{{ $atdr->id }}" data-type="atdr"
                                        data-nome="{{ strtolower($atdr->nome_de_guerra) }}" data-numero="{{ $atdr->numero }}">
                                        <span class="nr">{{ $atdr->numero }}</span>
                                        <span class="nome">{{ $atdr->nome_de_guerra }}</span>

                                        @php
                                            $folga = $atdr->folga_dias;
                                            $color = '#ef4444'; 
                                            if ($folga >= 7) $color = '#f59e0b'; 
                                            if ($folga >= 14) $color = '#10b981'; 
                                            if ($folga == 999) $color = '#6366f1'; 
                                            if ($folga == -1) $color = '#10b981';
                                        @endphp
                                        <span class="folga-badge" style="background: {{ $color }};" title="{{ $folga == -1 ? 'Próximo' : 'Último' }} serviço: {{ $atdr->ultima_escala }}">
                                            @if($folga == 999) Novo @elseif($folga == -1) Escalado @else {{ $folga }}d @endif
                                        </span>

                                        <button type="button" class="remove-item-btn" onclick="removeItem(this)">
                                            <i class="fa-solid fa-xmark"></i>
                                        </button>
                                        <input type="hidden" class="user-id-input" value="{{ $atdr->id }}" disabled>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Document Area -->
                <div class="adt-document-canvas" style="display: flex; flex-direction: column; gap: 2rem;">
                    <!-- 1ª PARTE: ESCALA (Visual do dia selecionado) -->
                    <div class="adt-paper" style="padding: 2.5rem;">
                        <div class="adt-header" style="margin-bottom: 1.5rem; border-bottom: 2px solid #333; padding-bottom: 0.5rem; text-align: center;">
                            <h2 style="font-weight: 900; font-size: 1.2rem; margin-bottom: 0.5rem;">1ª PARTE - SERVIÇOS DIÁRIOS</h2>
                            <p style="font-weight: 700; font-size: 0.9rem; text-transform: uppercase; margin-top: 1rem;">ESCALA DE SERVIÇO PARA O DIA 1 (INICIAL)</p>
                        </div>

                        <div class="adt-body">
                            <!-- Slot: Comandante -->
                            <div class="adt-section">
                                <label style="font-size: 0.8rem;">1. Comandante da Guarda</label>
                                <div id="slot-comandante" class="drop-slot single-slot">
                                    <div class="slot-placeholder" style="font-size: 0.7rem;">Arraste o Comandante da Guarda aqui</div>
                                </div>
                            </div>

                            <!-- Slots: Sentinelas -->
                            <div class="adt-section" style="margin-top: 1.5rem;">
                                <label style="font-size: 0.8rem;">2. Sentinelas (Atiradores/Monitores)</label>
                                <div id="slots-sentinelas" class="drop-slot grid-slots">
                                    <div class="slot-placeholder" style="font-size: 0.7rem;">Arraste as Sentinelas aqui</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- OUTRAS PARTES (FIXAS DO ADITAMENTO) -->
                    <div class="adt-paper" style="padding: 2.5rem;">
                        <div class="adt-section" style="margin-bottom: 2rem;">
                            <h2 style="border-bottom: 2px solid #333; padding-bottom: 0.5rem; font-weight: 900; font-size: 1.2rem; margin-bottom: 1rem; text-align: center;">2ª PARTE - INSTRUÇÃO</h2>
                            <textarea name="part2_instrucao" class="adt-textarea" placeholder="Digite aqui os detalhes da instrução..."></textarea>
                        </div>

                        <div class="adt-section" style="margin-bottom: 2rem;">
                            <h2 style="border-bottom: 2px solid #333; padding-bottom: 0.5rem; font-weight: 900; font-size: 1.2rem; margin-bottom: 1rem; text-align: center;">3ª PARTE - ASSUNTOS GERAIS</h2>
                            <textarea name="part3_assuntos_gerais" class="adt-textarea" placeholder="Digite aqui os assuntos gerais e administrativos..."></textarea>
                        </div>

                        <div class="adt-section">
                            <h2 style="border-bottom: 2px solid #333; padding-bottom: 0.5rem; font-weight: 900; font-size: 1.2rem; margin-bottom: 1rem; text-align: center;">4ª PARTE - JUSTIÇA E DISCIPLINA</h2>
                            <textarea name="part4_justica_disciplina" class="adt-textarea" placeholder="Digite aqui os assuntos de justiça e disciplina..."></textarea>
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

        .submit-btn:hover {
            background: #15803d;
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

        .search-box i {
            font-size: 0.7rem;
            color: var(--text-secondary);
        }

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
            height: auto;
            min-height: 200px;
            overflow-y: auto;
            position: sticky;
            top: 20px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        }

        .sidebar-section h4 {
            font-size: 0.85rem;
            color: var(--primary-olive-dark);
            text-transform: uppercase;
        }

        .drag-pool {
            display: flex;
            flex-direction: column;
            gap: 0.4rem;
            min-height: 50px;
        }

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

        .drag-item:hover {
            border-color: var(--primary-olive);
            background: #f0f4f1;
        }

        .drag-item.sortable-ghost {
            opacity: 0.4;
            background: var(--primary-olive-light);
        }

        .drag-item .nr {
            font-weight: 800;
            color: var(--primary-olive);
            min-width: 25px;
            font-size: 0.85rem;
        }

        .drag-item .nome {
            font-size: 0.85rem;
            font-weight: 500;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .adt-paper {
            background: white;
            border: 1px solid #d1d5db;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.05);
            padding: 3rem;
            min-height: auto;
            border-radius: 4px;
        }

        .adt-textarea {
            width: 100%;
            min-height: 150px;
            padding: 1rem;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            font-family: 'Courier New', Courier, monospace;
            font-size: 0.9rem;
            line-height: 1.5;
            resize: vertical;
            background: #fcfcfc;
            outline: none;
            transition: border-color 0.2s;
        }

        .adt-textarea:focus {
            border-color: var(--primary-olive);
            background: white;
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

        .grid-slots {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 0.5rem;
            min-height: 120px;
        }

        .single-slot {
            min-height: 60px;
        }

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
            .adt-builder-layout {
                grid-template-columns: 1fr;
            }

            .tropa-sidebar {
                height: auto;
                position: static;
                max-height: 400px;
            }
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
        .remove-item-btn {
            display: none;
            background: #fee2e2;
            color: #ef4444;
            border: none;
            border-radius: 4px;
            width: 24px;
            height: 24px;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            margin-left: auto;
            transition: all 0.2s;
            font-size: 0.8rem;
        }

        .remove-item-btn:hover {
            background: #ef4444;
            color: white;
        }

        .drop-slot .drag-item .remove-item-btn {
            display: flex;
        }

        .folga-badge {
            margin-left: auto;
            font-size: 0.65rem;
            font-weight: 800;
            color: white;
            padding: 2px 6px;
            border-radius: 4px;
            text-transform: uppercase;
            min-width: 35px;
            text-align: center;
            box-shadow: 0 1px 2px rgba(0,0,0,0.1);
        }

        .drop-slot .folga-badge {
            display: none; /* Esconde a folga quando já está na escala para não poluir */
        }
    </style>

    <script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        window.toggleDrawer = function (id) {
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

        document.addEventListener('DOMContentLoaded', function () {
            // Filtro de Turma
            const turmaFilter = document.getElementById('turma-filter');
            if (turmaFilter) {
                turmaFilter.addEventListener('change', function() {
                    const currentUrl = new URL(window.location.href);
                    currentUrl.searchParams.set('turma', this.value);
                    window.location.href = currentUrl.toString();
                });
            }

            // Busca e Prefixo
            // Busca Global
            const globalSearch = document.getElementById('global-tropa-search');
            if (globalSearch) {
                globalSearch.addEventListener('input', function() {
                    const term = this.value.toLowerCase();
                    document.querySelectorAll('.drag-item').forEach(item => {
                        const text = (item.dataset.nome || '') + (item.dataset.numero || '');
                        item.style.display = text.toLowerCase().includes(term) ? 'flex' : 'none';
                    });

                    // Fecha ou abre os drawers se houver resultados
                    if (term.length > 0) {
                        document.querySelectorAll('.drawer-content').forEach(d => d.classList.add('open'));
                        document.querySelectorAll('.drawer-arrow').forEach(a => a.style.transform = 'rotate(180deg)');
                    }
                });
            }

            const suffixInput = document.querySelector('input[name="nome_suffix"]');
            const fullNomeInput = document.getElementById('full_nome');
            if (suffixInput) {
                suffixInput.addEventListener('input', function () {
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

            window.removeItem = function(btn) {
                const item = btn.closest('.drag-item');
                const type = item.dataset.type;
                const targetPoolId = type === 'mon' ? 'pool-monitores' : 'pool-atiradores';
                const pool = document.getElementById(targetPoolId);
                
                if (pool) {
                    const input = item.querySelector('.user-id-input');
                    input.disabled = true;
                    pool.appendChild(item);
                    
                    // Atualizar placeholders
                    document.querySelectorAll('.drop-slot').forEach(slot => {
                        checkPlaceholder(slot);
                    });
                }
            };

            function checkPlaceholder(slot) {
                const placeholder = slot.querySelector('.slot-placeholder');
                if (placeholder) placeholder.style.display = slot.querySelectorAll('.drag-item').length > 0 ? 'none' : 'block';
            }

            // Validação Popup
            document.getElementById('btnSubmit').addEventListener('click', function () {
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

            // Ajustar altura da sidebar para casar com a 1ª parte
            const updateSidebarHeight = () => {
                const firstPart = document.querySelector('.adt-paper');
                const sidebar = document.getElementById('sidebar-tropa');
                if (firstPart && sidebar) {
                    const viewportMax = window.innerHeight - 60;
                    const paperHeight = firstPart.offsetHeight;
                    sidebar.style.maxHeight = Math.min(paperHeight, viewportMax) + 'px';
                }
            };

            updateSidebarHeight();
            window.addEventListener('resize', updateSidebarHeight);

            // Re-ajustar quando conteúdo mudar
            const observer = new MutationObserver(updateSidebarHeight);
            const adtBody = document.querySelector('.adt-body');
            if (adtBody) {
                observer.observe(adtBody, { childList: true, subtree: true });
            }
        });
    </script>
@endsection