<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Puritan:ital,wght@0,400;0,700;1,400;1,700&family=Raleway:ital,wght@0,100..900;1,100..900&family=Roboto:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">
<title>Proveedores</title>
<style>
    @page {
        margin: 30px 36px;
    }

    * {
        box-sizing: border-box;
    }

    body {
       font-family: "Raleway", sans-serif;
       font-optical-sizing: auto;
       color: #1e293b;
       font-size: 12px;
       margin: 0;
       padding: 0;
    }

    /* ── HEADER ── */
    .header-table {
        width: 100%;
        margin-bottom: 22px;
        border-collapse: collapse;
    }
    .header-table td {
        vertical-align: top;
        padding: 0;
    }
    .header-table .logo-cell {
        text-align: right;
        width: 200px;
    }
    .header-table .logo-img {
        max-width: 300px;
        max-height: 160px;
    }
    .doc-title {
        font-size: 30px;
        font-weight: 800;
        color: #30533c;
        margin: 0;
    }
    .doc-sub {
        font-size: 11px;
        color: #64748b;
        margin: 2px 0 0;
    }

    .logo-fiesta {
        font-size: 13px;
        font-weight: 700;
        color: #1e3a2f;
        line-height: 1.2;
    }
    .logo-fiesta .accent {
        color: #2f7d4f;
    }
    .logo-peru {
        font-size: 13px;
        font-weight: 700;
        letter-spacing: 1px;
        color: #0f172a;
        margin-top: 4px;
    }
    .logo-peru .accentx {
        color: #c9a227;
    }
    .logo-sub {
        font-size: 8px;
        letter-spacing: 2px;
        color: #64748b;
    }

    /* ── SECTION TITLES ── */
    .section {
        margin-top: 18px;
        margin-bottom: 8px;
    }
    .section-title {
        font-size: 12px;
        font-weight: 700;
        color: #0f172a;
        text-transform: uppercase;
        border-left: 4px solid #2f7d4f;
        padding-left: 8px;
        margin: 0 0 10px 0;
    }

    /* ── SUPPLIERS TABLE ── */
    .suppliers-table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 4px;
    }
    .suppliers-table thead th {
        background-color: #6c8a77;
        color: #18201d;
        font-size: 10.5px;
        text-transform: uppercase;
        font-weight: 700;
        padding: 7px 9px;
        text-align: left;
        border: 1px solid #7aa688;
    }
    .suppliers-table tbody td {
        font-size: 11px;
        padding: 7px 9px;
        border: 1px solid #e2e8f0;
        color: #1e293b;
        vertical-align: top;
    }
    .suppliers-table tbody tr:nth-child(even) td {
        background-color: #f8fafc;
    }
    .empty-note {
        color: #94a3b8;
        font-size: 11px;
        font-style: italic;
        padding: 10px 0;
    }
</style>
</head>
<body>

    <table class="header-table">
        <tr>
            <td>
                <p class="doc-title">{{ $suppliers->count() === 1 ? 'Ficha de Proveedor' : 'Listado de Proveedores' }}</p>
                <p class="doc-sub">Generado el {{ now()->format('d/m/Y, h:i A') }}</p>
            </td>
            <td class="logo-cell">
               @if($logoBase64)
                    <img src="{{ $logoBase64 }}" alt="Logo" class="logo-img">
                @endif
                <div style="display:none">
                    <div class="logo-fiesta">FIESTA<br><span class="accent">TOURS PERU</span></div>
                    <div class="logo-peru">PERU LU<span class="accentx">X</span>URY</div>
                    <div class="logo-sub">J&nbsp;&nbsp;O&nbsp;&nbsp;U&nbsp;&nbsp;R&nbsp;&nbsp;N&nbsp;&nbsp;E&nbsp;&nbsp;Y&nbsp;&nbsp;S</div>
                </div>
            </td>
        </tr>
    </table>

    <div class="supplier-block">
        {{-- ===================== INFORMACIÓN DE PROVEEDORES ===================== --}}
        <div class="section">
            <p class="section-title">Información de Proveedores</p>
        </div>

        {{-- ===================== TABLA DE PROVEEDORES ===================== --}}
        <table class="suppliers-table">
            <thead>
                <tr>
                    <th style="width:4%">#</th>
                    <th style="width:20%">N. Comercial</th>
                    <th style="width:24%">Rz. Social</th>
                    <th style="width:15%">RUC</th>
                    <th style="width:20%">País - Ciudad</th>
                    <th style="width:17%">Dirección</th>
                </tr>
            </thead>
            <tbody>
                @forelse($suppliers as $index => $supplier)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ $supplier->supplier_name }}</td>
                        <td>{{ $supplier->business_name ?? '—' }}</td>
                        <td>{{ $supplier->tax_code ?? '—' }}</td>
                        <td>{{ $supplier->country->name ?? '—' }}{{ $supplier->city?->name ? ' - ' . $supplier->city->name : '' }}</td>
                        <td>{{ $supplier->address ?? '—' }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="empty-note">No hay proveedores registrados</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

</body>
</html>