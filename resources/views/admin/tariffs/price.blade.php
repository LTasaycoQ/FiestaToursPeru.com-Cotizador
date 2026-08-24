@extends('layouts.app')

@section('title', 'Gestionar Precios - ' . $service->name_service)

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0">Gestionar Precios</h1>
            <p class="text-muted mb-0">
                <strong>Servicio:</strong> {{ $service->name_service }}
                <span class="mx-2">|</span>
                <strong>Proveedor:</strong> {{ $service->supplier->supplier_name ?? 'N/A' }}
            </p>
        </div>
        <div>
            <a href="{{ route('admin.tariffs.index', $service->id_service) }}" class="btn btn-outline-secondary">
                <i class="ti ti-arrow-left"></i> Volver a Tarifas
            </a>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h5 class="mb-0"><i class="ti ti-coin"></i> Asignar Precios a Tarifas</h5>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.tariffs.updatePrice', ['service' => $service->id_service]) }}" method="POST">
                @csrf
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Subcategoría</th>
                                <th>Mín. Personas</th>
                                <th>Máx. Personas</th>
                                <th>Precio (S/)</th>
                                <th>Estado</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($tariffs as $tariff)
                                <tr>
                                    <td>
                                        <strong>{{ $tariff->subcategory->name ?? 'Sin subcategoría' }}</strong>
                                        <input type="hidden" name="tariffs[{{ $loop->index }}][id]" value="{{ $tariff->id_tariff }}">
                                    </td>
                                    <td>
                                        <input type="number" name="tariffs[{{ $loop->index }}][min_people_count]" 
                                               class="form-control form-control-sm" style="width: 80px;"
                                               value="{{ $tariff->min_people_count }}" min="1">
                                    </td>
                                    <td>
                                        <input type="number" name="tariffs[{{ $loop->index }}][max_people_count]" 
                                               class="form-control form-control-sm" style="width: 80px;"
                                               value="{{ $tariff->max_people_count }}" min="1">
                                    </td>
                                    <td>
                                        <input type="number" name="tariffs[{{ $loop->index }}][price]" 
                                               class="form-control form-control-sm" style="width: 120px;"
                                               value="{{ $tariff->price }}" step="0.01" min="0" required>
                                    </td>
                                    <td>
                                        <select name="tariffs[{{ $loop->index }}][status]" class="form-select form-select-sm" style="width: 120px;">
                                            <option value="active" {{ $tariff->status === 'active' ? 'selected' : '' }}>Activa</option>
                                            <option value="pending" {{ $tariff->status === 'pending' ? 'selected' : '' }}>Pendiente</option>
                                            <option value="inactive" {{ $tariff->status === 'inactive' ? 'selected' : '' }}>Inactiva</option>
                                        </select>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="mt-3">
                    <button type="submit" class="btn btn-primary">
                        <i class="ti ti-device-floppy"></i> Guardar Todos los Precios
                    </button>
                    <a href="{{ route('admin.tariffs.index', $service->id_service) }}" class="btn btn-secondary">
                        Cancelar
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection