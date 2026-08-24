{{-- admin.clients.export-pdf --}}
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title>Listado de Clientes</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Arial', 'Helvetica', sans-serif;
            font-size: 9.5px;
            color: #333333;
            background: #ffffff;
            padding: 12px 18px;
            line-height: 1.5;
        }
        .container { max-width: 100%; margin: 0 auto; }

        /* ── HEADER ── */
        .header {
            border-bottom: 3px solid #1a1a2e;
            padding-bottom: 10px;
            margin-bottom: 14px;
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
        }
        .header h1 {
            font-size: 16px;
            font-weight: 700;
            color: #1a1a2e;
            margin: 0;
            letter-spacing: 1px;
        }
        .header .subtitle {
            font-size: 8px;
            color: #888888;
            text-align: right;
            line-height: 1.4;
        }

        /* ── RESUMEN EN UNA SOLA FILA ── */
        .summary-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 16px;
            border: 1px solid #e0e0e0;
        }
        .summary-table td {
            padding: 8px 12px;
            text-align: center;
            vertical-align: middle;
            border-right: 1px solid #e8e8e8;
        }
        .summary-table td:last-child { border-right: none; }
        .summary-table .label {
            display: block;
            font-size: 7.5px;
            font-weight: 700;
            color: #888888;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .summary-table .value {
            display: block;
            font-size: 18px;
            font-weight: 700;
            color: #1a1a2e;
            margin-top: 2px;
        }

        /* ── TARJETA DE CLIENTE ── */
        .client-card {
            border: 1px solid #e0e0e0;
            margin-bottom: 10px;
            page-break-inside: avoid;
        }
        .client-card .card-header {
            background: #f5f5f5;
            padding: 6px 12px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 2px solid #1a1a2e;
        }
        .client-card .card-header .client-name {
            font-size: 12px;
            font-weight: 700;
            color: #1a1a2e;
            letter-spacing: 0.5px;
        }
        .client-card .card-header .contact-badge {
            font-size: 9px;
            color: #555555;
            font-weight: 600;
        }

        /* ── TABLA DE DATOS DE EMPRESA ── */
        .company-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 9px;
        }
        .company-table td {
            padding: 4px 12px;
            border-bottom: 1px solid #f0f0f0;
            vertical-align: middle;
        }
        .company-table tr:last-child td { border-bottom: none; }
        .company-table .label {
            font-weight: 700;
            color: #666666;
            width: 10%;
            min-width: 70px;
            font-size: 8px;
            text-transform: uppercase;
            letter-spacing: 0.3px;
        }
        .company-table .value {
            color: #333333;
            font-weight: 400;
        }
        .company-table .value.empty { color: #cccccc; }

        /* ── TABLA DE CONTACTOS ── */
        .contacts-table-wrap {
            padding: 0 12px 10px 12px;
        }
        .contacts-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 8.5px;
            margin-top: 4px;
        }
        .contacts-table th {
            background: #f8f9fa;
            padding: 4px 8px;
            text-align: left;
            font-size: 7px;
            font-weight: 700;
            color: #666666;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            border-bottom: 2px solid #e8e8e8;
        }
        .contacts-table td {
            padding: 3px 8px;
            border-bottom: 1px solid #f2f2f2;
            vertical-align: middle;
            font-size: 8.5px;
            color: #333333;
        }
        .contacts-table tr:last-child td { border-bottom: none; }
        .contacts-table .contact-name {
            font-weight: 600;
            color: #1a1a2e;
        }
        .contacts-table .principal-badge {
            display: inline-block;
            background: #1a1a2e;
            color: #ffffff;
            font-size: 6.5px;
            font-weight: 700;
            padding: 1px 8px;
            border-radius: 2px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            text-align: center;
        }
        .contacts-table .principal-badge.no {
            background: transparent;
            color: #cccccc;
            font-weight: 400;
        }
        .contacts-table .text-muted { color: #aaaaaa; }
        .contacts-table .empty-cell {
            text-align: center;
            color: #aaaaaa;
            font-style: italic;
            padding: 10px 0;
        }

        /* ── SIN REGISTROS ── */
        .empty-state {
            text-align: center;
            padding: 40px 20px;
            color: #999999;
            border: 1px solid #e8e8e8;
        }
        .empty-state .icon { font-size: 28px; margin-bottom: 6px; }
        .empty-state .title { font-size: 14px; font-weight: 600; color: #666666; }
        .empty-state .sub { font-size: 10px; color: #999999; margin-top: 2px; }

        /* ── FOOTER ── */
        .footer {
            text-align: center;
            font-size: 7px;
            color: #999999;
            border-top: 1px solid #e8e8e8;
            padding-top: 8px;
            margin-top: 4px;
        }

        /* ── PRINT ── */
        @media print {
            body { padding: 8px 12px; }
            .client-card { break-inside: avoid; page-break-inside: avoid; }
            .card-header { -webkit-print-color-adjust: exact; print-color-adjust: exact; }
            .summary-table { -webkit-print-color-adjust: exact; print-color-adjust: exact; }
            .contacts-table th { -webkit-print-color-adjust: exact; print-color-adjust: exact; }
            .principal-badge { -webkit-print-color-adjust: exact; print-color-adjust: exact; }
        }

        @page {
            margin: 8mm 8mm 8mm 8mm;
            size: A4 portrait;
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- HEADER -->
        <div class="header">
            <h1>FIESTA TOURS PERU</h1>
            <div class="subtitle">
                Listado de Clientes<br>
                {{ now()->format('d/m/Y H:i') }} hrs
            </div>
        </div>

        <!-- RESUMEN EN UNA SOLA FILA -->
        <table class="summary-table">
            <tr>
                <td>
                    <span class="label">Total Clientes</span>
                    <span class="value">{{ $clients->count() }}</span>
                </td>
                <td>
                    <span class="label">Total Contactos</span>
                    <span class="value">{{ $clients->sum('contacts_count') }}</span>
                </td>
                <td>
                    <span class="label">Promedio</span>
                    <span class="value">{{ $clients->count() > 0 ? number_format($clients->sum('contacts_count') / $clients->count(), 1) : 0 }}</span>
                </td>
                <td>
                    <span class="label">Fecha Generación</span>
                    <span class="value" style="font-size:14px;">{{ now()->format('d/m/Y') }}</span>
                </td>
            </tr>
        </table>

        <!-- TARJETAS DE CLIENTES -->
        @forelse($clients as $client)
            <div class="client-card">
                <!-- Header de la tarjeta -->
                <div class="card-header">
                    <span class="client-name">{{ $client->name_client }}</span>
                    <span class="contact-badge">{{ $client->contacts_count }} contacto(s)</span>
                </div>

                <!-- Tabla de datos de la empresa -->
                <table class="company-table">
                    <tr>
                        <td class="label">Empresa:</td>
                        <td class="value">{{ $client->name_client }}</td>
                        <td class="label" style="width:8%;">RUC:</td>
                        <td class="value" style="width:25%;">{{ $client->tax_code ?? '—' }}</td>
                    </tr>
                    <tr>
                        <td class="label">Teléfono:</td>
                        <td class="value">{{ $client->general_phone ?? '—' }}</td>
                        <td class="label">Email:</td>
                        <td class="value" style="font-size:8px;">{{ $client->general_email ?? '—' }}</td>
                    </tr>
                    <tr>
                        <td class="label">Ubicación:</td>
                        <td class="value">{{ $client->country_name ?? '' }}{{ $client->country_name && $client->city_name ? ', ' : '' }}{{ $client->city_name ?? ' ' }}</td>
                        <td class="label">Dirección:</td>
                        <td class="value" style="font-size:8px;">{{ $client->address ?? '—' }}</td>
                    </tr>
                    @if($client->business_name)
                    <tr>
                        <td class="label">Razón Social:</td>
                        <td class="value" colspan="3">{{ $client->business_name }}</td>
                    </tr>
                    @endif
                </table>

                <!-- Tabla de contactos -->
                <div class="contacts-table-wrap">
                    @if($client->contacts->isNotEmpty())
                        <table class="contacts-table">
                            <thead>
                                <tr>
                                    <th style="width:22%;">Contacto</th>
                                    <th style="width:12%;">Principal</th>
                                    <th style="width:18%;">Cargo</th>
                                    <th style="width:28%;">Email</th>
                                    <th style="width:20%;">Teléfonos</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($client->contacts as $contact)
                                    <tr>
                                        <td class="contact-name">
                                            {{ trim($contact->name . ' ' . $contact->last_names) }}
                                        </td>
                                        <td>
                                            @if($contact->es_principal)
                                                <span class="principal-badge">Principal</span>
                                            @else
                                                <span class="principal-badge no">—</span>
                                            @endif
                                        </td>
                                        <td>{{ $contact->qualification ?? '—' }}</td>
                                        <td style="font-size:8px;">{{ $contact->email ?? '—' }}</td>
                                        <td>
                                            @if($contact->first_phone)
                                                {{ $contact->first_phone }}
                                                @if($contact->second_phone)
                                                    <span style="color:#cccccc;padding:0 3px;">|</span>
                                                    {{ $contact->second_phone }}
                                                @endif
                                            @else
                                                —
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @else
                        <div style="text-align:center;color:#aaaaaa;font-style:italic;font-size:9px;padding:6px 0;">
                            No hay contactos registrados para este cliente
                        </div>
                    @endif
                </div>
            </div>
        @empty
            <div class="empty-state">
                <div class="icon">📭</div>
                <div class="title">No hay clientes registrados</div>
                <div class="sub">Comienza creando tu primer contacto</div>
            </div>
        @endforelse

        <!-- FOOTER -->
        <div class="footer">
            Fiesta Tours Peru &copy; {{ now()->format('Y') }} · Lima, Peru · Sistema de Gestión Interna
        </div>
    </div>
</body>
</html>
