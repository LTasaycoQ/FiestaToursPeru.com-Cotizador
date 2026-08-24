@extends('layouts.app')
@section('title', 'Proveedores')
@section('content')

<div id="modal-pdf-loading" style="display:none; position:fixed; inset:0; background:rgba(15,23,42,0.6); backdrop-filter:blur(4px); z-index:10000; justify-content:center; align-items:center; padding:1rem;">
    <div style="background:#fff; border-radius:16px; padding:2.5rem; max-width:400px; width:100%; text-align:center; animation: modalFadeIn .2s ease-out;">
        <div style="margin-bottom:1.5rem;">
            <div style="display:inline-block; width:60px; height:60px; border:4px solid #e2e8f0; border-top:4px solid #6366f1; border-radius:50%; animation: spin 0.8s linear infinite;"></div>
        </div>
        <h3 style="font-size:18px; font-weight:700; color:#0f172a; margin-bottom:.5rem;">Generando PDF</h3>
        <p style="font-size:13px; color:#94a3b8; margin-bottom:1rem;">Por favor espera, estamos preparando tu documento...</p>
        <div style="width:100%; height:4px; background:#e2e8f0; border-radius:2px; overflow:hidden;">
            <div id="pdf-progress-bar" style="width:0%; height:100%; background:linear-gradient(90deg, #6366f1, #8b5cf6); border-radius:2px; transition:width .3s ease;"></div>
        </div>
        <p id="pdf-progress-text" style="font-size:11px; color:#94a3b8; margin-top:.5rem;">0%</p>
    </div>
</div>

<div class="page-header" style="display:flex;justify-content:space-between;align-items:flex-start;margin-bottom:1.6rem">
    <div>
        <div class="page-title">Proveedores</div>
        <div class="page-sub">Gestiona todos los proveedores del sistema</div>
    </div>
    <div style="display:flex;gap:.6rem;align-items:center;flex-wrap:wrap">
        <a href="{{ route('admin.suppliers.import.view') }}"
           style="display:inline-flex;align-items:center;gap:6px;padding:.5rem .9rem;
                  background:#fff;border:1px solid #e2e8f0;border-radius:8px;
                  font-size:13px;font-weight:600;color:#6366f1;text-decoration:none;
                  transition:all .15s"
           onmouseover="this.style.background='#f5f3ff';this.style.borderColor='#6366f1'"
           onmouseout="this.style.background='#fff';this.style.borderColor='#e2e8f0'">
            <i class="ti ti-file-upload" style="font-size:16px"></i> Importar
        </a>
        <a href="#" id="btn-export-pdf-suppliers"
           style="display:inline-flex;align-items:center;gap:6px;padding:.5rem .9rem;
                  background:#fff;border:1px solid #e2e8f0;border-radius:8px;
                  font-size:13px;font-weight:600;color:#ef4444;text-decoration:none;
                  transition:all .15s"
           onmouseover="this.style.background='#fef2f2';this.style.borderColor='#ef4444'"
           onmouseout="this.style.background='#fff';this.style.borderColor='#e2e8f0'">
            <i class="ti ti-file-type-pdf" style="font-size:16px"></i> PDF
        </a>
        <a href="#" id="btn-export-excel-suppliers"
           style="display:inline-flex;align-items:center;gap:6px;padding:.5rem .9rem;
                  background:#fff;border:1px solid #e2e8f0;border-radius:8px;
                  font-size:13px;font-weight:600;color:#16a34a;text-decoration:none;
                  transition:all .15s"
           onmouseover="this.style.background='#f0fdf4';this.style.borderColor='#16a34a'"
           onmouseout="this.style.background='#fff';this.style.borderColor='#e2e8f0'">
            <i class="ti ti-file-type-xls" style="font-size:16px"></i> Excel
        </a>
        <a href="{{ route('admin.suppliers.create') }}" class="btn btn-primary" style="display:inline-flex;align-items:center;gap:6px">
            <i class="ti ti-plus" style="font-size:15px"></i> Nuevo proveedor
        </a>
    </div>
</div>

{{-- FILTROS DE ESTADO (ACTIVOS / ELIMINADOS / TODOS) --}}
<div style="margin-bottom:1.2rem; background:#f8fafc; padding:0.6rem 1.1rem; border-radius:12px; border:1px solid #e2e8f0; display:flex; align-items:center; flex-wrap:wrap; gap:8px;">
    <span style="font-size: 12px; font-weight: 600; color: #64748b; margin-right: 0.5rem;">
        <i class="ti ti-filter"></i> Estado:
    </span>
    <div style="display: flex; gap: 6px; flex-wrap: wrap;">
        {{-- Botón "Activos" - visible para todos --}}
        <a href="{{ route('admin.suppliers.index', array_merge(request()->except(['filter', 'page']), ['filter' => 'active'])) }}"
           class="filter-status-btn {{ request('filter', 'active') == 'active' ? 'active' : '' }}"
           style="padding:0.3rem 1rem; border-radius:20px; font-size:12px; font-weight:600; text-decoration:none; display:inline-flex; align-items:center; gap:5px; border:1px solid {{ request('filter', 'active') == 'active' ? '#16a34a' : '#e2e8f0' }};
                  background: {{ request('filter', 'active') == 'active' ? '#16a34a' : '#f1f5f9' }};
                  color: {{ request('filter', 'active') == 'active' ? '#fff' : '#64748b' }};
                  transition:all .15s;">
            <i class="ti ti-user-check" style="font-size:13px;"></i>
            Activos <span style="padding:0 6px; border-radius:10px; font-size:10px; background: {{ request('filter', 'active') == 'active' ? 'rgba(255,255,255,0.2)' : '#e2e8f0' }};">{{ $activeCount ?? 0 }}</span>
        </a>

        {{-- Botones "Eliminados" y "Todos" - solo para administradores --}}
        @if(auth()->check() && auth()->user()->isAdmin())
            <a href="{{ route('admin.suppliers.index', array_merge(request()->except(['filter', 'page']), ['filter' => 'trashed'])) }}"
               class="filter-status-btn {{ request('filter') == 'trashed' ? 'active' : '' }}"
               style="padding:0.3rem 1rem; border-radius:20px; font-size:12px; font-weight:600; text-decoration:none; display:inline-flex; align-items:center; gap:5px; border:1px solid {{ request('filter') == 'trashed' ? '#dc2626' : '#e2e8f0' }};
                      background: {{ request('filter') == 'trashed' ? '#dc2626' : '#f1f5f9' }};
                      color: {{ request('filter') == 'trashed' ? '#fff' : '#64748b' }};
                      transition:all .15s;">
                <i class="ti ti-user-x" style="font-size:13px;"></i>
                Eliminados <span style="padding:0 6px; border-radius:10px; font-size:10px; background: {{ request('filter') == 'trashed' ? 'rgba(255,255,255,0.2)' : '#e2e8f0' }};">{{ $trashedCount ?? 0 }}</span>
            </a>

            <a href="{{ route('admin.suppliers.index', array_merge(request()->except(['filter', 'page']), ['filter' => 'all'])) }}"
               class="filter-status-btn {{ request('filter') == 'all' ? 'active' : '' }}"
               style="padding:0.3rem 1rem; border-radius:20px; font-size:12px; font-weight:600; text-decoration:none; display:inline-flex; align-items:center; gap:5px; border:1px solid {{ request('filter') == 'all' ? '#6366f1' : '#e2e8f0' }};
                      background: {{ request('filter') == 'all' ? '#6366f1' : '#f1f5f9' }};
                      color: {{ request('filter') == 'all' ? '#fff' : '#64748b' }};
                      transition:all .15s;">
                <i class="ti ti-users" style="font-size:13px;"></i>
                Todos <span style="padding:0 6px; border-radius:10px; font-size:10px; background: {{ request('filter') == 'all' ? 'rgba(255,255,255,0.2)' : '#e2e8f0' }};">{{ $totalCount ?? 0 }}</span>
            </a>
        @endif
    </div>
</div>

