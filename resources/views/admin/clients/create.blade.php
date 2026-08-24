@extends('layouts.app')
@section('title', 'Nuevo Cliente')

@push('styles')
<style>
/* ── HEADER ── */
.page-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 1.4rem;
    gap: 1rem;
    flex-wrap: wrap;
}

.page-header .page-title {
    font-size: 22px;
    font-weight: 700;
    color: #0f172a;
}

.page-header .page-sub {
    font-size: 13px;
    color: #64748b;
    margin-top: 3px;
}

.edit-client-layout {
    display: grid;
    grid-template-columns: 560px 1fr;
    gap: 2rem;
    align-items: start;
}

@media (max-width: 1024px) {
    .edit-client-layout {
        grid-template-columns: 1fr;
        gap: 1.5rem;
    }

    .edit-client-left {
        position: static !important;
    }
}

.edit-client-left {
    position: sticky;
    top: 1rem;
}

/* ── Cards mejoradas ── */
.card-modern {
    background: rgb(255, 255, 255);
    border-radius: 16px;
    border: 1px solid #e9edf2;
    padding: 1.5rem;
    margin-bottom: 1.5rem;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.04);
    transition: box-shadow .2s;
}

.card-modern:hover {
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.06);
}

.card-modern .card-header-custom {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 1.5rem;
    padding-bottom: 0.75rem;
    border-bottom: 2px solid #f1f5f9;
    flex-wrap: wrap;
    gap: 0.5rem;
}

.card-modern .card-title-custom {
    font-size: 16px;
    font-weight: 700;
    color: #0f172a;
    display: flex;
    align-items: center;
    gap: 8px;
}

.card-modern .card-title-custom i {
    color: #6366f1;
    font-size: 18px;
}

.card-modern .card-sub-custom {
    font-size: 13px;
    color: #94a3b8;
    margin-top: 2px;
}

/* ── Campos de formulario mejorados ── */
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
    margin-bottom: 6px;
}

.field-group label .req {
    color: #ef4444;
    margin-left: 2px;
}

.field-group input,
.field-group select {
    width: 100%;
    padding: 0.7rem 0.9rem;
    border: 1.5px solid #e2e8f0;
    border-radius: 10px;
    font-size: 14px;
    background: #fafbfc;
    transition: all .2s;
    color: #0f172a;
    box-sizing: border-box;
}

.field-group input:focus,
.field-group select:focus {
    border-color: #6366f1;
    outline: none;
    background: #ffffff;
    box-shadow: 0 0 0 4px rgba(99, 102, 241, 0.08);
}

.field-group input::placeholder {
    color: #94a3b8;
}

.field-grid-2 {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 1rem;
}

@media (max-width: 640px) {
    .field-grid-2 {
        grid-template-columns: 1fr;
        gap: 1.25rem;
    }
}

/* ── Combos buscables (país / ciudad) ── */
.combo-wrap {
    position: relative;
}

.combo-input {
    width: 100%;
    padding: 0.7rem 0.9rem;
    border: 1.5px solid #e2e8f0;
    border-radius: 10px;
    font-size: 14px;
    color: #0f172a;
    outline: none;
    transition: all .2s;
    background: #fafbfc;
    box-sizing: border-box;
}

.combo-input:focus {
    border-color: #6366f1;
    background: #fff;
    box-shadow: 0 0 0 4px rgba(99, 102, 241, 0.08);
}

.combo-input[disabled] {
    background: #f1f5f9;
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
    padding: .6rem .8rem;
    font-size: 13px;
    color: #0f172a;
    cursor: pointer;
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

/* ── Botones de acción ── */
.btn-actions {
    display: flex;
    gap: 1rem;
    margin-top: 1.5rem;
    padding-top: 1.5rem;
    border-top: 2px solid #f1f5f9;
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
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
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
    justify-content: center;
    gap: 6px;
}

.btn-secondary:hover {
    background: #e2e8f0;
    color: #0f172a;
}

/* ── Contactos - TABLA ── */
.contact-section {
    background: #f8fafc;
    border: 1.5px solid #e9edf2;
    border-radius: 16px;
    padding: 1.5rem;
    margin-bottom: 1.5rem;
}

.contact-section .section-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 1rem;
    gap: 0.75rem;
    flex-wrap: wrap;
}

