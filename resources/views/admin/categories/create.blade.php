@extends('layouts.app')
@section('title', 'Nueva Categoría')
@section('content')

<div class="page-header">
    <div class="page-title">Nueva Categoría</div>
    <div class="page-sub">Agrega una categoría de proveedor</div>
</div>

<div style="max-width:500px">
    @if($errors->any())
        <div class="alert alert-error">
            <i class="ti ti-alert-circle"></i>
            <ul style="list-style:none;margin-left:.5rem">
                @foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach
            </ul>
        </div>
    @endif

    <div class="card">
        <div class="card-header">
            <div>
                <div class="card-title">Datos de la categoría</div>
                <div class="card-sub">Máximo 100 caracteres</div>
            </div>
            <a href="{{ route('admin.categories.index') }}" class="btn btn-secondary btn-sm">
                <i class="ti ti-arrow-left" style="font-size:13px"></i> Volver
            </a>
        </div>
        <form action="{{ route('admin.categories.store') }}" method="POST">
            @csrf
            <div class="form-field" style="margin-bottom:1.4rem">
                <label>Nombre de la categoría *</label>
                <input type="text" name="category_name"
                       value="{{ old('category_name') }}"
                       placeholder="Ej: Hoteles, Aerolíneas, Transporte..."
                       maxlength="100" required autofocus>
            </div>
            <div style="display:flex;gap:.8rem;padding-top:1rem;border-top:1px solid #f1f5f9">
                <button type="submit" class="btn btn-primary">
                    <i class="ti ti-plus" style="font-size:14px"></i> Crear categoría
                </button>
                <a href="{{ route('admin.categories.index') }}" class="btn btn-secondary">Cancelar</a>
            </div>
        </form>
    </div>
</div>
@endsection
