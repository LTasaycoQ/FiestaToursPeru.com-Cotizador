@extends('layouts.app')

@section('title', 'Editar Cotización')
@section('content')

@push('styles')
<style>
.quote-edit-page {
    --qe-ink-900: #0f172a; --qe-ink-700: #334155; --qe-ink-500: #64748b; --qe-ink-300: #94a3b8;
    --qe-line: #e2e8f0; --qe-line-soft: #f1f5f9; --qe-surface: #ffffff; --qe-surface-muted: #f8fafc;
    --qe-accent: #6366f1; --qe-accent-600: #4f46e5; --qe-accent-soft: rgba(99, 102, 241, .08);
    --qe-success: #16a34a; --qe-success-bg: #dcfce7; --qe-success-border: #86efac; --qe-success-text: #166534;
    --qe-danger: #ef4444; --qe-danger-600: #dc2626; --qe-danger-bg: #fee2e2; --qe-danger-border: #fca5a5; --qe-danger-text: #991b1b;
    --qe-warning-bg: #fef3c7; --qe-warning-text: #92400e;
    --qe-radius-sm: 6px; --qe-radius-md: 10px; --qe-radius-lg: 14px; --qe-radius-xl: 20px;
    --qe-shadow-sm: 0 1px 2px rgba(15, 23, 42, .04); --qe-shadow-md: 0 8px 24px rgba(15, 23, 42, .08); --qe-shadow-lg: 0 24px 64px rgba(15, 23, 42, .20);
    color: var(--qe-ink-900);
}
.quote-edit-page * { box-sizing: border-box; }

.qe-card { border: 1px solid var(--qe-line); border-radius: var(--qe-radius-lg); overflow: hidden; background: var(--qe-surface); box-shadow: var(--qe-shadow-sm); }
.qe-card-body { padding: 28px 32px 32px; }

.qe-header { display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 16px; background: var(--qe-surface); padding: 20px 32px; border-bottom: 1px solid var(--qe-line); }
.qe-header-left { display: flex; align-items: center; gap: 16px; flex-wrap: wrap; min-width: 0; }
.qe-header-icon { width: 40px; height: 40px; border-radius: var(--qe-radius-md); background: var(--qe-accent-soft); color: var(--qe-accent); display: flex; align-items: center; justify-content: center; font-size: 18px; flex-shrink: 0; }
.qe-heading { display: flex; flex-direction: column; gap: 2px; min-width: 0; }
.qe-heading h3 { font-size: 19px; font-weight: 700; color: var(--qe-ink-900); margin: 0; line-height: 1.2; }
.qe-heading .qe-code { font-size: 12px; font-weight: 600; color: var(--qe-ink-500); letter-spacing: 0.02em; }