<div style="margin-bottom:1.2rem; background:#fff; padding:.8rem 1rem; border-radius:12px; border:1px solid #e2e8f0;">
    <form id="filter-form" method="GET" action="{{ route('admin.suppliers.index') }}" style="display:flex; flex-wrap:wrap; gap:8px; align-items:center;">
        <div style="position:relative;flex:1;min-width:200px">
            <i class="ti ti-search" style="position:absolute;left:.7rem;top:50%;transform:translateY(-50%);color:#94a3b8;font-size:15px"></i>
            <input type="text" id="f-search" name="search"
                   placeholder="Buscar por nombre, RUC, email..."
                   value="{{ $search ?? '' }}"
                   style="width:100%;padding:.5rem .7rem .5rem 2.2rem;border:1px solid #e2e8f0;border-radius:8px;font-size:13px;color:#0f172a;outline:none;transition:border-color .15s;box-sizing:border-box">
        </div>

        <div style="width:1px;height:32px;background:#e2e8f0;flex-shrink:0"></div>

        <select id="f-country" name="country" style="min-width:140px;padding:.5rem .7rem;border:1px solid #e2e8f0;border-radius:8px;font-size:13px;color:#0f172a;background:#fff;outline:none;transition:border-color .15s;box-sizing:border-box;cursor:pointer">
            <option value="">Todos los países</option>
            @foreach($countries ?? [] as $countryId => $countryName)
                <option value="{{ $countryId }}" {{ ($country ?? '') == $countryId ? 'selected' : '' }}>
                    {{ $countryName }}
                </option>
            @endforeach
        </select>

        <select id="f-category" name="category" style="min-width:140px;padding:.5rem .7rem;border:1px solid #e2e8f0;border-radius:8px;font-size:13px;color:#0f172a;background:#fff;outline:none;transition:border-color .15s;box-sizing:border-box;cursor:pointer">
            <option value="">Todas las categorías</option>
            @foreach($categories ?? [] as $id => $catName)
                <option value="{{ $id }}" {{ ($category ?? '') == $id ? 'selected' : '' }}>
                    {{ $catName }}
                </option>
            @endforeach
        </select>

        <select id="f-chain" name="chain" style="min-width:140px;padding:.5rem .7rem;border:1px solid #e2e8f0;border-radius:8px;font-size:13px;color:#0f172a;background:#fff;outline:none;transition:border-color .15s;box-sizing:border-box;cursor:pointer">
            <option value="">Todas las cadenas</option>
            <option value="independent" {{ ($chainId ?? '') == 'independent' ? 'selected' : '' }}>
                Independientes
            </option>
            @foreach($chains ?? [] as $id => $chainName)
                <option value="{{ $id }}" {{ ($chainId ?? '') == $id ? 'selected' : '' }}>
                    {{ $chainName }}
                </option>
            @endforeach
        </select>

        <select id="f-sort" name="sort" style="min-width:150px;padding:.5rem .7rem;border:1px solid #e2e8f0;border-radius:8px;font-size:13px;color:#0f172a;background:#fff;outline:none;transition:border-color .15s;box-sizing:border-box;cursor:pointer">
            <option value="newest" {{ ($sort ?? 'newest') == 'newest' ? 'selected' : '' }}>Más recientes</option>
            <option value="oldest" {{ ($sort ?? '') == 'oldest' ? 'selected' : '' }}>Más antiguos</option>
            <option value="az" {{ ($sort ?? '') == 'az' ? 'selected' : '' }}>Proveedor A → Z</option>
            <option value="za" {{ ($sort ?? '') == 'za' ? 'selected' : '' }}>Proveedor Z → A</option>
        </select>

        <div style="width:1px;height:32px;background:#e2e8f0;flex-shrink:0"></div>

        <button type="submit" style="padding:.5rem 1.2rem;background:#6366f1;color:#fff;border:none;border-radius:8px;font-size:13px;font-weight:600;cursor:pointer;transition:background .15s;display:inline-flex;align-items:center;gap:6px;white-space:nowrap"
                onmouseover="this.style.background='#4f46e5'"
                onmouseout="this.style.background='#6366f1'">
            <i class="ti ti-search" style="font-size:14px"></i> Buscar
        </button>

        <button type="button" onclick="clearFilters()"
                style="padding:.5rem .9rem;background:none;border:1px solid #e2e8f0;border-radius:8px;font-size:13px;color:#64748b;cursor:pointer;display:inline-flex;align-items:center;gap:5px;white-space:nowrap;transition:all .15s"
                onmouseover="this.style.background='#f1f5f9';this.style.borderColor='#94a3b8'"
                onmouseout="this.style.background='none';this.style.borderColor='#e2e8f0'">
            <i class="ti ti-filter-off" style="font-size:14px"></i> Limpiar
        </button>

        <div style="display:flex;gap:4px;align-items:center;margin-left:4px;border-left:1px solid #e2e8f0;padding-left:12px">
            <button type="button"
                    onclick="setViewMode('all')"
                    class="view-mode-btn {{ ($viewMode ?? 'all') == 'all' ? 'active' : '' }}"
                    style="padding:.4rem .8rem;border-radius:6px;border:1px solid {{ ($viewMode ?? 'all') == 'all' ? '#6366f1' : '#e2e8f0' }};background:{{ ($viewMode ?? 'all') == 'all' ? '#6366f1' : '#fff' }};color:{{ ($viewMode ?? 'all') == 'all' ? '#fff' : '#64748b' }};font-size:12px;font-weight:600;cursor:pointer;transition:all .15s">
                Todos
            </button>
            <button type="button"
                    onclick="setViewMode('chain')"
                    class="view-mode-btn {{ ($viewMode ?? 'all') == 'chain' ? 'active' : '' }}"
                    style="padding:.4rem .8rem;border-radius:6px;border:1px solid {{ ($viewMode ?? 'all') == 'chain' ? '#6366f1' : '#e2e8f0' }};background:{{ ($viewMode ?? 'all') == 'chain' ? '#6366f1' : '#fff' }};color:{{ ($viewMode ?? 'all') == 'chain' ? '#fff' : '#64748b' }};font-size:12px;font-weight:600;cursor:pointer;transition:all .15s">
                <i class="ti ti-link"></i> Cadena
            </button>
            <button type="button"
                    onclick="setViewMode('independent')"
                    class="view-mode-btn {{ ($viewMode ?? 'all') == 'independent' ? 'active' : '' }}"
                    style="padding:.4rem .8rem;border-radius:6px;border:1px solid {{ ($viewMode ?? 'all') == 'independent' ? '#6366f1' : '#e2e8f0' }};background:{{ ($viewMode ?? 'all') == 'independent' ? '#6366f1' : '#fff' }};color:{{ ($viewMode ?? 'all') == 'independent' ? '#fff' : '#64748b' }};font-size:12px;font-weight:600;cursor:pointer;transition:all .15s">
                <i class="ti ti-user-off"></i> Indep.
            </button>
        </div>

        <input type="hidden" name="view_mode" value="{{ $viewMode ?? 'all' }}">
        <input type="hidden" name="filter" value="{{ $filter ?? 'active' }}">
    </form>
</div>

<div style="margin-bottom:1.2rem; display:flex; gap:.5rem; flex-wrap:wrap;">
    <a href="{{ route('admin.chains.index') }}"
       style="display:inline-flex;align-items:center;gap:6px;padding:.45rem 1rem;border-radius:8px;font-size:13px;font-weight:600;text-decoration:none;transition:all .15s;
              background:#fff;color:#475569;border:1px solid #e2e8f0;"
       onmouseover="this.style.borderColor='#8b5cf6';this.style.color='#8b5cf6'"
       onmouseout="this.style.borderColor='#e2e8f0';this.style.color='#475569'">
        <i class="ti ti-link"></i> Ver cadenas ({{ $totalChain }})
    </a>
</div>

@if(session('success'))
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            Swal.fire({
                icon: 'success',
                title: '¡Éxito!',
                text: '{{ session('success') }}',
                timer: 3000,
                showConfirmButton: true,
                confirmButtonColor: '#6366f1'
            });
        });
    </script>
@endif

@if(session('error'))
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            Swal.fire({
                icon: 'error',
                title: '¡Error!',
                text: '{{ session('error') }}',
                confirmButtonColor: '#ef4444'
            });
        });
    </script>
@endif

@if($errors->any())
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            Swal.fire({
                icon: 'error',
                title: '¡Error de validación!',
                html: `{!! implode('<br>', $errors->all()) !!}`,
                confirmButtonColor: '#ef4444'
            });
        });
    </script>
@endif

<div id="bulk-actions-bar" style="display:none;align-items:center;gap:12px;background:#fef2f2;border:1px solid #fecaca;border-radius:10px;padding:.7rem 1rem;margin-bottom:.8rem">
    <span id="bulk-count-label" style="font-size:13px;font-weight:600;color:#991b1b;flex:1">0 seleccionados</span>
    <button type="button" onclick="clearSelection()"
            style="padding:.4rem .8rem;background:#fff;border:1px solid #fecaca;color:#991b1b;border-radius:7px;font-size:12px;font-weight:600;cursor:pointer">
        Cancelar
    </button>
    <button type="button" onclick="bulkDeleteSelected()"
            style="padding:.4rem .9rem;background:#ef4444;border:none;color:#fff;border-radius:7px;font-size:12px;font-weight:600;cursor:pointer;display:inline-flex;align-items:center;gap:6px">
        <i class="ti ti-trash" style="font-size:14px"></i> Eliminar seleccionados
    </button>
</div>

@if($suppliers->isEmpty())
    <div style="text-align:center;padding:4rem;background:#fff;border-radius:14px;border:1px solid #e2e8f0">
        <i class="ti ti-truck-off" style="font-size:48px;color:#cbd5e1;display:block;margin-bottom:1rem"></i>
        @if(($search ?? '') || ($country ?? '') || ($category ?? '') || ($chainId ?? ''))
            <p style="font-size:15px;font-weight:600;color:#0f172a;margin-bottom:.4rem">No se encontraron proveedores</p>
            <p style="font-size:13px;color:#94a3b8;margin-bottom:1.2rem">
                @if($search) No hay resultados para "{{ $search }}" @endif
                @if($country) en el país seleccionado @endif
                @if($category) en la categoría seleccionada @endif
                @if(($chainId ?? '') === 'independent') que sean independientes @elseif($chainId ?? '') en la cadena seleccionada @endif
                @if(($viewMode ?? 'all') === 'independent') (solo proveedores independientes) @endif
            </p>
            <a href="{{ route('admin.suppliers.index') }}" class="btn btn-secondary btn-sm">
                <i class="ti ti-arrow-left"></i> Ver todos los proveedores
            </a>
        @else
            <p style="font-size:15px;font-weight:600;color:#0f172a;margin-bottom:.4rem">No hay proveedores aún</p>
            <p style="font-size:13px;color:#94a3b8;margin-bottom:1.2rem">Comienza agregando tu primer proveedor</p>
            <a href="{{ route('admin.suppliers.create') }}" class="btn btn-primary btn-sm">
                <i class="ti ti-plus"></i> Crear proveedor
            </a>
        @endif
    </div>