.contact-section .section-title {
    font-size: 16px;
    font-weight: 700;
    color: #0f172a;
    display: flex;
    align-items: center;
    gap: 8px;
    flex-wrap: wrap;
}

.contact-section .section-title i {
    color: #6366f1;
    font-size: 18px;
}

.contact-section .section-hint {
    font-size: 13px;
    color: #94a3b8;
    margin-top: 2px;
}

.add-contact-btn {
    padding: 0.5rem 1.2rem;
    background: #6366f1;
    border: none;
    border-radius: 10px;
    font-size: 12px;
    font-weight: 600;
    color: #fff;
    cursor: pointer;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 6px;
    white-space: nowrap;
    transition: all .2s;
    box-shadow: 0 2px 8px rgba(99, 102, 241, 0.2);
}

.add-contact-btn:hover {
    background: #4f46e5;
    transform: translateY(-2px);
    box-shadow: 0 4px 16px rgba(99, 102, 241, 0.3);
}

.contact-counter {
    background: #e2e8f0;
    color: #475569;
    font-size: 10px;
    padding: 2px 10px;
    border-radius: 12px;
    font-weight: 700;
    margin-left: 8px;
}

.contact-table-wrapper {
    overflow-x: auto;
    margin-top: 0.5rem;
    -webkit-overflow-scrolling: touch;
    border-radius: 10px;
}

.contact-table {
    width: 100%;
    min-width: 560px;
    border-collapse: collapse;
    font-size: 13px;
}

.contact-table thead {
    background: #f1f5f9;
    border-radius: 8px;
}

.contact-table thead th {
    padding: 0.7rem 0.8rem;
    text-align: left;
    font-size: 10px;
    font-weight: 700;
    color: #64748b;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    border-bottom: 2px solid #e2e8f0;
    white-space: nowrap;
}

.contact-table tbody tr {
    border-bottom: 1px solid #f1f5f9;
    transition: background .2s;
}

.contact-table tbody tr:hover {
    background: #f8fafc;
}

.contact-table tbody td {
    padding: 0.6rem 0.8rem;
    color: #334155;
    vertical-align: middle;
}

.contact-table .badge-principal {
    font-size: 9px;
    font-weight: 700;
    color: #b45309;
    background: #fef3c7;
    padding: 2px 10px;
    border-radius: 10px;
    display: inline-block;
    white-space: nowrap;
}

.contact-table .btn-action {
    background: transparent;
    border: 1.5px solid #e2e8f0;
    border-radius: 6px;
    width: 28px;
    height: 28px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    font-size: 13px;
    transition: all .2s;
    margin: 0 2px;
    flex-shrink: 0;
}

.contact-table .btn-action:hover {
    transform: scale(1.05);
}

.contact-table .btn-edit-contact {
    color: #6366f1;
    border-color: #c7d2fe;
}

.contact-table .btn-edit-contact:hover {
    background: #eef2ff;
    border-color: #6366f1;
}

.contact-table .btn-remove-contact {
    color: #991b1b;
    border-color: #fecaca;
}

.contact-table .btn-remove-contact:hover {
    background: #fee2e2;
    border-color: #f87171;
}

.contact-table .empty-message {
    text-align: center;
    padding: 2rem;
    color: #94a3b8;
    font-size: 14px;
}

.contact-table .empty-message i {
    font-size: 32px;
    display: block;
    margin-bottom: 0.5rem;
    color: #cbd5e1;
}

.contact-table .action-cell {
    display: flex;
    gap: 4px;
    align-items: center;
}

/* ── Modal mejorado ── */
.modal-overlay {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(15, 23, 42, 0.6);
    display: none;
    justify-content: center;
    align-items: center;
    z-index: 9999;
    backdrop-filter: blur(6px);
    padding: 1rem;
    box-sizing: border-box;
}

.modal-overlay.active {
    display: flex;
}

.modal-box {
    background: #fff;
    border-radius: 20px;
    padding: 2rem 2.5rem;
    max-width: 580px;
    width: 92%;
    max-height: 90vh;
    overflow-y: auto;
    box-shadow: 0 24px 64px rgba(0, 0, 0, 0.25);
    animation: modalFade .3s ease;
    box-sizing: border-box;
}

