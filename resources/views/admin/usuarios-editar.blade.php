@extends('layouts.app')
@section('title', 'Editar Usuario')
@section('content')

<style>
    .edit-container {
        max-width: 800px;
        margin: 0 auto;
    }
    
    .card {
        background: #fff;
        border-radius: 16px;
        border: 1px solid #e2e8f0;
        overflow: hidden;
        box-shadow: 0 1px 3px rgba(0,0,0,0.06);
    }
    
    .card-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 1.2rem 1.6rem;
        border-bottom: 1px solid #f1f5f9;
        background: #fafafa;
    }
    
    .card-title {
        font-size: 1rem;
        font-weight: 700;
        color: #0f172a;
    }
    
    .card-sub {
        font-size: 0.78rem;
        color: #94a3b8;
        margin-top: 2px;
    }
    
    .card-body {
        padding:0.8rem 1.6rem;
    }
    
    .form-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 1rem;
    }
    
    .form-field {
        display: flex;
        flex-direction: column;
        gap: 4px;
    }
    
    .form-field.full {
        grid-column: 1 / -1;
    }
    
    .form-field label {
        font-size: 0.72rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        color: #64748b;
    }
    
    .form-field label .required {
        color: #ef4444;
    }
    
    .form-field input,
    .form-field select {
        padding: 0.6rem 0.8rem;
        border: 1.5px solid #e2e8f0;
        border-radius: 10px;
        font-size: 0.88rem;
        color: #0f172a;
        background: #f8fafc;
        outline: none;
        transition: all 0.15s;
        font-family: inherit;
        width: 100%;
        box-sizing: border-box;
    }
    
    .form-field input:focus,
    .form-field select:focus {
        border-color: #3C4E3E;
        background: #fff;
        box-shadow: 0 0 0 3px rgba(60, 78, 62, 0.1);
    }
    
    .form-field input::placeholder {
        color: #94a3b8;
    }
    
    .form-actions {
        display: flex;
        gap: 0.8rem;
        padding-top: 1.2rem;
        border-top: 1px solid #f1f5f9;
        margin-top: 0.5rem;
    }
    
    .btn {
        display: inline-flex;
        align-items: center;
        gap: 0.4rem;
        padding: 0.6rem 1.2rem;
        border-radius: 10px;
        font-weight: 600;
        font-size: 0.8rem;
        border: none;
        transition: all 0.2s;
        cursor: pointer;
        text-decoration: none;
    }
    
    .btn-primary {
        background: #3C4E3E;
        color: #fff;
    }
    
    .btn-primary:hover {
        background: #2d3d2f;
        transform: translateY(-2px);
        box-shadow: 0 8px 16px rgba(60, 78, 62, 0.25);
    }
    
    .btn-secondary {
        background: #f1f5f9;
        color: #475569;
    }
    
    .btn-secondary:hover {
        background: #e2e8f0;
        transform: translateY(-2px);
    }
    
    .btn-sm {
        font-size: 0.75rem;
        padding: 0.45rem 1rem;
    }
    
    .alert {
        display: flex;
        align-items: flex-start;
        gap: 0.75rem;
        padding: 0.9rem 1.2rem;
        border-radius: 12px;
        margin-bottom: 1.2rem;
    }
    
    .alert-error {
        background: #fef2f2;
        border: 1px solid #fca5a5;
        color: #991b1b;
    }
    
    .alert-error i {
        color: #dc2626;
        font-size: 18px;
        flex-shrink: 0;
        margin-top: 2px;
    }
    
    .alert-error ul {
        margin: 0;
        padding: 0;
        list-style: none;
    }
    
    .alert-error ul li {
        font-size: 0.85rem;
    }
    
    /* ── Avatar preview ── */
    .avatar-preview-row {
        display: flex;
        align-items: center;
        gap: 1rem;
        flex-wrap: wrap;
    }
    
    .avatar-preview-row .avatar-wrap {
        position: relative;
        flex-shrink: 0;
    }
    
    .avatar-preview-row .avatar-wrap .avatar-img {
        width: 64px;
        height: 64px;
        border-radius: 50%;
        object-fit: cover;
        border: 2px solid #e2e8f0;
        cursor: pointer;
        transition: all 0.2s;
    }
    
    .avatar-preview-row .avatar-wrap .avatar-img:hover {
        border-color: #3C4E3E;
        transform: scale(1.05);
    }
    
    .avatar-preview-row .avatar-wrap .avatar-placeholder {
        width: 64px;
        height: 64px;
        border-radius: 50%;
        background: #f1f5f9;
        color: #94a3b8;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 11px;
        text-align: center;
        border: 2px dashed #cbd5e1;
    }
    
    .avatar-preview-row .avatar-info {
        flex: 1;
    }
    
    .avatar-preview-row .avatar-info .avatar-name {
        font-size: 0.88rem;
        font-weight: 600;
        color: #0f172a;
    }
    
    .avatar-preview-row .avatar-info .avatar-hint {
        font-size: 0.72rem;
        color: #94a3b8;
        margin-top: 2px;
    }
    
    .avatar-preview-row .avatar-actions {
        display: flex;
        flex-direction: column;
        gap: 4px;
    }
    
    .avatar-preview-row .avatar-actions .file-label {
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 0.4rem 0.9rem;
        background: #e8ece9;
        color: #3C4E3E;
        border-radius: 8px;
        font-size: 0.72rem;
        font-weight: 600;
        transition: all 0.15s;
        border: none;
    }
    
    .avatar-preview-row .avatar-actions .file-label:hover {
        background: #d4dcd5;
        transform: translateY(-1px);
    }
    
    .avatar-preview-row .avatar-actions .file-name {
        font-size: 0.68rem;
        color: #94a3b8;
        text-align: center;
    }
    
    .avatar-preview-row .avatar-actions input[type="file"] {
        display: none;
    }
    
    /* ── Lightbox ── */
    .lightbox {
        display: none;
        position: fixed;
        inset: 0;
        background: rgba(15, 23, 42, 0.85);
        z-index: 1000;
        align-items: center;
        justify-content: center;
        cursor: zoom-out;
        backdrop-filter: blur(4px);
    }
    
    .lightbox.active {
        display: flex;
    }
    
    .lightbox .close-btn {
        position: absolute;
        top: 24px;
        right: 28px;
        background: rgba(255, 255, 255, 0.1);
        border: 1px solid rgba(255, 255, 255, 0.1);
        color: #fff;
        width: 44px;
        height: 44px;
        border-radius: 50%;
        font-size: 24px;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.2s;
    }
    
    .lightbox .close-btn:hover {
        background: rgba(255, 255, 255, 0.2);
    }
    
    .lightbox .lightbox-img {
        max-width: 90vw;
        max-height: 85vh;
        border-radius: 16px;
        box-shadow: 0 20px 60px rgba(0, 0, 0, 0.5);
    }
    
    @media (max-width: 640px) {
        .form-grid {
            grid-template-columns: 1fr;
        }
        .card-header {
            flex-direction: column;
            align-items: flex-start;
            gap: 0.8rem;
        }
        .card-body {
            padding: 1rem;
        }
        .form-actions {
            flex-direction: column;
        }
        .form-actions .btn {
            justify-content: center;
        }
        .avatar-preview-row {
            flex-direction: column;
            align-items: flex-start;
        }
    }
