@extends('layouts.app')
@section('title', 'Mi Perfil')

@push('styles')
<style>
/* ── Layout principal ── */
.profile-layout {
    display: grid;
    grid-template-columns: 300px 1fr;
    gap: 1.25rem;
    align-items: start;
}
@media (max-width: 768px) {
    .profile-layout { grid-template-columns: 1fr; }
}

/* ── Columna izquierda: Hero ── */
.profile-hero {
    background: linear-gradient(160deg, #0f172a 0%, #1e293b 100%);
    border-radius: 16px;
    overflow: hidden;
    position: relative;
}
.ph-accent {
    position: absolute; bottom: -30px; right: -30px;
    width: 140px; height: 140px; border-radius: 50%;
    background: rgba(99,102,241,.15); pointer-events: none;
}
.ph-accent2 {
    position: absolute; top: -20px; left: 50%;
    width: 80px; height: 80px; border-radius: 50%;
    background: rgba(99,102,241,.08); pointer-events: none;
}
.ph-bg-letter {
    position: absolute; right: 1rem; bottom: 1.5rem;
    font-size: 5rem; font-weight: 900;
    color: rgba(255,255,255,.05);
    line-height: 1; pointer-events: none; user-select: none;
}
.ph-inner { padding: 1.5rem; }
.ph-eyebrow {
    font-size: .65rem; font-weight: 600; text-transform: uppercase;
    letter-spacing: .8px; color: rgba(255,255,255,.35);
    margin-bottom: 1.25rem;
}
.ph-avatar {
    width: 72px; height: 72px; border-radius: 50%;
    background: linear-gradient(135deg, #6366f1, #8b5cf6);
    border: 3px solid rgba(255,255,255,.25);
    display: flex; align-items: center; justify-content: center;
    font-size: 22px; color: #fff; font-weight: 700;
    margin-bottom: 1rem;
}


.ph-avatar_image {
    width: 72px; height: 72px; border-radius: 50%;
    background: linear-gradient(135deg, #6366f1, #8b5cf6);
    border: 3px solid rgba(255,255,255,.25);
    display: flex; align-items: center; justify-content: center;
    font-size: 22px; color: #fff; font-weight: 700;
}
.ph-name { font-size: 1rem; font-weight: 700; color: #fff; line-height: 1.3; }
.ph-role {
    display: inline-flex; align-items: center; gap: 4px;
    margin-top: .5rem;
    font-size: .68rem; font-weight: 700;
    padding: 3px 10px; border-radius: 999px;
}
.ph-edit-btn {
    display: flex; align-items: center; justify-content: center; gap: 6px;
    margin: 0 1.5rem 1.5rem;
    padding: 9px;
    background: rgba(99,102,241,.15);
    border: 1px solid rgba(99,102,241,.3);
    border-radius: 10px;
    color: #a5b4fc; font-size: .78rem; font-weight: 600;
    text-decoration: none; transition: background .15s;
}
.ph-edit-btn:hover { background: rgba(99,102,241,.25); }

/* ── Activity grid ── */
.activity-section {
    background: #fff;
    border: 1px solid #e2e8f0;
    border-radius: 14px;
    overflow: hidden;
    margin-top: 1rem;
}
.activity-grid {
    display: grid; grid-template-columns: 1fr 1fr;
    gap: .75rem; padding: 1.25rem;
}
.act-card {
    background: #f8fafc;
    border-radius: 10px;
    padding: .9rem;
    text-align: center;
}
.act-ico {
    width: 36px; height: 36px; border-radius: 9px;
    display: flex; align-items: center; justify-content: center;
    font-size: 16px; margin: 0 auto .6rem;
}
.act-num { font-size: 1.2rem; font-weight: 700; color: #0f172a; }
.act-label { font-size: .68rem; color: #94a3b8; margin-top: 2px; }

/* ── Quick links ── */
.quick-links-section {
    background: #fff;
    border: 1px solid #e2e8f0;
    border-radius: 14px;
    overflow: hidden;
    margin-top: 1rem;
}
.ql-inner { padding: .75rem 1.25rem 1.25rem; }
.ql-label {
    font-size: .65rem; font-weight: 700; text-transform: uppercase;
    letter-spacing: .6px; color: #94a3b8;
    margin-bottom: .75rem; padding-top: 1rem;
    display: block;
}
.ql-item {
    display: flex; align-items: center; gap: 10px;
    padding: .6rem .75rem; border-radius: 8px;
    color: #64748b; font-size: .82rem;
    text-decoration: none; transition: background .12s;
}
.ql-item:hover { background: #f1f5f9; color: #0f172a; }
.ql-item i { font-size: 15px; flex-shrink: 0; }

/* ── Columna derecha: Cards ── */
.info-card {
    background: #fff;
    border: 1px solid #e2e8f0;
    border-radius: 14px;
    overflow: hidden;
    margin-bottom: 1rem;
}
.ic-head {
    display: flex; align-items: center; justify-content: space-between;
    padding: 1rem 1.4rem;
    border-bottom: 1px solid #f1f5f9;
    background: #fafafa;
}
.ic-title {
    font-size: .75rem; font-weight: 700; text-transform: uppercase;
    letter-spacing: .6px; color: #94a3b8;
}
.ic-head-ico { font-size: 18px; color: #e2e8f0; }

/* Info rows */
.ip-row {
    display: flex; align-items: center;
    padding: .9rem 1.4rem;
    border-bottom: 1px solid #f8fafc;
    gap: 12px;
}
.ip-row:last-child { border-bottom: none; }
.ip-ico {
    width: 32px; height: 32px; border-radius: 8px;
    display: flex; align-items: center; justify-content: center;
    font-size: 14px; flex-shrink: 0;
}
.ip-key { font-size: .8rem; color: #64748b; flex: 1; }
.ip-val { font-size: .85rem; font-weight: 700; color: #0f172a; text-align: right; }

/* Security rows */
.sec-row {
    display: flex; align-items: center; justify-content: space-between;
    padding: .85rem 1.4rem;
    border-bottom: 1px solid #f8fafc;
    gap: 12px;
}
.sec-row:last-child { border-bottom: none; }
.sec-left { display: flex; align-items: center; gap: 10px; }
.sec-label { font-size: .82rem; font-weight: 600; color: #0f172a; }
.sec-sub { font-size: .72rem; color: #94a3b8; margin-top: 1px; }
.sec-badge {
    font-size: .68rem; font-weight: 600;
    padding: 3px 9px; border-radius: 999px;
    background: #dcfce7; color: #166534;
    flex-shrink: 0;
}

/* Actions */
.profile-actions {
    display: flex; gap: .7rem;
    padding: 1rem 1.4rem;
    border-top: 1px solid #f1f5f9;
    flex-wrap: wrap;
}
</style>
@endpush

@section('content')

<div class="page-header" style="display:flex;align-items:flex-start;justify-content:space-between;flex-wrap:wrap;gap:.8rem;margin-bottom:1.5rem">
    <div>
        <div class="page-title">Mi Perfil</div>
        <div class="page-sub">Tu información registrada en el sistema</div>
    </div>
    <a href="{{ route('dashboard') }}" class="btn btn-secondary btn-sm">
        <i class="ti ti-arrow-left" style="font-size:13px"></i> Dashboard
    </a>
</div>

<div class="profile-layout">

    {{-- ── COLUMNA IZQUIERDA ── --}}
    <div>

        {{-- Hero card --}}
        <div class="profile-hero">
            <div class="ph-accent"></div>
            <div class="ph-accent2"></div>
            <div class="ph-bg-letter">{{ strtoupper(substr($user->name, 0, 1)) }}</div>

           <div class="ph-inner">
                <div class="ph-eyebrow">Fiesta Tours · Mi cuenta</div>
                
                @php
                    $user = auth()->user();
                    $hasAvatar = !empty($user->avatar);
                    $initials = strtoupper(substr($user->name, 0, 2));
                    $filename = $hasAvatar ? basename($user->avatar) : null;
                @endphp
                
                @if($hasAvatar)
                    <div class="ph-avatar">
                        <img class="ph-avatar_image" 
                            src="{{ route('avatar.show', $filename) }}" 
                            alt="Avatar de {{ $user->name }}"
                            onerror="this.style.display='none'; this.parentElement.textContent='{{ $initials }}'; this.parentElement.style.display='flex'; this.parentElement.style.alignItems='center'; this.parentElement.style.justifyContent='center'; this.parentElement.style.background='linear-gradient(135deg, #667eea 0%, #764ba2 100%)'; this.parentElement.style.color='#fff'; this.parentElement.style.fontWeight='600'; this.parentElement.style.fontSize='1.2rem';">
                    </div>
                @else
                    <div class="ph-avatar" style="display:flex; align-items:center; justify-content:center; background:linear-gradient(135deg, #667eea 0%, #764ba2 100%); color:#fff; font-weight:600; font-size:1.2rem; border-radius:50%; width:50px; height:50px;">
                        {{ $initials }}
                    </div>
                @endif
                
                <div class="ph-name">{{ $user->name }}</div>
                
                <span class="ph-role" style="{{ $user->isAdmin() ? 'background:rgba(109,40,217,.3);color:#c4b5fd' : 'background:rgba(22,101,52,.3);color:#86efac' }}">
                    <i class="ti {{ $user->isAdmin() ? 'ti-shield-check' : 'ti-user' }}" style="font-size:10px"></i>
                    {{ ucfirst($user->role) }}
                </span>
            </div>

            <a href="{{ route('perfil.edit') }}" class="ph-edit-btn">
                <i class="ti ti-edit" style="font-size:13px"></i> Editar perfil
            </a>
        </div>

        {{-- Actividad --}}
        <div class="activity-section">
            <div class="ic-head">
                <span class="ic-title">Actividad</span>
                <i class="ti ti-chart-bar ic-head-ico"></i>
            </div>
            <div class="activity-grid">
                <div class="act-card">
                    <div class="act-ico" style="background:#ede9fe"><i class="ti ti-calendar-event" style="color:#6d28d9"></i></div>
                    <div class="act-num">—</div>
                    <div class="act-label">Reservas</div>
                </div>
                <div class="act-card">
                    <div class="act-ico" style="background:#dbeafe"><i class="ti ti-users" style="color:#1d4ed8"></i></div>
                    <div class="act-num">—</div>
                    <div class="act-label">Clientes</div>
                </div>
                <div class="act-card">
                    <div class="act-ico" style="background:#dcfce7"><i class="ti ti-map-pin" style="color:#166534"></i></div>
                    <div class="act-num">—</div>
                    <div class="act-label">Destinos</div>
                </div>
                <div class="act-card">
                    <div class="act-ico" style="background:#fef9c3"><i class="ti ti-report" style="color:#ca8a04"></i></div>
                    <div class="act-num">—</div>
                    <div class="act-label">Reportes</div>
                </div>
            </div>
        </div>

        {{-- Accesos rápidos --}}
        <div class="quick-links-section">
            <div class="ql-inner">
                <span class="ql-label">Accesos rápidos</span>
                <a href="{{ route('perfil.edit') }}" class="ql-item">
                    <i class="ti ti-edit"></i> Editar perfil
                </a>
                <a href="{{ route('perfil.edit') }}#password" class="ql-item">
                    <i class="ti ti-lock"></i> Cambiar contraseña
                </a>
                @if($user->isAdmin())
                <a href="{{ route('admin.usuarios') }}" class="ql-item">
                    <i class="ti ti-users"></i> Gestionar usuarios
                </a>
                @endif
                <a href="{{ route('dashboard') }}" class="ql-item">
                    <i class="ti ti-layout-dashboard"></i> Dashboard
                </a>
            </div>
        </div>

    </div>

    {{-- ── COLUMNA DERECHA ── --}}
    <div>

        {{-- Información de la cuenta --}}
        <div class="info-card">
            <div class="ic-head">
                <span class="ic-title">Información de la cuenta</span>
                <i class="ti ti-user-circle ic-head-ico"></i>
            </div>

            <div class="ip-row">
                <div class="ip-ico" style="background:#ede9fe"><i class="ti ti-user" style="color:#6d28d9"></i></div>
                <span class="ip-key">Nombre completo</span>
                <span class="ip-val">{{ $user->name }}</span>
            </div>
            <div class="ip-row">
                <div class="ip-ico" style="background:#dbeafe"><i class="ti ti-mail" style="color:#1d4ed8"></i></div>
                <span class="ip-key">Correo electrónico</span>
                <span class="ip-val">{{ $user->email }}</span>
            </div>
            <div class="ip-row">
                <div class="ip-ico" style="{{ $user->isAdmin() ? 'background:#ede9fe' : 'background:#dcfce7' }}">
                    <i class="ti ti-shield" style="color:{{ $user->isAdmin() ? '#6d28d9' : '#166534' }}"></i>
                </div>
                <span class="ip-key">Rol en el sistema</span>
                <span class="ip-val">
                    <span style="display:inline-flex;align-items:center;gap:4px;font-size:.73rem;padding:3px 10px;border-radius:999px;
                        background:{{ $user->isAdmin() ? '#ede9fe' : '#dcfce7' }};
                        color:{{ $user->isAdmin() ? '#6d28d9' : '#166534' }}">
                        <i class="ti {{ $user->isAdmin() ? 'ti-shield-check' : 'ti-user' }}" style="font-size:10px"></i>
                        {{ ucfirst($user->role) }}
                    </span>
                </span>
            </div>
            <div class="ip-row">
                <div class="ip-ico" style="background:#fef9c3"><i class="ti ti-calendar" style="color:#ca8a04"></i></div>
                <span class="ip-key">Miembro desde</span>
                <span class="ip-val">{{ $user->created_at->format('d \d\e F \d\e Y') }}</span>
            </div>
            <div class="ip-row">
                <div class="ip-ico" style="background:#f0fdf4"><i class="ti ti-clock" style="color:#16a34a"></i></div>
                <span class="ip-key">Último acceso</span>
                <span class="ip-val" style="color:#94a3b8">Hoy</span>
            </div>

            <div class="profile-actions">
                <a href="{{ route('perfil.edit') }}" class="btn btn-primary">
                    <i class="ti ti-edit" style="font-size:14px"></i> Editar perfil
                </a>
                <a href="{{ route('dashboard') }}" class="btn btn-secondary">
                    <i class="ti ti-arrow-left" style="font-size:14px"></i> Volver al dashboard
                </a>
            </div>
        </div>

        {{-- Seguridad --}}
        <div class="info-card">
            <div class="ic-head">
                <span class="ic-title">Seguridad</span>
                <i class="ti ti-lock ic-head-ico"></i>
            </div>

            <div class="sec-row">
                <div class="sec-left">
                    <div class="ip-ico" style="background:#dcfce7"><i class="ti ti-lock-password" style="color:#166534"></i></div>
                    <div>
                        <div class="sec-label">Contraseña</div>
                        <div class="sec-sub">Actualiza tu contraseña periódicamente</div>
                    </div>
                </div>
                <span class="sec-badge">Activa</span>
            </div>
            <div class="sec-row">
                <div class="sec-left">
                    <div class="ip-ico" style="background:#dbeafe"><i class="ti ti-device-laptop" style="color:#1d4ed8"></i></div>
                    <div>
                        <div class="sec-label">Sesión activa</div>
                        <div class="sec-sub">Lima, Perú</div>
                    </div>
                </div>
                <span class="sec-badge">En curso</span>
            </div>
            <div class="sec-row">
                <div class="sec-left">
                    <div class="ip-ico" style="background:#fef9c3"><i class="ti ti-mail-check" style="color:#ca8a04"></i></div>
                    <div>
                        <div class="sec-label">Correo verificado</div>
                        <div class="sec-sub">{{ $user->email }}</div>
                    </div>
                </div>
                <span class="sec-badge">Verificado</span>
            </div>
        </div>

        {{-- Sistema --}}
        <div class="info-card">
            <div class="ic-head">
                <span class="ic-title">Sistema</span>
                <i class="ti ti-settings ic-head-ico"></i>
            </div>

            <div class="ip-row">
                <div class="ip-ico" style="background:#f0fdf4"><i class="ti ti-world" style="color:#16a34a"></i></div>
                <span class="ip-key">Zona horaria</span>
                <span class="ip-val">America/Lima (UTC-5)</span>
            </div>
            <div class="ip-row">
                <div class="ip-ico" style="background:#ede9fe"><i class="ti ti-language" style="color:#6d28d9"></i></div>
                <span class="ip-key">Idioma</span>
                <span class="ip-val">Español</span>
            </div>
            <div class="ip-row">
                <div class="ip-ico" style="background:#dbeafe"><i class="ti ti-device-desktop" style="color:#1d4ed8"></i></div>
                <span class="ip-key">Versión del sistema</span>
                <span class="ip-val">v1.0.0</span>
            </div>
        </div>

    </div>
</div>

@endsection
