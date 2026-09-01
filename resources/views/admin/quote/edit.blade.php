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

.form-grid { display: grid; grid-template-columns: repeat(2, 1fr); gap: 16px 20px; }
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
.quote-main-tabs { display: flex; gap: 8px; margin: 4px 0 22px; padding: 5px; border: 1px solid var(--qe-line); border-radius: var(--qe-radius-md); background: var(--qe-surface-muted); }
.quote-main-tab { border: 0; border-radius: 7px; padding: 10px 18px; background: transparent; color: var(--qe-ink-500); font-weight: 700; font-size: 13px; cursor: pointer; display: inline-flex; align-items: center; gap: 7px; }
.quote-main-tab.active { background: var(--qe-surface); color: var(--qe-accent-600); box-shadow: var(--qe-shadow-sm); }
.quote-main-panel { display: none; }
.quote-main-panel.active { display: block; }
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

   <nav class="quote-main-tabs" style="width:100%; justify-content: space-between;" aria-label="Secciones de la cotización">
                        <button  style="width: 33.3%; height: 50px; text-align:center; display:flex; justify-content: center; align-items: center; background-color: #f8f9fa; border: 1px solid #dee2e6;" type="button" class="quote-main-tab" data-quote-tab="information" onclick="switchQuoteTab('information')">
                            <i class="ti ti-info-circle"></i> Información
                        </button>
                        <button active  style="width: 33.3%; height: 50px; text-align:center; display:flex; justify-content: center; align-items: center; background-color: #f8f9fa; border: 1px solid #dee2e6;" type="button" class="quote-main-tab active" data-quote-tab="itinerary" onclick="switchQuoteTab('itinerary')">
                            <i class="ti ti-route"></i> Programa
                        </button>
                        <button  style="width: 33.3%; height: 50px; text-align:center; display:flex; justify-content: center; align-items: center; background-color: #f8f9fa; border: 1px solid #dee2e6;" type="button" class="quote-main-tab" data-quote-tab="documents" onclick="switchQuoteTab('documents')">
                            <i class="ti ti-file-text"></i> Documentos
                        </button>
                    </nav>