@else
    <div class="table-wrap" style="background:#fff;border-radius:12px;border:1px solid #e2e8f0;overflow:hidden">
        <div style="overflow-x:auto; overflow-y:visible !important;">
            <table style="width:100%;border-collapse:collapse;font-size:13px; overflow:visible !important;">
                <thead style="background:#f8fafc;border-bottom:1px solid #e2e8f0">
                    <tr>
                        <th style="padding:12px 14px;text-align:center;width:40px">
                            <input type="checkbox" id="select-all" onclick="toggleSelectAll(this)" style="width:16px;height:16px;cursor:pointer;accent-color:#6366f1">
                        </th>
                        <th style="padding:12px 16px;text-align:left;font-weight:600;color:#475569;font-size:11px;text-transform:uppercase;letter-spacing:.5px">Proveedor</th>
                        <th style="padding:12px 16px;text-align:left;font-weight:600;color:#475569;font-size:11px;text-transform:uppercase;letter-spacing:.5px">RUC</th>
                        <th style="padding:12px 16px;text-align:left;font-weight:600;color:#475569;font-size:11px;text-transform:uppercase;letter-spacing:.5px">Email</th>
                        <th style="padding:12px 16px;text-align:left;font-weight:600;color:#475569;font-size:11px;text-transform:uppercase;letter-spacing:.5px">Teléfono</th>
                        <th style="padding:12px 16px;text-align:left;font-weight:600;color:#475569;font-size:11px;text-transform:uppercase;letter-spacing:.5px">Ciudad</th>
                        <th style="padding:12px 16px;text-align:left;font-weight:600;color:#475569;font-size:11px;text-transform:uppercase;letter-spacing:.5px">Categoría</th>
                        <th style="padding:12px 16px;text-align:left;font-weight:600;color:#475569;font-size:11px;text-transform:uppercase;letter-spacing:.5px">Registro</th>
                        <th style="padding:12px 16px;text-align:center;font-weight:600;color:#475569;font-size:11px;text-transform:uppercase;letter-spacing:.5px;width:110px">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($suppliers as $s)
                    <tr style="border-bottom:1px solid #f1f5f9;transition:background .1s; {{ $s->trashed() ? 'background:#fef2f2;' : '' }} position:relative;" onmouseover="this.style.background='#fafbfc'" onmouseout="this.style.background='{{ $s->trashed() ? '#fef2f2' : '' }}'">
                        <td style="padding:12px 14px;text-align:center">
                            <input type="checkbox" class="row-checkbox" value="{{ $s->id_supplier }}" onclick="updateBulkBar()" style="width:16px;height:16px;cursor:pointer;accent-color:#6366f1" {{ $s->trashed() ? 'data-trashed="true"' : '' }}>
                        </td>
                        <td style="padding:12px 16px">
                            <div style="display:flex;align-items:center;gap:10px">
                                <div class="cadena_name" style="background:linear-gradient(135deg, #f2e0fe, #c6bafd);color: #7403a1;width:32px;height:32px;border-radius:50%;display:flex;justify-content:center;align-items:center;font-weight:700;font-size:12px;flex-shrink:0">
                                    {{ strtoupper(substr($s->supplier_name, 0, 2)) }}
                                </div>
                                <a href="{{ route('admin.suppliers.productos', ['supplier' => $s->id_supplier]) }}"
                                   style="font-weight:600;color:#0f172a;text-decoration:none;"
                                   onmouseover="this.style.color='#6366f1'"
                                   onmouseout="this.style.color='#0f172a'">
                                    {{ $s->supplier_name }}
                                    @if($s->trashed())
                                        <span style="background:#dc2626;color:#fff;font-size:9px;font-weight:700;padding:2px 8px;border-radius:10px;margin-left:6px;">
                                            <i class="ti ti-trash" style="font-size:10px;"></i> ELIMINADO
                                        </span>
                                    @endif
                                </a>
                            </div>
                        </td>
                        <td style="padding:12px 16px">
                            @if($s->tax_code)
                                <span style="background:#fef3c7;color:#92400e;font-size:12px;padding:3px 8px;border-radius:5px;font-weight:500;font-family:monospace">
                                    {{ $s->tax_code }}
                                </span>
                            @else
                                <span style="color:#cbd5e1;font-size:12px">—</span>
                            @endif
                        </td>
                        <td style="padding:12px 16px;color:#0f172a;font-size:12px">
                            @if($s->general_email)
                                {{ $s->general_email }}
                            @else
                                <span style="color:#cbd5e1">—</span>
                            @endif
                        </td>
                        <td style="padding:12px 16px;color:#0f172a;font-size:12px">
                            @if($s->general_phone)
                                {{ $s->general_phone }}
                            @else
                                <span style="color:#cbd5e1">—</span>
                            @endif
                        </td>
                        <td style="padding:12px 16px">
                            @if($s->city)
                                <span style="background:#dbeafe;color:#1e40af;padding:3px 10px;border-radius:5px;font-size:12px;font-weight:500">
                                    {{ $s->city->name ?? "-" }}
                                </span>
                            @else
                                <span style="color:#cbd5e1;font-size:12px">—</span>
                            @endif
                        </td>
                        <td style="padding:12px 16px">
                            @if($s->category)
                                <span style="background:#ede9fe;color:#6d28d9;padding:3px 10px;border-radius:5px;font-size:12px;font-weight:500">
                                    {{ $s->category->category_name }}
                                </span>
                            @else
                                <span style="color:#cbd5e1;font-size:12px">—</span>
                            @endif
                        </td>
                        <td style="padding:12px 16px;color:#94a3b8;font-size:12px">
                            {{ $s->created_at->format('d/m/Y') }}
                            @if($s->trashed())
                                <br><small style="color:#dc2626;">Eliminado: {{ $s->deleted_at->format('d/m/Y H:i') }}</small>
                            @endif
                        </td>
                        <td style="padding:12px 16px;text-align:center;overflow:visible !important;position:relative;">
                            <div class="dropdown-actions">
                                <button type="button"
                                        class="dropdown-toggle"
                                        onclick="toggleDropdown({{ $s->id_supplier }}, this)"
                                        aria-label="Acciones">
                                    <i class="ti ti-dots-vertical"></i>
                                </button>
                                <div id="dropdown-{{ $s->id_supplier }}" class="dropdown-menu">
                                    @if(!$s->trashed())
                                        <button type="button"
                                                class="dropdown-item btn-open-modal"
                                                data-target="modal-{{ $s->id_supplier }}">
                                            <i class="ti ti-eye icon blue"></i>
                                            Ver detalles
                                        </button>
                                        <button type="button"
                                                class="dropdown-item"
                                                onclick="downloadPDF({{ $s->id_supplier }}, '{{ addslashes($s->supplier_name) }}')">
                                            <i class="ti ti-file-type-pdf icon red"></i>
                                            Descargar PDF
                                        </button>
                                        <a href="{{ route('admin.suppliers.edit', $s->id_supplier) }}"
                                           class="dropdown-item">
                                            <i class="ti ti-edit icon purple"></i>
                                            Editar
                                        </a>
                                        <div class="dropdown-divider"></div>
                                        <button type="button"
                                                class="dropdown-item danger"
                                                onclick="confirmDelete({{ $s->id_supplier }}, '{{ addslashes($s->supplier_name) }}')">
                                            <i class="ti ti-trash icon red"></i>
                                            Eliminar
                                        </button>
                                    @else
                                        <button type="button"
                                                class="dropdown-item btn-open-modal"
                                                data-target="modal-{{ $s->id_supplier }}">
                                            <i class="ti ti-eye icon blue"></i>
                                            Ver detalles
                                        </button>
                                        <div class="dropdown-divider"></div>
                                        <button type="button"
                                                class="dropdown-item"
                                                onclick="restoreSupplier({{ $s->id_supplier }})">
                                            <i class="ti ti-rotate-clockwise icon green"></i>
                                            Restaurar
                                        </button>
                                        <button type="button"
                                                class="dropdown-item danger"
                                                onclick="confirmForceDelete({{ $s->id_supplier }}, '{{ addslashes($s->supplier_name) }}')">
                                            <i class="ti ti-trash-off icon red"></i>
                                            Eliminar permanentemente
                                        </button>
                                    @endif
                                </div>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div style="display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:.5rem;padding:1rem 1.5rem;border-top:1px solid #e2e8f0;background:#fafbfc">
            <span style="font-size:13px;color:#64748b">
                Mostrando {{ $suppliers->firstItem() }}–{{ $suppliers->lastItem() }} de {{ $suppliers->total() }} proveedor(es)
                @if(($viewMode ?? 'all') === 'independent')
                    <span style="background:#fef3c7;color:#92400e;padding:2px 10px;border-radius:4px;font-size:11px;font-weight:600;margin-left:6px">INDEPENDIENTES</span>
                @endif
            </span>
            <div style="display:flex;align-items:center;gap:.3rem">
                @if($suppliers->onFirstPage())
                    <span style="padding:.35rem .7rem;border:1px solid #e2e8f0;border-radius:7px;font-size:12px;color:#cbd5e1;cursor:default;background:#fff">
                        <i class="ti ti-chevron-left"></i>
                    </span>
                @else
                    <a href="{{ $suppliers->previousPageUrl() }}"
                       style="padding:.35rem .7rem;border:1px solid #e2e8f0;border-radius:7px;font-size:12px;color:#374151;text-decoration:none;background:#fff;transition:all .15s"
                       onmouseover="this.style.borderColor='#6366f1';this.style.color='#6366f1'"
                       onmouseout="this.style.borderColor='#e2e8f0';this.style.color='#374151'">
                        <i class="ti ti-chevron-left"></i>
                    </a>
                @endif

                @php
                    $current = $suppliers->currentPage();
                    $last = $suppliers->lastPage();
                    $range = 2;
                @endphp

                @for($i = 1; $i <= $last; $i++)
                    @if($i == 1 || $i == $last || abs($i - $current) <= $range)
                        @if($i == $current)
                            <span style="padding:.35rem .65rem;border:1px solid #6366f1;border-radius:7px;font-size:12px;font-weight:700;color:#fff;background:#6366f1;min-width:32px;text-align:center">
                                {{ $i }}
                            </span>
                        @else
                            <a href="{{ $suppliers->url($i) }}"
                               style="padding:.35rem .65rem;border:1px solid #e2e8f0;border-radius:7px;font-size:12px;color:#374151;text-decoration:none;background:#fff;min-width:32px;text-align:center;transition:all .15s"
                               onmouseover="this.style.borderColor='#6366f1';this.style.color='#6366f1'"
                               onmouseout="this.style.borderColor='#e2e8f0';this.style.color='#374151'">
                                {{ $i }}
                            </a>
                        @endif
                    @elseif(abs($i - $current) == $range + 1)
                        <span style="font-size:12px;color:#94a3b8;padding:0 .1rem">…</span>
                    @endif
                @endfor

                @if($suppliers->hasMorePages())
                    <a href="{{ $suppliers->nextPageUrl() }}"
                       style="padding:.35rem .7rem;border:1px solid #e2e8f0;border-radius:7px;font-size:12px;color:#374151;text-decoration:none;background:#fff;transition:all .15s"
                       onmouseover="this.style.borderColor='#6366f1';this.style.color='#6366f1'"
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
    </div>

    {{-- MODALES DE VER --}}
    @foreach($suppliers as $s)
    <div id="modal-{{ $s->id_supplier }}" class="custom-modal-overlay" style="display:none; position:fixed; inset:0; background:rgba(15,23,42,0.5); backdrop-filter:blur(4px); z-index:9999; justify-content:center; align-items:center; padding:1rem;">
        <div class="custom-modal-content" style="background:#fff;width:100%;max-width:800px;border-radius:16px;box-shadow:0 25px 50px -12px rgba(0,0,0,0.25);display:flex;flex-direction:column;max-height:90vh;animation:modalFadeIn .2s ease-out;">

            <div style="background:#f8fafc;border-bottom:1px solid #e2e8f0;border-top-left-radius:16px;border-top-right-radius:16px;padding:1.2rem 1.5rem;display:flex;justify-content:space-between;align-items:center">
                <div style="display:flex;align-items:center;gap:12px">
                    <div style="background:linear-gradient(135deg,#e0f2fe,#bae6fd);color:#0369a1;width:40px;height:40px;border-radius:10px;display:flex;align-items:center;justify-content:center;font-size:20px">
                        <i class="ti ti-truck"></i>
                    </div>
                    <div>
                        <h5 style="font-weight:700;color:#0f172a;margin:0;font-size:16px">
                            {{ $s->supplier_name }}
                            @if($s->trashed())
                                <span style="background:#dc2626;color:#fff;font-size:9px;font-weight:700;padding:2px 8px;border-radius:10px;margin-left:6px;">
                                    <i class="ti ti-trash" style="font-size:10px;"></i> ELIMINADO
                                </span>
                            @endif
                        </h5>
                        <span style="font-size:12px;color:#64748b">ID: #{{ $s->id_supplier }}</span>
                        @if($s->chains->count() > 0)
                            <span style="background:#ede9fe;color:#6d28d9;font-size:10px;padding:2px 8px;border-radius:4px;font-weight:600;margin-left:8px">CADENA</span>
                        @else
                            <span style="background:#f1f5f9;color:#64748b;font-size:10px;padding:2px 8px;border-radius:4px;font-weight:600;margin-left:8px">INDEPENDIENTE</span>
                        @endif
                    </div>
                </div>
                <button type="button" class="btn-close-modal" data-close="modal-{{ $s->id_supplier }}" style="background:none;border:none;color:#64748b;cursor:pointer;font-size:22px;line-height:1;padding:4px;transition:color .15s" onmouseover="this.style.color='#0f172a'" onmouseout="this.style.color='#64748b'">
                    <i class="ti ti-x"></i>
                </button>
            </div>

            <div style="padding:1.5rem;overflow-y:auto;flex:1;">
                <div class="custom-tabs" data-supplier="{{ $s->id_supplier }}" style="display:flex;gap:8px;border-bottom:1px solid #f1f5f9;padding-bottom:12px;margin-bottom:1.5rem">
                    <button class="tab-trigger active" data-tab="info" style="border:none;cursor:pointer;font-size:13px;font-weight:600;padding:8px 16px;border-radius:8px;background:#0f172a;color:#fff;display:flex;align-items:center;gap:6px;transition:all .15s">
                        <i class="ti ti-info-circle"></i> General
                    </button>
                    <button class="tab-trigger" data-tab="contacts" style="border:none;cursor:pointer;font-size:13px;font-weight:600;padding:8px 16px;border-radius:8px;background:#f1f5f9;color:#475569;display:flex;align-items:center;gap:6px;transition:all .15s">
                        <i class="ti ti-users"></i> Contactos ({{ $s->contacts->count() }})
                    </button>
                    <button class="tab-trigger" data-tab="banks" style="border:none;cursor:pointer;font-size:13px;font-weight:600;padding:8px 16px;border-radius:8px;background:#f1f5f9;color:#475569;display:flex;align-items:center;gap:6px;transition:all .15s">
                        <i class="ti ti-credit-card"></i> Bancos ({{ $s->bankAccounts->count() }})
                    </button>
                </div>

                <div class="custom-tab-contents" data-supplier="{{ $s->id_supplier }}">
                    <div class="tab-content-panel panel-info" style="display:block">
                        <div style="display:grid;grid-template-columns:repeat(2,1fr);gap:1.2rem">
                            <div>
                                <label style="display:block;font-size:11px;font-weight:700;color:#94a3b8;text-transform:uppercase;letter-spacing:.5px;margin-bottom:4px">Razón Social</label>
                                <div style="font-size:14px;color:#1e293b;font-weight:500">{{ $s->business_name ?? '—' }}</div>
                            </div>
                            <div>
                                <label style="display:block;font-size:11px;font-weight:700;color:#94a3b8;text-transform:uppercase;letter-spacing:.5px;margin-bottom:4px">RUC</label>
                                <div style="font-size:14px;color:#1e293b;font-weight:500;font-family:monospace">{{ $s->tax_code ?? '—' }}</div>
                            </div>
                            <div>
                                <label style="display:block;font-size:11px;font-weight:700;color:#94a3b8;text-transform:uppercase;letter-spacing:.5px;margin-bottom:4px">Teléfono</label>
                                <div style="font-size:14px;color:#1e293b;font-weight:500">{{ $s->general_phone ?? '—' }}</div>
                            </div>
                            <div>
                                <label style="display:block;font-size:11px;font-weight:700;color:#94a3b8;text-transform:uppercase;letter-spacing:.5px;margin-bottom:4px">Email</label>
                                <div style="font-size:14px;color:#1e293b;font-weight:500">{{ $s->general_email ?? '—' }}</div>
                            </div>
                            <div>
                                <label style="display:block;font-size:11px;font-weight:700;color:#94a3b8;text-transform:uppercase;letter-spacing:.5px;margin-bottom:4px">País</label>
                                <div style="font-size:14px;color:#1e293b;font-weight:500">{{ $s->city->country->name ?? '—' }}</div>
                            </div>
                            <div>
                                <label style="display:block;font-size:11px;font-weight:700;color:#94a3b8;text-transform:uppercase;letter-spacing:.5px;margin-bottom:4px">Ciudad</label>
                                <div style="font-size:14px;color:#1e293b;font-weight:500">{{ $s->city->name ?? '—' }}</div>
                            </div>
                            <div>
                                <label style="display:block;font-size:11px;font-weight:700;color:#94a3b8;text-transform:uppercase;letter-spacing:.5px;margin-bottom:4px">Dirección</label>
                                <div style="font-size:14px;color:#1e293b;font-weight:500">{{ $s->address ?? '—' }}</div>
                            </div>
                            <div>
                                <label style="display:block;font-size:11px;font-weight:700;color:#94a3b8;text-transform:uppercase;letter-spacing:.5px;margin-bottom:4px">Categoría</label>
                                <div style="font-size:14px;color:#1e293b;font-weight:500">{{ $s->category ? $s->category->category_name : '—' }}</div>
                            </div>
                            <div>
                                <label style="display:block;font-size:11px;font-weight:700;color:#94a3b8;text-transform:uppercase;letter-spacing:.5px;margin-bottom:4px">Cadenas</label>
                                <div style="font-size:14px;color:#1e293b;font-weight:500">
                                    @if($s->chains->count() > 0)
                                        {{ $s->chains->pluck('name')->implode(', ') }}
                                    @else
                                        Independiente
                                    @endif
                                </div>
                            </div>
                            @if($s->trashed())
                                <div style="grid-column: span 2; background:#fef2f2; padding:0.8rem; border-radius:8px; border:1px solid #fecaca;">
                                    <label style="display:block;font-size:11px;font-weight:700;color:#dc2626;text-transform:uppercase;margin-bottom:4px">
                                        <i class="ti ti-trash" style="font-size:12px;"></i> Información de eliminación
                                    </label>
                                    <div style="font-size:13px;color:#991b1b;">
                                        Eliminado el: {{ $s->deleted_at->format('d/m/Y H:i:s') }}
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>

                    <div class="tab-content-panel panel-contacts" style="display:none">
                        @if($s->contacts->isEmpty())
                            <div style="text-align:center;padding:2rem;color:#94a3b8">
                                <i class="ti ti-users-minus" style="font-size:32px;display:block;margin-bottom:8px"></i>
                                <span style="font-size:13px">No hay contactos registrados</span>
                            </div>
                        @else
                            <div style="display:flex;flex-direction:column;gap:12px">
                                @foreach($s->contacts as $contact)
                                    <div style="background:#f8fafc;border:1px solid #e2e8f0;border-radius:10px;padding:1rem;display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:8px">
                                        <div style="display:flex;align-items:center;gap:12px">
                                            <div style="background:linear-gradient(135deg,#e0f2fe,#bae6fd);color:#0369a1;width:36px;height:36px;border-radius:50%;display:flex;align-items:center;justify-content:center;font-weight:700;font-size:13px">
                                                {{ strtoupper(substr($contact->name, 0, 1)) }}
                                            </div>
                                            <div>
                                                <div style="font-size:14px;font-weight:600;color:#0f172a">
                                                    {{ $contact->name }} {{ $contact->last_names }}
                                                    @if($contact->es_principal)
                                                        <span style="background:#dcfce7;color:#15803d;font-size:10px;font-weight:700;padding:2px 8px;border-radius:4px;margin-left:6px;text-transform:uppercase">Principal</span>
                                                    @endif
                                                </div>
                                                <div style="font-size:12px;color:#64748b">{{ $contact->qualification ?? 'Sin cargo' }}</div>
                                            </div>
                                        </div>
                                        <div style="text-align:right;font-size:12px;color:#334155">
                                            <div><i class="ti ti-mail" style="color:#94a3b8;margin-right:4px"></i> {{ $contact->email ?? '—' }}</div>
                                            <div><i class="ti ti-phone" style="color:#94a3b8;margin-right:4px"></i> {{ $contact->first_phone }}</div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>

                    <div class="tab-content-panel panel-banks" style="display:none">
                        @if($s->bankAccounts->isEmpty())
                            <div style="text-align:center;padding:2rem;color:#94a3b8">
                                <i class="ti ti-credit-card-off" style="font-size:32px;display:block;margin-bottom:8px"></i>
                                <span style="font-size:13px">No hay cuentas bancarias registradas</span>
                            </div>
                        @else
                            <div style="border:1px solid #e2e8f0;border-radius:10px;overflow:hidden">
                                <table style="width:100%;font-size:13px;border-collapse:collapse">
                                    <thead style="background:#f8fafc">
                                        <tr>
                                            <th style="padding:10px 14px;font-weight:600;color:#64748b;text-align:left;font-size:11px;text-transform:uppercase">Banco</th>
                                            <th style="padding:10px 14px;font-weight:600;color:#64748b;text-align:left;font-size:11px;text-transform:uppercase">Cuenta</th>
                                            <th style="padding:10px 14px;font-weight:600;color:#64748b;text-align:left;font-size:11px;text-transform:uppercase">CCI</th>
                                            <th style="padding:10px 14px;font-weight:600;color:#64748b;text-align:left;font-size:11px;text-transform:uppercase">Moneda</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($s->bankAccounts as $account)
                                            <tr style="border-top:1px solid #f1f5f9">
                                                <td style="padding:12px 14px;font-weight:600;color:#1e293b">{{ $account->bank ? $account->bank->bank_name : '—' }}</td>
                                                <td style="padding:12px 14px;color:#475569;font-family:monospace">{{ $account->account_number }}</td>
                                                <td style="padding:12px 14px;color:#475569;font-family:monospace">{{ $account->cci ?? '—' }}</td>
                                                <td style="padding:12px 14px">
                                                    <span style="background:#f1f5f9;color:#1e293b;font-weight:600;padding:3px 10px;border-radius:4px;font-size:12px">{{ $account->currency ?? '—' }}</span>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <div style="background:#f8fafc;border-top:1px solid #e2e8f0;border-bottom-left-radius:16px;border-bottom-right-radius:16px;padding:.8rem 1.5rem;display:flex;justify-content:flex-end;gap:8px;">
                @if($s->trashed())
                    <button type="button" onclick="restoreSupplier({{ $s->id_supplier }})"
                            style="font-size:13px;background:#16a34a;color:#fff;border:none;padding:8px 16px;border-radius:7px;cursor:pointer;font-weight:500;transition:background .15s"
                            onmouseover="this.style.background='#15803d'" onmouseout="this.style.background='#16a34a'">
                        <i class="ti ti-rotate-clockwise"></i> Restaurar
                    </button>
                    <button type="button" onclick="confirmForceDelete({{ $s->id_supplier }}, '{{ addslashes($s->supplier_name) }}')"
                            style="font-size:13px;background:#dc2626;color:#fff;border:none;padding:8px 16px;border-radius:7px;cursor:pointer;font-weight:500;transition:background .15s"
                            onmouseover="this.style.background='#b91c1c'" onmouseout="this.style.background='#dc2626'">
                        <i class="ti ti-trash-off"></i> Eliminar definitivo
                    </button>
                @else
                    <button type="button" onclick="window.location.href='{{ route('admin.suppliers.edit', $s->id_supplier) }}'"
                            style="font-size:13px;background:#8b5cf6;color:#fff;border:none;padding:8px 16px;border-radius:7px;cursor:pointer;font-weight:500;transition:background .15s"
                            onmouseover="this.style.background='#7c3aed'" onmouseout="this.style.background='#8b5cf6'">
                        <i class="ti ti-edit"></i> Editar
                    </button>
                @endif
                <button type="button" class="btn-close-modal" data-close="modal-{{ $s->id_supplier }}" style="font-size:13px;background:#64748b;color:#fff;border:none;padding:8px 20px;border-radius:7px;cursor:pointer;font-weight:500;transition:background .15s" onmouseover="this.style.background='#475569'" onmouseout="this.style.background='#64748b'">
                    Cerrar
                </button>
            </div>
        </div>
    </div>
    @endforeach