@keyframes modalFade {
    from {
        opacity: 0;
        transform: scale(0.96) translateY(12px);
    }

    to {
        opacity: 1;
        transform: scale(1) translateY(0);
    }
}

.modal-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 1.5rem;
    padding-bottom: 1rem;
    border-bottom: 2px solid #f1f5f9;
    gap: 0.75rem;
}

.modal-title {
    font-size: 20px;
    font-weight: 700;
    color: #0f172a;
    display: flex;
    align-items: center;
    gap: 10px;
}

.modal-title i {
    color: #6366f1;
    font-size: 22px;
}

.modal-close {
    background: #f1f5f9;
    border: none;
    border-radius: 50%;
    width: 40px;
    height: 40px;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    font-size: 20px;
    color: #475569;
    transition: all .2s;
    flex-shrink: 0;
}

.modal-close:hover {
    background: #e2e8f0;
    color: #0f172a;
    transform: rotate(90deg);
}

.modal-body .field-group {
    margin-bottom: 1.2rem;
}

.modal-body .field-group label {
    font-size: 12px;
    font-weight: 700;
    color: #475569;
    display: block;
    margin-bottom: 5px;
}

.modal-body .field-group label .req {
    color: #ef4444;
}

.modal-body .field-group input,
.modal-body .field-group select {
    width: 100%;
    padding: 0.7rem 0.9rem;
    border: 1.5px solid #e2e8f0;
    border-radius: 10px;
    font-size: 14px;
    transition: all .2s;
    box-sizing: border-box;
}

.modal-body .field-group input:focus,
.modal-body .field-group select:focus {
    border-color: #6366f1;
    outline: none;
    box-shadow: 0 0 0 4px rgba(99, 102, 241, 0.08);
}

.modal-body .field-group.phone-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 0.8rem;
}

.modal-footer {
    display: flex;
    justify-content: flex-end;
    gap: 1rem;
    margin-top: 1.5rem;
    padding-top: 1.2rem;
    border-top: 2px solid #f1f5f9;
}

.modal-footer .btn {
    padding: 0.7rem 2rem;
    border-radius: 10px;
    font-weight: 600;
    font-size: 13px;
    cursor: pointer;
    border: none;
    transition: all .2s;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 6px;
}

.modal-footer .btn-secondary {
    background: #f1f5f9;
    color: #475569;
}

.modal-footer .btn-secondary:hover {
    background: #e2e8f0;
    color: #0f172a;
}

.modal-footer .btn-primary {
    background: #6366f1;
    color: #fff;
}

.modal-footer .btn-primary:hover {
    background: #4f46e5;
    transform: translateY(-2px);
    box-shadow: 0 4px 16px rgba(99, 102, 241, 0.3);
}

/* ══════════════════════════════════════════════
   BREAKPOINTS RESPONSIVE
   ══════════════════════════════════════════════ */

/* Tablets (≤ 768px) */
@media (max-width: 768px) {
    .page-header .page-title {
        font-size: 19px;
    }

    .card-modern {
        padding: 1.25rem;
    }

    .contact-section {
        padding: 1.25rem;
    }

    .modal-box {
        padding: 1.5rem 1.75rem;
    }
}

/* Móviles grandes (≤ 640px) */
@media (max-width: 640px) {
    .page-header {
        flex-direction: column;
        align-items: flex-start;
    }

    .page-header a.btn-secondary {
        align-self: stretch;
        justify-content: center;
    }

    .btn-actions {
        flex-direction: column;
    }

    .btn-actions .btn-primary,
    .btn-actions .btn-secondary {
        width: 100%;
    }

    .contact-section .section-header {
        flex-direction: column;
        align-items: stretch;
    }

    .add-contact-btn {
        width: 100%;
    }

    .contact-table thead th,
    .contact-table tbody td {
        padding: 0.5rem;
        font-size: 12px;
    }

    .contact-table .badge-principal {
        font-size: 8px;
        padding: 2px 6px;
    }

    .modal-body .field-group.phone-grid {
        grid-template-columns: 1fr;
    }

    .modal-footer {
        flex-direction: column-reverse;
    }

    .modal-footer .btn {
        width: 100%;
    }
}

