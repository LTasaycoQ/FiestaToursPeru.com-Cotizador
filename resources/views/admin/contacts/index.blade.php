@extends('layouts.app')
@section('title', 'Contactos')

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

    .view-tabs {
        display: flex;
        gap: 6px;
        background: #f1f5f9;
        padding: 4px;
        border-radius: 10px;
    }
    .view-tab {
        padding: 8px 20px;
        border: none;
        border-radius: 8px;
        font-size: 13px;
        font-weight: 600;
        cursor: pointer;
        transition: all .15s;
        background: transparent;
        color: #64748b;
        text-decoration: none;
    }
    .view-tab:hover {
        background: #e2e8f0;
        color: #0f172a;
    }
    .view-tab.active {
        background: #fff;
        color: #0f172a;
        box-shadow: 0 2px 8px rgba(0,0,0,.08);
    }
    .view-tab .badge-count {
        display: inline-block;
        padding: 1px 8px;
        border-radius: 12px;
        font-size: 10px;
        background: #e2e8f0;
        color: #64748b;
        margin-left: 6px;
    }
    .view-tab.active .badge-count {
        background: #eef2ff;
        color: #6366f1;
    }

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

    input[type="checkbox"] {
        width: 15px;
        height: 15px;
        accent-color: #e63232;
        cursor: pointer;
    }
    tr.selected td { background: #fef2f2 !important; }

    tr.trashed td { background: #fef2f2 !important; }
    tr.trashed td:not(:last-child) { color: #94a3b8; }
    tr.trashed .avatar-sm { filter: grayscale(35%); opacity: 0.8; }
    tr.trashed td .ti-trash { color: #dc2626; }

    .results-count {
        font-size: 12px;
        color: #94a3b8;
        margin-left: auto;
    }

    .avatar-sm {
        width: 34px;
        height: 34px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 12px;
        font-weight: 700;
        flex-shrink: 0;
    }

    .badge {
        display: inline-block;
        padding: .2rem .65rem;
        border-radius: 6px;
        font-size: 11px;
        font-weight: 600;
    }

    .badge-cliente {
        background: #dbeafe;
        color: #1d4ed8;
        border: 1px solid #bfdbfe;
    }
    .badge-proveedor {
        background: #fef3c7;
        color: #92400e;
        border: 1px solid #fde68a;
    }
    .badge-principal {
        background: #dcfce7;
        color: #15803d;
        border: 1px solid #bbf7d0;
    }
    .badge-secundario {
        background: #f1f5f9;
        color: #64748b;
        border: 1px solid #e2e8f0;
    }

    .btn-sm {
        padding: .3rem .7rem;
        font-size: 12px;
        border-radius: 6px;
        border: none;
        cursor: pointer;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 4px;
    }
    .btn-secondary {
        background: #f1f5f9;
        color: #475569;
    }
    .btn-secondary:hover { background: #e2e8f0; }
    .btn-danger {
        background: #fef2f2;
        color: #ef4444;
    }
    .btn-danger:hover { background: #fee2e2; }
    .btn-success {
        background: #dcfce7;
        color: #16a34a;
    }
    .btn-success:hover { background: #bbf7d0; }

    .btn-empty {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 6px;
        padding: 0.5rem 1.2rem;
        font-size: 13px;
        font-weight: 600;
        border-radius: 8px;
        border: none;
        background: #6366f1;
        color: #fff;
        cursor: pointer;
        text-decoration: none;
        transition: background 0.15s ease;
        line-height: 1.2;
    }
    .btn-empty:hover {
        background: #4f46e5;
        color: #fff;
    }
    .btn-empty i {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 14px;
        line-height: 1;
    }

    .table-footer {
        padding: .8rem 1rem;
        background: #f8fafc;
        border-top: 1px solid #e2e8f0;
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: .5rem;
    }

    #footer-count {
        font-size: 13px;
        color: #64748b;
    }
    #footer-count span {
        font-weight: 600;
        color: #0f172a;
    }

    .pagination-controls {
        display: flex;
        align-items: center;
        gap: .4rem;
        flex-wrap: wrap;
    }
    .pagination-controls a,
    .pagination-controls span.page-current {
        padding: .35rem .65rem;
        border: 1px solid #e2e8f0;
        border-radius: 7px;
        font-size: 12px;
        color: #374151;
        text-decoration: none;
        background: #fff;
        min-width: 32px;
        text-align: center;
        transition: border-color .15s;
    }
    .pagination-controls a:hover { border-color: #6366f1; }
    .pagination-controls span.page-current {
        border-color: #6366f1;
        color: #fff;
        background: #6366f1;
        font-weight: 700;
    }
    .pagination-controls .disabled {
        color: #cbd5e1;
        cursor: default;
        border-color: #e2e8f0;
    }
    .pagination-controls .ellipsis {
        font-size: 12px;
        color: #94a3b8;
        padding: 0 .2rem;
    }

    #footer-filter {
        color: #6366f1;
        font-size: 12px;
        font-weight: 500;
    }

    .empty-state {
        text-align: center;
        padding: 3rem 2rem;
        background: #fff;
        border-radius: 14px;
        border: 1px solid #e2e8f0;
    }
    .empty-state i {
        font-size: 40px;
        color: #cbd5e1;
        display: block;
        margin-bottom: 0.8rem;
    }
    .empty-state h3 {
        font-size: 15px;
        font-weight: 600;
        color: #0f172a;
        margin-bottom: 0.3rem;
    }
    .empty-state p {
        font-size: 13px;
        color: #94a3b8;
        margin-bottom: 1.2rem;
    }
    .empty-state .btn-empty {
        margin-top: 0.2rem;
    }

    .table-wrap {
        overflow-x: auto;
        overflow-y: visible !important;
        -webkit-overflow-scrolling: touch;
    }

    #tabla-contactos {
        min-width: 980px;
        overflow: visible !important;
    }

    #tabla-contactos tbody tr {
        position: relative;
    }

    #tabla-contactos td {
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
        opacity: 1;
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

    @keyframes modalFadeInContact {
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
        max-width: 600px;
        border-radius: 14px;
        box-shadow: 0 25px 50px -12px rgba(0,0,0,0.25);
        display: flex;
        flex-direction: column;
        max-height: 90vh;
        animation: modalFadeInContact 0.2s ease-out;
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

    @media (max-width: 640px) {
        #tabla-contactos { min-width: 820px; }
        .bulk-bar { flex-wrap: wrap; row-gap: .6rem; }
        .bulk-bar .bulk-delete-btn {
            margin-left: 0 !important;
            width: 100%;
            justify-content: center;
        }
        .custom-modal-content { border-radius: 12px; }
    }
</style>
@endpush

@section('content')

@php
    $hayFiltros = request()->anyFilled(['search','client','supplier','principal','date'])
        || (request('sort') && request('sort') !== 'newest');

    $viewType = $viewType ?? 'clientes';
    $isClientView = $viewType === 'clientes';
    $filter = $filter ?? 'active';
@endphp

<div class="page-header" style="display:flex;justify-content:space-between;align-items:flex-start;margin-bottom:1.2rem">
    <div>
        <div class="page-title">Contactos</div>
        <div class="page-sub">Gestiona todos los contactos registrados</div>
    </div>
    <div style="display:flex;gap:.6rem;align-items:center;flex-wrap:wrap">
        <a href="{{ route('admin.contacts.export.excel', ['view' => $viewType]) }}"
           style="display:inline-flex;align-items:center;gap:6px;padding:.5rem .9rem;
                  background:#fff;border:1px solid #e2e8f0;border-radius:8px;
                  font-size:13px;font-weight:600;color:#16a34a;text-decoration:none">
            <i class="ti ti-file-type-xls" style="font-size:16px"></i> Excel
        </a>
        <a href="{{ route('admin.contacts.create', ['type' => $viewType]) }}" class="btn btn-primary"
            style="text-decoration:none;display:inline-flex;align-items:center;gap:6px">
            <i class="ti ti-plus" style="font-size:15px"></i> Nuevo Contacto
        </a>
    </div>
</div>

{{-- TABS --}}
<div style="margin-bottom:1.2rem;">
    <div class="view-tabs">
        <a href="{{ route('admin.contacts.index', ['view' => 'clientes'] + request()->except(['view', 'page'])) }}"
           class="view-tab {{ $viewType === 'clientes' ? 'active' : '' }}">
            <i class="ti ti-users" style="font-size:14px"></i> Clientes
            <span class="badge-count">{{ $clients->count() }}</span>
        </a>
        <a href="{{ route('admin.contacts.index', ['view' => 'proveedores'] + request()->except(['view', 'page'])) }}"
           class="view-tab {{ $viewType === 'proveedores' ? 'active' : '' }}">
            <i class="ti ti-truck" style="font-size:14px"></i> Proveedores
            <span class="badge-count">{{ $suppliers->count() }}</span>
        </a>
    </div>
</div>

{{-- FILTROS DE ESTADO (ACTIVOS / ELIMINADOS / TODOS) --}}
<div class="filter-bar" style="margin-bottom: 1rem; background: #f8fafc; padding: 0.6rem 1.1rem;">
    <span style="font-size: 12px; font-weight: 600; color: #64748b; margin-right: 0.5rem;">
        <i class="ti ti-filter"></i> Estado:
    </span>
    <div style="display: flex; gap: 6px; flex-wrap: wrap; align-items:center;">
        {{-- Botón "Activos" - visible para todos --}}
        <a href="{{ route('admin.contacts.index', array_merge(request()->except(['filter', 'page']), ['filter' => 'active'])) }}"
           class="filter-status-btn {{ request('filter', 'active') == 'active' ? 'active' : '' }}"
           style="background: {{ request('filter', 'active') == 'active' ? '#16a34a' : '#f1f5f9' }};
                  color: {{ request('filter', 'active') == 'active' ? '#fff' : '#64748b' }};
                  border-color: {{ request('filter', 'active') == 'active' ? '#16a34a' : '#e2e8f0' }};">
            <i class="ti ti-user-check" style="font-size: 13px;"></i>
            Activos <span class="badge-count" style="background: {{ request('filter', 'active') == 'active' ? 'rgba(255,255,255,0.2)' : '#e2e8f0' }};">{{ $activeCount ?? 0 }}</span>
        </a>

        {{-- Botones "Eliminados" y "Todos" - solo para administradores --}}
        @if(auth()->check() && auth()->user()->isAdmin())
            <a href="{{ route('admin.contacts.index', array_merge(request()->except(['filter', 'page']), ['filter' => 'trashed'])) }}"
               class="filter-status-btn {{ request('filter') == 'trashed' ? 'active' : '' }}"
               style="background: {{ request('filter') == 'trashed' ? '#dc2626' : '#f1f5f9' }};
                      color: {{ request('filter') == 'trashed' ? '#fff' : '#64748b' }};
                      border-color: {{ request('filter') == 'trashed' ? '#dc2626' : '#e2e8f0' }};">
                <i class="ti ti-user-x" style="font-size: 13px;"></i>
                Eliminados <span class="badge-count" style="background: {{ request('filter') == 'trashed' ? 'rgba(255,255,255,0.2)' : '#e2e8f0' }};">{{ $trashedCount ?? 0 }}</span>
            </a>

            <a href="{{ route('admin.contacts.index', array_merge(request()->except(['filter', 'page']), ['filter' => 'all'])) }}"
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
    <form id="filter-form" method="GET" action="{{ route('admin.contacts.index') }}" style="display:flex; flex-wrap:wrap; gap:8px; align-items:center; width:100%;">
        <input type="hidden" name="view" value="{{ $viewType }}">

        <div style="position:relative;flex:1;min-width:200px">
            <i class="ti ti-search" style="position:absolute;left:.7rem;top:50%;transform:translateY(-50%);color:#94a3b8;font-size:15px"></i>
            <input type="text" id="f-search" name="search" class="filter-input"
                   value="{{ request('search') }}"
                   placeholder="Buscar por nombre, email, teléfono, cargo..."
                   style="width:100%;padding-left:2.2rem">
        </div>

        <div class="filter-sep"></div>

        @if($isClientView)
            <select id="f-client" name="client" class="filter-input" style="min-width:160px">
                <option value="">Todos los clientes</option>
                @foreach($clients as $client)
                    <option value="{{ $client->id_client }}" {{ request('client') == $client->id_client ? 'selected' : '' }}>
                        {{ $client->name_client }}
                    </option>
                @endforeach
            </select>
        @else
            <select id="f-supplier" name="supplier" class="filter-input" style="min-width:160px">
                <option value="">Todos los proveedores</option>
                @foreach($suppliers as $supplier)
                    <option value="{{ $supplier->id_supplier }}" {{ request('supplier') == $supplier->id_supplier ? 'selected' : '' }}>
                        {{ $supplier->supplier_name }}
                    </option>
                @endforeach
            </select>
        @endif

        <select id="f-principal" name="principal" class="filter-input" style="min-width:130px">
            <option value="">Todos</option>
            <option value="1" {{ request('principal') == '1' ? 'selected' : '' }}>Solo principales</option>
            <option value="0" {{ request('principal') == '0' ? 'selected' : '' }}>Solo secundarios</option>
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
            <option value="az"     {{ request('sort') == 'az'     ? 'selected' : '' }}>Nombre A → Z</option>
            <option value="za"     {{ request('sort') == 'za'     ? 'selected' : '' }}>Nombre Z → A</option>
        </select>

        <div class="filter-sep"></div>

        {{-- BOTÓN BUSCAR (igual que en proveedores) --}}
        <button type="submit" class="btn-search">
            <i class="ti ti-search" style="font-size:14px"></i> Buscar
        </button>

        {{-- BOTÓN LIMPIAR (igual que en proveedores) --}}
        <a href="{{ route('admin.contacts.index', ['view' => $viewType]) }}"
           style="padding:.5rem .9rem;background:none;border:1px solid #e2e8f0;
                  border-radius:8px;font-size:13px;color:#64748b;cursor:pointer;
                  display:inline-flex;align-items:center;gap:5px;white-space:nowrap;
                  text-decoration:none;transition:all .15s"
           onmouseover="this.style.background='#f1f5f9';this.style.borderColor='#94a3b8'"
           onmouseout="this.style.background='none';this.style.borderColor='#e2e8f0'">
            <i class="ti ti-filter-off" style="font-size:14px"></i> Limpiar
        </a>

        <span class="results-count">{{ $contacts->total() }} resultado(s)</span>
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
<form id="bulk-delete-form" action="{{ route('admin.contacts.bulk-destroy') }}" method="POST" style="display:none">
    @csrf
    <input type="hidden" name="_method" value="DELETE">
    <div id="bulk-ids-container"></div>
</form>

<form id="bulk-restore-form" action="{{ route('admin.contacts.bulk-restore') }}" method="POST" style="display:none">
    @csrf
    <div id="bulk-restore-ids-container"></div>
</form>

<form id="bulk-force-delete-form" action="{{ route('admin.contacts.bulk-force-destroy') }}" method="POST" style="display:none">
    @csrf
    <input type="hidden" name="_method" value="DELETE">
    <div id="bulk-force-ids-container"></div>
</form>

@if($contacts->isEmpty())
    @if($hayFiltros)
        <div class="empty-state">
            <i class="ti ti-search-off"></i>
            <h3>Sin resultados para tu búsqueda</h3>
            <p>Prueba con otros filtros</p>
        </div>
    @else
        <div class="empty-state">
            <i class="ti ti-address-book-off"></i>
            <h3>No hay contactos de {{ $viewType === 'clientes' ? 'clientes' : 'proveedores' }}</h3>
            <p>Comienza creando tu primer contacto</p>
            <a href="{{ route('admin.contacts.create', ['type' => $viewType]) }}" class="btn-empty">
                Crear primer contacto
            </a>
        </div>
    @endif
@else
    <div class="table-wrap" id="table-container">
        <table id="tabla-contactos">
            <thead>
                <tr>
                    <th style="width:40px">
                        <input type="checkbox" id="check-all" title="Seleccionar todos"
                            onchange="toggleAll(this.checked)">
                    </th>
                    <th>ID</th>
                    <th>Contacto</th>
                    <th>{{ $isClientView ? 'Cliente' : 'Proveedor' }}</th>
                    <th>Email</th>
                    <th>Teléfono</th>
                    <th>Principal</th>
                    <th>Registro</th>
                    <th style="text-align:center;width:110px">Acciones</th>
                </tr>
            </thead>
            <tbody id="tabla-body">
                @foreach($contacts as $c)
                    <tr class="contact-row {{ $c->trashed() ? 'trashed' : '' }}" data-id="{{ $c->id_contacts }}">
                        <td class="cb-wrap" style="padding:12px 8px;text-align:center;vertical-align:middle;">
                            <input type="checkbox" class="row-check"
                                value="{{ $c->id_contacts }}"
                                onchange="updateBulk()"
                                {{ $c->trashed() ? 'data-trashed="true"' : '' }}>
                        </td>
                        <td style="color:#94a3b8;font-size:12px">#{{ $c->id_contacts }}</td>
                        <td>
                            <div style="display:flex;align-items:center;gap:9px">
                                <div class="avatar-sm" style="background:#ede9fe;color:#6d28d9">
                                    {{ strtoupper(substr($c->name,0,1)) }}{{ strtoupper(substr($c->last_names ?? '',0,1)) }}
                                </div>
                                <div>
                                    <div style="font-weight:600;font-size:13px;color:#0f172a">
                                        {{ $c->name }} {{ $c->last_names }}
                                        @if($c->trashed())
                                            <span style="background:#dc2626;color:#fff;font-size:9px;font-weight:700;
                                                         padding:2px 8px;border-radius:10px;margin-left:6px;">
                                                <i class="ti ti-trash" style="font-size:10px;"></i> ELIMINADO
                                            </span>
                                        @endif
                                    </div>
                                    @if($c->qualification)
                                        <div style="font-size:11px;color:#94a3b8">{{ $c->qualification }}</div>
                                    @endif
                                    @if($c->trashed())
                                        <div style="font-size:10px;color:#dc2626;margin-top:2px;">
                                            <i class="ti ti-clock" style="font-size:10px;"></i>
                                            Eliminado: {{ $c->deleted_at->format('d/m/Y H:i') }}
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </td>
                        <td>
                            @if($c->client)
                                <span class="badge badge-cliente">
                                    {{ $c->client->name_client }}
                                    @if($c->client->trashed())
                                        <span style="font-size:8px;color:#dc2626;margin-left:2px;">(Eliminado)</span>
                                    @endif
                                </span>
                            @elseif($c->supplier)
                                <span class="badge badge-proveedor">
                                    {{ $c->supplier->supplier_name }}
                                    @if($c->supplier->trashed())
                                        <span style="font-size:8px;color:#dc2626;margin-left:2px;">(Eliminado)</span>
                                    @endif
                                </span>
                            @else
                                <span style="color:#cbd5e1;font-size:12px">—</span>
                            @endif
                        </td>
                        <td style="color:#64748b;font-size:12px">{{ $c->email ?? '—' }}</td>
                        <td style="color:#64748b;font-size:12px">{{ $c->first_phone ?? '—' }}</td>
                        <td>
                            @if($c->es_principal)
                                <span class="badge badge-principal">
                                    <i class="ti ti-star-filled" style="font-size:10px"></i> Principal
                                </span>
                            @else
                                <span class="badge badge-secundario">Secundario</span>
                            @endif
                        </td>
                        <td style="color:#94a3b8;font-size:12px">
                            {{ $c->created_at->format('d/m/Y') }}
                        </td>
                        <td>
                            <div class="dropdown-actions">
                                <button type="button"
                                        class="dropdown-toggle"
                                        onclick="toggleDropdown({{ $c->id_contacts }}, this)"
                                        aria-label="Acciones">
                                    <i class="ti ti-dots-vertical"></i>
                                </button>
                                <div id="dropdown-contact-{{ $c->id_contacts }}" class="dropdown-menu">
                                    @if(!$c->trashed())
                                        <button type="button"
                                                class="dropdown-item btn-open-modal"
                                                data-target="modal-contact-{{ $c->id_contacts }}">
                                            <i class="ti ti-eye icon blue"></i>
                                            Ver detalles
                                        </button>
                                        <a href="{{ route('admin.contacts.edit', $c->id_contacts) }}"
                                           class="dropdown-item">
                                            <i class="ti ti-edit icon purple"></i>
                                            Editar
                                        </a>
                                        <div class="dropdown-divider"></div>
                                        <button type="button"
                                                class="dropdown-item danger"
                                                onclick="confirmDeleteContact({{ $c->id_contacts }}, '{{ addslashes($c->name) }}')">
                                            <i class="ti ti-trash icon red"></i>
                                            Eliminar
                                        </button>
                                    @else
                                        <button type="button"
                                                class="dropdown-item btn-open-modal"
                                                data-target="modal-contact-{{ $c->id_contacts }}">
                                            <i class="ti ti-eye icon blue"></i>
                                            Ver detalles
                                        </button>
                                        <div class="dropdown-divider"></div>
                                        <button type="button"
                                                class="dropdown-item"
                                                onclick="restoreContact({{ $c->id_contacts }})">
                                            <i class="ti ti-rotate-clockwise icon green"></i>
                                            Restaurar
                                        </button>
                                        <button type="button"
                                                class="dropdown-item danger"
                                                onclick="confirmForceDeleteContact({{ $c->id_contacts }}, '{{ addslashes($c->name) }}')">
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
        <div class="table-footer">
            <span id="footer-count">
                Mostrando {{ $contacts->firstItem() }}–{{ $contacts->lastItem() }}
                de {{ $contacts->total() }} contacto(s)
            </span>
            <div class="pagination-controls" id="pagination-controls">
                @if($contacts->onFirstPage())
                    <span class="disabled"><i class="ti ti-chevron-left"></i></span>
                @else
                    <a href="{{ $contacts->previousPageUrl() }}"><i class="ti ti-chevron-left"></i></a>
                @endif

                @foreach($contacts->getUrlRange(1, $contacts->lastPage()) as $page => $url)
                    @if($page == $contacts->currentPage())
                        <span class="page-current">{{ $page }}</span>
                    @elseif($page == 1 || $page == $contacts->lastPage() || abs($page - $contacts->currentPage()) <= 1)
                        <a href="{{ $url }}">{{ $page }}</a>
                    @elseif(abs($page - $contacts->currentPage()) == 2)
                        <span class="ellipsis">…</span>
                    @endif
                @endforeach

                @if($contacts->hasMorePages())
                    <a href="{{ $contacts->nextPageUrl() }}"><i class="ti ti-chevron-right"></i></a>
                @else
                    <span class="disabled"><i class="ti ti-chevron-right"></i></span>
                @endif
            </div>
            <span id="footer-filter" style="color:#6366f1;font-size:12px;{{ $hayFiltros ? '' : 'display:none' }}">
                Mostrando resultados filtrados
            </span>
        </div>
    </div>
@endif

{{-- MODALES VER CONTACTO --}}
@foreach($contacts as $c)
<div id="modal-contact-{{ $c->id_contacts }}" class="custom-modal-overlay" style="display:none">
    <div class="custom-modal-content">
        <div style="background: #f8fafc; border-bottom:1px solid #e2e8f0; border-top-left-radius:14px; border-top-right-radius:14px; padding:1.2rem 1.5rem; display:flex; justify-content:space-between; align-items:center">
            <div style="display:flex; align-items:center; gap:12px">
                <div style="background: #ede9fe; color: #6d28d9; width:40px; height:40px; border-radius:10px; display:flex; align-items:center; justify-content:center">
                    <i class="ti ti-user" style="font-size:22px"></i>
                </div>
                <div>
                    <h5 style="font-weight:700; color:#0f172a; margin:0; font-size:16px">
                        {{ $c->name }} {{ $c->last_names }}
                        @if($c->trashed())
                            <span style="background:#dc2626;color:#fff;font-size:9px;font-weight:700;
                                         padding:2px 8px;border-radius:10px;margin-left:6px;">
                                <i class="ti ti-trash" style="font-size:10px;"></i> ELIMINADO
                            </span>
                        @endif
                    </h5>
                    <span style="font-size:12px; color:#64748b">ID Contacto: #{{ $c->id_contacts }}</span>
                </div>
            </div>
            <button type="button" class="btn-close-modal" data-close="modal-contact-{{ $c->id_contacts }}" style="background:none; border:none; color:#64748b; cursor:pointer; font-size:20px; line-height:1">
                <i class="ti ti-x"></i>
            </button>
        </div>

        <div style="padding:1.5rem; overflow-y:auto; flex:1;">
            <div style="display:grid; grid-template-columns: 1fr 1fr; gap:1rem">
                <div>
                    <label style="display:block; font-size:11px; font-weight:700; color:#94a3b8; text-transform:uppercase; margin-bottom:4px">Nombre</label>
                    <div style="font-size:14px; color:#1e293b; font-weight:500">{{ $c->name }}</div>
                </div>
                <div>
                    <label style="display:block; font-size:11px; font-weight:700; color:#94a3b8; text-transform:uppercase; margin-bottom:4px">Apellidos</label>
                    <div style="font-size:14px; color:#1e293b; font-weight:500">{{ $c->last_names ?? '—' }}</div>
                </div>
                <div>
                    <label style="display:block; font-size:11px; font-weight:700; color:#94a3b8; text-transform:uppercase; margin-bottom:4px">Cargo</label>
                    <div style="font-size:14px; color:#1e293b; font-weight:500">{{ $c->qualification ?? '—' }}</div>
                </div>
                <div>
                    <label style="display:block; font-size:11px; font-weight:700; color:#94a3b8; text-transform:uppercase; margin-bottom:4px">Email</label>
                    <div style="font-size:14px; color:#1e293b; font-weight:500">{{ $c->email ?? '—' }}</div>
                </div>
                <div>
                    <label style="display:block; font-size:11px; font-weight:700; color:#94a3b8; text-transform:uppercase; margin-bottom:4px">Teléfono 1</label>
                    <div style="font-size:14px; color:#1e293b; font-weight:500">{{ $c->first_phone ?? '—' }}</div>
                </div>
                <div>
                    <label style="display:block; font-size:11px; font-weight:700; color:#94a3b8; text-transform:uppercase; margin-bottom:4px">Teléfono 2</label>
                    <div style="font-size:14px; color:#1e293b; font-weight:500">{{ $c->second_phone ?? '—' }}</div>
                </div>
                <div>
                    <label style="display:block; font-size:11px; font-weight:700; color:#94a3b8; text-transform:uppercase; margin-bottom:4px">Estado</label>
                    <div style="font-size:14px; color:#1e293b; font-weight:500">
                        @if($c->es_principal)
                            <span class="badge badge-principal">Principal</span>
                        @else
                            <span class="badge badge-secundario">Secundario</span>
                        @endif
                    </div>
                </div>
                <div>
                    <label style="display:block; font-size:11px; font-weight:700; color:#94a3b8; text-transform:uppercase; margin-bottom:4px">Asociado a</label>
                    <div style="font-size:14px; color:#1e293b; font-weight:500">
                        @if($c->client)
                            <span class="badge badge-cliente">{{ $c->client->name_client }}</span>
                        @elseif($c->supplier)
                            <span class="badge badge-proveedor">{{ $c->supplier->supplier_name }}</span>
                        @else
                            —
                        @endif
                    </div>
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

        <div style="background:#f8fafc; border-top:1px solid #e2e8f0; border-bottom-left-radius:14px; border-bottom-right-radius:14px; padding:0.8rem 1.5rem; display:flex; justify-content:flex-end; gap:8px;">
            @if($c->trashed())
                <button type="button" onclick="restoreContact({{ $c->id_contacts }})"
                        style="font-size:13px; background:#16a34a; color:#fff; border:none; padding:8px 16px; border-radius:6px; cursor:pointer; font-weight:500">
                    <i class="ti ti-rotate-clockwise"></i> Restaurar
                </button>
                <button type="button" onclick="confirmForceDeleteContact({{ $c->id_contacts }}, '{{ addslashes($c->name) }}')"
                        style="font-size:13px; background:#dc2626; color:#fff; border:none; padding:8px 16px; border-radius:6px; cursor:pointer; font-weight:500">
                    <i class="ti ti-trash-off"></i> Eliminar definitivo
                </button>
            @else
                <button type="button" onclick="window.location.href='{{ route('admin.contacts.edit', $c->id_contacts) }}'"
                        style="font-size:13px; background:#8b5cf6; color:#fff; border:none; padding:8px 16px; border-radius:6px; cursor:pointer; font-weight:500">
                    <i class="ti ti-edit"></i> Editar
                </button>
            @endif
            <button type="button" class="btn-close-modal" data-close="modal-contact-{{ $c->id_contacts }}"
                    style="font-size:13px; background:#64748b; color:#fff; border:none; padding:8px 16px; border-radius:6px; cursor:pointer; font-weight:500">
                Cerrar
            </button>
        </div>
    </div>
</div>
@endforeach

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    // ============================================================
    // VARIABLES GLOBALES
    // ============================================================
    window.restoreUrl = '{{ route("admin.contacts.restore", ["contact" => "__ID__"]) }}';
    window.forceDeleteUrl = '{{ route("admin.contacts.force-destroy", ["contact" => "__ID__"]) }}';
    window.deleteUrl = '{{ route("admin.contacts.destroy", ["contact" => "__ID__"]) }}';
    window.csrfToken = '{{ csrf_token() }}';

    // ============================================================
    // DROPDOWN
    // ============================================================
    function toggleDropdown(id, button) {
        const dropdown = document.getElementById('dropdown-contact-' + id);
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
        }
    });

    // ============================================================
    // ELIMINAR CONTACTO CON SWEETALERT
    // ============================================================
    function confirmDeleteContact(id, name) {
        Swal.fire({
            title: '¿Eliminar contacto?',
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
    // ELIMINAR PERMANENTEMENTE CONTACTO CON SWEETALERT
    // ============================================================
    function confirmForceDeleteContact(id, name) {
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
    // RESTAURAR CONTACTO CON SWEETALERT
    // ============================================================
    function restoreContact(id) {
        Swal.fire({
            title: '¿Restaurar contacto?',
            text: 'El contacto será reactivado en el sistema.',
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

    // ============================================================
    // ELIMINACIÓN MASIVA CON FETCH
    // ============================================================
    function bulkDelete() {
        const checked = document.querySelectorAll('.row-check:checked:not([data-trashed="true"])');
        if (checked.length === 0) {
            Swal.fire('Aviso', 'No hay contactos activos seleccionados para eliminar.', 'info');
            return;
        }

        Swal.fire({
            title: `¿Eliminar ${checked.length} contacto(s)?`,
            html: `Estás a punto de eliminar <strong>${checked.length}</strong> contacto(s) seleccionados.<br>Esta acción no se puede deshacer.`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ef4444',
            cancelButtonColor: '#64748b',
            confirmButtonText: 'Sí, eliminar',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                const ids = [];
                checked.forEach(cb => ids.push(cb.value));

                const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || window.csrfToken;

                const formData = new FormData();
                formData.append('_method', 'DELETE');
                ids.forEach(id => formData.append('ids[]', id));

                fetch('/contactos/bulk', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': token,
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: formData
                })
                .then(response => {
                    if (response.redirected) {
                        window.location.href = response.url;
                        return;
                    }
                    return response.json();
                })
                .then(data => {
                    if (data && data.success) {
                        window.location.reload();
                    } else if (data && data.message) {
                        Swal.fire('Info', data.message, 'info');
                    } else {
                        window.location.reload();
                    }
                })
                .catch(() => {
                    window.location.reload();
                });
            }
        });
    }

    // ============================================================
    // RESTAURAR MASIVA CON FETCH
    // ============================================================
    function restoreSelected() {
        const checked = document.querySelectorAll('.row-check:checked[data-trashed="true"]');
        if (checked.length === 0) {
            Swal.fire('Aviso', 'No hay contactos eliminados seleccionados.', 'info');
            return;
        }

        Swal.fire({
            title: `¿Restaurar ${checked.length} contacto(s)?`,
            html: `Se restaurarán <strong>${checked.length}</strong> contacto(s) eliminados.`,
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#16a34a',
            cancelButtonColor: '#64748b',
            confirmButtonText: 'Sí, restaurar',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                const ids = [];
                checked.forEach(cb => ids.push(cb.value));

                const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || window.csrfToken;

                const formData = new FormData();
                ids.forEach(id => formData.append('ids[]', id));

                fetch('/contactos/bulk/restore', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': token,
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: formData
                })
                .then(response => {
                    if (response.redirected) {
                        window.location.href = response.url;
                        return;
                    }
                    return response.json();
                })
                .then(data => {
                    if (data && data.success) {
                        window.location.reload();
                    } else if (data && data.message) {
                        Swal.fire('Info', data.message, 'info');
                    } else {
                        window.location.reload();
                    }
                })
                .catch(() => {
                    window.location.reload();
                });
            }
        });
    }

    // ============================================================
    // ELIMINAR PERMANENTEMENTE MASIVA CON FETCH
    // ============================================================
    function forceDeleteSelected() {
        const checked = document.querySelectorAll('.row-check:checked[data-trashed="true"]');
        if (checked.length === 0) {
            Swal.fire('Aviso', 'No hay contactos eliminados seleccionados.', 'info');
            return;
        }

        Swal.fire({
            title: `¿Eliminar permanentemente ${checked.length} contacto(s)?`,
            html: `Se eliminarán permanentemente <strong>${checked.length}</strong> contacto(s).<br>
                   <span style="color: #dc2626; font-weight: 600;">Esta acción NO se puede deshacer.</span>`,
            icon: 'error',
            showCancelButton: true,
            confirmButtonColor: '#dc2626',
            cancelButtonColor: '#64748b',
            confirmButtonText: 'Sí, eliminar permanentemente',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                const ids = [];
                checked.forEach(cb => ids.push(cb.value));

                const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || window.csrfToken;

                const formData = new FormData();
                formData.append('_method', 'DELETE');
                ids.forEach(id => formData.append('ids[]', id));

                fetch('/contactos/bulk/force', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': token,
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: formData
                })
                .then(response => {
                    if (response.redirected) {
                        window.location.href = response.url;
                        return;
                    }
                    return response.json();
                })
                .then(data => {
                    if (data && data.success) {
                        window.location.reload();
                    } else if (data && data.message) {
                        Swal.fire('Info', data.message, 'info');
                    } else {
                        window.location.reload();
                    }
                })
                .catch(() => {
                    window.location.reload();
                });
            }
        });
    }

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
        ['f-client', 'f-supplier', 'f-principal', 'f-date', 'f-sort'].forEach(id => {
            const el = document.getElementById(id);
            if (el) {
                el.addEventListener('change', function() {
                    document.getElementById('filter-form').submit();
                });
            }
        });

        // MODALES
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
    });
</script>
@endpush
@endsection
