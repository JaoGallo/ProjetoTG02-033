@extends('layouts.app')

@section('title', 'Gerenciar Avisos')

@section('styles')
<style>
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
        max-width: 650px;
        max-height: 90vh;
        overflow-y: auto;
        padding: 35px;
        border-radius: 16px;
        box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.4);
        position: relative;
        border: none;
        color: #1a1a1a;
    }

    .modal-content-card::-webkit-scrollbar { width: 6px; }
    .modal-content-card::-webkit-scrollbar-thumb { background: #e5e7eb; border-radius: 10px; }

    .input-field {
        width: 100%;
        padding: 12px 16px;
        background: #f9fafb;
        border: 1px solid #d1d5db;
        border-radius: 8px;
        margin-top: 5px;
    }

    .category-tag {
        padding: 4px 10px;
        border-radius: 50px;
        font-size: 0.7rem;
        font-weight: 700;
        text-transform: uppercase;
    }

    .tag-geral { background: #f3f4f6; color: #4b5563; }
    .tag-urgente { background: #fee2e2; color: #b91c1c; }
    .tag-escala { background: #eff6ff; color: #1e40af; }
    .tag-instrucao { background: #ecfdf5; color: #047857; }
</style>
@endsection

@section('content')
<div class="header-actions" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px;">
    <div>
        <h2 style="font-weight: 800; color: var(--primary-olive-dark);">Gestão de Avisos</h2>
        <p style="color: var(--text-secondary); font-size: 0.9rem;">Comunicação oficial com a tropa</p>
    </div>
    <button class="btn btn-primary" onclick="openModal('modalNovoAviso')">
        <i class="fa-solid fa-plus"></i> Novo Aviso
    </button>
</div>

@if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif

<div class="card" style="background: white; padding: 0; border-radius: 12px; border: 1px solid var(--border-color); overflow: hidden;">
    <div style="overflow-x: auto;">
        <table style="width: 100%; border-collapse: collapse; text-align: left;">
            <thead>
                <tr style="background: #f9fafb; border-bottom: 2px solid var(--border-color);">
                    <th style="padding: 15px 20px; font-size: 0.75rem; color: #6b7280; text-transform: uppercase;">Aviso</th>
                    <th style="padding: 15px 20px; font-size: 0.75rem; color: #6b7280; text-transform: uppercase;">Categoria</th>
                    <th style="padding: 15px 20px; font-size: 0.75rem; color: #6b7280; text-transform: uppercase;">Visto por</th>
                    <th style="padding: 15px 20px; font-size: 0.75rem; color: #6b7280; text-transform: uppercase; text-align: right;">Ações</th>
                </tr>
            </thead>
            <tbody>
                @forelse($announcements as $aviso)
                <tr style="border-bottom: 1px solid #f3f4f6;">
                    <td style="padding: 15px 20px;">
                        <div style="font-weight: 700; color: #111827;">{{ $aviso->title }}</div>
                        <div style="font-size: 0.75rem; color: #9ca3af;">{{ $aviso->created_at->format('d/m/Y H:i') }} • Por {{ $aviso->author->name }}</div>
                    </td>
                    <td style="padding: 15px 20px;">
                        <span class="category-tag tag-{{ $aviso->category }}">
                            {{ $aviso->category }}
                        </span>
                        @if($aviso->priority)
                            <span style="color: #ef4444; margin-left: 5px;"><i class="fa-solid fa-bolt"></i></span>
                        @endif
                    </td>
                    <td style="padding: 15px 20px;">
                        <div style="display: flex; align-items: center; gap: 8px;">
                            <div style="background: #f3f4f6; padding: 2px 8px; border-radius: 4px; font-size: 0.8rem; font-weight: 600;">
                                {{ $aviso->readers->count() }}
                            </div>
                            <small style="color: #9ca3af;">visualizações</small>
                        </div>
                    </td>
                    <td style="padding: 15px 20px; text-align: right;">
                        <div style="display: flex; justify-content: flex-end; gap: 8px;">
                            <a href="{{ route('avisos.show', $aviso->id) }}" class="btn-icon" style="background: #f3f4f6; color: #4b5563; width: 32px; height: 32px; display: flex; align-items: center; justify-content: center; border-radius: 8px; text-decoration: none;">
                                <i class="fa-solid fa-eye"></i>
                            </a>
                            <form action="{{ route('avisos.destroy', $aviso->id) }}" method="POST" onsubmit="return confirm('Excluir este aviso?')">
                                @csrf @method('DELETE')
                                <button type="submit" style="background: #fee2e2; color: #b91c1c; border: none; width: 32px; height: 32px; border-radius: 8px; cursor: pointer;">
                                    <i class="fa-solid fa-trash"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="4" style="padding: 40px; text-align: center; color: #9ca3af;">Nenhum aviso publicado.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- Modal Novo Aviso -->
<div id="modalNovoAviso" class="modal-background">
    <div class="modal-content-card">
        <button onclick="closeModal('modalNovoAviso')" style="position: absolute; right: 25px; top: 25px; border: none; background: none; cursor: pointer; font-size: 1.2rem; color: #9ca3af;"><i class="fa-solid fa-xmark"></i></button>
        <h3 style="font-weight: 800; margin-bottom: 5px;">Publicar Novo Aviso</h3>
        <p style="color: #6b7280; font-size: 0.9rem; margin-bottom: 25px;">O aviso será enviado para todos os atiradores da turma de {{ date('Y') }}.</p>
        
        <form action="{{ route('avisos.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div style="display: flex; flex-direction: column; gap: 20px;">
                <div>
                    <label style="font-weight: 700; font-size: 0.8rem; color: #374151; text-transform: uppercase;">Título do Aviso</label>
                    <input type="text" name="title" required class="input-field" placeholder="Ex: Instrução de Tiro AMANHÃ">
                </div>
                
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                    <div>
                        <label style="font-weight: 700; font-size: 0.8rem; color: #374151; text-transform: uppercase;">Categoria</label>
                        <select name="category" required class="input-field">
                            <option value="geral">Geral</option>
                            <option value="urgente">Urgente</option>
                            <option value="escala">Escala</option>
                            <option value="instrucao">Instrução</option>
                        </select>
                    </div>
                    <div style="display: flex; align-items: flex-end; padding-bottom: 15px;">
                        <label style="display: flex; align-items: center; gap: 10px; cursor: pointer;">
                            <input type="checkbox" name="priority" value="1" style="width: 20px; height: 20px;">
                            <span style="font-weight: 700; font-size: 0.8rem; color: #b91c1c;">MARCAR COMO URGENTE</span>
                        </label>
                    </div>
                </div>

                <div>
                    <label style="font-weight: 700; font-size: 0.8rem; color: #374151; text-transform: uppercase;">Conteúdo do Aviso</label>
                    <textarea name="content" required class="input-field" style="min-height: 150px; resize: vertical;" placeholder="Descreva os detalhes do aviso aqui..."></textarea>
                </div>

                <div>
                    <label style="font-weight: 700; font-size: 0.8rem; color: #374151; text-transform: uppercase;">Anexo (PDF, Imagem ou DOC)</label>
                    <input type="file" name="attachment" class="input-field">
                    <p style="font-size: 0.7rem; color: #9ca3af; margin-top: 5px;">Tamanho máximo: 5MB</p>
                </div>
            </div>

            <button type="submit" class="btn btn-primary" style="width: 100%; margin-top: 30px; padding: 15px; font-weight: 800;">PUBLICAR E ENVIAR E-MAIL</button>
        </form>
    </div>
</div>

<script>
    function openModal(id) { document.getElementById(id).style.display = 'flex'; }
    function closeModal(id) { document.getElementById(id).style.display = 'none'; }
    window.onclick = function(e) { if(e.target.classList.contains('modal-background')) e.target.style.display = 'none'; }
</script>
@endsection
