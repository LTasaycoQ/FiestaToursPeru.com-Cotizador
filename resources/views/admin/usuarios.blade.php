@extends('layouts.app')
@section('title', 'Usuarios')
@section('content')

<style>
    .badge {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        padding: 4px 12px;
        border-radius: 20px;
        font-size: 11px;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: .3px;
    }
    .badge-admin {
        background: #e8ece9;
        color: #3C4E3E;
    }
    .badge-usuario {
        background: #dcfce7;
        color: #166534;
    }
    .avatar-sm {
        width: 32px;
        height: 32px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        font-size: 12px;
        flex-shrink: 0;
        overflow: hidden;
    }
    .avatar-sm img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        border-radius: 50%;
    }
    select:focus, input:focus {
        border-color: #3C4E3E !important;
        box-shadow: 0 0 0 3px rgba(60,78,62,0.1);
    }
    .btn {
        display: inline-flex;
        align-items: center;
        gap: .4rem;
        padding: .6rem 1.2rem;
        border-radius: 10px;
        font-weight: 600;
        font-size: .8rem;
        border: none;
        transition: all .2s;
        cursor: pointer;
        text-decoration: none;
    }
    .btn-primary {
        background: #3C4E3E;
        color: #fff;
    }
    .btn-primary:hover {
        background: #2d3d2f;
        transform: translateY(-2px);
        box-shadow: 0 8px 16px rgba(60, 78, 62, .25);
    }
    .btn-sm {
        font-size: .75rem;
        padding: .45rem 1rem;
    }
</style>

<div class="page-header" style="display:flex;justify-content:space-between;align-items:flex-start;margin-bottom:1.2rem">
    <div>
        <div class="page-title">Usuarios</div>
        <div class="page-sub">Gestiona todos los usuarios del sistema</div>
    </div>
    <a href="{{ route('admin.usuarios.create') }}" class="btn btn-primary">
        <i class="ti ti-user-plus" style="font-size:15px"></i> Crear usuario
    </a>
</div>

{{-- ── BARRA DE FILTROS ── --}}
<div style="margin-bottom:1.2rem; background:#fff; padding:.8rem 1rem; border-radius:12px; border:1px solid #e2e8f0;">
    <form method="GET" action="{{ route('admin.usuarios') }}" style="display:flex; flex-wrap:wrap; gap:8px; align-items:center;">
        
        <div style="position:relative;flex:1;min-width:180px">
            <i class="ti ti-search" style="position:absolute;left:.7rem;top:50%;transform:translateY(-50%);color:#94a3b8;font-size:15px"></i>
            <input type="text" name="search"
                   placeholder="Buscar usuario..."
                   value="{{ request('search') }}"
                   style="width:100%;padding:.5rem .7rem .5rem 2.2rem;border:1px solid #e2e8f0;border-radius:8px;font-size:13px;color:#0f172a;outline:none;transition:border-color .15s;box-sizing:border-box">
        </div>

        <div style="width:1px;height:32px;background:#e2e8f0;flex-shrink:0"></div>

        <select name="role" style="min-width:140px;padding:.5rem .7rem;border:1px solid #e2e8f0;border-radius:8px;font-size:13px;color:#0f172a;background:#fff;outline:none;transition:border-color .15s;box-sizing:border-box;cursor:pointer">
            <option value="">Todos los roles</option>
            <option value="admin" {{ request('role') == 'admin' ? 'selected' : '' }}>Administradores</option>
            <option value="usuario" {{ request('role') == 'usuario' ? 'selected' : '' }}>Usuarios</option>
        </select>

        <select name="sort" style="min-width:160px;padding:.5rem .7rem;border:1px solid #e2e8f0;border-radius:8px;font-size:13px;color:#0f172a;background:#fff;outline:none;transition:border-color .15s;box-sizing:border-box;cursor:pointer">
            <option value="newest" {{ request('sort', 'newest') == 'newest' ? 'selected' : '' }}>Más recientes</option>
            <option value="oldest" {{ request('sort') == 'oldest' ? 'selected' : '' }}>Más antiguos</option>
            <option value="az" {{ request('sort') == 'az' ? 'selected' : '' }}>Nombre A → Z</option>
            <option value="za" {{ request('sort') == 'za' ? 'selected' : '' }}>Nombre Z → A</option>
        </select>

        <button type="submit" style="padding:.5rem 1.2rem;background:#3C4E3E;color:#fff;border:none;border-radius:8px;font-size:13px;font-weight:600;cursor:pointer;transition:all .2s;display:inline-flex;align-items:center;gap:6px;white-space:nowrap"
                onmouseover="this.style.background='#2d3d2f';this.style.transform='translateY(-1px)';this.style.boxShadow='0 4px 12px rgba(60,78,62,.25)'"
                onmouseout="this.style.background='#3C4E3E';this.style.transform='';this.style.boxShadow=''">
            <i class="ti ti-filter" style="font-size:14px"></i> Filtrar
        </button>

        <a href="{{ route('admin.usuarios') }}" 
           style="padding:.5rem .9rem;background:none;border:1px solid #e2e8f0;border-radius:8px;font-size:13px;color:#64748b;cursor:pointer;display:inline-flex;align-items:center;gap:5px;white-space:nowrap;text-decoration:none;transition:all .15s"
           onmouseover="this.style.background='#e8ece9';this.style.borderColor='#3C4E3E';this.style.color='#3C4E3E'"
           onmouseout="this.style.background='none';this.style.borderColor='#e2e8f0';this.style.color='#64748b'">
            <i class="ti ti-filter-off" style="font-size:14px"></i> Limpiar
        </a>

        <span style="font-size:12px;color:#94a3b8;margin-left:auto;">
            {{ $users->total() }} usuario(s)
        </span>
    </form>