<div class="container-fluid quote-edit-page">
    <div class="row">
        <div class="col-12">

            <div class="qe-card">

                <div class="qe-header">
                    <div class="qe-header-left">
                        <a href="{{ route('admin.quotes.index', $quote->id_quote) }}" class="btn btn-secondary">
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
                    <div class="header-actions" style="display:flex; gap:8px; flex-wrap:wrap;">
                        <a href="{{ route('admin.quotes.export.excel', $quote->id_quote) }}" class="btn btn-secondary">
                            <i class="ti ti-file-spreadsheet"></i> Excel detalle
                        </a>
                        <a href="{{ route('admin.quotes.export.excel.tariffs', $quote->id_quote) }}" class="btn btn-secondary">
                            <i class="ti ti-file-spreadsheet"></i> Excel tarifas
                        </a>
                        <a href="{{ route('admin.quotes.export.pdf', $quote->id_quote) }}" class="btn btn-secondary" target="_blank">
                            <i class="ti ti-file-text"></i> PDF
                        </a>
                        <button class="btn btn-danger" onclick="confirmDelete({{ $quote->id_quote }}, '{{ addslashes($quote->name ?? 'Cotización') }}')">
                            <i class="ti ti-trash"></i> Eliminar
                        </button>
                    </div>}
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

                 

                    <div class="quote-main-panel" id="quote-tab-information">
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
                                    <label for="passengers_count">Pasajeros</label>
                                    <input type="number" class="form-control" id="passengers_count" name="passengers_count" value="{{ old('passengers_count', $quote->passengers_count) }}" min="1">
                                </div>
                                
                                <div class="form-group">
                                    <label for="id_language">Idioma de documento <span class="text-danger">*</span></label>
                                    <select class="form-control @error('id_language') is-invalid @enderror" id="id_language" name="id_language" required>
                                        <option value="">Seleccione un idioma</option>
                                        @foreach($languages as $language)
                                            <option value="{{ $language->id_language }}" {{ old('id_language', $quote->id_language) == $language->id_language ? 'selected' : '' }}>
                                                {{ $language->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('id_language') <span class="invalid-feedback">{{ $message }}</span> @enderror
                                </div>


                                <div class="form-group">
                                    <label>Mercado</label>
                                    <input type="text" class="form-control" value="{{ $quote->market?->name_labels ?? 'Sin mercado' }}" readonly>
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
                    </div>

                    <div class="form-actions quote-itinerary-action" style="margin-top: 12px;">
                        <button type="button" class="btn btn-primary" onclick="document.getElementById('quotePricingModal').style.display='flex'">
                            <i class="ti ti-calculator"></i> Cotizar
                        </button>
                    </div>

                    <div class="quote-main-panel" id="quote-tab-documents">
                        <div class="field-section">
                            <div class="field-section__label"><i class="ti ti-file-text"></i> Documentos</div>
                            <div class="documents-grid" style="display:grid; grid-template-columns:repeat(auto-fit,minmax(220px, 1fr)); gap:16px;">
                                <div class="qe-card" style="border:1px solid var(--qe-line); border-radius:var(--qe-radius-lg); background:var(--qe-surface-muted);">
                                    <div class="qe-card-body" style="padding:20px;">
                                        <div style="display:flex; align-items:center; gap:10px; margin-bottom:12px;">
                                            <div class="qe-header-icon" style="width:36px; height:36px; font-size:16px;"><i class="ti ti-file-spreadsheet"></i></div>
                                            <div>
                                                <div style="font-weight:700;">Excel</div>
                                                <small style="color:var(--qe-ink-500);">Exportar plantilla de cotización</small>
                                            </div>
                                        </div>
                                        <a class="btn btn-primary" href="{{ route('admin.quotes.export.excel', $quote->id_quote) }}">
                                            <i class="ti ti-download"></i> Descargar Excel
                                        </a>
                                    </div>
                                </div>
                                {{-- <div class="qe-card" style="border:1px solid var(--qe-line); border-radius:var(--qe-radius-lg); background:var(--qe-surface-muted);">
                                    <div class="qe-card-body" style="padding:20px;">
                                        <div style="display:flex; align-items:center; gap:10px; margin-bottom:12px;">
                                            <div class="qe-header-icon" style="width:36px; height:36px; font-size:16px;"><i class="ti ti-file-text"></i></div>
                                            <div>
                                                <div style="font-weight:700;">PDF</div>
                                                <small style="color:var(--qe-ink-500);">Vista previa y exportación</small>
                                            </div>
                                        </div>
                                        <a class="btn btn-secondary" href="{{ route('admin.quotes.export.pdf', $quote->id_quote) }}" target="_blank">
                                            <i class="ti ti-eye"></i> Ver PDF
                                        </a>
                                    </div>
                                </div> --}}
                                <div class="qe-card" style="border:1px solid var(--qe-line); border-radius:var(--qe-radius-lg); background:var(--qe-surface-muted);">
                                    <div class="qe-card-body" style="padding:20px;">
                                        <div style="display:flex; align-items:center; gap:10px; margin-bottom:12px;">
                                            <div class="qe-header-icon" style="width:36px; height:36px; font-size:16px;"><i class="ti ti-file-type-doc"></i></div>
                                            <div>
                                                <div style="font-weight:700;">DOCX</div>
                                                <small style="color:var(--qe-ink-500);">Itinerario listo para Google Docs</small>
                                            </div>
                                        </div>
                                        <a class="btn btn-secondary" href="{{ route('admin.quotes.export.docx', $quote->id_quote) }}" target="_blank">
                                            <i class="ti ti-download"></i> Descargar DOCX
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div id="quotePricingModal" style="display:none; position:fixed; inset:0; background:rgba(15,23,42,.45); z-index:9999; align-items:center; justify-content:center; padding:20px;">
                        <div style="width:min(460px,100%); background:#fff; border-radius:14px; padding:22px; box-shadow:var(--qe-shadow-lg);">
                            <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:18px;">
                                <h5 style="margin:0;">Definir cotización</h5>
                                <button type="button" class="btn btn-secondary" onclick="document.getElementById('quotePricingModal').style.display='none'">✕</button>
                            </div>
                            <form action="{{ route('admin.quotes.quote', $quote->id_quote) }}" method="POST">
                                @csrf
                                <div class="form-group" style="margin-bottom:16px;">
                                    <label for="quote_passengers_count">Cantidad de pasajeros</label>
                                    <input type="number" class="form-control" id="quote_passengers_count" name="passengers_count" min="1" required value="{{ $quote->passengers_count }}">
                                </div>
                                <div class="form-group">
                                    <label for="quote_pricing_type">Tipo de tarifa</label>
                                    <select class="form-control" id="quote_pricing_type" name="pricing_type" required>
                                        <option value="economico">Regular Económico</option>
                                        <option value="vip">Regular VIP</option>
                                        <option value="privado">Privado</option>
                                    </select>
                                </div>
                                <div class="form-group" style="margin-top:16px;">
                                    <label>Acomodación para todos los hoteles</label>
                                    <div style="display:grid; grid-template-columns:repeat(3, 1fr); gap:10px;">
                                        <div>
                                            <small>Simples (SPL)</small>
                                            <input type="number" class="form-control quote-room-count" name="room_counts[simple]" min="0" value="0">
                                        </div>
                                        <div>
                                            <small>Dobles (DBL)</small>
                                            <input type="number" class="form-control quote-room-count" name="room_counts[doble]" min="0" value="0">
                                        </div>
                                        <div>
                                            <small>Triples (TPL)</small>
                                            <input type="number" class="form-control quote-room-count" name="room_counts[triple]" min="0" value="0">
                                        </div>
                                    </div>
                                    <small style="display:block; color:var(--qe-ink-500); margin-top:6px;">Esta distribución se aplicará a todos los hoteles de las opciones. La asignación individual de pasajeros se hará después.</small>
                                </div>
                                <div class="form-actions" style="justify-content:flex-end;">
                                    <button type="button" class="btn btn-secondary" onclick="document.getElementById('quotePricingModal').style.display='none'">Cancelar</button>
                                    <button type="submit" class="btn btn-primary">Calcular cotización</button>
                                </div>
                            </form>
                        </div>
                    </div>

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

                    <div class="quote-main-panel active" id="quote-tab-itinerary">
                    <div class="itinerary-section">
                        <div class="itinerary-header">
                            <h4 class="title">
                                <i class="ti ti-route"></i> Itinerario
                            </h4>
                        </div>
                        </div>

                        @if($quote->quoteDays->count() > 0)
                            <div class="day-tabs" id="dayTabs">
                                @foreach($quote->quoteDays as $index => $day)
                                    <button type="button" class="day-tab {{ $index === 0 ? 'active' : '' }}" data-day-panel="day-panel-{{ $day->id_quote_day }}" onclick="switchDayTab(this)">
                                        <span>{{ 'Día '.$day->day_number }}</span>
                                    </button>
                                @endforeach
                            </div>
 
                            @foreach($quote->quoteDays as $index => $day)
                                <div class="day-panel {{ $index === 0 ? 'active' : '' }}" id="day-panel-{{ $day->id_quote_day }}">
                                    <div class="day-name-editor" style="display:flex; align-items:center; gap:10px; margin-bottom:14px; flex-wrap:wrap;">
                                        <label for="day-name-{{ $day->id_quote_day }}" style="margin:0; font-size:12px; font-weight:700; color:var(--qe-ink-500); text-transform:uppercase; letter-spacing:.04em;">Nombre del día</label>
                                        <input type="text" id="day-name-{{ $day->id_quote_day }}" class="form-control" value="{{ $day->name ?? '' }}" placeholder="Ej: Llegada / Alojamiento" style="max-width:320px;">
                                        <button type="button" class="btn btn-primary btn-sm" onclick="saveDayName({{ $day->id_quote_day }})">
                                            <i class="ti ti-device-floppy"></i> Guardar
                                        </button>
                                    </div>
                                    <div class="bulk-delete-bar" data-bulk-bar="day-panel-{{ $day->id_quote_day }}" style="display:none; align-items:center; justify-content:space-between; gap:12px; margin-bottom:12px; padding:10px 12px; border:1px solid var(--qe-line); border-radius:10px; background:#f8fafc;">
                                        <label style="display:flex; align-items:center; gap:8px; margin:0; font-weight:600; color:var(--qe-ink-500);">
                                            <input type="checkbox" class="bulk-select-all" data-day-panel="day-panel-{{ $day->id_quote_day }}">
                                            Seleccionar todos
                                        </label>
                                        <button type="button" class="btn btn-danger btn-sm" onclick="deleteSelectedServices('day-panel-{{ $day->id_quote_day }}')">
                                            <i class="ti ti-trash"></i> Eliminar marcados (<span class="bulk-count">0</span>)
                                        </button>
                                    </div>
                                    <div class="day-services-list">
                                        @forelse($day->details as $detail)
                                            @php
                                                $detailService = $detail->service;
                                                $detailTariffGroups = $detailService
                                                    ? collect($detailService->tariffs ?? [])->groupBy('id_subcategories')
                                                    : collect();
                                            @endphp
                                            @php
                                                $quoteHasPassengers = (bool) ($quote->passengers_count ?? 0);
                                            @endphp
                                            <div class="day-service-card" id="detail-{{ $detail->id_detail_quote }}">
                                                <div class="service-info" style="display:flex;flex-direction:row; align-items:center; gap:10px;">
                                                    <input type="checkbox" class="service-select-checkbox" data-detail-id="{{ $detail->id_detail_quote }}" aria-label="Seleccionar servicio para eliminar">
                                                    <div style="display:flex; flex-direction:column; gap:2px;">
                                                        <span class="name">{{ $detail->service->name_service ?? 'Servicio eliminado' }}</span>
                                                        <span class="category" style="font-size:12px; color: red; font-weight:600;">
                                                            {{ $detail->notes ?? '' }}
                                                    </div>
                                                </div>
                                                <div class="actions">
                                                    @if($quoteHasPassengers)
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
                                                                <input type="number" disabled class="form-control quantity-input" id="detail-quantity-{{ $detail->id_detail_quote }}" value="{{ $detail->quantity ?: ($quote->passengers_count ?: 1) }}" min="1" step="1" aria-label="Cantidad de pasajeros" readonly>
                                                            </div>
                                                        </div>
                                                        <span class="service-price" id="detail-subtotal-{{ $detail->id_detail_quote }}">$ {{ number_format($detail->subtotal ?? 0, 2) }}</span>
                                                      
                                                    @else
                                                        <span class="service-price" id="detail-subtotal-{{ $detail->id_detail_quote }}" style="color: var(--qe-ink-400); font-style: italic;">Por cotizar</span>
                                                    @endif
                                                    <button type="button" class="btn btn-secondary btn-sm" onclick="openServiceNotes({{ $detail->id_detail_quote }}, '{{ addslashes($detail->notes ?? '') }}')" title="Agregar nota al servicio">
                                                        <i class="ti ti-note"></i> Nota
                                                    </button>
                                                    <button type="button" class="btn btn-danger btn-icon btn-sm" onclick="removeService({{ $detail->id_detail_quote }})" title="Eliminar">
                                                        <i class="ti ti-trash"></i>
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


                        
                    <div class="accommodation-section">
                        <div class="itinerary-header">
                            <h4 class="title">
                                   <i class="ti ti-bed"></i> Hospedaje — opciones
                                <span style="font-size:12px; font-weight:400; color:var(--qe-ink-500);">
                                    (cada día puede tener hotel diferente)
                                </span>
                            </h4>
                            <button type="button" class="btn btn-primary btn-sm" onclick="openNextAccommodationOption()">
                                <i class="ti ti-plus"></i> Nueva opción
                            </button>
                        </div>

                        @php
                            $additionalAccommodationOptions = $quote->accommodations
                                ->groupBy('option_number')
                                ->filter(fn ($accommodations, $option) => (int) $option > 2);
                        @endphp
                        @foreach($additionalAccommodationOptions as $optionNumber => $optionAccommodations)
                            <div class="accommodation-option has-hotel" id="accOption{{ $optionNumber }}">
                                <div class="option-label"><i class="ti ti-number"></i> Opción {{ $optionNumber }}</div>
                                <div class="accommodation-groups">
                                    @foreach($optionAccommodations->groupBy('id_service') as $serviceAccommodations)
                                        @php $firstAccommodation = $serviceAccommodations->first(); @endphp
                                        <div class="accommodation-group">
                                            <div class="group-summary">
                                                <div style="flex:1; min-width:0;">
                                                    <div style="font-weight:700;">{{ $firstAccommodation->service->name_service ?? 'Hotel eliminado' }}</div>
                                                    <div style="font-size:12px; color:var(--qe-ink-500);">Días {{ $serviceAccommodations->min('quoteDay.day_number') }} al {{ $serviceAccommodations->max('quoteDay.day_number') }}</div>
                                                </div>
                                                <button type="button" class="btn btn-primary btn-sm" onclick="openAccommodationToDayModal({{ $optionNumber }}, null, {{ $serviceAccommodations->min('quoteDay.day_number') }}, {{ $serviceAccommodations->max('quoteDay.day_number') }}, {{ $firstAccommodation->id_service }})">
                                                    <i class="ti ti-edit"></i> Habitaciones
                                                </button>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endforeach

                        <div class="accommodation-grid">
                            <!-- OPCIÓN 1 -->
                            <div class="accommodation-option {{ $quote->accommodationOption1->count() > 0 ? 'has-hotel' : '' }}" id="accOption1">
                                <div class="option-label"><i class="ti ti-number-1"></i> Opción 1</div>
                                @php $hotelsOption1 = $quote->accommodationOption1->sortBy('quoteDay.day_number'); @endphp
                                @if($hotelsOption1->count() > 0)
                                    @php
                                        $groups = [];
                                        foreach($hotelsOption1 as $hotel) {
                                            if (! $hotel->quoteDay || ! $hotel->quoteDay->day_number) {
                                                continue;
                                            }
                                            $sid = $hotel->id_service ?? 's'.$hotel->id_quote_accommodation;
                                            if (! isset($groups[$sid])) {
                                                $groups[$sid] = [
                                                    'service' => $hotel->service,
                                                    'prices' => [],
                                                    'days' => [],
                                                    'dates' => [],
                                                    'accom_ids' => [],
                                                    'room_labels' => [],
                                                ];
                                            }
                                            $groups[$sid]['days'][] = $hotel->quoteDay->day_number;
                                            $groups[$sid]['dates'][$hotel->quoteDay->day_number] = $hotel->quoteDay->date?->format('d/m/Y') ?? 'Fecha no definida';
                                            $groups[$sid]['prices'][] = $hotel->unit_price;
                                            $groups[$sid]['accom_ids'][$hotel->quoteDay->day_number] = $hotel->id_quote_accommodation;
                                            $roomType = $hotel->room_type ?? 'simple';
                                            $roomCount = (int) ($hotel->room_count ?? 1);
                                            $roomLabel = ucfirst($roomType) . ($roomCount > 1 ? ' x'.$roomCount : '');
                                            $groups[$sid]['room_labels'][] = $roomLabel;
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
                                                        <div style="font-weight:700;">{{ $group['service']?->supplier?->supplier_name ?? 'Sin proveedor' }}</div>
                                                        <div style="font-size:12px; color:var(--qe-ink-500); margin-top:2px;">{{ $group['service']?->name_service ?? 'Hotel eliminado' }}</div>
                                                        @php $uniqueRoomLabels = array_values(array_unique($group['room_labels'] ?? [])); @endphp
                                                        @if(!empty($uniqueRoomLabels))
                                                            <div style="font-size:12px; color:var(--qe-ink-500); margin-top:6px;">
                                                                @foreach($uniqueRoomLabels as $label)
                                                                    <span class="badge" style="display:inline-block; margin-right:6px; padding:2px 8px; border-radius:999px; background:#eef2ff; color:#3730a3;">{{ $label }}</span>
                                                                @endforeach
                                                            </div>
                                                        @endif
                                                       
                                                    </div>
                                                    <div style="display:flex; gap:8px; align-items:center;">
                                                        <div style="display:flex; gap:6px;">
                                                            <button type="button" class="btn btn-secondary btn-sm" onclick="toggleAccommodationGroup('accg1_{{ $sid }}')"><i class="ti ti-chevron-down"></i> Ver días</button>
                                                            <button type="button" class="btn btn-primary btn-sm" onclick="openAccommodationToDayModal(1, null, {{ $min }}, {{ $max }}, {{ $group['service']->id_service }})"><i class="ti ti-edit"></i></button>
                                                            <button type="button" class="btn btn-danger btn-sm" onclick="removeAccommodationGroup(@json(array_values($group['accom_ids'])))"><i class="ti ti-trash"></i> </button>
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
                                                                    <span class="hotel-name"><strong>{{ $group['service']?->supplier?->supplier_name ?? 'Sin proveedor' }}</strong><small style="display:block; color:var(--qe-ink-500);">{{ $group['service']?->name_service ?? 'Hotel eliminado' }}</small></span>
                                                                    <span class="hotel-price">$ {{ number_format($group['prices'][array_search($d, $days)] ?? $group['prices'][0], 2) }}</span>
                                                                </div>
                                                                <div class="day-actions">
                                                                    <button type="button" class="btn btn-secondary btn-sm" onclick="openAccommodationToDayModal(1, {{ $d }}, {{ $d }}, {{ $d }}, {{ $group['service']->id_service }})" title="Cambiar habitaciones">
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

                            <div class="accommodation-option {{ $quote->accommodationOption2->count() > 0 ? 'has-hotel' : '' }}" id="accOption2">
                                <div class="option-label"><i class="ti ti-number-2"></i> Opción 2</div>
                                @php $hotelsOption2 = $quote->accommodationOption2->sortBy('quoteDay.day_number'); @endphp
                                @if($hotelsOption2->count() > 0)
                                    @php
                                        $groups2 = [];
                                        foreach($hotelsOption2 as $hotel) {
                                            if (! $hotel->quoteDay || ! $hotel->quoteDay->day_number) {
                                                continue;
                                            }
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
                                            $groups2[$sid]['dates'][$hotel->quoteDay->day_number] = $hotel->quoteDay->date?->format('d/m/Y') ?? 'Fecha no definida';
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
                                                        <div style="font-weight:700;">{{ $group['service']?->supplier?->supplier_name ?? 'Sin proveedor' }}</div>
                                                        <div style="font-size:12px; color:var(--qe-ink-500); margin-top:2px;">{{ $group['service']?->name_service ?? 'Hotel eliminado' }}</div>
                                                        @php $uniqueRoomLabels = array_values(array_unique($group['room_labels'] ?? [])); @endphp
                                                        @if(!empty($uniqueRoomLabels))
                                                            <div style="font-size:12px; color:var(--qe-ink-500); margin-top:6px;">
                                                                @foreach($uniqueRoomLabels as $label)
                                                                    <span class="badge" style="display:inline-block; margin-right:6px; padding:2px 8px; border-radius:999px; background:#eef2ff; color:#3730a3;">{{ $label }}</span>
                                                                @endforeach
                                                            </div>
                                                        @endif
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
                                                            <button type="button" class="btn btn-primary btn-sm" onclick="openAccommodationToDayModal(2, null, {{ $min }}, {{ $max }}, {{ $group['service']->id_service }})"><i class="ti ti-edit"></i> Habitaciones</button>
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
                                                                    <span class="hotel-name"><strong>{{ $group['service']?->supplier?->supplier_name ?? 'Sin proveedor' }}</strong><small style="display:block; color:var(--qe-ink-500);">{{ $group['service']?->name_service ?? 'Hotel eliminado' }}</small></span>
                                                                    <span class="hotel-price">$ {{ number_format($group['prices'][array_search($d, $days)] ?? $group['prices'][0], 2) }}</span>
                                                                </div>
                                                                <div class="day-actions">
                                                                    <button type="button" class="btn btn-secondary btn-sm" onclick="openAccommodationToDayModal(2, {{ $d }}, {{ $d }}, {{ $d }}, {{ $group['service']->id_service }})" title="Cambiar habitaciones">
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
</div>

<div class="modal quote-edit-page" id="modalAddService" tabindex="-1" style="display:none;">
    <div class="modal-dialog" role="document" style="width: min(1200px, calc(100vw - 32px)); max-width: 1200px; height: min(760px, calc(100vh - 32px)); margin: 16px auto;">
            <div class="modal-header">
                <h5 class="modal-title"><i class="ti ti-list"></i> Seleccionar Servicio — <span id="modalDayLabel">Día</span></h5>
                <button type="button" class="btn-close-modal" onclick="closeAddServiceModal()"><i class="ti ti-x"></i></button>
            </div>
            <div class="modal-body">

                <!-- Filtros -->
                <div class="modal-section-label"><i class="ti ti-search"></i> Filtrar servicios</div>
                <div class="filters-grid" style="grid-template-columns: 2fr 1fr 1fr 1fr auto; margin-bottom: 15px;">
                    <div class="form-group">
                        <label for="filter_service_search">Buscar servicio</label>
                        <input type="search" class="form-control" id="filter_service_search" placeholder="Nombre del servicio o proveedor" oninput="filterServiceList()">
                    </div>
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
                            <option value="">Mercado: {{ $quote->market?->name_labels ?? 'No definido' }}</option>
                            @foreach($labels as $label)
                                @if((int) $label->id_labels === (int) $quote->id_labels)
                                    <option value="{{ $label->id_labels }}" selected>{{ $label->name_labels }}</option>
                                @endif
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

                <div style="height: 400px; overflow-y: auto; border: 1px solid var(--qe-line); border-radius: var(--qe-radius-md);">
                    <table class="table service-catalog-table" style="font-size: 13px; margin-bottom: 0;">
                        <thead style="position: sticky; top: 0; background: #f8fafc; z-index: 5; border-bottom: 2px solid var(--qe-line);">
                            <tr>
                                <th style="padding: 10px 12px; text-align: left; font-weight: 600; color: var(--qe-ink-500);">Proveedor</th>
                                <th style="padding: 10px 12px; text-align: left; font-weight: 600; color: var(--qe-ink-500);">Servicio</th>
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
                                                            <td style="padding: 10px 12px; color: var(--qe-ink-500);">{{ $service->supplier->supplier_name ?? '-' }}</td>
                                                            <td style="padding: 10px 12px; font-weight: 500;">{{ $service->name_service }}</td>
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
                <div id="serviceCart" style="margin-top: 14px; padding: 12px; border: 1px solid var(--qe-line); border-radius: var(--qe-radius-md); background: var(--qe-surface-muted);">
                    <strong>Servicios seleccionados (<span id="serviceCartCount">0</span>)</strong>
                    <div id="serviceCartItems" style="display:flex; flex-wrap:wrap; gap:8px; margin-top:8px;"></div>
                    <button type="button" class="btn btn-primary btn-sm" style="margin-top:10px;" onclick="saveServiceCart()">
                        <i class="ti ti-device-floppy"></i> Registrar seleccionados
                    </button>
                </div>
                <div id="servicePagination" style="display:flex; justify-content:center; gap:6px; margin-top:12px;"></div>

            </div>
           
    </div>
</div>

<div class="modal quote-edit-page" id="modalAddAccommodationToDay" tabindex="-1" style="display:none;">
    <div class="modal-dialog" role="document" style="max-width: 720px;">
        <div class="modal-header">
            <h5 class="modal-title"><i class="ti ti-bed"></i> <span id="modalAccActionLabel">Registrar hotel</span> — Opción <span id="modalAccOptionLabel">1</span></h5>
            <button type="button" class="btn-close-modal" onclick="closeAccommodationToDayModal()"><i class="ti ti-x"></i></button>
        </div>
        <div class="modal-body">
            <form id="addAccommodationToDayForm" autocomplete="off">
                <input type="hidden" id="acc_option_number" name="option_number" value="1">

                <div class="modal-section-label" style="margin-bottom:12px;"><i class="ti ti-search"></i> Seleccionar hotel</div>
                <div id="accommodationDateFields" class="detail-edit-grid" style="display:grid; grid-template-columns:1fr 1fr; gap:16px; margin-bottom:18px;">
                    <div class="form-group">
                        <label for="acc_day_start_select">Día inicio</label>
                        <select class="form-control" id="acc_day_start_select" name="day_start">
                            <option value="">Seleccione</option>
                            @foreach($quote->quoteDays as $day)
                                <option value="{{ $day->day_number }}">Día {{ $day->day_number }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="acc_day_end_select">Día fin</label>
                        <select class="form-control" id="acc_day_end_select" name="day_end">
                            <option value="">Seleccione</option>
                            @foreach($quote->quoteDays as $day)
                                <option value="{{ $day->day_number }}">Día {{ $day->day_number }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div id="accommodationServiceField" class="form-group" style="margin-bottom:16px;">
                    <label>Buscar hotel</label>
                    <div style="display:grid; grid-template-columns: 1.5fr 1fr; gap:12px; align-items:end;">
                        <div>
                            <input type="search" id="accommodation_search" class="form-control" placeholder="Buscar por hotel o proveedor" oninput="filterAccommodationCatalog()">
                        </div>
                        <div>
                            <select class="form-control" id="accommodation_category_filter" onchange="filterAccommodationCatalog()">
                                <option value="">Todas las categorías</option>
                                @foreach(($accommodationServices->pluck('category')->filter()->unique('id_category')->sortBy('name') ?? collect()) as $category)
                                    <option value="{{ $category->id_category }}">{{ $category->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <input type="hidden" id="accommodation_service_select" name="id_service" value="">
                </div>

                <div id="accommodationSeasonSelector" class="form-group" style="display:none; margin-bottom:16px;">
                    <label for="accommodation_season_select">Temporada del hotel</label>
                    <select class="form-control" id="accommodation_season_select" name="id_season">
                        <option value="">Seleccione la temporada</option>
                    </select>
                    <div class="field-hint">Si la cotización aún no tiene fechas definidas, elige la temporada manualmente para evitar errores de tarifa.</div>
                </div>

                <div id="accommodationRoomTypes" class="form-group" style="display:none; margin-bottom:16px;">
                    <label>Cantidad de habitaciones por tarifa</label>
                    <div id="accommodationRoomTypeRows" style="display:grid; gap:8px;"></div>
                    <div class="field-hint">Las habitaciones son opcionales al registrar el hotel. Puedes definirlas ahora o después.</div>
                </div>

                <div id="accommodationServiceCatalog" style="max-height: 360px; overflow-y: auto; border: 1px solid var(--qe-line); border-radius: var(--qe-radius-md);">
                    <table class="table service-catalog-table" style="font-size: 13px; margin-bottom: 0;">
                        <thead style="position: sticky; top: 0; background: #f8fafc; z-index: 5; border-bottom: 2px solid var(--qe-line);">
                            <tr>
                                <th style="padding: 10px 12px; text-align: left; font-weight: 600; color: var(--qe-ink-500);">Hotel</th>
                                <th style="padding: 10px 12px; text-align: left; font-weight: 600; color: var(--qe-ink-500);">Proveedor</th>
                                <th style="padding: 10px 12px; text-align: left; font-weight: 600; color: var(--qe-ink-500);">Categoría</th>
                                <th style="padding: 10px 12px; text-align: center; font-weight: 600; color: var(--qe-ink-500);">Acción</th>
                            </tr>
                        </thead>
                        <tbody id="accommodationListTableBody">
                            @foreach($accommodationServices as $service)
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
                                @if($isAcc)
                                    <tr class="accommodation-row" data-service-id="{{ $service->id_service }}" data-category="{{ $service->id_category ?? '' }}" data-search="{{ strtolower($service->name_service ?? '') }} {{ strtolower($service->supplier->supplier_name ?? '') }}" style="border-bottom:1px solid var(--qe-line);">
                                        <td style="padding: 10px 12px; font-weight: 500;"><strong>{{ $service->name_service }}</strong></td>
                                        <td style="padding: 10px 12px; color: var(--qe-ink-500);"><strong>{{ $service->supplier->supplier_name ?? '-' }}</strong></td>
                                        <td style="padding: 10px 12px;">{{ $service->category->name ?? '-' }}</td>
                                        <td style="padding: 10px 12px; text-align: center;">
                                            <button type="button" class="btn btn-primary btn-sm" onclick="selectAccommodationService({{ $service->id_service }}, '{{ addslashes($service->name_service) }}')">
                                                <i class="ti ti-plus"></i> Agregar
                                            </button>
                                        </td>
                                    </tr>
                                @endif
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div id="accommodationPagination" style="display:flex; justify-content:space-between; align-items:center; margin-top:12px; gap:12px; flex-wrap:wrap;">
                    <small id="accommodationPaginationMeta" style="color:var(--qe-ink-500);">Mostrando 0 resultados</small>
                    <div style="display:flex; gap:8px;">
                        <button type="button" class="btn btn-light btn-sm" id="accommodationPrevPage" onclick="changeAccommodationPage(-1)"><i class="ti ti-chevron-left"></i> Anterior</button>
                        <button type="button" class="btn btn-light btn-sm" id="accommodationNextPage" onclick="changeAccommodationPage(1)">Siguiente <i class="ti ti-chevron-right"></i></button>
                    </div>
                </div>

                <div id="accommodationSelectedService" style="display:none; margin-top:14px; padding:10px 12px; border:1px solid var(--qe-line); border-radius:8px; background:#f8fafc;">
                    <div style="font-weight:600;">Servicio: <span id="accommodationSelectedServiceName"></span></div>
                    <div id="accommodationSelectedServiceNights" style="font-size:13px; color:var(--qe-ink-500); margin-top:4px;"></div>
                </div>

                <div id="accPricePreview" class="tariff-preview" style="margin-top:16px; margin-bottom:8px; padding:12px 14px; border-radius:10px; background:#f8fafc; border:1px solid var(--qe-line); color:var(--qe-ink-700);">
                    <span id="accPricePreviewText">Selecciona un hotel de la tabla y luego guarda el rango de días.</span>
                </div>
            </form>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" onclick="closeAccommodationToDayModal()">Cancelar</button>
            <button type="button" class="btn btn-primary" id="btnAddAccommodationToDay" onclick="addAccommodationToDay()"><i class="ti ti-plus"></i> <span id="btnAddAccommodationLabel">Guardar hotel</span></button>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
@php
    $accommodationTariffs = $accommodationServices->mapWithKeys(function ($service) {
        return [
            $service->id_service => $service->tariffs
                ->where('status', 'active')
                ->map(function ($tariff) {
                    return [
                        'id_tariff' => $tariff->id_tariff,
                        'name' => $tariff->subCategory->name ?? 'Sin subcategoría',
                        'price' => (float) $tariff->price,
                        'season_id' => $tariff->id_season,
                        'season_name' => $tariff->season?->name,
                    ];
                })->values(),
        ];
    });

    $accommodationSeasonOptions = $accommodationServices->mapWithKeys(function ($service) {
        $seasonMap = $service->tariffs
            ->where('status', 'active')
            ->whereNotNull('id_season')
            ->map(function ($tariff) {
                $season = $tariff->season;

                return [
                    'id' => (string) $tariff->id_season,
                    'name' => $season?->name ?? 'Temporada '.(int) $tariff->id_season,
                    'start_date' => $season?->start_date?->format('Y-m-d') ?? null,
                    'end_date' => $season?->end_date?->format('Y-m-d') ?? null,
                ];
            })
            ->unique('id')
            ->values()
            ->mapWithKeys(fn ($season) => [(string) $season['id'] => $season]);

        return [$service->id_service => $seasonMap->all()];
    });

    $existingRoomAllocations = $quote->accommodations
        ->filter(fn ($accommodation) => $accommodation->id_tariff && $accommodation->quoteDay)
        ->mapWithKeys(fn ($accommodation) => [
            $accommodation->option_number . ':' . $accommodation->id_service . ':' . $accommodation->quoteDay->day_number . ':' . $accommodation->id_tariff
                => (int) $accommodation->room_count,
        ]);

    $existingAccommodationSeasons = $quote->accommodations
        ->filter(fn ($accommodation) => $accommodation->id_service)
        ->mapWithKeys(fn ($accommodation) => [
            $accommodation->option_number . ':' . $accommodation->id_service => (string) ($accommodation->id_season ?? ($accommodation->tariff?->id_season ?? '')),
        ])
        ->filter(fn ($seasonId) => $seasonId !== '')
        ->all();
@endphp
<script>
function switchQuoteTab(tab) {
    document.querySelectorAll('[data-quote-tab]').forEach((button) => {
        button.classList.toggle('active', button.dataset.quoteTab === tab);
    });
    document.querySelectorAll('.quote-main-panel').forEach((panel) => {
        panel.classList.toggle('active', panel.id === `quote-tab-${tab}`);
    });
    document.querySelectorAll('.quote-itinerary-action').forEach((action) => {
        action.style.display = tab === 'itinerary' ? 'flex' : 'none';
    });
}

switchQuoteTab('itinerary');

const CSRF_TOKEN = '{{ csrf_token() }}';
const CONTACT_STORE_URL = '{{ route("admin.contacts.store") }}';
const ADD_SERVICE_URL = '{{ route("admin.quotes.add-service", $quote->id_quote) }}';
const UPDATE_SERVICE_URL_BASE = '{{ route("admin.quotes.update-service", [$quote->id_quote, "__ID__"]) }}';
const UPDATE_DAY_URL_BASE = '{{ route("admin.quotes.update-day", [$quote->id_quote, "__ID__"]) }}';
const REMOVE_SERVICE_URL_BASE = '{{ route("admin.quotes.remove-service", [$quote->id_quote, "__ID__"]) }}';
const ADD_ACCOMMODATION_TO_DAY_URL = '{{ route("admin.quotes.add-accommodation-to-day", $quote->id_quote) }}';
const REMOVE_ACCOMMODATION_URL_BASE = '{{ route("admin.quotes.remove-accommodation", [$quote->id_quote, "__ID__"]) }}';
const accommodationTariffs = @json($accommodationTariffs);
const accommodationSeasonOptions = @json($accommodationSeasonOptions);
const accommodationDays = @json($quote->quoteDays->pluck('day_number')->values());
const existingRoomAllocations = @json($existingRoomAllocations);
const existingAccommodationSeasons = @json($existingAccommodationSeasons);
const quoteIsCalculated = {{ (int) ((bool) ($quote->passengers_count ?? false)) }};
const quoteHasDates = {{ (int) ((bool) ($quote->start_date && $quote->end_date)) }};
const quoteStartDate = '{{ $quote->start_date ? $quote->start_date->format('Y-m-d') : '' }}';
const quoteEndDate = '{{ $quote->end_date ? $quote->end_date->format('Y-m-d') : '' }}';
let currentOccupancyAccommodationId = null;
let currentOccupancyAccommodationIds = [];
let currentOccupancyLabel = '';
let accommodationEditingExisting = false;

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

function saveDayName(dayId) {
    const input = document.getElementById(`day-name-${dayId}`);
    const name = input ? input.value.trim() : '';
    const url = UPDATE_DAY_URL_BASE.replace('__ID__', dayId);

    const formData = new FormData();
    formData.append('_token', CSRF_TOKEN);
    formData.append('_method', 'PUT');
    formData.append('name', name);

    fetch(url, {
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': CSRF_TOKEN, 'Accept': 'application/json' },
        body: formData
    })
        .then(response => response.json().then(data => ({ ok: response.ok, data })))
        .then(({ ok, data }) => {
            if (!ok || !data.success) {
                throw new Error(data.message || 'No se pudo guardar el nombre del día.');
            }

            const tabButton = document.querySelector(`.day-tab[data-day-panel="day-panel-${dayId}"] span`);
            if (tabButton) {
                tabButton.textContent = data.label || 'Día ' + dayId;
            }

            Swal.fire({ icon: 'success', title: 'Nombre guardado', timer: 1000, showConfirmButton: false });
        })
        .catch(error => {
            Swal.fire({ icon: 'error', title: 'Error', text: error.message });
        });
}

function openServiceNotes(detailId, currentNotes = '') {
    Swal.fire({
        title: 'Nota del servicio',
        input: 'textarea',
        inputLabel: 'Agrega una nota para este servicio',
        inputValue: currentNotes,
        inputAttributes: { maxlength: 600 },
        showCancelButton: true,
        confirmButtonText: 'Guardar nota',
        cancelButtonText: 'Cancelar',
        confirmButtonColor: '#6366f1',
        inputValidator: (value) => {
            if (value !== null && value.length > 600) {
                return 'La nota no puede exceder 600 caracteres.';
            }
            return null;
        }
    }).then((result) => {
        if (!result.isConfirmed) {
            return;
        }

        const noteValue = result.value ?? '';
        const formData = new FormData();
        formData.append('_token', CSRF_TOKEN);
        formData.append('_method', 'PUT');
        formData.append('notes', noteValue);

        fetch(UPDATE_SERVICE_URL_BASE.replace('__ID__', detailId), {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': CSRF_TOKEN, 'Accept': 'application/json' },
            body: formData
        })
            .then(response => response.json().then(data => ({ ok: response.ok, data })))
            .then(({ ok, data }) => {
                if (!ok || !data.success) {
                    throw new Error(data.message || 'No se pudo guardar la nota.');
                }

                const roomMetaNode = document.querySelector(`#detail-${detailId} .service-info small`);
                if (roomMetaNode) {
                    roomMetaNode.textContent = noteValue.trim() || '';
                    roomMetaNode.style.display = noteValue.trim() ? 'block' : 'none';
                }

                Swal.fire({ icon: 'success', title: 'Nota guardada', timer: 1000, showConfirmButton: false });
            })
            .catch(error => {
                Swal.fire({ icon: 'error', title: 'Error', text: error.message });
            });
    });
}

function syncDetailPrice(detailId) {
    const tariffSelect = document.getElementById(`detail-tariff-${detailId}`);
    const priceInput = document.getElementById(`detail-price-${detailId}`);
    const quantityInput = document.getElementById(`detail-quantity-${detailId}`);
    const subtotalEl = document.getElementById(`detail-subtotal-${detailId}`);
    const selectedOption = tariffSelect ? tariffSelect.options[tariffSelect.selectedIndex] : null;

    if (!quoteIsCalculated) {
        if (priceInput) {
            priceInput.value = '';
        }
        if (subtotalEl) {
            subtotalEl.textContent = 'Por cotizar';
            subtotalEl.style.color = 'var(--qe-ink-400)';
            subtotalEl.style.fontStyle = 'italic';
        }
        return;
    }

    if (selectedOption && priceInput) {
        const numericPrice = Number(String(selectedOption.dataset.price || 0).replace(',', '.')) || 0;
        priceInput.value = numericPrice.toFixed(2);
    }

    try {
        const priceRaw = priceInput ? String(priceInput.value || '0').replace(',', '.') : '0';
        const qtyRaw = quantityInput ? String(quantityInput.value || '0').replace(',', '.') : '0';
        const price = Number(priceRaw) || 0;
        const qty = Number(qtyRaw) || 0;
        if (subtotalEl) {
            subtotalEl.textContent = `$ ${(price * qty).toFixed(2)}`;
            subtotalEl.style.color = '';
            subtotalEl.style.fontStyle = '';
        }
    } catch (e) {
        console.error('syncDetailPrice error', e);
    }
}

function updateServiceDetail(detailId, options = {}) {
    const silent = options.silent || false;

    if (!quoteIsCalculated) {
        if (!silent) {
            Swal.fire({
                icon: 'info',
                title: 'Cotización pendiente',
                text: 'Define la cantidad de pasajeros y el tipo de tarifa antes de calcular el precio.'
            });
        }
        return;
    }

    const tariffSelect = document.getElementById(`detail-tariff-${detailId}`);
    const priceInput = document.getElementById(`detail-price-${detailId}`);
    const quantityInput = document.getElementById(`detail-quantity-${detailId}`);
    const saveButton = document.querySelector(`#detail-${detailId} .detail-editor button`);
    const originalHtml = saveButton ? saveButton.innerHTML : '';
    const unitPrice = Number(String(priceInput.value || '0').replace(',', '.'));
    const quantity = parseInt(String(quantityInput.value || '0'), 10) || 0;

    if (!tariffSelect || !tariffSelect.value || !Number.isFinite(unitPrice) || unitPrice < 0 || !Number.isInteger(quantity) || quantity < 1) {
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
    if (tariffSelect && tariffSelect.value) {
        formData.append('id_tariff', tariffSelect.value);
    }
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
            if (priceInput && data.unit_price) {
                priceInput.value = Number(String(data.unit_price).replace(',', '.')).toFixed(2);
            }

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
let serviceCart = [];
let serviceListPage = 1;
let serviceCartSaving = false;
const serviceListPageSize = 10;

function openAddServiceModal(dayNumber) {
    currentDayNumberList = dayNumber;
    document.getElementById('modalDayLabel').textContent = 'Día ' + dayNumber;

    const modal = document.getElementById('modalAddService');
    modal.style.display = 'flex';
    modal.classList.add('show');
    document.body.style.overflow = 'hidden';

    // Resetear filtros y mostrar todos
    document.getElementById('filter_supplier_list').value = '';
    document.getElementById('filter_service_search').value = '';
    document.getElementById('filter_language_list').value = '{{ $quote->id_labels }}';
    document.getElementById('filter_category_list').value = '';
    serviceListPage = 1;
    serviceCart = [];
    renderServiceCart();
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
    const languageId = '{{ $quote->id_labels }}';
    const categoryId = document.getElementById('filter_category_list').value;
    const searchTerm = document.getElementById('filter_service_search').value.trim().toLowerCase();
    const rows = document.querySelectorAll('#serviceListTableBody .service-row');
    const matchingRows = [];

    rows.forEach(row => {
        const rowSupplier = row.dataset.supplier;
        const rowLanguage = row.dataset.language;
        const rowCategory = row.dataset.category;
        const searchableText = row.textContent.toLowerCase();
        let show = true;

        if (supplierId && rowSupplier !== supplierId) show = false;
        if (languageId && rowLanguage !== languageId) show = false;
        if (categoryId && rowCategory !== categoryId) show = false;
        if (searchTerm && !searchableText.includes(searchTerm)) show = false;

        if (show) matchingRows.push(row);
        row.style.display = 'none';
    });

    const totalPages = Math.max(1, Math.ceil(matchingRows.length / serviceListPageSize));
    serviceListPage = Math.min(serviceListPage, totalPages);
    matchingRows.slice((serviceListPage - 1) * serviceListPageSize, serviceListPage * serviceListPageSize)
        .forEach(row => row.style.display = '');
    renderServicePagination(totalPages);
}

function resetServiceListFilters() {
    document.getElementById('filter_supplier_list').value = '';
    document.getElementById('filter_service_search').value = '';
    document.getElementById('filter_language_list').value = '{{ $quote->id_labels }}';
    document.getElementById('filter_category_list').value = '';
    serviceListPage = 1;
    filterServiceList();
}

function renderServicePagination(totalPages) {
    const container = document.getElementById('servicePagination');
    container.innerHTML = '';
    if (totalPages <= 1) return;

    for (let page = 1; page <= totalPages; page++) {
        const button = document.createElement('button');
        button.type = 'button';
        button.className = `btn btn-sm ${page === serviceListPage ? 'btn-primary' : 'btn-secondary'}`;
        button.textContent = page;
        button.onclick = () => {
            serviceListPage = page;
            filterServiceList();
        };
        container.appendChild(button);
    }
}

function renderServiceCart() {
    const container = document.getElementById('serviceCartItems');
    document.getElementById('serviceCartCount').textContent = serviceCart.length;
    container.innerHTML = serviceCart.map((item, index) => `
        <span style="display:inline-flex; align-items:center; gap:6px; padding:6px 9px; background:#fff; border:1px solid var(--qe-line); border-radius:6px;">
            ${escapeHtml(item.name)}${item.tariffName ? ` — ${escapeHtml(item.tariffName)}` : ''}
            <button type="button" class="btn btn-danger btn-sm" style="padding:2px 5px;" onclick="removeFromServiceCart(${index})">×</button>
        </span>
    `).join('');
}

function removeFromServiceCart(index) {
    serviceCart.splice(index, 1);
    renderServiceCart();
}

function saveServiceCart() {
    if (serviceCartSaving) return;
    if (!currentDayNumberList || serviceCart.length === 0) {
        Swal.fire({ icon: 'warning', title: 'Selecciona servicios', text: 'Agrega al menos un servicio al carrito.' });
        return;
    }

    serviceCartSaving = true;
    const saveButton = document.querySelector('#serviceCart button[onclick="saveServiceCart()"]');
    if (saveButton) {
        saveButton.disabled = true;
        saveButton.innerHTML = '<i class="ti ti-loader ti-spin"></i> Registrando...';
    }

    const items = [...serviceCart];
    const saveNext = (index) => {
        if (index >= items.length) {
            serviceCart = [];
            renderServiceCart();
            closeAddServiceModal();
            Swal.fire({ icon: 'success', title: 'Servicios registrados', timer: 1300, showConfirmButton: false })
                .then(() => window.location.reload());
            return;
        }

        const formData = new FormData();
        formData.append('_token', CSRF_TOKEN);
        formData.append('day_number', currentDayNumberList);
        formData.append('id_service', items[index].serviceId);
        formData.append('quantity', {{ (int) ($quote->passengers_count ?: 1) }});
        if (items[index].tariffId) formData.append('id_tariff', items[index].tariffId);

        fetch(ADD_SERVICE_URL, {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': CSRF_TOKEN, 'Accept': 'application/json' },
            body: formData
        })
        .then(response => response.json().then(data => ({ response, data })))
        .then(({ response, data }) => {
            if (!response.ok || !data.success) throw new Error(data.message || 'No se pudo registrar un servicio.');
            saveNext(index + 1);
        })
        .catch(error => {
            serviceCartSaving = false;
            if (saveButton) {
                saveButton.disabled = false;
                saveButton.innerHTML = '<i class="ti ti-device-floppy"></i> Registrar seleccionados';
            }
            Swal.fire({ icon: 'error', title: 'Error', text: error.message });
        });
    };

    saveNext(0);
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
    const isAccFromButton = button && button.dataset && (button.dataset.isAccommodation === '1' || button.dataset.isAccommodation === 'true');
    if (!isAccFromButton) {
        if (serviceCart.some(item => item.serviceId === serviceId)) {
            Swal.fire({ icon: 'info', title: 'Servicio ya seleccionado', timer: 1000, showConfirmButton: false });
            return;
        }
        serviceCart.push({
            serviceId,
            name: serviceName,
            tariffId: tariffSelect.value,
            tariffName: selectedTariff.value ? selectedTariff.text : '',
        });
        renderServiceCart();
        button.innerHTML = '<i class="ti ti-check"></i> Seleccionado';
        button.disabled = true;
        return;
    }
    const passengersCount = {{ (int) ($quote->passengers_count ?: 1) }};
    const tariffMessage = selectedTariff.value
        ? `Tarifa seleccionada: <strong>${selectedTariff.text}</strong>.<br>Pasajeros: <strong>${passengersCount}</strong>.`
        : `Se usará la tarifa automática según pasajeros.<br>Pasajeros: <strong>${passengersCount}</strong>.`;

    // If the service button indicates this is an accommodation, open the accommodation modal (itinerario modal) prefilled
    if (isAccFromButton) {
        // Open the accommodation modal for the complete itinerary range.
        closeAddServiceModal();
        openAccommodationToDayModal(1, null);
        setTimeout(() => {
            const accSelect = document.getElementById('accommodation_service_select');
            const startSel = document.getElementById('acc_day_start_select');
            const endSel = document.getElementById('acc_day_end_select');
            if (startSel && startSel.options.length > 1) startSel.value = startSel.options[1].value;
            if (endSel && endSel.options.length > 1) endSel.value = endSel.options[endSel.options.length - 1].value;
            if (accSelect) {
                accSelect.value = serviceId;
                onAccommodationServiceSelect();
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

function syncBulkSelectionState(dayPanel) {
    if (!dayPanel) return;

    const checkboxes = dayPanel.querySelectorAll('.service-select-checkbox');
    const selected = Array.from(checkboxes).filter(cb => cb.checked);
    const bulkBar = dayPanel.querySelector('.bulk-delete-bar');
    const bulkSelectAll = dayPanel.querySelector('.bulk-select-all');
    const bulkCount = dayPanel.querySelector('.bulk-count');

    if (bulkBar) {
        bulkBar.style.display = selected.length > 0 ? 'flex' : 'none';
    }

    if (bulkSelectAll) {
        bulkSelectAll.checked = checkboxes.length > 0 && selected.length === checkboxes.length;
        bulkSelectAll.indeterminate = selected.length > 0 && selected.length < checkboxes.length;
    }

    if (bulkCount) {
        bulkCount.textContent = selected.length;
    }
}

function deleteSelectedServices(dayPanelId) {
    const dayPanel = document.getElementById(dayPanelId);
    if (!dayPanel) return;

    const selectedIds = Array.from(dayPanel.querySelectorAll('.service-select-checkbox:checked'))
        .map(cb => Number(cb.dataset.detailId))
        .filter(id => Number.isFinite(id));

    if (selectedIds.length === 0) {
        Swal.fire({ icon: 'info', title: 'No hay servicios marcados', text: 'Selecciona al menos un servicio para eliminar.' });
        return;
    }

    Swal.fire({
        title: '¿Eliminar servicios seleccionados?',
        text: `Se eliminarán ${selectedIds.length} servicio(s).`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#991b1b',
        cancelButtonColor: '#94a3b8',
        confirmButtonText: 'Sí, eliminar',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (!result.isConfirmed) return;

        const deleteNext = (index) => {
            if (index >= selectedIds.length) {
                Swal.fire({ icon: 'success', title: 'Servicios eliminados', timer: 1200, showConfirmButton: false })
                    .then(() => window.location.reload());
                return;
            }

            const detailId = selectedIds[index];
            const url = REMOVE_SERVICE_URL_BASE.replace('__ID__', detailId);
            fetch(url, { method: 'DELETE', headers: { 'X-CSRF-TOKEN': CSRF_TOKEN, 'Accept': 'application/json' } })
                .then(r => r.json())
                .then(data => {
                    if (!data.success) {
                        throw new Error(data.message || 'No se pudo eliminar uno de los servicios.');
                    }
                    deleteNext(index + 1);
                })
                .catch((error) => {
                    Swal.fire({ icon: 'error', title: 'Error', text: error.message || 'No se pudo completar la eliminación.', confirmButtonColor: '#ef4444' });
                });
        };

        deleteNext(0);
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

document.addEventListener('change', function (event) {
    const target = event.target;

    if (target && target.classList.contains('service-select-checkbox')) {
        const dayPanel = target.closest('.day-panel');
        syncBulkSelectionState(dayPanel);
    }

    if (target && target.classList.contains('bulk-select-all')) {
        const dayPanel = target.closest('.day-panel');
        const checkboxes = dayPanel ? dayPanel.querySelectorAll('.service-select-checkbox') : [];
        checkboxes.forEach(cb => {
            cb.checked = target.checked;
        });
        syncBulkSelectionState(dayPanel);
    }
});

// ============================================================
// MODAL HOSPEDAJE PARA UN DÍA ESPECÍFICO
// ============================================================
const accommodationPageSize = 10;
let accommodationCatalogPage = 1;

function filterAccommodationCatalog() {
    const searchInput = document.getElementById('accommodation_search');
    const categoryInput = document.getElementById('accommodation_category_filter');
    const rows = document.querySelectorAll('#accommodationListTableBody .accommodation-row');
    const hiddenInput = document.getElementById('accommodation_service_select');

    if (!rows.length) {
        return;
    }

    const search = (searchInput ? searchInput.value : '').toLowerCase().trim();
    const selectedCategory = (categoryInput ? categoryInput.value : '');
    const matchingRows = [];

    rows.forEach((row) => {
        const category = String(row.dataset.category || '').toLowerCase();
        const text = String(row.dataset.search || row.textContent || '').toLowerCase();
        const matchesCategory = !selectedCategory || category === selectedCategory.toLowerCase();
        const matchesSearch = !search || text.includes(search);
        const show = matchesCategory && matchesSearch;
        row.style.display = 'none';

        if (show) {
            matchingRows.push(row);
        }
    });

    const totalPages = Math.max(1, Math.ceil(matchingRows.length / accommodationPageSize));
    accommodationCatalogPage = Math.min(accommodationCatalogPage, totalPages);

    const startIndex = (accommodationCatalogPage - 1) * accommodationPageSize;
    matchingRows.slice(startIndex, startIndex + accommodationPageSize).forEach((row) => {
        row.style.display = '';
    });

    renderAccommodationPagination(matchingRows.length, totalPages);

    if (matchingRows.length === 0) {
        if (hiddenInput) hiddenInput.value = '';
        resetAccTariffSection();
    }
}

function renderAccommodationPagination(totalItems, totalPages) {
    const meta = document.getElementById('accommodationPaginationMeta');
    const nextButton = document.getElementById('accommodationNextPage');
    const prevButton = document.getElementById('accommodationPrevPage');

    if (meta) {
        meta.textContent = totalItems ? `Mostrando ${Math.min(totalItems, (accommodationCatalogPage - 1) * accommodationPageSize + 1)}-${Math.min(totalItems, accommodationCatalogPage * accommodationPageSize)} de ${totalItems} hoteles` : 'Mostrando 0 resultados';
    }

    if (nextButton) nextButton.disabled = totalPages <= 1 || accommodationCatalogPage >= totalPages;
    if (prevButton) prevButton.disabled = totalPages <= 1 || accommodationCatalogPage <= 1;
}

function changeAccommodationPage(direction) {
    const rows = document.querySelectorAll('#accommodationListTableBody .accommodation-row');
    const searchInput = document.getElementById('accommodation_search');
    const categoryInput = document.getElementById('accommodation_category_filter');
    const search = (searchInput ? searchInput.value : '').toLowerCase().trim();
    const selectedCategory = (categoryInput ? categoryInput.value : '').toLowerCase();
    const matchingRows = Array.from(rows).filter(row => {
        const category = String(row.dataset.category || '').toLowerCase();
        const text = String(row.dataset.search || row.textContent || '').toLowerCase();
        return (!selectedCategory || category === selectedCategory) && (!search || text.includes(search));
    });

    const totalPages = Math.max(1, Math.ceil(matchingRows.length / accommodationPageSize));
    accommodationCatalogPage = Math.min(Math.max(1, accommodationCatalogPage + direction), totalPages);
    filterAccommodationCatalog();
}

document.addEventListener('DOMContentLoaded', function () {
    accommodationCatalogPage = 1;
    filterAccommodationCatalog();
});

function closeAccommodationToDayModal() {
    const modal = document.getElementById('modalAddAccommodationToDay');
    if (!modal) return;
    modal.style.display = 'none';
    modal.classList.remove('show');
    document.body.style.overflow = '';

    const form = document.getElementById('addAccommodationToDayForm');
    if (form) form.reset();

    const search = document.getElementById('accommodation_search');
    const category = document.getElementById('accommodation_category_filter');
    const hiddenInput = document.getElementById('accommodation_service_select');
    const seasonSelect = document.getElementById('accommodation_season_select');
    const seasonPanel = document.getElementById('accommodationSeasonSelector');
    if (search) search.value = '';
    if (category) category.value = '';
    if (hiddenInput) hiddenInput.value = '';
    if (seasonSelect) seasonSelect.value = '';
    if (seasonPanel) seasonPanel.style.display = 'none';
    accommodationCatalogPage = 1;
    filterAccommodationCatalog();
}

function selectAccommodationService(serviceId, serviceName) {
    const serviceSelect = document.getElementById('accommodation_service_select');
    if (serviceSelect) {
        serviceSelect.value = serviceId;
        const selectedRow = document.querySelector(`#accommodationListTableBody .accommodation-row[data-service-id="${serviceId}"]`);
        if (selectedRow) {
            document.querySelectorAll('#accommodationListTableBody .accommodation-row').forEach((row) => row.style.background = '');
            selectedRow.style.background = '#eef2ff';
        }
        onAccommodationServiceSelect(serviceName);
    }
}

function resolveAccommodationSeasonFromQuote(serviceId) {
    const seasons = accommodationSeasonOptions[serviceId] || {};
    const entries = Object.entries(seasons || {});
    if (!quoteHasDates || !quoteStartDate || !quoteEndDate || entries.length === 0) {
        return null;
    }

    const start = new Date(quoteStartDate + 'T00:00:00');
    const end = new Date(quoteEndDate + 'T00:00:00');
    const matching = entries.filter(([seasonId, season]) => {
        if (!season || !season.start_date || !season.end_date) {
            return false;
        }
        const seasonStart = new Date(season.start_date + 'T00:00:00');
        const seasonEnd = new Date(season.end_date + 'T00:00:00');
        return seasonStart <= end && seasonEnd >= start;
    });

    if (matching.length === 1) {
        return matching[0][0];
    }

    if (entries.length === 1) {
        return entries[0][0];
    }

    return null;
}

function onAccommodationServiceSelect(serviceName = null) {
    const select = document.getElementById('accommodation_service_select');
    const preview = document.getElementById('accPricePreviewText');
    const typesPanel = document.getElementById('accommodationRoomTypes');
    const typesRows = document.getElementById('accommodationRoomTypeRows');
    const seasonSelect = document.getElementById('accommodation_season_select');
    const seasonPanel = document.getElementById('accommodationSeasonSelector');

    if (!select || !preview) {
        return;
    }

    if (!select.value) {
        preview.innerHTML = 'Selecciona un hotel de la tabla y luego guarda el rango de días.';
        if (typesPanel) typesPanel.style.display = 'none';
        if (typesRows) typesRows.innerHTML = '';
        if (seasonSelect) seasonSelect.value = '';
        if (seasonPanel) seasonPanel.style.display = 'none';
        return;
    }

    const serviceId = Number(select.value);
    const seasons = accommodationSeasonOptions[serviceId] || {};
    const seasonEntries = Object.entries(seasons || {});
    const autoDetectedSeason = resolveAccommodationSeasonFromQuote(serviceId);
    const showManualSeason = seasonEntries.length > 0 && !autoDetectedSeason && (!quoteHasDates || seasonEntries.length > 1);
    const optionKey = `${document.getElementById('acc_option_number')?.value || 1}:${serviceId}`;
    const savedSeason = existingAccommodationSeasons[optionKey] || '';

    if (seasonSelect) {
        seasonSelect.innerHTML = '<option value="">Seleccione la temporada</option>' + seasonEntries.map(([seasonId, season]) => `<option value="${seasonId}">${escapeHtml(season.name || 'Temporada')}</option>`).join('');
        if (savedSeason) {
            seasonSelect.value = String(savedSeason);
        } else if (autoDetectedSeason) {
            seasonSelect.value = String(autoDetectedSeason);
        } else if (seasonEntries.length === 1) {
            seasonSelect.value = seasonEntries[0][0];
        } else {
            seasonSelect.value = '';
        }
    }
    if (seasonPanel) {
        seasonPanel.style.display = showManualSeason ? 'block' : 'none';
    }

    if (seasonSelect && seasonSelect.value) {
        renderAccommodationRoomMatrix();
    }

    if (!accommodationEditingExisting) {
        if (typesPanel) typesPanel.style.display = 'none';
        if (typesRows) typesRows.innerHTML = '';
        preview.innerHTML = '<strong>' + (serviceName || document.querySelector(`#accommodationListTableBody .accommodation-row[data-service-id="${select.value}"]`)?.textContent?.trim() || 'Hotel') + '</strong>: hotel seleccionado. La distribución de pasajeros y habitaciones se configurará después de registrar esta opción.';
        return;
    }

    renderAccommodationRoomMatrix();
    if (typesPanel) typesPanel.style.display = 'block';
    preview.innerHTML = '<strong>' + (serviceName || document.querySelector(`#accommodationListTableBody .accommodation-row[data-service-id="${select.value}"]`)?.textContent?.trim() || 'Hotel') + '</strong>: define cuántas habitaciones usarás por tipo y por día.';
}

function renderAccommodationRoomMatrix() {
    const serviceSelect = document.getElementById('accommodation_service_select');
    const rows = document.getElementById('accommodationRoomTypeRows');
    const seasonSelect = document.getElementById('accommodation_season_select');
    const start = Number(document.getElementById('acc_day_start_select')?.value || 0);
    const end = Number(document.getElementById('acc_day_end_select')?.value || 0);
    if (!serviceSelect || !rows || !start || !end || !serviceSelect.value) {
        if (rows) rows.innerHTML = '';
        return;
    }

    const serviceId = Number(serviceSelect.value);
    const selectedSeason = seasonSelect ? seasonSelect.value : '';
    const allTariffs = accommodationTariffs[serviceId] || [];
    const tariffs = selectedSeason
        ? allTariffs.filter(tariff => String(tariff.season_id ?? '') === String(selectedSeason))
        : allTariffs.filter(tariff => tariff.season_id === null || tariff.season_id === undefined || tariff.season_id === '');

    const days = accommodationDays.filter(day => day >= start && day <= end);
    const columns = ['all', ...days];
    if (Object.keys(accommodationSeasonOptions[serviceId] || {}).length > 0 && !selectedSeason) {
        rows.innerHTML = '<div class="field-hint">Selecciona la temporada del hotel antes de definir habitaciones.</div>';
        return;
    }
    if (tariffs.length === 0) {
        rows.innerHTML = '<div class="field-hint">Este hotel no tiene tarifas activas para la temporada seleccionada. Elige otra temporada o registra nuevas tarifas.</div>';
        return;
    }

    const seasonMeta = selectedSeason ? (accommodationSeasonOptions[serviceId]?.[selectedSeason] || null) : null;
    const seasonName = seasonMeta ? (seasonMeta.name || 'Temporada seleccionada') : 'Tarifas base';
    const priceSummary = tariffs.map(tariff => `<span style="display:inline-flex; align-items:center; gap:6px; padding:4px 8px; border-radius:999px; background:#eef2ff; color:#312e81; font-size:12px; margin-right:6px; margin-bottom:6px;">${escapeHtml(tariff.name)}: $ ${Number(tariff.price).toFixed(2)}</span>`).join('');
    const preview = document.getElementById('accPricePreviewText');
    if (preview) {
        preview.innerHTML = `<strong>${document.querySelector(`#accommodationListTableBody .accommodation-row[data-service-id="${serviceId}"]`)?.textContent?.trim() || 'Hotel'}</strong><br><span style="color:var(--qe-ink-500);">Temporada: ${escapeHtml(seasonName)}</span><br>${priceSummary}`;
    }

    rows.innerHTML = `
        <div style="overflow-x:auto;">
            <table style="width:100%; min-width:560px; border-collapse:collapse;">
                <thead>
                    <tr>
                        <th style="padding:8px; text-align:left;">Tipo de habitación</th>
                        ${columns.map(day => `<th style="padding:8px; text-align:center;">${day === 'all' ? 'Todos los días' : 'Día ' + day}</th>`).join('')}
                    </tr>
                </thead>
                <tbody>
                    ${tariffs.map(tariff => `
                        <tr>
                            <td style="padding:8px; border-top:1px solid var(--qe-line);">
                                <strong>${escapeHtml(tariff.name)}</strong><br>
                                <small style="color:var(--qe-ink-500);">$ ${Number(tariff.price).toFixed(2)}</small>
                            </td>
                            ${columns.map(day => `
                                <td style="padding:8px; border-top:1px solid var(--qe-line);">
                                    <input type="number" min="0" value="0" class="form-control accommodation-room-count"
                                        data-tariff-id="${tariff.id_tariff}" data-day="${day}"
                                        name="room_allocations[${day}][${tariff.id_tariff}]">
                                </td>
                            `).join('')}
                        </tr>
                    `).join('')}
                </tbody>
            </table>
        </div>
    `;

    rows.querySelectorAll('input[data-day="all"]').forEach(input => {
        input.addEventListener('input', () => {
            rows.querySelectorAll(`input[data-tariff-id="${input.dataset.tariffId}"]:not([data-day="all"])`)
                .forEach(dayInput => dayInput.value = input.value);
        });
    });
}

function escapeHtml(value) {
    return String(value ?? '').replace(/[&<>"']/g, character => ({
        '&': '&amp;',
        '<': '&lt;',
        '>': '&gt;',
        '"': '&quot;',
        "'": '&#039;'
    }[character]));
}

function resetAccTariffSection() {
    const preview = document.getElementById('accPricePreviewText');
    const typesPanel = document.getElementById('accommodationRoomTypes');
    const typesRows = document.getElementById('accommodationRoomTypeRows');
    const catalog = document.getElementById('accommodationServiceCatalog');
    const selectedService = document.getElementById('accommodationSelectedService');
    const serviceField = document.getElementById('accommodationServiceField');
    const dateFields = document.getElementById('accommodationDateFields');
    const seasonSelect = document.getElementById('accommodation_season_select');
    const seasonPanel = document.getElementById('accommodationSeasonSelector');
    if (preview) {
        preview.innerHTML = 'Selecciona un hotel de la tabla y luego guarda el rango de días.';
    }
    if (typesPanel) typesPanel.style.display = 'none';
    if (typesRows) typesRows.innerHTML = '';
    if (catalog) catalog.style.display = '';
    if (selectedService) selectedService.style.display = 'none';
    if (serviceField) serviceField.style.display = '';
    if (dateFields) dateFields.style.display = 'grid';
    if (seasonSelect) seasonSelect.value = '';
    if (seasonPanel) seasonPanel.style.display = 'none';
}

function updateAccommodationToDayPricePreviewText(text, hasPrice) {
    const preview = document.getElementById('accPricePreviewText');
    if (!preview) return;
    preview.innerHTML = text;
    const panel = document.getElementById('accPricePreview');
    if (panel) {
        panel.classList.toggle('has-price', hasPrice);
    }
}

function updateAccommodationToDayPricePreview() {
    const preview = document.getElementById('accPricePreviewText');
    if (preview) {
        preview.innerHTML = 'Sin tarifa fija; el hotel se registrará sin precio por ahora.';
    }
}

function addAccommodationToDay() {
    const serviceSelect = document.getElementById('accommodation_service_select');
    const dayStartSelect = document.getElementById('acc_day_start_select');
    const dayEndSelect = document.getElementById('acc_day_end_select');
    const dayStart = dayStartSelect ? dayStartSelect.value : '';
    const dayEnd = dayEndSelect ? dayEndSelect.value : '';

    if (!serviceSelect || !serviceSelect.value) {
        Swal.fire({ icon: 'warning', title: 'Selecciona un hotel', confirmButtonColor: '#6366f1' });
        return;
    }

    if (!dayStart || !dayEnd) {
        Swal.fire({ icon: 'warning', title: 'Define el rango de días', text: 'Selecciona el día inicial y final para el hotel.', confirmButtonColor: '#6366f1' });
        return;
    }

    const seasonSelect = document.getElementById('accommodation_season_select');
    const selectedSeason = seasonSelect ? seasonSelect.value : '';
    const serviceHasSeasons = Object.keys(accommodationSeasonOptions[Number(serviceSelect.value)] || {}).length > 0;
    if (serviceHasSeasons && !selectedSeason) {
        Swal.fire({ icon: 'warning', title: 'Selecciona la temporada', text: 'Este hotel tiene tarifas por temporada. Elige la temporada antes de guardar.', confirmButtonColor: '#6366f1' });
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
    formData.delete('room_type');
    formData.delete('room_count');
    formData.delete('auto_allocate');
    fetch(ADD_ACCOMMODATION_TO_DAY_URL, {
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': CSRF_TOKEN, 'Accept': 'application/json' },
        body: formData
    })
    .then(async response => {
        const rawText = await response.text();
        let data = {};

        if (rawText) {
            try {
                data = JSON.parse(rawText);
            } catch (e) {
                throw new Error(rawText.slice(0, 300) || 'Respuesta inválida del servidor.');
            }
        }

        if (!response.ok) {
            throw new Error(data.message || 'Error del servidor al guardar el hotel.');
        }

        return data;
    })
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
    .catch((error) => {
        btn.disabled = false;
        btn.innerHTML = original;
        Swal.fire({ icon: 'error', title: 'Error', text: error.message || 'Error de conexión', confirmButtonColor: '#ef4444' });
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

function openAccommodationToDayModal(optionNumber, dayNumber, dayStart, dayEnd, serviceId = null) {
    const modal = document.getElementById('modalAddAccommodationToDay');
    const form = document.getElementById('addAccommodationToDayForm');
    const accOptionInput = document.getElementById('acc_option_number');
    const optionLabel = document.getElementById('modalAccOptionLabel');
    const actionLabel = document.getElementById('modalAccActionLabel');
    const saveLabel = document.getElementById('btnAddAccommodationLabel');
    const dayStartSelect = document.getElementById('acc_day_start_select');
    const dayEndSelect = document.getElementById('acc_day_end_select');

    if (!modal) {
        Swal.fire({ icon: 'error', title: 'Error', text: 'El modal de hoteles no está disponible en esta vista.' });
        return;
    }

    accommodationEditingExisting = Boolean(serviceId);
    if (accOptionInput) accOptionInput.value = optionNumber;
    if (optionLabel) optionLabel.textContent = optionNumber;
    if (actionLabel) actionLabel.textContent = serviceId ? 'Modificar habitaciones' : 'Registrar hotel';
    if (saveLabel) saveLabel.textContent = serviceId ? 'Guardar habitaciones' : 'Guardar hotel';
    if (form) form.reset();

    if (dayStartSelect) dayStartSelect.value = '';
    if (dayEndSelect) dayEndSelect.value = '';

    const selectedStart = (typeof dayStart !== 'undefined' && dayStart !== null && dayStart !== '') ? dayStart : dayNumber;
    const selectedEnd = (typeof dayEnd !== 'undefined' && dayEnd !== null && dayEnd !== '') ? dayEnd : dayNumber;

    if (dayStartSelect && selectedStart) dayStartSelect.value = selectedStart;
    if (dayEndSelect && selectedEnd) dayEndSelect.value = selectedEnd;

    if (accOptionInput) accOptionInput.value = optionNumber;

    modal.style.display = 'flex';
    modal.classList.add('show');
    document.body.style.overflow = 'hidden';
    if (serviceId) {
        const serviceSelect = document.getElementById('accommodation_service_select');
        const catalog = document.getElementById('accommodationServiceCatalog');
        const selectedService = document.getElementById('accommodationSelectedService');
        const selectedServiceName = document.getElementById('accommodationSelectedServiceName');
        const selectedServiceNights = document.getElementById('accommodationSelectedServiceNights');
        const serviceField = document.getElementById('accommodationServiceField');
        const dateFields = document.getElementById('accommodationDateFields');
        if (serviceSelect) {
            serviceSelect.value = serviceId;
            const selectedRow = document.querySelector(`#accommodationListTableBody .accommodation-row[data-service-id="${serviceId}"]`);
            const selectedText = selectedRow?.querySelector('td strong')?.textContent?.trim() || 'Hotel seleccionado';
            if (catalog) catalog.style.display = 'none';
            if (serviceField) serviceField.style.display = 'none';
            if (dateFields) dateFields.style.display = 'none';
            if (selectedService) selectedService.style.display = 'block';
            if (selectedServiceName) selectedServiceName.textContent = selectedText;
            if (selectedServiceNights) {
                selectedServiceNights.textContent = `Días ${selectedStart} al ${selectedEnd}`;
            }
            onAccommodationServiceSelect(selectedText);
            prefillAccommodationRoomMatrix(optionNumber, serviceId, selectedStart, selectedEnd);
        }
    } else {
        accommodationCatalogPage = 1;
        filterAccommodationCatalog();
        resetAccTariffSection();
    }
}

function openNextAccommodationOption() {
    const optionIds = Array.from(document.querySelectorAll('[id^="accOption"]'))
        .map(element => Number(element.id.replace('accOption', '')))
        .filter(Number.isFinite);
    const nextOption = (optionIds.length ? Math.max(...optionIds) : 0) + 1;

    openAccommodationToDayModal(nextOption, null);
}

function prefillAccommodationRoomMatrix(optionNumber, serviceId, start, end) {
    const rows = document.getElementById('accommodationRoomTypeRows');
    if (!rows) return;

    rows.querySelectorAll('.accommodation-room-count').forEach(input => {
        const day = input.dataset.day;
        if (day === 'all') return;
        const key = `${optionNumber}:${serviceId}:${day}:${input.dataset.tariffId}`;
        input.value = existingRoomAllocations[key] || 0;
    });

    rows.querySelectorAll('input[data-day="all"]').forEach(input => {
        const tariffId = input.dataset.tariffId;
        const values = Array.from(rows.querySelectorAll(`input[data-tariff-id="${tariffId}"]:not([data-day="all"])`))
            .map(dayInput => Number(dayInput.value) || 0);
        const firstValue = values[0] || 0;
        input.value = values.length > 0 && values.every(value => value === firstValue) ? firstValue : 0;
    });
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
    document.getElementById('acc_day_start_select')?.addEventListener('change', renderAccommodationRoomMatrix);
    document.getElementById('acc_day_end_select')?.addEventListener('change', renderAccommodationRoomMatrix);
    document.getElementById('accommodation_season_select')?.addEventListener('change', renderAccommodationRoomMatrix);
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
        closeOccupancyModal();
    }
});
</script>
@endpush

@endsection