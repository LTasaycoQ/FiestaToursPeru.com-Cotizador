@extends('layouts.app')
@section('title', 'Proveedores de ' . $chain->name)
@section('content')

<div class="page-header" style="display:flex;justify-content:space-between;align-items:center;margin-bottom:1.6rem">
    <div>
        <div class="page-title">{{ $chain->name }}</div>
        <div class="page-sub">Proveedores pertenecientes a esta cadena</div>
    </div>
    <div style="display:flex;gap:.6rem;align-items:center;flex-wrap:wrap">
        <a href="{{ route('admin.suppliers.create') }}?chain={{ $chain->id_chain }}" class="btn btn-primary" style="display:inline-flex;align-items:center;gap:6px">
            <i class="ti ti-plus" style="font-size:15px"></i> Nuevo proveedor
        </a>
        <a href="{{ route('admin.chains.index') }}" class="btn btn-secondary" style="display:inline-flex;align-items:center;gap:6px">
            <i class="ti ti-arrow-left" style="font-size:15px"></i> Volver
        </a>
    </div>
</div>

{{-- Información de la cadena --}}
<div style="background:#fff;border-radius:12px;border:1px solid #e2e8f0;padding:1.2rem 1.5rem;margin-bottom:1.2rem;display:flex;flex-wrap:wrap;gap:1.5rem;align-items:center">
    <div style="display:flex;align-items:center;gap:12px">
        <div style="background:linear-gradient(135deg,#ede9fe,#c4b5fd);color:#5b21b6;width:48px;height:48px;border-radius:12px;display:flex;align-items:center;justify-content:center;font-size:22px">
            <i class="ti ti-link"></i>
        </div>
        <div>
            <h2 style="font-size:18px;font-weight:700;color:#0f172a;margin:0">{{ $chain->name }}</h2>
            @if($chain->description)
                <p style="font-size:13px;color:#64748b;margin:4px 0 0 0">{{ $chain->description }}</p>
            @endif
        </div>
    </div>
    <div style="display:flex;gap:1.5rem;margin-left:auto;flex-wrap:wrap">
        <div style="text-align:center">
            <div style="font-size:22px;font-weight:700;color:#6366f1">{{ $suppliers->total() }}</div>
            <div style="font-size:11px;color:#94a3b8;text-transform:uppercase;font-weight:600">Proveedores</div>
        </div>
        <div style="text-align:center">
            <div style="font-size:22px;font-weight:700;color:#0f172a">{{ $chain->created_at ? $chain->created_at->format('d/m/Y') : 'N/A' }}</div>
            <div style="font-size:11px;color:#94a3b8;text-transform:uppercase;font-weight:600">Fecha creación</div>
        </div>
    </div>
</div>

{{-- Barra de búsqueda y filtros --}}
<div style="margin-bottom:1.2rem; background:#fff; padding:.8rem 1rem; border-radius:12px; border:1px solid #e2e8f0;">
    <form id="filter-form" method="GET" action="{{ route('admin.chains.show', $chain->id_chain) }}" style="display:flex; flex-wrap:wrap; gap:8px; align-items:center;">
        <div style="position:relative;flex:1;min-width:200px">
            <i class="ti ti-search" style="position:absolute;left:.7rem;top:50%;transform:translateY(-50%);color:#94a3b8;font-size:15px"></i>
            <input type="text" id="f-search" name="search"
                   placeholder="Buscar proveedor por nombre, RUC, email..."
                   value="{{ $search ?? '' }}"
                   style="width:100%;padding:.5rem .7rem .5rem 2.2rem;border:1px solid #e2e8f0;border-radius:8px;font-size:13px;color:#0f172a;outline:none;transition:border-color .15s;box-sizing:border-box">
        </div>

        <select id="f-sort" name="sort" style="min-width:150px;padding:.5rem .7rem;border:1px solid #e2e8f0;border-radius:8px;font-size:13px;color:#0f172a;background:#fff;outline:none;transition:border-color .15s;box-sizing:border-box;cursor:pointer">
            <option value="newest" {{ ($sort ?? 'newest') == 'newest' ? 'selected' : '' }}>Más recientes</option>
            <option value="oldest" {{ ($sort ?? '') == 'oldest' ? 'selected' : '' }}>Más antiguos</option>
            <option value="az" {{ ($sort ?? '') == 'az' ? 'selected' : '' }}>Proveedor A → Z</option>
            <option value="za" {{ ($sort ?? '') == 'za' ? 'selected' : '' }}>Proveedor Z → A</option>
        </select>

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
    </form>
</div>

@if($suppliers->isEmpty())
    <div style="text-align:center;padding:4rem;background:#fff;border-radius:14px;border:1px solid #e2e8f0">
        <i class="ti ti-truck-off" style="font-size:48px;color:#cbd5e1;display:block;margin-bottom:1rem"></i>
        @if($search ?? '')
            <p style="font-size:15px;font-weight:600;color:#0f172a;margin-bottom:.4rem">No se encontraron proveedores</p>
            <p style="font-size:13px;color:#94a3b8;margin-bottom:1.2rem">No hay resultados para "{{ $search }}"</p>
            <a href="{{ route('admin.chains.show', $chain->id_chain) }}" class="btn btn-secondary btn-sm">
                <i class="ti ti-arrow-left"></i> Ver todos los proveedores
            </a>
        @else
            <p style="font-size:15px;font-weight:600;color:#0f172a;margin-bottom:.4rem">No hay proveedores en esta cadena</p>
            <p style="font-size:13px;color:#94a3b8;margin-bottom:1.2rem">Comienza agregando un proveedor a esta cadena</p>
            <a href="{{ route('admin.suppliers.create') }}?chain={{ $chain->id_chain }}" class="btn btn-primary btn-sm">
                <i class="ti ti-plus"></i> Agregar proveedor
            </a>
        @endif
    </div>