.badge-status { padding: 6px 14px 6px 10px; border-radius: 999px; font-size: 12px; font-weight: 600; display: inline-flex; align-items: center; gap: 7px; text-transform: capitalize; white-space: nowrap; }
.badge-status .badge-dot { display: inline-block; width: 7px; height: 7px; border-radius: 50%; flex-shrink: 0; }
.badge-status.draft { background: var(--qe-line-soft); color: var(--qe-ink-700); } .badge-status.draft .badge-dot { background: var(--qe-ink-300); }
.badge-status.sent { background: #dbeafe; color: #1e40af; } .badge-status.sent .badge-dot { background: #3b82f6; }
.badge-status.approved { background: var(--qe-success-bg); color: var(--qe-success-text); } .badge-status.approved .badge-dot { background: #22c55e; }
.badge-status.rejected { background: var(--qe-danger-bg); color: var(--qe-danger-text); } .badge-status.rejected .badge-dot { background: var(--qe-danger); }
.badge-status.expired { background: var(--qe-warning-bg); color: var(--qe-warning-text); } .badge-status.expired .badge-dot { background: #f59e0b; }
.badge-status.cancelled { background: var(--qe-line-soft); color: var(--qe-ink-500); } .badge-status.cancelled .badge-dot { background: var(--qe-ink-300); }

.qe-card .btn { padding: 9px 18px; border-radius: var(--qe-radius-sm); font-weight: 600; font-size: 13px; transition: all .15s ease; text-decoration: none; display: inline-flex; align-items: center; justify-content: center; gap: 7px; border: 1px solid transparent; cursor: pointer; line-height: 1.3; }
.btn-secondary { background: var(--qe-surface-muted); color: var(--qe-ink-700); border-color: var(--qe-line); }
.btn-secondary:hover { background: #eef2f7; border-color: #cbd5e1; color: var(--qe-ink-900); }
.btn-primary { background: var(--qe-ink-900); color: #fff; }
.btn-primary:hover { background: #1e293b; }
.btn-primary:disabled { opacity: .6; cursor: not-allowed; }
.btn-danger {background-color:transparent; color: #b32a2a; }
.btn-icon { padding: 9px; width: 38px; height: 38px; }
.btn-sm { padding: 6px 12px; font-size: 12px; }

.field-section { padding: 20px 0 24px; border-bottom: 1px dashed var(--qe-line); }
.field-section:first-child { padding-top: 4px; }
.field-section:last-of-type { border-bottom: none; }
.field-section__label { display: flex; align-items: center; gap: 8px; font-size: 11.5px; font-weight: 700; color: var(--qe-ink-500); text-transform: uppercase; letter-spacing: 0.06em; margin-bottom: 14px; }
.field-section__label i { color: var(--qe-accent); font-size: 15px; }

.qe-collapsible { border: 1px solid var(--qe-line); border-radius: var(--qe-radius-lg); background: var(--qe-surface-muted); overflow: hidden; margin-bottom: 18px; }
.qe-collapsible summary { list-style: none; cursor: pointer; display: flex; align-items: center; justify-content: space-between; gap: 12px; padding: 14px 18px; font-size: 11.5px; font-weight: 700; color: var(--qe-ink-500); text-transform: uppercase; letter-spacing: 0.06em; }
.qe-collapsible summary::-webkit-details-marker { display: none; }
.qe-collapsible summary .qe-collapse-title { display: flex; align-items: center; gap: 8px; }
.qe-collapsible summary .qe-collapse-icon { width: 28px; height: 28px; display: inline-flex; align-items: center; justify-content: center; border-radius: 8px; background: rgba(99, 102, 241, 0.08); color: var(--qe-accent); font-size: 16px; }
.qe-collapsible summary .qe-collapse-arrow { transition: transform .2s ease; color: var(--qe-ink-500); }
.qe-collapsible[open] summary .qe-collapse-arrow { transform: rotate(180deg); }
.qe-collapsible-content { padding: 0 18px 18px; }

.form-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 16px 20px; }
.form-grid.cols-2 { grid-template-columns: repeat(2, 1fr); }
.form-group { margin-bottom: 0; }
.form-group label { display: block; font-size: 12px; font-weight: 600; color: var(--qe-ink-700); margin-bottom: 6px; }
.form-group label .text-danger { color: var(--qe-danger); }

.form-control { width: 100%; padding: 10px 14px; border-radius: var(--qe-radius-sm); border: 1.5px solid var(--qe-line); font-size: 13.5px; outline: none; transition: border-color .15s, box-shadow .15s; background: var(--qe-surface); color: var(--qe-ink-900); height: 42px; }
textarea.form-control { height: auto; min-height: 84px; padding-top: 10px; resize: vertical; }
.form-control:focus { border-color: var(--qe-accent); box-shadow: 0 0 0 3px var(--qe-accent-soft); }
.form-control.is-invalid { border-color: var(--qe-danger); }
.invalid-feedback { display: block; font-size: 12px; color: var(--qe-danger); margin-top: 5px; }
.field-hint { font-size: 11.5px; color: var(--qe-ink-300); margin-top: 5px; }

.alert { padding: 13px 16px; border-radius: var(--qe-radius-md); margin-bottom: 16px; font-size: 13.5px; border: 1px solid transparent; }
.alert-success { background: var(--qe-success-bg); border-color: var(--qe-success-border); color: var(--qe-success-text); }
.alert-danger { background: var(--qe-danger-bg); border-color: var(--qe-danger-border); color: var(--qe-danger-text); }
.alert-info { background: #e0f2fe; border-color: #7dd3fc; color: #0369a1; }
.alert .close { float: right; background: none; border: none; font-size: 18px; color: inherit; cursor: pointer; opacity: .55; line-height: 1; }
.alert .close:hover { opacity: 1; }

.form-actions { display: flex; gap: 10px; margin-top: 24px; }
.form-actions .btn-primary { flex: 1; }
.form-actions .btn-secondary { flex: 0 0 auto; padding-left: 24px; padding-right: 24px; }

/* ===== ITINERARIO POR DÍAS ===== */
.itinerary-section { margin-top: 8px; padding-top: 28px; border-top: 1px solid var(--qe-line); }
.itinerary-header { display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 12px; margin-bottom: 16px; }
.itinerary-header .title { font-size: 15.5px; font-weight: 700; color: var(--qe-ink-900); margin: 0; display: flex; align-items: center; gap: 9px; }
.itinerary-header .title i { color: var(--qe-accent); font-size: 17px; }

.day-tabs { display: flex; gap: 6px; overflow-x: auto; padding-bottom: 4px; margin-bottom: 16px; }
.day-tab {
    flex-shrink: 0; padding: 10px 16px; border-radius: var(--qe-radius-md); border: 1.5px solid var(--qe-line);
    background: var(--qe-surface); cursor: pointer; font-size: 12.5px; font-weight: 600; color: var(--qe-ink-500);
    display: flex; flex-direction: column; align-items: center; gap: 2px; transition: all .15s; min-width: 90px;
}
.day-tab:hover { border-color: var(--qe-accent); color: var(--qe-accent-600); }
.day-tab.active { background: var(--qe-ink-900); border-color: var(--qe-ink-900); color: #fff; }
.day-tab .day-date { font-size: 10.5px; font-weight: 500; opacity: .75; }
.day-tab .day-count { font-size: 10px; background: rgba(255,255,255,.15); padding: 1px 7px; border-radius: 999px; margin-top: 2px; }
.day-tab:not(.active) .day-count { background: var(--qe-line-soft); color: var(--qe-ink-500); }

.day-panel { display: none; }
.day-panel.active { display: block; animation: fadeIn .2s ease; }
@keyframes fadeIn { from { opacity:0; transform:translateY(4px);} to {opacity:1; transform:translateY(0);} }

.day-services-list { display: flex; flex-direction: column; gap: 10px; margin-bottom: 12px; }
.day-service-card {
    display: flex; justify-content: space-between; align-items: center; gap: 12px;
    padding: 14px 16px; background: var(--qe-surface-muted); border: 1px solid var(--qe-line); border-radius: var(--qe-radius-md);
}
.day-service-card .service-info { display: flex; flex-direction: column; gap: 2px; min-width: 0; }
.day-service-card .service-info .name { font-weight: 700; font-size: 13.5px; color: var(--qe-ink-900); }
.day-service-card .service-info .meta { font-size: 12px; color: var(--qe-ink-500); display: flex; gap: 10px; flex-wrap: wrap; }
.day-service-card .service-price { font-weight: 700; color: var(--qe-success-text); font-size: 14px; white-space: nowrap; }
.day-service-card .actions { display: flex; align-items: center; gap: 10px; flex-shrink: 0; }
.day-service-card .detail-editor { display: flex; align-items: center; gap: 6px; flex-wrap: wrap; justify-content: flex-end; }
.day-service-card .detail-editor select,
.day-service-card .detail-editor input { height: 34px; padding: 6px 9px; font-size: 12px; }
.day-service-card .detail-editor select { min-width: 190px; }
.day-service-card .detail-editor input { width: 105px; }
.day-service-card .detail-editor input.quantity-input { width: 78px; }
.day-service-card .detail-editor .editor-field { display: flex; flex-direction: column; gap: 3px; }
.day-service-card .detail-editor .editor-field label { color: var(--qe-ink-500); font-size: 10px; font-weight: 600; }

.day-empty { text-align: center; padding: 24px; background: var(--qe-surface-muted); border-radius: var(--qe-radius-md); border: 1px dashed var(--qe-line); color: var(--qe-ink-500); font-size: 13px; }
.day-limit-note { font-size: 11.5px; color: var(--qe-ink-300); margin-top: 8px; display: flex; align-items: center; gap: 6px; }

/* ===== HOSPEDAJE - 2 OPCIONES (POR DÍA) ===== */
.accommodation-section { margin-top: 8px; padding-top: 28px; border-top: 1px solid var(--qe-line); }
.accommodation-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-top: 16px; }
.accommodation-option {
    border: 1.5px solid var(--qe-line); border-radius: var(--qe-radius-lg); padding: 18px; background: var(--qe-surface-muted);
}
.accommodation-option.has-hotel { border-color: var(--qe-accent); background: rgba(99,102,241,.04); }
.accommodation-option .option-label {
    font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: .06em; color: var(--qe-accent);
    display: flex; align-items: center; gap: 6px; margin-bottom: 12px;
}

.accommodation-days-grid {
    display: flex;
    flex-direction: column;
    gap: 6px;
    max-height: 300px;
    overflow-y: auto;
}
.accommodation-day-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 8px 12px;
    background: var(--qe-surface);
    border-radius: var(--qe-radius-sm);
    border: 1px solid var(--qe-line);
    font-size: 12.5px;
}
.accommodation-day-item .day-info {
    display: flex;
    align-items: center;
    gap: 8px;
}
.accommodation-day-item .day-info .day-number {
    font-weight: 700;
    color: var(--qe-ink-900);
}
.accommodation-day-item .day-info .day-date {
    color: var(--qe-ink-500);
    font-size: 11px;
}
.accommodation-day-item .day-hotel {
    display: flex;
    align-items: center;
    gap: 8px;
}
.accommodation-day-item .day-hotel .hotel-name {
    font-weight: 600;
    color: var(--qe-ink-900);
}
.accommodation-day-item .day-hotel .hotel-price {
    font-weight: 600;
    color: var(--qe-success-text);
    font-size: 12px;
}
.accommodation-day-item .day-actions {
    display: flex;
    gap: 4px;
}
.accommodation-day-item .day-actions .btn-sm {
    padding: 2px 6px;
    font-size: 11px;
}

.accommodation-empty { text-align: center; padding: 20px 10px; color: var(--qe-ink-300); font-size: 12.5px; }
.accommodation-empty i { font-size: 26px; display: block; margin-bottom: 6px; }

/* ===== MODALES ===== */
.modal { position: fixed !important; inset: 0; width: 100% !important; height: 100vh !important; z-index: 1050 !important; overflow-y: auto !important; background: rgba(15, 23, 42, .62); backdrop-filter: blur(6px); display: none; padding: 24px; align-items: center; justify-content: center; }
.modal.show { display: flex !important; }
.modal .modal-dialog { width: 100%; max-width: 900px; margin: 0; background-color:white; }
.modal .modal-content { background: #fff; border-radius: var(--qe-radius-xl); box-shadow: var(--qe-shadow-lg); width:100%; max-height: 90vh; overflow-y: auto; }
.modal .modal-header { display: flex; justify-content: space-between; align-items: center; padding: 20px 28px; border-bottom: 1px solid var(--qe-line); position: sticky; top: 0; background: #fff; z-index: 10; border-radius: var(--qe-radius-xl) var(--qe-radius-xl) 0 0; }
.modal .modal-header .modal-title { font-size: 17px; font-weight: 700; color: var(--qe-ink-900); margin: 0; display: flex; align-items: center; gap: 10px; }
.modal .modal-header .modal-title i { color: var(--qe-accent); background: var(--qe-accent-soft); width: 32px; height: 32px; border-radius: 9px; display: flex; align-items: center; justify-content: center; font-size: 16px; }
.modal .modal-header .btn-close-modal { cursor: pointer; background: none; border: none; font-size: 20px; color: var(--qe-ink-300); padding: 6px; border-radius: var(--qe-radius-sm); transition: all .15s; line-height: 1; }
.modal .modal-header .btn-close-modal:hover { color: var(--qe-ink-900); background: var(--qe-surface-muted); }
.modal .modal-body { padding: 24px 28px; }
.modal .modal-footer { display: flex; gap: 10px; justify-content: flex-end; padding: 16px 28px; border-top: 1px solid var(--qe-line); background: var(--qe-surface-muted); border-radius: 0 0 var(--qe-radius-xl) var(--qe-radius-xl); }

.modal-section-label { font-size: 11px; font-weight: 700; color: var(--qe-ink-500); text-transform: uppercase; letter-spacing: .06em; margin: 20px 0 10px; display: flex; align-items: center; gap: 6px; }
.modal-section-label:first-child { margin-top: 0; }
.modal-section-label i { color: var(--qe-accent); font-size: 13px; }

.filters-grid { display: grid; grid-template-columns: 1fr 1fr auto; gap: 10px; align-items: end; }
.filters-grid .form-group { margin-bottom: 0; }
.filter-actions { display: flex; gap: 6px; align-items: center; padding-bottom: 1px; }
.filter-actions .btn { height: 42px; padding: 0 14px; }

.service-select-wrapper { position: relative; margin-top: 14px; }
.service-select-wrapper .form-control { padding-right: 40px; }
.service-select-wrapper .service-loading { position: absolute; right: 12px; top: 50%; transform: translateY(-50%); color: var(--qe-ink-300); }
.service-catalog-table { width: 100%; border-collapse: collapse; }
.service-catalog-table .service-category { color: var(--qe-ink-500); font-size: 12px; }
.service-catalog-table .service-tariff-select { min-width: 190px; height: 36px; padding: 6px 9px; font-size: 12px; }
.service-catalog-table .service-price { color: var(--qe-success-text); font-weight: 700; white-space: nowrap; }

.tariff-section { margin-top: 16px; padding: 16px 18px; background: var(--qe-surface-muted); border-radius: var(--qe-radius-md); border: 1px solid var(--qe-line); display: none; }
.tariff-section.visible { display: block; }
.tariff-section .tariff-header { display: flex; align-items: center; gap: 8px; font-size: 12px; font-weight: 600; color: var(--qe-ink-500); text-transform: uppercase; letter-spacing: .05em; margin-bottom: 10px; }
.tariff-section .tariff-header i { color: var(--qe-accent); }
.tariff-hint { font-size: 12px; color: var(--qe-ink-500); margin-top: 6px; display: flex; align-items: center; gap: 6px; }
.tariff-hint i { color: var(--qe-accent); font-size: 13px; }
.tariff-hint.warning { color: #b45309; } .tariff-hint.warning i { color: #f59e0b; }

.details-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 14px; margin-top: 16px; }

.price-preview { background: var(--qe-accent-soft); border: 1px solid rgba(99, 102, 241, .25); padding: 14px 18px; border-radius: var(--qe-radius-md); margin-top: 16px; display: flex; align-items: center; gap: 12px; }
.price-preview.has-price { border-color: var(--qe-accent); background: rgba(99, 102, 241, .1); }
.price-preview i { color: var(--qe-accent); font-size: 18px; flex-shrink: 0; }
.price-preview .price-content { display: flex; flex-direction: column; gap: 2px; flex: 1; }
.price-preview .price-label { font-size: 12px; color: var(--qe-ink-500); }
.price-preview .price-value { font-size: 15px; font-weight: 700; color: var(--qe-ink-900); }
.price-preview .price-value .highlight { color: var(--qe-success); }

.spinner-border-sm { width: 16px; height: 16px; border-width: 2px; border-style: solid; border-color: currentColor; border-right-color: transparent; border-radius: 50%; animation: spin .6s linear infinite; display: inline-block; }
@keyframes spin { to { transform: rotate(360deg); } }

@media (max-width: 992px) {
    .form-grid { grid-template-columns: repeat(2, 1fr); }
    .accommodation-grid { grid-template-columns: 1fr; }
}

@media (max-width: 768px) {
    .qe-header { flex-direction: column; align-items: stretch; padding: 16px 18px; }
    .qe-header-left { flex-direction: column; align-items: flex-start; gap: 10px; }
    .qe-card-body { padding: 20px 18px 24px; }
    .form-grid, .form-grid.cols-2 { grid-template-columns: 1fr; gap: 14px; }
    .itinerary-header { flex-direction: column; align-items: stretch; }
    .modal { padding: 12px; }
    .modal .modal-header, .modal .modal-body, .modal .modal-footer { padding-left: 18px; padding-right: 18px; }
    .filters-grid { grid-template-columns: 1fr; }
    .details-grid { grid-template-columns: 1fr; }
    .form-actions { flex-direction: column; }
    .form-actions .btn { width: 100%; }
    .day-service-card { flex-direction: column; align-items: flex-start; }
}
</style>
@endpush

<div class="container-fluid quote-edit-page">
    <div class="row">
        <div class="col-12">

            <div class="qe-card">

                <div class="qe-header">
                    <div class="qe-header-left">
                        <a href="{{ route('admin.quotes.show', $quote->id_quote) }}" class="btn btn-secondary">
                            <i class="ti ti-chevron-left"></i>
                        </a>
                        <div style="display:flex; align-items:center; gap:12px;">
                            <div class="qe-header-icon"><i class="ti ti-file-edit"></i></div>
                            <div class="qe-heading">
                                <h3>Editar Cotización</h3>
                                <span class="qe-code">{{ $quote->quote_number ?? '' }}</span>
                            </div>
                        </div>
                        <span class="badge-status {{ $quote->status }}">
                            <span class="badge-dot"></span>
                            {{ $quote->status_label }}
                        </span>
                    </div>
                    <div class="header-actions" style="display:flex; gap:8px;">
                        <button class="btn btn-danger" onclick="confirmDelete({{ $quote->id_quote }}, '{{ addslashes($quote->name ?? 'Cotización') }}')">
                            <i class="ti ti-trash"></i> Eliminar
                        </button>
                    </div>
                </div>

                <!-- ===== BODY ===== -->
                <div class="qe-card-body">

                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('success') }}
                            <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            {{ session('error') }}
                            <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
                        </div>
                    @endif

                    @if($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0" style="padding-left:1.2rem;">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    @if($quote->quoteDays->count() === 0)
                        <div class="alert alert-info">
                            <i class="ti ti-info-circle"></i>
                            Esta cotización aún no tiene fechas de itinerario definidas. Guarda una fecha de inicio y fin para generar los días.
                        </div>
                    @endif

                    <!-- ===== FORMULARIO DATOS GENERALES ===== -->
                    <form action="{{ route('admin.quotes.update', $quote->id_quote) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="field-section">
                            <div class="field-section__label"><i class="ti ti-info-circle"></i> Información general</div>
                            <div class="form-grid">
                                <div class="form-group">
                                    <label for="name">Nombre Cotización <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $quote->name) }}" maxlength="300">
                                    @error('name') <span class="invalid-feedback">{{ $message }}</span> @enderror
                                </div>
                                <div class="form-group">
                                    <label for="status">Estado</label>
                                    <select class="form-control" id="status" name="status">
                                        <option value="draft" {{ $quote->status == 'draft' ? 'selected' : '' }}>Borrador</option>
                                        <option value="sent" {{ $quote->status == 'sent' ? 'selected' : '' }}>Enviada</option>
                                        <option value="approved" {{ $quote->status == 'approved' ? 'selected' : '' }}>Aprobada</option>
                                        <option value="rejected" {{ $quote->status == 'rejected' ? 'selected' : '' }}>Rechazada</option>
                                        <option value="expired" {{ $quote->status == 'expired' ? 'selected' : '' }}>Vencida</option>
                                        <option value="cancelled" {{ $quote->status == 'cancelled' ? 'selected' : '' }}>Cancelada</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="passengers_count">Pasajeros</label>
                                    <input type="number" class="form-control" id="passengers_count" name="passengers_count" value="{{ old('passengers_count', $quote->passengers_count) }}" min="1">
                                </div>
                            </div>
                        </div>

                        <div class="field-section">
                            <div class="field-section__label"><i class="ti ti-users"></i> Cliente y contacto</div>
                            <div class="form-grid cols-2">
                                <div class="form-group">
                                    <label for="id_client">Cliente <span class="text-danger">*</span></label>
                                    <select class="form-control" id="id_client" name="id_client" onchange="cargarContactos()">
                                        <option value="">Seleccione un cliente</option>
                                        @foreach($clients as $client)
                                            <option value="{{ $client->id_client }}" {{ old('id_client', $quote->id_client) == $client->id_client ? 'selected' : '' }}>{{ $client->name_client }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="id_contacts">Contacto</label>
                                    <div style="display:flex; gap:8px; align-items:flex-end;">
                                        <select class="form-control" id="id_contacts" name="id_contacts" style="flex:1;">
                                            <option value="">Seleccione un contacto</option>
                                            @foreach($contacts as $contact)
                                                <option value="{{ $contact->id_contacts }}" {{ old('id_contacts', $quote->id_contacts) == $contact->id_contacts ? 'selected' : '' }}>{{ $contact->name }} {{ $contact->last_names }}</option>
                                            @endforeach
                                        </select>
                                        <button type="button" class="btn btn-secondary" style="background-color: #0F172A;  color:white; padding:10px 12px; white-space:nowrap;" onclick="openQuoteContactModal()">
                                            <i class="ti ti-plus"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="field-section">
                            <div class="field-section__label"><i class="ti ti-calendar-time"></i> Fechas del itinerario</div>
                            <div class="form-grid cols-2">
                                <div class="form-group">
                                    <label for="start_date">Fecha Inicio</label>
                                    <input type="date" class="form-control" id="start_date" name="start_date" value="{{ old('start_date', $quote->start_date ? $quote->start_date->format('Y-m-d') : '') }}">
                                </div>
                                <div class="form-group">
                                    <label for="end_date">Fecha Fin</label>
                                    <input type="date" class="form-control" id="end_date" name="end_date" value="{{ old('end_date', $quote->end_date ? $quote->end_date->format('Y-m-d') : '') }}">
                                </div>
                            </div>
                            
                        </div>

                        <div class="field-section">
                            <div class="field-section__label"><i class="ti ti-notes"></i> Notas</div>
                            <div class="form-group">
                                <textarea class="form-control" id="notes" name="notes" rows="3">{{ old('notes', $quote->notes) }}</textarea>
                            </div>
                        </div>

                        <div class="form-actions">
                            <button type="submit" class="btn btn-primary"><i class="ti ti-device-floppy"></i> Actualizar Cotización</button>
                            {{-- <a href="{{ route('admin.quotes.show', $quote->id_quote) }}" class="btn btn-secondary">Cancelar</a> --}}
                        </div>
                    </form>

                    <div id="modalQuoteNewContact" style="display:none; position:fixed; inset:0; background:rgba(15,23,42,.45); z-index:9999; align-items:center; justify-content:center; padding:20px;">
                        <div style="width:min(520px,100%); background:#fff; border-radius:14px; padding:22px 20px; box-shadow:0 24px 64px rgba(15,23,42,.18);">
                            <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:16px;">
                                <h5 style="margin:0; font-size:18px; font-weight:700; color:#0f172a;">Nuevo contacto</h5>
                                <button type="button" class="btn btn-secondary" onclick="closeQuoteContactModal()" style="padding:8px 12px;">✕</button>
                            </div>
                            <form id="quoteNewContactForm">
                                @csrf
                                <input type="hidden" name="id_client" id="quote_new_contact_id_client">
                                <div class="form-grid" style="grid-template-columns:repeat(2,1fr); gap:16px 20px;">
                                    <div class="form-group" style="grid-column:span 2;">
                                        <label>Nombre <span style="color:#dc2626">*</span></label>
                                        <input type="text" class="form-control" name="name" required>
                                    </div>
                                    <div class="form-group">
                                        <label>Apellidos</label>
                                        <input type="text" class="form-control" name="last_names">
                                    </div>
                                    <div class="form-group">
                                        <label>Cargo</label>
                                        <input type="text" class="form-control" name="qualification">
                                    </div>
                                    <div class="form-group">
                                        <label>Correo</label>
                                        <input type="email" class="form-control" name="email">
                                    </div>
                                    <div class="form-group">
                                        <label>Teléfono</label>
                                        <input type="text" class="form-control" name="first_phone">
                                    </div>
                                </div>
                                <div style="display:flex; justify-content:flex-end; gap:10px; margin-top:18px;">
                                    <button style="width:100%; border-radius:8px;" type="submit" class="btn btn-primary"><i class="ti ti-device-floppy"></i> Guardar contacto</button>
                                </div>
                            </form>
                        </div>
                    </div>

                    <div class="itinerary-section">
                        <div class="itinerary-header">
                            <h4 class="title">
                                <i class="ti ti-route"></i> Itinerario
                            </h4>
                        </div>

                        @if($quote->quoteDays->count() > 0)
                            <div class="day-tabs" id="dayTabs">
                                @foreach($quote->quoteDays as $index => $day)
                                    <button type="button" class="day-tab {{ $index === 0 ? 'active' : '' }}" data-day-panel="day-panel-{{ $day->id_quote_day }}" onclick="switchDayTab(this)">
                                        <span>Día {{ $day->day_number }}</span>
                                    </button>
                                @endforeach
                            </div>

                            @foreach($quote->quoteDays as $index => $day)
                                <div class="day-panel {{ $index === 0 ? 'active' : '' }}" id="day-panel-{{ $day->id_quote_day }}">
                                    <div class="day-services-list">
                                        @forelse($day->details as $detail)
                                            @php
                                                $detailService = $detail->service;
                                                $detailTariffGroups = $detailService
                                                    ? collect($detailService->tariffs ?? [])->groupBy('id_subcategories')
                                                    : collect();
                                            @endphp
                                            <div class="day-service-card" id="detail-{{ $detail->id_detail_quote }}">
                                                <div class="service-info">
                                                    <span class="name">{{ $detail->service->name_service ?? 'Servicio eliminado' }}</span>
                                                    
                                                </div>
                                                <div class="actions">
                                                    <div class="detail-editor">
                                                        <div class="editor-field">
                                                            <label for="detail-tariff-{{ $detail->id_detail_quote }}">Categoría</label>
                                                            <select class="form-control" id="detail-tariff-{{ $detail->id_detail_quote }}" aria-label="Tarifa del servicio" onchange="syncDetailPrice({{ $detail->id_detail_quote }})">
                                                                @foreach($detailTariffGroups as $subcategoryTariffs)
                                                                    @php
                                                                        $detailTariff = $subcategoryTariffs
                                                                            ->filter(function ($candidateTariff) use ($quote) {
                                                                                if ($candidateTariff->pricing_type === 'flat') {
                                                                                    return true;
                                                                                }

                                                                                $passengers = $quote->passengers_count ?: 1;
                                                                                return ($candidateTariff->min_people_count === null || $candidateTariff->min_people_count <= $passengers)
                                                                                    && ($candidateTariff->max_people_count === null || $candidateTariff->max_people_count >= $passengers);
                                                                            })
                                                                            ->sortByDesc('price')
                                                                            ->first() ?? $subcategoryTariffs->first();
                                                                    @endphp
                                                                    <option value="{{ $detailTariff->id_tariff }}" data-price="{{ (float) ($detailTariff->price ?? 0) }}" {{ $detail->id_tariff == $detailTariff->id_tariff ? 'selected' : '' }}>
                                                                        {{ $detailTariff->subCategory->name ?? 'Tarifa' }}
                                                                    </option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                        <div class="editor-field">
                                                            <label for="detail-price-{{ $detail->id_detail_quote }}">Precio unitario</label>
                                                            <input type="number" class="form-control" id="detail-price-{{ $detail->id_detail_quote }}" value="{{ number_format((float) $detail->unit_price, 2, '.', '') }}" min="0" step="0.01" aria-label="Precio editable">
                                                        </div>
                                                        <div class="editor-field">
                                                            <label for="detail-quantity-{{ $detail->id_detail_quote }}">Pasajeros</label>
                                                            <input type="number" class="form-control quantity-input" id="detail-quantity-{{ $detail->id_detail_quote }}" value="{{ $detail->quantity ?: ($quote->passengers_count ?: 1) }}" min="1" step="1" aria-label="Cantidad de pasajeros">
                                                        </div>
                                                        
                                                    </div>
                                                    <span class="service-price" id="detail-subtotal-{{ $detail->id_detail_quote }}">$ {{ number_format($detail->subtotal ?? 0, 2) }}</span>
                                                    <button type="button" class="btn btn-danger btn-icon btn-sm" onclick="removeService({{ $detail->id_detail_quote }})" title="Eliminar">
                                                        <i class="ti ti-trash"></i>
                                                    </button>

                                                    <button type="button" class="btn btn-primary btn-sm" onclick="updateServiceDetail({{ $detail->id_detail_quote }})" title="Guardar tarifa y precio">
                                                        <i class="ti ti-device-floppy"></i>
                                                    </button>
                                                </div>
                                            </div>
                                        @empty
                                            <div class="day-empty">
                                                <i class="ti ti-calendar-off" style="font-size:22px;display:block;margin-bottom:6px;"></i>
                                                Sin servicios agregados para este día
                                            </div>
                                        @endforelse
                                    </div>

                                        <button type="button" class="btn btn-primary btn-sm" onclick="openAddServiceModal({{ $day->day_number }})">
                                            <i class="ti ti-plus"></i> Agregar servicio al Día {{ $day->day_number }}
                                        </button>
                                </div>
                            @endforeach
                        @else
                            <div class="day-empty">
                                <i class="ti ti-calendar-off" style="font-size:22px;display:block;margin-bottom:6px;"></i>
                                Define fechas de inicio y fin arriba y guarda para generar el itinerario
                            </div>
                        @endif
                    </div>

                    <!-- ============================================================
                         HOSPEDAJE - 2 OPCIONES (POR DÍA)
                         ============================================================ -->
                    <div class="accommodation-section">
                        <div class="itinerary-header">
                            <h4 class="title">
                                <i class="ti ti-bed"></i> Hospedaje — 2 opciones
                                <span style="font-size:12px; font-weight:400; color:var(--qe-ink-500);">
                                    (cada día puede tener hotel diferente)
                                </span>
                            </h4>
                        </div>

                        <div class="accommodation-grid">
                            <!-- OPCIÓN 1 -->
                            <div class="accommodation-option {{ $quote->accommodationOption1->count() > 0 ? 'has-hotel' : '' }}" id="accOption1">
                                <div class="option-label"><i class="ti ti-number-1"></i> Opción 1</div>
                                @php $hotelsOption1 = $quote->accommodationOption1->sortBy('quoteDay.day_number'); @endphp
                                @if($hotelsOption1->count() > 0)
                                    @php
                                        $groups = [];
                                        foreach($hotelsOption1 as $hotel) {
                                            $sid = $hotel->id_service ?? 's'.$hotel->id_quote_accommodation;
                                            if (! isset($groups[$sid])) {
                                                $groups[$sid] = [
                                                    'service' => $hotel->service,
                                                    'prices' => [],
                                                    'days' => [],
                                                    'dates' => [],
                                                    'accom_ids' => [],
                                                ];
                                            }
                                            $groups[$sid]['days'][] = $hotel->quoteDay->day_number;
                                            $groups[$sid]['dates'][$hotel->quoteDay->day_number] = $hotel->quoteDay->date->format('d/m/Y');
                                            $groups[$sid]['prices'][] = $hotel->unit_price;
                                            $groups[$sid]['accom_ids'][$hotel->quoteDay->day_number] = $hotel->id_quote_accommodation;
                                        }
                                    @endphp

                                    <div class="accommodation-groups">
                                        @foreach($groups as $sid => $group)
                                            @php
                                                $days = $group['days'];
                                                sort($days);
                                                $ranges = [];
                                                $start = null; $prev = null;
                                                foreach($days as $d) {
                                                    if ($start === null) { $start = $prev = $d; }
                                                    elseif ($d == $prev + 1) { $prev = $d; }
                                                    else { $ranges[] = [$start,$prev]; $start = $prev = $d; }
                                                }
                                                if ($start !== null) $ranges[] = [$start,$prev];
                                                $min = min($days);
                                                $max = max($days);
                                                $uniquePrices = array_values(array_unique($group['prices']));
                                                $priceDisplay = count($uniquePrices) === 1 ? number_format($uniquePrices[0], 2) : null;
                                            @endphp

                                            <div class="accommodation-group" style="border:1px solid var(--qe-line); border-radius:8px; padding:10px; margin-bottom:10px;">
                                                <div class="group-summary" style="display:flex; align-items:center; justify-content:space-between; gap:12px;">
                                                    <div style="flex:1; min-width:0;">
                                                        <div style="font-weight:700;">{{ $group['service']->name_service ?? 'Hotel eliminado' }}</div>
                                                        <div style="font-size:12px; color:var(--qe-ink-500); margin-top:6px;">
                                                            @foreach($ranges as $r)
                                                                @if($r[0] == $r[1])
                                                                    <span>Día {{ $r[0] }} ({{ $group['dates'][$r[0]] }})</span>
                                                                @else
                                                                    <span>Día {{ $r[0] }}–{{ $r[1] }} ({{ $group['dates'][$r[0]] }} – {{ $group['dates'][$r[1]] }})</span>
                                                                @endif
                                                                @if(! $loop->last) <span style="margin:0 8px;">•</span> @endif
                                                            @endforeach
                                                        </div>
                                                    </div>
                                                    <div style="display:flex; gap:8px; align-items:center;">
                                                        <div style="min-width:80px; text-align:right; font-weight:600;">@if($priceDisplay) $ {{ $priceDisplay }} @else Varios precios @endif</div>
                                                        <div style="display:flex; gap:6px;">
                                                            <button type="button" class="btn btn-secondary btn-sm" onclick="toggleAccommodationGroup('accg1_{{ $sid }}')"><i class="ti ti-chevron-down"></i> Ver días</button>
                                                            <button type="button" class="btn btn-primary btn-sm" onclick="openAccommodationToDayModal(1, null, {{ $min }}, {{ $max }})"><i class="ti ti-edit"></i> Editar rango</button>
                                                            <button type="button" class="btn btn-danger btn-sm" onclick="removeAccommodationGroup(@json(array_values($group['accom_ids'])))"><i class="ti ti-trash"></i> Eliminar</button>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="group-days" id="accg1_{{ $sid }}" style="display:none; margin-top:10px;">
                                                    <div class="accommodation-days-grid">
                                                        @foreach($days as $d)
                                                            <div class="accommodation-day-item" id="acc-{{ $group['accom_ids'][$d] }}">
                                                                <div class="day-info">
                                                                    <span class="day-number">Día {{ $d }}</span>
                                                                    <span class="day-date">{{ $group['dates'][$d] }}</span>
                                                                </div>
                                                                <div class="day-hotel">
                                                                    <span class="hotel-name">{{ $group['service']->name_service ?? 'Hotel eliminado' }}</span>
                                                                    <span class="hotel-price">$ {{ number_format($group['prices'][array_search($d, $days)] ?? $group['prices'][0], 2) }}</span>
                                                                </div>
                                                                <div class="day-actions">
                                                                    <button type="button" class="btn btn-secondary btn-sm" onclick="openAccommodationToDayModal(1, {{ $d }})" title="Cambiar">
                                                                        <i class="ti ti-edit"></i>
                                                                    </button>
                                                                    <button type="button" class="btn btn-danger btn-sm" onclick="removeAccommodation({{ $group['accom_ids'][$d] }})" title="Eliminar">
                                                                        <i class="ti ti-trash"></i>
                                                                    </button>
                                                                </div>
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                </div>
                                            </div>

                                        @endforeach
                                    </div>

                                    <div style="margin-top:10px;">
                                        <button type="button" class="btn btn-primary btn-sm" onclick="openAccommodationToDayModal(1, null)">
                                            <i class="ti ti-plus"></i> Agregar/Reemplazar hotel
                                        </button>
                                    </div>
                                @else
                                    <div class="accommodation-empty">
                                        <i class="ti ti-bed-off"></i>
                                        Sin hoteles asignados
                                    </div>
                                    <button type="button" class="btn btn-primary btn-sm" style="width:100%; justify-content:center; margin-top:10px;" onclick="openAccommodationToDayModal(1, null)">
                                        <i class="ti ti-plus"></i> Agregar hotel
                                    </button>
                                @endif
                            </div>

                            <!-- OPCIÓN 2 -->
                            <div class="accommodation-option {{ $quote->accommodationOption2->count() > 0 ? 'has-hotel' : '' }}" id="accOption2">
                                <div class="option-label"><i class="ti ti-number-2"></i> Opción 2</div>
                                @php $hotelsOption2 = $quote->accommodationOption2->sortBy('quoteDay.day_number'); @endphp
                                @if($hotelsOption2->count() > 0)
                                    @php
                                        $groups2 = [];
                                        foreach($hotelsOption2 as $hotel) {
                                            $sid = $hotel->id_service ?? 's'.$hotel->id_quote_accommodation;
                                            if (! isset($groups2[$sid])) {
                                                $groups2[$sid] = [
                                                    'service' => $hotel->service,
                                                    'prices' => [],
                                                    'days' => [],
                                                    'dates' => [],
                                                    'accom_ids' => [],
                                                ];
                                            }
                                            $groups2[$sid]['days'][] = $hotel->quoteDay->day_number;
                                            $groups2[$sid]['dates'][$hotel->quoteDay->day_number] = $hotel->quoteDay->date->format('d/m/Y');
                                            $groups2[$sid]['prices'][] = $hotel->unit_price;
                                            $groups2[$sid]['accom_ids'][$hotel->quoteDay->day_number] = $hotel->id_quote_accommodation;
                                        }
                                    @endphp

                                    <div class="accommodation-groups">
                                        @foreach($groups2 as $sid => $group)
                                            @php
                                                $days = $group['days'];
                                                sort($days);
                                                $ranges = [];
                                                $start = null; $prev = null;
                                                foreach($days as $d) {
                                                    if ($start === null) { $start = $prev = $d; }
                                                    elseif ($d == $prev + 1) { $prev = $d; }
                                                    else { $ranges[] = [$start,$prev]; $start = $prev = $d; }
                                                }
                                                if ($start !== null) $ranges[] = [$start,$prev];
                                                $min = min($days);
                                                $max = max($days);
                                                $uniquePrices = array_values(array_unique($group['prices']));
                                                $priceDisplay = count($uniquePrices) === 1 ? number_format($uniquePrices[0], 2) : null;
                                            @endphp

                                            <div class="accommodation-group" style="border:1px solid var(--qe-line); border-radius:8px; padding:10px; margin-bottom:10px;">
                                                <div class="group-summary" style="display:flex; align-items:center; justify-content:space-between; gap:12px;">
                                                    <div style="flex:1; min-width:0;">
                                                        <div style="font-weight:700;">{{ $group['service']->name_service ?? 'Hotel eliminado' }}</div>
                                                        <div style="font-size:12px; color:var(--qe-ink-500); margin-top:6px;">
                                                            @foreach($ranges as $r)
                                                                @if($r[0] == $r[1])
                                                                    <span>Día {{ $r[0] }} ({{ $group['dates'][$r[0]] }})</span>
                                                                @else
                                                                    <span>Día {{ $r[0] }}–{{ $r[1] }} ({{ $group['dates'][$r[0]] }} – {{ $group['dates'][$r[1]] }})</span>
                                                                @endif
                                                                @if(! $loop->last) <span style="margin:0 8px;">•</span> @endif
                                                            @endforeach
                                                        </div>
                                                    </div>
                                                    <div style="display:flex; gap:8px; align-items:center;">
                                                        <div style="min-width:80px; text-align:right; font-weight:600;">@if($priceDisplay) $ {{ $priceDisplay }} @else Varios precios @endif</div>
                                                        <div style="display:flex; gap:6px;">
                                                            <button type="button" class="btn btn-secondary btn-sm" onclick="toggleAccommodationGroup('accg2_{{ $sid }}')"><i class="ti ti-chevron-down"></i> Ver días</button>
                                                            <button type="button" class="btn btn-primary btn-sm" onclick="openAccommodationToDayModal(2, null, {{ $min }}, {{ $max }})"><i class="ti ti-edit"></i> Editar rango</button>
                                                            <button type="button" class="btn btn-danger btn-sm" onclick="removeAccommodationGroup(@json(array_values($group['accom_ids'])))"><i class="ti ti-trash"></i> Eliminar</button>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="group-days" id="accg2_{{ $sid }}" style="display:none; margin-top:10px;">
                                                    <div class="accommodation-days-grid">
                                                        @foreach($days as $d)
                                                            <div class="accommodation-day-item" id="acc-{{ $group['accom_ids'][$d] }}">
                                                                <div class="day-info">
                                                                    <span class="day-number">Día {{ $d }}</span>
                                                                    <span class="day-date">{{ $group['dates'][$d] }}</span>
                                                                </div>
                                                                <div class="day-hotel">
                                                                    <span class="hotel-name">{{ $group['service']->name_service ?? 'Hotel eliminado' }}</span>
                                                                    <span class="hotel-price">$ {{ number_format($group['prices'][array_search($d, $days)] ?? $group['prices'][0], 2) }}</span>
                                                                </div>
                                                                <div class="day-actions">
                                                                    <button type="button" class="btn btn-secondary btn-sm" onclick="openAccommodationToDayModal(2, {{ $d }})" title="Cambiar">
                                                                        <i class="ti ti-edit"></i>
                                                                    </button>
                                                                    <button type="button" class="btn btn-danger btn-sm" onclick="removeAccommodation({{ $group['accom_ids'][$d] }})" title="Eliminar">
                                                                        <i class="ti ti-trash"></i>
                                                                    </button>
                                                                </div>
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                </div>
                                            </div>

                                        @endforeach
                                    </div>

                                    <div style="margin-top:10px;">
                                        <button type="button" class="btn btn-primary btn-sm" onclick="openAccommodationToDayModal(2, null)">
                                            <i class="ti ti-plus"></i> Agregar/Reemplazar hotel
                                        </button>
                                    </div>
                                @else
                                    <div class="accommodation-empty">
                                        <i class="ti ti-bed-off"></i>
                                        Sin hoteles asignados
                                    </div>
                                    <button type="button" class="btn btn-primary btn-sm" style="width:100%; justify-content:center; margin-top:10px;" onclick="openAccommodationToDayModal(2, null)">
                                        <i class="ti ti-plus"></i> Agregar hotel
                                    </button>
                                @endif
                            </div>
                        </div>
                    </div>

                </div>
            </div>

        </div>
    </div>
</div>

<div class="modal quote-edit-page" id="modalAddService" tabindex="-1" style="display:none;">
    <div class="modal-dialog" role="document" style="max-width: 1200px;">
            <div class="modal-header">
                <h5 class="modal-title"><i class="ti ti-list"></i> Seleccionar Servicio — <span id="modalDayLabel">Día</span></h5>
                <button type="button" class="btn-close-modal" onclick="closeAddServiceModal()"><i class="ti ti-x"></i></button>
            </div>
            <div class="modal-body">

                <!-- Filtros -->
                <div class="modal-section-label"><i class="ti ti-search"></i> Filtrar servicios</div>
                <div class="filters-grid" style="grid-template-columns: 1fr 1fr 1fr auto; margin-bottom: 15px;">
                    <div class="form-group">
                        <label for="filter_supplier_list">Proveedor</label>
                        <select class="form-control" id="filter_supplier_list" onchange="filterServiceList()">
                            <option value="">Todos los proveedores</option>
                            @foreach($suppliers ?? [] as $supplier)
                                <option value="{{ $supplier->id_supplier }}">{{ $supplier->supplier_name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="filter_language_list">Mercado</label>
                        <select class="form-control" id="filter_language_list" onchange="filterServiceList()">
                            <option value="">Todos los idiomas</option>
                            @foreach($labels as $label)
                                <option value="{{ $label->id_labels }}">{{ $label->name_labels }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="filter_category_list">Categoría</label>
                        <select class="form-control" id="filter_category_list" onchange="filterServiceList()">
                            <option value="">Todas las categorías</option>
                            @foreach($services->pluck('category')->filter()->unique('id_category')->sortBy('name') as $category)
                                <option value="{{ $category->id_category }}">{{ $category->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="filter-actions" style="align-items: center; padding-top: 20px;">
                        <button type="button" class="btn btn-secondary btn-icon" onclick="filterServiceList()" title="Filtrar"><i class="ti ti-filter"></i></button>
                        <button type="button" class="btn btn-secondary btn-icon" onclick="resetServiceListFilters()" title="Limpiar"><i class="ti ti-x"></i></button>
                    </div>
                </div>

                <!-- Tabla de Servicios -->

                <div style="margin-bottom:8px; display:flex; align-items:center; gap:12px;">
                    <label style="display:flex; align-items:center; gap:8px; font-weight:500;">
                        <input type="checkbox" id="service_as_acc_checkbox" onchange="toggleServiceAsAccommodation(this)"> Agregar como alojamiento
                    </label>
                    <div id="service_as_acc_days" style="display:none; align-items:center; gap:8px;">
                        <label style="font-size:13px; color:var(--qe-ink-600);">Desde</label>
                        <select id="service_acc_day_start" class="form-control">
                            <option value="">Inicio</option>
                            @foreach($quote->quoteDays as $day)
                                <option value="{{ $day->day_number }}">Día {{ $day->day_number }}</option>
                            @endforeach
                        </select>
                        <label style="font-size:13px; color:var(--qe-ink-600);">Hasta</label>
                        <select id="service_acc_day_end" class="form-control">
                            <option value="">Fin</option>
                            @foreach($quote->quoteDays as $day)
                                <option value="{{ $day->day_number }}">Día {{ $day->day_number }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div style="max-height: 400px; overflow-y: auto; border: 1px solid var(--qe-line); border-radius: var(--qe-radius-md);">
                    <table class="table service-catalog-table" style="font-size: 13px; margin-bottom: 0;">
                        <thead style="position: sticky; top: 0; background: #f8fafc; z-index: 5; border-bottom: 2px solid var(--qe-line);">
                            <tr>
                                <th style="padding: 10px 12px; text-align: left; font-weight: 600; color: var(--qe-ink-500);">Servicio</th>
                                <th style="padding: 10px 12px; text-align: left; font-weight: 600; color: var(--qe-ink-500);">Proveedor</th>
                                <th style="padding: 10px 12px; text-align: left; font-weight: 600; color: var(--qe-ink-500);">Categoría</th>
                                <th style="padding: 10px 12px; text-align: left; font-weight: 600; color: var(--qe-ink-500);">Tarifa / subcategoría</th>
                                <th style="padding: 10px 12px; text-align: right; font-weight: 600; color: var(--qe-ink-500);">Precio</th>
                                <th style="padding: 10px 12px; text-align: center; font-weight: 600; color: var(--qe-ink-500);">Acción</th>
                            </tr>
                        </thead>
                        <tbody id="serviceListTableBody">
                            @php
                                $passengersCount = $quote->passengers_count ?: 1;
                            @endphp
                            @foreach($services as $service)
                                                        @php
                                                            $isAcc = false;
                                                            if (isset($service->category) && !empty($service->category->is_accommodation) && $service->category->is_accommodation) {
                                                                $isAcc = true;
                                                            } else {
                                                                $hay = strtolower(($service->name_service ?? '') . ' ' . ($service->supplier->supplier_name ?? ''));
                                                                $keywords = ['hotel','hosped','alojam','room','habitaci','hostel','resort','suite','lodging'];
                                                                foreach ($keywords as $kw) {
                                                                    if (strpos($hay, $kw) !== false) { $isAcc = true; break; }
                                                                }
                                                            }
                                                        @endphp
                                                        <tr class="service-row" data-supplier="{{ $service->id_supplier }}" data-language="{{ $service->id_labels }}" data-category="{{ $service->id_category }}" data-is-accommodation="{{ $isAcc ? '1' : '0' }}" style="border-bottom: 1px solid var(--qe-line);">
                                                            <td style="padding: 10px 12px; font-weight: 500;">{{ $service->name_service }}</td>
                                                            <td style="padding: 10px 12px; color: var(--qe-ink-500);">{{ $service->supplier->supplier_name ?? '-' }}</td>
                                                            <td class="service-category" style="padding: 10px 12px;">{{ $service->category->name ?? '-' }}</td>
                                                            <td style="padding: 10px 12px;">
                                                                <select class="form-control service-tariff-select" id="service-tariff-{{ $service->id_service }}" onchange="updateServiceRowPrice({{ $service->id_service }})">
                                                                    <option value="">Automática según pasajeros</option>
                                                                    @foreach($service->tariffs->groupBy('id_subcategories') as $subcategoryTariffs)
                                                                        @php
                                                                            $matchingTariff = null;
                                                                            foreach ($subcategoryTariffs as $candidateTariff) {
                                                                                $minOk = $candidateTariff->pricing_type === 'flat'
                                                                                    || $candidateTariff->min_people_count === null
                                                                                    || $candidateTariff->min_people_count <= $passengersCount;
                                                                                $maxOk = $candidateTariff->pricing_type === 'flat'
                                                                                    || $candidateTariff->max_people_count === null
                                                                                    || $candidateTariff->max_people_count >= $passengersCount;
                                                                                if ($minOk && $maxOk) {
                                                                                    if (!$matchingTariff || $candidateTariff->price > $matchingTariff->price) {
                                                                                        $matchingTariff = $candidateTariff;
                                                                                    }
                                                                                }
                                                                            }
                                                                            $subcategoryTariff = $matchingTariff ?? $subcategoryTariffs->first();
                                                                            $displayPrice = $matchingTariff ? $subcategoryTariff->price : 0;
                                                                        @endphp
                                                                        <option value="{{ $subcategoryTariff->id_tariff }}" data-price="{{ (float) $displayPrice }}">
                                                                            {{ $subcategoryTariff->subCategory->name ?? 'Sin subcategoría' }}
                                                                        </option>
                                                                    @endforeach
                                                                </select>
                                                            </td>
                                                            <td class="service-price" id="service-price-{{ $service->id_service }}" style="padding: 10px 12px; text-align: right;">
                                                                Automática
                                                            </td>
                                                            <td style="padding: 10px 12px; text-align: center;">
                                                                <button type="button" class="btn btn-primary btn-sm" data-is-accommodation="{{ $isAcc ? '1' : '0' }}" onclick="addServiceFromList({{ $service->id_service }}, '{{ addslashes($service->name_service) }}', this)">
                                                                    <i class="ti ti-plus"></i> Agregar
                                                                </button>
                                                            </td>
                                                        </tr>
                                                        @endforeach
                            @if($services->isEmpty())
                            <tr><td colspan="5" style="padding: 20px; text-align: center; color: var(--qe-ink-300);">No hay servicios disponibles.</td></tr>
                            @endif
                        </tbody>
                    </table>
                </div>
                <div class="field-hint" style="margin-top: 10px;">
                    <i class="ti ti-info-circle"></i> Selecciona un servicio de la lista para agregarlo al día actual.
                </div>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="closeAddServiceModal()">Cancelar</button>
            </div>
    </div>
</div>


@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
const CSRF_TOKEN = '{{ csrf_token() }}';
const CONTACT_STORE_URL = '{{ route("admin.contacts.store") }}';
const ADD_SERVICE_URL = '{{ route("admin.quotes.add-service", $quote->id_quote) }}';
const UPDATE_SERVICE_URL_BASE = '{{ route("admin.quotes.update-service", [$quote->id_quote, "__ID__"]) }}';
const REMOVE_SERVICE_URL_BASE = '{{ route("admin.quotes.remove-service", [$quote->id_quote, "__ID__"]) }}';
const ADD_ACCOMMODATION_TO_DAY_URL = '{{ route("admin.quotes.add-accommodation-to-day", $quote->id_quote) }}';
const REMOVE_ACCOMMODATION_URL_BASE = '{{ route("admin.quotes.remove-accommodation", [$quote->id_quote, "__ID__"]) }}';

function openQuoteContactModal() {
    const clientId = document.getElementById('id_client').value;
    if (!clientId) {
        Swal.fire({ icon: 'warning', title: 'Selecciona un cliente primero', confirmButtonColor: '#6366f1' });
        return;
    }

    document.getElementById('quote_new_contact_id_client').value = clientId;
    document.getElementById('modalQuoteNewContact').style.display = 'flex';
}

function closeQuoteContactModal() {
    document.getElementById('modalQuoteNewContact').style.display = 'none';
    document.getElementById('quoteNewContactForm').reset();
}

function saveQuoteContactModal(event) {
    event.preventDefault();
    const form = document.getElementById('quoteNewContactForm');
    const formData = new FormData(form);
    formData.set('_token', CSRF_TOKEN);

    fetch(CONTACT_STORE_URL, {
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': CSRF_TOKEN, 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
        body: formData
    })
    .then(async response => {
        const data = await response.json().catch(() => ({}));
        if (!response.ok || !data.success) {
            throw new Error(data.message || 'No se pudo crear el contacto.');
        }
        return data;
    })
    .then((data) => {
        closeQuoteContactModal();
        cargarContactos();
        setTimeout(() => {
            const contactsSelect = document.getElementById('id_contacts');
            for (let i = 0; i < contactsSelect.options.length; i++) {
                if (String(contactsSelect.options[i].value) === String(data.contact.id)) {
                    contactsSelect.value = data.contact.id;
                    break;
                }
            }
        }, 300);
        Swal.fire({ icon: 'success', title: 'Contacto guardado', timer: 1200, showConfirmButton: false });
    })
    .catch((error) => {
        Swal.fire({ icon: 'error', title: 'Error', text: error.message || 'No se pudo crear el contacto.', confirmButtonColor: '#ef4444' });
    });
}

// ============================================================
// TABS DE DÍAS
// ============================================================
function switchDayTab(tabEl) {
    document.querySelectorAll('.day-tab').forEach(t => t.classList.remove('active'));
    document.querySelectorAll('.day-panel').forEach(p => p.classList.remove('active'));
    tabEl.classList.add('active');
    document.getElementById(tabEl.dataset.dayPanel).classList.add('active');
}

function syncDetailPrice(detailId) {
    const tariffSelect = document.getElementById(`detail-tariff-${detailId}`);
    const priceInput = document.getElementById(`detail-price-${detailId}`);
    const selectedOption = tariffSelect.options[tariffSelect.selectedIndex];

    if (selectedOption) {
        priceInput.value = Number(selectedOption.dataset.price || 0).toFixed(2);
    }
}

function updateServiceDetail(detailId, options = {}) {
    const silent = options.silent || false;

    const tariffSelect = document.getElementById(`detail-tariff-${detailId}`);
    const priceInput = document.getElementById(`detail-price-${detailId}`);
    const quantityInput = document.getElementById(`detail-quantity-${detailId}`);
    const saveButton = document.querySelector(`#detail-${detailId} .detail-editor button`);
    const originalHtml = saveButton ? saveButton.innerHTML : '';
    const unitPrice = Number(priceInput.value);
    const quantity = Number(quantityInput.value);

    if (!tariffSelect.value || !Number.isFinite(unitPrice) || unitPrice < 0 || !Number.isInteger(quantity) || quantity < 1) {
        if (!silent) {
            Swal.fire({ icon: 'warning', title: 'Datos incompletos', text: 'Selecciona una tarifa, un precio válido y una cantidad mayor que cero.' });
        }
        return;
    }

    if (saveButton) {
        saveButton.disabled = true;
        saveButton.innerHTML = '<i class="ti ti-loader ti-spin"></i>';
    }

    const formData = new FormData();
    formData.append('_token', CSRF_TOKEN);
    formData.append('_method', 'PUT');
    formData.append('id_tariff', tariffSelect.value);
    formData.append('unit_price', unitPrice.toFixed(2));
    formData.append('quantity', quantity);

    fetch(UPDATE_SERVICE_URL_BASE.replace('__ID__', detailId), {
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': CSRF_TOKEN, 'Accept': 'application/json' },
        body: formData
    })
        .then(response => response.json().then(data => ({ ok: response.ok, data })))
        .then(({ ok, data }) => {
            if (!ok || !data.success) {
                throw new Error(data.message || 'No se pudo actualizar el servicio.');
            }

            const subtotalEl = document.getElementById(`detail-subtotal-${detailId}`);
            if (subtotalEl) subtotalEl.textContent = `$ ${data.subtotal}`;

            if (!silent) {
                Swal.fire({ icon: 'success', title: 'Servicio actualizado', timer: 1100, showConfirmButton: false });
            }
        })
        .catch(error => {
            if (!silent) {
                Swal.fire({ icon: 'error', title: 'Error', text: error.message });
            } else {
                console.error('Auto-save error for detail ' + detailId + ':', error.message);
            }
        })
        .finally(() => {
            if (saveButton) {
                saveButton.disabled = false;
                saveButton.innerHTML = originalHtml;
            }
        });
}


let currentDayNumberList = null;

function openAddServiceModal(dayNumber) {
    currentDayNumberList = dayNumber;
    document.getElementById('modalDayLabel').textContent = 'Día ' + dayNumber;

    const modal = document.getElementById('modalAddService');
    modal.style.display = 'flex';
    modal.classList.add('show');
    document.body.style.overflow = 'hidden';

    // Resetear filtros y mostrar todos
    document.getElementById('filter_supplier_list').value = '';
    document.getElementById('filter_language_list').value = '';
    document.getElementById('filter_category_list').value = '';
    filterServiceList();
}

function closeAddServiceModal() {
    const modal = document.getElementById('modalAddService');
    modal.style.display = 'none';
    modal.classList.remove('show');
    document.body.style.overflow = '';
}

function filterServiceList() {
    const supplierId = document.getElementById('filter_supplier_list').value;
    const languageId = document.getElementById('filter_language_list').value;
    const categoryId = document.getElementById('filter_category_list').value;
    const rows = document.querySelectorAll('#serviceListTableBody .service-row');

    rows.forEach(row => {
        const rowSupplier = row.dataset.supplier;
        const rowLanguage = row.dataset.language;
        const rowCategory = row.dataset.category;
        let show = true;

        if (supplierId && rowSupplier !== supplierId) show = false;
        if (languageId && rowLanguage !== languageId) show = false;
        if (categoryId && rowCategory !== categoryId) show = false;

        row.style.display = show ? '' : 'none';
    });
}

function resetServiceListFilters() {
    document.getElementById('filter_supplier_list').value = '';
    document.getElementById('filter_language_list').value = '';
    document.getElementById('filter_category_list').value = '';
    filterServiceList();
}

function toggleServiceAsAccommodation(el) {
    const container = document.getElementById('service_as_acc_days');
    if (!container) return;
    if (el && el.checked) {
        container.style.display = 'flex';
    } else {
        container.style.display = 'none';
        // reset selects
        const s = document.getElementById('service_acc_day_start');
        const e = document.getElementById('service_acc_day_end');
        if (s) s.value = '';
        if (e) e.value = '';
    }
}

function updateServiceRowPrice(serviceId) {
    const select = document.getElementById(`service-tariff-${serviceId}`);
    const priceCell = document.getElementById(`service-price-${serviceId}`);
    const selectedOption = select.options[select.selectedIndex];

    if (selectedOption?.dataset.price) {
        priceCell.textContent = `${Number(selectedOption.dataset.price).toFixed(2)}`;
        return;
    }

    priceCell.textContent = 'Automática';
}

function addServiceFromList(serviceId, serviceName, button) {
    if (!currentDayNumberList) {
        Swal.fire({ icon: 'error', title: 'Error', text: 'No se pudo identificar el día.' });
        return;
    }

    const tariffSelect = document.getElementById(`service-tariff-${serviceId}`);
    const selectedTariff = tariffSelect.options[tariffSelect.selectedIndex];
    const passengersCount = {{ (int) ($quote->passengers_count ?: 1) }};
    const tariffMessage = selectedTariff.value
        ? `Tarifa seleccionada: <strong>${selectedTariff.text}</strong>.<br>Pasajeros: <strong>${passengersCount}</strong>.`
        : `Se usará la tarifa automática según pasajeros.<br>Pasajeros: <strong>${passengersCount}</strong>.`;

    // If the service button indicates this is an accommodation, open the accommodation modal (itinerario modal) prefilled
    const isAccFromButton = button && button.dataset && (button.dataset.isAccommodation === '1' || button.dataset.isAccommodation === 'true');
    if (isAccFromButton) {
        // Open the existing accommodation modal, prefill service and day
        openAccommodationToDayModal(1, currentDayNumberList);
        setTimeout(() => {
            const accSelect = document.getElementById('accommodation_service_select');
            const startSel = document.getElementById('acc_day_start_select');
            const endSel = document.getElementById('acc_day_end_select');
            if (startSel) startSel.value = currentDayNumberList;
            if (endSel) endSel.value = currentDayNumberList;
            if (accSelect) {
                accSelect.value = serviceId;
                // ensure tariffs load
                try { onAccommodationServiceSelect(); } catch(e) { /* ignore */ }
            }
        }, 120);
        return;
    }

    // Fallback: if user used the checkbox flow (legacy), keep that behavior
    const addAsAccCheckbox = document.getElementById('service_as_acc_checkbox');
    const addingAsAccommodation = addAsAccCheckbox && addAsAccCheckbox.checked;

    if (addingAsAccommodation) {
        // When adding as accommodation, ask for confirmation mentioning the selected day range
        const dayStart = document.getElementById('service_acc_day_start').value;
        const dayEnd = document.getElementById('service_acc_day_end').value;

        if (!dayStart || !dayEnd) {
            Swal.fire({ icon: 'warning', title: 'Define el rango', text: 'Selecciona el día inicial y final para el hotel.', confirmButtonColor: '#6366f1' });
            return;
        }
        if (Number(dayStart) > Number(dayEnd)) {
            Swal.fire({ icon: 'warning', title: 'Rango inválido', text: 'El día final debe ser mayor o igual al día inicial.', confirmButtonColor: '#6366f1' });
            return;
        }

        Swal.fire({
            title: 'Agregar como alojamiento',
            html: `¿Agregar <strong>${serviceName}</strong> como alojamiento desde el día <strong>${dayStart}</strong> hasta <strong>${dayEnd}</strong>?<br><small>${tariffMessage}</small>`,
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#6366f1',
            cancelButtonColor: '#94a3b8',
            confirmButtonText: 'Sí, agregar',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (!result.isConfirmed) return;

            const original = button.innerHTML;
            button.disabled = true;
            button.innerHTML = '<i class="ti ti-loader ti-spin"></i>';

            const formData = new FormData();
            formData.append('_token', CSRF_TOKEN);
            // option_number: try to reuse acc_option_number if present, otherwise default to 1
            const optEl = document.getElementById('acc_option_number');
            formData.append('option_number', optEl ? optEl.value : 1);
            formData.append('day_start', dayStart);
            formData.append('day_end', dayEnd);
            formData.append('id_service', serviceId);
            if (selectedTariff && selectedTariff.value) {
                formData.append('id_tariff', selectedTariff.value);
            }

            fetch(ADD_ACCOMMODATION_TO_DAY_URL, {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': CSRF_TOKEN, 'Accept': 'application/json' },
                body: formData
            })
            .then(r => r.json())
            .then(data => {
                button.disabled = false;
                button.innerHTML = original;

                if (data.success) {
                    Swal.fire({ icon: 'success', title: '¡Hotel guardado!', text: data.days_assigned ? `Se asignó a ${data.days_assigned} día(s).` : 'Hotel guardado correctamente.', timer: 1400, showConfirmButton: false })
                        .then(() => window.location.reload());
                } else {
                    Swal.fire({ icon: 'error', title: 'Error', text: data.message || 'Error al guardar el hotel', confirmButtonColor: '#ef4444' });
                }
            })
            .catch(() => {
                button.disabled = false;
                button.innerHTML = original;
                Swal.fire({ icon: 'error', title: 'Error de conexión', confirmButtonColor: '#ef4444' });
            });
        });

        return; // handled as accommodation
    }

    // Default: add as normal service
    Swal.fire({
        title: 'Agregar servicio',
        html: `¿Agregar <strong>${serviceName}</strong> al Día ${currentDayNumberList}?<br><small>${tariffMessage}</small>`,
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#6366f1',
        cancelButtonColor: '#94a3b8',
        confirmButtonText: 'Sí, agregar',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (!result.isConfirmed) return;

        const original = button.innerHTML;
        button.disabled = true;
        button.innerHTML = '<i class="ti ti-loader ti-spin"></i>';

        // Construimos el FormData manualmente como espera el backend
        const formData = new FormData();
        formData.append('_token', CSRF_TOKEN);
        formData.append('day_number', currentDayNumberList);
        formData.append('id_service', serviceId);
        formData.append('quantity', {{ (int) ($quote->passengers_count ?: 1) }});
        if (tariffSelect.value) {
            formData.append('id_tariff', tariffSelect.value);
        }

        fetch(ADD_SERVICE_URL, {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': CSRF_TOKEN, 'Accept': 'application/json' },
            body: formData
        })
        .then(r => r.json())
        .then(data => {
            button.disabled = false;
            button.innerHTML = original;

            if (data.success) {
                Swal.fire({ icon: 'success', title: '¡Servicio agregado!', timer: 1200, showConfirmButton: false })
                    .then(() => window.location.reload());
            } else {
                Swal.fire({ icon: 'error', title: 'Error', text: data.message || 'Error al agregar el servicio', confirmButtonColor: '#ef4444' });
            }
        })
        .catch(() => {
            button.disabled = false;
            button.innerHTML = original;
            Swal.fire({ icon: 'error', title: 'Error de conexión', confirmButtonColor: '#ef4444' });
        });
    });
}

function onServiceSelect() {
    const select = document.getElementById('service_select');
    if (!select.value) {
        resetTariffSection('Selecciona un servicio para ver las tarifas');
        return;
    }
    loadTariffsForService(select.value);
}

function resetTariffSection(hintText) {
    const tariffSection = document.getElementById('tariffSection');
    const tariffSelect = document.getElementById('tariff_select');
    const tariffHint = document.getElementById('tariffHint');

    tariffSection.classList.remove('visible');
    tariffSelect.innerHTML = '<option value="">' + (hintText || 'Selecciona un servicio primero') + '</option>';
    tariffHint.innerHTML = '<i class="ti ti-info-circle"></i><span>' + (hintText || '') + '</span>';
    tariffHint.className = 'tariff-hint';
    updatePricePreviewText('Selecciona un servicio para ver el precio', false);
}

function loadTariffsForService(serviceId) {
    const tariffSection = document.getElementById('tariffSection');
    const tariffSelect = document.getElementById('tariff_select');
    const tariffHint = document.getElementById('tariffHint');

    tariffSection.classList.add('visible');
    tariffSelect.innerHTML = '<option value="">Cargando tarifas...</option>';
    tariffSelect.disabled = true;

    fetch('/cotizaciones/get-tariffs-by-service/' + serviceId)
        .then(r => r.json())
        .then(data => {
            tariffSelect.disabled = false;

            if (data.success && data.data.length > 0) {
                tariffSelect.innerHTML = '';
                const autoOption = document.createElement('option');
                autoOption.value = '';
                autoOption.text = '📊 Automática (según N° pasajeros)';
                tariffSelect.appendChild(autoOption);

                data.data.forEach(function(tariff) {
                    const option = document.createElement('option');
                    option.value = tariff.id_tariff;
                    option.dataset.price = tariff.price;
                    option.text = Number(tariff.price).toFixed(2) + formatPeopleRange(tariff);
                    tariffSelect.appendChild(option);
                });

                tariffHint.innerHTML = '<i class="ti ti-info-circle"></i><span>Selecciona una tarifa o deja "Automática".</span>';
                updatePricePreview();
            } else {
                tariffSelect.innerHTML = '<option value="">⚠️ No hay tarifas activas</option>';
                tariffHint.innerHTML = '<i class="ti ti-alert-circle"></i><span>No podrás agregar este servicio.</span>';
                tariffHint.className = 'tariff-hint warning';
            }
        })
        .catch(() => {
            tariffSelect.disabled = false;
            tariffSelect.innerHTML = '<option value="">Error al cargar tarifas</option>';
        });
}

function formatPeopleRange(tariff) {
    if (tariff.min_people_count !== null && tariff.min_people_count !== undefined) {
        return tariff.max_people_count !== null && tariff.max_people_count !== undefined
            ? ` (${tariff.min_people_count}-${tariff.max_people_count} pax)`
            : ` (${tariff.min_people_count}+ pax)`;
    }
    return '';
}

function updatePricePreviewText(text, hasPrice) {
    document.getElementById('pricePreviewText').innerHTML = text;
    document.getElementById('pricePreview').classList.toggle('has-price', hasPrice);
}

function updatePricePreview() {
    const tariffSelect = document.getElementById('tariff_select');
    const quantityInput = document.getElementById('service_quantity');
    const selectedOption = tariffSelect.options[tariffSelect.selectedIndex];

    if (selectedOption && selectedOption.value && selectedOption.dataset.price) {
        const price = parseFloat(selectedOption.dataset.price);
        const days = parseInt(quantityInput.value, 10) || 1;
        const subtotal = price * days;
        updatePricePreviewText(` ${price.toFixed(2)} × ${days} día${days > 1 ? 's' : ''} = <span class="highlight">${subtotal.toFixed(2)}</span>`, true);
    } else if (tariffSelect.value === '' && tariffSelect.options.length > 1) {
        updatePricePreviewText('📊 La tarifa se calculará automáticamente según el N° de pasajeros', false);
    } else {
        updatePricePreviewText('Selecciona una tarifa para ver el precio', false);
    }
}

function addService() {
    const serviceSelect = document.getElementById('service_select');
    if (!serviceSelect.value) {
        Swal.fire({ icon: 'warning', title: 'Selecciona un servicio', confirmButtonColor: '#6366f1' });
        return;
    }

    const btn = document.getElementById('btnAddService');
    const original = btn.innerHTML;
    btn.disabled = true;
    btn.innerHTML = '<i class="ti ti-loader ti-spin"></i> Agregando...';

    const formData = new FormData(document.getElementById('addServiceForm'));

    fetch(ADD_SERVICE_URL, {
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': CSRF_TOKEN, 'Accept': 'application/json' },
        body: formData
    })
    .then(r => r.json())
    .then(data => {
        btn.disabled = false;
        btn.innerHTML = original;

        if (data.success) {
            Swal.fire({ icon: 'success', title: '¡Servicio agregado!', timer: 1200, showConfirmButton: false })
                .then(() => window.location.reload());
        } else {
            Swal.fire({ icon: 'error', title: 'Error', text: data.message || 'Error al agregar el servicio', confirmButtonColor: '#ef4444' });
        }
    })
    .catch(() => {
        btn.disabled = false;
        btn.innerHTML = original;
        Swal.fire({ icon: 'error', title: 'Error de conexión', confirmButtonColor: '#ef4444' });
    });
}

function removeService(detailId) {
    Swal.fire({
        title: '¿Eliminar servicio?', text: 'Esta acción no se puede deshacer.', icon: 'warning',
        showCancelButton: true, confirmButtonColor: '#991b1b', cancelButtonColor: '#94a3b8',
        confirmButtonText: 'Sí, eliminar', cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (!result.isConfirmed) return;
        const url = REMOVE_SERVICE_URL_BASE.replace('__ID__', detailId);
        fetch(url, { method: 'DELETE', headers: { 'X-CSRF-TOKEN': CSRF_TOKEN, 'Accept': 'application/json' } })
            .then(r => r.json())
            .then(data => {
                if (data.success) {
                    Swal.fire({ icon: 'success', title: '¡Eliminado!', timer: 1200, showConfirmButton: false })
                        .then(() => window.location.reload());
                } else {
                    Swal.fire({ icon: 'error', title: 'Error', text: data.message, confirmButtonColor: '#ef4444' });
                }
            })
            .catch(() => Swal.fire({ icon: 'error', title: 'Error de conexión', confirmButtonColor: '#ef4444' }));
    });
}

// ============================================================
// MODAL HOSPEDAJE PARA UN DÍA ESPECÍFICO (CORREGIDO)
// ============================================================
function filterAccommodationServices() {
    const searchInput = document.getElementById('accommodation_search');
    const categoryInput = document.getElementById('accommodation_category_filter');
    const select = document.getElementById('accommodation_service_select');

    if (!select) {
        return;
    }

    const search = (searchInput ? searchInput.value : '').toLowerCase().trim();
    const selectedCategory = (categoryInput ? categoryInput.value : 'all');
    let visibleCount = 0;

    Array.from(select.options).forEach((option) => {
        if (!option.value) {
            option.hidden = false;
            return;
        }

        const category = (option.dataset.category || '').toLowerCase();
        const text = (option.dataset.search || option.textContent || '').toLowerCase();
        const matchesCategory = selectedCategory === 'all' || category === selectedCategory;
        const matchesSearch = !search || text.includes(search);
        const show = matchesCategory && matchesSearch;

        option.hidden = !show;
        if (show) {
            visibleCount++;
        }
    });

    if (visibleCount === 0) {
        select.value = '';
        resetAccTariffSection();
    }
}

function openAccommodationToDayModal(optionNumber, dayNumber) {
    document.getElementById('acc_option_number').value = optionNumber;
    document.getElementById('modalAccOptionLabel').textContent = optionNumber;

    const dayStartSelect = document.getElementById('acc_day_start_select');
    const dayEndSelect = document.getElementById('acc_day_end_select');
    const searchInput = document.getElementById('accommodation_search');
    const categoryInput = document.getElementById('accommodation_category_filter');

    dayStartSelect.value = '';
    dayEndSelect.value = '';
    if (searchInput) searchInput.value = '';
    if (categoryInput) categoryInput.value = 'all';

    if (dayNumber) {
        dayStartSelect.value = dayNumber;
        dayEndSelect.value = dayNumber;
    }

    document.getElementById('addAccommodationToDayForm').reset();
    document.getElementById('acc_option_number').value = optionNumber;
    if (dayNumber) {
        dayStartSelect.value = dayNumber;
        dayEndSelect.value = dayNumber;
    }
    if (searchInput) searchInput.value = '';
    if (categoryInput) categoryInput.value = 'all';
    filterAccommodationServices();
    resetAccTariffSection();
}



function onAccommodationServiceSelect() {
    const select = document.getElementById('accommodation_service_select');
    if (!select.value) {
        resetAccTariffSection();
        return;
    }
    loadAccommodationTariffs(select.value);
}

function resetAccTariffSection() {
    const section = document.getElementById('accTariffSection');
    const select = document.getElementById('acc_tariff_select');
    section.classList.remove('visible');
    select.innerHTML = '<option value="">Selecciona un hotel primero</option>';
    updateAccommodationToDayPricePreviewText('Selecciona un hotel para ver el precio', false);
}

function loadAccommodationTariffs(serviceId) {
    const section = document.getElementById('accTariffSection');
    const select = document.getElementById('acc_tariff_select');

    section.classList.add('visible');
    select.innerHTML = '<option value="">Cargando tarifas...</option>';
    select.disabled = true;

    fetch('/cotizaciones/get-tariffs-by-service/' + serviceId)
        .then(r => r.json())
        .then(data => {
            select.disabled = false;
            if (data.success && data.data.length > 0) {
                select.innerHTML = '';
                const autoOption = document.createElement('option');
                autoOption.value = '';
                autoOption.text = 'Automática (según N° pasajeros)';
                select.appendChild(autoOption);

                data.data.forEach(function(tariff) {
                    const option = document.createElement('option');
                    option.value = tariff.id_tariff;
                    option.dataset.price = tariff.price;
                    option.text = Number(tariff.price).toFixed(2) + ' / noche';
                    select.appendChild(option);
                });
                updateAccommodationToDayPricePreview();
            } else {
                select.innerHTML = '<option value="">⚠️ No hay tarifas activas para este hotel</option>';
            }
        })
        .catch(() => {
            select.disabled = false;
            select.innerHTML = '<option value="">Error al cargar tarifas</option>';
        });
}

function updateAccommodationToDayPricePreviewText(text, hasPrice) {
    document.getElementById('accPricePreviewText').innerHTML = text;
    document.getElementById('accPricePreview').classList.toggle('has-price', hasPrice);
}

function updateAccommodationToDayPricePreview() {
    const tariffSelect = document.getElementById('acc_tariff_select');
    const selectedOption = tariffSelect.options[tariffSelect.selectedIndex];

    if (selectedOption && selectedOption.value && selectedOption.dataset.price) {
        const price = parseFloat(selectedOption.dataset.price);
        updateAccommodationToDayPricePreviewText(`${price.toFixed(2)} por noche`, true);
    } else {
        updateAccommodationToDayPricePreviewText('Selecciona una tarifa para ver el precio', false);
    }
}

function addAccommodationToDay() {
    const serviceSelect = document.getElementById('accommodation_service_select');
    const dayStartSelect = document.getElementById('acc_day_start_select');
    const dayEndSelect = document.getElementById('acc_day_end_select');
    const dayStart = dayStartSelect.value;
    const dayEnd = dayEndSelect.value;

    if (!serviceSelect.value) {
        Swal.fire({ icon: 'warning', title: 'Selecciona un hotel', confirmButtonColor: '#6366f1' });
        return;
    }

    if (!dayStart || !dayEnd) {
        Swal.fire({ icon: 'warning', title: 'Define el rango de días', text: 'Selecciona el día inicial y final para el hotel.', confirmButtonColor: '#6366f1' });
        return;
    }

    if (Number(dayStart) > Number(dayEnd)) {
        Swal.fire({ icon: 'warning', title: 'Rango inválido', text: 'El día final debe ser mayor o igual al día inicial.', confirmButtonColor: '#6366f1' });
        return;
    }

    const btn = document.getElementById('btnAddAccommodationToDay');
    const original = btn.innerHTML;
    btn.disabled = true;
    btn.innerHTML = '<i class="ti ti-loader ti-spin"></i> Guardando...';

    const formData = new FormData(document.getElementById('addAccommodationToDayForm'));
    formData.set('day_start', dayStart);
    formData.set('day_end', dayEnd);

    fetch(ADD_ACCOMMODATION_TO_DAY_URL, {
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': CSRF_TOKEN, 'Accept': 'application/json' },
        body: formData
    })
    .then(r => r.json())
    .then(data => {
        btn.disabled = false;
        btn.innerHTML = original;

        if (data.success) {
            Swal.fire({ icon: 'success', title: '¡Hotel guardado!', text: data.days_assigned ? `Se asignó a ${data.days_assigned} día(s).` : 'Hotel guardado correctamente.', timer: 1600, showConfirmButton: false })
                .then(() => window.location.reload());
        } else {
            Swal.fire({ icon: 'error', title: 'Error', text: data.message || 'Error al guardar el hotel', confirmButtonColor: '#ef4444' });
        }
    })
    .catch(() => {
        btn.disabled = false;
        btn.innerHTML = original;
        Swal.fire({ icon: 'error', title: 'Error de conexión', confirmButtonColor: '#ef4444' });
    });
}

function removeAccommodation(accommodationId) {
    Swal.fire({
        title: '¿Eliminar hotel?', text: 'Esta acción no se puede deshacer.', icon: 'warning',
        showCancelButton: true, confirmButtonColor: '#991b1b', cancelButtonColor: '#94a3b8',
        confirmButtonText: 'Sí, eliminar', cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (!result.isConfirmed) return;
        const url = REMOVE_ACCOMMODATION_URL_BASE.replace('__ID__', accommodationId);
        fetch(url, { method: 'DELETE', headers: { 'X-CSRF-TOKEN': CSRF_TOKEN, 'Accept': 'application/json' } })
            .then(r => r.json())
            .then(data => {
                if (data.success) {
                    Swal.fire({ icon: 'success', title: '¡Eliminado!', timer: 1200, showConfirmButton: false })
                        .then(() => window.location.reload());
                } else {
                    Swal.fire({ icon: 'error', title: 'Error', text: data.message, confirmButtonColor: '#ef4444' });
                }
            })
            .catch(() => Swal.fire({ icon: 'error', title: 'Error de conexión', confirmButtonColor: '#ef4444' }));
    });
}

function confirmDelete(id, name) {
    Swal.fire({
        title: '¿Eliminar cotización?', html: `Estás a punto de eliminar <strong>${name}</strong>.<br>Esta acción no se puede deshacer.`,
        icon: 'warning', showCancelButton: true, confirmButtonColor: '#991b1b', cancelButtonColor: '#94a3b8',
        confirmButtonText: 'Sí, eliminar', cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = `/cotizaciones/${id}`;
            form.innerHTML = `<input type="hidden" name="_token" value="{{ csrf_token() }}"><input type="hidden" name="_method" value="DELETE">`;
            document.body.appendChild(form);
            form.submit();
        }
    });
}


function toggleAccommodationGroup(id) {
    const el = document.getElementById(id);
    if (!el) return;
    el.style.display = (el.style.display === 'none' || getComputedStyle(el).display === 'none') ? '' : 'none';
}

function removeAccommodationGroup(ids) {
    if (!Array.isArray(ids) || ids.length === 0) return;

    Swal.fire({
        title: '¿Eliminar hotel(s)?',
        html: 'Se eliminarán los hoteles seleccionados. Esta acción no se puede deshacer.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#991b1b',
        cancelButtonColor: '#94a3b8',
        confirmButtonText: 'Sí, eliminar',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (!result.isConfirmed) return;

        const deletes = ids.map(id => {
            const url = REMOVE_ACCOMMODATION_URL_BASE.replace('__ID__', id);
            return fetch(url, { method: 'DELETE', headers: { 'X-CSRF-TOKEN': CSRF_TOKEN, 'Accept': 'application/json' } })
                .then(r => r.json())
                .catch(() => ({ success: false }));
        });

        Promise.all(deletes).then(results => {
            const failed = results.filter(r => !r || !r.success);
            if (failed.length > 0) {
                Swal.fire({ icon: 'error', title: 'Error', text: 'Algunos hoteles no pudieron eliminarse.' });
            } else {
                Swal.fire({ icon: 'success', title: 'Eliminado', timer: 900, showConfirmButton: false }).then(() => window.location.reload());
            }
        });
    });
}

function openAccommodationToDayModal(optionNumber, dayNumber, dayStart, dayEnd) {
    document.getElementById('acc_option_number').value = optionNumber;
    document.getElementById('modalAccOptionLabel').textContent = optionNumber;

    const dayStartSelect = document.getElementById('acc_day_start_select');
    const dayEndSelect = document.getElementById('acc_day_end_select');
    const searchInput = document.getElementById('accommodation_search');
    const categoryInput = document.getElementById('accommodation_category_filter');

    // reset
    dayStartSelect.value = '';
    dayEndSelect.value = '';
    if (searchInput) searchInput.value = '';
    if (categoryInput) categoryInput.value = 'all';

    // Use provided dayStart/dayEnd if present, otherwise fall back to dayNumber
    if (typeof dayStart !== 'undefined' && dayStart !== null) {
        dayStartSelect.value = dayStart;
    } else if (dayNumber) {
        dayStartSelect.value = dayNumber;
    }

    if (typeof dayEnd !== 'undefined' && dayEnd !== null) {
        dayEndSelect.value = dayEnd;
    } else if (dayNumber) {
        dayEndSelect.value = dayNumber;
    }


    document.body.style.overflow = 'hidden';

    document.getElementById('addAccommodationToDayForm').reset();
    document.getElementById('acc_option_number').value = optionNumber;

    // Set again after reset
    if (typeof dayStart !== 'undefined' && dayStart !== null) {
        dayStartSelect.value = dayStart;
    } else if (dayNumber) {
        dayStartSelect.value = dayNumber;
    }

    if (typeof dayEnd !== 'undefined' && dayEnd !== null) {
        dayEndSelect.value = dayEnd;
    } else if (dayNumber) {
        dayEndSelect.value = dayNumber;
    }

    if (searchInput) searchInput.value = '';
    if (categoryInput) categoryInput.value = 'all';
    filterAccommodationServices();
    resetAccTariffSection();
}

function cargarContactos() {
    var clientId = document.getElementById('id_client').value;
    var contactsSelect = document.getElementById('id_contacts');
    var currentValue = contactsSelect.value;
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

            if (currentValue) {
                var matched = Array.from(contactsSelect.options).some(function(option) {
                    return option.value == currentValue;
                });
                if (matched) {
                    contactsSelect.value = currentValue;
                }
            }
        })
        .catch(() => { contactsSelect.innerHTML = '<option value="">Error al cargar</option>'; });
}

document.addEventListener('DOMContentLoaded', function() {
    var quoteNewContactForm = document.getElementById('quoteNewContactForm');
    if (quoteNewContactForm) {
        quoteNewContactForm.addEventListener('submit', saveQuoteContactModal);
    }
    if (document.getElementById('id_client')) {
        cargarContactos();
    }

    // Debounce helper for auto-save
    function debounce(fn, wait) {
        let timer = null;
        return function(...args) {
            if (timer) clearTimeout(timer);
            timer = setTimeout(() => fn.apply(this, args), wait);
        };
    }

    // Auto-save wrapper that calls updateServiceDetail in silent mode
    function autoSaveDetail(detailId) {
        try {
            updateServiceDetail(detailId, { silent: true });
        } catch (e) {
            console.error('autoSaveDetail error', e);
        }
    }

    // Attach listeners to tariff selects so changing category syncs price and triggers save
    document.querySelectorAll('[id^="detail-tariff-"]').forEach(function(el) {
        const parts = el.id.split('-');
        const detailId = parts[parts.length - 1];
        const debounced = debounce(() => autoSaveDetail(detailId), 600);
        el.addEventListener('change', function() {
            try { syncDetailPrice(detailId); } catch(e) { /* ignore */ }
            debounced();
        });
    });

    // Attach listeners to price and quantity inputs to auto-save on input (debounced)
    document.querySelectorAll('[id^="detail-price-"], [id^="detail-quantity-"]').forEach(function(el) {
        const parts = el.id.split('-');
        const detailId = parts[parts.length - 1];
        const debounced = debounce(() => autoSaveDetail(detailId), 700);
        el.addEventListener('input', debounced);
    });

});

document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeAddServiceModal();
        closeAccommodationToDayModal();
        closeQuoteContactModal();
    }
});
</script>
@endpush

@endsection