@endif

{{-- MODAL EXPORTAR --}}
<div id="modal-export-suppliers">
    <div class="modal-box">
        <button class="modal-close" onclick="closeExportSuppliersModal()">
            <i class="ti ti-x"></i>
        </button>

        <div style="display:flex;align-items:center;gap:12px;margin-bottom:1.5rem">
            <div style="width:44px;height:44px;background:#f0fdf4;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:22px">
                <i class="ti ti-file-export" id="export-modal-icon" style="color:#16a34a"></i>
            </div>
            <div>
                <h2 style="font-size:17px;font-weight:700;color:#0f172a;margin:0">
                    Exportar <span id="export-type-label" style="color:#16a34a">Excel</span>
                </h2>
                <p style="font-size:12px;color:#94a3b8;margin:0">Selecciona qué datos quieres exportar</p>
            </div>
        </div>

        <div class="export-option" onclick="exportAllExcel()">
            <div class="icon green">
                <i class="ti ti-file-type-xls"></i>
            </div>
            <div style="flex:1">
                <div class="title">Todos los proveedores (Excel)</div>
                <div class="sub">Descarga el listado completo en Excel</div>
            </div>
            <i class="ti ti-chevron-right arrow"></i>
        </div>

        <div class="export-option" onclick="exportAllPDF()" style="border-color:#fee2e2;">
            <div class="icon" style="background:#fee2e2; color:#dc2626;">
                <i class="ti ti-file-type-pdf"></i>
            </div>
            <div style="flex:1">
                <div class="title">Todos los proveedores (PDF)</div>
                <div class="sub">Descarga el listado completo en PDF</div>
            </div>
            <i class="ti ti-chevron-right arrow"></i>
        </div>

        <div class="export-by-id-wrapper">
            <div class="header">
                <div class="icon blue">
                    <i class="ti ti-truck"></i>
                </div>
                <div style="flex:1">
                    <div class="title">Proveedor específico</div>
                    <div class="sub">Busca y elige un proveedor por nombre</div>
                </div>
            </div>
            <div style="position:relative;margin-top:.8rem;padding-left:52px;display:flex;gap:.6rem;align-items:center">
                <div style="flex:1;position:relative">
                    <input type="text"
                           id="export-supplier-search"
                           placeholder="Escribe para buscar proveedor..."
                           autocomplete="off"
                           style="width:100%;padding:.5rem .7rem;border:1px solid #e2e8f0;border-radius:7px;font-size:13px;outline:none;transition:border-color .15s;box-sizing:border-box">
                    <button type="button" id="export-supplier-clear"
                            style="display:none;position:absolute;right:.5rem;top:50%;transform:translateY(-50%);background:none;border:none;color:#cbd5e1;cursor:pointer;font-size:14px;padding:2px;line-height:1">
                        <i class="ti ti-x"></i>
                    </button>
                    <div id="export-supplier-list"
                         style="display:none;position:absolute;top:calc(100% + 4px);left:0;right:0;background:#fff;border:1px solid #e2e8f0;border-radius:9px;max-height:200px;overflow-y:auto;z-index:60;box-shadow:0 10px 25px -5px rgba(0,0,0,.1)">
                    </div>
                </div>
                <button id="export-supplier-btn"
                        onclick="exportSelectedSupplier()"
                        disabled
                        style="padding:.5rem 1rem;background:#6366f1;color:#fff;border:none;border-radius:7px;font-size:13px;font-weight:600;cursor:pointer;transition:all .15s;white-space:nowrap;opacity:.45">
                    <i class="ti ti-arrow-right" style="font-size:14px"></i> Exportar
                </button>
            </div>
        </div>

        <button class="btn-cancel-export" onclick="closeExportSuppliersModal()">
            Cancelar
        </button>
    </div>
