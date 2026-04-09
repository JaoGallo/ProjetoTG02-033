@extends('layouts.app')

@section('title', 'Meu Perfil')

@section('content')
    <!-- Mensagens de Sucesso/Erro -->
    @if(session('success'))
        <div class="alert alert-success">
            <i class="fa-solid fa-circle-check"></i>
            {{ session('success') }}
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger" id="errorAlert">
            <i class="fa-solid fa-circle-xmark"></i>
            <div>
                <ul style="margin: 0; padding-left: 1.25rem;">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    @endif

    <div class="profile-grid-layout">
        <!-- Coluna de Informações Fixas (Dados e Stats) -->
        <div style="display: flex; flex-direction: column; gap: 1.5rem;">
            <div class="profile-card">
                <div class="profile-section-title">
                    <i class="fa-solid fa-address-card"></i>
                    <span>Dados do Militar</span>
                </div>

                <div class="profile-info-horizontal">
                    <!-- Esquerda: Foto 3x4 -->
                    <div class="profile-photo-wrapper" style="margin: 0;">
                        <div class="photo-frame" id="profilePhotoFrame" title="Clique e segure para opções">
                            @if(Auth::user()->photo)
                                <img src="{{ asset('storage/' . Auth::user()->photo) }}" alt="Foto do Militar" id="mainPhoto">
                            @else
                                <div class="photo-placeholder">
                                    <i class="fa-solid fa-user-tie"></i>
                                    <span>Sem Foto 3x4</span>
                                </div>
                            @endif

                            <!-- Overlay de Ações -->
                            <div class="photo-overlay">
                                <a href="{{ Auth::user()->photo ? asset('storage/' . Auth::user()->photo) : '#' }}"
                                    download="foto_atirador_{{ Auth::user()->ra }}.jpg" class="photo-action-btn"
                                    title="Baixar Foto">
                                    <i class="fa-solid fa-download"></i>
                                </a>

                                @php
                                    $canEditPhoto = (Auth::user()->role === 'master' || Auth::user()->role === 'instructor');
                                    if (Auth::user()->role === 'atirador' || Auth::user()->role === 'monitor')
                                        $canEditPhoto = false;
                                @endphp

                                @if($canEditPhoto)
                                    <button type="button" class="photo-action-btn edit-btn" id="triggerUpload"
                                        title="Alterar Foto">
                                        <i class="fa-solid fa-camera"></i>
                                    </button>
                                    <form id="photoUploadForm" action="{{ route('profile.photo', Auth::user()->id) }}"
                                        method="POST" enctype="multipart/form-data" style="display: none;">
                                        @csrf
                                        <input type="file" name="photo" id="photoInput" accept="image/*"
                                            onchange="document.getElementById('photoUploadForm').submit()">
                                    </form>
                                @endif
                            </div>
                        </div>
                        <div class="mobile-photo-hint">Segure para opções</div>
                    </div>

                    <!-- Direita: Informações -->
                    <div style="flex: 1;">
                        <div class="info-row">
                            <div class="info-label">Nome</div>
                            <div class="info-value">{{ Auth::user()->name }}</div>
                        </div>

                        <div class="info-row">
                            <div class="info-label">RA</div>
                            <div class="info-value">{{ Auth::user()->ra }}</div>
                        </div>

                        <div class="info-row">
                            <div class="info-label">CPF</div>
                            @php
                                $cpf = Auth::user()->cpf;
                                $maskedCpf = substr($cpf, 0, 3) . '.***.***-' . substr($cpf, -2);
                            @endphp
                            <div class="info-value">{{ $maskedCpf }}</div>
                        </div>

                        <div class="info-row" style="border-bottom: none;">
                            <div class="info-label">Posto</div>
                            <div class="info-value">
                                <span class="badge" style="background: var(--primary-olive); opacity: 0.8;">
                                    {{ Auth::user()->role }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="profile-card">
                <div class="profile-section-title">
                    <i class="fa-solid fa-shield-halved"></i>
                    <span>Segurança da Conta</span>
                </div>
                <p style="font-size: 0.85rem; color: var(--text-secondary); margin-bottom: 1.5rem;">
                    Recomendamos trocar sua senha periodicamente para manter sua conta segura.
                </p>
                <button type="button" class="btn-primary" id="openPasswordModal" style="width: auto;">
                    <i class="fa-solid fa-key" style="margin-right: 8px;"></i> Alterar Senha
                </button>
            </div>
        </div>

        <!-- Coluna de Pontuação e Configurações -->
        <div style="display: flex; flex-direction: column; gap: 1.5rem;">
            <div class="profile-card">
                <div class="profile-section-title">
                    <i class="fa-solid fa-chart-line"></i>
                    <span>Sistema de Pontuação</span>
                </div>

                <div style="display: flex; flex-direction: column; gap: 1.5rem;">
                    <!-- Contagem de Faltas (Unitária) -->
                    <div style="background: #f8faf9; padding: 1.25rem; border-radius: 12px; border: 1px solid #edf2ef;">
                        <div
                            style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.5rem;">
                            <span
                                style="font-weight: 600; font-size: 0.85rem; color: var(--text-secondary); text-transform: uppercase;">Total
                                de Faltas</span>
                            <span
                                style="font-weight: 900; font-size: 1.5rem; color: var(--text-primary);">{{ Auth::user()->faults }}</span>
                        </div>
                        <p style="margin: 0; font-size: 0.75rem; color: var(--text-secondary);">Contagem unitária de
                            ausências registradas.</p>
                    </div>

                    <!-- Contagem de Pontos (Máximo 120) -->
                    <div style="background: #fff5f5; padding: 1.25rem; border-radius: 12px; border: 1px solid #fed7d7;">
                        <div
                            style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.5rem;">
                            <span
                                style="font-weight: 600; font-size: 0.85rem; color: #c53030; text-transform: uppercase;">Pontos
                                de Falta</span>
                            <span style="font-weight: 900; font-size: 1.5rem; color: #c53030;">{{ Auth::user()->points }}
                                <small style="font-size: 0.9rem; font-weight: 500; color: #9b2c2c;">/ 120</small></span>
                        </div>
                        <div class="progress-container">
                            @php
                                $percent = min(100, (Auth::user()->points / 120) * 100);
                            @endphp
                            <div class="progress-bar" style="width: {{ $percent }}%; background: #c53030;"></div>
                        </div>
                        <p style="margin-top: 0.75rem; font-size: 0.75rem; color: #9b2c2c;">Com <strong>120 pontos</strong>
                            o militar é passível de dispensa conforme o regulamento.</p>
                    </div>
                </div>
            </div>

            <div class="profile-card">
                <div class="profile-section-title">
                    <i class="fa-solid fa-envelope"></i>
                    <span>Contato</span>
                </div>
                <form action="{{ route('profile.update') }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="input-group" style="margin-bottom: 1rem;">
                        <label for="email">E-mail Cadastrado</label>
                        <input type="email" id="email" name="email" value="{{ Auth::user()->email }}" required>
                    </div>
                    <button type="submit" class="btn-primary" style="width: 100%;">Atualizar E-mail</button>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal para Alteração de Senha -->
    <div class="modal-overlay" id="passwordModal">
        <div class="modal-card">
            <div class="modal-header">
                <h3 style="margin: 0; color: var(--primary-olive-dark);">Alterar Senha</h3>
                <button type="button" class="modal-close" id="closePasswordModal">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>

            <form action="{{ route('profile.update') }}" method="POST">
                @csrf
                @method('PUT')
                <!-- Manter o e-mail no form para validação do controller -->
                <input type="hidden" name="email" value="{{ Auth::user()->email }}">

                <div class="input-group">
                    <label for="current_password">Senha Atual</label>
                    <input type="password" id="current_password" name="current_password" required
                        placeholder="Digite sua senha atual">
                </div>

                <div class="input-group">
                    <label for="password">Nova Senha</label>
                    <input type="password" id="password" name="password" required placeholder="Mínimo 6 caracteres">
                </div>

                <div class="input-group">
                    <label for="password_confirmation">Confirmar Nova Senha</label>
                    <input type="password" id="password_confirmation" name="password_confirmation" required
                        placeholder="Repita a nova senha">
                </div>

                <div style="margin-top: 2rem; display: flex; gap: 1rem;">
                    <button type="button" class="btn-primary" id="cancelModal"
                        style="background: #e2e8f0; color: #4a5568; flex: 1;">Cancelar</button>
                    <button type="submit" class="btn-primary" style="flex: 1;">Salvar Senha</button>
                </div>
            </form>
        </div>
    </div>

@endsection

@section('scripts')
    <script>
        const modal = document.getElementById('passwordModal');
        const openBtn = document.getElementById('openPasswordModal');
        const closeBtn = document.getElementById('closePasswordModal');
        const cancelBtn = document.getElementById('cancelModal');
        const errorAlert = document.getElementById('errorAlert');

        function openModal() {
            modal.classList.add('active');
            document.body.style.overflow = 'hidden';
        }

        function closeModal() {
            modal.classList.remove('active');
            document.body.style.overflow = 'auto';
        }

        openBtn.addEventListener('click', openModal);
        closeBtn.addEventListener('click', closeModal);
        cancelBtn.addEventListener('click', closeModal);

        // Fechar ao clicar fora
        modal.addEventListener('click', (e) => {
            if (e.target === modal) closeModal();
        });

        // Se houver erro de senha (current_password ou password), abrir o modal automaticamente
        @if($errors->has('current_password') || $errors->has('password'))
            openModal();
        @endif
        // Long Press para Mobile na Foto
        const photoFrame = document.getElementById('profilePhotoFrame');
        const triggerUpload = document.getElementById('triggerUpload');
        const photoInput = document.getElementById('photoInput');
        let pressTimer;

        if (photoFrame) {
            photoFrame.addEventListener('touchstart', (e) => {
                pressTimer = window.setTimeout(() => {
                    photoFrame.classList.toggle('show-actions');
                }, 600); // 600ms para considerar long press
            });

            photoFrame.addEventListener('touchend', (e) => {
                clearTimeout(pressTimer);
            });

            // Clique no ícone de upload (disparar o input escondido)
            if (triggerUpload && photoInput) {
                triggerUpload.addEventListener('click', (e) => {
                    e.stopPropagation();
                    photoInput.click();
                });
            }
        }
    </script>
@endsection