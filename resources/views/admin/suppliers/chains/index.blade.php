@extends('layouts.app')
@section('title', 'Cadenas de Proveedores')
@section('content')

<div class="page-header" style="display:flex;justify-content:space-between;align-items:flex-start;margin-bottom:1.6rem">
    <div>
        <div class="page-title">Cadenas de Proveedores</div>
        <div class="page-sub">Gestiona todas las cadenas del sistema</div>
    </div>
    <div style="display:flex;gap:.6rem;align-items:center;flex-wrap:wrap">
        <button type="button" onclick="openCreateModal()" class="btn btn-primary" style="display:inline-flex;align-items:center;gap:6px">
            <i class="ti ti-plus" style="font-size:15px"></i> Nueva cadena
        </button>
    </div>
</div>

{{-- Barra de búsqueda --}}
<div style="margin-bottom:1.2rem; background:#fff; padding:.8rem 1rem; border-radius:12px; border:1px solid #e2e8f0;">
    <form id="filter-form" method="GET" action="{{ route('admin.chains.index') }}" style="display:flex; flex-wrap:wrap; gap:8px; align-items:center;">
        <div style="position:relative;flex:1;min-width:200px">
            <i class="ti ti-search" style="position:absolute;left:.7rem;top:50%;transform:translateY(-50%);color:#94a3b8;font-size:15px"></i>
            <input type="text" id="f-search" name="search"
                   placeholder="Buscar cadena por nombre..."
                   value="{{ $search ?? '' }}"
                   style="width:100%;padding:.5rem .7rem .5rem 2.2rem;border:1px solid #e2e8f0;border-radius:8px;font-size:13px;color:#0f172a;outline:none;transition:border-color .15s;box-sizing:border-box">
        </div>

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

@if($chains->isEmpty())
    <div style="text-align:center;padding:4rem;background:#fff;border-radius:14px;border:1px solid #e2e8f0">
        <i class="ti ti-link-off" style="font-size:48px;color:#cbd5e1;display:block;margin-bottom:1rem"></i>
        @if($search ?? '')
            <p style="font-size:15px;font-weight:600;color:#0f172a;margin-bottom:.4rem">No se encontraron cadenas</p>
            <p style="font-size:13px;color:#94a3b8;margin-bottom:1.2rem">No hay resultados para "{{ $search }}"</p>
            <a href="{{ route('admin.chains.index') }}" class="btn btn-secondary btn-sm">
                <i class="ti ti-arrow-left"></i> Ver todas las cadenas
            </a>
        @else
            <p style="font-size:15px;font-weight:600;color:#0f172a;margin-bottom:.4rem">No hay cadenas aún</p>
            <p style="font-size:13px;color:#94a3b8;margin-bottom:1.2rem">Comienza agregando tu primera cadena de proveedores</p>
            <button type="button" onclick="openCreateModal()" class="btn btn-primary btn-sm">
                <i class="ti ti-plus"></i> Crear cadena
            </button>
        @endif
    </div>
