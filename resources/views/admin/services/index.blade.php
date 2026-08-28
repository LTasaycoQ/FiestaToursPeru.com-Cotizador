@extends('layouts.app')

@section('title', 'Servicios')

@section('content')
<div class="container-fluid" style="padding:2rem;">
    <div style="display:flex;justify-content:space-between;align-items:center;gap:1rem;margin-bottom:1.5rem;">
        <div>
            <h1 style="margin:0;color:#0f172a;font-size:24px;">Servicios</h1>
            <p style="margin:5px 0 0;color:#64748b;">Gestiona todas las actividades registradas.</p>
        </div>
        <div style="display:flex;gap:8px;">
            <a class="btn btn-primary" href="{{ route('admin.services.import.view') }}"><i class="ti ti-file-upload"></i> Importar Servicios</a>
            <a class="btn btn-secondary" href="{{ route('admin.services.create') }}"><i class="ti ti-plus"></i> Nuevo servicio</a>
        </div>
    </div>

    <form method="GET" class="card" style="padding:1rem;margin-bottom:1rem;display:grid;grid-template-columns:2fr 1fr 1fr 1fr auto;gap:10px;align-items:end;">
        <div><label>Buscar</label><input class="form-control" name="search" value="{{ $search }}" placeholder="Nombre o descripción"></div>
        <div><label>Proveedor</label><input class="form-control" value="Todos" readonly></div>
        <div><label>Categoría</label><select class="form-control" name="category"><option value="">Todas</option>@foreach($categories as $item)<option value="{{ $item->id_category }}" {{ (string) $category === (string) $item->id_category ? 'selected' : '' }}>{{ $item->name }}</option>@endforeach</select></div>
        <div><label>Mercado</label><select class="form-control" name="label"><option value="">Todos</option>@foreach($labels as $item)<option value="{{ $item->id_labels }}" {{ (string) $label === (string) $item->id_labels ? 'selected' : '' }}>{{ $item->name_labels }}</option>@endforeach</select></div>
        <button class="btn btn-secondary" type="submit"><i class="ti ti-search"></i> Filtrar</button>
    </form>

    <div class="card">
        <div class="table-responsive">
            <table class="table">
                <thead><tr><th>Servicio</th><th>Proveedor</th><th>Categoría</th><th>Mercado</th><th>Tipo</th><th>Estado</th><th>Acciones</th></tr></thead>
                <tbody>
                @forelse($services as $service)
                    <tr>
                        <td><strong>{{ $service->name_service }}</strong><br><small>{{ \Illuminate\Support\Str::limit($service->description, 80) }}</small></td>
                        <td>{{ $service->supplier?->supplier_name ?? 'Sin proveedor' }}</td>
                        <td>{{ $service->category?->name ?? 'Sin categoría' }}</td>
                        <td>{{ $service->labels?->name_labels ?? 'Sin mercado' }}</td>
                        <td>{{ ucfirst($service->pricing_type ?? 'flat') }}</td>
                        <td>{{ ucfirst($service->status ?? 'active') }}</td>
                        <td><a class="btn btn-sm btn-secondary" href="{{ route('admin.tariffs.index', $service->id_service) }}"><i class="ti ti-coin"></i> Tarifas</a></td>
                    </tr>
                @empty
                    <tr><td colspan="7" style="text-align:center;padding:2rem;">No hay servicios registrados.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
        <div style="padding:1rem;">{{ $services->links() }}</div>
    </div>
</div>
@endsection