/* Móviles pequeños (≤ 480px) */
@media (max-width: 480px) {
    .card-modern,
    .contact-section {
        padding: 1rem;
        border-radius: 12px;
    }

    .page-header .page-title {
        font-size: 18px;
    }

    .page-header .page-sub {
        font-size: 12px;
    }

    .card-modern .card-title-custom,
    .contact-section .section-title {
        font-size: 14.5px;
    }

    .field-group input,
    .field-group select,
    .combo-input {
        padding: 0.6rem 0.75rem;
        font-size: 13.5px;
    }

    .modal-overlay {
        align-items: flex-end;
        padding: 0;
    }

    .modal-box {
        width: 100%;
        max-width: 100%;
        max-height: 92vh;
        border-radius: 20px 20px 0 0;
        padding: 1.25rem 1.25rem 1.5rem;
    }

    .modal-title {
        font-size: 17px;
    }
}

/* Móviles muy pequeños (≤ 380px) */
@media (max-width: 380px) {
    .contact-table {
        min-width: 480px;
    }

    .add-contact-btn,
    .btn-primary,
    .btn-secondary {
        font-size: 13px;
        padding: 0.65rem 1rem;
    }
}
</style>
@endpush

@section('content')

<div class="page-header">
    <div>
        <div class="page-title">Nuevo Cliente</div>
        <div class="page-sub">Completa los datos de la empresa y agrega sus contactos</div>
    </div>
    <a href="{{ route('admin.clients.index') }}" class="btn btn-secondary btn-sm">
        <i class="ti ti-arrow-left" style="font-size:13px"></i> Volver
    </a>
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

<form action="{{ route('admin.clients.store') }}" method="POST" id="form-client">
    @csrf

    <div class="edit-client-layout">

        {{-- ══════════ COLUMNA IZQUIERDA ══════════ --}}
        <div class="edit-client-left">

            {{-- Datos de la empresa --}}
            <div class="card-modern">
                <div class="card-header-custom">
                    <div>
                        <div class="card-title-custom">
                            <i class="ti ti-building"></i> Datos de la empresa
                        </div>
                        <div class="card-sub-custom">Información general que identifica a la agencia o cliente</div>
                    </div>
                </div>

                <div class="field-group">
                    <label>Nombre comercial <span class="req">*</span></label>
                    <input type="text" name="name_client"
                           value="{{ old('name_client') }}"
                           placeholder="Ej: Fiesta Tours Perú S.A.C."
                           maxlength="100" required autofocus>
                </div>

                <div class="field-group">
                    <label>Razón Social</label>
                    <input type="text" name="business_name"
                           value="{{ old('business_name') }}"
                           placeholder="Razón social de la empresa"
                           maxlength="150">
                </div>

                <div class="field-group">
                    <label>Código tributario (RUC)</label>
                    <input type="text" name="tax_code"
                           value="{{ old('tax_code') }}"
                           placeholder="Ej: 20123456789"
                           maxlength="20">
                </div>

                <div class="field-group">
                    <label>Email general</label>
                    <input type="email" name="general_email"
                           value="{{ old('general_email') }}"
                           placeholder="contacto@empresa.com"
                           maxlength="120">
                </div>

                <div class="field-group">
                    <label>Tipo de Cliente</label>
                    <select name="type_client">
                        <option value="">— Seleccionar —</option>
                        <option value="cliente" {{ old('type_client') == 'cliente' ? 'selected' : '' }}>Cliente</option>
                        <option value="prospecto" {{ old('type_client') == 'prospecto' ? 'selected' : '' }}>Prospecto</option>
                    </select>
                </div>

                <div class="field-group">
                    <label>Teléfono general</label>
                    <input type="text" name="general_phone"
                           value="{{ old('general_phone') }}"
                           placeholder="Ej: 01-234567"
                           maxlength="20">
                </div>
            </div>
        </div>

        {{-- ══════════ COLUMNA DERECHA ══════════ --}}
        <div class="edit-client-right">

            {{-- Ubicación --}}
            <div class="card-modern">
                <div class="card-header-custom">
                    <div>
                        <div class="card-title-custom">
                            <i class="ti ti-map-pin"></i> Ubicación
                        </div>
                        <div class="card-sub-custom">Escribe para buscar el país y la ciudad</div>
                    </div>
                </div>

                <div class="field-grid-2">
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
                        {{-- ✅ CORREGIDO: name="id_cities" en lugar de id_countries --}}
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

                <div class="field-group">
                    <label>Dirección</label>
                    <input type="text" name="address"
                           value="{{ old('address') }}"
                           placeholder="Ej: Avenida Suecia, calle 124, primera casa"
                           maxlength="200">
                </div>
            </div>

            {{-- Contactos - TABLA --}}
            <div class="contact-section">
                <div class="section-header">
                    <div>
                        <div class="section-title">
                            <i class="ti ti-users"></i> Contactos
                            <span class="contact-counter" id="contact-counter">0</span>
                        </div>
                        <div class="section-hint">El primer contacto se registrará como el representante principal</div>
                    </div>
                    <button type="button" class="add-contact-btn" onclick="abrirModalContacto()">
                        <i class="ti ti-plus"></i> Añadir contacto
                    </button>
                </div>

                <div class="contact-table-wrapper">
                    <table class="contact-table">
                        <thead>
                            <tr>
                                <th style="width:40px">#</th>
                                <th>Nombre</th>
                                <th>Email</th>
                                <th>Teléfono</th>
                                <th style="width:90px">Estado</th>
                                <th style="width:90px">Acciones</th>
                            </tr>
                        </thead>
                        <tbody id="contact-table-body">
                            <!-- Los contactos se agregan aquí dinámicamente -->
                        </tbody>
                    </table>
                    <div id="empty-contacts" class="empty-message">
                        <i class="ti ti-user-off"></i>
                        No hay contactos registrados
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Botones de acción (siempre al final del formulario) --}}
    <div class="btn-actions">
        <button type="submit" class="btn-primary">
            <i class="ti ti-plus"></i> Crear cliente
        </button>
        <a href="{{ route('admin.clients.index') }}" class="btn-secondary">
            <i class="ti ti-x"></i> Cancelar
        </a>
    </div>