</div>

<style>
/* ══════════════════════════════════════════════
   DROPDOWN ACTIONS - FIX PARA QUE NO SE CORTE
   ══════════════════════════════════════════════ */
.dropdown-actions {
    position: relative;
    display: inline-flex;
    justify-content: center;
    align-items: center;
}

.dropdown-toggle {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 32px;
    height: 32px;
    border-radius: 8px;
    border: 1px solid #e2e8f0;
    background: #fff;
    color: #64748b;
    cursor: pointer;
    transition: all 0.15s ease;
    font-size: 16px;
    margin: 0 auto;
}

.dropdown-toggle:hover {
    border-color: #6366f1;
    color: #6366f1;
    background: #f5f3ff;
}

.dropdown-toggle:active {
    transform: scale(0.95);
}

.dropdown-menu {
    display: none;
    position: fixed;
    min-width: 200px;
    background: #fff;
    border: 1px solid #e2e8f0;
    border-radius: 12px;
    box-shadow: 0 12px 30px -8px rgba(0, 0, 0, 0.18);
    z-index: 99999 !important;
    padding: 6px;
    animation: dropdownFadeIn 0.18s ease-out;
}

.dropdown-menu.show {
    display: block;
}

.dropdown-menu .dropdown-item {
    display: flex;
    align-items: center;
    gap: 12px;
    width: 100%;
    padding: 8px 14px;
    border: none;
    border-radius: 8px;
    background: none;
    color: #334155;
    font-size: 13px;
    font-weight: 500;
    cursor: pointer;
    transition: all 0.12s ease;
    text-decoration: none;
    font-family: inherit;
    line-height: 1.4;
    text-align: left;
    box-sizing: border-box;
}

.dropdown-menu .dropdown-item:hover {
    background: #f1f5f9;
    color: #0f172a;
}

.dropdown-menu .dropdown-item .icon {
    width: 20px;
    text-align: center;
    font-size: 16px;
    flex-shrink: 0;
}

