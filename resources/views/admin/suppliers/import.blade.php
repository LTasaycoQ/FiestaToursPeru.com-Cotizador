@extends('layouts.app')
@section('title', 'Importar Proveedores')

@section('content')

<div class="page-header" style="display:flex;justify-content:space-between;align-items:center;margin-bottom:1.4rem">
    <div>
        <div class="page-title">Importar Proveedores</div>
        <div class="page-sub">Carga un archivo Excel o CSV con proveedores</div>
    </div>
    <a href="{{ route('admin.suppliers.index') }}" class="btn btn-secondary btn-sm">
        <i class="ti ti-arrow-left" style="font-size:13px"></i> Volver
    </a>
</div>

@if($errors->any())
    <div class="alert alert-error" style="margin-bottom:1rem;padding:1rem;border-radius:10px;background:#fef2f2;border:1px solid #fecaca;color:#991b1b">
        <i class="ti ti-alert-circle"></i>
        <ul style="list-style:none;margin-left:.5rem;padding:0">
            @foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach
        </ul>
    </div>
@endif

<div class="card" style="max-width:600px;margin:0 auto;padding:2rem;text-align:center">
    <div style="font-size:48px;color:#6366f1;margin-bottom:1rem">
        <i class="ti ti-file-upload"></i>
    </div>
    <h3 style="font-size:18px;font-weight:700;color:#0f172a;margin-bottom:.5rem">Subir archivo de proveedores</h3>
    <p style="font-size:13px;color:#94a3b8;margin-bottom:1.5rem">
        Formatos aceptados: <strong>.xlsx, .xls, .csv</strong> (máx. 5MB)
    </p>

    <form action="{{ route('admin.suppliers.import') }}" method="POST" enctype="multipart/form-data">
        @csrf

        <div style="border:2px dashed #e2e8f0;border-radius:12px;padding:2rem;margin-bottom:1.5rem;transition:all .2s;background:#f8fafc" id="drop-zone">
            <div style="font-size:32px;color:#cbd5e1;margin-bottom:.5rem">
                <i class="ti ti-file"></i>
            </div>
            <p style="font-size:13px;color:#64748b;margin-bottom:.3rem">Arrastra tu archivo aquí o haz clic para seleccionar</p>
            <input type="file" name="archivo" id="archivo" style="display:none" accept=".xlsx,.xls,.csv" required>
            <button type="button" class="btn btn-secondary" onclick="document.getElementById('archivo').click()" style="margin-top:.5rem">
                <i class="ti ti-folder-open" style="font-size:14px"></i> Seleccionar archivo
            </button>
            <p id="file-name" style="font-size:12px;color:#6366f1;margin-top:.5rem;display:none"></p>
        </div>

        <div style="display:flex;gap:.8rem;justify-content:center">
            <button type="submit" class="btn btn-primary">
                <i class="ti ti-upload" style="font-size:14px"></i> Importar
            </button>
            <a href="{{ route('admin.suppliers.template') }}" class="btn btn-secondary" style="text-decoration:none">
                <i class="ti ti-file-download" style="font-size:14px"></i> Descargar plantilla
            </a>
        </div>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const dropZone = document.getElementById('drop-zone');
    const fileInput = document.getElementById('archivo');
    const fileName = document.getElementById('file-name');

    dropZone.addEventListener('click', function(e) {
        if (e.target.tagName !== 'BUTTON' && e.target.closest('button') === null) {
            fileInput.click();
        }
    });

    dropZone.addEventListener('dragover', function(e) {
        e.preventDefault();
        dropZone.style.borderColor = '#6366f1';
        dropZone.style.background = '#eef2ff';
    });

    dropZone.addEventListener('dragleave', function(e) {
        e.preventDefault();
        dropZone.style.borderColor = '#e2e8f0';
        dropZone.style.background = '#f8fafc';
    });

    dropZone.addEventListener('drop', function(e) {
        e.preventDefault();
        dropZone.style.borderColor = '#e2e8f0';
        dropZone.style.background = '#f8fafc';
        if (e.dataTransfer.files.length) {
            fileInput.files = e.dataTransfer.files;
            updateFileName();
        }
    });

    fileInput.addEventListener('change', updateFileName);

    function updateFileName() {
        if (fileInput.files.length) {
            fileName.textContent = '📄 ' + fileInput.files[0].name;
            fileName.style.display = 'block';
            dropZone.style.borderColor = '#16a34a';
            dropZone.style.background = '#f0fdf4';
        } else {
            fileName.style.display = 'none';
            dropZone.style.borderColor = '#e2e8f0';
            dropZone.style.background = '#f8fafc';
        }
    }
});
</script>

<style>
#drop-zone {
    cursor: pointer;
    transition: all .2s;
}
#drop-zone:hover {
    border-color: #6366f1;
    background: #f1f5f9;
}
</style>

@endsection