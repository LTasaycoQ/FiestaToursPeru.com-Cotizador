<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Fiesta Tours')</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@latest/tabler-icons.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    {{-- Anti-flash: aplica el estado guardado ANTES del primer paint --}}
    <script>
        (function() {
            if (localStorage.getItem('sidebarCollapsed') === '1') {
                document.documentElement.classList.add('sidebar-collapsed-init');
            }
        })();
    </script>

    <style>
        *, *::before, *::after { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Segoe UI', system-ui, sans-serif; background: #f1f5f9; display: flex; height: 100vh; overflow: hidden; color: #0f172a; }

        /* ══ SIDEBAR ══ */
        .sidebar {
            width: 210px;
            height: 100vh;
            background: #0f172a;
            flex-shrink: 0;
            overflow: hidden;
            transition: width .28s cubic-bezier(.4,0,.2,1);
        }
        .sidebar.collapsed { width: 64px; }

        .sidebar-inner {
            width: 100%;
            height: 100%;
            display: flex;
            flex-direction: column;
            background: #0f172a;
        }

        .sb-head { padding: 1.3rem 1.2rem 1rem; border-bottom: 1px solid rgba(255,255,255,.07); overflow: hidden; }
        .sb-brand { display: flex; align-items: center; gap: 10px; }
        .sb-icon { width: 34px; height: 34px; background: #3C4E3E; border-radius: 9px; display: flex; align-items: center; justify-content: center; font-size: 13px; font-weight: 700; color: #fff; flex-shrink: 0; }
        .sb-name { font-size: 13px; font-weight: 700; color: #fff; white-space: nowrap; }
        .sb-tagline { font-size: 10px; color: rgba(255,255,255,.35); margin-top: 1px; white-space: nowrap; }

        .sb-nav { flex: 1; padding: .8rem .8rem 0; overflow-y: auto; overflow-x: hidden; }

        .sb-group { font-size: 10px; font-weight: 600; color: rgba(255,255,255,.3); letter-spacing: .8px; text-transform: uppercase; padding: .8rem .6rem .3rem; white-space: nowrap; }

        .sb-link { display: flex; align-items: center; gap: 10px; padding: .56rem .8rem; border-radius: 8px; font-size: 13px; color: rgba(255,255,255,.55); cursor: pointer; text-decoration: none; transition: all .15s; margin-bottom: 1px; white-space: nowrap; }
        .sb-link i { font-size: 16px; flex-shrink: 0; }
        .sb-link:hover { background: rgba(255,255,255,.07); color: rgba(255,255,255,.85); }
        .sb-link.active { background: #3c4e3e; color: #fff; }
        .sb-link .arrow { margin-left: auto; font-size: 12px; transition: transform .2s; }
        .sb-link .arrow.open { transform: rotate(90deg); }
        .sb-text { transition: opacity .15s; }

        .sb-submenu {
            overflow: hidden;
            max-height: 0;
            transition: max-height .3s ease;
            padding-left: .5rem;
        }
        .sb-submenu.open { max-height: 500px; }
        .sb-submenu .sb-link {
            padding-left: 2.2rem;
            font-size: 12px;
            color: rgba(255,255,255,.4);
        }
        .sb-submenu .sb-link:hover { color: rgba(255,255,255,.8); }
        .sb-submenu .sb-link.active { background: #3C4E3E; color: #fff; }

        .sb-foot { padding: .8rem; border-top: 1px solid rgba(255,255,255,.07); overflow: hidden; }
        .sb-user { display: flex; align-items: center; gap: 9px; padding: .7rem .8rem; background: rgba(255,255,255,.05); border-radius: 10px; border: 1px solid rgba(255,255,255,.07); white-space: nowrap; }
        .sb-av { width: 30px; height: 30px; border-radius: 50%; background: linear-gradient(135deg,#6366f1,#8b5cf6); display: flex; align-items: center; justify-content: center; font-size: 11px; color: #fff; font-weight: 700; flex-shrink: 0; }
        .sb-av img { width: 100%; height: 100%; border-radius: 50%; object-fit: cover; }
        .sb-uname { font-size: 12px; font-weight: 600; color: #fff; white-space: nowrap; }
        .sb-urole { font-size: 10px; color: rgba(255,255,255,.35); white-space: nowrap; }

        /* ══ ESTADO COLAPSADO ══ */
        .sidebar.collapsed .sb-group,
        .sidebar.collapsed .sb-name,
        .sidebar.collapsed .sb-tagline,
        .sidebar.collapsed .sb-text,
        .sidebar.collapsed .sb-uname,
        .sidebar.collapsed .sb-urole,
        .sidebar.collapsed .sb-submenu,
        .sidebar.collapsed .arrow {
            display: none;
        }
        .sidebar.collapsed .sb-brand { justify-content: center; }
        .sidebar.collapsed .sb-link { justify-content: center; padding: .6rem; gap: 0; }
        .sidebar.collapsed .sb-user { justify-content: center; padding: .7rem; }
        .sidebar.collapsed .sb-head { padding: 1.3rem .6rem 1rem; }
        .sidebar.collapsed .sb-nav { padding: .8rem .5rem 0; }
        .sidebar.collapsed .sb-foot { padding: .8rem .5rem; }

        html.sidebar-collapsed-init .sidebar { width: 64px; transition: none; }
        html.sidebar-collapsed-init .sidebar .sb-group,
        html.sidebar-collapsed-init .sidebar .sb-name,
        html.sidebar-collapsed-init .sidebar .sb-tagline,
        html.sidebar-collapsed-init .sidebar .sb-text,
        html.sidebar-collapsed-init .sidebar .sb-uname,
        html.sidebar-collapsed-init .sidebar .sb-urole,
        html.sidebar-collapsed-init .sidebar .sb-submenu,
        html.sidebar-collapsed-init .sidebar .arrow { display: none; }
        html.sidebar-collapsed-init .sidebar .sb-brand,
        html.sidebar-collapsed-init .sidebar .sb-link,
        html.sidebar-collapsed-init .sidebar .sb-user { justify-content: center; }
        html.sidebar-collapsed-init .sidebar .sb-link { padding: .6rem; gap: 0; }
        html.sidebar-collapsed-init .search-box i { transform: rotate(180deg); }

        /* ══ MAIN ══ */
        .main-wrap { flex: 1; display: flex; flex-direction: column; overflow: hidden; }

        /* ══ TOPBAR ══ */
        .topbar { background: #fff; border-bottom: 1px solid #e2e8f0; padding: .8rem 1.6rem .8rem 0; display: flex; align-items: center; gap: 1rem; flex-shrink: 0; }
        .search-box { border: none; outline: none; display: flex; align-items: center; gap: 8px; background: #0F172A; border-radius: 0px 9px 9px 0px; padding: 10px 10px 10px 5px; cursor: pointer; }
        .search-box i { font-size: 25px; color: #ffffff; transition: transform .28s cubic-bezier(.4,0,.2,1); }
        .search-box.is-collapsed i { transform: rotate(180deg); }
        .tb-right { margin-left: auto; display: flex; align-items: center; gap: 8px; }
        .ib { width: 36px; height: 36px; border-radius: 9px; border: 1px solid #e2e8f0; background: #fff; display: flex; align-items: center; justify-content: center; cursor: pointer; color: #64748b; font-size: 24px; position: relative; text-decoration: none; }
        .ib:hover { background: #f8fafc; }
        .nd { position: absolute; top: 8px; right: 8px; width: 6px; height: 6px; background: #e63232; border-radius: 50%; border: 1.5px solid #fff; }

        /* DROPDOWN PERFIL */
        .u-menu { position: relative; }
        .u-trigger { display: flex; align-items: center; gap: 9px; padding: 5px 10px; border-radius: 10px; cursor: pointer; border: 1px solid #e2e8f0; background: #fff; transition: background .15s; }
        .u-trigger:hover { background: #f8fafc; }
        .u-av { width: 34px; height: 34px; border-radius: 50%; background: linear-gradient(135deg,#6366f1,#8b5cf6); display: flex; align-items: center; justify-content: center; font-size: 12px; color: #fff; font-weight: 700; flex-shrink: 0; }
        .u-av img { width: 100%; height: 100%; border-radius: 50%; object-fit: cover; }
        .u-name { font-size: 12px; font-weight: 600; color: #0f172a; }
        .u-role { font-size: 10px; color: #94a3b8; }
        .u-dd { display: none; position: absolute; top: calc(100% + 8px); right: 0; background: #fff; border: 1px solid #e2e8f0; border-radius: 12px; padding: .5rem; min-width: 190px; z-index: 200; box-shadow: 0 8px 30px rgba(0,0,0,.1); }
        .u-menu.open .u-dd { display: block; }
        .dd-header { padding: .7rem .9rem .5rem; border-bottom: 1px solid #f1f5f9; margin-bottom: .4rem; }
        .dd-fullname { font-size: 13px; font-weight: 600; color: #0f172a; }
        .dd-email { font-size: 11px; color: #94a3b8; margin-top: 1px; }
        .dd-item { display: flex; align-items: center; gap: 9px; padding: .55rem .9rem; font-size: 13px; color: #0f172a; border-radius: 7px; cursor: pointer; text-decoration: none; }
        .dd-item:hover { background: #f8fafc; }
        .dd-item i { font-size: 15px; color: #64748b; }
        .dd-sep { height: 1px; background: #f1f5f9; margin: .3rem 0; }
        .dd-danger { color: #e63232 !important; }
        .dd-danger i { color: #e63232 !important; }

        /* ══ CONTENT ══ */
        .page-content { flex: 1; overflow-y: auto; padding: 1.8rem 2rem; }
        .page-header { margin-bottom: 1.6rem; }
        .page-title { font-size: 22px; font-weight: 700; color: #0f172a; }
        .page-sub { font-size: 13px; color: #64748b; margin-top: 3px; }

        /* ALERTAS */
        .alert { padding: .85rem 1.1rem; border-radius: 10px; font-size: .88rem; margin-bottom: 1.2rem; display: flex; align-items: center; gap: 8px; }
        .alert-success { background: #f0fdf4; border: 1px solid #bbf7d0; color: #166534; }
        .alert-error   { background: #fef2f2; border: 1px solid #fecaca; color: #991b1b; }
        .alert i { font-size: 16px; flex-shrink: 0; }

        /* CARDS */
        .card { background: #fff; border: 1px solid #e2e8f0; border-radius: 14px; padding: 1.4rem; }
        .card-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.2rem; padding-bottom: .9rem; border-bottom: 1px solid #f1f5f9; }
        .card-title { font-size: 15px; font-weight: 700; color: #0f172a; }
        .card-sub { font-size: 12px; color: #64748b; margin-top: 2px; }

        /* FORM */
        .form-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 1.1rem; }
        .form-field label { display: block; font-size: 11px; font-weight: 600; color: #64748b; text-transform: uppercase; letter-spacing: .5px; margin-bottom: 5px; }
        .form-field input, .form-field select {
            width: 100%; padding: 10px 13px; border: 1px solid #e2e8f0; border-radius: 9px;
            background: #f8fafc; color: #0f172a; font-size: 13px; outline: none;
            transition: border-color .15s, background .15s;
        }
        .form-field input:focus, .form-field select:focus { border-color: #6366f1; background: #fff; box-shadow: 0 0 0 3px rgba(99,102,241,.08); }
        .form-field .hint { font-size: 11px; color: #94a3b8; margin-top: 4px; }
        .form-field .field-error { font-size: 11px; color: #e63232; margin-top: 4px; }

        /* BOTONES */
        .btn { display: inline-flex; align-items: center; gap: 7px; padding: 10px 20px; border-radius: 9px; font-size: 13px; font-weight: 600; cursor: pointer; border: none; transition: all .15s; text-decoration: none; }
        .btn-primary { background: #141C2F; color: #fff; }
        .btn-primary:hover { background: #252d41; }
        .btn-secondary { background: #f8fafc; color: #475569; border: 1px solid #e2e8f0; }
        .btn-secondary:hover { background: #f1f5f9; }
        .btn-danger { background: #fef2f2; color: #e63232; border: 1px solid #fecaca; }
        .btn-danger:hover { background: #fee2e2; }
        .btn-sm { padding: 6px 14px; font-size: 12px; }

        /* TABLA */
        .table-wrap { background: #fff; border: 1px solid #e2e8f0; border-radius: 12px; overflow: hidden; margin-bottom: 0.5rem; }
        table { width: 100%; border-collapse: collapse; }
        thead tr { background: #f8fafc; }
        th { padding: .85rem 1.1rem; text-align: left; font-size: 11px; font-weight: 700; color: #64748b; text-transform: uppercase; letter-spacing: .5px; border-bottom: 1px solid #e2e8f0; }
        td { padding: .85rem 1.1rem; border-bottom: 1px solid #f8fafc; font-size: 13px; color: #0f172a; }
        tr:last-child td { border-bottom: none; }
        tr.highlight { background: #fefce8; }
        tr:hover td { background: #fafafa; }
        .avatar-sm { width: 32px; height: 32px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 11px; font-weight: 700; flex-shrink: 0; }
        .badge { display: inline-flex; align-items: center; gap: 4px; padding: 3px 10px; border-radius: 999px; font-size: 11px; font-weight: 700; }
        .badge-admin   { background: #ede9fe; color: #6d28d9; }
        .badge-usuario { background: #dcfce7; color: #166534; }
        .table-footer { padding: .7rem 1.1rem; background: #f8fafc; border-top: 1px solid #e2e8f0; font-size: 12px; color: #94a3b8; }

        .btn-support {
            width: 100%;
            border: none;
            border-radius: 9px;
            padding: 12px 15px;
            display: flex;
            gap:10px;
            align-items:center;
            color: rgba(255,255,255,.55);
            font-weight: 600;
            background-color:transparent;
            cursor: pointer;
        }
        .btn-support:hover {
            background: #111a30d2;
        }

        /* ════════════════════════════════════ */
        /*    MODAL DE SOPORTE MEJORADO        */
        /* ════════════════════════════════════ */
        #supportModal {
            display: none;
            position: fixed;
            width: 100%;
            height: 100vh;
            background: rgba(15, 23, 42, 0.7);
            backdrop-filter: blur(6px);
            z-index: 300;
            align-items: center;
            justify-content: center;
            top: 0;
            left: 0;
            padding: 1.5rem;
        }
        #supportModal.show { display: flex; }

        .modal-content {
            background: #ffffff;
            border-radius: 20px;
            padding: 2.5rem 2.5rem 2rem;
            max-width: 520px;
            width: 100%;
            position: relative;
            animation: modalSlideIn 0.35s cubic-bezier(0.16, 1, 0.3, 1);
            box-shadow: 0 25px 60px rgba(0, 0, 0, 0.25);
        }

        @keyframes modalSlideIn {
            from { transform: translateY(40px) scale(0.96); opacity: 0; }
            to { transform: translateY(0) scale(1); opacity: 1; }
        }

        .modal-close {
            position: absolute;
            top: 1rem;
            right: 1.2rem;
            background: #f1f5f9;
            border: none;
            width: 36px;
            height: 36px;
            border-radius: 50%;
            font-size: 20px;
            color: #64748b;
            cursor: pointer;
            transition: all 0.25s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            line-height: 1;
        }
        .modal-close:hover {
            background: #e2e8f0;
            color: #0f172a;
            transform: rotate(90deg);
        }

        .modal-header-icon {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 56px;
            height: 56px;
            background: linear-gradient(135deg, #e63232, #c42a2a);
            border-radius: 16px;
            margin-bottom: 1rem;
            color: #fff;
            font-size: 28px;
            box-shadow: 0 8px 24px rgba(230, 50, 50, 0.3);
        }

        .modal-title {
            font-size: 22px;
            font-weight: 800;
            color: #0f172a;
            margin-bottom: 0.25rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .modal-subtitle {
            color: #64748b;
            font-size: 14px;
            margin-bottom: 1.75rem;
            line-height: 1.5;
        }

        .form-group {
            margin-bottom: 1.5rem;
        }
        .form-group label {
            display: block;
            font-size: 13px;
            font-weight: 600;
            color: #334155;
            margin-bottom: 6px;
        }
        .form-group label .required {
            color: #e63232;
            margin-left: 2px;
        }

        .form-group textarea {
            width: 100%;
            padding: 12px 16px;
            border: 2px solid #e2e8f0;
            border-radius: 12px;
            font-family: inherit;
            font-size: 14px;
            transition: all 0.25s ease;
            background: #f8fafc;
            resize: vertical;
            min-height: 130px;
            color: #0f172a;
            line-height: 1.6;
        }
        .form-group textarea:focus {
            outline: none;
            border-color: #e63232;
            background: #ffffff;
            box-shadow: 0 0 0 4px rgba(230, 50, 50, 0.08);
        }
        .form-group textarea::placeholder {
            color: #94a3b8;
        }

        .form-group .char-count {
            font-size: 12px;
            color: #94a3b8;
            text-align: right;
            margin-top: 6px;
            font-weight: 500;
        }
        .form-group .char-count .current {
            color: #0f172a;
        }
        .form-group .char-count.warning .current {
            color: #f59e0b;
        }
        .form-group .char-count.danger .current {
            color: #e63232;
        }

        .form-group .hint-text {
            display: flex;
            align-items: center;
            gap: 6px;
            font-size: 12px;
            color: #94a3b8;
            margin-top: 6px;
        }
        .form-group .hint-text i {
            font-size: 14px;
        }

        .modal-footer-actions {
            display: flex;
            gap: 0.75rem;
            margin-top: 0.5rem;
        }

        .btn-submit {
            flex: 1;
            background: linear-gradient(135deg, #e63232, #c42a2a);
            color: white;
            border: none;
            padding: 14px 24px;
            border-radius: 12px;
            font-size: 15px;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            box-shadow: 0 4px 16px rgba(230, 50, 50, 0.3);
        }
        .btn-submit:hover:not(:disabled) {
            transform: translateY(-2px);
            box-shadow: 0 6px 24px rgba(230, 50, 50, 0.4);
        }
        .btn-submit:active:not(:disabled) {
            transform: translateY(0);
        }
        .btn-submit:disabled {
            opacity: 0.7;
            cursor: not-allowed;
            transform: none;
        }

        .btn-cancel {
            padding: 14px 24px;
            border-radius: 12px;
            border: 2px solid #e2e8f0;
            background: transparent;
            color: #64748b;
            font-size: 15px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s ease;
        }
        .btn-cancel:hover {
            background: #f8fafc;
            border-color: #cbd5e1;
            color: #0f172a;
        }

        .alert-modal {
            padding: 12px 16px;
            border-radius: 12px;
            margin-top: 1rem;
            display: none;
            font-size: 14px;
            font-weight: 500;
            align-items: center;
            gap: 10px;
        }
        .alert-modal.success {
            display: flex;
            background: #f0fdf4;
            color: #15803d;
            border: 1px solid #86efac;
        }
        .alert-modal.error {
            display: flex;
            background: #fef2f2;
            color: #dc2626;
            border: 1px solid #fca5a5;
        }
        .alert-modal i {
            font-size: 20px;
            flex-shrink: 0;
        }

        .support-email-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: #f1f5f9;
            padding: 4px 12px 4px 8px;
            border-radius: 20px;
            font-size: 12px;
            color: #475569;
            margin-top: 2px;
        }
        .support-email-badge i {
            font-size: 14px;
            color: #64748b;
        }

        /* ═══ RESPONSIVE ═══ */
        @media (max-width: 640px) {
            .modal-content {
                padding: 1.75rem 1.25rem 1.5rem;
                border-radius: 16px;
            }
            .modal-title {
                font-size: 19px;
            }
            .modal-footer-actions {
                flex-direction: column-reverse;
            }
            .btn-cancel {
                padding: 12px;
                text-align: center;
            }
            .btn-submit {
                padding: 12px;
            }
            .modal-header-icon {
                width: 48px;
                height: 48px;
                font-size: 22px;
            }
        }
    </style>
    @stack('styles')
</head>
<body>

<aside class="sidebar" id="sidebar">
    <div class="sidebar-inner">
        <div class="sb-head">
            <div class="sb-brand">
                <div class="sb-icon">FT</div>
                <div>
                    <div class="sb-name">Fiesta Tours</div>
                    <div class="sb-tagline">Panel de control</div>
                </div>
            </div>
        </div>

        <nav class="sb-nav">
            {{-- MENÚ PRINCIPAL --}}
            <div class="sb-group">Menú</div>
            <a href="{{ route('dashboard') }}" class="sb-link {{ request()->routeIs('dashboard') ? 'active' : '' }}" title="Dashboard">
                <i class="ti ti-layout-dashboard"></i> <span class="sb-text">Dashboard</span>
            </a>

            {{-- GESTIÓN --}}
            <div class="sb-group">Gestión</div>

            {{-- CLIENTES Y CONTACTOS --}}
            <div class="sb-link" onclick="toggleSubmenu('sub-clientes')" title="Clientes y Contactos">
                <i class="ti ti-users"></i> <span class="sb-text">Clientes</span>
                <i class="ti ti-chevron-right arrow" id="arrow-sub-clientes"></i>
            </div>
            <div class="sb-submenu" id="sub-clientes">
                <a href="{{ route('admin.clients.index') }}" class="sb-link {{ request()->routeIs('admin.clients.*') ? 'active' : '' }}">
                    <i class="ti ti-building"></i> <span class="sb-text">Clientes</span>
                </a>
                <a href="{{ route('admin.contacts.index') }}" class="sb-link {{ request()->routeIs('admin.contacts.*') ? 'active' : '' }}">
                    <i class="ti ti-address-book"></i> <span class="sb-text">Contactos</span>
                </a>
            </div>

            {{-- PROVEEDORES --}}
            <div class="sb-link" onclick="toggleSubmenu('sub-proveedores')" title="Proveedores">
                <i class="ti ti-truck"></i> <span class="sb-text">Catalogo</span>
                <i class="ti ti-chevron-right arrow" id="arrow-sub-proveedores"></i>
            </div>
            <div class="sb-submenu" id="sub-proveedores">
                <a href="{{ route('admin.suppliers.index') }}" class="sb-link {{ request()->routeIs('admin.suppliers.*') ? 'active' : '' }}">
                    <i class="ti ti-truck"></i> <span class="sb-text">Proveedores</span>
                </a>

                <a href="{{ route('admin.chains.index') }}" class="sb-link {{ request()->routeIs('admin.chains.*') ? 'active' : '' }}">
                    <i class="ti ti-link"></i> <span class="sb-text">Cadenas</span>
                </a>
                <a href="{{ route('admin.services.index') }}" class="sb-link {{ request()->routeIs('admin.services.*') ? 'active' : '' }}">
                   <i class="ti ti-apps"></i><span class="sb-text">Programas</span>
                </a>
            </div>


                <a href="{{ route('admin.quotes.index') }}" class="sb-link {{ request()->routeIs('admin.quotes.*') ? 'active' : '' }}">
                   <i class="ti ti-lock-dollar"></i><span class="sb-text">Proyectos</span>
                </a>

                    <a href="{{ route('finance.index') }}" class="sb-link {{ request()->routeIs('finance.*') ? 'active' : '' }}">
                  <i class="ti ti-shopping-bag"></i><span class="sb-text">Control Compras</span>
                </a>

            {{-- CONFIGURACIÓN --}}
            <div class="sb-link" onclick="toggleSubmenu('sub-configuracion')" title="Configuración">
                <i class="ti ti-settings"></i> <span class="sb-text">Configuración</span>
                <i class="ti ti-chevron-right arrow" id="arrow-sub-configuracion"></i>
            </div>

            {{-- ADMINISTRACIÓN (solo admin) --}}
            @if(auth()->user()->isAdmin())
                <div class="sb-group">Administración</div>
                <a href="{{ route('admin.usuarios') }}" class="sb-link {{ request()->routeIs('admin.usuarios') ? 'active' : '' }}" title="Usuarios">
                    <i class="ti ti-users"></i> <span class="sb-text">Usuarios</span>
                </a>

            @endif

            <div class="sb-group">General</div>
            <a href="{{ route('perfil') }}" class="sb-link {{ request()->routeIs('perfil*') ? 'active' : '' }}" title="Configuración">
                <i class="ti ti-user"></i> <span class="sb-text">Mi Perfil</span>
            </a>
            <button class="btn-support" title="Ayuda" onclick="openModal()">
                <i class="ti ti-help"></i> <span class="sb-text">Ayuda</span>
            </button>
            <form action="{{ route('logout') }}" method="POST" style="margin:1px 0">
                @csrf
                <button type="submit" class="sb-link" title="Cerrar sesión" style="width:100%;border:none;background:none;text-align:left;cursor:pointer;color:rgba(255,255,255,.55)">
                    <i class="ti ti-logout"></i> <span class="sb-text">Cerrar sesión</span>
                </button>
            </form>
        </nav>

        <div class="sb-foot">
            <div class="sb-user">
                @php $user = auth()->user(); @endphp
                @if($user->avatar)
                    @php $filename = basename($user->avatar); @endphp
                    <div class="sb-av"><img src="{{ route('avatar.show', $filename) }}" alt="{{ $user->name }}" /></div>
                @else
                    <div class="sb-av">{{ strtoupper(substr($user->name, 0, 2)) }}</div>
                @endif
                <div>
                    <div class="sb-uname">{{ Str::limit($user->name, 16) }}</div>
                    <div class="sb-urole">{{ ucfirst($user->role) }}</div>
                </div>
            </div>
        </div>
    </div>
</aside>

<div class="main-wrap">
    <header class="topbar">
        <button class="search-box" id="sidebar-toggle" title="Colapsar/Expandir menú">
            <i class="ti ti-layout-sidebar-right-expand"></i>
        </button>

        <div class="tb-right">
            <a href="#" class="ib" title="Mensajes"><i class="ti ti-mail"></i></a>
            <a href="#" class="ib" title="Notificaciones">
               <i class="ti ti-settings"></i>
                <span class="nd"></span>
            </a>

            <div class="u-menu" id="userMenu">
                <div class="u-trigger" onclick="toggleMenu()">
                    @php $user = auth()->user(); @endphp
                    <div class="u-av">
                        @if($user->avatar)
                            @php $filename = basename($user->avatar); @endphp
                            <img src="{{ route('avatar.show', $filename) }}" alt="{{ $user->name }}" />
                        @else
                            {{ strtoupper(substr($user->name, 0, 2)) }}
                        @endif
                    </div>
                    <div>
                        <div class="u-name">{{ Str::limit($user->name, 14) }}</div>
                        <div class="u-role">{{ ucfirst($user->role) }}</div>
                    </div>
                    <i class="ti ti-chevron-down" style="font-size:13px;color:#94a3b8;margin-left:2px"></i>
                </div>
                <div class="u-dd">
                    <div class="dd-header">
                        <div class="dd-fullname">{{ $user->name }}</div>
                        <div class="dd-email">{{ $user->email }}</div>
                    </div>
                    <a href="{{ route('perfil') }}" class="dd-item">
                        <i class="ti ti-user"></i> Mi perfil
                    </a>
                    <a href="{{ route('perfil.edit') }}" class="dd-item">
                        <i class="ti ti-edit"></i> Editar datos
                    </a>
                    <div class="dd-sep"></div>
                    <form action="{{ route('logout') }}" method="POST">
                        @csrf
                        <button type="submit" class="dd-item dd-danger" style="width:100%;border:none;background:none;text-align:left;cursor:pointer">
                            <i class="ti ti-logout"></i> Cerrar sesión
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </header>

    {{-- CONTENIDO --}}
    <main class="page-content">
       
        @if($errors->has('error'))
            <div class="alert alert-error">
                <i class="ti ti-alert-circle"></i> {{ $errors->first('error') }}
            </div>
        @endif

        @yield('content')
    </main>
</div>

<div id="supportModal">
    <div class="modal-content">
        <button class="modal-close" onclick="closeModal()" aria-label="Cerrar">
            <i class="ti ti-x"></i>
        </button>

        <div class="modal-header-icon">
            <i class="ti ti-headset"></i>
        </div>

        <h2 class="modal-title">¿Necesitas ayuda?</h2>
        <p class="modal-subtitle">
            Describe el problema que estás experimentando y nuestro equipo de soporte te atenderá a la brevedad.
        </p>

        <form id="supportForm" action="{{ route('support.send') }}" method="POST">
            @csrf
            <input type="hidden" id="email" name="email" value="{{ auth()->user()->email }}">

            <div class="form-group">
                <label for="mensaje">
                    Mensaje <span class="required">*</span>
                </label>
                <textarea
                    id="mensaje"
                    name="mensaje"
                    placeholder="Escribe aquí tu consulta o problema con el mayor detalle posible..."
                    required
                    minlength="10"
                    maxlength="1000"
                ></textarea>
                <div class="char-count" id="charCount">
                    <span class="current" id="charCurrent">0</span> / <span id="charMax">1000</span> caracteres
                </div>
                <div class="hint-text">
                    <i class="ti ti-info-circle"></i>
                    Mínimo 10 caracteres. Sé lo más específico posible para recibir una mejor atención.
                </div>
            </div>

            <div class="support-email-badge">
                <i class="ti ti-mail"></i>
                Responderemos a: <strong>{{ auth()->user()->email }}</strong>
            </div>

            <div class="modal-footer-actions">
                <button type="button" class="btn-cancel" onclick="closeModal()">Cancelar</button>
                <button type="submit" class="btn-submit" id="btnSubmit">
                    <i class="ti ti-send"></i> Enviar mensaje
                </button>
            </div>

            <div id="alertMessage" class="alert-modal" style="display: none;">
                <i class="ti ti-circle-check" id="alertIcon"></i>
                <span id="alertText"></span>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const textarea = document.getElementById('mensaje');
    const charCurrent = document.getElementById('charCurrent');
    const charMax = document.getElementById('charMax');
    const charCount = document.getElementById('charCount');
    const maxLength = 1000;

    textarea.addEventListener('input', function() {
        const length = this.value.length;
        charCurrent.textContent = length;

        charCount.classList.remove('warning', 'danger');
        if (length > maxLength * 0.8) {
            charCount.classList.add('warning');
        }
        if (length > maxLength * 0.95) {
            charCount.classList.remove('warning');
            charCount.classList.add('danger');
        }
    });

    textarea.addEventListener('input', function() {
        this.style.height = 'auto';
        this.style.height = Math.min(this.scrollHeight, 300) + 'px';
    });
});

document.getElementById('supportForm').addEventListener('submit', async function(e) {
    e.preventDefault();
    const form = this;
    const btnSubmit = document.getElementById('btnSubmit');
    const alertMessage = document.getElementById('alertMessage');
    const alertIcon = document.getElementById('alertIcon');
    const alertText = document.getElementById('alertText');
    const originalBtnHtml = btnSubmit.innerHTML;

    btnSubmit.disabled = true;
    btnSubmit.innerHTML = '<i class="ti ti-loader ti-spin"></i> Enviando...';

    const formData = new FormData(form);

    try {
        const response = await fetch(form.action, {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        });

        const data = await response.json();

        alertMessage.style.display = 'flex';

        if (response.ok && data.success) {
            alertMessage.className = 'alert-modal success';
            alertIcon.className = 'ti ti-circle-check';
            alertText.textContent = data.message;
            form.reset();
            document.getElementById('charCurrent').textContent = '0';

            setTimeout(() => {
                if (typeof closeModal === 'function') closeModal();
                alertMessage.style.display = 'none';
            }, 2500);
        } else {
            alertMessage.className = 'alert-modal error';
            alertIcon.className = 'ti ti-alert-circle';
            alertText.textContent = data.message || 'Ocurrió un error al procesar la solicitud.';
        }
    } catch (error) {
        alertMessage.style.display = 'flex';
        alertMessage.className = 'alert-modal error';
        alertIcon.className = 'ti ti-alert-circle';
        alertText.textContent = 'Error de conexión. Inténtalo de nuevo más tarde.';
    } finally {
        btnSubmit.disabled = false;
        btnSubmit.innerHTML = originalBtnHtml;
    }
});

function openModal() {
    document.getElementById('supportModal').classList.add('show');
    document.body.style.overflow = 'hidden';
    const alert = document.getElementById('alertMessage');
    alert.className = 'alert-modal';
    alert.style.display = 'none';
    document.getElementById('charCurrent').textContent = '0';
}

function closeModal() {
    document.getElementById('supportModal').classList.remove('show');
    document.body.style.overflow = 'auto';
    const alert = document.getElementById('alertMessage');
    alert.className = 'alert-modal';
    alert.style.display = 'none';
}

(function() {
    const html      = document.documentElement;
    const sidebar   = document.getElementById('sidebar');
    const toggleBtn = document.getElementById('sidebar-toggle');

    const savedCollapsed = localStorage.getItem('sidebarCollapsed') === '1';
    if (savedCollapsed) {
        sidebar.classList.add('collapsed');
        toggleBtn.classList.add('is-collapsed');
    }
    html.classList.remove('sidebar-collapsed-init');

    toggleBtn.addEventListener('click', function() {
        const collapsed = sidebar.classList.toggle('collapsed');
        toggleBtn.classList.toggle('is-collapsed', collapsed);
        localStorage.setItem('sidebarCollapsed', collapsed ? '1' : '0');
    });
})();

function toggleSubmenu(id) {
    const submenu = document.getElementById(id);
    const arrow = document.getElementById('arrow-' + id);
    if (submenu) {
        submenu.classList.toggle('open');
        if (arrow) arrow.classList.toggle('open');
    }
}

function toggleMenu() {
    document.getElementById('userMenu').classList.toggle('open');
}

document.addEventListener('click', function(e) {
    if (!e.target.closest('#userMenu')) {
        document.getElementById('userMenu').classList.remove('open');
    }
});

document.addEventListener('DOMContentLoaded', function() {
    @if(request()->routeIs('admin.quotations.*'))
        const sub = document.getElementById('sub-cotizaciones');
        const arrow = document.getElementById('arrow-sub-cotizaciones');
        if (sub) sub.classList.add('open');
        if (arrow) arrow.classList.add('open');
    @endif

    @if(request()->routeIs('admin.services.*'))
        const sub = document.getElementById('sub-servicios');
        const arrow = document.getElementById('arrow-sub-servicios');
        if (sub) sub.classList.add('open');
        if (arrow) arrow.classList.add('open');
    @endif

    @if(request()->routeIs('admin.clients.*') || request()->routeIs('admin.contacts.*'))
        const sub = document.getElementById('sub-clientes');
        const arrow = document.getElementById('arrow-sub-clientes');
        if (sub) sub.classList.add('open');
        if (arrow) arrow.classList.add('open');
    @endif

    // Abrir submenú de Proveedores si estamos en esa sección
    @if(request()->routeIs('admin.suppliers.*') || request()->routeIs('admin.categories.*') || request()->routeIs('admin.chains.*'))
        const sub = document.getElementById('sub-proveedores');
        const arrow = document.getElementById('arrow-sub-proveedores');
        if (sub) sub.classList.add('open');
        if (arrow) arrow.classList.add('open');
    @endif

    // Abrir submenú de Configuración si estamos en esa sección
    @if(request()->routeIs('admin.price-types.*') || request()->routeIs('admin.pax-ranges.*') || request()->routeIs('admin.room-categories.*') || request()->routeIs('admin.holidays.*'))
        const sub = document.getElementById('sub-configuracion');
        const arrow = document.getElementById('arrow-sub-configuracion');
        if (sub) sub.classList.add('open');
        if (arrow) arrow.classList.add('open');
    @endif
});
</script>
@stack('scripts')
</body>
</html>