@else
    {{-- Tabla de proveedores --}}
    <div style="background:#fff;border-radius:12px;border:1px solid #e2e8f0;overflow:hidden">
        <div style="overflow-x:auto">
            <table style="width:100%;border-collapse:collapse;font-size:13px">
                <thead style="background:#f8fafc;border-bottom:1px solid #e2e8f0">
                    <tr>
                        <th style="padding:12px 16px;text-align:left;font-weight:600;color:#475569;font-size:11px;text-transform:uppercase;letter-spacing:.5px">Proveedor</th>
                        <th style="padding:12px 16px;text-align:left;font-weight:600;color:#475569;font-size:11px;text-transform:uppercase;letter-spacing:.5px">RUC</th>
                        <th style="padding:12px 16px;text-align:left;font-weight:600;color:#475569;font-size:11px;text-transform:uppercase;letter-spacing:.5px">Email</th>
                        <th style="padding:12px 16px;text-align:left;font-weight:600;color:#475569;font-size:11px;text-transform:uppercase;letter-spacing:.5px">Teléfono</th>
                        <th style="padding:12px 16px;text-align:left;font-weight:600;color:#475569;font-size:11px;text-transform:uppercase;letter-spacing:.5px">Categoría</th>
                        <th style="padding:12px 16px;text-align:left;font-weight:600;color:#475569;font-size:11px;text-transform:uppercase;letter-spacing:.5px">Registro</th>
                        <th style="padding:12px 16px;text-align:center;font-weight:600;color:#475569;font-size:11px;text-transform:uppercase;letter-spacing:.5px;width:100px">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($suppliers as $s)
                    <tr style="border-bottom:1px solid #f1f5f9;transition:background .1s" onmouseover="this.style.background='#fafbfc'" onmouseout="this.style.background=''">
                        <td style="padding:12px 16px">
                            <div style="display:flex;align-items:center;gap:10px">
                                <div style="background:linear-gradient(135deg,#e0f2fe,#bae6fd);color:#0369a1;width:32px;height:32px;border-radius:50%;display:flex;justify-content:center;align-items:center;font-weight:700;font-size:12px;flex-shrink:0">
                                    {{ strtoupper(substr($s->supplier_name, 0, 2)) }}
                                </div>
                                <span style="font-weight:600;color:#0f172a">{{ $s->supplier_name }}</span>
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
                            {{ $s->general_email ?? '—' }}
                        </td>
                        <td style="padding:12px 16px;color:#0f172a;font-size:12px">
                            {{ $s->general_phone ?? '—' }}
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
                        <td style="padding:12px 16px;color:#94a3b8;font-size:12px">{{ $s->created_at->format('d/m/Y') }}</td>
                        <td style="padding:12px 16px;text-align:center">
                            <div style="display:flex;gap:4px;justify-content:center">
                                <a href="{{ route('admin.suppliers.edit', $s->id_supplier) }}"
                                   style="padding:4px 8px;border-radius:4px;border:1px solid #e2e8f0;color:#64748b;text-decoration:none;font-size:12px;transition:all .15s;display:inline-flex;align-items:center"
                                   onmouseover="this.style.borderColor='#8b5cf6';this.style.color='#8b5cf6'"
                                   onmouseout="this.style.borderColor='#e2e8f0';this.style.color='#64748b'">
                                    <i class="ti ti-edit"></i>
                                </a>
                                <button type="button"
                                        onclick="confirmDeleteSupplier({{ $s->id_supplier }}, '{{ addslashes($s->supplier_name) }}')"
                                        style="padding:4px 8px;border-radius:4px;border:1px solid #e2e8f0;color:#64748b;font-size:12px;background:none;cursor:pointer;transition:all .15s;display:inline-flex;align-items:center"
                                        onmouseover="this.style.borderColor='#ef4444';this.style.color='#ef4444'"
                                        onmouseout="this.style.borderColor='#e2e8f0';this.style.color='#64748b'">
                                    <i class="ti ti-trash"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{-- PAGINADOR --}}
        <div style="display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:.5rem;padding:1rem 1.5rem;border-top:1px solid #e2e8f0;background:#fafbfc">
            <span style="font-size:13px;color:#64748b">
                Mostrando {{ $suppliers->firstItem() }}–{{ $suppliers->lastItem() }} de {{ $suppliers->total() }} proveedor(es)
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
@endif

<style>
    @keyframes modalFadeIn {
        from { opacity: 0; transform: scale(0.95) translateY(10px); }
        to { opacity: 1; transform: scale(1) translateY(0); }
    }
</style>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    // ── LIMPIAR FILTROS ──
    function clearFilters() {
        document.getElementById('f-search').value = '';
        document.getElementById('f-sort').value = 'newest';
        document.getElementById('filter-form').submit();
    }

    // ── CONFIRMAR ELIMINACIÓN DE PROVEEDOR ──
    function confirmDeleteSupplier(id, name) {
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
                form.action = `/proveedores/${id}`;

                const csrf = document.createElement('input');
                csrf.type = 'hidden';
                csrf.name = '_token';
                csrf.value = '{{ csrf_token() }}';

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

    const searchInput = document.getElementById('f-search');
    if (searchInput) {
        searchInput.addEventListener('keydown', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                document.getElementById('filter-form').submit();
            }
        });
    }
</script>
@endsection
