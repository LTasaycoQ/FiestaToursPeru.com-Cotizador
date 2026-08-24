@extends('layouts.app')
@section('title', 'Categorías de Proveedores')
@section('content')

<div class="page-header" style="display:flex;justify-content:space-between;align-items:flex-start;margin-bottom:1.6rem">
    <div>
        <div class="page-title">Categorías de Proveedores</div>
        <div class="page-sub">Gestiona las categorías disponibles</div>
    </div>
    <a href="{{ route('admin.categories.create') }}" class="btn btn-primary">
        <i class="ti ti-plus" style="font-size:15px"></i> Nueva categoría
    </a>
</div>

@if($categories->isEmpty())
    <div style="text-align:center;padding:4rem;background:#fff;border-radius:14px;border:1px solid #e2e8f0">
        <i class="ti ti-tag-off" style="font-size:48px;color:#cbd5e1;display:block;margin-bottom:1rem"></i>
        <p style="font-size:15px;font-weight:600;color:#0f172a;margin-bottom:.4rem">No hay categorías aún</p>
        <p style="font-size:13px;color:#94a3b8;margin-bottom:1.2rem">Comienza creando tu primera categoría</p>
        <a href="{{ route('admin.categories.create') }}" class="btn btn-primary btn-sm">
            <i class="ti ti-plus"></i> Crear categoría
        </a>
    </div>
@else
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nombre de categoría</th>
                    <th>Registro</th>
                    <th style="text-align:center">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @foreach($categories as $c)
                <tr>
                    <td style="color:#cbd5e1;font-size:12px;font-weight:600">#{{ $c->id_categories_suppliers }}</td>
                    <td>
                        <div style="display:flex;align-items:center;gap:10px">
                            <div class="avatar-sm" style="background:#ede9fe;color:#6d28d9">
                                <i class="ti ti-tag" style="font-size:14px"></i>
                            </div>
                            <span style="font-weight:600">{{ $c->category_name }}</span>
                        </div>
                    </td>
                    <td style="color:#94a3b8;font-size:12px">{{ $c->created_at->format('d/m/Y') }}</td>
                    <td>
                        <div style="display:flex;justify-content:center;gap:6px">
                            <a href="{{ route('admin.categories.edit', $c->id_categories_suppliers) }}"
                               class="btn btn-secondary btn-sm">
                                <i class="ti ti-edit" style="font-size:13px"></i> Editar
                            </a>
                            <form action="{{ route('admin.categories.destroy', $c->id_categories_suppliers) }}"
                                  method="POST"
                                  onsubmit="return confirm('¿Eliminar categoría {{ addslashes($c->category_name) }}?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-sm">
                                    <i class="ti ti-trash" style="font-size:13px"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        <div class="table-footer">Total: {{ $categories->count() }} categoría(s)</div>
    </div>
@endif
@endsection
