@extends('layouts.app')

@section('title', 'Importar servicios')

@push('styles')
<style>
    /* --- Contenedor principal --- */
    .import-page {
        max-width: 920px;
        margin: 2rem auto;
        font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
    }

    /* --- Tarjeta principal --- */
    .import-card {
        background: 
#ffffff;
        border-radius: 20px;
        box-shadow: 0 8px 30px rgba(0, 0, 0, 0.06);
        border: 1px solid 
#eef2f6;
        overflow: hidden;
        transition: box-shadow 0.2s ease;
    }

    /* --- Cabecera --- */
    .import-header {
        padding: 1.75rem 2.25rem;
        background: 
#fafbfc;
        border-bottom: 1px solid 
#eef2f6;
    }

    .import-header h1 {
        margin: 0;
        font-size: 1.5rem;
        font-weight: 600;
        color: 
#0b1a33;
        display: flex;
        align-items: center;
        gap: 0.6rem;
        letter-spacing: -0.01em;
    }

    .import-header h1 i {
        color: 
#2563eb;
        font-size: 1.5rem;
    }

    .import-header .subtitle {
        margin: 0.35rem 0 0 0;
        color: 
#64748b;
        font-size: 0.92rem;
        font-weight: 400;
    }

    /* --- Cuerpo --- */
    .import-body {
        padding: 2rem 2.25rem 2.25rem 2.25rem;
    }

    /* --- Grid de campos --- */
    .import-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 1.5rem;
        margin-bottom: 1.5rem;
    }

    .import-field {
        display: flex;
        flex-direction: column;
        gap: 0.4rem;
    }

    .import-field label {
        font-size: 0.85rem;
        font-weight: 600;
        color: 
#1e293b;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .import-field label i {
        color: 
#2563eb;
        font-size: 1rem;
    }

    .import-field label .required {
        color: 
#dc2626;
        font-weight: 600;
        margin-left: 0.1rem;
    }

    .import-field .form-control {
        padding: 0.65rem 1rem;
        border: 1px solid 
#dce1e9;
        border-radius: 12px;
        font-size: 0.92rem;
        background: 
#ffffff;
        transition: border-color 0.2s, box-shadow 0.2s;
        width: 100%;
        appearance: auto;
        color: 
#0b1a33;
    }

    .import-field .form-control:focus {
        border-color: 
#2563eb;
        box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.10);
        outline: none;
    }

    .import-help {
        font-size: 0.78rem;
        color: 
#6a7b99;
        margin: 0.2rem 0 0 0;
    }

    /* --- ÁREA DE DRAG & DROP (estilo moderno) --- */
    .drop-zone {
        border: 2px dashed 
#dce1e9;
        border-radius: 16px;
        padding: 2.5rem 1.5rem;
        text-align: center;
        background: 
#fafbfc;
        transition: border-color 0.25s, background 0.25s;
        cursor: pointer;
        position: relative;
        margin-bottom: 1.5rem;
    }

    .drop-zone:hover {
        border-color: 
#94a3b8;
        background: 
#f8fafc;
    }

    .drop-zone.dragover {
        border-color: 
#2563eb;
        background: 
#eff6ff;
    }

    .drop-zone .drop-icon {
        font-size: 2.8rem;
        color: 
#94a3b8;
        display: block;
        margin-bottom: 0.5rem;
    }

    .drop-zone .drop-title {
        font-size: 1rem;
        font-weight: 500;
        color: 
#1e293b;
        margin: 0 0 0.2rem 0;
    }

    .drop-zone .drop-subtitle {
        font-size: 0.85rem;
        color: 
#64748b;
        margin: 0;
    }

    .drop-zone .drop-formats {
        font-size: 0.78rem;
        color: 
#94a3b8;
        margin-top: 0.4rem;
    }

    .drop-zone input[type="file"] {
        position: absolute;
        inset: 0;
        opacity: 0;
        cursor: pointer;
        width: 100%;
        height: 100%;
    }

    /* --- Nombre del archivo seleccionado --- */
    .file-selected {
        display: none;
        align-items: center;
        justify-content: center;
        gap: 0.6rem;
        padding: 0.6rem 1rem;
        background: 
#eff6ff;
        border-radius: 12px;
        color: 
#1e40af;
        font-size: 0.88rem;
        font-weight: 500;
        margin-top: 0.75rem;
        border: 1px solid 
#bfdbfe;
    }

    .file-selected i {
        font-size: 1.1rem;
    }

    .file-selected .file-name {
        flex: 1;
        text-align: left;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    .file-selected .file-remove {
        background: transparent;
        border: none;
        color: 
#dc2626;
        cursor: pointer;
        font-size: 1.1rem;
        padding: 0 0.3rem;
        transition: color 0.15s;
    }

    .file-selected .file-remove:hover {
        color: 
#b91c1c;
    }

    .file-selected.show {
        display: flex;
    }

    /* --- Tarjetas de información --- */
    .import-info-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 1rem;
        margin: 1.5rem 0 2rem 0;
    }

    .import-info-item {
        background: 
#f8faff;
        border-radius: 14px;
        padding: 0.9rem 1.2rem;
        border: 1px solid 
#e7edf4;
        transition: background 0.15s ease;
    }

    .import-info-item i {
        display: block;
        font-size: 1.2rem;
        color: 
#2563eb;
        margin-bottom: 0.3rem;
    }

    .import-info-item strong {
        display: block;
        font-weight: 600;
        font-size: 0.82rem;
        color: 
#0b1a33;
        margin-bottom: 0.1rem;
    }

    .import-info-item span {
        font-size: 0.78rem;
        color: 
#4c5f7d;
    }

    /* --- Botones (estilo coherente con el layout) --- */
    .import-actions {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        gap: 0.75rem;
        margin-top: 0.25rem;
    }

    .btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 0.5rem;
        padding: 0.6rem 1.5rem;
        border-radius: 10px;
        font-weight: 500;
        font-size: 0.88rem;
        border: 1px solid transparent;
        transition: background 0.2s, border-color 0.2s, box-shadow 0.15s;
        cursor: pointer;
        text-decoration: none;
        background: transparent;
        color: 
