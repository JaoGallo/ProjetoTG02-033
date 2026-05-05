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
        <button class="btn btn-secondary" onclick="openImportModal()" style="display: flex; align-items: center; gap: 8px; background: #4b5563;">
            <i class="fa-solid fa-file-import"></i> Importar Excel
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
        <table style="width: 100%; border-collapse: collapse; text-align: left;">
            <thead>
                <tr style="background: #f9fafb; border-bottom: 2px solid var(--border-color);">
                    <th style="padding: 15px 20px; font-size: 0.75rem; color: #6b7280; text-transform: uppercase;">Nº</th>
                    <th style="padding: 15px 20px; font-size: 0.75rem; color: #6b7280; text-transform: uppercase;">Nome de Guerra</th>
                    <th style="padding: 15px 20px; font-size: 0.75rem; color: #6b7280; text-transform: uppercase;">Identificação</th>
                    <th style="padding: 15px 20px; font-size: 0.75rem; color: #6b7280; text-transform: uppercase;">CFC</th>
                    <th style="padding: 15px 20px; font-size: 0.75rem; color: #6b7280; text-transform: uppercase; text-align: right;">Ações</th>
                </tr>
            </thead>
            <tbody>
                @forelse($atiradores as $atirador)
                <tr style="border-bottom: 1px solid #f3f4f6;">
                    <td style="padding: 15px 20px; font-weight: 600; color: #9ca3af;">{{ str_pad($atirador->numero, 2, '0', STR_PAD_LEFT) }}</td>
                    <td style="padding: 15px 20px;">
                        <div style="font-weight: 800; color: #111827;">{{ mb_strtoupper($atirador->nome_de_guerra) }}</div>
                        <div style="font-size: 0.7rem; color: #9ca3af;">{{ $atirador->name }}</div>
                    </td>
                    <td style="padding: 15px 20px;">
                        <div style="font-size: 0.8rem;"><strong>CPF:</strong> {{ $atirador->cpf }}</div>
                        <div style="font-size: 0.8rem;"><strong>RA:</strong> {{ $atirador->ra }}</div>
                    </td>
                    <td style="padding: 15px 20px;">
                        <form action="{{ route('atiradores.toggle-cfc', $atirador->id) }}" method="POST">
                            @csrf @method('PATCH')
                            <button type="submit" class="cfc-badge {{ $atirador->is_cfc ? 'badge-active' : 'badge-inactive' }}">
                                {{ $atirador->is_cfc ? 'CFC ATIVO' : 'NÃO CFC' }}
                            </button>
                        </form>
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
        <form action="{{ route('atiradores.import') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="form-grid">
                <div class="input-wrapper full-width">
                    <label>Arquivo Excel</label>
                    <input type="file" name="excel_file" required class="input-field" accept=".xlsx,.xls,.csv">
                </div>
                <div class="input-wrapper full-width">
                    <label>Turma Destino (Ano)</label>
                    <select name="turma" required class="input-field">
                        @for ($i = date('Y'); $i <= date('Y') + 1; $i++)
                            <option value="{{ $i }}">{{ $i }}</option>
                        @endfor
                    </select>
                </div>
                <div class="full-width" style="background: #fdf2f2; padding: 12px; border-radius: 8px; border-left: 4px solid #ef4444; margin-top: 10px;">
                    <p style="font-size: 0.8rem; color: #991b1b; margin: 0;">
                        <i class="fa-solid fa-circle-info"></i> O sistema buscará automaticamente as colunas de Nome, CPF e RA.
                    </p>
                </div>
            </div>
            <button type="submit" class="btn btn-primary" style="width: 100%; margin-top: 25px; padding: 14px; background: #059669;">
                INICIAR IMPORTAÇÃO
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

    window.onclick = function(e) {
        if (e.target.classList.contains('modal-background')) {
            e.target.style.display = 'none';
        }
    }
</script>
@endsection