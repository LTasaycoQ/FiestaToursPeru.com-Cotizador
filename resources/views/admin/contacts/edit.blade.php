@extends('layouts.app')
@section('title', 'Editar Contacto')
@section('content')

<div class="page-header">
    <div class="page-title">Editar Contacto</div>
    <div class="page-sub">Modifica los datos del contacto</div>
</div>

<div style="max-width:700px">
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
                <div class="card-title">{{ $contact->name }} {{ $contact->last_names }}</div>
                <div class="card-sub">ID #{{ $contact->id_contacts }}</div>
            </div>
            <a href="{{ route('admin.contacts.index') }}" class="btn btn-secondary btn-sm">
                <i class="ti ti-arrow-left" style="font-size:13px"></i> Volver
            </a>
        </div>

        <form action="{{ route('admin.contacts.update', $contact->id_contacts) }}" method="POST">
            @csrf @method('PUT')

            <p style="font-size:11px;font-weight:700;color:#94a3b8;text-transform:uppercase;
                      letter-spacing:.5px;margin-bottom:.8rem;display:flex;align-items:center;gap:6px">
                <i class="ti ti-user" style="font-size:14px"></i> Información personal
            </p>
            <div class="form-grid" style="margin-bottom:1.4rem">
                <div class="form-field">
                    <label>Nombre *</label>
                    <input type="text" name="name"
                           value="{{ old('name', $contact->name) }}"
                           maxlength="100" required>
                </div>
                <div class="form-field">
                    <label>Apellidos</label>
                    <input type="text" name="last_names"
                           value="{{ old('last_names', $contact->last_names) }}"
                           maxlength="100">
                </div>
                <div class="form-field">
                    <label>Fecha de nacimiento</label>
                    <input type="date" name="Date_of_birth"
                           value="{{ old('Date_of_birth', $contact->Date_of_birth) }}">
                </div>
                <div class="form-field">
                    <label>Cargo / Calificación</label>
                    <input type="text" name="qualification"
                           value="{{ old('qualification', $contact->qualification) }}"
                           maxlength="30">
                </div>
            </div>

            <p style="font-size:11px;font-weight:700;color:#94a3b8;text-transform:uppercase;
                      letter-spacing:.5px;margin-bottom:.8rem;display:flex;align-items:center;gap:6px">
                <i class="ti ti-phone" style="font-size:14px"></i> Información de contacto
            </p>
            <div class="form-grid" style="margin-bottom:1.4rem">
                <div class="form-field">
                    <label>Email</label>
                    <input type="email" name="email"
                           value="{{ old('email', $contact->email) }}"
                           maxlength="80">
                </div>
                <div class="form-field" style="grid-column:1">
                    <label>Teléfono principal</label>
                    <input type="text" name="first_phone"
                           value="{{ old('first_phone', $contact->first_phone) }}"
                           maxlength="20">
                </div>
                <div class="form-field">
                    <label>Teléfono secundario</label>
                    <input type="text" name="second_phone"
                           value="{{ old('second_phone', $contact->second_phone) }}"
                           maxlength="20">
                </div>
            </div>

            <p style="font-size:11px;font-weight:700;color:#94a3b8;text-transform:uppercase;
                      letter-spacing:.5px;margin-bottom:.8rem;display:flex;align-items:center;gap:6px">
                <i class="ti ti-link" style="font-size:14px"></i> Asociaciones
            </p>
            <div class="form-grid" style="margin-bottom:1.4rem">
                <div class="form-field">
                    <label>Cliente asociado</label>
                    <select name="id_client">
                        <option value="">— Sin cliente —</option>
                        @foreach($clients as $cl)
                            <option value="{{ $cl->id_client }}"
                                {{ old('id_client', $contact->id_client) == $cl->id_client ? 'selected' : '' }}>
                                {{ $cl->name_client }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="form-field">
                    <label>Proveedor asociado</label>
                    <select name="id_supplier">
                        <option value="">— Sin proveedor —</option>
                        @foreach($suppliers as $s)
                            <option value="{{ $s->id_supplier }}"
                                {{ old('id_supplier', $contact->id_supplier) == $s->id_supplier ? 'selected' : '' }}>
                                {{ $s->supplier_name }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div style="display:flex;align-items:center;gap:10px;margin-bottom:1.4rem;
                        padding:.9rem 1.1rem;background:#fffbeb;border:1px solid #fde68a;
                        border-radius:9px;cursor:pointer"
                 onclick="document.getElementById('es_principal').click()">
                <input type="hidden" name="es_principal" value="0">
                <input type="checkbox" name="es_principal" id="es_principal" value="1"
                       {{ old('es_principal', $contact->es_principal) ? 'checked' : '' }}
                       style="width:16px;height:16px;cursor:pointer;flex-shrink:0">
                <div>
                    <div style="font-size:13px;font-weight:600;color:#92400e">
                        <i class="ti ti-star" style="font-size:14px;vertical-align:middle"></i>
                        Marcar como contacto principal
                    </div>
                    <div style="font-size:11px;color:#b45309;margin-top:1px">
                        Solo puede haber un contacto principal por cliente
                    </div>
                </div>
            </div>

            <div style="display:flex;gap:.8rem;padding-top:1rem;border-top:1px solid #f1f5f9">
                <button type="submit" class="btn btn-primary">
                    <i class="ti ti-device-floppy" style="font-size:14px"></i> Guardar cambios
                </button>
                <a href="{{ route('admin.contacts.index') }}" class="btn btn-secondary">Cancelar</a>
            </div>
        </form>
    </div>
</div>
@endsection
