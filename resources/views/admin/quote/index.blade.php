@extends('layouts.app')

@section('title', 'Cotizaciones')
@section('content')

@push('styles')
<style>
/* ============================================================
   ESTILOS GENERALES (sin cambios)
   ============================================================ */
.card-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 1rem;
    background: #fff;
    padding: 1.2rem 1.5rem;
    border-bottom: 1px solid #e2e8f0;
    border-radius: 12px 12px 0 0;
}

.card-title {
    font-size: 22px;
    display: flex;
    gap: 12px;
    align-items: center;
    color: #0f172a;
    font-weight: 700;
    margin: 0;
}

.card-title i {
    color: #6366f1;
}

.u-av {
    width: 32px;
    height: 32px;
    border-radius: 50%;
    background: linear-gradient(135deg, #6366f1, #8b5cf6);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 12px;
    border: 2px solid #e2e8f0;
    color: #fff;
    font-weight: 700;
    flex-shrink: 0;
}

.u-av img {
    width: 100%;
    height: 100%;
    border-radius: 50%;
    object-fit: cover;
}

/* ============================================================
   BADGES DE ESTADO
   ============================================================ */
.badge-status {
    padding: 6px 14px;
    border-radius: 20px;
    font-size: 12px;
    font-weight: 600;
    display: inline-flex;
    align-items: center;
    gap: 6px;
    text-transform: capitalize;
    min-width: 90px;
    justify-content: center;
    border: none;
    transition: all 0.2s ease;
    cursor: pointer;
    position: relative;
}

.badge-status:hover {
    transform: scale(1.05);
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
}

.badge-status .badge-dot {
    display: inline-block;
    width: 6px;
    height: 6px;
    border-radius: 50%;
}

.badge-status.draft { background: #f1f5f9; color: #475569; }
.badge-status.draft .badge-dot { background: #94a3b8; }

.badge-status.sent { background: #dbeafe; color: #1e40af; }
.badge-status.sent .badge-dot { background: #3b82f6; }

.badge-status.approved { background: #dcfce7; color: #166534; }
.badge-status.approved .badge-dot { background: #22c55e; }

.badge-status.rejected { background: #fee2e2; color: #991b1b; }
.badge-status.rejected .badge-dot { background: #ef4444; }

.badge-status.expired { background: #fef3c7; color: #92400e; }
.badge-status.expired .badge-dot { background: #f59e0b; }

.badge-status.cancelled { background: #f1f5f9; color: #64748b; }
.badge-status.cancelled .badge-dot { background: #94a3b8; }

/* ============================================================
   MENÚ DESPLEGABLE PARA CAMBIAR ESTADO
   ============================================================ */
.status-dropdown {
    position: relative;
    display: inline-block;
}

.quote-status-menu {
    display: none !important;
    position: fixed !important;
    min-width: 160px;
    background: #fff;
    border-radius: 12px;
    box-shadow: 0 10px 40px rgba(0, 0, 0, 0.15);
    border: 1px solid #e2e8f0;
    padding: 6px;
    margin: 0;
    list-style: none;
    z-index: 9999;
}

.quote-status-menu.show {
    display: block !important;
    animation: dropdownFadeIn 0.15s ease;
}

@keyframes dropdownFadeIn {
    from { opacity: 0; transform: translateY(-4px); }
    to { opacity: 1; transform: translateY(0); }
}

.quote-status-item {
    display: flex;
    align-items: center;
    gap: 8px;
    padding: 8px 14px;
    border-radius: 8px;
    cursor: pointer;
    transition: all 0.15s;
    font-size: 13px;
    color: #0f172a;
    border: none;
    background: none;
    width: 100%;
    text-align: left;
    text-decoration: none;
}

.quote-status-item:hover { background: #f1f5f9; }

.quote-status-item .status-dot {
    width: 8px;
    height: 8px;
    border-radius: 50%;
    flex-shrink: 0;
}

.quote-status-item.draft .status-dot { background: #94a3b8; }
.quote-status-item.sent .status-dot { background: #3b82f6; }
.quote-status-item.approved .status-dot { background: #22c55e; }
.quote-status-item.rejected .status-dot { background: #ef4444; }
.quote-status-item.expired .status-dot { background: #f59e0b; }
.quote-status-item.cancelled .status-dot { background: #94a3b8; }

/* ============================================================
   MENÚ DESPLEGABLE PARA ACCIONES (NUEVO)
   ============================================================ */
.action-dropdown {
    position: relative;
    display: inline-block;
}

.quote-action-menu {
    display: none !important;
    position: fixed !important;
    min-width: 140px;
    background: #fff;
    border-radius: 12px;
    box-shadow: 0 10px 40px rgba(0, 0, 0, 0.15);
    border: 1px solid #e2e8f0;
    padding: 6px;
    margin: 0;
    list-style: none;
    z-index: 9999;
}

.quote-action-menu.show {
    display: block !important;
    animation: dropdownFadeIn 0.15s ease;
}

.quote-action-item {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 8px 14px;
    border-radius: 8px;
    cursor: pointer;
    transition: all 0.15s;
    font-size: 13px;
    color: #0f172a;
    border: none;
    background: none;
    width: 100%;
    text-align: left;
    text-decoration: none;
}

.quote-action-item:hover { background: #f1f5f9; }
.quote-action-item.danger:hover { background: #fee2e2; color: #dc2626; }

.quote-action-item i {
    font-size: 16px;
    width: 18px;
    text-align: center;
}

.quote-action-item .action-icon {
    display: flex;
    align-items: center;
    justify-content: center;
}

/* Botón de acciones (tres puntitos) */
.btn-actions {
    background: transparent;
    border: none;
    color: #94a3b8;
    padding: 4px 8px;
    border-radius: 6px;
    cursor: pointer;
    transition: all 0.2s;
    font-size: 18px;
    line-height: 1;
}

.btn-actions:hover {
    background: #f1f5f9;
    color: #0f172a;
}

.btn-actions:focus {
    outline: none;
}

/* ============================================================
   BOTONES
   ============================================================ */
.btn-primary {
    background: #0f172a;
    border: none;
    color: #fff;
    padding: 8px 20px;
    border-radius: 8px;
    font-weight: 500;
    transition: all 0.2s;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 6px;
}

.btn-primary:hover {
    background: #1e293b;
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(15, 23, 42, 0.15);
    color: #fff;
}

.btn-secondary {
    background: #f1f5f9;
    border: 1px solid #e2e8f0;
    color: #475569;
    padding: 8px 20px;
    border-radius: 8px;
    font-weight: 500;
    transition: all 0.15s;
}

.btn-secondary:hover {
    background: #e2e8f0;
    border-color: #cbd5e1;
}

.btn-outline-secondary {
    background: transparent;
    border: 1px solid #e2e8f0;
    color: #475569;
    padding: 8px 20px;
    border-radius: 8px;
    font-weight: 500;
    transition: all 0.15s;
}

.btn-outline-secondary:hover {
    background: #f1f5f9;
    border-color: #cbd5e1;
}

/* ============================================================
   TABLA
   ============================================================ */
.table-responsive {
    overflow-x: auto;
    position: relative;
}

.table {
    width: 100%;
    border-collapse: collapse;
    font-size: 13px;
}

.table thead {
    background: #f8fafc;
    border-bottom: 2px solid #e2e8f0;
}

.table thead th {
    padding: 12px 14px;
    text-align: left;
    font-weight: 600;
    color: #475569;
    font-size: 11px;
    text-transform: uppercase;
    letter-spacing: 0.4px;
    white-space: nowrap;
}

.table tbody tr {
    border-bottom: 1px solid #f1f5f9;
    transition: background 0.15s;
}

.table tbody tr:hover { background: #fafbfc; }

.table tbody td {
    padding: 12px 14px;
    vertical-align: middle;
    color: #0f172a;
}

.table .quote-name-link {
    color: #0f172a;
    font-weight: 600;
    text-decoration: none;
    transition: color 0.15s;
}

.table .quote-name-link:hover { color: #6366f1; }

.table .quote-number {
    color: #94a3b8;
    font-size: 11px;
    display: block;
}

/* ============================================================
   CORRELATIVO BADGE
   ============================================================ */
.badge-correlative {
    display: inline-block;
    padding: 2px 10px;
    border-radius: 4px;
    font-size: 12px;
    font-weight: 600;
    background: #f1f5f9;
    color: #0f172a;
    font-family: monospace;
    letter-spacing: 0.5px;
}

.badge-correlative.assigned {
    background: #dbeafe;
    color: #1e40af;
    border: 1px solid #93c5fd;
}

.badge-correlative.pending {
    background: #f1f5f9;
    color: #94a3b8;
}

/* ============================================================
   FILTROS
   ============================================================ */
.filters-row {
    display: flex;
    gap: 12px;
    flex-wrap: wrap;
    align-items: center;
    margin-bottom: 1.2rem;
}

.filters-row .filter-group {
    flex: 1;
    min-width: 150px;
}

.filters-row .filter-group .form-control {
    padding: 8px 12px;
    font-size: 13px;
}

.form-control {
    width: 100%;
    padding: 10px 14px;
    border-radius: 8px;
    border: 1.5px solid #e2e8f0;
    font-size: 13px;
    outline: none;
    transition: all 0.15s;
    background: #fff;
    color: #0f172a;
}

.form-control:focus {
    border-color: #6366f1;
    box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.1);
}

/* ============================================================
   PAGINACIÓN
   ============================================================ */
.pagination-wrapper {
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 0.8rem;
    padding-top: 1rem;
    margin-top: 0.5rem;
    border-top: 1px solid #e2e8f0;
}

.pagination-wrapper .info-text { font-size: 13px; color: #94a3b8; }

.pagination-wrapper .pagination-links {
    display: flex;
    align-items: center;
    gap: 0.3rem;
}

.pagination-wrapper .pagination-links .page-link {
    padding: 6px 12px;
    border-radius: 6px;
    font-size: 13px;
    text-decoration: none;
    border: 1px solid #e2e8f0;
    color: #475569;
    background: #fff;
    transition: all 0.15s;
}

.pagination-wrapper .pagination-links .page-link:hover {
    background: #f1f5f9;
    border-color: #cbd5e1;
}

.pagination-wrapper .pagination-links .page-current {
    background: #0f172a;
    color: #fff;
    font-weight: 600;
    border: 1px solid #0f172a;
    padding: 6px 12px;
    border-radius: 6px;
    font-size: 13px;
}

.pagination-wrapper .pagination-links .page-disabled {
    padding: 6px 12px;
    border-radius: 6px;
    font-size: 13px;
    border: 1px solid #e2e8f0;
    color: #cbd5e1;
    cursor: default;
    background: #fff;
}

.pagination-wrapper .pagination-links .page-dots {
    color: #cbd5e1;
    padding: 6px 4px;
    border: none;
}

/* ============================================================
   EMPTY STATE
   ============================================================ */
.empty-state {
    text-align: center;
    padding: 3rem 1.5rem;
    color: #94a3b8;
}

.empty-state i {
    font-size: 48px;
    color: #cbd5e1;
    display: block;
    margin-bottom: 12px;
}

.empty-state p {
    font-size: 16px;
    font-weight: 600;
    color: #0f172a;
    margin-bottom: 4px;
}

.empty-state span { font-size: 13px; color: #94a3b8; }

/* ============================================================
   BADGE HOTEL
   ============================================================ */
.hotel-badge {
    display: inline-flex;
    align-items: center;
    gap: 4px;
    padding: 2px 10px;
    border-radius: 12px;
    font-size: 11px;
    font-weight: 500;
}

.hotel-badge.full {
    background: #dcfce7;
    color: #166534;
}

.hotel-badge.partial {
    background: #fef3c7;
    color: #92400e;
}

.hotel-badge.none {
    background: #f1f5f9;
    color: #94a3b8;
}

.hotel-badge.option {
    background: #dbeafe;
    color: #1e40af;
}

/* ============================================================
   RESPONSIVE
   ============================================================ */
@media (max-width: 768px) {
    .card-header { flex-direction: column; align-items: stretch; gap: 0.8rem; }
    .card-title { font-size: 18px; }
    .filters-row .filter-group { min-width: 100%; }
    .filters-row { flex-direction: column; align-items: stretch; }
    .filters-row .filter-group { flex: none; }
    .btn-apply-filters, .btn-clear-filters { width: 100%; justify-content: center; }
    .pagination-wrapper { flex-direction: column; align-items: stretch; gap: 0.8rem; }
    .pagination-wrapper .pagination-links { justify-content: center; flex-wrap: wrap; }

    .table thead { display: none; }

    .table tbody tr {
        display: block;
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        margin-bottom: 10px;
        background: #fff;
        padding: 4px 0;
    }

    .table tbody td {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 6px 12px;
        border-bottom: 1px solid #f1f5f9;
    }

    .table tbody td:last-child { border-bottom: none; }

    .table tbody td::before {
        content: attr(data-label);
        font-weight: 600;
        color: #475569;
        font-size: 10px;
        text-transform: uppercase;
        letter-spacing: 0.4px;
    }

    .badge-status { font-size: 11px; padding: 4px 12px; min-width: 80px; }
    .quote-status-menu { min-width: 200px; max-width: 90vw; }
    .quote-action-menu { min-width: 160px; max-width: 90vw; }
}

@media (max-width: 480px) {
    .card-title { font-size: 16px; }
    .btn-primary { padding: 6px 14px; font-size: 13px; }
}

/* ============================================================
   ALERTAS
   ============================================================ */
.alert {
    padding: 12px 16px;
    border-radius: 8px;
    margin-bottom: 1rem;
}

.alert-success { background: #dcfce7; border: 1px solid #86efac; color: #166534; }
.alert-danger { background: #fee2e2; border: 1px solid #fca5a5; color: #991b1b; }

.alert .close {
    float: right;
    background: none;
    border: none;
    font-size: 20px;
    color: inherit;
    cursor: pointer;
    opacity: 0.6;
}

.alert .close:hover { opacity: 1; }
</style>
@endpush

<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <!-- ===== CARD HEADER ===== -->
            <div class="card-header">
                <h3 class="card-title">
                    <i class="ti ti-file-percent"></i>
                    Mis Cotizaciones
                    <span style="font-size:14px; font-weight:500; color:#94a3b8; background:#f1f5f9; padding:2px 12px; border-radius:12px;">
                        {{ $totalQuotes }}
                    </span>
                </h3>
                <div class="card-tools">
                    <a href="{{ route('admin.quotes.create') }}" class="btn btn-primary">
                        <i class="ti ti-circle-plus"></i> Nueva Cotización
                    </a>
                </div>
            </div>

            <!-- ===== CARD BODY ===== -->
            <div class="card" style="border-radius:0 0 12px 12px; border:1px solid #e2e8f0; border-top:none; background:#fff;">
                <div class="card-body" style="padding:1.5rem;">

                    <!-- ===== FILTROS ===== -->
                    <div class="filters-row">
                        <div class="filter-group">
                            <input type="text" id="searchInput" class="form-control" placeholder="Buscar cotización..." value="{{ request('search') }}">
                        </div>
                        <div class="filter-group">
                            <select id="statusFilter" class="form-control">
                                <option value="">Todos los estados</option>
                                <option value="draft" {{ request('status') == 'draft' ? 'selected' : '' }}>Borrador</option>
                                <option value="sent" {{ request('status') == 'sent' ? 'selected' : '' }}>Enviada</option>
                                <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Aprobada</option>
                                <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Rechazada</option>
                                <option value="expired" {{ request('status') == 'expired' ? 'selected' : '' }}>Vencida</option>
                                <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Cancelada</option>
                            </select>
                        </div>
                        <button id="btnApplyFilters" class="btn btn-secondary btn-apply-filters">
                            <i class="ti ti-search"></i> Filtrar
                        </button>
                        <button id="btnClearFilters" class="btn btn-outline-secondary btn-clear-filters">
                            <i class="ti ti-x"></i> Limpiar
                        </button>
                    </div>

                    <!-- ===== ALERTAS ===== -->
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('success') }}
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            {{ session('error') }}
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    @endif

                    <!-- ===== TABLA ===== -->
                    <div class="table-responsive">
                        <table class="table" id="quotesTable">
                            <thead>
                                <tr>
                                    <th style="width:30px;"><input type="checkbox" id="selectAll"></th>
                                    <th>Nombre</th>
                                    <th>N° File</th>
                                    <th>Estado</th>
                                    <th>Cliente</th>
                                    <th>Autor</th>
                                    <th>Días</th>
                                    <th>Pasajeros</th>
                                    <th>Total</th>
                                    <th>Fecha</th>
                                    <th style="text-align:center; width:50px;">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($quotes as $quote)
                                <tr>
                                    <td data-label="Seleccionar">
                                        <input type="checkbox" class="form-check-input quote-checkbox" name="quote_ids[]" value="{{ $quote->id_quote }}">
                                    </td>
                                    <td data-label="Nombre">
                                        <a  href="{{ route('admin.quotes.edit', $quote->id_quote) }}"  class="quote-name-link">
                                            {{ $quote->name ?? 'Cotización' }}
                                        </a>
                                    </td>
                                    <td data-label="Correlativo">
                                        @if($quote->correlative)
                                            <span class="badge-correlative assigned">
                                                <i class="ti ti-file-text" style="font-size:11px;"></i>
                                                {{ $quote->correlative }}
                                            </span>
                                        @else
                                            <span class="badge-correlative pending">
                                                <i class="ti ti-minus" style="font-size:11px;"></i>
                                                No Generado
                                            </span>
                                        @endif
                                    </td>
                                    <td data-label="Estado">
                                        <div class="status-dropdown">
                                            <span class="badge-status {{ $quote->status }}"
                                                  data-id="{{ $quote->id_quote }}"
                                                  data-current-status="{{ $quote->status }}"
                                                  onclick="toggleStatusMenu(this, event)">
                                                <span class="badge-dot"></span>
                                                {{ ucfirst($quote->status) }}
                                                <i class="ti ti-chevron-down" style="font-size:10px; margin-left:4px;"></i>
                                            </span>
                                            <div class="quote-status-menu" data-id="{{ $quote->id_quote }}">
                                                <button type="button" class="quote-status-item draft" data-status="draft" onclick="changeQuoteStatus(this, {{ $quote->id_quote }}, 'draft', event)">
                                                    <span class="status-dot"></span> Borrador
                                                </button>
                                                <button type="button" class="quote-status-item sent" data-status="sent" onclick="changeQuoteStatus(this, {{ $quote->id_quote }}, 'sent', event)">
                                                    <span class="status-dot"></span> Enviada
                                                </button>
                                                <button type="button" class="quote-status-item approved" data-status="approved" onclick="changeQuoteStatus(this, {{ $quote->id_quote }}, 'approved', event)">
                                                    <span class="status-dot"></span> Aprobada
                                                </button>
                                                <button type="button" class="quote-status-item rejected" data-status="rejected" onclick="changeQuoteStatus(this, {{ $quote->id_quote }}, 'rejected', event)">
                                                    <span class="status-dot"></span> Rechazada
                                                </button>
                                                <button type="button" class="quote-status-item expired" data-status="expired" onclick="changeQuoteStatus(this, {{ $quote->id_quote }}, 'expired', event)">
                                                    <span class="status-dot"></span> Vencida
                                                </button>
                                                <button type="button" class="quote-status-item cancelled" data-status="cancelled" onclick="changeQuoteStatus(this, {{ $quote->id_quote }}, 'cancelled', event)">
                                                    <span class="status-dot"></span> Cancelada
                                                </button>
                                            </div>
                                        </div>
                                    </td>
                                    <td data-label="Cliente">{{ $quote->client ? $quote->client->name_client : 'N/A' }}</td>
                                    <td data-label="Autor">
                                        <div style="display:flex; align-items:center; gap:8px;">
                                            <div class="u-av">
                                                @if($quote->user && $quote->user->avatar)
                                                    @php $filename = basename($quote->user->avatar); @endphp
                                                    <img src="{{ route('avatar.show', $filename) }}" alt="{{ $quote->user->name }}" />
                                                @else
                                                    {{ strtoupper(substr($quote->user ? $quote->user->name : '-', 0, 2)) }}
                                                @endif
                                            </div>
                                            <span>{{ $quote->user ? $quote->user->name : '-' }}</span>
                                        </div>
                                    </td>
                                    <td data-label="Días">{{ $quote->days ?? '-' }}</td>
                                    <td data-label="Pasajeros">{{ $quote->passengers_count ?? '-' }}</td>
                                    
                                    <td data-label="Total">
                                       <strong>{{ number_format($quote->total ?? 0, 2) }}</strong>
                                    </td>
                                    <td data-label="Fecha">
                                        <small>{{ $quote->created_at ? date('d/m/Y', strtotime($quote->created_at)) : '-' }}</small>
                                    </td>
                                    <td data-label="Acciones" style="text-align:center;">
    <div class="action-dropdown">
        <button class="btn-actions" 
                data-id="{{ $quote->id_quote }}"
                onclick="toggleActionMenu(this, event)"
                title="Acciones">
            <i class="ti ti-dots-vertical"></i>
        </button>
        <div class="quote-action-menu" data-id="{{ $quote->id_quote }}">
            <!-- Ver Cotización (opcional pero útil) -->
            <a href="{{ route('admin.quotes.show', $quote->id_quote) }}" 
               class="quote-action-item">
                <i class="ti ti-eye"></i> Ver
            </a>
            <!-- Editar -->
            <a href="{{ route('admin.quotes.edit', $quote->id_quote) }}" 
               class="quote-action-item">
                <i class="ti ti-edit"></i> Editar
            </a>
            <!-- DUPLICAR - NUEVO -->
            <button type="button" 
                    class="quote-action-item"
                    onclick="confirmDuplicate({{ $quote->id_quote }}, '{{ addslashes($quote->name ?? 'Cotización') }}'); closeActionMenu(this);">
                <i class="ti ti-copy"></i> Duplicar
            </button>
            <!-- Eliminar -->
            <button type="button" 
                    class="quote-action-item danger"
                    onclick="confirmDelete({{ $quote->id_quote }}, '{{ addslashes($quote->name ?? 'Cotización') }}'); closeActionMenu(this);">
                <i class="ti ti-trash"></i> Eliminar
            </button>
        </div>
    </div>
</td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="11">
                                        <div class="empty-state">
                                            <i class="ti ti-file-off"></i>
                                            <p>No hay cotizaciones</p>
                                            <span>Crea tu primera cotización haciendo clic en "Nueva Cotización"</span>
                                        </div>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    @if(method_exists($quotes, 'links') && $quotes->total() > 0)
                        <div class="pagination-wrapper">
                            <span class="info-text">
                                Mostrando {{ $quotes->firstItem() }}–{{ $quotes->lastItem() }} de {{ $quotes->total() }} cotizaciones
                            </span>
                            <div class="pagination-links">
                                @if($quotes->onFirstPage())
                                    <span class="page-disabled"><i class="ti ti-chevron-left"></i></span>
                                @else
                                    <a href="{{ $quotes->previousPageUrl() }}" class="page-link"><i class="ti ti-chevron-left"></i></a>
                                @endif

                                @php
                                    $current = $quotes->currentPage();
                                    $last = $quotes->lastPage();
                                    $range = 2;
                                @endphp

                                @for($i = 1; $i <= $last; $i++)
                                    @if($i == 1 || $i == $last || abs($i - $current) <= $range)
                                        @if($i == $current)
                                            <span class="page-current">{{ $i }}</span>
                                        @else
                                            <a href="{{ $quotes->url($i) }}" class="page-link">{{ $i }}</a>
                                        @endif
                                    @elseif(abs($i - $current) == $range + 1)
                                        <span class="page-dots">…</span>
                                    @endif
                                @endfor

                                @if($quotes->hasMorePages())
                                    <a href="{{ $quotes->nextPageUrl() }}" class="page-link"><i class="ti ti-chevron-right"></i></a>
                                @else
                                    <span class="page-disabled"><i class="ti ti-chevron-right"></i></span>
                                @endif
                            </div>
                        </div>
                    @endif

                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>

(function ensureSwalLoaded() {
    if (typeof window.Swal === 'undefined' && !document.getElementById('sweetalert2-cdn')) {
        var script = document.createElement('script');
        script.id = 'sweetalert2-cdn';
        script.src = 'https://cdn.jsdelivr.net/npm/sweetalert2@11';
        document.head.appendChild(script);
    }
})();

function safeConfirm(title, text, confirmText, cancelText) {
    return new Promise(function(resolve) {
        if (typeof window.Swal !== 'undefined') {
            Swal.fire({
                title: title,
                text: text,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#6366f1',
                cancelButtonColor: '#94a3b8',
                confirmButtonText: confirmText || 'Sí',
                cancelButtonText: cancelText || 'Cancelar'
            }).then(function(result) {
                resolve(result.isConfirmed);
            });
        } else {
            resolve(window.confirm(title + '\n' + text));
        }
    });
}

function safeAlert(icon, title, text, timer) {
    if (typeof window.Swal !== 'undefined') {
        Swal.fire({
            icon: icon,
            title: title,
            text: text,
            timer: timer || undefined,
            showConfirmButton: !timer
        });
    } else {
        window.alert(title + (text ? '\n' + text : ''));
    }
}

const CSRF_TOKEN = '{{ csrf_token() }}';

document.addEventListener('DOMContentLoaded', function() {

    // ============================================================
    // FILTROS
    // ============================================================
    const searchInput = document.getElementById('searchInput');
    const statusFilter = document.getElementById('statusFilter');
    const btnApplyFilters = document.getElementById('btnApplyFilters');
    const btnClearFilters = document.getElementById('btnClearFilters');

    function applyFilters() {
        const url = new URL(window.location.href);
        const search = searchInput?.value.trim();
        const status = statusFilter?.value;

        if (search) url.searchParams.set('search', search);
        else url.searchParams.delete('search');

        if (status) url.searchParams.set('status', status);
        else url.searchParams.delete('status');

        window.location.href = url.toString();
    }

    if (btnApplyFilters) btnApplyFilters.addEventListener('click', applyFilters);

    if (btnClearFilters) {
        btnClearFilters.addEventListener('click', function() {
            if (searchInput) searchInput.value = '';
            if (statusFilter) statusFilter.value = '';
            applyFilters();
        });
    }

    if (searchInput) {
        searchInput.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') applyFilters();
        });
    }

    // ============================================================
    // SELECT ALL
    // ============================================================
    const selectAll = document.getElementById('selectAll');
    const checkboxes = document.querySelectorAll('.quote-checkbox');

    if (selectAll) {
        selectAll.addEventListener('change', function() {
            checkboxes.forEach(cb => cb.checked = this.checked);
        });
    }

    window.toggleStatusMenu = function(element, event) {
        if (event) event.stopPropagation();

        var id = element.dataset.id;
        var dropdown = document.querySelector('.quote-status-menu[data-id="' + id + '"]');
        if (!dropdown) return;

        document.querySelectorAll('.quote-action-menu.show').forEach(function(menu) {
            menu.classList.remove('show');
        });

        if (dropdown.parentNode !== document.body) {
            document.body.appendChild(dropdown);
        }

        var wasOpen = dropdown.classList.contains('show');

        document.querySelectorAll('.quote-status-menu.show').forEach(function(menu) {
            menu.classList.remove('show');
        });

        if (wasOpen) return;

        dropdown.style.visibility = 'hidden';
        dropdown.classList.add('show');

        var rect = element.getBoundingClientRect();
        var dRect = dropdown.getBoundingClientRect();
        var margin = 8;

        var spaceBelow = window.innerHeight - rect.bottom;
        var spaceAbove = rect.top;

        var top = (spaceBelow >= dRect.height + margin || spaceBelow > spaceAbove)
            ? rect.bottom + 4
            : rect.top - dRect.height - 4;

        var left = rect.left + (rect.width / 2) - (dRect.width / 2);
        if (left < margin) left = margin;
        if (left + dRect.width > window.innerWidth - margin) {
            left = window.innerWidth - dRect.width - margin;
        }
        if (top < margin) top = margin;

        dropdown.style.top = top + 'px';
        dropdown.style.left = left + 'px';
        dropdown.style.visibility = 'visible';
    };

    window.toggleActionMenu = function(element, event) {
        if (event) event.stopPropagation();

        var id = element.dataset.id;
        var dropdown = document.querySelector('.quote-action-menu[data-id="' + id + '"]');
        if (!dropdown) return;

        document.querySelectorAll('.quote-status-menu.show').forEach(function(menu) {
            menu.classList.remove('show');
        });

        if (dropdown.parentNode !== document.body) {
            document.body.appendChild(dropdown);
        }

        var wasOpen = dropdown.classList.contains('show');

        document.querySelectorAll('.quote-action-menu.show').forEach(function(menu) {
            menu.classList.remove('show');
        });

        if (wasOpen) return;

        dropdown.style.visibility = 'hidden';
        dropdown.classList.add('show');

        var rect = element.getBoundingClientRect();
        var dRect = dropdown.getBoundingClientRect();
        var margin = 8;

        var spaceBelow = window.innerHeight - rect.bottom;
        var spaceAbove = rect.top;

        var top = (spaceBelow >= dRect.height + margin || spaceBelow > spaceAbove)
            ? rect.bottom + 4
            : rect.top - dRect.height - 4;

        var left = rect.left + (rect.width / 2) - (dRect.width / 2);
        if (left < margin) left = margin;
        if (left + dRect.width > window.innerWidth - margin) {
            left = window.innerWidth - dRect.width - margin;
        }
        if (top < margin) top = margin;

        dropdown.style.top = top + 'px';
        dropdown.style.left = left + 'px';
        dropdown.style.visibility = 'visible';
    };

    window.closeActionMenu = function(element) {
        var dropdown = element.closest('.quote-action-menu');
        if (dropdown) dropdown.classList.remove('show');
    };

    document.addEventListener('click', function() {
        document.querySelectorAll('.quote-status-menu.show').forEach(function(menu) {
            menu.classList.remove('show');
        });
        document.querySelectorAll('.quote-action-menu.show').forEach(function(menu) {
            menu.classList.remove('show');
        });
    });

    window.addEventListener('scroll', function() {
        document.querySelectorAll('.quote-status-menu.show, .quote-action-menu.show').forEach(function(menu) {
            menu.classList.remove('show');
        });
    }, true);

    window.addEventListener('resize', function() {
        document.querySelectorAll('.quote-status-menu.show, .quote-action-menu.show').forEach(function(menu) {
            menu.classList.remove('show');
        });
    });

    window.changeQuoteStatus = function(element, id, newStatus, event) {
        if (event) event.stopPropagation();

        var badge = document.querySelector('.badge-status[data-id="' + id + '"]');
        var currentStatus = badge ? badge.dataset.currentStatus : '';

        if (newStatus === currentStatus) {
            var dropdown = element.closest('.quote-status-menu');
            if (dropdown) dropdown.classList.remove('show');
            return;
        }

        var dropdown = element.closest('.quote-status-menu');
        if (dropdown) dropdown.classList.remove('show');

        var statusLabels = {
            'draft': 'Borrador', 'sent': 'Enviada', 'approved': 'Aprobada',
            'rejected': 'Rechazada', 'expired': 'Vencida', 'cancelled': 'Cancelada'
        };

        var newStatusLabel = statusLabels[newStatus] || newStatus;

        if (newStatus === 'approved' || newStatus === 'rejected' || newStatus === 'cancelled') {
            safeConfirm(
                '¿Cambiar estado?',
                'Estás a punto de cambiar el estado a "' + newStatusLabel + '". ¿Estás seguro?',
                'Sí, cambiar',
                'Cancelar'
            ).then(function(confirmed) {
                if (confirmed) sendStatusChange(id, newStatus);
            });
        } else {
            sendStatusChange(id, newStatus);
        }
    };

    function sendStatusChange(id, newStatus) {
        var badge = document.querySelector('.badge-status[data-id="' + id + '"]');
        if (badge) {
            badge.style.opacity = '0.5';
            badge.style.pointerEvents = 'none';
        }

        fetch('/cotizaciones/' + id + '/status', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': CSRF_TOKEN,
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            },
            body: JSON.stringify({ status: newStatus })
        })
        .then(response => response.json())
        .then(data => {
            if (badge) {
                badge.style.opacity = '1';
                badge.style.pointerEvents = 'auto';
            }

            if (data.success) {
                if (badge) {
                    badge.className = 'badge-status ' + newStatus;
                    badge.dataset.currentStatus = newStatus;
                    badge.innerHTML = '<span class="badge-dot"></span> ' +
                        capitalizeFirstLetter(newStatus) +
                        ' <i class="ti ti-chevron-down" style="font-size:10px; margin-left:4px;"></i>';
                }

                var message = 'Estado actualizado correctamente';
                if (data.correlative) message += ' - Correlativo asignado: ' + data.correlative;

                safeAlert('success', '¡Éxito!', message, 2500);

                if (data.correlative) {
                    var row = badge ? badge.closest('tr') : null;
                    if (row) {
                        var correlativeCell = row.querySelector('td[data-label="Correlativo"]');
                        if (correlativeCell) {
                            correlativeCell.innerHTML = `
                                <span class="badge-correlative assigned">
                                    <i class="ti ti-file-text" style="font-size:11px;"></i>
                                    ${data.correlative}
                                </span>
                            `;
                        }
                    }
                }
            } else {
                var currentStatus = badge ? badge.dataset.currentStatus : 'draft';
                if (badge) {
                    badge.className = 'badge-status ' + currentStatus;
                    badge.innerHTML = '<span class="badge-dot"></span> ' +
                        capitalizeFirstLetter(currentStatus) +
                        ' <i class="ti ti-chevron-down" style="font-size:10px; margin-left:4px;"></i>';
                }
                safeAlert('error', 'Error', data.message || 'Error al cambiar el estado');
            }
        })
        .catch(error => {
            if (badge) {
                badge.style.opacity = '1';
                badge.style.pointerEvents = 'auto';
                var currentStatus = badge.dataset.currentStatus || 'draft';
                badge.className = 'badge-status ' + currentStatus;
                badge.innerHTML = '<span class="badge-dot"></span> ' +
                    capitalizeFirstLetter(currentStatus) +
                    ' <i class="ti ti-chevron-down" style="font-size:10px; margin-left:4px;"></i>';
            }
            safeAlert('error', 'Error de conexión', 'No se pudo cambiar el estado. Verifica tu conexión.');
            console.error('Error:', error);
        });
    }

    function capitalizeFirstLetter(string) {
        return string.charAt(0).toUpperCase() + string.slice(1);
    }

    // ============================================================
    // ELIMINAR COTIZACIÓN
    // ============================================================
    window.confirmDelete = function(id, name) {
        // Cerrar menú de acciones
        var actionMenus = document.querySelectorAll('.quote-action-menu.show');
        actionMenus.forEach(function(menu) {
            menu.classList.remove('show');
        });

        safeConfirm(
            '¿Eliminar cotización?',
            'Estás a punto de eliminar "' + name + '". Esta acción no se puede deshacer.',
            'Sí, eliminar',
            'Cancelar'
        ).then(function(confirmed) {
            if (confirmed) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = '/cotizaciones/' + id;
                form.innerHTML = `
                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                    <input type="hidden" name="_method" value="DELETE">
                `;
                document.body.appendChild(form);
                form.submit();
            }
        });
    };


    // ============================================================
// DUPLICAR COTIZACIÓN
// ============================================================
window.confirmDuplicate = function(id, name) {
    // Cerrar menú de acciones
    var actionMenus = document.querySelectorAll('.quote-action-menu.show');
    actionMenus.forEach(function(menu) {
        menu.classList.remove('show');
    });

    safeConfirm(
        '¿Duplicar cotización?',
        'Estás a punto de duplicar "' + name + '". Se creará una copia en estado "Borrador".',
        'Sí, duplicar',
        'Cancelar'
    ).then(function(confirmed) {
        if (confirmed) {
            // Mostrar loading
            safeAlert('info', 'Duplicando...', 'Por favor espera.', 1500);
            
            fetch('/cotizaciones/' + id + '/duplicate', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': CSRF_TOKEN,
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    safeAlert('success', '¡Duplicado!', 'La cotización fue duplicada exitosamente.', 2000);
                    // Recargar la página para ver la nueva cotización
                    setTimeout(() => {
                        window.location.reload();
                    }, 1500);
                } else {
                    safeAlert('error', 'Error', data.message || 'Error al duplicar la cotización.');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                safeAlert('error', 'Error de conexión', 'No se pudo duplicar la cotización. Verifica tu conexión.');
            });
        }
    });
};

});
</script>
@endpush

@endsection