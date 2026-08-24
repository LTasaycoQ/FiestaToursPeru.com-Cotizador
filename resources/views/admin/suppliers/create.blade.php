@extends('layouts.app')
@section('title', 'Nuevo Proveedor')

@push('styles')
<style>
/* ============================================================
   PROVEEDORS CREATE - ESTILOS REFINADOS
   ============================================================ */

/* ── HEADER ── */
.page-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 1.8rem;
    flex-wrap: wrap;
    gap: 1rem;
}

.page-header .page-title {
    font-size: 24px;
    font-weight: 700;
    color: #0f172a;
    letter-spacing: -0.3px;
}

.page-header .page-sub {
    font-size: 14px;
    color: #64748b;
    margin-top: 2px;
}

.page-header-actions {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    flex-shrink: 0;
}

.page-header-actions .btn-sm {
    padding: 8px 20px !important;
    font-size: 13px !important;
    border-radius: 8px !important;
}

.btn-primary {
    background: #6366f1;
    color: #fff;
    border: none;
    padding: 0.75rem 2rem;
    border-radius: 10px;
    font-weight: 600;
    font-size: 14px;
    cursor: pointer;
    transition: all .2s;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    text-decoration: none;
}

.btn-primary:hover {
    background: #4f46e5;
    transform: translateY(-2px);
    box-shadow: 0 4px 16px rgba(99, 102, 241, 0.3);
}

.btn-secondary {
    background: #f1f5f9;
    color: #475569;
    border: none;
    padding: 0.75rem 2rem;
    border-radius: 10px;
    font-weight: 600;
    font-size: 14px;
    cursor: pointer;
    transition: all .2s;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 6px;
}

.btn-secondary:hover {
    background: #e2e8f0;
    color: #0f172a;
}

.btn-sm {
    padding: 8px 20px !important;
    border-radius: 8px !important;
    font-size: 13px !important;
}

/* ── ALERTAS ── */
.alert-error {
    margin-bottom: 1.5rem;
    padding: 1rem 1.25rem;
    border-radius: 12px;
    background: #fef2f2;
    border: 1px solid #fecaca;
    color: #991b1b;
    display: flex;
    align-items: flex-start;
    gap: 10px;
}

.alert-error ul {
    list-style: none;
    margin: 0;
    padding: 0;
}

/* ── LAYOUT PRINCIPAL ── */
.edit-supplier-layout {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 2rem;
    align-items: start;
}

.edit-supplier-left,
.edit-supplier-right {
    display: flex;
    flex-direction: column;
    gap: 1.5rem;
}

@media (max-width: 1024px) {
    .edit-supplier-layout {
        grid-template-columns: 1fr;
        gap: 1.5rem;
    }
}

/* ── CARDS MODERNAS ── */
.card-modern {
    background: #ffffff;
    border-radius: 16px;
    border: 1px solid #eef2f6;
    padding: 1.5rem;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.04);
    transition: box-shadow .2s, border-color .2s;
}

.card-modern:hover {
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.06);
    border-color: #e2e8f0;
}

.card-header-custom {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 1.25rem;
    padding-bottom: 0.75rem;
    border-bottom: 2px solid #f1f5f9;
}

.card-title-custom {
    font-size: 16px;
    font-weight: 700;
    color: #0f172a;
    display: flex;
    align-items: center;
    gap: 8px;
}

.card-title-custom i {
    color: #6366f1;
    font-size: 18px;
}

.card-sub-custom {
    font-size: 13px;
    color: #94a3b8;
    margin-top: 2px;
}

/* ── CAMPOS DE FORMULARIO ── */
.field-group {
    margin-bottom: 1.25rem;
}

.field-group:last-child {
    margin-bottom: 0;
}

.field-group label {
    display: block;
    font-size: 12px;
    font-weight: 700;
    color: #475569;
    text-transform: uppercase;
    letter-spacing: 0.4px;
    margin-bottom: 5px;
}

.field-group label .req {
    color: #ef4444;
    margin-left: 2px;
}

.field-group input,
.field-group select,
.field-group textarea {
    width: 100%;
    padding: 0.7rem 0.9rem;
    border: 1.5px solid #e2e8f0;
    border-radius: 10px;
    font-size: 14px;
    background: #fafbfc;
    transition: all .2s;
    color: #0f172a;
    box-sizing: border-box;
    font-family: inherit;
}

