@extends('layouts.app')
@section('title', 'Crear Usuario')
@section('content')

<style>
    .create-container {
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
        padding:0.5rem 1.6rem;
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
    
    /* ── File input personalizado ── */
    .file-upload-wrapper {
        position: relative;
        width: 100%;
    }
    
    .file-upload-wrapper input[type="file"] {
        position: absolute;
        opacity: 0;
        width: 100%;
        height: 100%;
        cursor: pointer;
        z-index: 2;
    }
    
    .file-upload-box {
        display: flex;
        align-items: center;
        gap: 0.8rem;
        padding: 0.6rem 1rem;
        border: 2px dashed #e2e8f0;
        border-radius: 10px;
        background: #f8fafc;
        transition: all 0.2s;
        min-height: 56px;
    }
    
    .file-upload-box:hover {
        border-color: #3C4E3E;
        background: #f1f5f9;
    }
    
    .file-upload-box .file-icon {
        width: 36px;
        height: 36px;
        border-radius: 8px;
        background: #e8ece9;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }
    
    .file-upload-box .file-icon i {
        font-size: 18px;
        color: #3C4E3E;
    }
    
    .file-upload-box .file-info {
        flex: 1;
        display: flex;
        flex-direction: column;
        gap: 2px;
    }
    
    .file-upload-box .file-info .file-title {
        font-size: 0.82rem;
        font-weight: 600;
        color: #0f172a;
    }
    
    .file-upload-box .file-info .file-hint {
        font-size: 0.7rem;
        color: #94a3b8;
    }
    
    .file-upload-box .file-info .file-name {
        font-size: 0.78rem;
        color: #3C4E3E;
        font-weight: 600;
        display: none;
    }
    
    .file-upload-box .file-info .file-name.active {
        display: block;
    }
    
    .file-upload-box .file-info .file-hint.hidden {
        display: none;
    }
    
    .file-upload-box .file-size {
        font-size: 0.65rem;
        color: #94a3b8;
        padding: 2px 8px;
        background: #f1f5f9;
        border-radius: 4px;
        flex-shrink: 0;
    }
    
    .error-text {
        color: #dc2626;
        font-size: 0.78rem;
        margin-top: 4px;
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
    }
</style>



 <div class="page-header" style="display:flex;justify-content:space-between;align-items:flex-start;margin-bottom:1.2rem">
        <div>
            <div class="page-title">Crear Usuario</div>
            <div class="page-sub">Agrega un nuevo usuario al sistema</div>
        </div>
        <a href="{{ route('admin.usuarios') }}" class="btn btn-secondary btn-sm">
            <i class="ti ti-arrow-left" style="font-size:14px"></i> Volver
        </a>
    </div>

<div class="create-container">

   

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
                    <i class="ti ti-user-plus" style="font-size:16px;color:#3C4E3E;margin-right:6px"></i>
                    Nuevo usuario
                </div>
                <div class="card-sub">Completa los datos del usuario a registrar</div>
            </div>
        </div>

        <div class="card-body">
            <form action="{{ route('admin.usuarios.store') }}" enctype="multipart/form-data" method="POST">
                @csrf

                <div class="form-grid">

                    <div class="form-field full">
                        <label>Foto de perfil</label>
                        <div class="file-upload-wrapper">
                            <input type="file" name="avatar" id="avatar" accept="image/*">
                            <div class="file-upload-box" id="fileUploadBox">
                                <div class="file-icon">
                                    <i class="ti ti-upload"></i>
                                </div>
                                <div class="file-info">
                                    <span class="file-title" id="fileTitle">Seleccionar imagen</span>
                                    <span class="file-hint" id="fileHint">JPG, PNG o GIF · Máximo 10MB</span>
                                    <span class="file-name" id="fileName"></span>
                                </div>
                                <span class="file-size" id="fileSize">Sin archivo</span>
                            </div>
                        </div>
                        @error('avatar')
                            <span class="error-text">{{ $message }}</span>
                        @enderror
                    </div>

                    {{-- Nombre --}}
                    <div class="form-field">
                        <label>Nombre completo <span class="required">*</span></label>
                        <input type="text" name="name" value="{{ old('name') }}"
                               placeholder="Nombre del usuario" required>
                    </div>

                    {{-- Email --}}
                    <div class="form-field">
                        <label>Correo electrónico <span class="required">*</span></label>
                        <input type="email" name="email" value="{{ old('email') }}"
                               placeholder="correo@ejemplo.com" required>
                    </div>

                    {{-- Contraseña --}}
                    <div class="form-field">
                        <label>Contraseña <span class="required">*</span></label>
                        <input type="password" name="password" placeholder="Mínimo 8 caracteres" required>
                    </div>

                    {{-- Confirmar contraseña --}}
                    <div class="form-field">
                        <label>Confirmar contraseña <span class="required">*</span></label>
                        <input type="password" name="password_confirmation" placeholder="Repite la contraseña" required>
                    </div>

                   

                </div>
                 {{-- Rol --}}
                    <div class="form-field">
                        <label>Rol <span class="required">*</span></label>
                        <select style="width:100%;" name="role">
                            <option value="usuario" {{ old('role','usuario') == 'usuario' ? 'selected' : '' }}>Usuario</option>
                            <option value="admin" {{ old('role') == 'admin' ? 'selected' : '' }}>Administrador</option>
                        </select>
                    </div>

                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">
                        <i class="ti ti-user-plus" style="font-size:15px"></i> Crear usuario
                    </button>
                    <a href="{{ route('admin.usuarios') }}" class="btn btn-secondary">Cancelar</a>
                </div>

            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const fileInput = document.getElementById('avatar');
    const fileTitle = document.getElementById('fileTitle');
    const fileHint = document.getElementById('fileHint');
    const fileName = document.getElementById('fileName');
    const fileSize = document.getElementById('fileSize');
    const fileBox = document.getElementById('fileUploadBox');

    fileInput.addEventListener('change', function() {
        const file = this.files[0];
        
        if (file) {
            // Validar tamaño (10MB)
            if (file.size > 10 * 1024 * 1024) {
                alert('La imagen supera el tamaño máximo de 10MB.');
                this.value = '';
                resetFileDisplay();
                return;
            }

            // Validar tipo
            const validTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
            if (!validTypes.includes(file.type)) {
                alert('Formato no soportado. Usa JPG, PNG, GIF o WEBP.');
                this.value = '';
                resetFileDisplay();
                return;
            }

            // Mostrar nombre
            fileTitle.textContent = 'Archivo seleccionado';
            fileHint.classList.add('hidden');
            fileName.textContent = file.name;
            fileName.classList.add('active');
            
            // Mostrar tamaño
            const sizeInKB = (file.size / 1024).toFixed(1);
            const sizeInMB = (file.size / (1024 * 1024)).toFixed(2);
            fileSize.textContent = sizeInMB > 1 ? sizeInMB + ' MB' : sizeInKB + ' KB';
            
            // Cambiar estilo del box
            fileBox.style.borderColor = '#3C4E3E';
            fileBox.style.background = '#e8ece9';
        } else {
            resetFileDisplay();
        }
    });

    function resetFileDisplay() {
        fileTitle.textContent = 'Seleccionar imagen';
        fileHint.classList.remove('hidden');
        fileName.textContent = '';
        fileName.classList.remove('active');
        fileSize.textContent = 'Sin archivo';
        fileBox.style.borderColor = '#e2e8f0';
        fileBox.style.background = '#f8fafc';
    }
});
</script>

@endsection