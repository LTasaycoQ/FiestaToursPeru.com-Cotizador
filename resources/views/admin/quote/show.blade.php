@extends('layouts.app')

@section('title', 'Detalle de Cotización')
@section('content')

@push('styles')
<style>
.card { border: 1px solid #e2e8f0; border-radius: 12px; overflow: hidden; background: #fff; }
.card-header { display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem; background: #fff; padding: 1.2rem 1.8rem; border-bottom: 1px solid #e2e8f0; }
.card-body { padding: 1.8rem; }

.badge-status { padding: 6px 16px; border-radius: 20px; font-size: 12px; font-weight: 600; display: inline-flex; align-items: center; gap: 8px; text-transform: capitalize; min-width: 90px; justify-content: center; }
.badge-status .badge-dot { display: inline-block; width: 8px; height: 8px; border-radius: 50%; }
.badge-status.draft { background: #f1f5f9; color: #475569; } .badge-status.draft .badge-dot { background: #94a3b8; }
.badge-status.sent { background: #dbeafe; color: #1e40af; } .badge-status.sent .badge-dot { background: #3b82f6; }
.badge-status.approved { background: #dcfce7; color: #166534; } .badge-status.approved .badge-dot { background: #22c55e; }
.badge-status.rejected { background: #fee2e2; color: #991b1b; } .badge-status.rejected .badge-dot { background: #ef4444; }
.badge-status.expired { background: #fef3c7; color: #92400e; } .badge-status.expired .badge-dot { background: #f59e0b; }
.badge-status.cancelled { background: #f1f5f9; color: #64748b; } .badge-status.cancelled .badge-dot { background: #94a3b8; }

.btn { padding:10px; border-radius: 50px; font-weight: 500; transition: all 0.2s; text-decoration: none; display: inline-flex; align-items: center; gap: 6px; border: none; font-size: 15px; }
.btn-secondary { background: #f1f5f9; color: #475569; border: 1px solid #e2e8f0; }
.btn-secondary:hover { background: #e2e8f0; border-color: #cbd5e1; color: #0f172a; }
.btn-warning { background: #f59e0b; color: #fff; } .btn-warning:hover { background: #d97706; }
.btn-danger { background: #ef4444; color: #fff; } .btn-danger:hover { background: #dc2626; }

.quote-show-tabs { display:flex; gap:8px; flex-wrap:wrap; margin: 0 0 1.25rem; padding:5px; background:#f8fafc; border:1px solid #e2e8f0; border-radius:12px; }
.quote-show-tab { border:0; background:transparent; color:#64748b; padding:10px 16px; border-radius:10px; font-weight:700; font-size:13px; cursor:pointer; }
.quote-show-tab.active { background:#fff; color:#0f172a; box-shadow: 0 1px 2px rgba(15,23,42,.08); }
.quote-show-panel { display:none; }
.quote-show-panel.active { display:block; }

.info-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 1rem; margin-bottom: 1.5rem; }
.info-box { background: #f8fafc; padding: 1rem 1.2rem; border-radius: 10px; border: 1px solid #e2e8f0; }
.info-box .icon { color: #94a3b8; font-size: 14px; margin-right: 4px; }
.info-box .label { color: #94a3b8; text-transform: uppercase; font-weight: 600; font-size: 10px; letter-spacing: 0.5px; display: block; margin-bottom: 2px; }
.info-box .value { font-weight: 600; color: #0f172a; font-size: 15px; margin: 0; }
.info-box .value .sub { font-weight: 400; color: #94a3b8; font-size: 11px; display: block; }

.notes-box { background: #fff; padding: 1rem 1.2rem; border-radius: 10px; border: 1px solid #e2e8f0; margin-bottom: 2rem; }
.notes-box .label { color: #94a3b8; text-transform: uppercase; font-weight: 600; font-size: 10px; letter-spacing: 0.5px; display: block; margin-bottom: 4px; }
.notes-box .text { color: #0f172a; margin: 0; font-size: 14px; line-height: 1.6; }

/* ===== AVISO SIN FECHAS ===== */
.no-dates-banner {
    display: flex;
    align-items: center;
    gap: 12px;
    background: #fffbeb;
    border: 1px solid #fde68a;
    color: #92400e;
    padding: 12px 18px;
    border-radius: 10px;
    margin-bottom: 1.5rem;
    font-size: 13px;
}
.no-dates-banner i { font-size: 20px; flex-shrink: 0; }
.no-dates-banner a { color: #92400e; font-weight: 700; text-decoration: underline; }

/* ===== ITINERARIO POR DÍAS ===== */
.itinerary-block { margin-top: 1.5rem; }
.section-title { font-size: 16px; font-weight: 600; color: #0f172a; margin: 0 0 1rem; display: flex; align-items: center; gap: 8px; flex-wrap: wrap; }
.section-title i { color: #6366f1; }

.day-card { border: 1px solid #e2e8f0; border-radius: 12px; margin-bottom: 12px; overflow: hidden; }
.day-card-header { background: #f8fafc; padding: 10px 16px; border-bottom: 1px solid #e2e8f0; display: flex; justify-content: space-between; align-items: center; font-size: 13px; font-weight: 700; color: #0f172a; }
.day-card-header .day-date { font-weight: 500; color: #94a3b8; font-size: 12px; }
.day-card-header .day-date.pending { color: #d97706; font-style: italic; }
.day-card-body { padding: 12px 16px; }
.day-service-row { display: flex; justify-content: space-between; align-items: center; padding: 8px 0; border-bottom: 1px solid #f1f5f9; gap:10px; }
.day-service-row:last-child { border-bottom: none; }
.day-service-row .name { font-weight: 600; font-size: 13.5px; color: #0f172a; }
.day-service-row .meta { font-size: 11.5px; color: #94a3b8; margin-top: 1px; }
.day-service-row .price { font-weight: 700; color: #166534; font-size: 13.5px; white-space:nowrap; }
.day-card-empty { color: #94a3b8; font-size: 12.5px; text-align: center; padding: 6px 0; }

/* ===== HOSPEDAJE POR DÍA ===== */
.accommodation-block { margin-top: 2rem; }
.accommodation-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-top: 1rem; }
.accommodation-option {
    border: 1.5px solid #e2e8f0;
    border-radius: 12px;
    padding: 18px;
    background: #fff;
}
.accommodation-option.has-hotel { border-color: #6366f1; box-shadow: 0 0 0 3px rgba(99,102,241,.08); }
.accommodation-option .option-title {
    font-size: 12px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: .05em;
    color: #6366f1;
    display: flex;
    align-items: center;
    gap: 6px;
    margin-bottom: 12px;
}

.accommodation-days-list {
    display: flex;
    flex-direction: column;
    gap: 6px;
}
.accommodation-day-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 8px 12px;
    background: #f8fafc;
    border-radius: 8px;
    border: 1px solid #e2e8f0;
    font-size: 12.5px;
}
.accommodation-day-item .day-info {
    display: flex;
    align-items: center;
    gap: 8px;
}
.accommodation-day-item .day-info .day-number {
    font-weight: 700;
    color: #0f172a;
}
.accommodation-day-item .day-info .day-date {
    color: #94a3b8;
    font-size: 11px;
}
.accommodation-day-item .day-info .day-date.pending {
    color: #d97706;
    font-style: italic;
}
.accommodation-day-item .day-hotel {
    display: flex;
    align-items: center;
    gap: 8px;
}
.accommodation-day-item .day-hotel .hotel-name {
    font-weight: 600;
    color: #0f172a;
}
.accommodation-day-item .day-hotel .hotel-price {
    font-weight: 600;
    color: #166534;
    font-size: 12px;
}
.accommodation-empty {
    text-align: center;
    padding: 20px 10px;
    color: #94a3b8;
    font-size: 12.5px;
}
.accommodation-empty i {
    font-size: 26px;
    display: block;
    margin-bottom: 6px;
}

/* ===== COMPARACIÓN DE TOTALES ===== */
.options-compare { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-top: 1rem; }
.option-card { border: 1.5px solid #e2e8f0; border-radius: 12px; padding: 18px; background: #fff; }
.option-card.recommended { border-color: #6366f1; box-shadow: 0 0 0 3px rgba(99,102,241,.08); }
.option-card .option-title { font-size: 12px; font-weight: 700; text-transform: uppercase; letter-spacing: .05em; color: #6366f1; display:flex; align-items:center; gap:6px; margin-bottom: 12px; }
.option-card .divider { border-top: 1px dashed #e2e8f0; margin: 12px 0; }
.option-card .total-row { display:flex; justify-content: space-between; align-items:center; font-size: 15px; font-weight: 800; color: #166534; }
.option-card .subtotal-row { display:flex; justify-content: space-between; font-size: 12.5px; color: #64748b; margin-bottom:4px; }

/* ===== EMPTY STATE ===== */
.empty-state { text-align: center; padding: 2.5rem 1.5rem; }
.empty-state i { font-size: 40px; color: #cbd5e1; display: block; margin-bottom: 10px; }
.empty-state p { color: #94a3b8; font-size: 14px; margin: 0; }
.empty-state a { color: #6366f1; text-decoration: none; font-weight: 600; }
.empty-state a:hover { text-decoration: underline; }

@media (max-width: 992px) {
    .info-grid { grid-template-columns: repeat(2, 1fr); }
    .options-compare { grid-template-columns: 1fr; }
    .accommodation-grid { grid-template-columns: 1fr; }
}

@media (max-width: 768px) {
    .card-header { flex-direction: column; align-items: stretch; gap: 0.8rem; padding: 1rem 1.2rem; }
    .card-header .header-left { flex-direction: column; align-items: stretch; gap: 0.6rem; }
    .card-header .header-actions { justify-content: stretch; }
    .card-header .header-actions .btn { flex: 1; justify-content: center; }
    .info-grid { grid-template-columns: 1fr 1fr; gap: 0.6rem; }
    .day-service-row { flex-direction: column; align-items: flex-start; gap:2px; }
    .accommodation-day-item { flex-direction: column; align-items: flex-start; gap: 4px; }
}

@media (max-width: 480px) {
    .info-grid { grid-template-columns: 1fr; }
    .card-body { padding: 1rem; }
}
</style>
@endpush

<div class="container-fluid">
    <div class="row">
        <div class="col-12">

            <div class="card">

                <!-- ===== HEADER ===== -->
                <div class="card-header">
                    <div class="header-left" style="display:flex; align-items:center; gap:1rem; flex-wrap:wrap;">
                        <a href="{{ route('admin.quotes.index') }}" class="btn btn-secondary">
                            <i class="ti ti-chevron-left"></i>

                        </a>
                        <div>
                            <h3 style="font-size:20px; font-weight:700; color:#0f172a; margin:0; display:flex; align-items:center; gap:10px;">
                                <i class="ti ti-file-percent" style="color:#6366f1;"></i>
                                {{ $quote->name ?? 'Cotización' }}
                            </h3>
                        </div>
                        <span class="badge-status {{ $quote->status }}">
                            <span class="badge-dot"></span>
                            {{ $quote->status_label }}
                        </span>
                    </div>
                    <div class="header-actions" style="display:flex; gap:8px; flex-wrap:wrap;">
                        <a href="{{ route('admin.quotes.export.excel', $quote->id_quote) }}" class="btn btn-secondary">
                            <i class="ti ti-file-spreadsheet"></i> Excel
                        </a>
                        <a href="{{ route('admin.quotes.export.pdf', $quote->id_quote) }}" class="btn btn-secondary" target="_blank">
                            <i class="ti ti-file-text"></i> PDF
                        </a>
                        <a href="{{ route('admin.quotes.export.docx', $quote->id_quote) }}" class="btn btn-secondary" target="_blank">
                            <i class="ti ti-file-type-doc"></i> DOCX
                        </a>
                        <a href="{{ route('admin.quotes.edit', $quote->id_quote) }}" class="btn btn-warning">
                            <i class="ti ti-edit"></i> Editar
                        </a>
                       <div class="info-box avatar_info_box" style="background:transparent; border:none; padding:0; margin:0; display:flex; align-items:center; gap:8px;">
                            @if($quote->user && $quote->user->avatar)
                                @php $filename = basename($quote->user->avatar); @endphp
                                <img class="img_avatar_user" style="width: 50px; height: 50px; border-radius: 50%;" src="{{ route('avatar.show', $filename) }}" alt="{{ $quote->user->name }}" />

                                <div style="display:flex; flex-direction:column; justify-content:center;">
                                    <span style="margin-left: 10px; font-weight: 500; font-size: 14px;">{{ $quote->user ? $quote->user->name : '-' }}</span>
                                    <span style="margin-left: 10px; font-size: 12px; color: #94a3b8;">creador</span>
                                </div>
                            @else
                                 {{ strtoupper(substr($quote->user ? $quote->user->name : '-', 0, 2)) }}
                            @endif
                        </div>
                    </div>
                </div>

                <!-- ===== BODY ===== -->
                <div class="card-body">

                    <!-- ===== AVISO: COTIZACIÓN SIN FECHAS ===== -->
                    @if(!$quote->start_date || !$quote->end_date)
                        <div class="no-dates-banner">
                            <i class="ti ti-calendar-exclamation"></i>
                            <div>
                                Esta cotización se creó <strong>sin fechas</strong> (solo con {{ $quote->quoteDays->count() }} día(s) de itinerario).
                                Cuando el cliente confirme las fechas, asígnalas desde
                                <a href="{{ route('admin.quotes.edit', $quote->id_quote) }}">Editar cotización</a>.
                            </div>
                        </div>
                    @endif

                    <nav class="quote-show-tabs" aria-label="Secciones de la cotización">
                        <button type="button" class="quote-show-tab active" data-show-tab="information" onclick="switchQuoteShowTab('information')">
                            <i class="ti ti-info-circle"></i> Información
                        </button>
                        <button type="button" class="quote-show-tab" data-show-tab="itinerary" onclick="switchQuoteShowTab('itinerary')">
                            <i class="ti ti-route"></i> Itinerario
                        </button>
                        <button type="button" class="quote-show-tab" data-show-tab="documents" onclick="switchQuoteShowTab('documents')">
                            <i class="ti ti-file-text"></i> Documentos
                        </button>
                    </nav>

                    <div class="quote-show-panel active" id="quote-show-information">
                        <div class="info-grid">
                      
                        <div class="info-box">
                            <span class="label"><i class="icon ti ti-user"></i> Cliente</span>
                            <p class="value">{{ $quote->client ? $quote->client->name_client : 'N/A' }}</p>
                        </div>
                        <div class="info-box">
                            <span class="label"><i class="icon ti ti-phone"></i> Contacto</span>
                            <p class="value">{{ $quote->contact ? $quote->contact->name . ' ' . $quote->contact->last_names : 'N/A' }}</p>
                        </div>
                       

                        <div class="info-box">
                            <span class="label"><i class="icon ti ti-users"></i> Pasajeros</span>
                            <p class="value">{{ $quote->passengers_count ?? '-' }}</p>
                        </div>
                        <div class="info-box">
                            <span class="label"><i class="icon ti ti-calendar"></i> Días</span>
                            <p class="value">
                                {{ $quote->quoteDays->count() }} días
                                @if($quote->start_date && $quote->end_date)
                                    <span class="sub">{{ $quote->start_date->format('d/m/Y') }} - {{ $quote->end_date->format('d/m/Y') }}</span>
                                @else
                                    <span class="sub" style="color:#d97706;">Sin fechas asignadas</span>
                                @endif
                            </p>
                        </div>
                        <div class="info-box">
                            <span class="label"><i class="icon ti ti-calendar-event"></i> Inicio</span>
                            <p class="value">{{ $quote->start_date ? $quote->start_date->format('d/m/Y') : 'Sin definir' }}</p>
                        </div>
                        <div class="info-box">
                            <span class="label"><i class="icon ti ti-calendar-event"></i> Fin</span>
                            <p class="value">{{ $quote->end_date ? $quote->end_date->format('d/m/Y') : 'Sin definir' }}</p>
                        </div>
                    </div>

                        @if($quote->notes)
                           <div class="notes-box">
                               <span class="label"><i class="ti ti-notes"></i> Observaciones</span>
                               <p class="text">{{ $quote->notes }}</p>
                           </div>
                        @endif
                    </div>

                    <div class="quote-show-panel" id="quote-show-itinerary">
                        <div class="itinerary-block">
                        <h4 class="section-title">
                            <i class="ti ti-route"></i> Itinerario
                            <span style="font-size:12px; font-weight:400; color:#94a3b8; background:#f1f5f9; padding:2px 10px; border-radius:12px;">
                                {{ $quote->quoteDays->count() }} día(s)
                            </span>
                        </h4>

                        @forelse($quote->quoteDays as $day)
                            <div class="day-card">
                                <div class="day-card-header">
                                    <span>Día {{ $day->day_number }}</span>
                                    @if($day->date)
                                        <span class="day-date">{{ $day->date->format('d/m/Y') }}</span>
                                    @else
                                        <span class="day-date pending"><i class="ti ti-calendar-exclamation"></i> Sin fecha asignada</span>
                                    @endif
                                </div>
                                <div class="day-card-body">
                                    @forelse($day->details as $detail)
                                        <div class="day-service-row">
                                            <div>
                                                <div class="name">{{ $detail->service->name_service ?? 'Servicio eliminado' }}</div>
                                                <div class="meta">
                                                    @if($detail->supplier)
                                                        <i class="ti ti-building"></i> {{ $detail->supplier->supplier_name }}
                                                    @endif
                                                    @if($detail->service && $detail->service->language)
                                                        &nbsp;·&nbsp;<i class="ti ti-language"></i> {{ $detail->service->language->name_language }}
                                                    @endif
                                                    @if($detail->quantity > 1)
                                                        &nbsp;·&nbsp;<i class="ti ti-copy"></i> {{ $detail->quantity }}x
                                                    @endif
                                                </div>
                                            </div>
                                            <span class="price">$ {{ number_format($detail->subtotal ?? 0, 2) }}</span>
                                        </div>
                                    @empty
                                        <div class="day-card-empty">Sin servicios este día</div>
                                    @endforelse
                                </div>
                            </div>
                        @empty
                            <div class="empty-state">
                                <i class="ti ti-calendar-off"></i>
                                <p>Aún no se ha definido el itinerario</p>
                            </div>
                        @endforelse
                        </div>

                        <!-- ============================================================
                             HOSPEDAJE POR DÍA (2 OPCIONES)
                             ============================================================ -->
                        <div class="accommodation-block">
                        <h4 class="section-title">
                            <i class="ti ti-bed"></i> Hospedaje — 2 opciones
                            <span style="font-size:12px; font-weight:400; color:#94a3b8; background:#f1f5f9; padding:2px 10px; border-radius:12px;">
                                {{ $quote->accommodations->count() }} hotel(es)
                            </span>
                        </h4>

                        @php
                            $option1Hotels = $quote->accommodations->where('option_number', 1)->sortBy('quoteDay.day_number');
                            $option2Hotels = $quote->accommodations->where('option_number', 2)->sortBy('quoteDay.day_number');
                            $hasOption1 = $option1Hotels->count() > 0;
                            $hasOption2 = $option2Hotels->count() > 0;
                        @endphp

                        <div class="accommodation-grid">
                            <!-- OPCIÓN 1 -->
                            <div class="accommodation-option {{ $hasOption1 ? 'has-hotel' : '' }}">
                                <div class="option-title"><i class="ti ti-number-1"></i> Opción 1</div>

                                @if($hasOption1)
                                    <div class="accommodation-days-list">
                                        @foreach($option1Hotels as $hotel)
                                            <div class="accommodation-day-item">
                                                <div class="day-info">
                                                    <span class="day-number">Día {{ $hotel->quoteDay->day_number }}</span>
                                                    @if($hotel->quoteDay->date)
                                                        <span class="day-date">{{ $hotel->quoteDay->date->format('d/m/Y') }}</span>
                                                    @else
                                                        <span class="day-date pending">Sin fecha</span>
                                                    @endif
                                                </div>
                                                <div class="day-hotel">
                                                    <span class="hotel-name">{{ $hotel->service->name_service ?? 'Hotel eliminado' }}</span>
                                                    <span class="hotel-price">$ {{ number_format($hotel->unit_price, 2) }}</span>
                                                </div>
                                            </div>
                                        @endforeach
                                        @php $totalOption1 = $option1Hotels->sum('subtotal'); @endphp
                                        <div style="margin-top:8px; padding-top:8px; border-top:1px dashed #e2e8f0; text-align:right; font-weight:700; color:#166534; font-size:13px;">
                                            Total opción 1: $ {{ number_format($totalOption1, 2) }}
                                        </div>
                                    </div>
                                @else
                                    <div class="accommodation-empty">
                                        <i class="ti ti-bed-off"></i>
                                        Sin hoteles asignados
                                    </div>
                                @endif
                            </div>

                            <!-- OPCIÓN 2 -->
                            <div class="accommodation-option {{ $hasOption2 ? 'has-hotel' : '' }}">
                                <div class="option-title"><i class="ti ti-number-2"></i> Opción 2</div>

                                @if($hasOption2)
                                    <div class="accommodation-days-list">
                                        @foreach($option2Hotels as $hotel)
                                            <div class="accommodation-day-item">
                                                <div class="day-info">
                                                    <span class="day-number">Día {{ $hotel->quoteDay->day_number }}</span>
                                                    @if($hotel->quoteDay->date)
                                                        <span class="day-date">{{ $hotel->quoteDay->date->format('d/m/Y') }}</span>
                                                    @else
                                                        <span class="day-date pending">Sin fecha</span>
                                                    @endif
                                                </div>
                                                <div class="day-hotel">
                                                    <span class="hotel-name">{{ $hotel->service->name_service ?? 'Hotel eliminado' }}</span>
                                                    <span class="hotel-price">$ {{ number_format($hotel->unit_price, 2) }}</span>
                                                </div>
                                            </div>
                                        @endforeach
                                        @php $totalOption2 = $option2Hotels->sum('subtotal'); @endphp
                                        <div style="margin-top:8px; padding-top:8px; border-top:1px dashed #e2e8f0; text-align:right; font-weight:700; color:#166534; font-size:13px;">
                                            Total opción 2: $ {{ number_format($totalOption2, 2) }}
                                        </div>
                                    </div>
                                @else
                                    <div class="accommodation-empty">
                                        <i class="ti ti-bed-off"></i>
                                        Sin hoteles asignados
                                    </div>
                                @endif
                            </div>
                        </div>

                        @if(!$hasOption1 && !$hasOption2)
                            <div class="empty-state" style="margin-top: 0.5rem;">
                                <i class="ti ti-bed-off"></i>
                                <p>Aún no se han agregado hoteles. <a href="{{ route('admin.quotes.edit', $quote->id_quote) }}">Agrégalos desde Editar</a>.</p>
                            </div>
                        @endif
                    </div>

                    <!-- ============================================================
                         COMPARACIÓN DE TOTALES
                         ============================================================ -->
                    <div class="itinerary-block">
                        <h4 class="section-title"><i class="ti ti-git-compare"></i> Comparación de totales</h4>

                        @php
                            $itineraryTotal = $quote->quoteDays->flatMap->details->sum('subtotal');
                            $totalOption1 = $option1Hotels->sum('subtotal') ?? 0;
                            $totalOption2 = $option2Hotels->sum('subtotal') ?? 0;
                            $grandTotal1 = $itineraryTotal + $totalOption1;
                            $grandTotal2 = $itineraryTotal + $totalOption2;
                        @endphp

                        <div class="options-compare">
                            <!-- OPCIÓN 1 -->
                            <div class="option-card {{ ($hasOption1 && (!$hasOption2 || $grandTotal1 <= $grandTotal2)) ? 'recommended' : '' }}">
                                <div class="option-title"><i class="ti ti-number-1"></i> Cotización — Opción 1</div>

                                <div class="subtotal-row"><span>Servicios de itinerario</span><span>$ {{ number_format($itineraryTotal, 2) }}</span></div>
                                <div class="subtotal-row"><span>Hospedaje</span><span>$ {{ number_format($totalOption1, 2) }}</span></div>
                                <div class="divider"></div>
                                <div class="total-row"><span>TOTAL</span><span>$ {{ number_format($grandTotal1, 2) }}</span></div>
                            </div>

                            <!-- OPCIÓN 2 -->
                            <div class="option-card {{ ($hasOption2 && $hasOption1 && $grandTotal2 < $grandTotal1) ? 'recommended' : '' }}">
                                <div class="option-title"><i class="ti ti-number-2"></i> Cotización — Opción 2</div>

                                <div class="subtotal-row"><span>Servicios de itinerario</span><span>$ {{ number_format($itineraryTotal, 2) }}</span></div>
                                <div class="subtotal-row"><span>Hospedaje</span><span>$ {{ number_format($totalOption2, 2) }}</span></div>
                                <div class="divider"></div>
                                <div class="total-row"><span>TOTAL</span><span>$ {{ number_format($grandTotal2, 2) }}</span></div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="quote-show-panel" id="quote-show-documents">
                    <div class="documents-grid" style="display:grid; grid-template-columns:repeat(auto-fit,minmax(220px, 1fr)); gap:16px;">
                        <div class="card" style="border:1px solid #e2e8f0; border-radius:12px; overflow:hidden; background:#f8fafc;">
                            <div class="card-body" style="padding:20px;">
                                <div style="display:flex; align-items:center; gap:10px; margin-bottom:12px;">
                                    <div class="qe-header-icon" style="width:36px; height:36px; font-size:16px;"><i class="ti ti-file-spreadsheet"></i></div>
                                    <div>
                                        <div style="font-weight:700;">Excel</div>
                                        <small style="color:#64748b;">Exportar cotización</small>
                                    </div>
                                </div>
                                <a class="btn btn-secondary" href="{{ route('admin.quotes.export.excel', $quote->id_quote) }}">
                                    <i class="ti ti-download"></i> Descargar Excel
                                </a>
                            </div>
                        </div>
                        <div class="card" style="border:1px solid #e2e8f0; border-radius:12px; overflow:hidden; background:#f8fafc;">
                            <div class="card-body" style="padding:20px;">
                                <div style="display:flex; align-items:center; gap:10px; margin-bottom:12px;">
                                    <div class="qe-header-icon" style="width:36px; height:36px; font-size:16px;"><i class="ti ti-file-text"></i></div>
                                    <div>
                                        <div style="font-weight:700;">PDF</div>
                                        <small style="color:#64748b;">Vista previa del documento</small>
                                    </div>
                                </div>
                                <a class="btn btn-secondary" href="{{ route('admin.quotes.export.pdf', $quote->id_quote) }}" target="_blank">
                                    <i class="ti ti-eye"></i> Ver PDF
                                </a>
                            </div>
                        </div>
                        <div class="card" style="border:1px solid #e2e8f0; border-radius:12px; overflow:hidden; background:#f8fafc;">
                            <div class="card-body" style="padding:20px;">
                                <div style="display:flex; align-items:center; gap:10px; margin-bottom:12px;">
                                    <div class="qe-header-icon" style="width:36px; height:36px; font-size:16px;"><i class="ti ti-file-type-doc"></i></div>
                                    <div>
                                        <div style="font-weight:700;">DOCX</div>
                                        <small style="color:#64748b;">Itinerario para Google Docs</small>
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

        </div>
    </div>
</div>


<script>
    function switchQuoteShowTab(tab) {
        document.querySelectorAll('.quote-show-tab').forEach((button) => {
            button.classList.toggle('active', button.dataset.showTab === tab);
        });

        document.querySelectorAll('.quote-show-panel').forEach((panel) => {
            panel.classList.toggle('active', panel.id === `quote-show-${tab}`);
        });
    }
</script>
@endsection