@else
    {{-- Grid de cadenas --}}
    <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(280px,1fr));gap:1.2rem">
        @foreach($chains as $chain)
            <div style="background:#fff;border-radius:14px;border:1px solid #e2e8f0;padding:1.2rem 1.4rem;transition:all .2s;cursor:pointer"
                 onmouseover="this.style.borderColor='#8b5cf6';this.style.boxShadow='0 8px 20px -6px rgba(0,0,0,0.08)'"
                 onmouseout="this.style.borderColor='#e2e8f0';this.style.boxShadow='none'"
                 onclick="window.location.href='{{ route('admin.chains.show', $chain->id_chain) }}'">

                <div style="display:flex;align-items:flex-start;justify-content:space-between;gap:12px">
                    <div style="display:flex;align-items:center;gap:12px;min-width:0">
                        <div style="background:linear-gradient(135deg,#ede9fe,#c4b5fd);color:#5b21b6;width:42px;height:42px;border-radius:12px;display:flex;align-items:center;justify-content:center;font-size:18px;flex-shrink:0">
                            <i class="ti ti-link"></i>
                        </div>
                        <div style="min-width:0">
                            <h3 style="font-size:15px;font-weight:700;color:#0f172a;margin:0;white-space:nowrap;overflow:hidden;text-overflow:ellipsis">
                                {{ $chain->name }}
                            </h3>
                            <div style="font-size:12px;color:#94a3b8;display:flex;align-items:center;gap:6px;margin-top:2px">
                                <i class="ti ti-truck" style="font-size:12px"></i>
                                <span>{{ $chain->suppliers_count }} proveedor(es)</span>
                            </div>
                        </div>
                    </div>

                    <div style="display:flex;gap:4px;flex-shrink:0">
                        <button type="button"
                                class="btn-edit-chain"
                                data-id="{{ $chain->id_chain }}"
                                data-name="{{ $chain->name }}"
                                data-description="{{ $chain->description }}"
                                onclick="event.stopPropagation()"
                                style="padding:6px 8px;border-radius:6px;border:1px solid #e2e8f0;color:#64748b;font-size:14px;background:none;cursor:pointer;transition:all .15s;display:inline-flex;align-items:center"
                                onmouseover="this.style.borderColor='#8b5cf6';this.style.color='#8b5cf6'"
                                onmouseout="this.style.borderColor='#e2e8f0';this.style.color='#64748b'">
                            <i class="ti ti-edit"></i>
                        </button>
                        <button type="button"
                                onclick="event.stopPropagation();confirmDelete({{ $chain->id_chain }}, '{{ addslashes($chain->name) }}')"
                                style="padding:6px 8px;border-radius:6px;border:1px solid #e2e8f0;color:#64748b;font-size:14px;background:none;cursor:pointer;transition:all .15s;display:inline-flex;align-items:center"
                                onmouseover="this.style.borderColor='#ef4444';this.style.color='#ef4444'"
                                onmouseout="this.style.borderColor='#e2e8f0';this.style.color='#64748b'">
                            <i class="ti ti-trash"></i>
                        </button>
                    </div>
                </div>

                @if($chain->description)
                    <div style="margin-top:.7rem;padding-top:.7rem;border-top:1px solid #f1f5f9;font-size:13px;color:#64748b;display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden">
                        {{ $chain->description }}
                    </div>
                @endif

               <div style="margin-top:.6rem;display:flex;flex-wrap:wrap;gap:4px">
    <span style="background:#f8fafc;color:#475569;font-size:11px;padding:2px 10px;border-radius:4px;font-weight:500">
        <i class="ti ti-calendar" style="font-size:10px;margin-right:3px"></i>
        {{ $chain->created_at ? $chain->created_at->format('d/m/Y') : 'Fecha no disponible' }}
    </span>
    @if($chain->suppliers_count > 0)
        <span style="background:#ede9fe;color:#6d28d9;font-size:11px;padding:2px 10px;border-radius:4px;font-weight:600">
            <i class="ti ti-users" style="font-size:10px;margin-right:3px"></i>
            {{ $chain->suppliers_count }} proveedores
        </span>
    @endif
