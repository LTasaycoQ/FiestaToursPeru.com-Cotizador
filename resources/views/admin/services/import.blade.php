@extends('layouts.app')

@section('title', 'Importar actividades')

@section('content')
<div class="container" style="max-width:760px;margin:2rem auto;">
    <div class="card" style="padding:2rem;">
        <h2 style="margin-bottom:.5rem;">Importar actividades</h2>
        <p style="color:#64748b;">Sube un Excel con el formato de cotización. Solo se importarán los nombres de actividad.</p>

        @if($errors->any())
            <div class="alert alert-danger" style="margin:1rem 0;">
                @foreach($errors->all() as $error)
                    <div>{{ $error }}</div>
                @endforeach
            </div>
        @endif

        <div style="padding:1rem;background:#f8fafc;border:1px solid #e2e8f0;border-radius:10px;margin:1rem 0;">
            <strong>Columnas aceptadas</strong>
            <div style="margin-top:.5rem;color:#475569;font-size:13px;">
                Cada fila debe tener <strong>Proveedor</strong>, <strong>Categoría</strong> y el nombre en <strong>Traslados Tours y Paquetes</strong>.
                Las tarifas se tomarán de Regular Económico, Regular VIP y Servicios Privados.
            </div>
            <div style="margin-top:.5rem;color:#64748b;font-size:12px;">
                Día y tipo no se guardan. Proveedor y categoría se leen desde cada fila del Excel.
            </div>
        </div>

        <form action="{{ route('admin.services.import') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <label for="archivo" style="display:block;margin-top:1rem;">Archivo Excel</label>
            <input type="file" name="archivo" accept=".xlsx,.xls" required class="form-control">
            <div style="display:flex;gap:10px;margin-top:1.25rem;">
                <button class="btn btn-primary" type="submit"><i class="ti ti-upload"></i> Importar actividades</button>
                <a class="btn btn-secondary" href="{{ route('admin.services.template') }}"><i class="ti ti-file-download"></i> Descargar formato Excel</a>
                <a class="btn btn-secondary" href="{{ route('admin.services.index') }}">Volver</a>
            </div>
        </form>
    </div>
</div>
@endsection