.field-group input:focus,
.field-group select:focus,
.field-group textarea:focus {
    border-color: #6366f1;
    outline: none;
    background: #ffffff;
    box-shadow: 0 0 0 4px rgba(99, 102, 241, 0.08);
}

.field-group input::placeholder,
.field-group textarea::placeholder {
    color: #94a3b8;
}

.field-group textarea {
    resize: vertical;
    min-height: 80px;
}

/* ── GRID PARA FORMULARIOS ── */
.form-grid-2 {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 1rem;
}

@media (max-width: 640px) {
    .form-grid-2 {
        grid-template-columns: 1fr;
    }
}

/* ── COMBOS BUSCABLES ── */
.combo-wrap {
    position: relative;
}

.combo-input {
    width: 100%;
    padding: 0.62rem 0.8rem;
    border: 1.5px solid #e2e8f0;
    border-radius: 10px;
    font-size: 14px;
    color: #0f172a;
    outline: none;
    transition: border-color .15s, background .15s;
    background: #fafbfc;
    box-sizing: border-box;
}

.combo-input:focus {
    border-color: #6366f1;
    background: #fff;
    box-shadow: 0 0 0 4px rgba(99, 102, 241, 0.08);
}

.combo-input[disabled] {
    background: #f8fafc;
    color: #94a3b8;
    cursor: not-allowed;
}

.combo-list {
    position: absolute;
    top: calc(100% + 4px);
    left: 0;
    right: 0;
    background: #fff;
    border: 1.5px solid #e2e8f0;
    border-radius: 10px;
    max-height: 220px;
    overflow-y: auto;
    z-index: 50;
    box-shadow: 0 10px 25px -5px rgba(0, 0, 0, .1);
    display: none;
}

.combo-list.show {
    display: block;
}

.combo-item {
    padding: .55rem .8rem;
    font-size: 13px;
    color: #0f172a;
    cursor: pointer;
    transition: background .15s;
}

.combo-item:hover,
.combo-item.active {
    background: #eef2ff;
    color: #4338ca;
}

.combo-empty {
    padding: .6rem .8rem;
    font-size: 12.5px;
    color: #94a3b8;
}

.combo-clear {
    position: absolute;
    right: .6rem;
    top: 50%;
    transform: translateY(-50%);
    background: none;
    border: none;
    color: #cbd5e1;
    cursor: pointer;
    font-size: 14px;
    display: none;
    padding: 2px;
    line-height: 1;
}

.combo-clear.show {
    display: block;
}

.combo-clear:hover {
    color: #ef4444;
}

/* ── CATEGORÍA INLINE ── */
.inline-create-block {
    background: #f8fafc;
    border: 1.5px solid #eef2f6;
    border-radius: 12px;
    padding: 1.25rem;
}

.inline-create-block .block-label {
    font-size: 11px;
    font-weight: 700;
    color: #94a3b8;
    text-transform: uppercase;
    letter-spacing: .6px;
    margin-bottom: 0.8rem;
    display: flex;
    align-items: center;
    gap: 8px;
}

.inline-create-block .block-label i {
    font-size: 15px;
    color: #6366f1;
}

.inline-create-block .inline-row {
    display: flex;
    align-items: flex-end;
    gap: 0.75rem;
}

.inline-create-block .inline-row .field-group {
    flex: 1;
    margin-bottom: 0;
}

.btn-inline-new {
    padding: 0.65rem 1.2rem;
    background: #fff;
    border: 1.5px solid #e2e8f0;
    border-radius: 10px;
    font-size: 12px;
    font-weight: 600;
    color: #6366f1;
    cursor: pointer;
    white-space: nowrap;
    flex-shrink: 0;
    transition: all .2s;
    display: flex;
    align-items: center;
    gap: 6px;
}

.btn-inline-new:hover {
    border-color: #6366f1;
    background: #eef2ff;
}

.new-field-box {
    display: none;
    margin-top: 0.8rem;
}

.new-field-box .inner {
    background: #ede9fe;
    border-radius: 10px;
    padding: 1rem;
}