</form>

{{-- ══════════ MODAL PARA NUEVO/EDITAR CONTACTO ══════════ --}}
<div class="modal-overlay" id="modal-contacto">
    <div class="modal-box">
        <div class="modal-header">
            <div class="modal-title" id="modal-contact-title">
                <i class="ti ti-user-plus"></i>
                Nuevo Contacto
            </div>
            <button type="button" class="modal-close" onclick="cerrarModalContacto()">
                <i class="ti ti-x"></i>
            </button>
        </div>
        <div class="modal-body">
            <input type="hidden" id="modal-contact-edit-id" value="">
            <div class="field-group">
                <label>Nombre <span class="req">*</span></label>
                <input type="text" id="modal-contact-name" placeholder="Nombre del contacto" required>
            </div>
            <div class="field-group">
                <label>Apellidos</label>
                <input type="text" id="modal-contact-lastnames" placeholder="Apellidos del contacto">
            </div>
            <div class="field-group">
                <label>Correo electrónico</label>
                <input type="email" id="modal-contact-email" placeholder="ejemplo@correo.com">
            </div>
            <div class="field-group">
                <label>Cargo / Puesto</label>
                <input type="text" id="modal-contact-qualification" placeholder="Ej: Gerente, Coordinador...">
            </div>
            <div class="field-group phone-grid">
                <div>
                    <label>Teléfono 1</label>
                    <input type="text" id="modal-contact-phone1" placeholder="Teléfono principal">
                </div>
                <div>
                    <label>Teléfono 2</label>
                    <input type="text" id="modal-contact-phone2" placeholder="Teléfono opcional">
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" onclick="cerrarModalContacto()">
                <i class="ti ti-x"></i> Cancelar
            </button>
            <button type="button" class="btn btn-primary" id="modal-contact-save-btn" onclick="guardarContactoModal()">
                <i class="ti ti-check"></i> Agregar contacto
            </button>
        </div>
    </div>
</div>

@push('scripts')
<script>
    window.geoPaisesUrl = "{{ url('api/geo/paises') }}";
    window.geoCiudadesUrl = "{{ url('api/geo/ciudades') }}";
</script>