</style>


    <div class="page-header" style="display:flex;justify-content:space-between;align-items:flex-start;margin-bottom:1.2rem">
        <div>
            <div class="page-title">Editar Usuario</div>
            <div class="page-sub">Actualiza los datos de {{ $user->name }}</div>
        </div>
        <a href="{{ route('admin.usuarios') }}" class="btn btn-secondary btn-sm">
            <i class="ti ti-arrow-left" style="font-size:14px"></i> Volver
        </a>
    </div>


<div class="edit-container">

    @if($errors->any())
        <div class="alert alert-error">
            <i class="ti ti-alert-circle"></i>
            <ul>
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="card">
        <div class="card-header">
            <div>
                <div class="card-title">
                    <i class="ti ti-edit" style="font-size:16px;color:#3C4E3E;margin-right:6px"></i>
                    Editar usuario
                </div>
                <div class="card-sub">Modifica los datos del usuario seleccionado</div>
            </div>
        </div>

        <div class="card-body">
            <form action="{{ route('admin.usuarios.update', $user) }}" enctype="multipart/form-data" method="POST">
                @csrf
                @method('PUT')

                <div class="form-grid">

                    {{-- Avatar --}}
                    <div class="form-field full">
                        <label>Foto de perfil</label>
                        
                        <div class="avatar-preview-row">
                            <div class="avatar-wrap">
                                @if($user->avatar)
                                    @php
                                        $filename = basename($user->avatar);
                                    @endphp
                                    <img id="avatarPreview" 
                                         class="avatar-img" 
                                         src="{{ route('avatar.show', $filename) }}" 
                                         alt="Avatar de {{ $user->name }}"
                                         onclick="openLightbox(this.src)">
                                @else
                                    <div class="avatar-placeholder" id="avatarPlaceholder">
                                        Sin foto
                                    </div>
                                    <img id="avatarPreview" 
                                         class="avatar-img" 
                                         style="display:none"
                                         alt="Avatar">
                                @endif
                            </div>

                            <div class="avatar-info">
                                <div class="avatar-name">{{ $user->name }}</div>
                                <div class="avatar-hint" id="avatarFileName">
                                    @if($user->avatar)
                                        <i class="ti ti-check-circle" style="color:#3C4E3E;font-size:12px"></i>
                                        Tiene foto de perfil
                                    @else
                                        <i class="ti ti-info-circle" style="font-size:12px"></i>
                                        Sin foto de perfil
                                    @endif
                                </div>
                            </div>

                            <div class="avatar-actions">
                                <label for="avatar" class="file-label">
                                    <i class="ti ti-upload" style="font-size:13px"></i>
                                    <span id="avatarBtnText">Cambiar foto</span>
                                </label>
                                <input type="file" name="avatar" id="avatar" accept="image/*">
                                <span class="file-name" id="avatarStatus">Déjalo vacío para no cambiar</span>
                            </div>
                        </div>

                        @error('avatar')
                            <span class="error-text">{{ $message }}</span>
                        @enderror
                    </div>

                    {{-- Nombre --}}
                    <div class="form-field">
                        <label>Nombre completo <span class="required">*</span></label>
                        <input type="text" name="name" value="{{ old('name', $user->name) }}"
                               placeholder="Nombre del usuario" required>
                    </div>

                    {{-- Email --}}
                    <div class="form-field">
                        <label>Correo electrónico <span class="required">*</span></label>
                        <input type="email" name="email" value="{{ old('email', $user->email) }}"
                               placeholder="correo@ejemplo.com" required>
                    </div>

                    {{-- Contraseña --}}
                    <div class="form-field">
                        <label>Nueva contraseña</label>
                        <input type="password" name="password" placeholder="Déjalo vacío para no cambiarla">
                    </div>

                    {{-- Confirmar contraseña --}}
                    <div class="form-field">
                        <label>Confirmar nueva contraseña</label>
                        <input type="password" name="password_confirmation" placeholder="Repite la nueva contraseña">
                    </div>


                </div>

                
                    {{-- Rol --}}
                    <div class="form-field">
                        <label>Rol <span class="required">*</span></label>
                        <select style="width:100%;" name="role">
                            <option value="usuario" {{ old('role', $user->role) == 'usuario' ? 'selected' : '' }}>Usuario</option>
                            <option value="admin" {{ old('role', $user->role) == 'admin' ? 'selected' : '' }}>Administrador</option>
                        </select>
                    </div>

                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">
                        <i class="ti ti-device-floppy" style="font-size:15px"></i> Guardar cambios
                    </button>
                    <a href="{{ route('admin.usuarios') }}" class="btn btn-secondary">Cancelar</a>
                </div>

            </form>
        </div>
    </div>