.new-field-box .inner label {
    font-size: 10px;
    font-weight: 700;
    color: #6d28d9;
    text-transform: uppercase;
    letter-spacing: .5px;
    display: block;
    margin-bottom: 6px;
}

.new-field-box .inner .input-row {
    display: flex;
    gap: 0.6rem;
}

.new-field-box .inner .input-row input {
    flex: 1;
    padding: 0.6rem 0.9rem;
    border: 1.5px solid #c4b5fd;
    border-radius: 8px;
    font-size: 13px;
    outline: none;
    background: #fff;
}

.new-field-box .inner .input-row input:focus {
    border-color: #7c3aed;
    box-shadow: 0 0 0 4px rgba(124, 58, 237, 0.1);
}

.new-field-box .btn-close-inline {
    padding: 0.55rem 0.9rem;
    background: none;
    border: 1.5px solid #c4b5fd;
    border-radius: 8px;
    color: #6d28d9;
    cursor: pointer;
    font-size: 13px;
    transition: all .2s;
}

.new-field-box .btn-close-inline:hover {
    background: #ede9fe;
}

.new-field-box .hint {
    font-size: 11px;
    color: #7c3aed;
    margin-top: 0.6rem;
    display: flex;
    align-items: center;
    gap: 5px;
}

/* ── CADENAS ── */
.chains-grid {
    display: flex;
    flex-wrap: wrap;
    gap: 8px;
    padding: 4px 0;
}

.chain-checkbox {
    display: flex;
    align-items: center;
    gap: 6px;
    cursor: pointer;
    padding: 6px 14px;
    border: 1.5px solid #e2e8f0;
    border-radius: 8px;
    transition: all .2s;
    background: #fff;
    font-size: 13px;
    color: #334155;
    user-select: none;
}

.chain-checkbox:hover {
    border-color: #6366f1;
    background: #f8fafc;
    transform: translateY(-1px);
}

.chain-checkbox input[type="checkbox"] {
    width: 16px;
    height: 16px;
    accent-color: #6366f1;
    cursor: pointer;
    flex-shrink: 0;
    margin: 0;
}

.chain-checkbox.checked {
    border-color: #6366f1;
    background: #eef2ff;
}

@media (max-width: 640px) {
    .inline-create-block .inline-row {
        flex-wrap: wrap;
    }
    .inline-create-block .inline-row .field-group {
        flex: 1 1 100%;
    }
    .chains-grid {
        gap: 6px;
    }
    .chain-checkbox {
        padding: 4px 10px;
        font-size: 12px;
    }
}
</style>
@endpush

@section('content')

<div class="page-header">
    <div>
        <div class="page-title">Nuevo Proveedor</div>
        <div class="page-sub">Registra un nuevo proveedor en el sistema</div>
    </div>
    <div class="page-header-actions">
        <a href="{{ route('admin.suppliers.index') }}" class="btn btn-secondary btn-sm">
            <i class="ti ti-arrow-left" style="font-size:13px"></i> Volver
        </a>
        <button type="submit" form="form-supplier" class="btn btn-primary btn-sm">
            <i class="ti ti-plus"></i> Guardar proveedor
        </button>
    </div>
</div>

@if($errors->any())
    <div class="alert alert-error">
        <i class="ti ti-alert-circle"></i>
        <ul>
            @foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach
        </ul>
    </div>
@endif

@if(session('error'))
    <div class="alert alert-error">
        <i class="ti ti-alert-circle"></i>
        {{ session('error') }}
    </div>
@endif

