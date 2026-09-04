@extends('layouts.app')

@section('title', 'Servicios')

@push('styles')
<style>
    /* --- Contenedor principal --- */
    .services-page {
        padding: 2rem;
        max-width: 1400px;
        margin: 0 auto;
        font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
    }

    /* --- Cabecera --- */
    .services-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 1rem;
        margin-bottom: 1.75rem;
        flex-wrap: wrap;
    }

    .services-header-left h1 {
        margin: 0;
        font-size: 1.5rem;
        font-weight: 600;
        color: #0b1a33;
        letter-spacing: -0.01em;
    }

    .services-header-left p {
        margin: 0.3rem 0 0 0;
        color: #64748b;
        font-size: 0.92rem;
    }

    .services-header-actions {
        display: flex;
        gap: 0.6rem;
        flex-wrap: wrap;
    }

    /* --- Botones --- */
    .btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 0.5rem;
        padding: 0.55rem 1.25rem;
        border-radius: 10px;
        font-weight: 500;
        font-size: 0.85rem;
        border: 1px solid transparent;
        transition: background 0.2s, border-color 0.2s, box-shadow 0.15s;
        cursor: pointer;
        text-decoration: none;
        background: transparent;
        color: #1e293b;
        line-height: 1.4;
        white-space: nowrap;
    }

    .btn-primary {
        background: #2563eb;
        color: #ffffff;
        border-color: #2563eb;
    }
    .btn-primary:hover {
        background: #1d4ed8;
        border-color: #1d4ed8;
        color: #ffffff;
        box-shadow: 0 4px 12px rgba(37, 99, 235, 0.25);
    }

    .btn-secondary {
        background: #f1f5f9;
        color: #1e293b;
        border-color: #e2e8f0;
    }
    .btn-secondary:hover {
        background: #e9eef4;
        border-color: #d0d9e6;
    }

    .btn-success {
        background: #059669;
        color: #ffffff;
        border-color: #059669;
    }
    .btn-success:hover {
        background: #047857;
        border-color: #047857;
        color: #ffffff;
        box-shadow: 0 4px 12px rgba(5, 150, 105, 0.25);
    }

    .btn-sm {
        padding: 0.35rem 0.85rem;
        font-size: 0.78rem;
        border-radius: 8px;
    }

    .btn-outline-secondary {
        background: transparent;
        border-color: #dce1e9;
        color: #475569;
    }
    .btn-outline-secondary:hover {
        background: #f8fafc;
        border-color: #bcc7d6;
    }

    /* --- Tarjeta de filtros --- */
    .filters-card {
        background: #ffffff;
        border-radius: 16px;
        border: 1px solid #eef2f6;
        padding: 1.25rem 1.5rem;
        margin-bottom: 1.5rem;
        display: grid;
        grid-template-columns: 2fr 1fr 1fr 1fr auto;
        gap: 1rem;
        align-items: end;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.03);
    }

    .filter-group {
        display: flex;
        flex-direction: column;
        gap: 0.3rem;
    }

    .filter-group label {
        font-size: 0.75rem;
        font-weight: 600;
        color: #475569;
        text-transform: uppercase;
        letter-spacing: 0.03em;
    }

    .filter-group .form-control {
        padding: 0.55rem 0.9rem;
        border: 1px solid #dce1e9;
        border-radius: 10px;
        font-size: 0.88rem;
        background: #ffffff;
        transition: border-color 0.2s, box-shadow 0.2s;
        width: 100%;
        color: #0b1a33;
    }

    .filter-group .form-control:focus {
        border-color: #2563eb;
        box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.08);
        outline: none;
    }

    .filter-group .form-control[readonly] {
        background: #f8fafc;
        cursor: not-allowed;
        color: #64748b;
    }

    .filter-group .form-control[readonly]:focus {
        border-color: #dce1e9;
        box-shadow: none;
    }

    .filter-actions {
        display: flex;
        gap: 0.5rem;
        align-items: center;
    }

    /* --- Tarjeta de tabla --- */
    .table-card {
        background: #ffffff;
        border-radius: 16px;
        border: 1px solid #eef2f6;
        overflow: hidden;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.03);
    }

    .table-responsive {
        overflow-x: auto;
    }

    .table {
        width: 100%;
        border-collapse: collapse;
        font-size: 0.88rem;
        min-width: 700px;
    }

    .table thead {
        background: #f8fafc;
        border-bottom: 1px solid #eef2f6;
    }

    .table thead th {
        padding: 0.85rem 1.2rem;
        text-align: left;
        font-weight: 600;
        font-size: 0.78rem;
        text-transform: uppercase;
        letter-spacing: 0.04em;
        color: #475569;
        white-space: nowrap;
    }

    .table tbody td {
        padding: 0.85rem 1.2rem;
        border-bottom: 1px solid #f1f5f9;
        vertical-align: middle;
        color: #0b1a33;
    }

    .table tbody tr:hover {
        background: #fafbfc;
    }

    .table tbody tr:last-child td {
        border-bottom: none;
    }

    .service-name {
        font-weight: 600;
        color: #0b1a33;
        display: block;
        margin-bottom: 0.1rem;
    }

    .service-description {
        font-size: 0.78rem;
        color: #64748b;
        display: block;
        max-width: 200px;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    /* --- Badges de estado --- */
    .badge {
        display: inline-flex;
        align-items: center;
        gap: 0.3rem;
        padding: 0.2rem 0.65rem;
        border-radius: 20px;
        font-size: 0.72rem;
        font-weight: 500;
        text-transform: capitalize;
    }

    .badge-active {
        background: #dcfce7;
        color: #166534;
    }

    .badge-inactive {
        background: #fee2e2;
        color: #991b1b;
    }

    .badge-draft {
        background: #fef3c7;
        color: #92400e;
    }

    .badge-archived {
        background: #f1f5f9;
        color: #475569;
    }

    .badge-pricing {
        background: #e0f2fe;
        color: #0369a1;
        padding: 0.15rem 0.5rem;
        border-radius: 12px;
        font-size: 0.68rem;
        font-weight: 500;
    }

    /* --- Celda de acciones --- */
    .actions-cell {
        display: flex;
        gap: 0.4rem;
        flex-wrap: wrap;
    }

    /* --- Paginación --- */
    .pagination-wrapper {
        padding: 1rem 1.25rem;
        border-top: 1px solid #f1f5f9;
        display: flex;
        justify-content: flex-end;
    }

    .pagination-wrapper .pagination {
        margin: 0;
    }

    /* --- Estado vacío --- */
    .empty-state {
        text-align: center;
        padding: 3rem 1.5rem;
        color: #64748b;
    }

    .empty-state i {
        font-size: 3rem;
        color: #cbd5e1;
        display: block;
        margin-bottom: 0.75rem;
    }

    .empty-state p {
        margin: 0;
        font-size: 0.95rem;
    }

    /* --- Responsive --- */
    @media (max-width: 1024px) {
        .filters-card {
            grid-template-columns: 1fr 1fr;
        }
        .filter-actions {
            grid-column: span 2;
            justify-content: flex-end;
        }
    }

    @media (max-width: 768px) {
        .services-page {
            padding: 1rem;
        }

        .services-header {
            flex-direction: column;
            align-items: stretch;
            gap: 0.75rem;
        }

        .services-header-actions {
            flex-direction: column;
        }
        .services-header-actions .btn {
            justify-content: center;
        }

        .filters-card {
            grid-template-columns: 1fr;
            padding: 1rem;
        }
        .filter-actions {
            grid-column: span 1;
            flex-direction: column;
        }
        .filter-actions .btn {
            width: 100%;
            justify-content: center;
        }

        .table tbody td {
            padding: 0.65rem 0.85rem;
        }
        .table thead th {
            padding: 0.65rem 0.85rem;
            font-size: 0.7rem;
        }

        .pagination-wrapper {
            justify-content: center;
        }
    }
</style>
@endpush

@section('content')
<div class="services-page">

    {{-- Cabecera --}}
    <div class="services-header">
        <div class="services-header-left">
            <h1><i class="ti ti-package" style="color:#2563eb;margin-right:0.4rem;"></i> Servicios</h1>
            <p>Gestiona todas las actividades registradas en el sistema.</p>
        </div>
        <div class="services-header-actions">
            <a class="btn btn-primary" href="{{ route('admin.services.import.view') }}">
                <i class="ti ti-file-upload"></i> Importar Servicios
            </a>
            <a class="btn btn-success" href="{{ route('admin.services.create') }}">
                <i class="ti ti-plus"></i> Nuevo servicio
            </a>
        </div>
    </div>

    {{-- Filtros --}}
    <form method="GET" class="filters-card">
        <div class="filter-group">
            <label for="search"><i class="ti ti-search" style="font-size:0.8rem;"></i> Buscar</label>
            <input id="search" class="form-control" name="search" value="{{ $search }}" placeholder="Nombre o descripción...">
        </div>
        <div class="filter-group">
            <label><i class="ti ti-building-store"></i> Proveedor</label>
            <input class="form-control" value="Todos" readonly>
        </div>
        <div class="filter-group">
            <label for="category"><i class="ti ti-category"></i> Categoría</label>
            <select id="category" class="form-control" name="category">
                <option value="">Todas</option>
                @foreach($categories as $item)
                    <option value="{{ $item->id_category }}" {{ (string) $category === (string) $item->id_category ? 'selected' : '' }}>
                        {{ $item->name }}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="filter-group">
            <label for="label"><i class="ti ti-tags"></i> Mercado</label>
            <select id="label" class="form-control" name="label">
                <option value="">Todos</option>
                @foreach($labels as $item)
                    <option value="{{ $item->id_labels }}" {{ (string) $label === (string) $item->id_labels ? 'selected' : '' }}>
                        {{ $item->name_labels }}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="filter-actions">
            <button class="btn btn-primary" type="submit">
                <i class="ti ti-filter"></i> Filtrar
            </button>
            <a class="btn btn-outline-secondary" href="{{ route('admin.services.index') }}">
                <i class="ti ti-rotate"></i> Limpiar
            </a>
        </div>
    </form>

    {{-- Tabla de servicios --}}
    <div class="table-card">
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>Servicio</th>
                        <th>Proveedor</th>
                        <th>Categoría</th>
                        <th>Mercado</th>
                        <th>Tipo</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                @forelse($services as $service)
                    <tr>
                        <td>
                            <span class="service-name">{{ $service->name_service }}</span>
                            <span class="service-description">{{ \Illuminate\Support\Str::limit($service->description, 70) }}</span>
                        </td>
                        <td>{{ $service->supplier?->supplier_name ?? 'Sin proveedor' }}</td>
                        <td>{{ $service->category?->name ?? 'Sin categoría' }}</td>
                        <td>{{ $service->labels?->name_labels ?? 'Sin mercado' }}</td>
                        <td>
                            <span class="badge-pricing">
                                {{ $service->pricing_type === 'per_person' ? 'Por persona' : ($service->pricing_type === 'flat' ? 'Fijo' : $service->pricing_type) }}
                            </span>
                        </td>
                        <td>
                            @php
                                $statusClass = match($service->status) {
                                    'active' => 'badge-active',
                                    'inactive' => 'badge-inactive',
                                    'draft' => 'badge-draft',
                                    'archived' => 'badge-archived',
                                    default => 'badge-active'
                                };
                            @endphp
                            <span class="badge {{ $statusClass }}">
                                <span class="badge-dot" style="display:inline-block;width:6px;height:6px;border-radius:50%;background:currentColor;"></span>
                                {{ ucfirst($service->status ?? 'active') }}
                            </span>
                        </td>
                        <td>
                            <div class="actions-cell">
                                <a class="btn btn-sm btn-secondary" href="{{ route('admin.tariffs.index', $service->id_service) }}">
                                    <i class="ti ti-coin"></i> Tarifas
                                </a>
                                <a class="btn btn-sm btn-outline-secondary" href="{{ route('admin.services.edit', $service->id_service) }}">
                                    <i class="ti ti-edit"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7">
                            <div class="empty-state">
                                <i class="ti ti-package-off"></i>
                                <p>No hay servicios registrados.</p>
                                <p style="font-size:0.85rem;margin-top:0.3rem;">
                                    <a href="{{ route('admin.services.create') }}" style="color:#2563eb;text-decoration:none;">
                                        <i class="ti ti-plus"></i> Crea el primer servicio
                                    </a>
                                </p>
                            </div>
                        </td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>
        @if($services->hasPages())
            <div class="pagination-wrapper">
                {{ $services->links() }}
            </div>
        @endif
    </div>
</div>
@endsection