.dropdown-menu .dropdown-item .icon.blue { color: #3b82f6; }
.dropdown-menu .dropdown-item .icon.red { color: #dc2626; }
.dropdown-menu .dropdown-item .icon.purple { color: #8b5cf6; }
.dropdown-menu .dropdown-item .icon.green { color: #16a34a; }
.dropdown-menu .dropdown-item .icon.orange { color: #f59e0b; }

.dropdown-menu .dropdown-item.danger:hover {
    background: #fef2f2;
    color: #dc2626;
}

.dropdown-menu .dropdown-item.danger:hover .icon {
    color: #dc2626;
}

.dropdown-menu .dropdown-divider {
    height: 1px;
    background: #e2e8f0;
    margin: 4px 10px;
}

@keyframes dropdownFadeIn {
    from {
        opacity: 0;
        transform: scale(0.92) translateY(-8px);
    }
    to {
        opacity: 1;
        transform: scale(1) translateY(0);
    }
}

.table-wrap {
    transition: all 0.2s ease;
}

.table-wrap:hover {
    border-color: #cbd5e1;
}

#modal-export-suppliers {
    position: fixed;
    inset: 0;
    background: rgba(0,0,0,.45);
    backdrop-filter: blur(4px);
    z-index: 1000;
    display: none;
    align-items: center;
    justify-content: center;
    padding: 1.2rem;
}
#modal-export-suppliers.show { display: flex; }

#modal-export-suppliers .modal-box {
    background: #fff;
    border-radius: 16px;
    width: 100%;
    max-width: 480px;
    max-height: 90vh;
    overflow-y: auto;
    padding: 2rem;
    position: relative;
    animation: modalFadeIn .2s ease-out;
}

#modal-export-suppliers .modal-close {
    position: absolute;
    top: 1rem;
    right: 1rem;
    background: none;
    border: none;
    font-size: 22px;
    cursor: pointer;
    color: #94a3b8;
    transition: color .15s;
}
#modal-export-suppliers .modal-close:hover { color: #0f172a; }

.export-by-id-wrapper {
    border: 2px solid #e2e8f0;
    border-radius: 12px;
    padding: 1rem 1.2rem;
    transition: all .2s;
    margin-bottom: .8rem;
}
.export-by-id-wrapper:hover {
    border-color: #6366f1;
}
.export-by-id-wrapper .header {
    display: flex;
    align-items: center;
    gap: 12px;
}
.export-by-id-wrapper .icon {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 18px;
    flex-shrink: 0;
}
.export-by-id-wrapper .icon.blue { background: #eff6ff; color: #3b82f6; }
.export-by-id-wrapper .title { font-size: 14px; font-weight: 600; color: #0f172a; }
.export-by-id-wrapper .sub { font-size: 12px; color: #94a3b8; }

.export-option {
    border: 2px solid #e2e8f0;
    border-radius: 12px;
    padding: 1rem 1.2rem;
    cursor: pointer;
    transition: all .2s;
    margin-bottom: .8rem;
    display: flex;
    align-items: center;
    gap: 12px;
}
.export-option:hover {
    border-color: #16a34a;
    background: #f0fdf4;
}
.export-option .icon {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 18px;
    flex-shrink: 0;
}
.export-option .icon.green { background: #f0fdf4; color: #16a34a; }
.export-option .title { font-size: 14px; font-weight: 600; color: #0f172a; }
.export-option .sub { font-size: 12px; color: #94a3b8; }
.export-option .arrow { color: #94a3b8; font-size: 18px; margin-left: auto; }

.btn-cancel-export {
    width: 100%;
    padding: .6rem;
    background: #f1f5f9;
    border: none;
    border-radius: 8px;
    font-size: 13px;
    font-weight: 600;
    color: #64748b;
    cursor: pointer;
    transition: background .15s;
    margin-top: .4rem;
}
.btn-cancel-export:hover { background: #e2e8f0; }

#export-supplier-search:focus {
    border-color: #6366f1 !important;
    box-shadow: 0 0 0 3px rgba(99,102,241,0.1);
}
#export-supplier-list div:hover {
    background: #eef2ff !important;
    color: #4338ca !important;
}

@keyframes spin {
    to { transform: rotate(360deg); }
}
@keyframes modalFadeIn {
    from { opacity: 0; transform: scale(0.95) translateY(10px); }
    to { opacity: 1; transform: scale(1) translateY(0); }
}

.cadena_name {
    display: block;
}

@media (width <= 640px) {
    .cadena_name{
        display: none;
    }
}

.view-mode-btn.active {
    border-color: #6366f1 !important;
    background: #6366f1 !important;
    color: #fff !important;
}

/* Asegurar que el dropdown no se corte */
.table-wrap td {
    overflow: visible !important;
    position: relative;
}

.filter-status-btn {
    transition: all 0.15s ease;
}
.filter-status-btn:hover:not(.active) {
    background: #e2e8f0 !important;
    transform: translateY(-1px);
}
.filter-status-btn.active {
    cursor: default;
}
</style>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
const PDF_SINGLE_URL = '{{ route("admin.suppliers.pdf", ["supplier" => "__ID__"]) }}';
const PDF_ALL_URL = '{{ route("admin.suppliers.export.pdf.all") }}';
const EXCEL_ALL_URL = '{{ route("admin.suppliers.export.excel") }}';
const EXCEL_SINGLE_URL = '{{ route("admin.suppliers.export.excel") }}';
const DELETE_URL = '{{ route("admin.suppliers.destroy", ["supplier" => "__ID__"]) }}';
const RESTORE_URL = '{{ route("admin.suppliers.restore", ["supplier" => "__ID__"]) }}';
const FORCE_DELETE_URL = '{{ route("admin.suppliers.force-destroy", ["supplier" => "__ID__"]) }}';
const BULK_DELETE_URL = '{{ route("admin.suppliers.bulk.destroy") }}';
const BULK_RESTORE_URL = '{{ route("admin.suppliers.bulk.restore") }}';
const BULK_FORCE_DELETE_URL = '{{ route("admin.suppliers.bulk.force.destroy") }}';
const CSRF_TOKEN = '{{ csrf_token() }}';

// ============================================================
// RESTAURAR PROVEEDOR
// ============================================================
function restoreSupplier(id) {
    Swal.fire({
        title: '¿Restaurar proveedor?',
        text: 'El proveedor será reactivado en el sistema.',
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#16a34a',
        cancelButtonColor: '#64748b',
        confirmButtonText: 'Sí, restaurar',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = RESTORE_URL.replace('__ID__', id);

            const csrf = document.createElement('input');
            csrf.type = 'hidden';
            csrf.name = '_token';
            csrf.value = CSRF_TOKEN;

            form.appendChild(csrf);
            document.body.appendChild(form);
            form.submit();
        }
    });
}

// ============================================================
// ELIMINAR PERMANENTEMENTE PROVEEDOR
// ============================================================
function confirmForceDelete(id, name) {
    Swal.fire({
        title: '¿Eliminar permanentemente?',
        html: `Estás a punto de eliminar <strong>${name}</strong> de forma permanente.<br>
               <span style="color: #dc2626; font-weight: 600;">Esta acción NO se puede deshacer.</span>`,
        icon: 'error',
        showCancelButton: true,
        confirmButtonColor: '#dc2626',
        cancelButtonColor: '#64748b',
        confirmButtonText: 'Sí, eliminar permanentemente',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = FORCE_DELETE_URL.replace('__ID__', id);

            const csrf = document.createElement('input');
            csrf.type = 'hidden';
            csrf.name = '_token';
            csrf.value = CSRF_TOKEN;

            const method = document.createElement('input');
            method.type = 'hidden';
            method.name = '_method';
            method.value = 'DELETE';

            form.appendChild(csrf);
            form.appendChild(method);
            document.body.appendChild(form);
            form.submit();
        }
    });
}

// ============================================================
// ELIMINAR PROVEEDOR (SOFT DELETE)
// ============================================================
function confirmDelete(id, name) {
    Swal.fire({
        title: '¿Eliminar proveedor?',
        html: `Estás a punto de eliminar <strong>${name}</strong>.<br>Esta acción no se puede deshacer.`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#ef4444',
        cancelButtonColor: '#64748b',
        confirmButtonText: 'Sí, eliminar',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = DELETE_URL.replace('__ID__', id);

            const csrf = document.createElement('input');
            csrf.type = 'hidden';
            csrf.name = '_token';
            csrf.value = CSRF_TOKEN;

            const method = document.createElement('input');
            method.type = 'hidden';
            method.name = '_method';
            method.value = 'DELETE';

            form.appendChild(csrf);
            form.appendChild(method);
            document.body.appendChild(form);
            form.submit();
        }
    });
}

function toggleSelectAll(source) {
    document.querySelectorAll('.row-checkbox').forEach(cb => cb.checked = source.checked);
    updateBulkBar();
}

function getSelectedIds() {
    return Array.from(document.querySelectorAll('.row-checkbox:checked')).map(cb => cb.value);
}

function getSelectedTrashedIds() {
    return Array.from(document.querySelectorAll('.row-checkbox:checked[data-trashed="true"]')).map(cb => cb.value);
}

function updateBulkBar() {
    const ids = getSelectedIds();
    const trashedIds = getSelectedTrashedIds();
    const bar = document.getElementById('bulk-actions-bar');
    const label = document.getElementById('bulk-count-label');
    const allCheckboxes = document.querySelectorAll('.row-checkbox');
    const selectAll = document.getElementById('select-all');

    if (ids.length > 0) {
        bar.style.display = 'flex';
        label.textContent = ids.length + (ids.length === 1 ? ' seleccionado' : ' seleccionados');
    } else {
        bar.style.display = 'none';
    }

    selectAll.checked = allCheckboxes.length > 0 && ids.length === allCheckboxes.length;
    selectAll.indeterminate = ids.length > 0 && ids.length < allCheckboxes.length;
}

function clearSelection() {
    document.querySelectorAll('.row-checkbox').forEach(cb => cb.checked = false);
    document.getElementById('select-all').checked = false;
    document.getElementById('select-all').indeterminate = false;
    updateBulkBar();
}

function bulkDeleteSelected() {
    const checked = document.querySelectorAll('.row-checkbox:checked:not([data-trashed="true"])');
    if (checked.length === 0) {
        Swal.fire('Aviso', 'No hay proveedores activos seleccionados para eliminar.', 'info');
        return;
    }

    Swal.fire({
        title: `¿Eliminar ${checked.length} proveedor(es)?`,
        html: `Estás a punto de eliminar <strong>${checked.length}</strong> proveedor(es) seleccionados.<br>Esta acción no se puede deshacer.`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#ef4444',
        cancelButtonColor: '#64748b',
        confirmButtonText: 'Sí, eliminar',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            const container = document.getElementById('bulk-ids-container');
            if (!container) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = BULK_DELETE_URL;
                form.style.display = 'none';

                const csrf = document.createElement('input');
                csrf.type = 'hidden';
                csrf.name = '_token';
                csrf.value = CSRF_TOKEN;
                form.appendChild(csrf);

                const method = document.createElement('input');
                method.type = 'hidden';
                method.name = '_method';
                method.value = 'DELETE';
                form.appendChild(method);

                checked.forEach(cb => {
                    const input = document.createElement('input');
                    input.type = 'hidden';
                    input.name = 'ids[]';
                    input.value = cb.value;
                    form.appendChild(input);
                });

                document.body.appendChild(form);
                form.submit();
                return;
            }

            container.innerHTML = '';
            checked.forEach(cb => {
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = 'ids[]';
                input.value = cb.value;
                container.appendChild(input);
            });
            document.getElementById('bulk-delete-form').submit();
        }
    });
}

function bulkRestoreSelected() {
    const checked = document.querySelectorAll('.row-checkbox:checked[data-trashed="true"]');
    if (checked.length === 0) {
        Swal.fire('Aviso', 'No hay proveedores eliminados seleccionados.', 'info');
        return;
    }

    Swal.fire({
        title: `¿Restaurar ${checked.length} proveedor(es)?`,
        html: `Se restaurarán <strong>${checked.length}</strong> proveedor(es) eliminados.`,
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#16a34a',
        cancelButtonColor: '#64748b',
        confirmButtonText: 'Sí, restaurar',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            const container = document.getElementById('bulk-restore-ids-container');
            if (!container) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = BULK_RESTORE_URL;
                form.style.display = 'none';

                const csrf = document.createElement('input');
                csrf.type = 'hidden';
                csrf.name = '_token';
                csrf.value = CSRF_TOKEN;
                form.appendChild(csrf);

                checked.forEach(cb => {
                    const input = document.createElement('input');
                    input.type = 'hidden';
                    input.name = 'ids[]';
                    input.value = cb.value;
                    form.appendChild(input);
                });

                document.body.appendChild(form);
                form.submit();
                return;
            }

            container.innerHTML = '';
            checked.forEach(cb => {
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = 'ids[]';
                input.value = cb.value;
                container.appendChild(input);
            });
            document.getElementById('bulk-restore-form').submit();
        }
    });
}

function bulkForceDeleteSelected() {
    const checked = document.querySelectorAll('.row-checkbox:checked[data-trashed="true"]');
    if (checked.length === 0) {
        Swal.fire('Aviso', 'No hay proveedores eliminados seleccionados.', 'info');
        return;
    }

    Swal.fire({
        title: `¿Eliminar permanentemente ${checked.length} proveedor(es)?`,
        html: `Se eliminarán permanentemente <strong>${checked.length}</strong> proveedor(es).<br>
               <span style="color: #dc2626; font-weight: 600;">Esta acción NO se puede deshacer.</span>`,
        icon: 'error',
        showCancelButton: true,
        confirmButtonColor: '#dc2626',
        cancelButtonColor: '#64748b',
        confirmButtonText: 'Sí, eliminar permanentemente',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            const container = document.getElementById('bulk-force-ids-container');
            if (!container) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = BULK_FORCE_DELETE_URL;
                form.style.display = 'none';

                const csrf = document.createElement('input');
                csrf.type = 'hidden';
                csrf.name = '_token';
                csrf.value = CSRF_TOKEN;
                form.appendChild(csrf);

                const method = document.createElement('input');
                method.type = 'hidden';
                method.name = '_method';
                method.value = 'DELETE';
                form.appendChild(method);

                checked.forEach(cb => {
                    const input = document.createElement('input');
                    input.type = 'hidden';
                    input.name = 'ids[]';
                    input.value = cb.value;
                    form.appendChild(input);
                });

                document.body.appendChild(form);
                form.submit();
                return;
            }

            container.innerHTML = '';
            checked.forEach(cb => {
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = 'ids[]';
                input.value = cb.value;
                container.appendChild(input);
            });
            document.getElementById('bulk-force-delete-form').submit();
        }
    });
}