</div>
            </div>
        @endforeach
    </div>

    {{-- PAGINADOR --}}
    <div style="display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:.5rem;margin-top:1.5rem;padding:1rem 1.5rem;background:#fff;border-radius:12px;border:1px solid #e2e8f0">
        <span style="font-size:13px;color:#64748b">
            Mostrando {{ $chains->firstItem() }}–{{ $chains->lastItem() }} de {{ $chains->total() }} cadena(s)
        </span>

        <div style="display:flex;align-items:center;gap:.3rem">
            @if($chains->onFirstPage())
                <span style="padding:.35rem .7rem;border:1px solid #e2e8f0;border-radius:7px;font-size:12px;color:#cbd5e1;cursor:default;background:#fff">
                    <i class="ti ti-chevron-left"></i>
                </span>
            @else
                <a href="{{ $chains->previousPageUrl() }}"
                   style="padding:.35rem .7rem;border:1px solid #e2e8f0;border-radius:7px;font-size:12px;color:#374151;text-decoration:none;background:#fff;transition:all .15s"
                   onmouseover="this.style.borderColor='#6366f1';this.style.color='#6366f1'"
                   onmouseout="this.style.borderColor='#e2e8f0';this.style.color='#374151'">
                    <i class="ti ti-chevron-left"></i>
                </a>
            @endif

            @php
                $current = $chains->currentPage();
                $last = $chains->lastPage();
                $range = 2;
            @endphp

            @for($i = 1; $i <= $last; $i++)
                @if($i == 1 || $i == $last || abs($i - $current) <= $range)
                    @if($i == $current)
                        <span style="padding:.35rem .65rem;border:1px solid #6366f1;border-radius:7px;font-size:12px;font-weight:700;color:#fff;background:#6366f1;min-width:32px;text-align:center">
                            {{ $i }}
                        </span>
                    @else
                        <a href="{{ $chains->url($i) }}"
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

            @if($chains->hasMorePages())
                <a href="{{ $chains->nextPageUrl() }}"
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
@endif

{{-- ══════════════ MODAL: CREAR / EDITAR CADENA ══════════════ --}}
<div id="chain-modal" style="display:none;position:fixed;inset:0;background:rgba(15,23,42,.55);z-index:1000;align-items:center;justify-content:center;padding:1rem">
    <div style="background:#fff;border-radius:14px;width:100%;max-width:440px;padding:1.6rem;animation:modalFadeIn .2s ease;box-shadow:0 20px 40px -10px rgba(0,0,0,.25)">
        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:1.3rem">
            <h3 id="modal-title" style="font-size:16px;font-weight:700;color:#0f172a;margin:0;display:flex;align-items:center;gap:8px">
                <i class="ti ti-link" id="modal-icon" style="color:#6366f1"></i>
                <span id="modal-title-text">Nueva cadena</span>
            </h3>
            <button type="button" onclick="closeChainModal()" style="background:none;border:none;font-size:22px;color:#94a3b8;cursor:pointer;line-height:1;padding:0">&times;</button>
        </div>

        <form id="chain-form" method="POST" action="{{ route('admin.chains.store') }}">
            @csrf
            <input type="hidden" name="_method" id="form-method" value="POST">
            <input type="hidden" name="chain_id" id="chain-id-field" value="{{ old('chain_id') }}">

            <div style="margin-bottom:1rem">
                <label style="display:block;font-size:12px;font-weight:600;color:#475569;margin-bottom:5px">
                    Nombre <span style="color:#ef4444">*</span>
                </label>
                <input type="text" name="name" id="chain-name" required maxlength="255"
                       value="{{ old('name') }}"
                       style="width:100%;padding:.6rem .8rem;border:1px solid {{ $errors->has('name') ? '#ef4444' : '#e2e8f0' }};border-radius:8px;font-size:13px;color:#0f172a;outline:none;transition:border-color .15s;box-sizing:border-box"
                       onfocus="this.style.borderColor='#8b5cf6'"
                       onblur="this.style.borderColor='{{ $errors->has('name') ? '#ef4444' : '#e2e8f0' }}'">
                @error('name')
                    <div style="color:#ef4444;font-size:12px;margin-top:4px">{{ $message }}</div>
                @enderror
            </div>

            <div style="margin-bottom:1.5rem">
                <label style="display:block;font-size:12px;font-weight:600;color:#475569;margin-bottom:5px">Descripción</label>
                <textarea name="description" id="chain-description" rows="3" maxlength="1000"
                          style="width:100%;padding:.6rem .8rem;border:1px solid {{ $errors->has('description') ? '#ef4444' : '#e2e8f0' }};border-radius:8px;font-size:13px;color:#0f172a;outline:none;transition:border-color .15s;box-sizing:border-box;resize:vertical;font-family:inherit"
                          onfocus="this.style.borderColor='#8b5cf6'"
                          onblur="this.style.borderColor='{{ $errors->has('description') ? '#ef4444' : '#e2e8f0' }}'">{{ old('description') }}</textarea>
                @error('description')
                    <div style="color:#ef4444;font-size:12px;margin-top:4px">{{ $message }}</div>
                @enderror
            </div>

            <div style="display:flex;justify-content:flex-end;gap:.6rem">
                <button type="button" onclick="closeChainModal()"
                        style="padding:.6rem 1.2rem;background:none;border:1px solid #e2e8f0;border-radius:8px;font-size:13px;font-weight:600;color:#64748b;cursor:pointer;transition:all .15s"
                        onmouseover="this.style.background='#f1f5f9'"
                        onmouseout="this.style.background='none'">
                    Cancelar
                </button>
                <button type="submit" id="modal-submit-btn"
                        style="padding:.6rem 1.4rem;background:#6366f1;color:#fff;border:none;border-radius:8px;font-size:13px;font-weight:600;cursor:pointer;transition:background .15s;display:inline-flex;align-items:center;gap:6px"
                        onmouseover="this.style.background='#4f46e5'"
                        onmouseout="this.style.background='#6366f1'">
                    <i class="ti ti-device-floppy" style="font-size:14px"></i> Guardar
                </button>
            </div>
        </form>
    </div>