#1e293b;
        line-height: 1.4;
    }

    /* --- Botón primario: azul corporativo --- */
    .btn-primary {
        background: 
#2563eb;
        color: 
#ffffff;
        border-color: 
#2563eb;
    }
    .btn-primary:hover {
        background: 
#1d4ed8;
        border-color: 
#1d4ed8;
        color: 
#ffffff;
        box-shadow: 0 4px 12px rgba(37, 99, 235, 0.25);
    }
    .btn-primary:active {
        transform: scale(0.97);
    }
    .btn-primary:disabled {
        opacity: 0.5;
        cursor: not-allowed;
        box-shadow: none;
    }

    /* --- Botón secundario --- */
    .btn-secondary {
        background: 
#f1f5f9;
        color: 
#1e293b;
        border-color: 
#e2e8f0;
    }
    .btn-secondary:hover {
        background: 
#e9eef4;
        border-color: 
#d0d9e6;
    }

    /* --- Botón outline (Volver) --- */
    .btn-outline {
        background: transparent;
        border-color: 
#dce1e9;
        color: 
#475569;
    }
    .btn-outline:hover {
        background: 
#f8fafc;
        border-color: 
#bcc7d6;
    }

    /* --- Alerta de errores --- */
    .alert-danger {
        background: 
#fef2f2;
        border: 1px solid 
#fecaca;
        border-radius: 14px;
        padding: 0.9rem 1.25rem;
        color: 
#991b1b;
        margin-bottom: 1.5rem;
        font-size: 0.88rem;
        display: flex;
        flex-direction: column;
        gap: 0.15rem;
    }

    .alert-danger div::before {
        content: "• ";
    }

    /* --- Responsive --- */
    @media (max-width: 768px) {
        .import-page {
            margin: 1rem;
        }
        .import-header {
            padding: 1.25rem 1.25rem;
        }
        .import-header h1 {
            font-size: 1.25rem;
        }
        .import-body {
            padding: 1.25rem;
        }
        .import-grid {
            grid-template-columns: 1fr;
            gap: 1rem;
        }
        .import-info-grid {
            grid-template-columns: 1fr;
        }
        .import-actions {
            flex-direction: column;
            align-items: stretch;
        }
        .import-actions .btn {
            justify-content: center;
            padding: 0.65rem 1.2rem;
        }
        .drop-zone {
            padding: 1.5rem 1rem;
        }
        .drop-zone .drop-icon {
            font-size: 2rem;
        }
    }
</style>
@endpush

