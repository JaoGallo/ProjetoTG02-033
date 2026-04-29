@extends('layouts.app')

@section('title', 'Gerenciar Feriados')

@section('content')
<div class="dashboard-container" style="max-width: 800px;">
    <div class="header-desktop" style="border-bottom: none; margin-bottom: 2rem;">
        <div>
            <a href="{{ route('escalas.index') }}" style="color: var(--primary-olive); font-size: 0.9rem; font-weight: 600; display: flex; align-items: center; gap: 0.5rem; margin-bottom: 1rem;">
                <i class="fa-solid fa-arrow-left"></i> Voltar ao Painel
            </a>
            <h2 style="margin: 0; color: var(--primary-olive-dark);">Feriados e Dispensas</h2>
            <p style="margin: 5px 0 0 0; color: var(--text-secondary);">Datas que congelam a rotação da escala.</p>
        </div>
    </div>

    @if(session('success'))
    <div class="alert alert-success">
        <i class="fa-solid fa-circle-check"></i>
        {{ session('success') }}
    </div>
    @endif

    <div class="profile-card" style="margin-bottom: 2rem;">
        <form action="{{ route('escalas.salvarFeriado') }}" method="POST" style="display: grid; grid-template-columns: 1fr 1fr auto; gap: 1rem; align-items: end;">
            @csrf
            <div class="input-group" style="margin-bottom: 0;">
                <label>Data</label>
                <input type="date" name="data" required>
            </div>
            <div class="input-group" style="margin-bottom: 0;">
                <label>Descrição (Opcional)</label>
                <input type="text" name="motivo" placeholder="Ex: Independência">
            </div>
            <button type="submit" class="btn-primary" style="padding: 0.75rem 1.5rem;">
                <i class="fa-solid fa-plus"></i> Adicionar
            </button>
        </form>
    </div>

    <div class="profile-card">
        <h3 style="margin-top: 0; margin-bottom: 1.5rem; font-size: 1rem;">Feriados Cadastrados</h3>
        <table class="matrix-table">
            <thead>
                <tr>
                    <th style="text-align: left;">Data</th>
                    <th style="text-align: left;">Descrição</th>
                    <th style="width: 80px;">Ações</th>
                </tr>
            </thead>
            <tbody>
                @forelse($feriados as $feriado)
                    <tr>
                        <td style="text-align: left; font-weight: 700;">{{ $feriado->data->format('d/m/Y') }} ({{ $feriado->data->translatedFormat('D') }})</td>
                        <td style="text-align: left;">{{ $feriado->motivo ?? 'Feriado' }}</td>
                        <td>
                            <form action="{{ route('escalas.deletarFeriado', $feriado) }}" method="POST" onsubmit="return confirm('Tem certeza que deseja remover este feriado?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" style="background: none; border: none; color: #ef4444; cursor: pointer; font-size: 1.1rem;">
                                    <i class="fa-solid fa-trash-can"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="3" style="text-align: center; padding: 2rem; color: var(--text-secondary);">Nenhum feriado cadastrado.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        
        <div style="margin-top: 2rem; background: #dbeafe; padding: 1rem; border-radius: 8px; color: #1e40af; font-size: 0.85rem; display: flex; gap: 1rem;">
            <i class="fa-solid fa-circle-info" style="margin-top: 3px;"></i>
            <p style="margin: 0;">
                <strong>Como funciona:</strong> Ao adicionar um feriado, a fila da escala <strong>congela</strong> nesse dia. 
                Ninguém assume serviço, e as posições dos atiradores permanecem as mesmas para o dia seguinte.
                <em>* Alterações em feriados exigem a regeneração da escala nas configurações para surtirem efeito.</em>
            </p>
        </div>
    </div>
</div>
@endsection
