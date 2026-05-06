@extends('layouts.app')

@section('title', 'Gerenciar Atiradores')

@section('styles')
<style>
    /* Estilos Premium para o Modal */
    .modal-background {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(15, 23, 15, 0.85);
        backdrop-filter: blur(4px);
        align-items: center;
        justify-content: center;
        z-index: 1000;
        padding: 20px;
    }

    .modal-content-card {
        background: #ffffff;
        width: 100%;
        max-width: 550px;
        max-height: 90vh;
        overflow-y: auto;
        padding: 35px;
        border-radius: 16px;
        box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.4);
        position: relative;
        border: none;
        color: #1a1a1a;
    }

    /* Custom scrollbar para o modal */
    .modal-content-card::-webkit-scrollbar {
        width: 6px;
    }
    .modal-content-card::-webkit-scrollbar-track {
        background: transparent;
    }
    .modal-content-card::-webkit-scrollbar-thumb {
        background: #e5e7eb;
        border-radius: 10px;
    }
    .modal-content-card::-webkit-scrollbar-thumb:hover {
        background: #d1d5db;
    }

    .modal-close-btn {
        position: absolute;
        right: 25px;
        top: 25px;
        background: #f3f4f6;
        border: none;
        color: #4b5563;
        cursor: pointer;
        font-size: 1rem;
        width: 32px;
        height: 32px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.2s;
    }

    .modal-close-btn:hover {
        background: #e5e7eb;
        color: #111827;
    }

    .modal-title {
        font-size: 1.5rem;
        font-weight: 800;
        color: #111827;
        margin-bottom: 8px;
        letter-spacing: -0.5px;
    }

    .modal-subtitle {
        color: #6b7280;
        font-size: 0.9rem;
        margin-bottom: 25px;
    }

    .form-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 20px;
    }

    .input-wrapper {
        display: flex;
        flex-direction: column;
        gap: 6px;
    }

    .input-wrapper label {
        font-weight: 600;
        font-size: 0.8rem;
        color: #374151;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .input-field {
        width: 100%;
        padding: 12px 16px;
        background: #f9fafb;
        border: 1px solid #d1d5db;
        color: #111827;
        border-radius: 8px;
        font-size: 0.95rem;
        transition: all 0.2s;
    }

    .input-field:focus {
        background: #fff;
        border-color: #4a5c48;
        box-shadow: 0 0 0 3px rgba(74, 92, 72, 0.1);
        outline: none;
    }

    .full-width {
        grid-column: 1 / -1;
    }

    .cfc-badge {
        padding: 5px 12px;
        border-radius: 50px;
        font-size: 0.75rem;
        font-weight: 700;
        text-transform: uppercase;
        cursor: pointer;
        transition: transform 0.2s;
        border: none;
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }

    .badge-active {
        background: #1e40af;
        color: #eff6ff;
    }

    .badge-inactive {
        background: #f3f4f6;
        color: #6b7280;
    }

    /* Standardized Import Button */
    .btn-import-header {
        background-color: #4b5563; /* Grayish olive for distinction from main action */
        color: white;
        padding: 0.85rem 1.5rem;
        border-radius: 4px;
        border: none;
        font-weight: 500;
        cursor: pointer;
        font-family: var(--font-heading);
        transition: all 0.2s ease;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        font-size: 0.875rem;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .btn-import-header:hover {
        background-color: #374151;
        transform: translateY(-1px);
    }

    /* Custom File Input - Themed */
    .file-drop-area {
        position: relative;
        display: flex;
        align-items: center;
        flex-direction: column;
        justify-content: center;
        width: 100%;
        padding: 40px 20px;
        border: 2px dashed var(--border-color);
        border-radius: 8px;
        background: #fcfdfc;
        transition: all 0.2s ease;
        cursor: pointer;
        text-align: center;
    }

    .file-drop-area:hover {
        border-color: var(--primary-olive);
        background: #f0f4f1;
    }

    .file-drop-area i {
        font-size: 2rem;
        color: var(--primary-olive);
        margin-bottom: 12px;
    }

    .file-drop-area .file-msg {
        font-size: 0.9rem;
        font-weight: 600;
        color: var(--text-primary);
    }

    .file-drop-area .file-hint {
        font-size: 0.75rem;
        color: var(--text-secondary);
        margin-top: 4px;
    }

    .file-input-hidden {
        position: absolute;
        left: 0;
        top: 0;
        height: 100%;
        width: 100%;
        opacity: 0;
        cursor: pointer;
    }

    .import-info-box {
        background: #f0f7f2;
        border-left: 4px solid var(--primary-olive);
        padding: 15px;
        border-radius: 4px;
        margin-top: 20px;
    }

    .import-info-box p {
        font-size: 0.8rem;
        color: var(--primary-olive-dark);
        margin: 0;
        line-height: 1.4;
    }

    /* Colunas mais juntas */
    .compact-table th, .compact-table td {
        padding: 12px 15px !important;
    }
    
    .col-shrink {
        width: 1%;
        white-space: nowrap;
    }

    @media (max-width: 768px) {
        .header-actions {
            flex-direction: column;
            align-items: flex-start !important;
            gap: 15px;
        }
        .header-actions div:last-child {
            width: 100%;
            flex-direction: column;
            align-items: stretch !important;
        }
        .form-grid {
            grid-template-columns: 1fr;
        }
        .table-container {
            overflow-x: auto;
        }
        table {
            min-width: 700px;
        }
    }
</style>
@endsection

@section('content')
<div class="header-actions" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px;">
    <div>
        <h2 style="font-weight: 800; color: var(--primary-olive-dark);">Gestão de Atiradores</h2>
        <p style="color: var(--text-secondary); font-size: 0.9rem;">Visualizando a Turma de {{ $turma }}</p>
    </div>
    
    <div style="display: flex; gap: 12px; align-items: center;">
        <form action="{{ route('atiradores.index') }}" method="GET" style="display: flex; align-items: center; gap: 10px;">
            <select name="turma" class="input-field" style="width: 100px; padding: 8px;" onchange="this.form.submit()">
                @for ($i = date('Y') - 5; $i <= date('Y') + 2; $i++)
                    <option value="{{ $i }}" {{ $turma == $i ? 'selected' : '' }}>{{ $i }}</option>
                @endfor
            </select>
        </form>
        <button class="btn-import-header" onclick="openImportModal()">
            <i class="fa-solid fa-file-excel"></i> IMPORTAR EXCEL
        </button>
        <button class="btn btn-primary" onclick="openCreateModal()" style="display: flex; align-items: center; gap: 8px;">
            <i class="fa-solid fa-user-plus"></i> Novo Atirador
        </button>
    </div>
</div>

@if(session('success'))
    <div class="alert alert-success">
        <i class="fa-solid fa-circle-check"></i> {{ session('success') }}
    </div>
@endif

@if($errors->any())
    <div class="alert alert-danger" style="flex-direction: column; align-items: flex-start;">
        <ul style="margin: 0; padding-left: 20px; font-size: 0.85rem;">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<div class="card" style="background: white; padding: 0; border-radius: 12px; border: 1px solid var(--border-color); box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05);">
    <div class="table-container">
        <table class="compact-table" style="width: 100%; border-collapse: collapse; text-align: left;">
            <thead>
                <tr style="background: #f9fafb; border-bottom: 2px solid var(--border-color);">
                    <th class="col-shrink" style="padding: 15px 20px; font-size: 0.75rem; color: #6b7280; text-transform: uppercase;">Nº</th>
                    <th class="col-shrink" style="padding: 15px 20px; font-size: 0.75rem; color: #6b7280; text-transform: uppercase;">Nome de Guerra</th>
                    <th class="col-shrink" style="padding: 15px 20px; font-size: 0.75rem; color: #6b7280; text-transform: uppercase;">Identificação</th>
                    <th class="col-shrink" style="padding: 15px 20px; font-size: 0.75rem; color: #6b7280; text-transform: uppercase;">CFC</th>
                    <th style="padding: 15px 20px; font-size: 0.75rem; color: #6b7280; text-transform: uppercase; text-align: right;">Ações</th>
                </tr>
            </thead>
            <tbody>
                @forelse($atiradores as $atirador)
                <tr style="border-bottom: 1px solid #f3f4f6;">
                    <td class="col-shrink" style="padding: 15px 20px; font-weight: 600; color: #9ca3af;">{{ str_pad($atirador->numero, 2, '0', STR_PAD_LEFT) }}</td>
                    <td class="col-shrink" style="padding: 15px 20px;">
                        <div style="font-weight: 800; color: #111827;">{{ mb_strtoupper($atirador->nome_de_guerra) }}</div>
                        <div style="font-size: 0.7rem; color: #9ca3af;">{{ $atirador->name }}</div>
                    </td>
                    <td class="col-shrink" style="padding: 15px 20px;">
                        <div style="font-size: 0.8rem;"><strong>CPF:</strong> {{ $atirador->cpf }}</div>
                        <div style="font-size: 0.8rem;"><strong>RA:</strong> {{ $atirador->ra }}</div>
                    </td>
                    <td class="col-shrink" style="padding: 15px 20px;">
                        <button type="button" 
                                onclick="toggleCfc(this, {{ $atirador->id }})" 
                                class="cfc-badge {{ $atirador->is_cfc ? 'badge-active' : 'badge-inactive' }}">
                            {{ $atirador->is_cfc ? 'CFC ATIVO' : 'NÃO CFC' }}
                        </button>
                    </td>
                    <td style="padding: 15px 20px; text-align: right; display: flex; justify-content: flex-end; gap: 8px;">
                        <button onclick="openEditModal({{ json_encode($atirador) }})" class="btn-icon" style="background: #f3f4f6; color: #4b5563; border: none; width: 32px; height: 32px; border-radius: 8px; cursor: pointer;">
                            <i class="fa-solid fa-pen-to-square"></i>
                        </button>
                        <button onclick="openDeleteModal({{ json_encode($atirador) }})" style="background: #fee2e2; color: #b91c1c; border: none; width: 32px; height: 32px; border-radius: 8px; cursor: pointer;">
                            <i class="fa-solid fa-trash-can"></i>
                        </button>
                    </td>
                </tr>
                @empty
                <tr><td colspan="5" style="padding: 40px; text-align: center; color: #9ca3af;">Nenhum atirador nesta turma.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- Modal Deletar -->
<div id="modalDeleteAtirador" class="modal-background">
    <div class="modal-content-card" style="max-width: 400px; text-align: center;">
        <div style="background: #fee2e2; color: #ef4444; width: 60px; height: 60px; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 20px; font-size: 1.5rem;">
            <i class="fa-solid fa-triangle-exclamation"></i>
        </div>
        <h3 class="modal-title">Confirmar Exclusão</h3>
        <p class="modal-subtitle" id="delete_warning_text">Tem certeza que deseja remover este atirador?</p>
        
        <form id="deleteForm" method="POST">
            @csrf @method('DELETE')
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px; margin-top: 25px;">
                <button type="button" onclick="closeModal('modalDeleteAtirador')" class="input-field" style="background: #f3f4f6; cursor: pointer; border: none;">Cancelar</button>
                <button type="submit" class="btn btn-primary" style="background: #ef4444; border: none;">Sim, Excluir</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Importar Excel -->
<div id="modalImportExcel" class="modal-background">
    <div class="modal-content-card">
        <button onclick="closeModal('modalImportExcel')" class="modal-close-btn"><i class="fa-solid fa-xmark"></i></button>
        <h3 class="modal-title">Importar Atiradores</h3>
        <p class="modal-subtitle">Selecione o arquivo Excel (.xlsx) para importar em massa.</p>
        <form action="{{ route('atiradores.import') }}" method="POST" enctype="multipart/form-data" id="importForm">
            @csrf
            <div class="form-grid">
                <div class="input-wrapper full-width">
                    <label>Selecione o Arquivo</label>
                    <div class="file-drop-area" id="fileDropArea">
                        <i class="fa-solid fa-cloud-arrow-up"></i>
                        <span class="file-msg">Clique para selecionar ou arraste o arquivo</span>
                        <span class="file-hint">Formatos aceitos: .xlsx, .xls, .csv</span>
                        <input type="file" name="excel_file" id="excel_file" required class="file-input-hidden" accept=".xlsx,.xls,.csv" onchange="updateFileName(this)">
                    </div>
                </div>
                
                <div class="input-wrapper full-width">
                    <label>Turma de Destino</label>
                    <div style="position: relative;">
                        <i class="fa-solid fa-calendar-days" style="position: absolute; left: 15px; top: 50%; transform: translateY(-50%); color: #9ca3af;"></i>
                        <select name="turma" required class="input-field" style="padding-left: 45px;">
                            @for ($i = date('Y'); $i <= date('Y') + 1; $i++)
                                <option value="{{ $i }}">{{ $i }}</option>
                            @endfor
                        </select>
                    </div>
                </div>

                <div class="import-info-box full-width">
                    <p>
                        <i class="fa-solid fa-circle-info" style="margin-right: 5px;"></i>
                        <strong>Importante:</strong> O sistema identificará automaticamente as colunas de Nome, CPF e RA. Certifique-se de que o arquivo contém os dados dos atiradores.
                    </p>
                </div>
            </div>

            <button type="submit" class="btn btn-primary" id="btnSubmitImport" style="width: 100%; margin-top: 30px; padding: 1rem; display: flex; align-items: center; justify-content: center; gap: 10px;">
                <i class="fa-solid fa-check-double"></i>
                <span>PROCESSAR IMPORTAÇÃO</span>
            </button>
        </form>
    </div>
</div>

<!-- Modal Criar -->
<div id="modalNovoAtirador" class="modal-background">
    <div class="modal-content-card">
        <button onclick="closeModal('modalNovoAtirador')" class="modal-close-btn"><i class="fa-solid fa-xmark"></i></button>
        <h3 class="modal-title">Novo Atirador</h3>
        <p class="modal-subtitle">Preencha os dados oficiais do cadete.</p>
        <form action="{{ route('atiradores.store') }}" method="POST">
            @csrf
            <div class="form-grid">
                <div class="input-wrapper full-width">
                    <label>Nome Completo</label>
                    <input type="text" name="nome" required class="input-field" placeholder="Ex: JOÃO DA SILVA">
                </div>
                <div class="input-wrapper full-width">
                    <label>Nome de Guerra</label>
                    <input type="text" name="nome_de_guerra" required class="input-field" placeholder="Ex: SILVA">
                </div>
                <div class="input-wrapper">
                    <label>Número</label>
                    <input type="number" name="numero" required min="1" max="150" class="input-field">
                </div>
                <div class="input-wrapper">
                    <label>Turma (Ano)</label>
                    <select name="turma" required class="input-field">
                        @for ($i = date('Y') - 1; $i <= date('Y') + 2; $i++)
                            <option value="{{ $i }}" {{ date('Y') == $i ? 'selected' : '' }}>{{ $i }}</option>
                        @endfor
                    </select>
                </div>
                <div class="input-wrapper">
                    <label>CPF (apenas números)</label>
                    <input type="text" name="cpf" required maxlength="11" class="input-field">
                </div>
                <div class="input-wrapper">
                    <label>RA (apenas números)</label>
                    <input type="text" name="ra" required maxlength="12" class="input-field">
                </div>
                <div class="input-wrapper full-width">
                    <label>Telefone (Opcional)</label>
                    <input type="text" name="telefone" class="input-field" placeholder="(17) 99... ">
                </div>
            </div>
            <button type="submit" class="btn btn-primary" style="width: 100%; margin-top: 25px; padding: 14px;">SALVAR ATIRADOR</button>
        </form>
    </div>
</div>

<!-- Modal Editar -->
<div id="modalEditAtirador" class="modal-background">
    <div class="modal-content-card">
        <button onclick="closeModal('modalEditAtirador')" class="modal-close-btn"><i class="fa-solid fa-xmark"></i></button>
        <h3 class="modal-title">Editar Atirador</h3>
        <p class="modal-subtitle">Atualize as informações do cadete.</p>
        <form id="editForm" method="POST" enctype="multipart/form-data">
            @csrf @method('PUT')
            <div class="form-grid">
                <div class="input-wrapper full-width">
                    <label>Nome Completo</label>
                    <input type="text" name="name" id="edit_name" required class="input-field">
                </div>
                <div class="input-wrapper full-width">
                    <label>Nome de Guerra</label>
                    <input type="text" name="nome_de_guerra" id="edit_nome_de_guerra" required class="input-field">
                </div>
                <div class="input-wrapper">
                    <label>Número</label>
                    <input type="number" name="numero" id="edit_numero" required class="input-field">
                </div>
                <div class="input-wrapper">
                    <label>Turma</label>
                    <input type="number" name="turma" id="edit_turma" required class="input-field">
                </div>
                <div class="input-wrapper">
                    <label>CPF</label>
                    <input type="text" name="cpf" id="edit_cpf" required maxlength="11" class="input-field">
                </div>
                <div class="input-wrapper">
                    <label>RA</label>
                    <input type="text" name="ra" id="edit_ra" required maxlength="12" class="input-field">
                </div>
                <div class="input-wrapper full-width">
                    <label>Telefone</label>
                    <input type="text" name="telefone" id="edit_telefone" class="input-field">
                </div>
                <div class="input-wrapper full-width">
                    <label>Nova Senha (deixe em branco para manter)</label>
                    <input type="password" name="password" class="input-field">
                </div>
                <div class="input-wrapper full-width">
                    <label>Confirmar Nova Senha</label>
                    <input type="password" name="password_confirmation" class="input-field">
                </div>
                <div class="input-wrapper full-width">
                    <label>Foto de Perfil (3x4)</label>
                    <input type="file" name="photo" class="input-field">
                </div>
                <div class="input-wrapper full-width" style="flex-direction: row; align-items: center; gap: 10px;">
                    <input type="checkbox" name="is_cfc" id="edit_cfc" value="1" style="width: 20px; height: 20px;">
                    <label for="edit_cfc" style="margin: 0;">Está fazendo o CFC</label>
                </div>
            </div>
            <button type="submit" class="btn btn-primary" style="width: 100%; margin-top: 25px; padding: 14px;">ATUALIZAR DADOS</button>
        </form>
    </div>
</div>

<script>
    function openCreateModal() {
        document.getElementById('modalNovoAtirador').style.display = 'flex';
    }
    
    function openEditModal(atirador) {
        const form = document.getElementById('editForm');
        form.action = `/atiradores/${atirador.id}`;
        
        document.getElementById('edit_name').value = atirador.name;
        document.getElementById('edit_nome_de_guerra').value = atirador.nome_de_guerra || '';
        document.getElementById('edit_numero').value = atirador.numero;
        document.getElementById('edit_turma').value = atirador.turma;
        document.getElementById('edit_cpf').value = atirador.cpf;
        document.getElementById('edit_ra').value = atirador.ra;
        document.getElementById('edit_telefone').value = atirador.telefone || '';
        document.getElementById('edit_cfc').checked = !!atirador.is_cfc;
        
        document.getElementById('modalEditAtirador').style.display = 'flex';
    }

    function openImportModal() {
        document.getElementById('modalImportExcel').style.display = 'flex';
    }

    function openDeleteModal(atirador) {
        const form = document.getElementById('deleteForm');
        form.action = `/atiradores/${atirador.id}`;
        document.getElementById('delete_warning_text').innerHTML = `Tem certeza que deseja remover o atirador <strong>${atirador.nome_de_guerra || atirador.name}</strong> do sistema?`;
        document.getElementById('modalDeleteAtirador').style.display = 'flex';
    }
    
    function closeModal(id) {
        document.getElementById(id).style.display = 'none';
    }

    function updateFileName(input) {
        const area = document.getElementById('fileDropArea');
        const msg = area.querySelector('.file-msg');
        const hint = area.querySelector('.file-hint');
        
        if (input.files && input.files[0]) {
            msg.innerHTML = `<i class="fa-solid fa-file-circle-check" style="font-size: 1.2rem; vertical-align: middle; margin-right: 8px;"></i> ${input.files[0].name}`;
            msg.style.color = 'var(--primary-olive)';
            hint.innerHTML = 'Arquivo pronto para processamento.';
            area.style.borderColor = 'var(--primary-olive)';
            area.style.background = '#f0f4f1';
        }
    }

    document.getElementById('importForm').onsubmit = function() {
        const btn = document.getElementById('btnSubmitImport');
        btn.disabled = true;
        btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> PROCESSANDO...';
        btn.style.opacity = '0.7';
    };

    function toggleCfc(button, id) {
        button.style.opacity = '0.5';
        button.disabled = true;

        fetch(`/atiradores/${id}/toggle-cfc`, {
            method: 'PATCH',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                if (data.is_cfc) {
                    button.classList.remove('badge-inactive');
                    button.classList.add('badge-active');
                    button.innerText = 'CFC ATIVO';
                } else {
                    button.classList.remove('badge-active');
                    button.classList.add('badge-inactive');
                    button.innerText = 'NÃO CFC';
                }
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Erro ao atualizar status.');
        })
        .finally(() => {
            button.style.opacity = '1';
            button.disabled = false;
        });
    }

    window.onclick = function(e) {
        if (e.target.classList.contains('modal-background')) {
            e.target.style.display = 'none';
        }
    }
</script>
@endsection