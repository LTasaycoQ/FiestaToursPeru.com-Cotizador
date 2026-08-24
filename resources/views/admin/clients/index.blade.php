@extends('layouts.app')
@section('title', 'Clientes')

@push('styles')
<style>
.filter-bar {
    display: flex;
    align-items: center;
    gap: .6rem;
    flex-wrap: wrap;
    margin-bottom: 1rem;
    padding: .9rem 1.1rem;
    background: #fff;
    border: 1px solid #e2e8f0;
    border-radius: 12px;
}
.filter-input {
    padding: .5rem .85rem;
    border: 1px solid #e2e8f0;
    border-radius: 8px;
    font-size: 13px;
    color: #0f172a;
    background: #f8fafc;
    outline: none;
    transition: border-color .15s;
}
.filter-input:focus { border-color: #6366f1; background: #fff; }
.filter-sep { width: 1px; height: 24px; background: #e2e8f0; flex-shrink: 0; }

/* Bulk bar */
.bulk-bar {
    display: none;
    align-items: center;
    gap: .8rem;
    padding: .75rem 1.1rem;
    background: #0f172a;
    border-radius: 10px;
    margin-bottom: 1rem;
}
.bulk-bar.visible { display: flex; }
.bulk-count { font-size: 13px; font-weight: 600; color: #fff; }
.bulk-sep { color: rgba(255,255,255,.2); }

/* Checkbox */
.cb-wrap { display: flex; align-items: center; justify-content: center; }
input[type="checkbox"] {
    width: 15px; height: 15px;
    accent-color: #e63232;
    cursor: pointer;
}
tr.selected td { background: #fef2f2 !important; }
tr.trashed td { background: #fef2f2 !important; opacity: 0.85; }
tr.trashed td .ti-trash { color: #dc2626; }

/* Contador de resultados */
.results-count {
    font-size: 12px;
    color: #94a3b8;
    margin-left: auto;
}

/* Filtros de estado */
.filter-status-btn {
    transition: all 0.15s ease;
    padding: 0.3rem 1rem;
    border-radius: 20px;
    font-size: 12px;
    font-weight: 600;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 5px;
    border: 1px solid #e2e8f0;
}
.filter-status-btn:hover:not(.active) {
    background: #e2e8f0;
    transform: translateY(-1px);
}
.filter-status-btn.active {
    cursor: default;
}
.filter-status-btn .badge-count {
    padding: 0 6px;
    border-radius: 10px;
    font-size: 10px;
}

#modal-export {
    position: fixed;
    inset: 0;
    background: rgba(0,0,0,.45);
    z-index: 1000;
    display: none;
    align-items: center;
    justify-content: center;
}
#modal-export.show { display: flex; }

.modal-box {
    background: #fff;
    border-radius: 16px;
    width: 100%;
    max-width: 480px;
    max-height: 90vh;
    overflow-y: auto;
    padding: 2rem;
    position: relative;
    animation: modalFadeIn .2s ease-out;
}
@keyframes modalFadeIn {
    from { opacity: 0; transform: scale(0.95) translateY(10px); }
    to { opacity: 1; transform: scale(1) translateY(0); }
}

.modal-close {
    position: absolute;
    top: 1rem;
    right: 1rem;
    background: none;
    border: none;
    font-size: 22px;
    cursor: pointer;
    color: #94a3b8;
    transition: color .15s;
}
.modal-close:hover { color: #0f172a; }

.export-option {
    border: 2px solid #e2e8f0;
    border-radius: 12px;
    padding: 1rem 1.2rem;
    cursor: pointer;
    transition: all .2s;
    margin-bottom: .8rem;
    display: flex;
    align-items: center;
    gap: 12px;
}
.export-option:hover {
    border-color: #16a34a;
    background: #f0fdf4;
}
.export-option .icon {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 18px;
    flex-shrink: 0;
}
.export-option .icon.green { background: #f0fdf4; color: #16a34a; }
.export-option .icon.blue { background: #eff6ff; color: #3b82f6; }
.export-option .title { font-size: 14px; font-weight: 600; color: #0f172a; }
.export-option .sub { font-size: 12px; color: #94a3b8; }
.export-option .arrow { color: #94a3b8; font-size: 18px; margin-left: auto; }

.export-by-id-wrapper {
    border: 2px solid #e2e8f0;
    border-radius: 12px;
    padding: 1rem 1.2rem;
    transition: all .2s;
    margin-bottom: .8rem;
}
.export-by-id-wrapper:hover { border-color: #6366f1; }
.export-by-id-wrapper .header {
    display: flex;
    align-items: center;
    gap: 12px;
}

.btn-cancel-export {
    width: 100%;
    padding: .6rem;
    background: #f1f5f9;
    border: none;
    border-radius: 8px;
    font-size: 13px;
    font-weight: 600;
    color: #64748b;
    cursor: pointer;
    transition: background .15s;
    margin-top: .4rem;
}
.btn-cancel-export:hover { background: #e2e8f0; }

/* MODAL VER CLIENTE */
@keyframes modalFadeInClient {
    from { opacity: 0; transform: scale(0.95); }
    to { opacity: 1; transform: scale(1); }
}
.custom-modal-overlay {
    position: fixed;
    inset: 0;
    background: rgba(15, 23, 42, 0.6);
    backdrop-filter: blur(4px);
    z-index: 9999;
    justify-content: center;
    align-items: center;
    padding: 1rem;
}
.custom-modal-content {
    background: #fff;
    width: 100%;
    max-width: 800px;
    border-radius: 14px;
    box-shadow: 0 25px 50px -12px rgba(0,0,0,0.25);
    display: flex;
    flex-direction: column;
    max-height: 90vh;
    animation: modalFadeInClient 0.2s ease-out;
}

/* ══════════════════════════════════════════════
   DROPDOWN ACTIONS - FIX PARA QUE NO SE CORTE
   ══════════════════════════════════════════════ */
.table-wrap {
    overflow-x: auto;
    overflow-y: visible !important;
    -webkit-overflow-scrolling: touch;
}

#tabla-clientes {
    min-width: 980px;
    overflow: visible !important;
}

#tabla-clientes tbody tr {
    position: relative;
}

#tabla-clientes td {
    overflow: visible !important;
    position: relative;
}

.dropdown-actions {
    position: relative;
    display: inline-flex;
    justify-content: center;
    align-items: center;
}

.dropdown-toggle {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 32px;
    height: 32px;
    border-radius: 8px;
    border: 1px solid #e2e8f0;
    background: #fff;
    color: #64748b;
    cursor: pointer;
    transition: all 0.15s ease;
    font-size: 16px;
    margin: 0 auto;
}

.dropdown-toggle:hover {
    border-color: #6366f1;
    color: #6366f1;
    background: #f5f3ff;
}

.dropdown-toggle:active {
    transform: scale(0.95);
}

.dropdown-menu {
    display: none;
    position: fixed;
    min-width: 200px;
    background: #fff;
    border: 1px solid #e2e8f0;
    border-radius: 12px;
    box-shadow: 0 12px 30px -8px rgba(0, 0, 0, 0.18);
    z-index: 99999 !important;
    padding: 6px;
    animation: dropdownFadeIn 0.18s ease-out;
}

.dropdown-menu.show {
    display: block;
}

.dropdown-menu .dropdown-item {
    display: flex;
    align-items: center;
    gap: 12px;
    width: 100%;
    padding: 8px 14px;
    border: none;
    border-radius: 8px;
    background: none;
    color: #334155;
    font-size: 13px;
    font-weight: 500;
    cursor: pointer;
    transition: all 0.12s ease;
    text-decoration: none;
    font-family: inherit;
    line-height: 1.4;
    text-align: left;
    box-sizing: border-box;
}

.dropdown-menu .dropdown-item:hover {
    background: #f1f5f9;
    color: #0f172a;
}

.dropdown-menu .dropdown-item .icon {
    width: 20px;
    text-align: center;
    font-size: 16px;
    flex-shrink: 0;
}

.dropdown-menu .dropdown-item .icon.blue { color: #3b82f6; }
.dropdown-menu .dropdown-item .icon.red { color: #dc2626; }
.dropdown-menu .dropdown-item .icon.purple { color: #8b5cf6; }
.dropdown-menu .dropdown-item .icon.green { color: #16a34a; }
.dropdown-menu .dropdown-item .icon.orange { color: #f59e0b; }

.dropdown-menu .dropdown-item.danger:hover {
    background: #fef2f2;
    color: #dc2626;
}

.dropdown-menu .dropdown-item.danger:hover .icon {
    color: #dc2626;
}

.dropdown-menu .dropdown-divider {
    height: 1px;
    background: #e2e8f0;
    margin: 4px 10px;
}

@keyframes dropdownFadeIn {
    from {
        opacity: 0;
        transform: scale(0.92) translateY(-8px);
    }
    to {
        opacity: 1;
        transform: scale(1) translateY(0);
    }
}

/* ══════════════════════════════════════════════
   TABLA: SCROLL HORIZONTAL EN RESPONSIVE
   ══════════════════════════════════════════════ */
#tabla-clientes th,
#tabla-clientes td {
    white-space: nowrap;
}
#tabla-clientes td:nth-child(3) div,
#tabla-clientes td:nth-child(8) {
    white-space: normal;
}

/* ══════════════════════════════════════════════
   RESPONSIVE GENERAL
   ══════════════════════════════════════════════ */
@media (max-width: 900px) {
    .filter-sep { display: none; }
    .filter-input { flex: 1 1 150px; }
    .results-count {
        width: 100%;
        margin-left: 0;
        text-align: left;
    }
}

@media (max-width: 640px) {
    #tabla-clientes { min-width: 820px; }

    .bulk-bar { flex-wrap: wrap; row-gap: .6rem; }
    .bulk-bar .bulk-delete-btn {
        margin-left: 0 !important;
        width: 100%;
        justify-content: center;
    }

    .client-info-grid {
        grid-template-columns: 1fr !important;
    }
}

@media (max-width: 480px) {
    .modal-box { padding: 1.5rem; }
    .custom-modal-content { border-radius: 12px; }
}

/* Estilos para botón buscar igual que en proveedores */
.btn-search {
    padding: .5rem 1.2rem;
    background: #6366f1;
    color: #fff;
    border: none;
    border-radius: 8px;
    font-size: 13px;
    font-weight: 600;
    cursor: pointer;
    transition: background .15s;
    display: inline-flex;
    align-items: center;
    gap: 6px;
    white-space: nowrap;
}
.btn-search:hover {
    background: #4f46e5;
}
</style>
@endpush

@section('content')

@php
    $hayFiltros = request()->anyFilled(['search','country','city','date'])
        || (request('sort') && request('sort') !== 'newest');
@endphp

<div class="page-header" style="display:flex;justify-content:space-between;align-items:flex-start;margin-bottom:1.2rem">
    <div>
        <div class="page-title">Clientes</div>
        <div class="page-sub">Gestiona todos los clientes registrados</div>
    </div>
    <div style="display:flex;gap:.6rem;align-items:center;flex-wrap:wrap">
        <a href="{{ route('admin.clients.import.view') }}"
           style="display:inline-flex;align-items:center;gap:6px;padding:.5rem .9rem;
                  background:#fff;border:1px solid #e2e8f0;border-radius:8px;
                  font-size:13px;font-weight:600;color:#6366f1;text-decoration:none">
            <i class="ti ti-file-upload" style="font-size:16px"></i> Importar
        </a>
        <a href="#" id="btn-export-pdf"
           style="display:inline-flex;align-items:center;gap:6px;padding:.5rem .9rem;
                  background:#fff;border:1px solid #e2e8f0;border-radius:8px;
                  font-size:13px;font-weight:600;color:#ef4444;text-decoration:none">
            <i class="ti ti-file-type-pdf" style="font-size:16px"></i> PDF
        </a>
        <a href="#" id="btn-export-excel"
           style="display:inline-flex;align-items:center;gap:6px;padding:.5rem .9rem;
                  background:#fff;border:1px solid #e2e8f0;border-radius:8px;
                  font-size:13px;font-weight:600;color:#16a34a;text-decoration:none">
            <i class="ti ti-file-type-xls" style="font-size:16px"></i> Excel
        </a>
        <a href="{{ route('admin.clients.create') }}" class="btn btn-primary"
            style="text-decoration:none;display:inline-flex;align-items:center;gap:6px">
            <i class="ti ti-plus" style="font-size:15px"></i> Nuevo Cliente
        </a>
    </div>
</div>

{{-- FILTROS DE ESTADO (ACTIVOS / ELIMINADOS / TODOS) --}}
<div class="filter-bar" style="margin-bottom: 1rem; background: #f8fafc; padding: 0.6rem 1.1rem;">
    <span style="font-size: 12px; font-weight: 600; color: #64748b; margin-right: 0.5rem;">
        <i class="ti ti-filter"></i> Estado:
    </span>
    <div style="display: flex; gap: 6px; flex-wrap: wrap;">
        {{-- Botón "Activos" - visible para todos --}}
        <a href="{{ route('admin.clients.index', array_merge(request()->except(['filter', 'page']), ['filter' => 'active'])) }}"
           class="filter-status-btn {{ request('filter', 'active') == 'active' ? 'active' : '' }}"
           style="background: {{ request('filter', 'active') == 'active' ? '#16a34a' : '#f1f5f9' }};
                  color: {{ request('filter', 'active') == 'active' ? '#fff' : '#64748b' }};
                  border-color: {{ request('filter', 'active') == 'active' ? '#16a34a' : '#e2e8f0' }};">
            <i class="ti ti-user-check" style="font-size: 13px;"></i>
            Activos <span class="badge-count" style="background: {{ request('filter', 'active') == 'active' ? 'rgba(255,255,255,0.2)' : '#e2e8f0' }};">{{ $activeCount ?? 0 }}</span>
        </a>

        {{-- Botones "Eliminados" y "Todos" - solo para administradores --}}
        @if(auth()->check() && auth()->user()->isAdmin())
            <a href="{{ route('admin.clients.index', array_merge(request()->except(['filter', 'page']), ['filter' => 'trashed'])) }}"
               class="filter-status-btn {{ request('filter') == 'trashed' ? 'active' : '' }}"
               style="background: {{ request('filter') == 'trashed' ? '#dc2626' : '#f1f5f9' }};
                      color: {{ request('filter') == 'trashed' ? '#fff' : '#64748b' }};
                      border-color: {{ request('filter') == 'trashed' ? '#dc2626' : '#e2e8f0' }};">
                <i class="ti ti-user-x" style="font-size: 13px;"></i>
                Eliminados <span class="badge-count" style="background: {{ request('filter') == 'trashed' ? 'rgba(255,255,255,0.2)' : '#e2e8f0' }};">{{ $trashedCount ?? 0 }}</span>
            </a>

            <a href="{{ route('admin.clients.index', array_merge(request()->except(['filter', 'page']), ['filter' => 'all'])) }}"
               class="filter-status-btn {{ request('filter') == 'all' ? 'active' : '' }}"
               style="background: {{ request('filter') == 'all' ? '#6366f1' : '#f1f5f9' }};
                      color: {{ request('filter') == 'all' ? '#fff' : '#64748b' }};
                      border-color: {{ request('filter') == 'all' ? '#6366f1' : '#e2e8f0' }};">
                <i class="ti ti-users" style="font-size: 13px;"></i>
                Todos <span class="badge-count" style="background: {{ request('filter') == 'all' ? 'rgba(255,255,255,0.2)' : '#e2e8f0' }};">{{ $totalCount ?? 0 }}</span>
            </a>
        @endif
    </div>
</div>

{{-- BARRA DE FILTROS --}}
<div class="filter-bar">
    {{-- FORMULARIO CON MÉTODO GET --}}
    <form id="filter-form" method="GET" action="{{ route('admin.clients.index') }}" style="display:flex; flex-wrap:wrap; gap:8px; align-items:center; width:100%;">
        <div style="position:relative;flex:1;min-width:200px">
            <i class="ti ti-search" style="position:absolute;left:.7rem;top:50%;transform:translateY(-50%);color:#94a3b8;font-size:15px"></i>
            <input type="text" id="f-search" name="search" class="filter-input"
                   value="{{ request('search') }}"
                   placeholder="Buscar por agencia, código tributario, email, teléfono..."
                   style="width:100%;padding-left:2.2rem">
        </div>

        <div class="filter-sep"></div>

        <select id="f-city" name="city" class="filter-input" style="min-width:150px">
            <option value="">Todas las ciudades</option>
            @foreach($cities as $cityName)
                <option value="{{ $cityName }}" {{ request('city') == $cityName ? 'selected' : '' }}>
                    {{ $cityName }}
                </option>
            @endforeach
        </select>

        <select id="f-date" name="date" class="filter-input" style="min-width:150px">
            <option value="">Cualquier fecha</option>
            <option value="today" {{ request('date') == 'today' ? 'selected' : '' }}>Hoy</option>
            <option value="week"  {{ request('date') == 'week'  ? 'selected' : '' }}>Esta semana</option>
            <option value="month" {{ request('date') == 'month' ? 'selected' : '' }}>Este mes</option>
            <option value="year"  {{ request('date') == 'year'  ? 'selected' : '' }}>Este año</option>
        </select>

        <select id="f-sort" name="sort" class="filter-input" style="min-width:170px">
            <option value="newest" {{ request('sort', 'newest') == 'newest' ? 'selected' : '' }}>Más recientes</option>
            <option value="oldest" {{ request('sort') == 'oldest' ? 'selected' : '' }}>Más antiguos</option>
            <option value="az"     {{ request('sort') == 'az'     ? 'selected' : '' }}>Agencia A → Z</option>
            <option value="za"     {{ request('sort') == 'za'     ? 'selected' : '' }}>Agencia Z → A</option>
            <option value="tax-az" {{ request('sort') == 'tax-az' ? 'selected' : '' }}>Código Tributario A → Z</option>
            <option value="tax-za" {{ request('sort') == 'tax-za' ? 'selected' : '' }}>Código Tributario Z → A</option>
        </select>

        <div class="filter-sep"></div>

        {{-- BOTÓN BUSCAR (igual que en proveedores) --}}
        <button type="submit" class="btn-search">
            <i class="ti ti-search" style="font-size:14px"></i> Buscar
        </button>

        {{-- BOTÓN LIMPIAR (igual que en proveedores) --}}
        <button type="button" onclick="clearFilters()"
                style="padding:.5rem .9rem;background:none;border:1px solid #e2e8f0;
                       border-radius:8px;font-size:13px;color:#64748b;cursor:pointer;
                       display:inline-flex;align-items:center;gap:5px;white-space:nowrap;
                       transition:all .15s"
                onmouseover="this.style.background='#f1f5f9';this.style.borderColor='#94a3b8'"
                onmouseout="this.style.background='none';this.style.borderColor='#e2e8f0'">
            <i class="ti ti-filter-off" style="font-size:14px"></i> Limpiar
        </button>

        <span class="results-count" id="results-count">{{ $clients->total() }} resultado(s)</span>
    </form>
</div>

{{-- BARRA DE ACCIONES MASIVAS --}}
<div class="bulk-bar" id="bulk-bar">
    <i class="ti ti-checkbox" style="font-size:18px;color:#e63232"></i>
    <span class="bulk-count"><span id="bulk-count">0</span> seleccionado(s)</span>
    <span class="bulk-sep">|</span>
    <button onclick="selectAll(true)"
            style="background:rgba(255,255,255,.1);border:none;color:#fff;
                   padding:.4rem .8rem;border-radius:6px;font-size:12px;cursor:pointer">
        Seleccionar todos
    </button>
    <button onclick="selectAll(false)"
            style="background:rgba(255,255,255,.1);border:none;color:#fff;
                   padding:.4rem .8rem;border-radius:6px;font-size:12px;cursor:pointer">
        Deseleccionar
    </button>
    @if(request('filter') == 'trashed' || request('filter') == 'all')
        <span class="bulk-sep">|</span>
        <button onclick="restoreSelected()" class="bulk-restore-btn"
                style="display:none; background:#16a34a; border:none; color:#fff;
                       padding:.4rem .9rem; border-radius:6px; font-size:12px; font-weight:600;
                       cursor:pointer; align-items:center; gap:5px;">
            <i class="ti ti-rotate-clockwise" style="font-size:13px;"></i> Restaurar
        </button>
        <button onclick="forceDeleteSelected()" class="bulk-force-delete-btn"
                style="display:none; background:#dc2626; border:none; color:#fff;
                       padding:.4rem .9rem; border-radius:6px; font-size:12px; font-weight:600;
                       cursor:pointer; align-items:center; gap:5px;">
            <i class="ti ti-trash-off" style="font-size:13px;"></i> Eliminar definitivo
        </button>
    @endif
    <button onclick="bulkDelete()" class="bulk-delete-btn"
            style="background:#e63232;border:none;color:#fff;padding:.4rem .9rem;
                   border-radius:6px;font-size:12px;font-weight:600;cursor:pointer;
                   display:flex;align-items:center;gap:5px;margin-left:auto">
        <i class="ti ti-trash" style="font-size:14px"></i> Eliminar seleccionados
    </button>
</div>

{{-- Formularios ocultos para acciones masivas --}}
<form id="bulk-delete-form" action="{{ route('admin.clients.bulk-destroy') }}" method="POST" style="display:none">
    @csrf
    @method('DELETE')
    <div id="bulk-ids-container"></div>
</form>

<form id="bulk-restore-form" action="{{ route('admin.clients.bulk-restore') }}" method="POST" style="display:none">
    @csrf
    <div id="bulk-restore-ids-container"></div>
</form>

<form id="bulk-force-delete-form" action="{{ route('admin.clients.bulk-force-destroy') }}" method="POST" style="display:none">
    @csrf
    @method('DELETE')
    <div id="bulk-force-ids-container"></div>
</form>

@if($clients->isEmpty())

    @if($hayFiltros)
        <div style="text-align:center;padding:3rem;background:#fff;border-radius:14px;border:1px solid #e2e8f0">
            <i class="ti ti-search-off" style="font-size:40px;color:#cbd5e1;display:block;margin-bottom:.7rem"></i>
            <p style="font-size:14px;font-weight:600;color:#475569">Sin resultados para tu búsqueda</p>
            <p style="font-size:12px;color:#94a3b8;margin-top:.3rem">Prueba con otros filtros</p>
        </div>
    @else
        <div style="text-align:center;padding:4rem;background:#fff;border-radius:14px;border:1px solid #e2e8f0">
            <i class="ti ti-building-off" style="font-size:48px;color:#cbd5e1;display:block;margin-bottom:1rem"></i>
            <p style="font-size:15px;font-weight:600;color:#0f172a;margin-bottom:.4rem">No hay clientes aún</p>
            <a href="{{ route('admin.clients.create') }}" class="btn btn-primary btn-sm" style="text-decoration:none">
                <i class="ti ti-plus"></i> Crear primer cliente
            </a>
        </div>
    @endif

@else
    <div class="table-wrap" id="table-container">
        <table id="tabla-clientes">
            <thead>
                <tr>
                    <th style="width:40px">
                        <input type="checkbox" id="check-all" title="Seleccionar todos"
                            onchange="toggleAll(this.checked)">
                    </th>
                    <th>ID</th>
                    <th>AGENCIA</th>
                    <th>CODIGO TRIBUTARIO</th>
                    <th>TIPO DE CLIENTE</th>
                    <th>EMAIL EMPRESARIAL</th>
                    <th>TELÉFONO EMPRESARIAL</th>
                    <th>UBICACIÓN</th>
                    <th>REGISTRO</th>
                    <th style="text-align:center;width:110px">ACCIONES</th>
                </tr>
            </thead>
            <tbody id="tabla-body">
                @foreach($clients as $c)
                    <tr class="client-row {{ $c->trashed() ? 'trashed' : '' }}" data-id="{{ $c->id_client }}">
                        <td class="cb-wrap">
                            <input type="checkbox" class="row-check"
                                value="{{ $c->id_client }}"
                                onchange="updateBulk()"
                                {{ $c->trashed() ? 'data-trashed="true"' : '' }}>
                        </td>
                        <td style="color:#94a3b8;font-size:12px">{{ $c->id_client }}</td>
                        <td>
                            <div style="font-weight:600;color:#0f172a;font-size:13px">
                                {{ $c->name_client }}
                                @if($c->trashed())
                                    <span style="background:#dc2626;color:#fff;font-size:9px;font-weight:700;
                                                 padding:2px 8px;border-radius:10px;margin-left:6px;">
                                        <i class="ti ti-trash" style="font-size:10px;"></i> ELIMINADO
                                    </span>
                                @endif
                            </div>
                        </td>
                        <td style="font-size:12px;color:#64748b">{{ $c->tax_code ?? '—' }}</td>
                        <td style="font-size:12px;color:#64748b">{{ strtoupper($c->type_client ?? '—') }}</td>
                        <td style="font-size:12px;color:#64748b">{{ $c->general_email ?? '—' }}</td>
                        <td style="font-size:12px;color:#64748b">{{ $c->general_phone ?? '—' }}</td>
                        <td style="font-size:12px;color:#64748b">
                            {{ $c->city->name ?? '—' }}
                            @if($c->city && $c->city->country)
                                ({{ $c->city->country->name }})
                            @endif
                        </td>
                        <td style="color:#94a3b8;font-size:12px">
                            {{ $c->created_at->format('d/m/Y') }}
                            @if($c->trashed())
                                <br><small style="color:#dc2626;">Eliminado: {{ $c->deleted_at->format('d/m/Y H:i') }}</small>
                            @endif
                        </td>
                        <td>
                            <div class="dropdown-actions">
                                <button type="button"
                                        class="dropdown-toggle"
                                        onclick="toggleDropdown({{ $c->id_client }}, this)"
                                        aria-label="Acciones">
                                    <i class="ti ti-dots-vertical"></i>
                                </button>
                                <div id="dropdown-client-{{ $c->id_client }}" class="dropdown-menu">
                                    @if(!$c->trashed())
                                        <button type="button"
                                                class="dropdown-item btn-open-modal"
                                                data-target="modal-client-{{ $c->id_client }}">
                                            <i class="ti ti-eye icon blue"></i>
                                            Ver detalles
                                        </button>
                                        <a href="{{ route('admin.clients.edit', $c->id_client) }}"
                                           class="dropdown-item">
                                            <i class="ti ti-edit icon purple"></i>
                                            Editar
                                        </a>
                                        <div class="dropdown-divider"></div>
                                        <button type="button"
                                                class="dropdown-item danger"
                                                onclick="confirmDeleteClient({{ $c->id_client }}, '{{ addslashes($c->name_client) }}')">
                                            <i class="ti ti-trash icon red"></i>
                                            Eliminar
                                        </button>
                                    @else
                                        <button type="button"
                                                class="dropdown-item btn-open-modal"
                                                data-target="modal-client-{{ $c->id_client }}">
                                            <i class="ti ti-eye icon blue"></i>
                                            Ver detalles
                                        </button>
                                        <div class="dropdown-divider"></div>
                                        <button type="button"
                                                class="dropdown-item"
                                                onclick="restoreClient({{ $c->id_client }})">
                                            <i class="ti ti-rotate-clockwise icon green"></i>
                                            Restaurar
                                        </button>
                                        <button type="button"
                                                class="dropdown-item danger"
                                                onclick="confirmForceDelete({{ $c->id_client }}, '{{ addslashes($c->name_client) }}')">
                                            <i class="ti ti-trash-off icon red"></i>
                                            Eliminar permanentemente
                                        </button>
                                    @endif
                                </div>
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        <div class="table-footer" style="display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:.5rem">
            <span id="footer-count">
                Mostrando {{ $clients->firstItem() }}–{{ $clients->lastItem() }}
                de {{ $clients->total() }} cliente(s)
            </span>
            <div class="pagination-controls" style="display:flex;align-items:center;gap:.4rem">
                @if($clients->onFirstPage())
                    <span style="padding:.35rem .7rem;border:1px solid #e2e8f0;border-radius:7px;
                                font-size:12px;color:#cbd5e1;cursor:default">
                        <i class="ti ti-chevron-left"></i>
                    </span>
                @else
                    <a href="{{ $clients->previousPageUrl() }}"
                    style="padding:.35rem .7rem;border:1px solid #e2e8f0;border-radius:7px;
                            font-size:12px;color:#374151;text-decoration:none;background:#fff;
                            transition:border-color .15s"
                    onmouseover="this.style.borderColor='#6366f1'"
                    onmouseout="this.style.borderColor='#e2e8f0'">
                        <i class="ti ti-chevron-left"></i>
                    </a>
                @endif

                @foreach($clients->getUrlRange(1, $clients->lastPage()) as $page => $url)
                    @if($page == $clients->currentPage())
                        <span style="padding:.35rem .65rem;border:1px solid #6366f1;border-radius:7px;
                                    font-size:12px;font-weight:700;color:#fff;background:#6366f1;min-width:32px;
                                    text-align:center">
                            {{ $page }}
                        </span>
                    @elseif($page == 1 || $page == $clients->lastPage() || abs($page - $clients->currentPage()) <= 1)
                        <a href="{{ $url }}"
                        style="padding:.35rem .65rem;border:1px solid #e2e8f0;border-radius:7px;
                                font-size:12px;color:#374151;text-decoration:none;background:#fff;min-width:32px;
                                text-align:center;transition:border-color .15s"
                        onmouseover="this.style.borderColor='#6366f1'"
                        onmouseout="this.style.borderColor='#e2e8f0'">
                            {{ $page }}
                        </a>
                    @elseif(abs($page - $clients->currentPage()) == 2)
                        <span style="font-size:12px;color:#94a3b8;padding:0 .2rem">…</span>
                    @endif
                @endforeach

                @if($clients->hasMorePages())
                    <a href="{{ $clients->nextPageUrl() }}"
                    style="padding:.35rem .7rem;border:1px solid #e2e8f0;border-radius:7px;
                            font-size:12px;color:#374151;text-decoration:none;background:#fff;
                            transition:border-color .15s"
                    onmouseover="this.style.borderColor='#6366f1'"
                    onmouseout="this.style.borderColor='#e2e8f0'">
                        <i class="ti ti-chevron-right"></i>
                    </a>
                @else
                    <span style="padding:.35rem .7rem;border:1px solid #e2e8f0;border-radius:7px;
                                font-size:12px;color:#cbd5e1;cursor:default">
                        <i class="ti ti-chevron-right"></i>
                    </span>
                @endif
            </div>
            <span id="footer-filter" style="color:#6366f1;font-size:12px;{{ $hayFiltros ? '' : 'display:none' }}">
                Mostrando resultados filtrados
            </span>
        </div>
    </div>

    {{-- MODALES VER CLIENTE --}}
    @foreach($clients as $c)
    <div id="modal-client-{{ $c->id_client }}" class="custom-modal-overlay" style="display:none">
        <div class="custom-modal-content">
            <div style="background: #f8fafc; border-bottom:1px solid #e2e8f0; border-top-left-radius:14px; border-top-right-radius:14px; padding:1.2rem 1.5rem; display:flex; justify-content:space-between; align-items:center">
                <div style="display:flex; align-items:center; gap:12px">
                    <div style="background: #e0e7ff; color: #4338ca; width:40px; height:40px; border-radius:10px; display:flex; align-items:center; justify-content:center">
                        <i class="ti ti-building" style="font-size:22px"></i>
                    </div>
                    <div>
                        <h5 style="font-weight:700; color:#0f172a; margin:0; font-size:16px">
                            {{ $c->name_client }}
                            @if($c->trashed())
                                <span style="background:#dc2626;color:#fff;font-size:9px;font-weight:700;
                                             padding:2px 8px;border-radius:10px;margin-left:6px;">
                                    <i class="ti ti-trash" style="font-size:10px;"></i> ELIMINADO
                                </span>
                            @endif
                        </h5>
                        <span style="font-size:12px; color:#64748b">ID Cliente: #{{ $c->id_client }}</span>
                    </div>
                </div>
                <button type="button" class="btn-close-modal" data-close="modal-client-{{ $c->id_client }}" style="background:none; border:none; color:#64748b; cursor:pointer; font-size:20px; line-height:1">
                    <i class="ti ti-x"></i>
                </button>
            </div>

            <div style="padding:1.5rem; overflow-y:auto; flex:1;">
                <div class="custom-tabs" data-client="{{ $c->id_client }}" style="display:flex; gap:8px; border-bottom:1px solid #f1f5f9; padding-bottom:12px; margin-bottom:1.5rem">
                    <button class="tab-trigger active" data-tab="info" style="border:none; cursor:pointer; font-size:13px; font-weight:600; padding:8px 16px; border-radius:8px; background: #4338ca; color:#fff; display:flex; align-items:center; gap:6px">
                        <i class="ti ti-info-circle"></i> Info. General
                    </button>
                    <button class="tab-trigger" data-tab="contacts" style="border:none; cursor:pointer; font-size:13px; font-weight:600; padding:8px 16px; border-radius:8px; background:#f1f5f9; color:#475569; display:flex; align-items:center; gap:6px">
                        <i class="ti ti-users"></i> Contactos ({{ $c->contacts->count() }})
                    </button>
                </div>

                <div class="custom-tab-contents" data-client="{{ $c->id_client }}">
                    <div class="tab-content-panel panel-info" style="display:block">
                        <div class="client-info-grid" style="display:grid; grid-template-columns: repeat(2, 1fr); gap:1.2rem">
                            <div>
                                <label style="display:block; font-size:11px; font-weight:700; color:#94a3b8; text-transform:uppercase; margin-bottom:4px">Razón Social</label>
                                <div style="font-size:14px; color:#1e293b; font-weight:500">{{ $c->business_name ?? '—' }}</div>
                            </div>
                            <div>
                                <label style="display:block; font-size:11px; font-weight:700; color:#94a3b8; text-transform:uppercase; margin-bottom:4px">Código Tributario / RUC</label>
                                <div style="font-size:14px; color:#1e293b; font-weight:500">{{ $c->tax_code ?? '—' }}</div>
                            </div>
                            <div>
                                <label style="display:block; font-size:11px; font-weight:700; color:#94a3b8; text-transform:uppercase; margin-bottom:4px">Teléfono General</label>
                                <div style="font-size:14px; color:#1e293b; font-weight:500">{{ $c->general_phone ?? '—' }}</div>
                            </div>
                            <div>
                                <label style="display:block; font-size:11px; font-weight:700; color:#94a3b8; text-transform:uppercase; margin-bottom:4px">Correo General</label>
                                <div style="font-size:14px; color:#1e293b; font-weight:500">{{ $c->general_email ?? '—' }}</div>
                            </div>
                            <div>
                                <label style="display:block; font-size:11px; font-weight:700; color:#94a3b8; text-transform:uppercase; margin-bottom:4px">País</label>
                                <div style="font-size:14px; color:#1e293b; font-weight:500">{{ $c->city->country->name ?? '—' }}</div>
                            </div>
                            <div>
                                <label style="display:block; font-size:11px; font-weight:700; color:#94a3b8; text-transform:uppercase; margin-bottom:4px">Ciudad</label>
                                <div style="font-size:14px; color:#1e293b; font-weight:500">{{ $c->city->name ?? '—' }}</div>
                            </div>
                            <div style="grid-column: span 2">
                                <label style="display:block; font-size:11px; font-weight:700; color:#94a3b8; text-transform:uppercase; margin-bottom:4px">Dirección</label>
                                <div style="font-size:14px; color:#1e293b; font-weight:500">{{ $c->address ?? '—' }}</div>
                            </div>
                            @if($c->trashed())
                                <div style="grid-column: span 2; background:#fef2f2; padding:0.8rem; border-radius:8px; border:1px solid #fecaca;">
                                    <label style="display:block; font-size:11px; font-weight:700; color:#dc2626; text-transform:uppercase; margin-bottom:4px">
                                        <i class="ti ti-trash" style="font-size:12px;"></i> Información de eliminación
                                    </label>
                                    <div style="font-size:13px; color:#991b1b;">
                                        Eliminado el: {{ $c->deleted_at->format('d/m/Y H:i:s') }}
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>

                    <div class="tab-content-panel panel-contacts" style="display:none">
                        @if($c->contacts->isEmpty())
                            <div style="text-align:center; padding:2rem; color:#94a3b8">
                                <i class="ti ti-users-minus" style="font-size:32px; display:block; margin-bottom:8px"></i>
                                <span style="font-size:13px">No hay contactos registrados para este cliente.</span>
                            </div>
                        @else
                            <div style="display:flex; flex-direction:column; gap:12px">
                                @foreach($c->contacts as $index => $contact)
                                    <div style="background:#f8fafc; border:1px solid #e2e8f0; border-radius:10px; padding:1rem; display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:.6rem">
                                        <div style="display:flex; align-items:center; gap:12px">
                                            <div style="background:#f1f5f9; color:#475569; width:36px; height:36px; border-radius:50%; display:flex; align-items:center; justify-content:center; font-weight:700; font-size:13px">
                                                {{ strtoupper(substr($contact->name, 0, 1)) }}
                                            </div>
                                            <div>
                                                <div style="font-size:14px; font-weight:600; color:#0f172a">
                                                    {{ $contact->name }} {{ $contact->last_names }}
                                                    @if($index === 0)
                                                        <span style="background:#dcfce7; color:#15803d; font-size:10px; font-weight:700; padding:2px 6px; border-radius:4px; margin-left:6px; text-transform:uppercase">Principal</span>
                                                    @endif
                                                </div>
                                                <div style="font-size:12px; color:#64748b">{{ $contact->qualification ?? 'Sin cargo asignado' }}</div>
                                            </div>
                                        </div>
                                        <div style="text-align:right; font-size:12px; color:#334155">
                                            <div><i class="ti ti-mail" style="color:#94a3b8"></i> {{ $contact->email ?? '—' }}</div>
                                            <div><i class="ti ti-phone" style="color:#94a3b8"></i> {{ $contact->first_phone ?? '—' }}</div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <div style="background:#f8fafc; border-top:1px solid #e2e8f0; border-bottom-left-radius:14px; border-bottom-right-radius:14px; padding:0.8rem 1.5rem; display:flex; justify-content:flex-end; gap:8px;">
                @if($c->trashed())
                    <button type="button" onclick="restoreClient({{ $c->id_client }})"
                            style="font-size:13px; background:#16a34a; color:#fff; border:none; padding:8px 16px; border-radius:6px; cursor:pointer; font-weight:500">
                        <i class="ti ti-rotate-clockwise"></i> Restaurar
                    </button>
                    <button type="button" onclick="confirmForceDelete({{ $c->id_client }}, '{{ addslashes($c->name_client) }}')"
                            style="font-size:13px; background:#dc2626; color:#fff; border:none; padding:8px 16px; border-radius:6px; cursor:pointer; font-weight:500">
                        <i class="ti ti-trash-off"></i> Eliminar definitivo
                    </button>
                @else
                    <button type="button" onclick="window.location.href='{{ route('admin.clients.edit', $c->id_client) }}'"
                            style="font-size:13px; background:#8b5cf6; color:#fff; border:none; padding:8px 16px; border-radius:6px; cursor:pointer; font-weight:500">
                        <i class="ti ti-edit"></i> Editar
                    </button>
                @endif
                <button type="button" class="btn-close-modal" data-close="modal-client-{{ $c->id_client }}"
                        style="font-size:13px; background:#64748b; color:#fff; border:none; padding:8px 16px; border-radius:6px; cursor:pointer; font-weight:500">
                    Cerrar
                </button>
            </div>
        </div>
    </div>
    @endforeach
@endif

{{-- MODAL EXPORTAR --}}
<div id="modal-export">
    <div class="modal-box">
        <button class="modal-close" onclick="closeExportModal()">
            <i class="ti ti-x"></i>
        </button>

        <div style="display:flex;align-items:center;gap:12px;margin-bottom:1.5rem">
            <div style="width:44px;height:44px;background:#f0fdf4;border-radius:50%;
                        display:flex;align-items:center;justify-content:center;font-size:22px">
                <i class="ti ti-file-export" id="export-modal-icon" style="color:#16a34a"></i>
            </div>
            <div>
                <h2 style="font-size:17px;font-weight:700;color:#0f172a;margin:0">
                    Exportar <span id="export-type-label" style="color:#16a34a">Excel</span>
                </h2>
                <p style="font-size:12px;color:#94a3b8;margin:0">Selecciona qué datos quieres exportar</p>
            </div>
        </div>

        <div class="export-option" onclick="exportAll()">
            <div class="icon green">
                <i class="ti ti-list"></i>
            </div>
            <div style="flex:1">
                <div class="title">Todos los clientes</div>
                <div class="sub">Exporta el listado completo de clientes</div>
            </div>
            <i class="ti ti-chevron-right arrow"></i>
        </div>

        <div class="export-by-id-wrapper" id="export-client-combo-wrap">
            <div class="header">
                <div class="icon blue">
                    <i class="ti ti-user"></i>
                </div>
                <div style="flex:1">
                    <div class="title">Cliente específico</div>
                    <div class="sub">Busca y elige un cliente por nombre</div>
                </div>
            </div>
            <div style="position:relative; margin-top:.8rem; padding-left:52px; display:flex; gap:.6rem; align-items:center">
                <div style="flex:1; position:relative">
                    <input type="text"
                           id="export-client-search"
                           placeholder="Escribe para buscar cliente..."
                           autocomplete="off"
                           style="width:100%; padding:.5rem .7rem; border:1px solid #e2e8f0;
                                  border-radius:7px; font-size:13px; outline:none;
                                  transition:border-color .15s; box-sizing:border-box">
                    <button type="button" id="export-client-clear"
                            style="display:none; position:absolute; right:.5rem; top:50%;
                                   transform:translateY(-50%); background:none; border:none;
                                   color:#cbd5e1; cursor:pointer; font-size:14px; padding:2px; line-height:1">
                        <i class="ti ti-x"></i>
                    </button>
                    <div id="export-client-list"
                         style="display:none; position:absolute; top:calc(100% + 4px); left:0; right:0;
                                background:#fff; border:1px solid #e2e8f0; border-radius:9px;
                                max-height:200px; overflow-y:auto; z-index:60;
                                box-shadow:0 10px 25px -5px rgba(0,0,0,.1)">
                    </div>
                </div>
                <button id="export-client-btn"
                        onclick="exportSelectedClient()"
                        disabled
                        style="padding:.5rem 1rem; background:#6366f1; color:#fff; border:none;
                               border-radius:7px; font-size:13px; font-weight:600; cursor:pointer;
                               transition:background .15s; white-space:nowrap; opacity:.45">
                    <i class="ti ti-arrow-right" style="font-size:14px"></i> Exportar
                </button>
            </div>
        </div>

        <button class="btn-cancel-export" onclick="closeExportModal()">
            Cancelar
        </button>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    window.clientsData = @json($allClientsForExport->map(fn($c) => ['id' => $c->id_client, 'name' => $c->name_client]));
    window.exportExcelUrl = '{{ route("admin.clients.export.excel") }}';
    window.exportPdfUrl = '{{ route("admin.clients.export.pdf") }}';
    window.deleteUrl = '{{ route("admin.clients.destroy", ["client" => "__ID__"]) }}';
    window.restoreUrl = '{{ route("admin.clients.restore", ["client" => "__ID__"]) }}';
    window.forceDeleteUrl = '{{ route("admin.clients.force-destroy", ["client" => "__ID__"]) }}';
    window.csrfToken = '{{ csrf_token() }}';
</script>

<script>
let exportType = 'excel';
let exportClientId = null;
const clientsData = window.clientsData || [];

// ============================================================
// DROPDOWN CLIENTES
// ============================================================
function toggleDropdown(id, button) {
    const dropdown = document.getElementById('dropdown-client-' + id);
    if (!dropdown) return;

    const isOpen = dropdown.classList.contains('show');

    document.querySelectorAll('.dropdown-menu').forEach(el => {
        el.classList.remove('show');
    });

    if (!isOpen) {
        const rect = button.getBoundingClientRect();

        dropdown.style.position = 'fixed';
        dropdown.style.left = 'auto';
        dropdown.style.right = (window.innerWidth - rect.right) + 'px';
        dropdown.style.top = (rect.bottom + 6) + 'px';
        dropdown.style.bottom = 'auto';

        const dropdownHeight = 220;
        if (rect.bottom + dropdownHeight + 20 > window.innerHeight) {
            dropdown.style.top = 'auto';
            dropdown.style.bottom = (window.innerHeight - rect.top + 6) + 'px';
        }

        const dropdownWidth = 200;
        if (rect.right - dropdownWidth < 0) {
            dropdown.style.right = 'auto';
            dropdown.style.left = (rect.left) + 'px';
        }

        dropdown.classList.add('show');
    }
}

function closeAllDropdowns() {
    document.querySelectorAll('.dropdown-menu').forEach(el => {
        el.classList.remove('show');
    });
}

document.addEventListener('click', function(e) {
    const target = e.target;
    const isDropdownAction = target.closest('.dropdown-actions');
    const isDropdownMenu = target.closest('.dropdown-menu');

    if (!isDropdownAction && !isDropdownMenu) {
        closeAllDropdowns();
    }
});

document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeAllDropdowns();
        document.querySelectorAll('.custom-modal-overlay').forEach(overlay => {
            if (overlay.style.display === 'flex') {
                overlay.style.display = 'none';
                document.body.style.overflow = '';
            }
        });
        closeExportModal();
    }
});

let scrollTimeout;
document.addEventListener('scroll', function() {
    clearTimeout(scrollTimeout);
    scrollTimeout = setTimeout(() => {
        closeAllDropdowns();
    }, 100);
}, { passive: true });

let resizeTimeout;
window.addEventListener('resize', function() {
    clearTimeout(resizeTimeout);
    resizeTimeout = setTimeout(() => {
        closeAllDropdowns();
    }, 150);
});

// ============================================================
// ELIMINAR CLIENTE
// ============================================================
function confirmDeleteClient(id, name) {
    Swal.fire({
        title: '¿Eliminar cliente?',
        html: `Estás a punto de eliminar <strong>${name}</strong>.<br>Esta acción no se puede deshacer.`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#ef4444',
        cancelButtonColor: '#64748b',
        confirmButtonText: 'Sí, eliminar',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = window.deleteUrl.replace('__ID__', id);

            const csrf = document.createElement('input');
            csrf.type = 'hidden';
            csrf.name = '_token';
            csrf.value = window.csrfToken;

            const method = document.createElement('input');
            method.type = 'hidden';
            method.name = '_method';
            method.value = 'DELETE';

            form.appendChild(csrf);
            form.appendChild(method);
            document.body.appendChild(form);
            form.submit();
        }
    });
}

// ============================================================
// RESTAURAR CLIENTE
// ============================================================
function restoreClient(id) {
    Swal.fire({
        title: '¿Restaurar cliente?',
        text: 'El cliente será reactivado en el sistema.',
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#16a34a',
        cancelButtonColor: '#64748b',
        confirmButtonText: 'Sí, restaurar',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = window.restoreUrl.replace('__ID__', id);

            const csrf = document.createElement('input');
            csrf.type = 'hidden';
            csrf.name = '_token';
            csrf.value = window.csrfToken;

            form.appendChild(csrf);
            document.body.appendChild(form);
            form.submit();
        }
    });
}

// ============================================================
// ELIMINAR PERMANENTEMENTE
// ============================================================
function confirmForceDelete(id, name) {
    Swal.fire({
        title: '¿Eliminar permanentemente?',
        html: `Estás a punto de eliminar <strong>${name}</strong> de forma permanente.<br>
               <span style="color: #dc2626; font-weight: 600;">Esta acción NO se puede deshacer.</span>`,
        icon: 'error',
        showCancelButton: true,
        confirmButtonColor: '#dc2626',
        cancelButtonColor: '#64748b',
        confirmButtonText: 'Sí, eliminar permanentemente',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = window.forceDeleteUrl.replace('__ID__', id);

            const csrf = document.createElement('input');
            csrf.type = 'hidden';
            csrf.name = '_token';
            csrf.value = window.csrfToken;

            const method = document.createElement('input');
            method.type = 'hidden';
            method.name = '_method';
            method.value = 'DELETE';

            form.appendChild(csrf);
            form.appendChild(method);
            document.body.appendChild(form);
            form.submit();
        }
    });
}

// ============================================================
// FILTROS Y PAGINACIÓN
// ============================================================
function clearFilters() {
    window.location.href = window.location.pathname;
}

// ============================================================
// SELECCIÓN MÚLTIPLE
// ============================================================
function toggleAll(checked) {
    document.querySelectorAll('.row-check').forEach(cb => {
        if (cb.closest('tr').style.display !== 'none') {
            cb.checked = checked;
            cb.closest('tr').classList.toggle('selected', checked);
        }
    });
    updateBulk();
}

function selectAll(val) {
    toggleAll(val);
}

function updateBulk() {
    const checked = document.querySelectorAll('.row-check:checked');
    const trashedChecked = document.querySelectorAll('.row-check:checked[data-trashed="true"]');
    const bar = document.getElementById('bulk-bar');
    document.getElementById('bulk-count').textContent = checked.length;

    if (checked.length > 0) {
        bar.classList.add('visible');
    } else {
        bar.classList.remove('visible');
        document.getElementById('check-all').checked = false;
    }

    // Mostrar/ocultar botones de restaurar y eliminar definitivo
    const restoreBtn = document.querySelector('.bulk-restore-btn');
    const forceDeleteBtn = document.querySelector('.bulk-force-delete-btn');

    if (restoreBtn) {
        restoreBtn.style.display = trashedChecked.length > 0 ? 'inline-flex' : 'none';
    }
    if (forceDeleteBtn) {
        forceDeleteBtn.style.display = trashedChecked.length > 0 ? 'inline-flex' : 'none';
    }

    document.querySelectorAll('.row-check').forEach(cb => {
        cb.closest('tr').classList.toggle('selected', cb.checked);
    });

    const all = document.querySelectorAll('.row-check');
    const allVisible = [...all].filter(cb => cb.closest('tr').style.display !== 'none');
    const ca = document.getElementById('check-all');
    if (checked.length === 0) {
        ca.checked = false;
        ca.indeterminate = false;
    } else if (checked.length === allVisible.length) {
        ca.checked = true;
        ca.indeterminate = false;
    } else {
        ca.indeterminate = true;
    }
}

function bulkDelete() {
    const checked = document.querySelectorAll('.row-check:checked:not([data-trashed="true"])');
    if (checked.length === 0) {
        Swal.fire('Aviso', 'No hay clientes activos seleccionados para eliminar.', 'info');
        return;
    }

    Swal.fire({
        title: `¿Eliminar ${checked.length} cliente(s)?`,
        html: `Estás a punto de eliminar <strong>${checked.length}</strong> cliente(s) seleccionados.<br>Esta acción no se puede deshacer.`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#ef4444',
        cancelButtonColor: '#64748b',
        confirmButtonText: 'Sí, eliminar',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            const container = document.getElementById('bulk-ids-container');
            container.innerHTML = '';
            checked.forEach(cb => {
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = 'ids[]';
                input.value = cb.value;
                container.appendChild(input);
            });
            document.getElementById('bulk-delete-form').submit();
        }
    });
}

function restoreSelected() {
    const checked = document.querySelectorAll('.row-check:checked[data-trashed="true"]');
    if (checked.length === 0) {
        Swal.fire('Aviso', 'No hay clientes eliminados seleccionados.', 'info');
        return;
    }

    Swal.fire({
        title: `¿Restaurar ${checked.length} cliente(s)?`,
        html: `Se restaurarán <strong>${checked.length}</strong> cliente(s) eliminados.`,
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#16a34a',
        cancelButtonColor: '#64748b',
        confirmButtonText: 'Sí, restaurar',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            const container = document.getElementById('bulk-restore-ids-container');
            container.innerHTML = '';
            checked.forEach(cb => {
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = 'ids[]';
                input.value = cb.value;
                container.appendChild(input);
            });
            document.getElementById('bulk-restore-form').submit();
        }
    });
}

function forceDeleteSelected() {
    const checked = document.querySelectorAll('.row-check:checked[data-trashed="true"]');
    if (checked.length === 0) {
        Swal.fire('Aviso', 'No hay clientes eliminados seleccionados.', 'info');
        return;
    }

    Swal.fire({
        title: `¿Eliminar permanentemente ${checked.length} cliente(s)?`,
        html: `Se eliminarán permanentemente <strong>${checked.length}</strong> cliente(s).<br>
               <span style="color: #dc2626; font-weight: 600;">Esta acción NO se puede deshacer.</span>`,
        icon: 'error',
        showCancelButton: true,
        confirmButtonColor: '#dc2626',
        cancelButtonColor: '#64748b',
        confirmButtonText: 'Sí, eliminar permanentemente',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            const container = document.getElementById('bulk-force-ids-container');
            container.innerHTML = '';
            checked.forEach(cb => {
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = 'ids[]';
                input.value = cb.value;
                container.appendChild(input);
            });
            document.getElementById('bulk-force-delete-form').submit();
        }
    });
}

// ============================================================
// MODAL EXPORTAR
// ============================================================
function exportSelectedClient() {
    if (!exportClientId) return;
    const url = exportType === 'excel'
        ? window.exportExcelUrl + '?client_id=' + exportClientId
        : window.exportPdfUrl + '?client_id=' + exportClientId;
    window.location.href = url;
    closeExportModal();
}

function openExportModal(type) {
    exportType = type;
    document.getElementById('modal-export').classList.add('show');

    const label = document.getElementById('export-type-label');
    const icon = document.getElementById('export-modal-icon');

    if (type === 'excel') {
        label.textContent = 'Excel';
        label.style.color = '#16a34a';
        icon.style.color = '#16a34a';
    } else {
        label.textContent = 'PDF';
        label.style.color = '#ef4444';
        icon.style.color = '#ef4444';
    }

    const input = document.getElementById('export-client-search');
    const clear = document.getElementById('export-client-clear');
    const list = document.getElementById('export-client-list');
    const btn = document.getElementById('export-client-btn');
    input.value = '';
    input.style.borderColor = '#e2e8f0';
    clear.style.display = 'none';
    list.style.display = 'none';
    btn.disabled = true;
    btn.style.opacity = '.45';
    btn.style.cursor = 'default';
    exportClientId = null;
}

function closeExportModal() {
    document.getElementById('modal-export').classList.remove('show');
}

function exportAll() {
    const url = exportType === 'excel'
        ? window.exportExcelUrl
        : window.exportPdfUrl;
    window.location.href = url;
    closeExportModal();
}

// ============================================================
// COMBO DE EXPORTACIÓN
// ============================================================
(function initExportCombo() {
    const input = document.getElementById('export-client-search');
    const list = document.getElementById('export-client-list');
    const clear = document.getElementById('export-client-clear');
    const btn = document.getElementById('export-client-btn');
    let activeIdx = -1;
    let filtered = [];

    function normalizeStr(str) {
        return (str || '').toLowerCase().normalize('NFD').replace(/[\u0300-\u036f]/g, '');
    }

    function renderList(term) {
        const q = normalizeStr(term);
        filtered = q
            ? clientsData.filter(c => normalizeStr(c.name).includes(q))
            : clientsData.slice(0, 50);

        if (filtered.length === 0) {
            list.innerHTML = '<div style="padding:.6rem .8rem;font-size:12.5px;color:#94a3b8">Sin resultados</div>';
        } else {
            list.innerHTML = filtered.map((c, i) =>
                `<div data-idx="${i}"
                      style="padding:.55rem .8rem;font-size:13px;color:#0f172a;cursor:pointer;
                             transition:background .1s">
                    <span style="color:#94a3b8;font-size:11px;margin-right:6px">#${c.id}</span>${c.name}
                </div>`
            ).join('');
        }
        activeIdx = -1;
        list.style.display = 'block';
    }

    function selectClient(c) {
        input.value = c.name;
        exportClientId = c.id;
        clear.style.display = 'block';
        btn.disabled = false;
        btn.style.opacity = '1';
        btn.style.cursor = 'pointer';
        list.style.display = 'none';
        input.style.borderColor = '#6366f1';
    }

    function clearSelection() {
        input.value = '';
        exportClientId = null;
        clear.style.display = 'none';
        btn.disabled = true;
        btn.style.opacity = '.45';
        btn.style.cursor = 'default';
        list.style.display = 'none';
        input.style.borderColor = '#e2e8f0';
        input.focus();
    }

    function updateActive() {
        list.querySelectorAll('[data-idx]').forEach(el => {
            el.style.background = '';
            el.style.color = '#0f172a';
        });
        const el = list.querySelector(`[data-idx="${activeIdx}"]`);
        if (el) {
            el.style.background = '#eef2ff';
            el.style.color = '#4338ca';
            el.scrollIntoView({ block: 'nearest' });
        }
    }

    input.addEventListener('focus', () => renderList(input.value));

    input.addEventListener('input', () => {
        exportClientId = null;
        btn.disabled = true;
        btn.style.opacity = '.45';
        btn.style.cursor = 'default';
        input.style.borderColor = '#e2e8f0';
        clear.style.display = input.value ? 'block' : 'none';
        renderList(input.value);
    });

    input.addEventListener('keydown', e => {
        if (list.style.display === 'none') return;
        if (e.key === 'ArrowDown') {
            e.preventDefault();
            activeIdx = Math.min(activeIdx + 1, filtered.length - 1);
            updateActive();
        } else if (e.key === 'ArrowUp') {
            e.preventDefault();
            activeIdx = Math.max(activeIdx - 1, 0);
            updateActive();
        } else if (e.key === 'Enter') {
            e.preventDefault();
            if (activeIdx >= 0 && filtered[activeIdx]) selectClient(filtered[activeIdx]);
        } else if (e.key === 'Escape') {
            list.style.display = 'none';
        }
    });

    list.addEventListener('mousedown', e => {
        e.preventDefault();
        const item = e.target.closest('[data-idx]');
        if (!item) return;
        selectClient(filtered[parseInt(item.dataset.idx)]);
    });

    list.addEventListener('mouseover', e => {
        const item = e.target.closest('[data-idx]');
        if (!item) return;
        list.querySelectorAll('[data-idx]').forEach(el => {
            el.style.background = '';
            el.style.color = '#0f172a';
        });
        item.style.background = '#eef2ff';
        item.style.color = '#4338ca';
        activeIdx = parseInt(item.dataset.idx);
    });

    clear.addEventListener('click', clearSelection);

    document.addEventListener('click', e => {
        if (!input.contains(e.target) && !list.contains(e.target)) {
            list.style.display = 'none';
        }
    });
})();

// ============================================================
// INICIALIZACIÓN
// ============================================================
document.addEventListener('DOMContentLoaded', function() {
    // --- BÚSQUEDA CON ENTER (igual que en proveedores) ---
    const searchInput = document.getElementById('f-search');
    if (searchInput) {
        searchInput.addEventListener('keydown', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                document.getElementById('filter-form').submit();
            }
        });
    }

    // Cambios en selects → submit automático (igual que en proveedores)
    ['f-city', 'f-date', 'f-sort'].forEach(id => {
        const el = document.getElementById(id);
        if (el) {
            el.addEventListener('change', function() {
                document.getElementById('filter-form').submit();
            });
        }
    });

    // Botones de exportación
    document.getElementById('btn-export-pdf').addEventListener('click', function(e) {
        e.preventDefault();
        openExportModal('pdf');
    });

    document.getElementById('btn-export-excel').addEventListener('click', function(e) {
        e.preventDefault();
        openExportModal('excel');
    });

    // Cerrar modal de export al hacer clic fuera
    document.getElementById('modal-export').addEventListener('click', function(e) {
        if (e.target === this) closeExportModal();
    });

    // Modales "Ver detalles"
    document.querySelectorAll('.btn-open-modal').forEach(btn => {
        btn.addEventListener('click', function() {
            const modalId = this.getAttribute('data-target');
            const modal = document.getElementById(modalId);
            if (modal) {
                modal.style.display = 'flex';
                document.body.style.overflow = 'hidden';
            }
        });
    });

    document.querySelectorAll('.btn-close-modal').forEach(btn => {
        btn.addEventListener('click', function() {
            const modalId = this.getAttribute('data-close');
            const modal = document.getElementById(modalId);
            if (modal) {
                modal.style.display = 'none';
                document.body.style.overflow = '';
            }
        });
    });

    document.querySelectorAll('.custom-modal-overlay').forEach(overlay => {
        overlay.addEventListener('click', function(e) {
            if (e.target === this) {
                this.style.display = 'none';
                document.body.style.overflow = '';
            }
        });
    });

    // Tabs en modales
    document.querySelectorAll('.custom-tabs .tab-trigger').forEach(tabBtn => {
        tabBtn.addEventListener('click', function() {
            const parentTabs = this.closest('.custom-tabs');
            const clientId = parentTabs.getAttribute('data-client');
            const targetPanelName = this.getAttribute('data-tab');

            parentTabs.querySelectorAll('.tab-trigger').forEach(b => {
                b.style.background = '#f1f5f9';
                b.style.color = '#475569';
            });

            this.style.background = '#4338ca';
            this.style.color = '#fff';

            const contentsContainer = document.querySelector(`.custom-tab-contents[data-client="${clientId}"]`);
            contentsContainer.querySelectorAll('.tab-content-panel').forEach(panel => {
                panel.style.display = 'none';
            });

            const activePanel = contentsContainer.querySelector(`.panel-${targetPanelName}`);
            if (activePanel) {
                activePanel.style.display = 'block';
            }
        });
    });
});
</script>
@endpush
@endsection