<script>
/**
 * CLIENTS CREATE - JAVASCRIPT
 * Módulo para la creación de clientes (contactos, combos, modales)
 */

// ============================================================
// CONTACTOS - GESTIÓN
// ============================================================
let contactos = [];
let editandoId = null;

function agregarContactoATabla(contacto) {
    const tbody = document.getElementById('contact-table-body');
    const empty = document.getElementById('empty-contacts');

    empty.style.display = 'none';

    const existingRow = document.getElementById(`contact-row-${contacto.id}`);
    if (existingRow) {
        actualizarFilaContacto(contacto);
        return;
    }

    const tr = document.createElement('tr');
    tr.id = `contact-row-${contacto.id}`;
    tr.innerHTML = generarFilaContacto(contacto);
    tbody.appendChild(tr);

    actualizarContador();
}

function generarFilaContacto(contacto) {
    return `
        <td>${contacto.id}</td>
        <td><strong>${escapeHtml(contacto.name)}</strong> ${contacto.lastnames ? escapeHtml(contacto.lastnames) : ''}</td>
        <td>${contacto.email ? escapeHtml(contacto.email) : '-'}</td>
        <td>${contacto.phone1 ? escapeHtml(contacto.phone1) : '-'}</td>
        <td>${contacto.principal ? '<span class="badge-principal"><i class="ti ti-star-filled"></i> Principal</span>' : ''}</td>
        <td>
            <div class="action-cell">
                <button type="button" class="btn-action btn-edit-contact" onclick="editarContacto(${contacto.id})" title="Editar contacto">
                    <i class="ti ti-pencil"></i>
                </button>
                <button type="button" class="btn-action btn-remove-contact" onclick="eliminarContacto(${contacto.id})" title="Eliminar contacto">
                    <i class="ti ti-trash"></i>
                </button>
            </div>
        </td>
    `;
}

function actualizarFilaContacto(contacto) {
    const row = document.getElementById(`contact-row-${contacto.id}`);
    if (row) {
        row.innerHTML = generarFilaContacto(contacto);
    }
}

function eliminarContacto(id) {
    if (!confirm('¿Estás seguro de eliminar este contacto?')) return;

    const eraPrincipal = contactos.find(c => c.id === id)?.principal;
    contactos = contactos.filter(c => c.id !== id);

    if (eraPrincipal && contactos.length > 0) {
        contactos[0].principal = true;
        actualizarFilaContacto(contactos[0]);
    }

    const row = document.getElementById(`contact-row-${id}`);
    if (row) row.remove();

    if (contactos.length === 0) {
        document.getElementById('empty-contacts').style.display = 'block';
    }

    actualizarContador();
}

function editarContacto(id) {
    const contacto = contactos.find(c => c.id === id);
    if (!contacto) return;

    editandoId = id;
    document.getElementById('modal-contact-title').innerHTML = '<i class="ti ti-pencil"></i> Editar Contacto';
    document.getElementById('modal-contact-save-btn').innerHTML = '<i class="ti ti-check"></i> Actualizar contacto';
    document.getElementById('modal-contact-edit-id').value = id;

    document.getElementById('modal-contact-name').value = contacto.name || '';
    document.getElementById('modal-contact-lastnames').value = contacto.lastnames || '';
    document.getElementById('modal-contact-email').value = contacto.email || '';
    document.getElementById('modal-contact-qualification').value = contacto.qualification || '';
    document.getElementById('modal-contact-phone1').value = contacto.phone1 || '';
    document.getElementById('modal-contact-phone2').value = contacto.phone2 || '';

    document.getElementById('modal-contacto').classList.add('active');
    setTimeout(() => document.getElementById('modal-contact-name').focus(), 150);
}

function actualizarContador() {
    document.getElementById('contact-counter').textContent = contactos.length;
}

// ============================================================
// MODAL CONTACTO
// ============================================================
function abrirModalContacto() {
    editandoId = null;
    document.getElementById('modal-contact-title').innerHTML = '<i class="ti ti-user-plus"></i> Nuevo Contacto';
    document.getElementById('modal-contact-save-btn').innerHTML = '<i class="ti ti-check"></i> Agregar contacto';
    document.getElementById('modal-contact-edit-id').value = '';
    document.getElementById('modal-contact-name').value = '';
    document.getElementById('modal-contact-lastnames').value = '';
    document.getElementById('modal-contact-email').value = '';
    document.getElementById('modal-contact-qualification').value = '';
    document.getElementById('modal-contact-phone1').value = '';
    document.getElementById('modal-contact-phone2').value = '';
    document.getElementById('modal-contacto').classList.add('active');
    setTimeout(() => document.getElementById('modal-contact-name').focus(), 150);
}