// ============================================================
// DROPDOWN
// ============================================================
function toggleDropdown(id, button) {
    const dropdown = document.getElementById('dropdown-' + id);
    if (!dropdown) return;

    const isOpen = dropdown.classList.contains('show');

    document.querySelectorAll('.dropdown-menu').forEach(el => {
        el.classList.remove('show');
    });

    if (!isOpen) {
        const rect = button.getBoundingClientRect();

        dropdown.style.position = 'fixed';
        dropdown.style.left = 'auto';
        dropdown.style.right = (window.innerWidth - rect.right) + 'px';
        dropdown.style.top = (rect.bottom + 6) + 'px';
        dropdown.style.bottom = 'auto';

        const dropdownHeight = 260;
        if (rect.bottom + dropdownHeight + 20 > window.innerHeight) {
            dropdown.style.top = 'auto';
            dropdown.style.bottom = (window.innerHeight - rect.top + 6) + 'px';
        }

        if (rect.right < 200) {
            dropdown.style.right = 'auto';
            dropdown.style.left = (rect.left) + 'px';
        }

        dropdown.classList.add('show');
    }
}

function closeAllDropdowns() {
    document.querySelectorAll('.dropdown-menu').forEach(el => {
        el.classList.remove('show');
    });
}

document.addEventListener('click', function(e) {
    const target = e.target;
    const isDropdownAction = target.closest('.dropdown-actions');
    const isDropdownMenu = target.closest('.dropdown-menu');

    if (!isDropdownAction && !isDropdownMenu) {
        closeAllDropdowns();
    }
});

document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeAllDropdowns();
        document.querySelectorAll('.custom-modal-overlay').forEach(el => {
            if (el.style.display === 'flex') {
                el.style.display = 'none';
                document.body.style.overflow = '';
            }
        });
        closeExportSuppliersModal();
    }
});

let scrollTimeout;
document.addEventListener('scroll', function() {
    clearTimeout(scrollTimeout);
    scrollTimeout = setTimeout(() => {
        closeAllDropdowns();
    }, 100);
}, { passive: true });

let resizeTimeout;
window.addEventListener('resize', function() {
    clearTimeout(resizeTimeout);
    resizeTimeout = setTimeout(() => {
        closeAllDropdowns();
    }, 150);
});

document.querySelectorAll('.dropdown-menu').forEach(menu => {
    menu.addEventListener('click', function(e) {
        e.stopPropagation();
    });
});

document.querySelectorAll('.dropdown-toggle').forEach(btn => {
    btn.addEventListener('click', function(e) {
        e.stopPropagation();
    });
});

function setViewMode(mode) {
    document.querySelector('input[name="view_mode"]').value = mode;
    document.getElementById('filter-form').submit();
}

function clearFilters() {
    document.getElementById('f-search').value = '';
    document.getElementById('f-country').value = '';
    document.getElementById('f-category').value = '';
    document.getElementById('f-chain').value = '';
    document.getElementById('f-sort').value = 'newest';
    document.querySelector('input[name="view_mode"]').value = 'all';
    document.getElementById('filter-form').submit();
}

function getFilters() {
    const params = new URLSearchParams();
    const search = document.getElementById('f-search')?.value || '';
    const country = document.getElementById('f-country')?.value || '';
    const category = document.getElementById('f-category')?.value || '';
    const chain = document.getElementById('f-chain')?.value || '';
    const sort = document.getElementById('f-sort')?.value || 'newest';
    const viewMode = document.querySelector('input[name="view_mode"]')?.value || 'all';

    if (search) params.append('search', search);
    if (country) params.append('country', country);
    if (category) params.append('category', category);
    if (chain) params.append('chain', chain);
    if (sort) params.append('sort', sort);
    if (viewMode) params.append('view_mode', viewMode);

    return params;
}

function downloadPDF(id, name) {
    const modal = document.getElementById('modal-pdf-loading');
    const progressBar = document.getElementById('pdf-progress-bar');
    const progressText = document.getElementById('pdf-progress-text');

    modal.style.display = 'flex';
    progressBar.style.width = '0%';
    progressText.textContent = '0%';

    let progress = 0;
    const interval = setInterval(() => {
        progress += Math.random() * 15;
        if (progress > 90) progress = 90;
        progressBar.style.width = progress + '%';
        progressText.textContent = Math.round(progress) + '%';
    }, 200);

    const url = PDF_SINGLE_URL.replace('__ID__', id);

    fetch(url, {
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
    })
    .then(response => {
        if (!response.ok) throw new Error('Error al generar el PDF');
        return response.blob();
    })
    .then(blob => {
        clearInterval(interval);
        progressBar.style.width = '100%';
        progressText.textContent = '100%';

        const link = document.createElement('a');
        link.href = URL.createObjectURL(blob);
        link.download = `proveedor_${name.replace(/\s+/g, '_')}.pdf`;
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
        URL.revokeObjectURL(link.href);

        setTimeout(() => {
            modal.style.display = 'none';
            progressBar.style.width = '0%';
            progressText.textContent = '0%';
        }, 500);
    })
    .catch(error => {
        clearInterval(interval);
        console.error('Error:', error);
        Swal.fire({
            icon: 'error',
            title: 'Error al generar PDF',
            text: 'Hubo un problema al generar el PDF. Intenta nuevamente.',
            confirmButtonColor: '#ef4444'
        });
        modal.style.display = 'none';
        progressBar.style.width = '0%';
        progressText.textContent = '0%';
    });
}

