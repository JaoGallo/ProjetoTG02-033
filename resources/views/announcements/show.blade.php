@extends('layouts.app')

@section('title', $announcement->title)

@section('styles')
<style>
    .announcement-container {
        max-width: 800px;
        margin: 0 auto;
        background: white;
        padding: 40px;
        border-radius: 16px;
        box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1);
        border: 1px solid var(--border-color);
    }
    .back-link {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        text-decoration: none;
        color: var(--text-secondary);
        font-weight: 600;
        margin-bottom: 25px;
        transition: color 0.2s;
    }
    .back-link:hover { color: var(--primary-olive-dark); }
    
    .category-badge {
        padding: 6px 14px;
        border-radius: 50px;
        font-size: 0.8rem;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-bottom: 20px;
        display: inline-block;
    }
    
    .cat-geral { background: #f3f4f6; color: #4b5563; }
    .cat-urgente { background: #fee2e2; color: #b91c1c; }
    .cat-escala { background: #eff6ff; color: #1e40af; }
    .cat-instrucao { background: #ecfdf5; color: #047857; }

    .attachment-card {
        background: #f9fafb;
        border: 1px solid #e5e7eb;
        padding: 15px 20px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-top: 30px;
        text-decoration: none;
        color: inherit;
        transition: transform 0.2s;
    }
    .attachment-card:hover { transform: translateY(-2px); border-color: var(--primary-olive); }
</style>
@endsection

@section('content')
<a href="{{ route('dashboard') }}" class="back-link">
    <i class="fa-solid fa-arrow-left"></i> Voltar ao Dashboard
</a>

<div class="announcement-container">
    <div style="display: flex; justify-content: space-between; align-items: flex-start;">
        <div>
            @php $cat = $announcement->category; @endphp
            <span class="category-badge cat-{{ $cat }}">
                {{ $cat }}
            </span>
            <h1 style="font-size: 2.2rem; font-weight: 900; line-height: 1.1; margin-bottom: 10px; color: #111827;">{{ $announcement->title }}</h1>
            <p style="color: #6b7280; font-size: 0.95rem; margin-bottom: 30px;">
                Publicado em {{ $announcement->created_at->format('d/m/Y à\s H:i') }} • Por {{ $announcement->author->name }}
            </p>
        </div>
        
        @if($announcement->priority)
            <div style="background: #fee2e2; color: #ef4444; padding: 10px; border-radius: 12px; font-size: 1.2rem;">
                <i class="fa-solid fa-bolt"></i>
            </div>
        @endif
    </div>

    <div style="line-height: 1.8; color: #374151; font-size: 1.1rem; white-space: pre-wrap;">
        {{ $announcement->content }}
    </div>

    @if($announcement->attachment)
        <a href="{{ asset('storage/' . $announcement->attachment) }}" target="_blank" class="attachment-card">
            <div style="display: flex; align-items: center; gap: 15px;">
                <div style="background: white; width: 40px; height: 40px; display: flex; align-items: center; justify-content: center; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.05);">
                    <i class="fa-solid fa-file-pdf" style="color: #ef4444; font-size: 1.2rem;"></i>
                </div>
                <div>
                    <h4 style="margin: 0; font-size: 0.9rem; font-weight: 700;">Documento Anexo</h4>
                    <p style="margin: 0; font-size: 0.75rem; color: #9ca3af;">Clique para visualizar ou baixar</p>
                </div>
            </div>
            <i class="fa-solid fa-download" style="color: #9ca3af;"></i>
        </a>
    @endif
    
    @if(in_array(Auth::user()->role, ['master', 'instructor']))
        <div style="margin-top: 50px; padding-top: 30px; border-top: 1px solid #f3f4f6;">
            <h4 style="margin-bottom: 15px; font-size: 0.9rem; color: #6b7280; text-transform: uppercase; letter-spacing: 1px;">Rastreamento de Leitura</h4>
            
            <details style="background: #f9fafb; border-radius: 12px; border: 1px solid #e5e7eb; overflow: hidden;">
                <summary style="padding: 15px 20px; cursor: pointer; display: flex; align-items: center; justify-content: space-between; list-style: none; font-weight: 700;">
                    <div style="display: flex; align-items: center; gap: 12px;">
                        <span style="font-size: 1.3rem; color: var(--primary-olive-dark);">{{ $announcement->readers->count() }}</span>
                        <span style="font-size: 0.85rem; color: #4b5563;">Atiradores visualizaram este aviso</span>
                    </div>
                    <i class="fa-solid fa-chevron-down" style="font-size: 0.8rem; color: #9ca3af;"></i>
                </summary>
                
                <div style="padding: 0 20px 20px; max-height: 300px; overflow-y: auto;">
                    <table style="width: 100%; font-size: 0.85rem; border-collapse: collapse;">
                        <thead>
                            <tr style="text-align: left; border-bottom: 1px solid #e5e7eb; color: #9ca3af; text-transform: uppercase; font-size: 0.7rem;">
                                <th style="padding: 10px 0;">Atirador</th>
                                <th style="padding: 10px 0; text-align: right;">Visto em</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($announcement->readers->sortByDesc('pivot.viewed_at') as $reader)
                                <tr style="border-bottom: 1px solid #f3f4f6;">
                                    <td style="padding: 8px 0; font-weight: 600;">{{ $reader->nome_de_guerra ?: $reader->name }}</td>
                                    <td style="padding: 8px 0; text-align: right; color: #6b7280;">{{ \Carbon\Carbon::parse($reader->pivot->viewed_at)->format('d/m/Y H:i') }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="2" style="padding: 20px; text-align: center; color: #9ca3af;">Nenhuma visualização registrada ainda.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </details>
        </div>
    @endif
</div>
@endsection