@section('content')
<div class="import-page">
    <div class="import-card">

        {{-- Cabecera --}}
        <div class="import-header">
            <h1>
                <i class="ti ti-file-spreadsheet"></i> Importar servicios
            </h1>
            <p class="subtitle">Carga tu tarifario general y asigna todos los servicios a un tipo de mercado en un solo paso.</p>
        </div>

        {{-- Cuerpo --}}
        <div class="import-body">

            {{-- Errores --}}
            @if($errors->any())
                <div class="alert-danger">
                    @foreach($errors->all() as $error)
                        <div>{{ $error }}</div>
                    @endforeach
                </div>
            @endif

            {{-- Formulario --}}
            <form id="serviceImportForm" action="{{ route('admin.services.import') }}" method="POST" enctype="multipart/form-data">
                @csrf

                {{-- Campos superiores --}}
                <div class="import-grid">
                    {{-- Mercado --}}
                    <div class="import-field">
                        <label for="id_labels">
                            <i class="ti ti-users"></i> Tipo de mercado <span class="required">*</span>
                        </label>
                        <select id="id_labels" name="id_labels" class="form-control" required>
                            <option value="">Selecciona el mercado</option>
                            @foreach($markets as $market)
                                <option value="{{ $market->id_labels }}" @selected(old('id_labels') == $market->id_labels)>
                                    {{ $market->name_labels }}
                                </option>
                            @endforeach
                        </select>
                        <p class="import-help">Se asignará a cada servicio nuevo o actualizado.</p>
                    </div>

                    {{-- Campo vacío para mantener el grid --}}
                    <div></div>
                </div>

                {{-- ÁREA DE DRAG & DROP --}}
                <div class="drop-zone" id="dropZone">
                    <i class="ti ti-cloud-upload drop-icon"></i>
                    <p class="drop-title">Arrastra tu archivo aquí o haz clic para seleccionar</p>
                    <p class="drop-subtitle">Subir archivo de servicios</p>
                    <p class="drop-formats">Formatos aceptados: .xlsx, .xls (máx. 5MB)</p>
                    <input type="file" id="archivo" name="archivo" accept=".xlsx,.xls" required>
                </div>

                {{-- Nombre del archivo seleccionado --}}
                <div class="file-selected" id="fileSelected">
                    <i class="ti ti-file-spreadsheet"></i>
                    <span class="file-name" id="fileName">archivo.xlsx</span>
                    <button type="button" class="file-remove" id="fileRemove" title="Quitar archivo">
                        <i class="ti ti-x"></i>
                    </button>
                </div>

                {{-- Información visual --}}
                <div class="import-info-grid">
                    <div class="import-info-item">
                        <i class="ti ti-building-store"></i>
                        <strong>Proveedor</strong>
                        <span>Debe coincidir con el catálogo.</span>
                    </div>
                    <div class="import-info-item">
                        <i class="ti ti-category"></i>
                        <strong>Categoría</strong>
                        <span>Se valida por cada fila.</span>
                    </div>
                    <div class="import-info-item">
                        <i class="ti ti-currency-dollar"></i>
                        <strong>Tarifas</strong>
                        <span>Se importan según sus columnas.</span>
                    </div>
                </div>

                {{-- Acciones --}}
                <div class="import-actions">
                    <button class="btn btn-primary" type="submit" id="importBtn">
                        <i class="ti ti-upload"></i> Importar
                    </button>
                    <a class="btn btn-secondary" href="{{ route('admin.services.template') }}">
                        <i class="ti ti-file-download"></i> Descargar plantilla
                    </a>
                    <a class="btn btn-outline" href="{{ route('admin.services.index') }}">
                        Volver
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
    (function() {
        const dropZone = document.getElementById('dropZone');
        const fileInput = document.getElementById('archivo');
        const fileSelected = document.getElementById('fileSelected');
        const fileName = document.getElementById('fileName');
        const fileRemove = document.getElementById('fileRemove');
        const importBtn = document.getElementById('importBtn');

        // --- Actualizar UI cuando se selecciona un archivo ---
        function updateFileUI(file) {
            if (file) {
                fileName.textContent = file.name;
                fileSelected.classList.add('show');
                dropZone.style.display = 'none';
                importBtn.disabled = false;
            } else {
                fileSelected.classList.remove('show');
                dropZone.style.display = 'block';
                fileInput.value = '';
                importBtn.disabled = true;
            }
        }

        // --- Evento: cambio en el input file ---
        fileInput.addEventListener('change', function(e) {
            if (this.files && this.files.length > 0) {
                updateFileUI(this.files[0]);
            } else {
                updateFileUI(null);
            }
        });

        // --- Evento: quitar archivo ---
        fileRemove.addEventListener('click', function(e) {
            e.preventDefault();
            updateFileUI(null);
        });

        // --- Drag & Drop ---
        dropZone.addEventListener('dragover', function(e) {
            e.preventDefault();
            this.classList.add('dragover');
        });

        dropZone.addEventListener('dragleave', function(e) {
            e.preventDefault();
            this.classList.remove('dragover');
        });

        dropZone.addEventListener('drop', function(e) {
            e.preventDefault();
            this.classList.remove('dragover');

            const files = e.dataTransfer.files;
            if (files && files.length > 0) {
                const file = files[0];
                const validTypes = [
                    'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                    'application/vnd.ms-excel'
                ];
                const ext = file.name.split('.').pop().toLowerCase();

                if (validTypes.includes(file.type) || ext === 'xlsx' || ext === 'xls') {
                    fileInput.files = files;
                    updateFileUI(file);
                } else {
                    alert('Formato no válido. Solo se permiten archivos .xlsx y .xls.');
                }
            }
        });

        // --- Validación antes de enviar ---
        document.getElementById('serviceImportForm').addEventListener('submit', function(e) {
            const mercado = document.getElementById('id_labels').value;
            if (!mercado) {
                e.preventDefault();
                alert('Por favor, selecciona un tipo de mercado.');
                document.getElementById('id_labels').focus();
                return;
            }

            if (!fileInput.files || fileInput.files.length === 0) {
                e.preventDefault();
                alert('Por favor, selecciona un archivo Excel.');
                return;
            }
        });

        // --- Inicializar: si ya hay un archivo seleccionado (por error de validación) ---
        if (fileInput.files && fileInput.files.length > 0) {
            updateFileUI(fileInput.files[0]);
        } else {
            importBtn.disabled = true;
        }
    })();
</script>
@endpush
@endsection