function cerrarModalContacto() {
    document.getElementById('modal-contacto').classList.remove('active');
    editandoId = null;
}

function guardarContactoModal() {
    const name = document.getElementById('modal-contact-name').value.trim();
    if (!name) {
        alert('El nombre del contacto es obligatorio.');
        document.getElementById('modal-contact-name').focus();
        return;
    }

    const lastnames = document.getElementById('modal-contact-lastnames').value.trim();
    const email = document.getElementById('modal-contact-email').value.trim();
    const qualification = document.getElementById('modal-contact-qualification').value.trim();
    const phone1 = document.getElementById('modal-contact-phone1').value.trim();
    const phone2 = document.getElementById('modal-contact-phone2').value.trim();

    const editId = parseInt(document.getElementById('modal-contact-edit-id').value);

    if (editId) {
        const index = contactos.findIndex(c => c.id === editId);
        if (index !== -1) {
            contactos[index] = {
                ...contactos[index],
                name: name,
                lastnames: lastnames,
                email: email,
                qualification: qualification,
                phone1: phone1,
                phone2: phone2
            };
            actualizarFilaContacto(contactos[index]);
        }
    } else {
        const contacto = {
            id: contactos.length + 1,
            name: name,
            lastnames: lastnames,
            email: email,
            qualification: qualification,
            phone1: phone1,
            phone2: phone2,
            principal: contactos.length === 0
        };
        contactos.push(contacto);
        agregarContactoATabla(contacto);
    }

    cerrarModalContacto();
}