function exportAllPDF() {
    const modal = document.getElementById('modal-pdf-loading');
    const progressBar = document.getElementById('pdf-progress-bar');
    const progressText = document.getElementById('pdf-progress-text');

    modal.style.display = 'flex';
    progressBar.style.width = '0%';
    progressText.textContent = '0%';

    let progress = 0;
    const interval = setInterval(() => {
        progress += Math.random() * 10;
        if (progress > 95) progress = 95;
        progressBar.style.width = progress + '%';
        progressText.textContent = Math.round(progress) + '%';
    }, 150);

    const params = getFilters();
    const url = PDF_ALL_URL + '?' + params.toString();

    fetch(url, {
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
    })
    .then(response => {
        if (!response.ok) throw new Error('Error al generar el PDF');
        return response.blob();
    })
    .then(blob => {
        clearInterval(interval);
        progressBar.style.width = '100%';
        progressText.textContent = '100%';

        const link = document.createElement('a');
        link.href = URL.createObjectURL(blob);
        link.download = `proveedores_${new Date().toISOString().slice(0,10)}.pdf`;
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
        URL.revokeObjectURL(link.href);

        setTimeout(() => {
            modal.style.display = 'none';
            progressBar.style.width = '0%';
            progressText.textContent = '0%';
            closeExportSuppliersModal();
        }, 500);
    })
    .catch(error => {
        clearInterval(interval);
        console.error('Error:', error);
        Swal.fire({
            icon: 'error',
            title: 'Error al generar PDF',
            text: 'Hubo un problema al generar el PDF. Intenta nuevamente.',
            confirmButtonColor: '#ef4444'
        });
        modal.style.display = 'none';
        progressBar.style.width = '0%';
        progressText.textContent = '0%';
        closeExportSuppliersModal();
    });
}

function exportAllExcel() {
    const params = getFilters();
    const url = EXCEL_ALL_URL + '?' + params.toString();
    window.location.href = url;
    closeExportSuppliersModal();
}

let exportSupplierId = null;

function openExportSuppliersModal() {
    document.getElementById('modal-export-suppliers').classList.add('show');

    const input = document.getElementById('export-supplier-search');
    const clear = document.getElementById('export-supplier-clear');
    const list = document.getElementById('export-supplier-list');
    const btn = document.getElementById('export-supplier-btn');
    input.value = '';
    input.style.borderColor = '#e2e8f0';
    clear.style.display = 'none';
    list.style.display = 'none';
    btn.disabled = true;
    btn.style.opacity = '.45';
    exportSupplierId = null;
}

function closeExportSuppliersModal() {
    document.getElementById('modal-export-suppliers').classList.remove('show');
}

function exportSelectedSupplier() {
    if (!exportSupplierId) {
        Swal.fire({
            icon: 'warning',
            title: '¡Atención!',
            text: 'Por favor, selecciona un proveedor primero.',
            confirmButtonColor: '#6366f1'
        });
        return;
    }

    const params = getFilters();
    params.append('supplier_id', exportSupplierId);
    const url = EXCEL_SINGLE_URL + '?' + params.toString();
    window.location.href = url;
    closeExportSuppliersModal();
}

// COMBO BUSCADOR PARA EXPORTAR
(function initExportCombo() {
    const input = document.getElementById('export-supplier-search');
    const list = document.getElementById('export-supplier-list');
    const clear = document.getElementById('export-supplier-clear');
    const btn = document.getElementById('export-supplier-btn');
    let activeIdx = -1;
    let filtered = [];

    const suppliersData = @json($suppliers->map(fn($s) => ['id' => $s->id_supplier, 'name' => $s->supplier_name, 'tax_code' => $s->tax_code]));

    function normalizeStr(str) {
        return (str || '').toLowerCase().normalize('NFD').replace(/[\u0300-\u036f]/g, '');
    }

    function renderList(term) {
        const q = normalizeStr(term);
        filtered = q
            ? suppliersData.filter(s => normalizeStr(s.name).includes(q))
            : suppliersData.slice(0, 50);

        if (filtered.length === 0) {
            list.innerHTML = '<div style="padding:.6rem .8rem;font-size:12.5px;color:#94a3b8">Sin resultados</div>';
        } else {
            list.innerHTML = filtered.map((s, i) =>
                `<div data-idx="${i}" style="padding:.55rem .8rem;font-size:13px;color:#0f172a;cursor:pointer;transition:background .1s">
                    <span style="color:#94a3b8;font-size:11px;margin-right:6px">#${s.id}</span>${s.name}
                </div>`
            ).join('');
        }
        activeIdx = -1;
        list.style.display = 'block';
    }

    function selectSupplier(s) {
        input.value = s.name + (s.tax_code ? ' · ' + s.tax_code : '');
        exportSupplierId = s.id;
        clear.style.display = 'block';
        btn.disabled = false;
        btn.style.opacity = '1';
        list.style.display = 'none';
        input.style.borderColor = '#6366f1';
    }

    function clearSelection2() {
        input.value = '';
        exportSupplierId = null;
        clear.style.display = 'none';
        btn.disabled = true;
        btn.style.opacity = '.45';
        list.style.display = 'none';
        input.style.borderColor = '#e2e8f0';
        input.focus();
    }

    function updateActive() {
        list.querySelectorAll('[data-idx]').forEach(el => {
            el.style.background = '';
            el.style.color = '#0f172a';
        });
        const el = list.querySelector(`[data-idx="${activeIdx}"]`);
        if (el) {
            el.style.background = '#eef2ff';
            el.style.color = '#4338ca';
            el.scrollIntoView({ block: 'nearest' });
        }
    }

    input.addEventListener('focus', () => {
        if (!input.value) renderList('');
    });

    input.addEventListener('input', () => {
        exportSupplierId = null;
        btn.disabled = true;
        btn.style.opacity = '.45';
        input.style.borderColor = '#e2e8f0';
        clear.style.display = input.value ? 'block' : 'none';
        renderList(input.value);
    });

    input.addEventListener('keydown', e => {
        if (list.style.display === 'none') return;
        if (e.key === 'ArrowDown') {
            e.preventDefault();
            activeIdx = Math.min(activeIdx + 1, filtered.length - 1);
            updateActive();
        } else if (e.key === 'ArrowUp') {
            e.preventDefault();
            activeIdx = Math.max(activeIdx - 1, 0);
            updateActive();
        } else if (e.key === 'Enter') {
            e.preventDefault();
            if (activeIdx >= 0 && filtered[activeIdx]) selectSupplier(filtered[activeIdx]);
        } else if (e.key === 'Escape') {
            list.style.display = 'none';
        }
    });

    list.addEventListener('mousedown', e => {
        e.preventDefault();
        const item = e.target.closest('[data-idx]');
        if (!item) return;
        selectSupplier(filtered[parseInt(item.dataset.idx)]);
    });

    list.addEventListener('mouseover', e => {
        const item = e.target.closest('[data-idx]');
        if (!item) return;
        list.querySelectorAll('[data-idx]').forEach(el => {
            el.style.background = '';
            el.style.color = '#0f172a';
        });
        item.style.background = '#eef2ff';
        item.style.color = '#4338ca';
        activeIdx = parseInt(item.dataset.idx);
    });

    clear.addEventListener('click', clearSelection2);

    document.addEventListener('click', e => {
        if (!input.contains(e.target) && !list.contains(e.target)) {
            list.style.display = 'none';
        }
    });
})();

document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('.btn-open-modal').forEach(btn => {
        btn.addEventListener('click', function() {
            const modalId = this.getAttribute('data-target');
            const modal = document.getElementById(modalId);
            if(modal) {
                modal.style.display = 'flex';
                document.body.style.overflow = 'hidden';
            }
        });
    });

    document.querySelectorAll('.btn-close-modal').forEach(btn => {
        btn.addEventListener('click', function() {
            const modalId = this.getAttribute('data-close');
            const modal = document.getElementById(modalId);
            if(modal) {
                modal.style.display = 'none';
                document.body.style.overflow = '';
            }
        });
    });

    document.querySelectorAll('.custom-modal-overlay').forEach(overlay => {
        overlay.addEventListener('click', function(e) {
            if(e.target === this) {
                this.style.display = 'none';
                document.body.style.overflow = '';
            }
        });
    });

    document.querySelectorAll('.custom-tabs .tab-trigger').forEach(tabBtn => {
        tabBtn.addEventListener('click', function() {
            const parentTabs = this.closest('.custom-tabs');
            const supplierId = parentTabs.getAttribute('data-supplier');
            const targetPanelName = this.getAttribute('data-tab');

            parentTabs.querySelectorAll('.tab-trigger').forEach(btn => {
                btn.style.background = '#f1f5f9';
                btn.style.color = '#475569';
            });

            this.style.background = '#0f172a';
            this.style.color = '#fff';

            const contentsContainer = document.querySelector(`.custom-tab-contents[data-supplier="${supplierId}"]`);
            contentsContainer.querySelectorAll('.tab-content-panel').forEach(panel => {
                panel.style.display = 'none';
            });

            const activePanel = contentsContainer.querySelector(`.panel-${targetPanelName}`);
            if(activePanel) {
                activePanel.style.display = 'block';
            }
        });
    });

    const searchInput = document.getElementById('f-search');
    if (searchInput) {
        searchInput.addEventListener('keydown', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                document.getElementById('filter-form').submit();
            }
        });
    }

    document.getElementById('btn-export-pdf-suppliers')?.addEventListener('click', function(e) {
        e.preventDefault();
        exportAllPDF();
    });

    document.getElementById('btn-export-excel-suppliers')?.addEventListener('click', function(e) {
        e.preventDefault();
        openExportSuppliersModal();
    });

    document.getElementById('modal-export-suppliers')?.addEventListener('click', function(e) {
        if (e.target === this) closeExportSuppliersModal();
    });

    updateBulkBar();
});
</script>

{{-- Formularios ocultos para acciones masivas --}}
<form id="bulk-delete-form" action="{{ route('admin.suppliers.bulk.destroy') }}" method="POST" style="display:none">
    @csrf
    @method('DELETE')
    <div id="bulk-ids-container"></div>
</form>

<form id="bulk-restore-form" action="{{ route('admin.suppliers.bulk.restore') }}" method="POST" style="display:none">
    @csrf
    <div id="bulk-restore-ids-container"></div>
</form>

<form id="bulk-force-delete-form" action="{{ route('admin.suppliers.bulk.force.destroy') }}" method="POST" style="display:none">
    @csrf
    @method('DELETE')
    <div id="bulk-force-ids-container"></div>
</form>

@endsection
