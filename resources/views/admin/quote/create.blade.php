@extends('layouts.app')
@section('title', 'Nueva Cotización')
@section('content')

@push('styles')
<style>
.qc-page { --qc-line:#e2e8f0; --qc-ink:#0f172a; --qc-muted:#94a3b8; }
.qc-card { background:#fff; border:1px solid var(--qc-line); border-radius:14px; overflow:hidden; }
.qc-header { display:flex; align-items:center; gap:14px; padding:20px 28px; border-bottom:1px solid var(--qc-line); }
.qc-header h3 { font-size:19px; font-weight:700; color:var(--qc-ink); margin:0; }
.qc-body { padding:28px; }
.form-grid { display:grid; grid-template-columns:repeat(2,1fr); gap:16px 20px; }
.form-group label { display:block; font-size:12px; font-weight:600; color:#475569; margin-bottom:6px; }
.form-control { width:100%; padding:10px 14px; border-radius:8px; border:1.5px solid var(--qc-line); font-size:13.5px; outline:none; }
.form-control:focus { border-color:#6366f1; box-shadow:0 0 0 3px rgba(99,102,241,.1); }
.form-control:disabled { background:#f1f5f9; color:#94a3b8; cursor:not-allowed; }
.form-actions { display:flex; gap:10px; margin-top:24px; }
.btn { padding:10px 20px; border-radius:8px; font-weight:600; font-size:13px; border:1px solid transparent; cursor:pointer; text-decoration:none; display:inline-flex; align-items:center; gap:6px; }
.btn-primary { background:#0f172a; color:#fff; flex:1; justify-content:center; }
.btn-primary:hover { background:#1e293b; }
.btn-secondary { background:#f1f5f9; color:#475569; border-color:var(--qc-line); }
.alert-danger { background:#fee2e2; border:1px solid #fca5a5; color:#991b1b; padding:12px 16px; border-radius:8px; margin-bottom:16px; }
.hint { font-size:11.5px; color:var(--qc-muted); margin-top:4px; display:flex; align-items:center; gap:4px; }
.hint i { font-size:14px; }
.qc-toggle { display:flex; gap:6px; background:#f1f5f9; border-radius:8px; padding:4px; margin-bottom:10px; grid-column:span 2; width:fit-content; }
.qc-toggle-btn { border:none; background:transparent; padding:7px 16px; border-radius:6px; font-size:12.5px; font-weight:600; color:#64748b; cursor:pointer; }
.qc-toggle-btn.active { background:#fff; color:#0f172a; box-shadow:0 1px 2px rgba(0,0,0,.06); }
.qc-mode-section { display:none; grid-column:span 2; }
.qc-mode-section.active { display:grid; grid-template-columns:repeat(2,1fr); gap:16px 20px; }
</style>
@endpush

<div class="container-fluid qc-page">
    <div class="qc-card">
        <div class="qc-header">
            <a href="{{ route('admin.quotes.index') }}" class="btn btn-secondary"><i class="ti ti-arrow-left"></i> Volver</a>
            <h3><i class="ti ti-file-plus" style="color:#6366f1;"></i> Nueva Cotización — {{ $quoteNumber }}</h3>
        </div>
        <div class="qc-body">

            @if($errors->any())
                <div class="alert-danger">
                    <ul style="margin:0;padding-left:1.2rem;">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('admin.quotes.store') }}" method="POST">
                @csrf
                <input type="hidden" name="id_users" value="{{ auth()->id() }}">
                <input type="hidden" name="date_mode" id="date_mode" value="{{ old('date_mode', 'dates') }}">

                <div class="form-grid">
                    <div class="form-group" style="grid-column:span 2;">
                        <label>Nombre de la Cotización</label>
                        <input type="text" class="form-control" name="name" value="{{ old('name') }}" maxlength="300" placeholder="Ej: Tour Cusco Familia Pérez">
                        <div class="hint">
                            <i class="ti ti-info-circle"></i>
                            Un nombre descriptivo ayuda a identificar la cotización fácilmente.
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Cliente</label>
                        <select class="form-control" name="id_client" id="id_client" onchange="cargarContactos()">
                            <option value="">Seleccione un cliente</option>
                            @foreach($clients as $client)
                                <option value="{{ $client->id_client }}" {{ old('id_client') == $client->id_client ? 'selected' : '' }}>{{ $client->name_client }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Contacto</label>
                        <select class="form-control" name="id_contacts" id="id_contacts">
                            <option value="">Primero seleccione un cliente</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Pasajeros</label>
                        <input type="number" class="form-control" name="passengers_count" min="1" value="{{ old('passengers_count', 1) }}">
                        <div class="hint">
                            <i class="ti ti-users"></i>
                            Número de personas para calcular tarifas.
                        </div>
                    </div>

                    <!-- Selector de modo: Fechas exactas vs. Cantidad de días -->
                    <div class="qc-toggle">
                        <button type="button" class="qc-toggle-btn" id="btn-mode-dates" onclick="setDateMode('dates')">
                            <i class="ti ti-calendar"></i> Tengo las fechas
                        </button>
                        <button type="button" class="qc-toggle-btn" id="btn-mode-days" onclick="setDateMode('days')">
                            <i class="ti ti-hash"></i> Solo cantidad de días
                        </button>
                    </div>

                    <!-- Modo 1: Rango de fechas -->
                    <div class="qc-mode-section" id="mode-section-dates">
                        <div class="form-group">
                            <label>Fecha Inicio <span class="req-dates" style="color:#991b1b">*</span></label>
                            <input type="date" class="form-control" name="start_date" id="start_date" value="{{ old('start_date') }}">
                            <div class="hint">
                                <i class="ti ti-calendar"></i>
                                Se generará un día del itinerario por cada fecha del rango.
                            </div>
                        </div>

                        <div class="form-group">
                            <label>Fecha Fin <span class="req-dates" style="color:#991b1b">*</span></label>
                            <input type="date" class="form-control" name="end_date" id="end_date" value="{{ old('end_date') }}">
                            <div class="hint">
                                <i class="ti ti-calendar"></i>
                                Debe ser igual o posterior a la fecha de inicio.
                            </div>
                        </div>
                    </div>

                    <!-- Modo 2: Solo cantidad de días, sin fecha -->
                    <div class="qc-mode-section" id="mode-section-days">
                        <div class="form-group" style="grid-column:span 2;">
                            <label>Cantidad de Días <span class="req-days" style="color:#991b1b">*</span></label>
                            <input type="number" class="form-control" name="days_count" id="days_count" min="1" max="60" value="{{ old('days_count', 1) }}">
                            <div class="hint">
                                <i class="ti ti-info-circle" style="color:#6366f1;"></i>
                                Se generará el itinerario como "Día 1", "Día 2", etc. Podrás asignarle las fechas reales más adelante, cuando el cliente las confirme.
                            </div>
                        </div>
                    </div>

                    <div class="form-group" style="grid-column:span 2;">
                        <label>Observaciones</label>
                        <textarea class="form-control" name="notes" rows="3" placeholder="Notas adicionales sobre la cotización...">{{ old('notes') }}</textarea>
                        <div class="hint">
                            <i class="ti ti-info-circle" style="color:#6366f1;"></i>
                            <strong>Importante:</strong> Los servicios se agregan después de crear la cotización.
                            Los hoteles se asignan por <strong>día específico</strong> (opción 1 y opción 2).
                        </div>
                    </div>
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">
                        <i class="ti ti-arrow-right"></i> Crear cotización
                    </button>
                    <a href="{{ route('admin.quotes.index') }}" class="btn btn-secondary">Cancelar</a>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
function setDateMode(mode) {
    document.getElementById('date_mode').value = mode;

    var btnDates = document.getElementById('btn-mode-dates');
    var btnDays = document.getElementById('btn-mode-days');
    var secDates = document.getElementById('mode-section-dates');
    var secDays = document.getElementById('mode-section-days');
    var startDate = document.getElementById('start_date');
    var endDate = document.getElementById('end_date');
    var daysCount = document.getElementById('days_count');

    if (mode === 'dates') {
        btnDates.classList.add('active');
        btnDays.classList.remove('active');
        secDates.classList.add('active');
        secDays.classList.remove('active');

        startDate.disabled = false;
        endDate.disabled = false;
        daysCount.disabled = true;
    } else {
        btnDays.classList.add('active');
        btnDates.classList.remove('active');
        secDays.classList.add('active');
        secDates.classList.remove('active');

        startDate.disabled = true;
        endDate.disabled = true;
        daysCount.disabled = false;
    }
}

function cargarContactos() {
    var clientId = document.getElementById('id_client').value;
    var contactsSelect = document.getElementById('id_contacts');
    contactsSelect.innerHTML = '<option value="">Cargando...</option>';

    if (!clientId) {
        contactsSelect.innerHTML = '<option value="">Primero seleccione un cliente</option>';
        return;
    }

    fetch('/cotizaciones/get-contacts-by-client/' + clientId)
        .then(r => r.json())
        .then(data => {
            contactsSelect.innerHTML = '<option value="">Seleccione un contacto</option>';
            (data.data || []).forEach(function(c) {
                var opt = document.createElement('option');
                opt.value = c.id;
                opt.text = c.name + ' ' + (c.last_names || '');
                contactsSelect.appendChild(opt);
            });
        })
        .catch(() => { contactsSelect.innerHTML = '<option value="">Error al cargar</option>'; });
}

document.addEventListener('DOMContentLoaded', function() {
    // Restaurar el modo previo (si hubo error de validación) o dejar 'dates' por defecto
    var initialMode = document.getElementById('date_mode').value || 'dates';
    setDateMode(initialMode);

    // Preseleccionar contacto si hay un valor old
    var oldContact = '{{ old('id_contacts') }}';
    if (oldContact) {
        var clientId = document.getElementById('id_client').value;
        if (clientId) {
            cargarContactos();
            setTimeout(function() {
                var contactsSelect = document.getElementById('id_contacts');
                for (var i = 0; i < contactsSelect.options.length; i++) {
                    if (contactsSelect.options[i].value == oldContact) {
                        contactsSelect.selectedIndex = i;
                        break;
                    }
                }
            }, 300);
        }
    }
});
</script>
@endpush

@endsection