</div>

@if(session('success'))
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            Swal.fire({
                icon: 'success',
                title: 'Exito!',
                text: '{{ session('success') }}',
                timer: 3000,
                showConfirmButton: true,
                confirmButtonColor: '#3C4E3E'
            });
        });
    </script>
@endif

@if(session('error'))
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            Swal.fire({
                icon: 'error',
                title: 'Error!',
                text: '{{ session('error') }}',
                confirmButtonColor: '#ef4444'
            });
        });
    </script>
@endif

<div class="table-wrap" style="background:#fff;border-radius:12px;border:1px solid #e2e8f0;overflow:hidden">
    <div style="overflow-x:auto">
        <table style="width:100%;border-collapse:collapse;font-size:13px">
            <thead style="background:#e8ece9;border-bottom:1px solid #e2e8f0">
                <tr>
                    <th style="padding:12px 14px;text-align:left;font-weight:600;color:#3C4E3E;font-size:11px;text-transform:uppercase;letter-spacing:.5px">Usuario</th>
                    <th style="padding:12px 14px;text-align:left;font-weight:600;color:#3C4E3E;font-size:11px;text-transform:uppercase;letter-spacing:.5px">Correo</th>
                    <th style="padding:12px 14px;text-align:left;font-weight:600;color:#3C4E3E;font-size:11px;text-transform:uppercase;letter-spacing:.5px">Rol</th>
                    <th style="padding:12px 14px;text-align:left;font-weight:600;color:#3C4E3E;font-size:11px;text-transform:uppercase;letter-spacing:.5px">Registro</th>
                    <th style="padding:12px 14px;text-align:center;font-weight:600;color:#3C4E3E;font-size:11px;text-transform:uppercase;letter-spacing:.5px">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse($users as $u)
                    <tr style="border-bottom:1px solid #f1f5f9;transition:background .1s" 
                        onmouseover="this.style.background='#f8fafc'" 
                        onmouseout="this.style.background=''">
                        <td style="padding:12px 14px">
                            <div style="display:flex;align-items:center;gap:10px">
                                @php
                                    $filename = basename($u->avatar);
                                @endphp
                                @if($u->avatar)
                                    <div class="avatar-sm"><img class="avatar-sm" src="{{ route('avatar.show', $filename) }}" /></div>
                                @else
                                    <div class="avatar-sm" style="background:{{ $u->isAdmin() ? '#e8ece9' : '#dcfce7' }};color:{{ $u->isAdmin() ? '#3C4E3E' : '#166534' }}">
                                        {{ strtoupper(substr($u->name, 0, 2)) }}
                                    </div>
                                @endif
                                <div style="font-weight:600;font-size:13px">{{ $u->name }}</div>
                            </div>
                        </td>
                        <td style="padding:12px 14px;color:#64748b;font-size:12px">{{ $u->email }}</td>
                        <td style="padding:12px 14px">
                            <span class="badge {{ $u->isAdmin() ? 'badge-admin' : 'badge-usuario' }}">
                                <i class="ti {{ $u->isAdmin() ? 'ti-shield' : 'ti-user' }}" style="font-size:11px"></i>
                                {{ ucfirst($u->role) }}
                            </span>
                        </td>
                        <td style="padding:12px 14px;color:#94a3b8;font-size:12px">{{ $u->created_at->format('d/m/Y') }}</td>
                        <td style="padding:12px 14px;text-align:center">
                            <div style="display:flex;gap:4px;justify-content:center">
                                <a href="{{ route('admin.usuarios.edit', $u) }}" 
                                   style="display:inline-flex;align-items:center;justify-content:center;width:32px;height:32px;background:#e8ece9;color:#3C4E3E;border-radius:6px;text-decoration:none;transition:all .15s"
                                   onmouseover="this.style.background='#d4dcd5'"
                                   onmouseout="this.style.background='#e8ece9'"
                                   title="Editar">
                                    <i class="ti ti-edit" style="font-size:15px"></i>
                                </a>
                                <button onclick="deleteUser({{ $u->id }}, '{{ addslashes($u->name) }}')"
                                        style="display:inline-flex;align-items:center;justify-content:center;width:32px;height:32px;background:#fee2e2;color:#dc2626;border:none;border-radius:6px;cursor:pointer;transition:all .15s"
                                        onmouseover="this.style.background='#fecaca'"
                                        onmouseout="this.style.background='#fee2e2'"
                                        title="Eliminar">
                                    <i class="ti ti-trash" style="font-size:15px"></i>
                                </button>
                                <form id="delete-form-{{ $u->id }}" action="{{ route('admin.usuarios.destroy', $u) }}" method="POST" style="display:none">
                                    @csrf
                                    @method('DELETE')
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" style="padding:3rem;text-align:center;color:#94a3b8">
                            <i class="ti ti-users-off" style="font-size:40px;display:block;margin-bottom:.8rem;color:#cbd5e1"></i>
                            @if(request()->anyFilled(['search', 'role']))
                                No se encontraron usuarios con los filtros aplicados
                            @else
                                No hay usuarios registrados
                            @endif
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    {{-- ── PAGINADOR ── --}}
    @if($users->hasPages())
    <div style="display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:.5rem;padding:1rem 1.5rem;border-top:1px solid #e2e8f0;background:#fafbfc">
        <span style="font-size:13px;color:#64748b">
            Mostrando {{ $users->firstItem() }}–{{ $users->lastItem() }} de {{ $users->total() }} usuario(s)
        </span>
        
        <div style="display:flex;align-items:center;gap:.3rem">
            @if($users->onFirstPage())
                <span style="padding:.35rem .7rem;border:1px solid #e2e8f0;border-radius:7px;font-size:12px;color:#cbd5e1;cursor:default;background:#fff">
                    <i class="ti ti-chevron-left"></i>
                </span>
            @else
                <a href="{{ $users->previousPageUrl() }}"
                   style="padding:.35rem .7rem;border:1px solid #e2e8f0;border-radius:7px;font-size:12px;color:#374151;text-decoration:none;background:#fff;transition:all .15s"
                   onmouseover="this.style.borderColor='#3C4E3E';this.style.color='#3C4E3E'"
                   onmouseout="this.style.borderColor='#e2e8f0';this.style.color='#374151'">
                    <i class="ti ti-chevron-left"></i>
                </a>
            @endif

            @php
                $current = $users->currentPage();
                $last = $users->lastPage();
                $range = 2;
            @endphp

            @for($i = 1; $i <= $last; $i++)
                @if($i == 1 || $i == $last || abs($i - $current) <= $range)
                    @if($i == $current)
                        <span style="padding:.35rem .65rem;border:1px solid #3C4E3E;border-radius:7px;font-size:12px;font-weight:700;color:#fff;background:#3C4E3E;min-width:32px;text-align:center">
                            {{ $i }}
                        </span>
                    @else
                        <a href="{{ $users->url($i) }}"
                           style="padding:.35rem .65rem;border:1px solid #e2e8f0;border-radius:7px;font-size:12px;color:#374151;text-decoration:none;background:#fff;min-width:32px;text-align:center;transition:all .15s"
                           onmouseover="this.style.borderColor='#3C4E3E';this.style.color='#3C4E3E'"
                           onmouseout="this.style.borderColor='#e2e8f0';this.style.color='#374151'">
                            {{ $i }}
                        </a>
                    @endif
                @elseif(abs($i - $current) == $range + 1)
                    <span style="font-size:12px;color:#94a3b8;padding:0 .1rem">…</span>
                @endif
            @endfor

            @if($users->hasMorePages())
                <a href="{{ $users->nextPageUrl() }}"
                   style="padding:.35rem .7rem;border:1px solid #e2e8f0;border-radius:7px;font-size:12px;color:#374151;text-decoration:none;background:#fff;transition:all .15s"
                   onmouseover="this.style.borderColor='#3C4E3E';this.style.color='#3C4E3E'"
                   onmouseout="this.style.borderColor='#e2e8f0';this.style.color='#374151'">
                    <i class="ti ti-chevron-right"></i>
                </a>
            @else
                <span style="padding:.35rem .7rem;border:1px solid #e2e8f0;border-radius:7px;font-size:12px;color:#cbd5e1;cursor:default;background:#fff">
                    <i class="ti ti-chevron-right"></i>
                </span>
            @endif
        </div>
    </div>
    @endif
</div>

<script>
function deleteUser(id, name) {
    Swal.fire({
        title: 'Eliminar usuario',
        html: '¿Estás seguro de eliminar a <strong>' + name + '</strong>?<br>Esta acción no se puede deshacer.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3C4E3E',
        cancelButtonColor: '#64748b',
        confirmButtonText: 'Si, eliminar',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            document.getElementById('delete-form-' + id).submit();
        }
    });
}
</script>

@if($errors->any())
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            Swal.fire({
                icon: 'error',
                title: 'Error!',
                html: `{!! implode('<br>', $errors->all()) !!}`,
                confirmButtonColor: '#ef4444'
            });
        });
    </script>
@endif

@endsection