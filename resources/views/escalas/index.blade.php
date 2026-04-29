@extends('layouts.app')

@section('title', 'Gestão de Escalas (QTS)')

@section('content')
<div class="page-header header-responsive">
    <div>
        <h2>Escalas de Serviço (ADTs)</h2>
        <p>Gerencie os períodos de escala através dos Aditamentos semanais.</p>
    </div>
</div>

@if(session('success'))
<div class="alert alert-success" style="margin-bottom: 2rem;">
    <i class="fa-solid fa-circle-check"></i>
    {{ session('success') }}
</div>
@endif

<!-- Carrossel de ADTs -->
<div class="adt-carousel-wrapper">
    <div class="adt-carousel" id="adtCarousel">
        <!-- Card de Criação Rápida -->
        <a href="{{ route('escalas.criar') }}" class="adt-card create-card">
            <div class="create-icon">
                <i class="fa-solid fa-calendar-plus"></i>
            </div>
            <span>Criar Novo Aditamento</span>
        </a>

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
                @if(!$adt->data_fim->isPast())
                    <a href="{{ route('escalas.edit', $adt->id) }}" title="Editar Aditamento" style="color: #3b82f6;">
                        <i class="fa-solid fa-pencil"></i>
                    </a>
                @endif
                
                @if($adt->data_inicio->isFuture() || (auth()->check() && auth()->user()->role === 'master'))
                    <form action="{{ route('escalas.destroy', $adt->id) }}" method="POST" style="display:inline;" class="delete-adt-form">
                        @csrf
                        @method('DELETE')
                        <button type="submit" title="Excluir" style="background:none; border:none; color: #ef4444; padding:0; cursor:pointer; font-size:1.1rem; transition: transform 0.2s;">
                            <i class="fa-solid fa-trash"></i>
                        </button>
                    </form>
                @endif

                <a href="{{ route('escalas.pdf', $adt->data_inicio->format('Y-m-d')) }}" title="Gerar PDF" style="color: #10b981;">
                    <i class="fa-solid fa-file-pdf"></i>
                </a>
            </div>
        </div>
        @endforeach
    </div>
</div>

<style>
.header-responsive {
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

@endsection
