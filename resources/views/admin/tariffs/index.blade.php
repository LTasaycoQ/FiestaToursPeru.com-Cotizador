@extends('layouts.app')

@section('title', 'Tarifas - ' . $service->name_service)

@section('content')
@section('pageContentClass', 'no-padding')
<style>
    .tariffs-wrapper {
        min-height: calc(100vh - 60px);
        background: #f8fafc;
    }

    .tariffs-header {
        background: #fff;
        border-bottom: 1px solid #e2e8f0;
        padding: 1.2rem 2rem;
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 1rem;
    }

    .tariffs-header .avatar-large {
        width: 56px;
        height: 56px;
        border-radius: 50%;
        background: linear-gradient(135deg, #0f172a, #1e293b);
        color: #fff;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 20px;
        font-weight: 700;
        flex-shrink: 0;
    }

    .tariffs-header .header-identity {
        display: flex;
        align-items: center;
        gap: 1rem;
        flex: 1;
        min-width: 260px;
    }

    .tariffs-header .header-title h1 {
        font-size: 20px;
        font-weight: 700;
        color: #0f172a;
        margin: 0;
    }

    .tariffs-header .header-title p {
        font-size: 13px;
        color: #94a3b8;
        margin: 4px 0 0;
    }

    .tariffs-header .header-actions {
        display: flex;
        gap: 8px;
        flex-wrap: wrap;
    }

    .tariffs-header .header-actions .btn {
        padding: 7px 16px;
        border-radius: 8px;
        font-size: 13px;
        font-weight: 600;
        text-decoration: none;
        transition: all 0.15s;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        border: none;
        cursor: pointer;
    }

    .tariffs-header .header-actions .btn-secondary {
        background: #f1f5f9;
        color: #475569;
        border: 1px solid #e2e8f0;
    }

    .tariffs-header .header-actions .btn-secondary:hover {
        background: #e8edf4;
    }

    .tariffs-header .header-actions .btn-primary {
        background: #0f172a;
        color: #fff;
    }

    .tariffs-header .header-actions .btn-primary:hover {
        background: #1e293b;
    }

    .availability-panel {
        margin-top: 30px;
        margin-bottom: 1.5rem;
        padding: 1rem 1.25rem;
        border-radius: 10px;
    }

    .availability-panel-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 1rem;
        margin-bottom: 0.8rem;
    }

    .availability-panel-header strong {
        color: #0f172a;
        font-size: 13px;
    }

    .availability-panel-header span {
        color: #64748b;
        font-size: 12px;
    }

    .availability-days {
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
    }

    .availability-day {
        position: relative;
    }

    .availability-day input {
        position: absolute;
        opacity: 0;
        pointer-events: none;
    }

    .availability-day label {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-width: 76px;
        padding: 7px 10px;
        border: 1px solid #cbd5e1;
        border-radius: 7px;
        background: #fff;
        color: #475569;
        cursor: pointer;
        font-size: 12px;
        transition: all 0.15s;
    }

    .availability-day input:checked + label {
        border-color: #0f172a;
        background: #0f172a;
        color: #fff;
    }

    .availability-save {
        margin-top: 0.8rem;
        padding: 7px 14px;
        border: 0;
        border-radius: 7px;
        background: #0f172a;
        color: #fff;
        cursor: pointer;
        font-size: 12px;
        font-weight: 600;
    }

    .availability-save:disabled {
        opacity: 0.6;
        cursor: wait;
    }

    .availability-feedback {
        margin-left: 8px;
        color: #166534;
        font-size: 12px;
    }

    .card {
        background: #fff;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        overflow: hidden;
    }

    .tariff-content-nav {
        display: flex;
        gap: 8px;
        background: #fff;
        border-bottom: 1px solid #e2e8f0;
        padding: 0 2rem;
    }

    .tariff-content-nav button {
        border: 0;
        background: transparent;
        color: #64748b;
        padding: 12px 18px;
        font-weight: 600;
        cursor: pointer;
        border-bottom: 2px solid transparent;
    }

    .tariff-content-nav button.active {
        color: #0f172a;
        border-bottom-color: #0f172a;
    }

    .tariff-tab-panel {
        display: none;
        padding: 1.8rem 2rem;
    }

    .tariff-tab-panel.active {
        display: block;
    }

    .card-header {
        padding: 1rem 1.5rem;
        border-bottom: 1px solid #e2e8f0;
        background: #f8fafc;
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 0.5rem;
    }

    .card-header h5 {
        font-size: 14px;
        font-weight: 600;
        color: #0f172a;
        margin: 0;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .card-body {
        padding: 1.5rem;
    }

    .alert {
        padding: 12px 16px;
        border-radius: 8px;
        margin-bottom: 1rem;
        font-size: 13px;
    }

    .alert-success {
        background: #dcfce7;
        color: #166534;
        border: 1px solid #86efac;
    }

    .alert-success .btn-close {
        float: right;
        background: none;
        border: none;
        font-size: 18px;
        cursor: pointer;
        color: #166534;
    }

    .table-responsive {
        overflow-x: auto;
    }

    .table {
        width: 100%;
        border-collapse: collapse;
        font-size: 13px;
    }

    .table thead {
        background: #f8fafc;
        border-bottom: 1px solid #e2e8f0;
    }

    .table th {
        padding: 10px 14px;
        text-align: left;
        font-weight: 600;
        color: #475569;
        font-size: 11px;
        text-transform: uppercase;
        letter-spacing: 0.4px;
    }

    .table td {
        padding: 10px 14px;
        border-bottom: 1px solid #f1f5f9;
        color: #0f172a;
        vertical-align: middle;
    }

    .table tr:last-child td {
        border-bottom: none;
    }

    .table tr:hover td {
        background: #fafbfc;
    }

    .badge {
        padding: 3px 12px;
        border-radius: 20px;
        font-size: 11px;
        font-weight: 500;
        display: inline-flex;
        align-items: center;
        gap: 4px;
    }

    .badge-info {
        background: #e0f2fe;
        color: #0369a1;
    }

    .badge-success {
        background: #dcfce7;
        color: #166534;
    }

    .badge-warning {
        background: #fef3c7;
        color: #92400e;
    }

    .badge-danger {
        background: #fee2e2;
        color: #991b1b;
    }

    .badge-secondary {
        background: #f1f5f9;
        color: #475569;
    }

    .badge-pending {
        background: #fef3c7;
        color: #92400e;
        padding: 3px 10px;
        border-radius: 20px;
        font-size: 11px;
        font-weight: 600;
        display: inline-block;
    }

    .badge-level {
        background: #ede9fe;
        color: #6d28d9;
        padding: 3px 10px;
        border-radius: 6px;
        font-size: 12px;
        font-weight: 600;
        display: inline-block;
        white-space: nowrap;
    }

    .badge-level.flat-badge {
        background: #dbeafe;
        color: #1e40af;
    }

    .badge-status {
        padding: 3px 10px;
        border-radius: 20px;
        font-size: 11px;
        font-weight: 600;
        display: inline-block;
    }

    .badge-status.active {
        background: #dcfce7;
        color: #166534;
    }

    .badge-status.inactive {
        background: #f1f5f9;
        color: #64748b;
    }

    .ranges-list {
        display: flex;
        flex-wrap: wrap;
        gap: 4px;
    }

    .ranges-list .range-item {
        background: #f1f5f9;
        padding: 2px 10px;
        border-radius: 4px;
        font-size: 11px;
        color: #475569;
        display: inline-flex;
        align-items: center;
        gap: 4px;
    }

    .ranges-list .range-item .price {
        font-weight: 600;
        color: #0f172a;
    }

    .btn-group {
        display: flex;
        gap: 4px;
        justify-content: center;
    }

    .btn-group .btn {
        padding: 5px 10px;
        border-radius: 6px;
        font-size: 13px;
        border: none;
        cursor: pointer;
        transition: all 0.15s;
        display: inline-flex;
        align-items: center;
        gap: 4px;
        text-decoration: none;
    }

    .btn-group .btn-outline-primary {
        background: #e8edf4;
        color: #475569;
        border: 1px solid #e2e8f0;
    }

    .btn-group .btn-outline-primary:hover {
        background: #d1d9e6;
    }

    .btn-group .btn-outline-danger {
        background: #fee2e2;
        color: #991b1b;
        border: 1px solid #fecaca;
    }

    .btn-group .btn-outline-danger:hover {
        background: #fecaca;
    }

    .empty-state {
        text-align: center;
        padding: 3rem 1.5rem;
        color: #94a3b8;
    }

    .empty-state i {
        font-size: 48px;
        display: block;
        margin-bottom: 12px;
        color: #cbd5e1;
    }

    .empty-state p {
        font-size: 15px;
        font-weight: 600;
        color: #0f172a;
        margin-bottom: 4px;
    }

    .empty-state span {
        font-size: 13px;
        color: #94a3b8;
    }

    .empty-state .btn {
        margin-top: 12px;
        padding: 8px 20px;
        background: #0f172a;
        color: #fff;
        border: none;
        border-radius: 8px;
        font-size: 13px;
        font-weight: 500;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        transition: all 0.15s;
        text-decoration: none;
    }

    .empty-state .btn:hover {
        background: #1e293b;
    }

    .pagination-wrapper {
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 0.75rem;
        padding-top: 1rem;
        margin-top: 0.5rem;
        border-top: 1px solid #f1f5f9;
    }

    .pagination-wrapper .info-text {
        font-size: 13px;
        color: #94a3b8;
    }

    .pagination-wrapper .pagination-links {
        display: flex;
        align-items: center;
        gap: 0.3rem;
    }

    .pagination-wrapper .pagination-links a,
    .pagination-wrapper .pagination-links span {
        padding: 0.3rem 0.7rem;
        border-radius: 6px;
        font-size: 13px;
        text-decoration: none;
        min-width: 32px;
        text-align: center;
        transition: all 0.15s;
    }

    .pagination-wrapper .pagination-links .page-link {
        border: 1px solid #e2e8f0;
        color: #475569;
        background: #fff;
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
    }

    .pagination-wrapper .pagination-links .page-disabled {
        border: 1px solid #e2e8f0;
        color: #cbd5e1;
        cursor: default;
        background: #fff;
    }

    .pagination-wrapper .pagination-links .page-dots {
        color: #cbd5e1;
        padding: 0.3rem 0.1rem;
        border: none;
    }

    /* ===== MODAL ===== */
    .modal-overlay {
        display: none;
        position: fixed;
        inset: 0;
        background: rgba(0, 0, 0, 0.5);
        backdrop-filter: blur(4px);
        z-index: 99999;
        align-items: center;
        justify-content: center;
        padding: 1.5rem;
    }

    .modal-overlay.show {
        display: flex;
    }

    .modal-overlay .modal-box {
        background: #fff;
        border-radius: 14px;
        max-width: 520px;
        width: 100%;
        padding: 1.8rem 2rem 2rem;
        position: relative;
        animation: fadeSlide 0.25s ease;
        max-height: 90vh;
        overflow-y: auto;
    }

    .modal-overlay .modal-box .modal-close-btn {
        position: absolute;
        top: 1rem;
        right: 1.2rem;
        background: none;
        border: none;
        font-size: 22px;
        color: #94a3b8;
        cursor: pointer;
        transition: transform 0.2s;
    }

    .modal-overlay .modal-box .modal-close-btn:hover {
        transform: rotate(90deg);
    }

    .modal-overlay .modal-box h3 {
        font-size: 18px;
        font-weight: 600;
        color: #0f172a;
        margin-bottom: 4px;
    }

    .modal-overlay .modal-box .modal-sub {
        color: #94a3b8;
        font-size: 13px;
        margin-bottom: 1.5rem;
    }

    .modal-overlay .modal-box .form-group {
        margin-bottom: 1rem;
    }

    .modal-overlay .modal-box .form-group label {
        display: block;
        font-size: 11px;
        font-weight: 600;
        color: #475569;
        text-transform: uppercase;
        letter-spacing: 0.4px;
        margin-bottom: 4px;
    }

    .modal-overlay .modal-box .form-group label .required {
        color: #991b1b;
    }

    .modal-overlay .modal-box .form-group select {
        width: 100%;
        padding: 9px 12px;
        border: 1.5px solid #e2e8f0;
        border-radius: 8px;
        font-size: 13px;
        outline: none;
        background: #fff;
        transition: border-color 0.15s;
    }

    .modal-overlay .modal-box .form-group select:focus {
        border-color: #0f172a;
    }

    .modal-overlay .modal-box .form-group select[multiple] {
        height: 120px;
    }

    .subcategory-selector {
        display: flex;
        gap: 8px;
        align-items: stretch;
    }

    .subcategory-selector select {
        flex: 1;
    }

    .btn-add-subcategory {
        width: 38px;
        border: 1px solid #cbd5e1;
        border-radius: 8px;
        background: #f1f5f9;
        color: #0f172a;
        cursor: pointer;
        font-size: 18px;
        transition: all 0.15s;
    }

    .btn-add-subcategory:hover {
        background: #e2e8f0;
    }

    .modal-overlay .modal-box .form-group small {
        display: block;
        font-size: 11px;
        color: #94a3b8;
        margin-top: 4px;
    }

    .modal-overlay .modal-box .form-actions {
        display: flex;
        gap: 8px;
        margin-top: 1.2rem;
    }

    .modal-overlay .modal-box .form-actions .btn-submit {
        flex: 1;
        padding: 10px;
        background: #0f172a;
        color: #fff;
        border: none;
        border-radius: 8px;
        font-size: 14px;
        font-weight: 500;
        cursor: pointer;
        transition: all 0.15s;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 6px;
    }

    .modal-overlay .modal-box .form-actions .btn-submit:hover {
        background: #1e293b;
    }

    .modal-overlay .modal-box .form-actions .btn-submit:disabled {
        opacity: 0.6;
        cursor: not-allowed;
    }

    .modal-overlay .modal-box .form-actions .btn-cancel {
        padding: 10px 20px;
        background: #f1f5f9;
        color: #475569;
        border: none;
        border-radius: 8px;
        font-size: 14px;
        font-weight: 500;
        cursor: pointer;
        transition: all 0.15s;
    }

    .modal-overlay .modal-box .form-actions .btn-cancel:hover {
        background: #e2e8f0;
    }

    .modal-overlay .modal-box .form-message {
        margin-top: 12px;
        padding: 10px 14px;
        border-radius: 6px;
        font-size: 13px;
        display: none;
    }

    .modal-overlay .modal-box .form-message.success {
        display: block;
        background: #dcfce7;
        color: #166534;
        border: 1px solid #86efac;
    }

    .modal-overlay .modal-box .form-message.error {
        display: block;
        background: #fee2e2;
        color: #991b1b;
        border: 1px solid #fca5a5;
    }

    @keyframes fadeSlide {
        from { opacity: 0; transform: translateY(6px); }
        to { opacity: 1; transform: translateY(0); }
    }

    @media (max-width: 768px) {
        .tariffs-header,
        .tariff-content-nav,
        .tariff-tab-panel { padding-left: 1rem; padding-right: 1rem; }
        .tariffs-header { flex-direction: column; align-items: stretch; }
        .tariffs-header .header-actions { justify-content: flex-end; }
        .modal-overlay .modal-box { max-width: 100%; margin: 1rem; padding: 1.5rem 1.2rem; }
        .pagination-wrapper { flex-direction: column; align-items: stretch; gap: 0.8rem; }
        .pagination-wrapper .pagination-links { justify-content: center; }
    }

    @media (max-width: 600px) {
        .table thead { display: none; }
        .table tbody tr {
            display: block;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            margin-bottom: 10px;
            background: #fff;
            padding: 8px 0;
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
        .ranges-list .range-item { font-size: 10px; padding: 2px 6px; }
    }

    /* ===== INTERACTIVIDAD AJAX ===== */
    .card.is-refreshing {
        opacity: 0.55;
        pointer-events: none;
        transition: opacity 0.15s;
    }

    tr.row-removing {
        opacity: 0;
        transform: translateX(8px);
        transition: opacity 0.2s ease, transform 0.2s ease;
    }

    tr.row-entering {
        animation: rowIn 0.3s ease;
    }

    @keyframes rowIn {
        from { background: #f0fdf4; }
        to { background: transparent; }
    }
</style>

<div class="tariffs-wrapper">
    <div class="tariffs-header">
        <div class="header-identity">
            <div class="avatar-large">
                {{ strtoupper(substr($service->name_service, 0, 2)) }}
            </div>
            <div class="header-title">
                <h1>{{ $service->name_service }}</h1>
                <p>
                    <strong>Tarifas</strong>
                    <span class="mx-2">|</span>
                    {{ $service->supplier->supplier_name ?? 'Sin proveedor' }}
                    <span class="mx-2">|</span>
                    {{ $service->category->name ?? 'Sin categoría' }}
                </p>
            </div>
        </div>
        <div class="header-actions">
            <a href="{{ route('admin.suppliers.productos', $service->id_supplier) }}" class="btn btn-secondary">
                <i class="ti ti-arrow-left"></i> Volver al Proveedor
            </a>
            <button class="btn btn-primary" onclick="openCreateTariffModal()">
                <i class="ti ti-plus"></i> Registrar Subcategorías
            </button>
        </div>
    </div>


    <div class="tariff-content-nav">
        <button type="button" class="active" data-tariff-tab="tariff-info">
            <i class="ti ti-info-circle"></i> Información
        </button>
        <button type="button" data-tariff-tab="tariff-types">
            <i class="ti ti-category"></i> Tipos de servicio y tarifas
        </button>
    </div>

    <div id="tariff-info" class="tariff-tab-panel active">
        @php
            $descriptionByLanguage = $service->descriptions
                ->mapWithKeys(fn ($description) => [$description->id_language => $description->description])
                ->toArray();
            $defaultLanguage = $languages->firstWhere('code', 'es') ?? $languages->first();
        @endphp
        <div class="card" style="padding:1.5rem;">
            <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:14px;margin-bottom:20px;">
                <div><small style="color:#64748b;">Servicio</small><div style="font-weight:700;color:#0f172a;">{{ $service->name_service }}</div></div>
                <div><small style="color:#64748b;">Proveedor</small><div style="font-weight:700;color:#0f172a;">{{ $service->supplier?->supplier_name ?? 'Sin proveedor' }}</div></div>
                <div><small style="color:#64748b;">Categoría</small><div style="font-weight:700;color:#0f172a;">{{ $service->category?->name ?? 'Sin categoría' }}</div></div>
            </div>

            <div style="display:grid;grid-template-columns:1.1fr 1.9fr;gap:24px;align-items:start;">
                <div>
                    <div style="font-weight:700;color:#0f172a;margin-bottom:10px;">Imagen del servicio</div>
                    <form method="POST" action="{{ route('admin.services.image.update', $service->id_service) }}" enctype="multipart/form-data">
                        @csrf
                        <div style="border:1px dashed #cbd5e1;border-radius:12px;padding:14px;background:#f8fafc;text-align:center;">
                            @if($service->imagen)
                                <img src="{{ asset('storage/' . $service->imagen) }}" alt="{{ $service->name_service }}" style="width:100%;max-height:220px;object-fit:cover;border-radius:10px;margin-bottom:12px;display:block;">
                            @else
                                <div style="display:flex;align-items:center;justify-content:center;height:180px;border:1px dashed #dbe3ef;border-radius:10px;background:#fff;color:#94a3b8;font-size:14px;">
                                    <span>Sin imagen</span>
                                </div>
                            @endif
                            <input type="file" name="imagen" accept="image/*" style="width:100%;margin-top:12px;">
                            <small style="display:block;color:#64748b;margin-top:8px;">Formatos permitidos: JPG, PNG, WEBP. Máximo 2 MB.</small>
                        </div>
                        <div style="display:flex;gap:8px;flex-wrap:wrap;margin-top:12px;">
                            <button type="submit" class="btn btn-primary"><i class="ti ti-upload"></i> Guardar imagen</button>
                            @if($service->imagen)
                                <button type="submit" name="delete_imagen" value="1" class="btn btn-secondary" onclick="return confirm('¿Deseas eliminar la imagen actual del servicio?')">
                                    <i class="ti ti-trash"></i> Eliminar
                                </button>
                            @endif
                        </div>
                    </form>
                </div>

                <div>
                    <form method="POST" action="{{ route('admin.services.descriptions.store', $service->id_service) }}">
                        @csrf
                        <label for="serviceLanguage" style="display:block;font-weight:700;color:#0f172a;margin-bottom:6px;">Idioma</label>
                        <select name="id_language" id="serviceLanguage" required style="width:100%;height:42px;border:1px solid #dbe3ef;border-radius:8px;padding:0 10px;margin-bottom:14px;">
                            @foreach($languages->sortBy(fn ($language) => $language->code === 'es' ? 0 : 1) as $language)
                                <option value="{{ $language->id_language }}">{{ $language->name }} ({{ strtoupper($language->code) }})</option>
                            @endforeach
                        </select>
                        <label for="serviceDescription" style="display:block;font-weight:700;color:#0f172a;margin-bottom:6px;">Descripción</label>
                        <textarea name="description" id="serviceDescription" rows="6" required maxlength="5000" placeholder="Escribe la descripción del servicio..." style="width:100%;border:1px solid #dbe3ef;border-radius:8px;padding:10px;resize:vertical;"></textarea>
                        <button type="submit" class="btn btn-primary" style="margin-top:12px;"><i class="ti ti-device-floppy"></i> Guardar descripción</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div id="tariff-types" class="tariff-tab-panel">
    <div class="card" id="tariffsListCard">
        <div class="card-header">
            <h5><i class="ti ti-coin"></i> Lista de Tarifas</h5>
            <span class="badge badge-secondary">{{ $paginator->total() }} subcategorías</span>
        </div>
            <div style="padding:14px 20px; background:#f8fafc; border-bottom:1px solid #e2e8f0;">
                <strong>{{ $service->name_service }}</strong>
                <span style="margin-left:12px;color:#64748b;">Proveedor: {{ $service->supplier?->supplier_name ?? 'Sin proveedor' }}</span>
                @if($service->descriptions->isNotEmpty())
                    <div style="margin-top:5px;color:#64748b;font-size:13px;">
                        @foreach($service->descriptions as $description)
                            @if($description->description)
                                <span title="{{ $description->description }}">
                                    {{ $description->language?->name }}: {{ \Illuminate\Support\Str::limit($description->description, 120) }}
                                </span>@if(!$loop->last) <span style="margin:0 8px;">·</span> @endif
                            @endif
                        @endforeach
                    </div>
                @endif
            </div>
        <div class="card-body">
            @if(session('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                    <button class="btn-close" onclick="this.parentElement.remove()">✕</button>
                </div>
            @endif

            @if($paginator->isEmpty())
                <div class="empty-state">
                    <i class="ti ti-coin-off"></i>
                    <p>No hay tarifas registradas</p>
                    <span>Registra las subcategorías que tendrán precio para este servicio</span>
                    <br>
                    <button class="btn btn-primary" onclick="openCreateTariffModal()" style="margin-top:12px;">
                        <i class="ti ti-plus"></i> Registrar Subcategorías
                    </button>
                </div>
            @else
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Subcategoría</th>
                                <th>Estado</th>
                                <th style="text-align:center;">Acciones</th>
                            </tr>
                        </thead>
                        <tbody id="tariffsTableBody">
                            @foreach($paginator as $subcategoryId => $ranges)
                                @php
                                    $first = $ranges->first();
                                    $subcategoryName = $first->subcategory->name ?? 'Sin subcategoría';
                                    $totalRanges = $ranges->count();
                                    $allHavePrice = $ranges->whereNull('price')->count() === 0;
                                    $hasPending = $ranges->where('status', 'pending')->count() > 0;
                                @endphp
                                <tr data-subcategory-id="{{ $subcategoryId }}">
                                    <td data-label="#">
                                        {{ $loop->iteration + (($paginator->currentPage() - 1) * $paginator->perPage()) }}
                                    </td>
                                    <td data-label="Subcategoría">
                                        <span class="badge badge-info">{{ $subcategoryName }}</span>
                                    </td>
                                   
                                    <td data-label="Estado">
                                        @if($allHavePrice && $first->status === 'active' && !$hasPending)
                                            <span class="badge-status active">Activa</span>
                                        @elseif($hasPending || !$allHavePrice || $first->status === 'pending')
                                            <span class="badge-pending">Pendiente</span>
                                        @else
                                            <span class="badge-status inactive">Inactiva</span>
                                        @endif
                                    </td>
                                    <td data-label="Acciones" style="text-align:center;">
                                        <div class="btn-group">
                                            <a href="{{ route('admin.tariffs.editSubcategory', [$service->id_service, $subcategoryId]) }}" 
                                               class="btn btn-outline-primary">
                                                <i class="ti ti-edit"></i> Editar
                                            </a>
                                            <button class="btn btn-outline-danger" 
                                                    onclick="confirmDeleteSubcategory({{ $subcategoryId }})">
                                                <i class="ti ti-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                {{-- PAGINACIÓN --}}
                @if($paginator->hasPages())
                    <div class="pagination-wrapper">
                        <span class="info-text">
                            Mostrando {{ $paginator->firstItem() }}–{{ $paginator->lastItem() }} de {{ $paginator->total() }} subcategoría(s)
                        </span>
                        <div class="pagination-links">
                            @if($paginator->onFirstPage())
                                <span class="page-disabled"><i class="ti ti-chevron-left"></i></span>
                            @else
                                <a href="{{ $paginator->previousPageUrl() }}" class="page-link" data-ajax-page><i class="ti ti-chevron-left"></i></a>
                            @endif

                            @php
                                $current = $paginator->currentPage();
                                $last = $paginator->lastPage();
                                $range = 2;
                            @endphp

                            @for($i = 1; $i <= $last; $i++)
                                @if($i == 1 || $i == $last || abs($i - $current) <= $range)
                                    @if($i == $current)
                                        <span class="page-current">{{ $i }}</span>
                                    @else
                                        <a href="{{ $paginator->url($i) }}" class="page-link" data-ajax-page>{{ $i }}</a>
                                    @endif
                                @elseif(abs($i - $current) == $range + 1)
                                    <span class="page-dots">…</span>
                                @endif
                            @endfor

                            @if($paginator->hasMorePages())
                                <a href="{{ $paginator->nextPageUrl() }}" class="page-link" data-ajax-page><i class="ti ti-chevron-right"></i></a>
                            @else
                                <span class="page-disabled"><i class="ti ti-chevron-right"></i></span>
                            @endif
                        </div>
                    </div>
                @endif
            @endif
        </div>
    </div>



    @php
        $availableDays = ['Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado', 'Domingo'];
        $availableDaysForService = array_filter(array_map('trim', explode(',', (string) $service->availability_days)));
        $closedDays = blank($service->availability_days)
            ? []
            : array_values(array_diff($availableDays, $availableDaysForService));
    @endphp
    <div class="availability-panel">
        
        <form id="availabilityForm" action="{{ route('admin.services.update-availability', $service->id_service) }}" method="POST">
            @csrf
            <input type="hidden" name="closed_days_present" value="1">
            
            <div class="availability-panel-header">
            <div style="display:flex; justify-content:space-between; width:100%;margin-bottom: 10px; align-items:center;">
                <div style="display:flex; gap:10px; ">
                    <strong><i class="ti ti-calendar-event"></i> Días de cierre</strong>
                    <span style="display:none;">Los días sin marcar están disponibles. Marca solo los días en que el servicio no opera.</span>
                    
                </div>

                <div>
                    
                    <button type="submit" class="availability-save"><i class="ti ti-device-floppy"></i> Guardar</button>
                    <span class="availability-feedback" id="availabilityFeedback" aria-live="polite"></span>
                </div>
            </div>
        </div>
            
            <div class="availability-days">
                @foreach($availableDays as $day)
                    <div class="availability-day">
                        <input type="checkbox" id="availability-{{ $loop->index }}" name="closed_days[]" value="{{ $day }}" {{ in_array($day, $closedDays, true) ? 'checked' : '' }}>
                        <label for="availability-{{ $loop->index }}">{{ $day }}</label>
                    </div>
                @endforeach
            </div>



        </form>

        
    </div>
    </div>

</div>

<div class="modal-overlay" id="createTariffModal">
    <div class="modal-box">
        <button class="modal-close-btn" onclick="closeCreateTariffModal()">✕</button>

        <h3><i class="ti ti-plus" style="color:#64748b;"></i> Registrar Subcategorías</h3>
        <p class="modal-sub">Selecciona una subcategoría para registrar su tarifa</p>

        <form id="createTariffForm" action="{{ route('admin.tariffs.store', $service->id_service) }}" method="POST">
            @csrf

            <div class="form-group">
                <label>Subcategorías <span class="required">*</span></label>
                <div class="subcategory-selector">
                    <select name="id_subcategories[]" id="tariffSubcategorySelect" class="form-control" required>
                        <option value="">Seleccionar subcategoría</option>
                        @foreach($availableSubcategories as $subcategory)
                            <option value="{{ $subcategory->id_subcategories }}">
                                {{ $subcategory->name }}
                            </option>
                        @endforeach
                    </select>
                    <button type="button" class="btn-add-subcategory" onclick="openCreateSubcategoryModal()" title="Crear subcategoría">
                        <i class="ti ti-plus"></i>
                    </button>
                </div>
                <small>Registra una subcategoría por vez</small>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn-submit" id="submitTariffBtn">
                    <i class="ti ti-device-floppy"></i> Registrar Subcategorías
                </button>
                <button type="button" class="btn-cancel" onclick="closeCreateTariffModal()">Cancelar</button>
            </div>

            <div class="form-message" id="tariffFormMessage"></div>
        </form>
    </div>
</div>

<div class="modal-overlay" id="createSubcategoryModal">
    <div class="modal-box">
        <button class="modal-close-btn" type="button" onclick="closeCreateSubcategoryModal()">✕</button>

        <h3><i class="ti ti-tag-plus" style="color:#64748b;"></i> Crear Subcategoría</h3>
        <p class="modal-sub">Crea una subcategoría para este servicio</p>

        <form id="createSubcategoryForm" action="{{ route('admin.services.subcategory.store') }}" method="POST">
            @csrf
            <input type="hidden" name="id_category" value="{{ $service->id_category }}">

            <div class="form-group">
                <label for="newSubcategoryName">Nombre <span class="required">*</span></label>
                <input type="text" id="newSubcategoryName" name="name" class="form-control" maxlength="300" required placeholder="Ej: Regular, VIP, Privado">
            </div>

            <div class="form-actions">
                <button type="submit" class="btn-submit" id="submitSubcategoryBtn">
                    <i class="ti ti-device-floppy"></i> Crear Subcategoría
                </button>
                <button type="button" class="btn-cancel" onclick="closeCreateSubcategoryModal()">Cancelar</button>
            </div>

            <div class="form-message" id="subcategoryFormMessage"></div>
        </form>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
const CSRF_TOKEN = '{{ csrf_token() }}';
const DELETE_SUBCATEGORY_URL_BASE = '/servicios/{{ $service->id_service }}/tarifas/subcategoria';
const AVAILABILITY_URL = '{{ route('admin.services.update-availability', $service->id_service) }}';
const SERVICE_DESCRIPTIONS = @json($descriptionByLanguage);
const DEFAULT_SERVICE_LANGUAGE = @json($defaultLanguage?->id_language);

function loadServiceDescription() {
    const language = document.getElementById('serviceLanguage').value;
    document.getElementById('serviceDescription').value = SERVICE_DESCRIPTIONS[language] || '';
}

document.getElementById('serviceLanguage')?.addEventListener('change', loadServiceDescription);
document.querySelectorAll('[data-tariff-tab]').forEach((button) => {
    button.addEventListener('click', () => {
        document.querySelectorAll('[data-tariff-tab]').forEach((item) => item.classList.remove('active'));
        document.querySelectorAll('.tariff-tab-panel').forEach((panel) => panel.classList.remove('active'));
        button.classList.add('active');
        document.getElementById(button.dataset.tariffTab).classList.add('active');
    });
});
document.addEventListener('DOMContentLoaded', () => {
    if (DEFAULT_SERVICE_LANGUAGE) {
        document.getElementById('serviceLanguage').value = DEFAULT_SERVICE_LANGUAGE;
        loadServiceDescription();
    }
});

const Toast = Swal.mixin({
    toast: true,
    position: 'top-end',
    showConfirmButton: false,
    timer: 2500,
    timerProgressBar: true,
    didOpen: (toast) => {
        toast.onmouseenter = Swal.stopTimer;
        toast.onmouseleave = Swal.resumeTimer;
    }
});

function openCreateTariffModal() {
    document.getElementById('createTariffModal').classList.add('show');
    document.body.style.overflow = 'hidden';
    document.getElementById('createTariffForm').reset();
    document.getElementById('tariffFormMessage').className = 'form-message';
    document.getElementById('tariffFormMessage').textContent = '';
}

function closeCreateTariffModal() {
    document.getElementById('createTariffModal').classList.remove('show');
    document.body.style.overflow = '';
}

function openCreateSubcategoryModal() {
    document.getElementById('createSubcategoryModal').classList.add('show');
    document.body.style.overflow = 'hidden';
    document.getElementById('createSubcategoryForm').reset();
    document.getElementById('subcategoryFormMessage').className = 'form-message';
    document.getElementById('subcategoryFormMessage').textContent = '';
    document.getElementById('newSubcategoryName').focus();
}

function closeCreateSubcategoryModal() {
    document.getElementById('createSubcategoryModal').classList.remove('show');
    document.body.style.overflow = '';
}

document.addEventListener('submit', function(event) {
    if (event.target?.id !== 'createSubcategoryForm') {
        return;
    }

    event.preventDefault();

    const form = event.target;
    const button = document.getElementById('submitSubcategoryBtn');
    const message = document.getElementById('subcategoryFormMessage');
    const originalHtml = button.innerHTML;

    button.disabled = true;
    button.innerHTML = '<i class="ti ti-loader ti-spin"></i> Creando...';
    message.className = 'form-message';
    message.textContent = '';

    fetch(form.action, {
        method: 'POST',
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json'
        },
        body: new FormData(form)
    })
        .then(response => response.json().then(data => ({ ok: response.ok, data })))
        .then(({ ok, data }) => {
            if (!ok || !data.success) {
                const validationError = data.errors ? Object.values(data.errors).flat()[0] : null;
                throw new Error(validationError || data.message || 'No se pudo crear la subcategoría.');
            }

            const select = document.getElementById('tariffSubcategorySelect');
            const option = document.createElement('option');
            option.value = data.subcategory.id;
            option.textContent = data.subcategory.name;
            option.selected = true;
            select.appendChild(option);

            closeCreateSubcategoryModal();
            Toast.fire({ icon: 'success', title: 'Subcategoría creada' });
        })
        .catch(error => {
            message.className = 'form-message error';
            message.textContent = error.message;
        })
        .finally(() => {
            button.disabled = false;
            button.innerHTML = originalHtml;
        });
});

// Reemplaza el contenido de la card de la lista con el HTML devuelto por el servidor,
// sin recargar la página. Funciona porque Laravel, tras un POST/PUT/DELETE exitoso o
// fallido, redirige al mismo index (fetch sigue esa redirección automáticamente).
function swapTariffsCard(html) {
    const doc = new DOMParser().parseFromString(html, 'text/html');
    const newCard = doc.getElementById('tariffsListCard');
    const oldCard = document.getElementById('tariffsListCard');
    if (newCard && oldCard) {
        oldCard.replaceWith(newCard);
    }
    return doc;
}

document.addEventListener('submit', function (event) {
    if (event.target?.id !== 'availabilityForm') {
        return;
    }

    event.preventDefault();

    const form = event.target;
    const button = form.querySelector('.availability-save');
    const feedback = form.querySelector('.availability-feedback');
    const originalText = button.innerHTML;

    button.disabled = true;
    button.innerHTML = '<i class="ti ti-loader ti-spin"></i> Guardando...';
    feedback.textContent = '';
    feedback.style.color = '';

    fetch(AVAILABILITY_URL, {
        method: 'POST',
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json'
        },
        body: new FormData(form)
    })
        .then(response => response.json().then(data => ({ ok: response.ok, data })))
        .then(({ ok, data }) => {
            if (!ok || !data.success) {
                throw new Error(data.message || 'No se pudieron guardar los días.');
            }

            feedback.textContent = data.message;
            Toast.fire({ icon: 'success', title: 'Días de operación actualizados' });
        })
        .catch(error => {
            feedback.textContent = error.message;
            feedback.style.color = '#991b1b';
        })
        .finally(() => {
            button.disabled = false;
            button.innerHTML = originalText;
        });
});

document.addEventListener('submit', function(e) {
    if (e.target && e.target.id === 'createTariffForm') {
        e.preventDefault();
        const form = e.target;
        const btn = document.getElementById('submitTariffBtn');
        const msgBox = document.getElementById('tariffFormMessage');
        const originalHtml = btn.innerHTML;

        btn.disabled = true;
        btn.innerHTML = '<i class="ti ti-loader ti-spin"></i> Registrando...';
        msgBox.className = 'form-message';
        msgBox.textContent = '';

        fetch(form.action, {
            method: 'POST',
            headers: { 'X-Requested-With': 'XMLHttpRequest' },
            body: new FormData(form)
        })
        .then(res => res.text())
        .then(html => {
            const doc = swapTariffsCard(html);
            const errorBox = doc.querySelector('.alert-danger, .alert-error');

            if (errorBox) {
                msgBox.className = 'form-message error';
                msgBox.textContent = errorBox.textContent.trim();
                return;
            }

            closeCreateTariffModal();
            Toast.fire({ icon: 'success', title: 'Subcategorías registradas correctamente' });
        })
        .catch(() => {
            msgBox.className = 'form-message error';
            msgBox.textContent = 'Ocurrió un error al registrar. Intenta nuevamente.';
        })
        .finally(() => {
            btn.disabled = false;
            btn.innerHTML = originalHtml;
        });
    }
});

// ============================================================
// ELIMINAR SUBCATEGORÍA (AJAX)
// ============================================================
function confirmDeleteSubcategory(subcategoryId) {
    Swal.fire({
        title: '¿Eliminar subcategoría?',
        text: 'Se eliminarán TODOS los rangos y precios asociados.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#991b1b',
        cancelButtonColor: '#94a3b8',
        confirmButtonText: 'Sí, eliminar todo',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (!result.isConfirmed) return;

        const row = document.querySelector(`tr[data-subcategory-id="${subcategoryId}"]`);
        if (row) row.classList.add('row-removing');

        const fd = new FormData();
        fd.append('_token', CSRF_TOKEN);
        fd.append('_method', 'DELETE');

        fetch(`${DELETE_SUBCATEGORY_URL_BASE}/${subcategoryId}`, {
            method: 'POST',
            headers: { 'X-Requested-With': 'XMLHttpRequest' },
            body: fd
        })
        .then(res => res.text())
        .then(html => {
            swapTariffsCard(html);
            Toast.fire({ icon: 'success', title: 'Subcategoría eliminada' });
        })
        .catch(() => {
            if (row) row.classList.remove('row-removing');
            Toast.fire({ icon: 'error', title: 'No se pudo eliminar la subcategoría' });
        });
    });
}

// ============================================================
// PAGINACIÓN (AJAX)
// ============================================================
document.addEventListener('click', function(e) {
    const link = e.target.closest('[data-ajax-page]');
    if (!link) return;
    e.preventDefault();

    const card = document.getElementById('tariffsListCard');
    if (card) card.classList.add('is-refreshing');

    fetch(link.href, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
        .then(res => res.text())
        .then(html => swapTariffsCard(html))
        .catch(() => Toast.fire({ icon: 'error', title: 'No se pudo cargar la página' }))
        .finally(() => {
            const refreshedCard = document.getElementById('tariffsListCard');
            if (refreshedCard) refreshedCard.classList.remove('is-refreshing');
        });
});

document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeCreateTariffModal();
        closeCreateSubcategoryModal();
    }
});

document.getElementById('createTariffModal').addEventListener('click', function(e) {
    if (e.target === this) closeCreateTariffModal();
});

document.getElementById('createSubcategoryModal').addEventListener('click', function(e) {
    if (e.target === this) closeCreateSubcategoryModal();
});
</script>
@endsection