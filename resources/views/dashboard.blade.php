@extends('layouts.app')
@section('title', 'Dashboard')

@push('styles')
<style>
/* ── PAGE HEADER ── */
.dash-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 1.5rem;
    gap: 1rem;
    flex-wrap: wrap;
}
.dash-heading { font-size: 1.5rem; font-weight: 800; color: #0f172a; line-height: 1.2; }
.dash-heading span { color: #e63232; }
.dash-sub { font-size: .8rem; color: #94a3b8; margin-top: .2rem; }
.dash-actions { display: flex; gap: .5rem; flex-wrap: wrap; }

/* ── STATS GRID ── */
.stats-grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: .85rem;
    margin-bottom: 1.25rem;
}
@media (max-width: 768px) { .stats-grid { grid-template-columns: repeat(2, 1fr); } }
@media (max-width: 480px) { .stats-grid { grid-template-columns: 1fr; } }

.stat-card {
    background: #fff;
    border-radius: 14px;
    padding: 1.2rem 1.3rem 1rem;
    border: 1px solid #e2e8f0;
    position: relative;
    overflow: hidden;
    transition: all .3s ease;
}
.stat-card:hover { 
    box-shadow: 0 8px 30px rgba(0,0,0,.08); 
    transform: translateY(-4px);
    border-color: #cbd5e1;
}
.stat-card.dark {
    background: linear-gradient(135deg, #0f172a 40%, #1e293b);
    border-color: transparent;
}
.stat-card.dark:hover {
    box-shadow: 0 8px 30px rgba(15,23,42,.3);
    transform: translateY(-4px);
}
.stat-card .sc-icon {
    width: 42px; height: 42px;
    border-radius: 12px;
    display: flex; align-items: center; justify-content: center;
    font-size: 18px;
    margin-bottom: .6rem;
}
.stat-card .sc-label {
    font-size: .7rem; font-weight: 700; text-transform: uppercase;
    letter-spacing: .6px; color: #94a3b8;
}
.stat-card.dark .sc-label { color: rgba(255,255,255,.45); }
.stat-card .sc-value {
    font-size: 2.1rem; font-weight: 800; color: #0f172a;
    margin: .1rem 0;
    line-height: 1.1;
}
.stat-card.dark .sc-value { color: #fff; }
.stat-card .sc-sub { font-size: .72rem; color: #94a3b8; margin-top: .1rem; }
.stat-card.dark .sc-sub { color: rgba(255,255,255,.35); }
.sc-blob {
    position: absolute; right: -20px; top: -20px;
    width: 90px; height: 90px;
    border-radius: 50%;
    opacity: .06;
}
.stat-card.dark .sc-blob { opacity: .12; }

/* ── DELTA CHIPS ── */
.delta-chip {
    font-size: .65rem; font-weight: 700;
    padding: 2px 10px; border-radius: 999px;
    display: inline-flex; align-items: center; gap: 3px;
    margin-top: 4px;
}
.delta-up   { background: #dcfce7; color: #166534; }
.delta-down { background: #fee2e2; color: #b91c1c; }
.delta-flat { background: #f1f5f9; color: #64748b; }
.delta-chip .ti { font-size: 12px; }

.growth-badge {
    font-size: .7rem; font-weight: 700;
    padding: 4px 14px; border-radius: 999px;
    display: inline-flex; align-items: center; gap: 4px;
}

/* ── PANEL CARD ── */
.panel {
    background: #fff;
    border: 1px solid #e2e8f0;
    border-radius: 14px;
    overflow: hidden;
    transition: box-shadow .3s ease;
}
.panel:hover { box-shadow: 0 4px 20px rgba(0,0,0,.05); }
.panel-head {
    display: flex; align-items: center; justify-content: space-between;
    padding: .9rem 1.3rem;
    border-bottom: 1px solid #f1f5f9;
}
.panel-title { font-size: .88rem; font-weight: 700; color: #0f172a; }
.panel-sub   { font-size: .7rem; color: #94a3b8; margin-top: 0; }

/* ── USER LIST ── */
.user-list { display: flex; flex-direction: column; }
.user-row {
    display: flex; align-items: center; gap: 10px;
    padding: .65rem 1.3rem;
    border-bottom: 1px solid #f8fafc;
    transition: background .2s;
    text-decoration: none;
}
.user-row:last-child { border-bottom: none; }
.user-row:hover { background: #f8fafc; }
.u-av-sm {
    width: 36px; height: 36px;
    border-radius: 50%;
    display: flex; align-items: center; justify-content: center;
    font-size: 12px; font-weight: 700; flex-shrink: 0;
}
.u-nm  { font-size: .82rem; font-weight: 600; color: #0f172a; }
.u-em  { font-size: .7rem; color: #94a3b8; }
.u-badge {
    margin-left: auto;
    font-size: .6rem; font-weight: 700;
    padding: 2px 12px; border-radius: 999px;
    text-transform: uppercase;
    letter-spacing: .4px;
}
.ub-admin   { background: #ede9fe; color: #6d28d9; }
.ub-usuario { background: #dbeafe; color: #1d4ed8; }

/* ── QUICK ACCESS ── */
.quick-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: .6rem;
    padding: 1rem 1.3rem;
}
.qa-card {
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    border-radius: 10px;
    padding: .8rem 1rem;
    text-decoration: none;
    display: block;
    transition: all .25s ease;
}
.qa-card:hover { 
    background: #fff; 
    box-shadow: 0 4px 16px rgba(0,0,0,.08); 
    border-color: #c7d2fe;
    transform: translateY(-3px);
}
.qa-card .ti { font-size: 20px; margin-bottom: .2rem; display: block; }
.qa-nm   { font-size: .8rem; font-weight: 700; color: #0f172a; }
.qa-sub  { font-size: .65rem; color: #94a3b8; }

/* ── BAR CHART ── */
.chart-body { padding: 1rem 1.3rem 1.2rem; }
.bar-chart {
    display: flex;
    align-items: flex-end;
    gap: 10px;
    height: 140px;
    margin-top: .2rem;
}
.bar-col {
    flex: 1;
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 5px;
    min-width: 0;
}
.bar-value { font-size: .65rem; font-weight: 700; color: #94a3b8; }
.bar-track {
    width: 100%;
    height: 100px;
    display: flex;
    align-items: flex-end;
}
.bar-fill {
    width: 100%;
    border-radius: 6px 6px 2px 2px;
    background: #e2e8f0;
    transition: height .6s ease;
    min-height: 4px;
}
.bar-fill.current {
    background: linear-gradient(180deg, #ff6b6b, #e63232);
}
.bar-label { 
    font-size: .65rem; 
    color: #94a3b8; 
    font-weight: 600; 
    text-transform: capitalize; 
}
.bar-label.current { color: #e63232; font-weight: 800; }

/* ── QUICK STATS LIST ── */
.qs-list { display: flex; flex-direction: column; }
.qs-row {
    display: flex; align-items: center; justify-content: space-between;
    padding: .8rem 1.3rem;
    border-bottom: 1px solid #f8fafc;
    transition: background .2s;
}
.qs-row:hover { background: #f8fafc; }
.qs-row:last-child { border-bottom: none; }
.qs-left { display: flex; align-items: center; gap: 12px; }
.qs-icon {
    width: 34px; height: 34px; border-radius: 9px;
    display: flex; align-items: center; justify-content: center;
    font-size: 14px; flex-shrink: 0;
}
.qs-label { font-size: .8rem; font-weight: 600; color: #334155; }
.qs-value { font-size: 1rem; font-weight: 800; color: #0f172a; }

/* ── LAYOUTS ── */
.dash-row {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: .85rem;
    margin-bottom: .85rem;
}
@media (max-width: 860px) { .dash-row { grid-template-columns: 1fr; } }

/* ── EMPTY STATE ── */
.empty-row {
    padding: 1.2rem;
    text-align: center;
    color: #94a3b8;
    font-size: 12px;
}

/* ── BOTONES ── */
.btn-secondary-sm {
    background: #f1f5f9;
    border: none;
    padding: .4rem .9rem;
    border-radius: 8px;
    font-size: .75rem;
    font-weight: 600;
    color: #475569;
    display: inline-flex;
    align-items: center;
    gap: 4px;
    cursor: pointer;
    transition: all .2s;
    text-decoration: none;
}
.btn-secondary-sm:hover {
    background: #e2e8f0;
    color: #0f172a;
}
.btn-primary-sm {
    background: #e63232;
    border: none;
    padding: .4rem .9rem;
    border-radius: 8px;
    font-size: .75rem;
    font-weight: 600;
    color: #fff;
    display: inline-flex;
    align-items: center;
    gap: 4px;
    cursor: pointer;
    transition: all .2s;
    text-decoration: none;
}
.btn-primary-sm:hover {
    background: #c62828;
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(230,50,50,.3);
}
.btn-primary-sm .ti,
.btn-secondary-sm .ti { font-size: 14px; }
</style>
@endpush

@section('content')

{{-- HEADER --}}
<div class="dash-header">
    <div>
        <div class="dash-heading">Bienvenido, <span>{{ explode(' ', $user->name)[0] }}</span> 👋</div>
        <div class="dash-sub">{{ now()->isoFormat('dddd, D [de] MMMM [de] YYYY') }} · Panel de control</div>
    </div>
    @if($user->isAdmin())
    <div class="dash-actions">
        <button class="btn-secondary-sm"><i class="ti ti-download"></i> Exportar</button>
        <a href="{{ route('admin.usuarios.create') }}" class="btn-primary-sm">
            <i class="ti ti-user-plus"></i> Nuevo usuario
        </a>
    </div>
    @endif
</div>

@if($user->isAdmin())

{{-- ── STATS ── --}}
<div class="stats-grid">
    <div class="stat-card dark">
        <div class="sc-blob" style="background:#818cf8"></div>
        <div class="sc-icon" style="background:rgba(99,102,241,.2)">
            <i class="ti ti-users" style="color:#818cf8"></i>
        </div>
        <div class="sc-label">Total usuarios</div>
        <div class="sc-value">{{ $totalUsers }}</div>
        <div class="sc-sub">Cuentas registradas</div>
        @if($growthPct > 0)
            <span class="delta-chip delta-up"><i class="ti ti-trending-up"></i> +{{ $growthPct }}%</span>
        @elseif($growthPct < 0)
            <span class="delta-chip delta-down"><i class="ti ti-trending-down"></i> {{ $growthPct }}%</span>
        @else
            <span class="delta-chip delta-flat"><i class="ti ti-minus"></i> Sin cambios</span>
        @endif
    </div>

    <div class="stat-card">
        <div class="sc-blob" style="background:#10b981"></div>
        <div class="sc-icon" style="background:#f0fdf4">
            <i class="ti ti-user" style="color:#10b981"></i>
        </div>
        <div class="sc-label">Usuarios</div>
        <div class="sc-value">{{ $totalUsuarios }}</div>
        <div class="sc-sub">Rol usuario</div>
    </div>

    <div class="stat-card">
        <div class="sc-blob" style="background:#6d28d9"></div>
        <div class="sc-icon" style="background:#ede9fe">
            <i class="ti ti-shield-star" style="color:#6d28d9"></i>
        </div>
        <div class="sc-label">Administradores</div>
        <div class="sc-value">{{ $totalAdmins }}</div>
        <div class="sc-sub">Rol administrador</div>
    </div>

    <div class="stat-card">
        <div class="sc-blob" style="background:#f59e0b"></div>
        <div class="sc-icon" style="background:#fffbeb">
            <i class="ti ti-user-plus" style="color:#d97706"></i>
        </div>
        <div class="sc-label">Nuevos hoy</div>
        <div class="sc-value">{{ $newToday }}</div>
        @if($todayVsYesterday > 0)
            <span class="delta-chip delta-up"><i class="ti ti-arrow-up-right"></i> +{{ $todayVsYesterday }}</span>
        @elseif($todayVsYesterday < 0)
            <span class="delta-chip delta-down"><i class="ti ti-arrow-down-right"></i> {{ $todayVsYesterday }}</span>
        @else
            <span class="delta-chip delta-flat"><i class="ti ti-minus"></i> Igual</span>
        @endif
    </div>
</div>

{{-- ── FILA: GRÁFICA + RESUMEN ── --}}
<div class="dash-row">

    {{-- GRÁFICA DE ACTIVIDAD --}}
    <div class="panel">
        <div class="panel-head">
            <div>
                <div class="panel-title">Actividad de registros</div>
                <div class="panel-sub">Últimos 6 meses</div>
            </div>
            @if($growthPct > 0)
                <span class="growth-badge delta-up"><i class="ti ti-trending-up"></i> +{{ $growthPct }}%</span>
            @elseif($growthPct < 0)
                <span class="growth-badge delta-down"><i class="ti ti-trending-down"></i> {{ $growthPct }}%</span>
            @else
                <span class="growth-badge delta-flat"><i class="ti ti-minus"></i> 0%</span>
            @endif
        </div>
        <div class="chart-body">
            <div class="bar-chart">
                @foreach($monthlyRegistrations as $i => $value)
                <div class="bar-col">
                    <span class="bar-value">{{ $value }}</span>
                    <div class="bar-track">
                        <div class="bar-fill {{ $i == $currentMonthIndex ? 'current' : '' }}"
                             style="height: {{ $maxRegistrations > 0 ? max(6, round(($value / $maxRegistrations) * 100)) : 6 }}%">
                        </div>
                    </div>
                    <span class="bar-label {{ $i == $currentMonthIndex ? 'current' : '' }}">{{ $monthLabels[$i] }}</span>
                </div>
                @endforeach
            </div>
        </div>
    </div>

    {{-- RESUMEN --}}
    <div class="panel">
        <div class="panel-head">
            <div>
                <div class="panel-title">Resumen</div>
                <div class="panel-sub">Registros por periodo</div>
            </div>
        </div>
        <div class="qs-list">
            <div class="qs-row">
                <div class="qs-left">
                    <div class="qs-icon" style="background:#f0fdf4"><i class="ti ti-user-plus" style="color:#10b981"></i></div>
                    <span class="qs-label">Hoy</span>
                </div>
                <span class="qs-value">{{ $newToday }}</span>
            </div>
            <div class="qs-row">
                <div class="qs-left">
                    <div class="qs-icon" style="background:#e0f2fe"><i class="ti ti-calendar-week" style="color:#0284c7"></i></div>
                    <span class="qs-label">Esta semana</span>
                </div>
                <span class="qs-value">{{ $newThisWeek }}</span>
            </div>
            <div class="qs-row">
                <div class="qs-left">
                    <div class="qs-icon" style="background:#fef3c7"><i class="ti ti-calendar-month" style="color:#d97706"></i></div>
                    <span class="qs-label">Este mes</span>
                </div>
                <span class="qs-value">{{ $newThisMonth }}</span>
            </div>
        </div>
    </div>

</div>

{{-- ── FILA: USUARIOS RECIENTES + ACCESOS ── --}}
<div class="dash-row">

    {{-- USUARIOS RECIENTES --}}
    <div class="panel">
        <div class="panel-head">
            <div>
                <div class="panel-title">Usuarios recientes</div>
                <div class="panel-sub">Últimos registrados</div>
            </div>
            <a href="{{ route('admin.usuarios') }}" class="btn-secondary-sm">Ver todos</a>
        </div>
        <div class="user-list">
            @forelse($recentUsers as $u)
            <a href="{{ route('admin.usuarios') }}" class="user-row">
                <div class="u-av-sm" style="background:{{ $u->isAdmin() ? '#ede9fe' : '#dbeafe' }};color:{{ $u->isAdmin() ? '#6d28d9' : '#1d4ed8' }}">
                    {{ strtoupper(substr($u->name, 0, 2)) }}
                </div>
                <div>
                    <div class="u-nm">{{ $u->name }}</div>
                    <div class="u-em">{{ $u->email }}</div>
                </div>
                <span class="u-badge {{ $u->isAdmin() ? 'ub-admin' : 'ub-usuario' }}">
                    {{ $u->isAdmin() ? 'Admin' : 'Usuario' }}
                </span>
            </a>
            @empty
            <div class="empty-row">Sin usuarios aún</div>
            @endforelse
        </div>
    </div>

    {{-- ACCESOS RÁPIDOS --}}
    <div class="panel">
        <div class="panel-head">
            <div>
                <div class="panel-title">Accesos rápidos</div>
                <div class="panel-sub">Atajos del sistema</div>
            </div>
        </div>
        <div class="quick-grid">
            <a href="{{ route('admin.usuarios') }}" class="qa-card">
                <i class="ti ti-users"></i>
                <div class="qa-nm">Usuarios</div>
                <div class="qa-sub">Gestionar</div>
            </a>
            <a href="{{ route('admin.clients.index') }}" class="qa-card">
                <i class="ti ti-building"></i>
                <div class="qa-nm">Clientes</div>
                <div class="qa-sub">Ver todos</div>
            </a>
            <a href="{{ route('admin.suppliers.index') }}" class="qa-card">
                <i class="ti ti-truck"></i>
                <div class="qa-nm">Proveedores</div>
                <div class="qa-sub">Ver todos</div>
            </a>
            <a href="{{ route('admin.usuarios.create') }}" class="qa-card">
                <i class="ti ti-user-plus"></i>
                <div class="qa-nm">Nuevo usuario</div>
                <div class="qa-sub">Crear cuenta</div>
            </a>
        </div>
    </div>

</div>

@else

{{-- ── VISTA USUARIO ── --}}
<div class="stats-grid">
    <div class="stat-card dark">
        <div class="sc-blob" style="background:#6366f1"></div>
        <div class="sc-icon" style="background:rgba(99,102,241,.2)">
            <i class="ti ti-building" style="color:#818cf8"></i>
        </div>
        <div class="sc-label">Clientes</div>
        <div class="sc-value">{{ $totalClients }}</div>
        <div class="sc-sub">Empresas registradas</div>
    </div>

    <div class="stat-card">
        <div class="sc-blob" style="background:#10b981"></div>
        <div class="sc-icon" style="background:#f0fdf4">
            <i class="ti ti-address-book" style="color:#10b981"></i>
        </div>
        <div class="sc-label">Contactos</div>
        <div class="sc-value">{{ $totalContacts }}</div>
        <div class="sc-sub">En total</div>
    </div>

    <div class="stat-card">
        <div class="sc-blob" style="background:#f59e0b"></div>
        <div class="sc-icon" style="background:#fffbeb">
            <i class="ti ti-truck" style="color:#d97706"></i>
        </div>
        <div class="sc-label">Proveedores</div>
        <div class="sc-value">{{ $totalSuppliers }}</div>
        <div class="sc-sub">Registrados</div>
    </div>

    <div class="stat-card">
        <div class="sc-blob" style="background:#6366f1"></div>
        <div class="sc-icon" style="background:#ede9fe">
            <i class="ti ti-address-book-off" style="color:#6d28d9"></i>
        </div>
        <div class="sc-label">Sin contacto</div>
        <div class="sc-value">{{ $clientsWithoutContact }}</div>
        <div class="sc-sub">Clientes sin contacto</div>
    </div>
</div>

{{-- ── FILA: PERFIL + ACCESOS ── --}}
<div class="dash-row">

    {{-- PERFIL --}}
    <div class="panel">
        <div class="panel-head">
            <div>
                <div class="panel-title">Mi perfil</div>
                <div class="panel-sub">Información personal</div>
            </div>
            <a href="{{ route('perfil.edit') }}" class="btn-secondary-sm">
                <i class="ti ti-edit"></i> Editar
            </a>
        </div>
        <div class="profile-body" style="padding:1.1rem 1.2rem 1.2rem;">
            <div class="profile-top" style="display:flex;align-items:center;gap:14px;">
                <div class="profile-avatar-wrap" style="position:relative;flex-shrink:0;">
                    <div class="profile-avatar-lg" style="width:58px;height:58px;border-radius:16px;display:flex;align-items:center;justify-content:center;font-size:22px;font-weight:800;background:linear-gradient(135deg,#e63232,#ff6b6b);color:#fff;box-shadow:0 6px 16px rgba(230,50,50,.28);">
                        {{ strtoupper(substr($user->name, 0, 2)) }}
                    </div>
                    <div class="profile-status-dot" style="position:absolute;bottom:-2px;right:-2px;width:15px;height:15px;border-radius:50%;background:#22c55e;border:2.5px solid #fff;"></div>
                </div>
                <div>
                    <div class="profile-name-lg" style="font-size:1rem;font-weight:800;color:#0f172a;line-height:1.25;">{{ $user->name }}</div>
                    <div class="profile-email-lg" style="font-size:.76rem;color:#94a3b8;margin-top:1px;">{{ $user->email }}</div>
                    <span class="profile-role-lg" style="font-size:.58rem;font-weight:700;text-transform:uppercase;letter-spacing:.5px;padding:2px 9px;border-radius:999px;background:#dcfce7;color:#166534;display:inline-flex;align-items:center;gap:3px;margin-top:5px;">
                        <i class="ti ti-circle-check" style="font-size:10px;"></i> Activo
                    </span>
                </div>
            </div>

            <div class="profile-meta-grid" style="display:grid;grid-template-columns:repeat(3,1fr);gap:8px;margin-top:1rem;">
                <div class="profile-meta-item" style="background:#f8fafc;border:1px solid #f1f5f9;border-radius:10px;padding:.55rem .6rem;transition:all .15s;">
                    <div class="profile-meta-icon" style="width:24px;height:24px;border-radius:7px;display:flex;align-items:center;justify-content:center;font-size:12px;margin-bottom:6px;background:#ede9fe;">
                        <i class="ti ti-shield-check" style="color:#6d28d9"></i>
                    </div>
                    <span class="profile-meta-label" style="font-size:.58rem;font-weight:700;text-transform:uppercase;letter-spacing:.4px;color:#94a3b8;display:block;margin-bottom:1px;">Rol</span>
                    <span class="profile-meta-value" style="font-size:.74rem;font-weight:700;color:#0f172a;display:block;">{{ $user->isAdmin() ? 'Admin' : 'Usuario' }}</span>
                </div>
                <div class="profile-meta-item" style="background:#f8fafc;border:1px solid #f1f5f9;border-radius:10px;padding:.55rem .6rem;transition:all .15s;">
                    <div class="profile-meta-icon" style="width:24px;height:24px;border-radius:7px;display:flex;align-items:center;justify-content:center;font-size:12px;margin-bottom:6px;background:#f0fdf4;">
                        <i class="ti ti-calendar" style="color:#10b981"></i>
                    </div>
                    <span class="profile-meta-label" style="font-size:.58rem;font-weight:700;text-transform:uppercase;letter-spacing:.4px;color:#94a3b8;display:block;margin-bottom:1px;">Desde</span>
                    <span class="profile-meta-value" style="font-size:.74rem;font-weight:700;color:#0f172a;display:block;">{{ $user->created_at->format('d/m/Y') }}</span>
                </div>
                <div class="profile-meta-item" style="background:#f8fafc;border:1px solid #f1f5f9;border-radius:10px;padding:.55rem .6rem;transition:all .15s;">
                    <div class="profile-meta-icon" style="width:24px;height:24px;border-radius:7px;display:flex;align-items:center;justify-content:center;font-size:12px;margin-bottom:6px;background:#fffbeb;">
                        <i class="ti ti-mail" style="color:#d97706"></i>
                    </div>
                    <span class="profile-meta-label" style="font-size:.58rem;font-weight:700;text-transform:uppercase;letter-spacing:.4px;color:#94a3b8;display:block;margin-bottom:1px;">Correo</span>
                    <span class="profile-meta-value" style="font-size:.74rem;font-weight:700;color:#0f172a;display:block;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;" title="{{ $user->email }}">{{ $user->email }}</span>
                </div>
            </div>
        </div>
    </div>

    <div class="panel">
        <div class="panel-head">
            <div>
                <div class="panel-title">⚡ Accesos rápidos</div>
                <div class="panel-sub">Atajos del sistema</div>
            </div>
        </div>
        <div class="quick-grid">
            <a href="{{ route('admin.clients.index') }}" class="qa-card">
                <i class="ti ti-building"></i>
                <div class="qa-nm">Clientes</div>
                <div class="qa-sub">Ver todos</div>
            </a>
            <a href="{{ route('admin.contacts.index') }}" class="qa-card">
                <i class="ti ti-address-book"></i>
                <div class="qa-nm">Contactos</div>
                <div class="qa-sub">Gestionar</div>
            </a>
            <a href="{{ route('admin.suppliers.index') }}" class="qa-card">
                <i class="ti ti-truck"></i>
                <div class="qa-nm">Proveedores</div>
                <div class="qa-sub">Ver todos</div>
            </a>
            <a href="{{ route('perfil') }}" class="qa-card">
                <i class="ti ti-user"></i>
                <div class="qa-nm">Mi perfil</div>
                <div class="qa-sub">Ver mis datos</div>
            </a>
        </div>
    </div>

</div>

{{-- ── FILA: PROVEEDORES + CLIENTES ── --}}
<div class="dash-row">

    {{-- PROVEEDORES RECIENTES --}}
    <div class="panel">
        <div class="panel-head">
            <div>
                <div class="panel-title">🚚 Proveedores recientes</div>
                <div class="panel-sub">Últimos registrados</div>
            </div>
            <a href="{{ route('admin.suppliers.index') }}" class="btn-secondary-sm">Ver todos</a>
        </div>
        <div class="user-list">
            @forelse($recentSuppliers as $s)
            <a href="{{ route('admin.suppliers.index') }}" class="user-row">
                <div class="u-av-sm" style="background:#f0f9ff;color:#0369a1">
                    {{ strtoupper(substr($s->supplier_name, 0, 2)) }}
                </div>
                <div>
                    <div class="u-nm">{{ $s->supplier_name }}</div>
                </div>
            </a>
            @empty
            <div class="empty-row">Sin proveedores aún</div>
            @endforelse
        </div>
    </div>

    {{-- CLIENTES RECIENTES --}}
    <div class="panel">
        <div class="panel-head">
            <div>
                <div class="panel-title">🏢 Clientes recientes</div>
                <div class="panel-sub">Últimos registrados</div>
            </div>
            <a href="{{ route('admin.clients.index') }}" class="btn-secondary-sm">Ver todos</a>
        </div>
        <div class="user-list">
            @forelse($recentClients as $c)
            <a href="{{ route('admin.clients.index') }}" class="user-row">
                <div class="u-av-sm" style="background:#dbeafe;color:#1d4ed8">
                    {{ strtoupper(substr($c->name_client, 0, 2)) }}
                </div>
                <div>
                    <div class="u-nm">{{ $c->name_client }}</div>
                    <div class="u-em">{{ $c->contacts_count }} contacto(s)</div>
                </div>
                <span class="u-badge" style="background:#dcfce7;color:#166534">ACTIVO</span>
            </a>
            @empty
            <div class="empty-row">Sin clientes aún</div>
            @endforelse
        </div>
    </div>

</div>

@endif
@endsection