</div>

{{-- ── LIGHTBOX ── --}}
<div class="lightbox" id="avatarLightbox" onclick="closeLightbox()">
    <button class="close-btn" onclick="closeLightbox()">
        <i class="ti ti-x"></i>
    </button>
    <img class="lightbox-img" id="avatarLightboxImg" src="" alt="Foto de perfil">
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const avatarInput = document.getElementById('avatar');
    const avatarPreview = document.getElementById('avatarPreview');
    const avatarPlaceholder = document.getElementById('avatarPlaceholder');
    const avatarFileName = document.getElementById('avatarFileName');
    const avatarStatus = document.getElementById('avatarStatus');
    const avatarBtnText = document.getElementById('avatarBtnText');

    // Lightbox
    const lightbox = document.getElementById('avatarLightbox');
    const lightboxImg = document.getElementById('avatarLightboxImg');

    window.openLightbox = function(src) {
        if (!src) return;
        lightboxImg.src = src;
        lightbox.classList.add('active');
        document.body.style.overflow = 'hidden';
    };

    window.closeLightbox = function() {
        lightbox.classList.remove('active');
        document.body.style.overflow = '';
    };

    // ESC para cerrar lightbox
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeLightbox();
        }
    });

    // Cambiar imagen
    avatarInput.addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (!file) return;

        if (file.size > 10 * 1024 * 1024) {
            alert('La imagen supera el tamaño máximo de 10MB.');
            e.target.value = '';
            return;
        }

        const validTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
        if (!validTypes.includes(file.type)) {
            alert('Formato no soportado. Usa JPG, PNG, GIF o WEBP.');
            e.target.value = '';
            return;
        }

        const reader = new FileReader();
        reader.onload = function(ev) {
            avatarPreview.src = ev.target.result;
            avatarPreview.style.display = 'block';
            avatarPreview.style.cursor = 'zoom-in';
            if (avatarPlaceholder) {
                avatarPlaceholder.style.display = 'none';
            }
        };
        reader.readAsDataURL(file);

        // Actualizar texto
        const sizeInKB = (file.size / 1024).toFixed(1);
        const sizeInMB = (file.size / (1024 * 1024)).toFixed(2);
        const sizeText = sizeInMB > 1 ? sizeInMB + ' MB' : sizeInKB + ' KB';
        
        avatarFileName.innerHTML = '<i class="ti ti-check-circle" style="color:#3C4E3E;font-size:12px"></i> ' + file.name;
        avatarStatus.textContent = sizeText + ' · Listo para subir';
        avatarBtnText.textContent = 'Cambiar foto';
    });

    // Clic en la imagen para abrir lightbox (si tiene foto)
    avatarPreview.addEventListener('click', function() {
        if (this.src && this.style.display !== 'none') {
            openLightbox(this.src);
        }
    });
});
</script>

@endsection