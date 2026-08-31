<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Cotización {{ $quote->quote_number ?? $quote->id_quote }}</title>
    <style>
        body {
            font-family: Arial, Helvetica, sans-serif;
            color: #0f172a;
            margin: 0;
            padding: 28px;
            background: #ffffff;
        }

        .header {
            display: block;
            border-bottom: 2px solid #e2e8f0;
            padding-bottom: 18px;
            margin-bottom: 20px;
        }

        .title {
            font-size: 24px;
            font-weight: 700;
            margin: 0;
        }

        .subtitle {
            font-size: 12px;
            color: #64748b;
            margin-top: 6px;
        }

        .meta {
            margin-top: 18px;
            display: table;
            width: 100%;
        }

        .meta-row {
            display: table-row;
        }

        .meta-label, .meta-value {
            display: table-cell;
            padding: 6px 0;
            font-size: 12px;
            vertical-align: top;
        }

        .meta-label {
            width: 140px;
            color: #64748b;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.03em;
        }

        .section {
            margin-top: 24px;
        }

        .section-title {
            font-size: 14px;
            font-weight: 700;
            letter-spacing: 0.04em;
            text-transform: uppercase;
            color: #1f2937;
            border-bottom: 1px solid #e2e8f0;
            padding-bottom: 8px;
            margin-bottom: 12px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 11px;
        }

        th, td {
            padding: 8px 8px;
            border-bottom: 1px solid #e2e8f0;
            text-align: left;
            vertical-align: top;
        }

        th {
            background: #f8fafc;
            font-weight: 700;
            color: #334155;
        }

        .totals {
            width: 100%;
            margin-top: 18px;
        }

        .totals td {
            border: none;
            padding: 7px 0;
            font-size: 12px;
        }

        .totals .amount {
            text-align: right;
            font-weight: 700;
        }

        .total-final {
            border-top: 2px solid #0f172a;
            font-size: 16px;
            font-weight: 700;
        }

        .muted {
            color: #64748b;
        }

        .badge {
            display: inline-block;
            font-size: 10px;
            padding: 4px 8px;
            border-radius: 999px;
            background: #e0f2fe;
            color: #075985;
            font-weight: 700;
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="title">Cotización {{ $quote->quote_number ?? 'N/A' }}</div>
        <div class="subtitle">{{ $quote->name ?? 'Sin nombre' }} · {{ ucfirst($quote->status ?? 'draft') }}</div>

        <div class="meta">
            <div class="meta-row">
                <div class="meta-label">Cliente</div>
                <div class="meta-value">{{ $quote->client?->name_client ?? 'N/A' }}</div>
            </div>
            <div class="meta-row">
                <div class="meta-label">Contacto</div>
                <div class="meta-value">{{ $quote->contact ? trim(($quote->contact->name ?? '').' '.($quote->contact->last_names ?? '')) : 'N/A' }}</div>
            </div>
            <div class="meta-row">
                <div class="meta-label">Pasajeros</div>
                <div class="meta-value">{{ $quote->passengers_count ?? '-' }}</div>
            </div>
            <div class="meta-row">
                <div class="meta-label">Fechas</div>
                <div class="meta-value">
                    @if($quote->start_date && $quote->end_date)
                        {{ $quote->start_date->format('d/m/Y') }} - {{ $quote->end_date->format('d/m/Y') }}
                    @else
                        <span class="muted">Sin fechas</span>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="section">
        <div class="section-title">Itinerario</div>
        @if($quoteDays->isEmpty())
            <div class="muted">Sin servicios registrados.</div>
        @else
            @foreach($quoteDays as $day)
                <div style="margin-bottom: 16px;">
                    <div style="font-size: 12px; font-weight: 700; margin-bottom: 8px;">
                        Día {{ $day->day_number }}
                        @if($day->date)
                            <span class="muted">· {{ $day->date->format('d/m/Y') }}</span>
                        @endif
                    </div>
                    <table>
                        <thead>
                            <tr>
                                <th>Servicio</th>
                                <th>Proveedor</th>
                                <th>Cant.</th>
                                <th>Subtotal</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($day->details as $detail)
                                <tr>
                                    <td>{{ $detail->service?->name_service ?? 'Servicio eliminado' }}</td>
                                    <td>{{ $detail->supplier?->supplier_name ?? '-' }}</td>
                                    <td>{{ $detail->quantity ?? 1 }}</td>
                                    <td>$ {{ number_format((float) ($detail->subtotal ?? 0), 2, '.', ',') }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="muted">Sin servicios para este día.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            @endforeach
        @endif
    </div>

    <div class="section">
        <div class="section-title">Hospedaje por opciones</div>
        @php $optionLabels = [1 => 'Opción 1', 2 => 'Opción 2']; @endphp
        @foreach([1, 2] as $optionNumber)
            @php $optionHotels = $optionNumber === 1 ? $option1Hotels : $option2Hotels; @endphp
            <div style="margin-bottom: 18px;">
                <div style="font-size: 12px; font-weight: 700; margin-bottom: 8px;">{{ $optionLabels[$optionNumber] }}</div>
                @if($optionHotels->isEmpty())
                    <div class="muted">Sin hoteles registrados.</div>
                @else
                    <table>
                        <thead>
                            <tr>
                                <th>Día</th>
                                <th>Proveedor</th>
                                <th>Hotel</th>
                                <th>Tipo</th>
                                <th>Subtotal</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($optionHotels as $hotel)
                                <tr>
                                    <td>{{ $hotel->quoteDay?->day_number ? 'Día '.$hotel->quoteDay->day_number : '-' }}</td>
                                    <td>{{ $hotel->supplier?->supplier_name ?? '-' }}</td>
                                    <td>{{ $hotel->service?->name_service ?? 'Hotel eliminado' }}</td>
                                    <td>{{ $hotel->tariff?->subCategory?->name ?? ($hotel->room_type ?? 'Sin tipo') }}</td>
                                    <td>$ {{ number_format((float) ($hotel->subtotal ?? 0), 2, '.', ',') }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @endif
            </div>
        @endforeach
    </div>

    <div class="section">
        <div class="section-title">Totales</div>
        <table class="totals">
            <tr>
                <td>Subtotal</td>
                <td class="amount">$ {{ number_format((float) ($totals['subtotal'] ?? $quote->subtotal ?? 0), 2, '.', ',') }}</td>
            </tr>
            <tr>
                <td>Hoteles</td>
                <td class="amount">$ {{ number_format((float) ($totals['accommodation'] ?? 0), 2, '.', ',') }}</td>
            </tr>
            <tr class="total-final">
                <td>Total</td>
                <td class="amount">$ {{ number_format((float) ($totals['total'] ?? $quote->total ?? 0), 2, '.', ',') }}</td>
            </tr>
        </table>
    </div>

    @if($quote->notes)
        <div class="section">
            <div class="section-title">Observaciones</div>
            <div style="font-size: 12px; line-height: 1.6;">{{ $quote->notes }}</div>
        </div>
    @endif
</body>
</html>