// ============================================================
// UTILIDADES
// ============================================================
function escapeHtml(text) {
    if (!text) return '';
    const map = {
        '&': '&amp;',
        '<': '&lt;',
        '>': '&gt;',
        '"': '&quot;',
        "'": '&#039;'
    };
    return text.replace(/[&<>"']/g, function(m) { return map[m]; });
}

// ============================================================
// ENVÍO DE FORMULARIO
// ============================================================
document.getElementById('form-client').addEventListener('submit', function(e) {
    document.querySelectorAll('input[name^="contacts"]').forEach(el => el.remove());

    contactos.forEach((contacto, index) => {
        // Mapeo: clave en el objeto JS -> nombre de campo que espera el controller
        const fieldMap = {
            name: 'name',
            lastnames: 'last_names',
            email: 'email',
            qualification: 'qualification',
            phone1: 'first_phone',
            phone2: 'second_phone'
        };

        Object.entries(fieldMap).forEach(([jsKey, backendKey]) => {
            if (contacto[jsKey]) {
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = `contacts[${index}][${backendKey}]`;
                input.value = contacto[jsKey];
                this.appendChild(input);
            }
        });

        if (contacto.principal) {
            const principalInput = document.createElement('input');
            principalInput.type = 'hidden';
            principalInput.name = `contacts[${index}][es_principal]`;
            principalInput.value = '1';
            this.appendChild(principalInput);
        }
    });
});

// ============================================================
// COMBO BUSCABLE GENÉRICO
// ============================================================
function crearCombo({ inputId, listId, clearId, onSelect, onClear }) {
    const input = document.getElementById(inputId);
    const list = document.getElementById(listId);
    const clear = document.getElementById(clearId);
    let options = [];
    let activeIndex = -1;

    function normalizar(str) {
        return (str || '').toString().toLowerCase()
            .normalize('NFD').replace(/[\u0300-\u036f]/g, '');
    }

    function render(filtro) {
        const term = normalizar(filtro);
        const filtradas = term ? options.filter(o => normalizar(o.label).includes(term)) : options;
        list.innerHTML = filtradas.length === 0
            ? '<div class="combo-empty">Sin resultados</div>'
            : filtradas.map((o, idx) => `<div class="combo-item" data-idx="${idx}">${o.label}</div>`).join('');
        list._filtradas = filtradas;
        activeIndex = -1;
        list.classList.add('show');
    }

    function cerrar() {
        list.classList.remove('show');
        activeIndex = -1;
    }

    function seleccionar(opt) {
        input.value = opt.label;
        clear.classList.add('show');
        cerrar();
        onSelect(opt);
    }

    function actualizarActivo() {
        list.querySelectorAll('.combo-item').forEach(el => el.classList.remove('active'));
        const el = list.querySelector(`[data-idx="${activeIndex}"]`);
        if (el) {
            el.classList.add('active');
            el.scrollIntoView({ block: 'nearest' });
        }
    }

    input.addEventListener('focus', () => {
        if (!input.disabled) render(input.value);
    });

    input.addEventListener('input', () => {
        clear.classList.toggle('show', input.value !== '');
        render(input.value);
        if (onClear) onClear(false);
    });

    input.addEventListener('keydown', (e) => {
        const filtradas = list._filtradas || [];
        if (!list.classList.contains('show')) return;
        if (e.key === 'ArrowDown') {
            e.preventDefault();
            activeIndex = Math.min(activeIndex + 1, filtradas.length - 1);
            actualizarActivo();
        } else if (e.key === 'ArrowUp') {
            e.preventDefault();
            activeIndex = Math.max(activeIndex - 1, 0);
            actualizarActivo();
        } else if (e.key === 'Enter') {
            e.preventDefault();
            if (activeIndex >= 0 && filtradas[activeIndex]) seleccionar(filtradas[activeIndex]);
        } else if (e.key === 'Escape') {
            cerrar();
        }
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

// ============================================================
// INICIALIZACIÓN - COMBOS DE PAÍS Y CIUDAD
// ============================================================
const comboPais = crearCombo({

    inputId: 'create-pais-input',
    listId: 'create-pais-list',
    clearId: 'create-pais-clear',

    onSelect: (opt) => {
        document.getElementById('create-country-id').value = opt.value;
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
        // Guardamos el ID de la ciudad (valor)
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
                paises.map(p => ({
                    value: p.id,      // ID para cargar ciudades
                    label: p.nombre   // NOMBRE para mostrar
                })),
                'Escribe para buscar país...'
            );

            // Si hay un país preseleccionado (edición), cargar sus ciudades
            const paisPreseleccionado = document.getElementById('create-country-id').value;
            if (paisPreseleccionado) {
                cargarCiudades(paisPreseleccionado);
            }
        })
        .catch(() => comboPais.setOptions([], 'No se pudo cargar países'));
}

function cargarCiudades(countryId) {
    comboCiudad.disable('Cargando ciudades...');

    if (!countryId) {
        comboCiudad.disable('Seleccione país primero');
        return;
    }

    // ✅ Usar country_id como parámetro
    fetch(`${window.geoCiudadesUrl}?country_id=${countryId}`, {
        headers: {
            'Accept': 'application/json'
        }
    })
    .then(response => {
        if (!response.ok) {
            throw new Error('Error al cargar ciudades');
        }
        return response.json();
    })
    .then(data => {
        // ✅ Verificar que data sea un array
        if (!Array.isArray(data)) {
            console.error('Respuesta no es un array:', data);
            comboCiudad.setOptions([], 'Error en los datos');
            return;
        }

        comboCiudad.setOptions(
            data.map(item => ({
                value: item.id,
                label: item.name || item.nombre || 'Sin nombre'
            })),
            'Escribe para buscar ciudad...'
        );
    })
    .catch(error => {
        console.error('Error cargando ciudades:', error);
        comboCiudad.setOptions([], 'No se pudo cargar ciudades');
    });
}

// ============================================================
// INICIALIZACIÓN - EVENTOS DEL DOM
// ============================================================
document.addEventListener('DOMContentLoaded', function() {
    // Cargar países al inicio
    cargarPaises();

    // Cerrar modal con ESC
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            cerrarModalContacto();
        }
    });

    // Cerrar modal haciendo clic fuera
    document.getElementById('modal-contacto').addEventListener('click', function(e) {
        if (e.target === this) {
            cerrarModalContacto();
        }
    });
});
</script>
@endpush
@endsection