</div>

<style>
    @keyframes modalFadeIn {
        from { opacity: 0; transform: scale(0.95) translateY(10px); }
        to { opacity: 1; transform: scale(1) translateY(0); }
    }
</style>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    // ── MODAL DE CADENA (crear / editar) ──
    function openCreateModal() {
        document.getElementById('modal-title-text').textContent = 'Nueva cadena';
        document.getElementById('chain-form').action = "{{ route('admin.chains.store') }}";
        document.getElementById('form-method').value = 'POST';
        document.getElementById('chain-id-field').value = '';
        document.getElementById('chain-name').value = '';
        document.getElementById('chain-description').value = '';
        document.getElementById('chain-modal').style.display = 'flex';
        setTimeout(() => document.getElementById('chain-name').focus(), 50);
    }

    function openEditModal(id, name, description) {
        document.getElementById('modal-title-text').textContent = 'Editar cadena';
        document.getElementById('chain-form').action = `/cadenas/${id}`;
        document.getElementById('form-method').value = 'PUT';
        document.getElementById('chain-id-field').value = id;
        document.getElementById('chain-name').value = name || '';
        document.getElementById('chain-description').value = description || '';
        document.getElementById('chain-modal').style.display = 'flex';
        setTimeout(() => document.getElementById('chain-name').focus(), 50);
    }

    function closeChainModal() {
        document.getElementById('chain-modal').style.display = 'none';
    }

    // Cerrar modal al hacer clic fuera del contenido
    document.getElementById('chain-modal').addEventListener('click', function(e) {
        if (e.target === this) closeChainModal();
    });

    // Cerrar modal con tecla ESC
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') closeChainModal();
    });

    // Conectar los botones de "editar" de cada tarjeta
    document.querySelectorAll('.btn-edit-chain').forEach(function(btn) {
        btn.addEventListener('click', function() {
            openEditModal(this.dataset.id, this.dataset.name, this.dataset.description);
        });
    });

    // Si la validación falló, reabrir el modal automáticamente con los datos y errores
    @if($errors->any())
        document.addEventListener('DOMContentLoaded', function() {
            @if(old('chain_id'))
                openEditModal(
                    {{ old('chain_id') }},
                    @json(old('name')),
                    @json(old('description'))
                );
            @else
                openCreateModal();
                document.getElementById('chain-name').value = @json(old('name'));
                document.getElementById('chain-description').value = @json(old('description'));
            @endif
        });
    @endif

    // ── LIMPIAR FILTROS ──
    function clearFilters() {
        document.getElementById('f-search').value = '';
        document.getElementById('filter-form').submit();
    }

    // ── CONFIRMAR ELIMINACIÓN ──
    function confirmDelete(id, name) {
        Swal.fire({
            title: '¿Eliminar cadena?',
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
                form.action = `/cadenas/${id}`;

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