<form action="{{ route('admin.suppliers.store') }}" method="POST" id="form-supplier">
    @csrf

    <div class="edit-supplier-layout">

        {{-- ============================================================ --}}
        {{-- COLUMNA IZQUIERDA: Datos del proveedor + Categoría           --}}
        {{-- ============================================================ --}}
        <div class="edit-supplier-left">

            {{-- ── DATOS DEL PROVEEDOR ── --}}
            <div class="card-modern">
                <div class="card-header-custom">
                    <div>
                        <div class="card-title-custom">
                            <i class="ti ti-building-store"></i> Datos del proveedor
                        </div>
                        <div class="card-sub-custom">Información principal del proveedor</div>
                    </div>
                </div>

                <div class="field-group">
                    <label>Nombre Comercial <span class="req">*</span></label>
                    <input type="text" name="supplier_name"
                           value="{{ old('supplier_name') }}"
                           placeholder="Ej: Agencia de Viajes Andina"
                           maxlength="100" required autofocus>
                </div>

                <div class="form-grid-2">
                    <div class="field-group">
                        <label>Razón Social</label>
                        <input type="text" name="business_name"
                               value="{{ old('business_name') }}"
                               placeholder="Razón social">
                    </div>
                    <div class="field-group">
                        <label>Código Tributario</label>
                        <input type="text" name="tax_code"
                               value="{{ old('tax_code') }}"
                               placeholder="Ej: RUC, NIT, VAT...">
                    </div>
                </div>

                <div class="form-grid-2">
                    <div class="field-group">
                        <label>Email Empresarial</label>
                        <input type="email" name="general_email"
                               value="{{ old('general_email') }}"
                               placeholder="proveedor@empresa.com">
                    </div>
                    <div class="field-group">
                        <label>Teléfono Empresarial</label>
                        <input type="text" name="general_phone"
                               value="{{ old('general_phone') }}"
                               placeholder="+51 987 654 321">
                    </div>
                </div>

                <div class="field-group">
                    <label>Descripción</label>
                    <textarea name="description" rows="3"
                              placeholder="Describe al proveedor...">{{ old('description') }}</textarea>
                </div>
            </div>

            {{-- ── CATEGORÍA ── --}}
            <div class="inline-create-block">
                <div class="block-label">
                    <i class="ti ti-tag"></i> Categoría del proveedor
                </div>
                <div class="inline-row">
                    <div class="field-group">
                        <label>Seleccionar categoría existente</label>
                        <select name="id_categories_suppliers" id="sel-category">
                            <option value="">— Sin categoría —</option>
                            @foreach($categories as $c)
                                <option value="{{ $c->id_categories_suppliers }}"
                                    {{ old('id_categories_suppliers') == $c->id_categories_suppliers ? 'selected' : '' }}>
                                    {{ $c->category_name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <button type="button" class="btn-inline-new" onclick="toggleNew('category')">
                        <i class="ti ti-plus"></i> Nueva
                    </button>
                </div>

                <div id="new-category" class="new-field-box">
                    <div class="inner">
                        <label><i class="ti ti-plus"></i> Nueva categoría</label>
                        <div class="input-row">
                            <input type="text" name="new_category_name" id="new-category-input"
                                   placeholder="Ej: Hoteles, Aerolíneas, Transporte...">
                            <button type="button" class="btn-close-inline" onclick="toggleNew('category')">
                                <i class="ti ti-x"></i>
                            </button>
                        </div>
                        <div class="hint">
                            <i class="ti ti-info-circle"></i> Se creará automáticamente al guardar
                        </div>
                    </div>
                </div>
            </div>

        </div>

        {{-- ============================================================ --}}
        {{-- COLUMNA DERECHA: Ubicación + Cadenas                         --}}
        {{-- ============================================================ --}}
        <div class="edit-supplier-right">

            {{-- ── UBICACIÓN ── --}}
            <div class="card-modern">
                <div class="card-header-custom">
                    <div>
                        <div class="card-title-custom">
                            <i class="ti ti-map-pin"></i> Ubicación
                        </div>
                        <div class="card-sub-custom">País, ciudad y dirección</div>
                    </div>
                </div>

                <div class="form-grid-2">
                    <div class="field-group">
                        <label>País</label>
                        <div class="combo-wrap" id="combo-pais">
                            <input type="text" class="combo-input" id="create-pais-input"
                                   placeholder="Cargando países..." autocomplete="off" disabled>
                            <button type="button" class="combo-clear" id="create-pais-clear" tabindex="-1">
                                <i class="ti ti-x"></i>
                            </button>
                            <div class="combo-list" id="create-pais-list"></div>
                        </div>
                        <input type="hidden" name="id_cities" id="create-country-name" value="{{ old('id_cities') }}">
                        <input type="hidden" id="create-country-id">
                    </div>

                    <div class="field-group">
                        <label>Ciudad</label>
                        <div class="combo-wrap" id="combo-ciudad">
                            <input type="text" class="combo-input" id="create-ciudad-input"
                                   placeholder="Seleccione país primero" autocomplete="off" disabled>
                            <button type="button" class="combo-clear" id="create-ciudad-clear" tabindex="-1">
                                <i class="ti ti-x"></i>
                            </button>
                            <div class="combo-list" id="create-ciudad-list"></div>
                        </div>
                        <input type="hidden" name="id_cities" id="create-ciudad-name" value="{{ old('id_cities') }}">
                    </div>
                </div>

                <div class="field-group" style="margin-top:.75rem">
                    <label>Dirección</label>
                    <input type="text" name="address" value="{{ old('address') }}"
                           placeholder="Ej: Avenida Suecia, calle 124, primera casa">
                </div>
            </div>

            {{-- ── CADENAS ── --}}
            <div class="card-modern">
                <div class="card-header-custom">
                    <div>
                        <div class="card-title-custom">
                            <i class="ti ti-link"></i> Cadena / Grupo
                        </div>
                        <div class="card-sub-custom">Selecciona la cadena o grupo al que pertenece</div>
                    </div>
                </div>

                <div class="field-group">
                    <label>Cadenas</label>
                    <div class="chains-grid" id="chains-container">
                        @foreach($chains as $chain)
                            <label class="chain-checkbox {{ is_array(old('chains')) && in_array($chain->id_chain, old('chains')) ? 'checked' : '' }}">
                                <input type="checkbox"
                                       name="chains[]"
                                       value="{{ $chain->id_chain }}"
                                       {{ is_array(old('chains')) && in_array($chain->id_chain, old('chains')) ? 'checked' : '' }}
                                       onchange="this.parentElement.classList.toggle('checked')">
                                {{ $chain->name }}
                            </label>
                        @endforeach
                    </div>
                    <div style="font-size:11px;color:#94a3b8;margin-top:6px;">
                        <i class="ti ti-info-circle"></i> Selecciona una o varias cadenas
                    </div>
                </div>
            </div>

        </div>
    </div>
</form>

@push('scripts')
<script>
    window.geoPaisesUrl = "{{ url('api/geo/paises') }}";
    window.geoCiudadesUrl = "{{ url('api/geo/ciudades') }}";
</script>

<script>
// ================================================================
// PROVEEDORS CREATE - JAVASCRIPT
// ================================================================

// ── COMBOS ──
function crearCombo({ inputId, listId, clearId, onSelect, onClear }) {
    const input = document.getElementById(inputId);
    const list = document.getElementById(listId);
    const clear = document.getElementById(clearId);
    let options = [];
    let activeIndex = -1;

    function normalizar(str) {
        return (str || '').toString().toLowerCase().normalize('NFD').replace(/[\u0300-\u036f]/g, '');
    }

    function render(filtro) {
        const term = normalizar(filtro);
        const filtradas = term ? options.filter(o => normalizar(o.label).includes(term)) : options;
        if (filtradas.length === 0) {
            list.innerHTML = '<div class="combo-empty">Sin resultados</div>';
        } else {
            list.innerHTML = filtradas.map((o, idx) =>
                `<div class="combo-item" data-idx="${idx}">${o.label}</div>`
            ).join('');
        }
        list._filtradas = filtradas;
        activeIndex = -1;
        list.classList.add('show');
    }

    function cerrar() { list.classList.remove('show'); activeIndex = -1; }

    function seleccionar(opt) {
        input.value = opt.label;
        clear.classList.add('show');
        cerrar();
        onSelect(opt);
    }

    function actualizarActivo() {
        list.querySelectorAll('.combo-item').forEach(el => el.classList.remove('active'));
        const el = list.querySelector(`[data-idx="${activeIndex}"]`);
        if (el) { el.classList.add('active'); el.scrollIntoView({ block: 'nearest' }); }
    }

    input.addEventListener('focus', () => { if (!input.disabled) render(input.value); });
    input.addEventListener('input', () => {
        if (input.value === '') clear.classList.remove('show');
        else clear.classList.add('show');
        render(input.value);
        if (onClear) onClear(false);
    });
    input.addEventListener('keydown', (e) => {
        const filtradas = list._filtradas || [];
        if (!list.classList.contains('show')) return;
        if (e.key === 'ArrowDown') { e.preventDefault(); activeIndex = Math.min(activeIndex + 1, filtradas.length - 1); actualizarActivo(); }
        else if (e.key === 'ArrowUp') { e.preventDefault(); activeIndex = Math.max(activeIndex - 1, 0); actualizarActivo(); }
        else if (e.key === 'Enter') { e.preventDefault(); if (activeIndex >= 0 && filtradas[activeIndex]) seleccionar(filtradas[activeIndex]); }
        else if (e.key === 'Escape') cerrar();
    });
    list.addEventListener('mousedown', (e) => {
        e.preventDefault();
        const item = e.target.closest('.combo-item');
        if (!item) return;
        seleccionar(list._filtradas[parseInt(item.dataset.idx)]);
    });
    document.addEventListener('click', (e) => {
        if (!input.contains(e.target) && !list.contains(e.target)) cerrar();
    });
    clear.addEventListener('click', () => {
        input.value = '';
        clear.classList.remove('show');
        cerrar();
        if (onClear) onClear(true);
        input.focus();
    });
    return {
        setOptions(nuevasOpciones, placeholder) {
            options = nuevasOpciones || [];
            input.value = '';
            clear.classList.remove('show');
            input.disabled = false;
            input.placeholder = placeholder || 'Escribe para buscar...';
            cerrar();
        },
        disable(placeholder) {
            input.disabled = true;
            input.value = '';
            clear.classList.remove('show');
            input.placeholder = placeholder || '';
            options = [];
            cerrar();
        }
    };
}

const comboPais = crearCombo({
    inputId: 'create-pais-input',
    listId: 'create-pais-list',
    clearId: 'create-pais-clear',
    onSelect: (opt) => {
        document.getElementById('create-country-name').value = opt.value;
        cargarCiudades(opt.value);
    },
    onClear: (full) => {
        document.getElementById('create-country-id').value = '';
        document.getElementById('create-country-name').value = '';
        if (full) {
            comboCiudad.disable('Seleccione país primero');
            document.getElementById('create-ciudad-name').value = '';
        }
    }
});

const comboCiudad = crearCombo({
    inputId: 'create-ciudad-input',
    listId: 'create-ciudad-list',
    clearId: 'create-ciudad-clear',
    onSelect: (opt) => {
        document.getElementById('create-ciudad-name').value = opt.value;
    },
    onClear: () => {
        document.getElementById('create-ciudad-name').value = '';
    }
});

function cargarPaises() {
    fetch(window.geoPaisesUrl)
        .then(r => r.json())
        .then(paises => {
            comboPais.setOptions(
                paises.map(p => ({ value: p.id, label: p.nombre })),
                'Escribe para buscar país...'
            );
        })
        .catch(() => comboPais.setOptions([], 'No se pudo cargar países'));
}

function cargarCiudades(countryId) {
    comboCiudad.disable('Cargando ciudades...');
    if (!countryId) { comboCiudad.disable('Seleccione país primero'); return; }
    fetch(`${window.geoCiudadesUrl}?country_id=${countryId}`, { headers: { 'Accept': 'application/json' } })
        .then(response => { if (!response.ok) throw new Error('Error'); return response.json(); })
        .then(data => {
            if (!Array.isArray(data)) { comboCiudad.setOptions([], 'Error en los datos'); return; }
            comboCiudad.setOptions(
                data.map(item => ({ value: item.id, label: item.name || item.nombre || 'Sin nombre' })),
                'Escribe para buscar ciudad...'
            );
        })
        .catch(() => comboCiudad.setOptions([], 'No se pudo cargar ciudades'));
}

// ── CATEGORÍA ──
function toggleNew(type) {
    const box = document.getElementById('new-' + type);
    const sel = document.getElementById('sel-' + type);
    const input = document.getElementById('new-' + type + '-input');
    const open = box.style.display === 'none' || box.style.display === '';
    box.style.display = open ? 'block' : 'none';
    if (open) { sel.value = ''; sel.disabled = true; if (input) input.focus(); }
    else { sel.disabled = false; if (input) input.value = ''; }
}

// ── INICIALIZACIÓN ──
document.addEventListener('DOMContentLoaded', function() {
    cargarPaises();
});
</script>
@endpush
@endsection
