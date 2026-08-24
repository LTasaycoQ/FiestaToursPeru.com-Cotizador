@extends('layouts.app')
@section('title', 'Mi Perfil')
@section('content')

<div style="max-width:500px">
    <h2 style="font-size:1.4rem;font-weight:700;color:#0f172a;margin-bottom:1.5rem">👤 Mi Perfil</h2>

    <div style="background:#fff;border-radius:12px;border:1px solid #e2e8f0;padding:1.8rem">
        <div style="display:flex;align-items:center;gap:1rem;margin-bottom:1.5rem;padding-bottom:1.2rem;border-bottom:1px solid #f1f5f9">
            <div style="width:56px;height:56px;border-radius:50%;background:linear-gradient(135deg,#6366f1,#8b5cf6);display:flex;align-items:center;justify-content:center;font-size:18px;color:#fff;font-weight:700">
                {{ strtoupper(substr(auth()->user()->name, 0, 2)) }}
            </div>
            <div>
                <div style="font-size:1rem;font-weight:700;color:#0f172a">{{ auth()->user()->name }}</div>
                <div style="font-size:.85rem;color:#64748b">{{ auth()->user()->email }}</div>
            </div>
        </div>

        <div style="display:flex;flex-direction:column;gap:.8rem">
            <div style="display:flex;justify-content:space-between;padding:.6rem 0;border-bottom:1px solid #f8fafc">
                <span style="font-size:.85rem;color:#64748b">Nombre</span>
                <span style="font-size:.85rem;font-weight:600;color:#0f172a">{{ auth()->user()->name }}</span>
            </div>
            <div style="display:flex;justify-content:space-between;padding:.6rem 0;border-bottom:1px solid #f8fafc">
                <span style="font-size:.85rem;color:#64748b">Correo</span>
                <span style="font-size:.85rem;font-weight:600;color:#0f172a">{{ auth()->user()->email }}</span>
            </div>
            <div style="display:flex;justify-content:space-between;padding:.6rem 0;border-bottom:1px solid #f8fafc">
                <span style="font-size:.85rem;color:#64748b">Rol</span>
                <span style="font-size:.78rem;font-weight:700;padding:.25rem .8rem;border-radius:999px;
                    background:{{ auth()->user()->isAdmin() ? '#ede9fe' : '#dcfce7' }};
                    color:{{ auth()->user()->isAdmin() ? '#6d28d9' : '#166534' }}">
                    {{ ucfirst(auth()->user()->role) }}
                </span>
            </div>
            <div style="display:flex;justify-content:space-between;padding:.6rem 0">
                <span style="font-size:.85rem;color:#64748b">Miembro desde</span>
                <span style="font-size:.85rem;font-weight:600;color:#0f172a">{{ auth()->user()->created_at->format('d/m/Y') }}</span>
            </div>
        </div>
    </div>
</div>
@endsection
