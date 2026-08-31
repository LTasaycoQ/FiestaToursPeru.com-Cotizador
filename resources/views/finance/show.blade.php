@extends('layouts.app')
@section('title', 'Detalle del Proyecto')
@section('content')

<style>
    /* ── Estilos base ── */
    .tab-container {
        display: flex;
        gap: 0;
        margin-bottom: 1.5rem;
    }
    .tab-btn {
        padding: 0.8rem 1.5rem;
        background: none;
        border: none;
        font-size: 13px;
        font-weight: 700;
        color: #94a3b8;
        cursor: pointer;
        position: relative;
        transition: all 0.3s ease;
        display: inline-flex;
        align-items: center;
        text-transform: uppercase;
        gap: 8px;
    }
    .tab-btn:hover {
        color: #0f172a;
    }
    .tab-btn.active {
        background-color: #fff;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.27);
        border-radius: 8px;
        color: #24523f;
    }
    .tab-content {
        display: none;
        animation: fadeIn 0.3s ease;
    }
    .tab-content.active {
        display: block;
    }
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(10px); }
        to { opacity: 1; transform: translateY(0); }
    }
    @keyframes modalFadeIn {
        from { opacity: 0; transform: scale(0.95) translateY(10px); }
        to { opacity: 1; transform: scale(1) translateY(0); }
    }
    @keyframes spin {
        from { transform: rotate(0deg); }
        to { transform: rotate(360deg); }
    }
    
    .filter-btn {
        padding: 0.3rem 0.8rem;
        background: #e2e8f0;
        color: #475569;
        border: none;
        border-radius: 6px;
        font-size: 11px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.2s;
    }
    .filter-btn:hover {
        opacity: 0.8;
        transform: scale(1.05);
    }
    .filter-btn.active {
        background: #2e453d;
        color: #fff;
    }

    .btn-export {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 0.5rem 1rem;
        background: #217346;
        color: #fff;
        border: none;
        border-radius: 6px;
        font-size: 12px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.2s;
        text-decoration: none;
    }
    .btn-export:hover {
        background: #1a5c38;
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(33, 115, 70, 0.3);
    }
    .btn-export i {
        font-size: 14px;
    }

    .pagination-container {
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: .5rem;
        padding: 1rem 1.5rem;
        border-top: 1px solid #e2e8f0;
        background: #fafbfc;
    }
    .pagination-info {
        font-size: 13px;
        color: #64748b;
    }
    .pagination-links {
        display: flex;
        align-items: center;
        gap: .3rem;
    }
    .pagination-links a,
    .pagination-links span {
        padding: .35rem .65rem;
        border: 1px solid #e2e8f0;
        border-radius: 7px;
        font-size: 12px;
        color: #374151;
        text-decoration: none;
        background: #fff;
        min-width: 32px;
        text-align: center;
        transition: all .15s;
    }
    .pagination-links a:hover {
        border-color: #3C4E3E;
        color: #3C4E3E;
    }
    .pagination-links .active-page {
        border-color: #3C4E3E;
        background: #3C4E3E;
        color: #fff;
        font-weight: 700;
    }
    .pagination-links .disabled {
        color: #cbd5e1;
        cursor: default;
        border-color: #e2e8f0;
    }
    .pagination-links .dots {
        border: none;
        background: transparent;
        color: #94a3b8;
        cursor: default;
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
    .btn-secondary {
        background: #f1f5f9;
        color: #475569;
    }
    .btn-secondary:hover {
        background: #e2e8f0;
        transform: translateY(-2px);
    }
    .btn-sm {
        font-size: .75rem;
        padding: .45rem 1rem;
    }
    .btn-success {
        background: #2e453d;
        color: #fff;
    }
    .btn-success:hover {
        background: #1f332c;
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(46, 69, 61, 0.3);
    }
    .btn-warning {
        background: #2d2c37;
        color: #fff;
    }
    .btn-warning:hover {
        background: #1e1e29;
        transform: translateY(-2px);
    }

    .badge-tipo {
        background: #2b4530;
        color: #fff;
        padding: 2px 10px;
        border-radius: 4px;
        font-size: 11px;
        font-weight: 600;
    }
</style>

<div class="page-header" style="display:flex;justify-content:space-between;align-items:flex-start;margin-bottom:1.6rem">
    <div>
        <div class="page-title">{{ $project->name }}</div>
        <div class="page-sub"><strong>Creado</strong> {{ $project->created_at->locale('es')->diffForHumans() }}</div>
    </div>
    <div style="display:flex;gap:.6rem;align-items:center;flex-wrap:wrap">
        <a href="{{ route('finance.index') }}" class="btn btn-secondary" style="display:inline-flex;align-items:center;gap:6px">
            <i class="ti ti-arrow-left" style="font-size:15px"></i> Volver
        </a>
        @php $user = auth()->user(); @endphp
        @if($user->isAdmin() || $user->email === 'administracion1@fiestatoursperu.com')

            @if(($project->balance->amount ?? 0) == 0)
                <button onclick="openSetInitialBalanceModal()" 
                        style="padding:.5rem 1.2rem;background: #6a1d24;color:#fff;border:none;border-radius:6px;font-size:13px;font-weight:600;cursor:pointer;display:inline-flex;align-items:center;gap:6px">
                    <i class="ti ti-plus"></i> Asignar Balance Inicial
                </button>
            @else
                    <button onclick="openRechargeModal()" 
                            style="padding:.5rem 1.2rem;background: #2e453d;color:#fff;border:none;border-radius:6px;font-size:13px;font-weight:600;cursor:pointer;display:inline-flex;align-items:center;gap:6px">
                        <i class="ti ti-plus-circle"></i> Recargar Balance
                    </button>
    
                    <div style="display:flex;gap:.5rem;align-items:center;flex-wrap:wrap">
                        <a href="{{ route('finance.export.all', $project->id_proyect) }}" class="btn-export" style="background:#1a5c38;">
                            <i class="ti ti-file-excel"></i> Exportar Todos
                        </a>
                    
                    </div>
    Q
            @endif
        @endif
    </div>
</div>

<div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(220px,1fr));gap:1.2rem;margin-bottom:1.5rem">
    <div style="background: #141C2F;border-radius:14px;border:1px solid #4f6492;padding:1.5rem;text-align:center;box-shadow: 0 4px 6px rgba(0, 0, 0, 0.28);">
        <div style="font-size:12px;color: #b8b9ba;text-transform:uppercase;font-weight:600;letter-spacing:.5px">Balance Disponible</div>
        <div style="font-size:28px;font-weight:700;color:#ffffff;margin-top:4px">
            {{ $currencySymbol }} {{ number_format($project->balance->amount ?? 0, 2) }}
        </div>
    </div>

    <div style="background: linear-gradient(135deg, #183d3d, #274f4f);border-radius:14px;border:1px solid #3fd5b7;padding:1.5rem;text-align:center;box-shadow: 0 4px 6px rgba(0, 0, 0, 0.28);position:relative;">
        <div style="font-size:12px;color: rgba(255,255,255,.8);text-transform:uppercase;font-weight:600;letter-spacing:.5px">Balance Real en el Banco</div>
        <div style="font-size:28px;font-weight:700;color:#ffffff;margin-top:4px">
            {{ $currencySymbol }} {{ number_format($realBalance ?? 0, 2) }}
        </div>
        @if($user->isAdmin() || $user->email === 'administracion1@fiestatoursperu.com')
            <button type="button" onclick="openRealBalanceModal()" style="margin-top:12px;padding:.5rem .9rem;background:rgba(255,255,255,.12);border:1px solid rgba(255,255,255,.2);border-radius:8px;color:#fff;font-weight:600;font-size:12px;cursor:pointer;display:inline-flex;align-items:center;gap:6px">
                <i class="ti ti-pencil"></i> Actualizar
            </button>
        @endif
    </div>
</div>

<div class="tab-container" style="display:flex; justify-content:space-between;align-items:center;flex-wrap:wrap;margin-bottom:1.5rem">
    <div style="display:flex;gap:.6rem;align-items:center;flex-wrap:wrap">
        <button class="tab-btn active" onclick="switchTab('gastos', this)">
            <i class="ti ti-receipt"></i> Gastos
        </button>
        <button class="tab-btn" onclick="switchTab('operativos', this)">
            <i class="ti ti-category"></i> Gestión Operativa
        </button>
         @if($user->isAdmin() || $user->email === 'administracion1@fiestatoursperu.com')
            <button class="tab-btn" onclick="switchTab('recargas', this)">
                <i class="ti ti-history"></i> Recargas
            </button>
        @endif
    </div>
</div>

{{-- ── TAB: GASTOS ── --}}
<div id="tab-gastos" class="tab-content active">
    <div style="background:#fff;border-radius:14px;border:1px solid #e2e8f0;padding:1.5rem">

        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:1rem;flex-wrap:wrap;gap:.5rem">
            <h4 style="margin:0;font-size:14px;color:#475569">
                <i class="ti ti-category" style="color:#2d2c37"></i>
                Gastos Registrados
            </h4>
            <div style="display:flex;gap:.5rem;align-items:center;flex-wrap:wrap">
                
                <button type="button" onclick="openExpenseModal()" class="btn btn-success" style="display:inline-flex;align-items:center;gap:6px; font-size:13px; font-weight:600; cursor:pointer; padding: 0.5rem 1.2rem; border-radius:6px; border:none;">
                    <i class="ti ti-plus" style="font-size:15px"></i> Registrar Gasto
                </button>
            </div>
        </div>

        @if($regularExpenses->count() > 0)
            <div style="overflow-x:auto">
                <table style="width:100%;border-collapse:collapse;font-size:13px">
                    <thead>
                        <tr style="background:#f8fafc;border-bottom:2px solid #e2e8f0">
                            <th style="text-align:center;padding:.6rem .8rem;color:#64748b;font-weight:600;font-size:12px;text-transform:uppercase">Fecha Compra</th>
                            <th style="text-align:center;padding:.6rem .8rem;color:#64748b;font-weight:600;font-size:12px;text-transform:uppercase">Persona</th>
                            <th style="text-align:center;padding:.6rem .8rem;color:#64748b;font-weight:600;font-size:12px;text-transform:uppercase">R. Codigo</th>
                            <th style="text-align:center;padding:.6rem .8rem;color:#64748b;font-weight:600;font-size:12px;text-transform:uppercase">N. File.</th>
                            <th style="text-align:center;padding:.6rem .8rem;color:#64748b;font-weight:600;font-size:12px;text-transform:uppercase">Monto</th>
                            <th style="text-align:center;padding:.6rem .8rem;color:#64748b;font-weight:600;font-size:12px;text-transform:uppercase">Acciones</th>
                        </tr>
                    </thead>
                    <tbody id="expenses-table-body">
                        @foreach($regularExpenses as $index => $expense)
                            <tr style="border-bottom:1px solid #f1f5f9;transition:background .15s" 
                                onmouseover="this.style.background='#f8fafc'" 
                                onmouseout="this.style.background='transparent'"
                                data-person="{{ $expense->name_people }}">
                                <td style="padding:.6rem .8rem;color:#64748b;font-size:12px; text-align:center;">
                                    {{ $expense->expense_date->format('d/m/Y') }}
                                </td>
                                <td style="padding:.6rem .8rem;color:#0f172a;font-weight:500; text-align:center;">
                                    {{ $expense->name_people ?? 'Usuario eliminado' }}
                                </td>
                                <td style="padding:.6rem .8rem;text-align:center;font-weight:600;color: #313039">
                                    {{ $expense->reservation_code }}
                                </td>
                                <td style="padding:.6rem .8rem;text-align:center;font-weight:600;color: #313039">
                                    {{ $expense->file_number }}
                                </td>
                                <td style="padding:.6rem .8rem;text-align:center;font-weight:600;color:#ef4444">
                                    -{{ $currencySymbol }} {{ number_format($expense->amount, 2) }}
                                </td>
                                <td style="padding:.6rem .8rem;text-align:center">
                                    <div style="display:flex;gap:4px;justify-content:center">
                                        <button onclick="openEditExpenseModal({{ json_encode($project->id_proyect) }}, {{ json_encode($expense->id_expense) }})"
                                                style="background:#6366f1;color:#fff;border:none;border-radius:4px;padding:4px 8px;font-size:11px;cursor:pointer;display:inline-flex;align-items:center;gap:3px"
                                                onmouseover="this.style.background='#4f46e5'"
                                                onmouseout="this.style.background='#6366f1'">
                                            <i class="ti ti-edit" style="font-size:12px"></i>
                                        </button>
                                        <button onclick="deleteExpense({{ json_encode($project->id_proyect) }}, {{ json_encode($expense->id_expense) }})" 
                                                style="background:#ef4444;color:#fff;border:none;border-radius:4px;padding:4px 8px;font-size:11px;cursor:pointer;display:inline-flex;align-items:center;gap:3px"
                                                onmouseover="this.style.background='#dc2626'"
                                                onmouseout="this.style.background='#ef4444'">
                                            <i class="ti ti-trash" style="font-size:12px"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- PAGINADOR GASTOS --}}
            @if($regularExpenses->hasPages())
            <div class="pagination-container">
                <span class="pagination-info">
                    Mostrando {{ $regularExpenses->firstItem() }}–{{ $regularExpenses->lastItem() }} de {{ $regularExpenses->total() }} gasto(s)
                </span>
                <div class="pagination-links">
                    {{-- Anterior --}}
                    @if($regularExpenses->onFirstPage())
                        <span class="disabled"><i class="ti ti-chevron-left"></i></span>
                    @else
                        <a href="{{ $regularExpenses->previousPageUrl() }}"><i class="ti ti-chevron-left"></i></a>
                    @endif

                    {{-- Números --}}
                    @php
                        $current = $regularExpenses->currentPage();
                        $last = $regularExpenses->lastPage();
                        $range = 2;
                    @endphp

                    @for($i = 1; $i <= $last; $i++)
                        @if($i == 1 || $i == $last || abs($i - $current) <= $range)
                            @if($i == $current)
                                <span class="active-page">{{ $i }}</span>
                            @else
                                <a href="{{ $regularExpenses->url($i) }}">{{ $i }}</a>
                            @endif
                        @elseif(abs($i - $current) == $range + 1)
                            <span class="dots">…</span>
                        @endif
                    @endfor

                    {{-- Siguiente --}}
                    @if($regularExpenses->hasMorePages())
                        <a href="{{ $regularExpenses->nextPageUrl() }}"><i class="ti ti-chevron-right"></i></a>
                    @else
                        <span class="disabled"><i class="ti ti-chevron-right"></i></span>
                    @endif
                </div>
            </div>
            @endif

        @else
            <div style="text-align:center;padding:3rem 0">
                <i class="ti ti-receipt-off" style="font-size:48px;color:#cbd5e1;display:block;margin-bottom:.5rem"></i>
                <p style="color:#94a3b8;font-size:14px;margin:0">No hay gastos registrados para este proyecto</p>
                <p style="color:#94a3b8;font-size:12px;margin-top:4px">Haz clic en "Registrar Gasto" para comenzar</p>
            </div>
        @endif
    </div>
</div>

{{-- ── TAB: GESTIÓN OPERATIVA ── --}}
<div id="tab-operativos" class="tab-content">
    <div style="background:#fff;border-radius:14px;border:1px solid #e2e8f0;padding:1.5rem">
        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:1rem;flex-wrap:wrap;gap:.5rem">
            <h4 style="margin:0;font-size:14px;color:#475569">
                <i class="ti ti-category" style="color:#2d2c37"></i>
                Gastos de Gestión Operativa
                <span style="font-size:12px;color:#94a3b8;font-weight:400">({{ $operationalExpenses->total() }})</span>
            </h4>
            <div style="display:flex;gap:.5rem;align-items:center;flex-wrap:wrap">
               
                <button type="button" onclick="openOtherExpenseModal()" class="btn btn-warning" style="display:inline-flex;align-items:center;gap:6px; font-size:13px; font-weight:600; cursor:pointer; padding: 0.5rem 1.2rem; border-radius:6px; border:none;">
                    <i class="ti ti-category" style="font-size:15px"></i> Gastos gestión Operativa
                </button>
            </div>
        </div>
        
        @if($operationalExpenses->count() > 0)
            <div style="overflow-x:auto">
                <table style="width:100%;border-collapse:collapse;font-size:13px">
                    <thead>
                        <tr style="background:#f8fafc;border-bottom:2px solid #e2e8f0">
                            <th style="text-align:center;padding:.6rem .8rem;color:#64748b;font-weight:600;font-size:12px;text-transform:uppercase">Fecha</th>
                            <th style="text-align:center;padding:.6rem .8rem;color:#64748b;font-weight:600;font-size:12px;text-transform:uppercase">Nombre</th>
                            <th style="text-align:center;padding:.6rem .8rem;color:#64748b;font-weight:600;font-size:12px;text-transform:uppercase">Tipo</th>
                            <th style="text-align:center;padding:.6rem .8rem;color:#64748b;font-weight:600;font-size:12px;text-transform:uppercase">Monto</th>
                            <th style="text-align:center;padding:.6rem .8rem;color:#64748b;font-weight:600;font-size:12px;text-transform:uppercase">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($operationalExpenses as $index => $expense)
                            <tr style="border-bottom:1px solid #f1f5f9;transition:background .15s" onmouseover="this.style.background='#f8fafc'" onmouseout="this.style.background='transparent'">
                                <td style="padding:.6rem .8rem;color:#64748b;font-size:12px;text-align:center;">
                                    {{ $expense->expense_date->format('d/m/Y') }}
                                </td>
                                <td style="padding:.6rem .8rem;color:#0f172a;font-weight:500;text-align:center;">
                                    {{ $expense->name_people ?? 'Gestión Operativa' }}
                                </td>
                                <td style="padding:.6rem .8rem;text-align:center;">
                                    <span class="badge-tipo">
                                        {{ $expense->reservation_code ?? 'OTRO' }}
                                    </span>
                                </td>
                                <td style="padding:.6rem .8rem;text-align:center;font-weight:600;color:#ef4444">
                                    -{{ $currencySymbol }} {{ number_format($expense->amount, 2) }}
                                </td>
                                <td style="padding:.6rem .8rem;text-align:center">
                                    <div style="display:flex;gap:4px;justify-content:center">
                                        <button onclick="openEditOperationalModal({{ json_encode($project->id_proyect) }}, {{ json_encode($expense->id_expense) }})"
                                                style="background:#6366f1;color:#fff;border:none;border-radius:4px;padding:4px 8px;font-size:11px;cursor:pointer;display:inline-flex;align-items:center;gap:3px"
                                                onmouseover="this.style.background='#4f46e5'"
                                                onmouseout="this.style.background='#6366f1'">
                                            <i class="ti ti-edit" style="font-size:12px"></i>
                                        </button>
                                        <button onclick="deleteExpense({{ json_encode($project->id_proyect) }}, {{ json_encode($expense->id_expense) }})" 
                                                style="background:#ef4444;color:#fff;border:none;border-radius:4px;padding:4px 8px;font-size:11px;cursor:pointer;display:inline-flex;align-items:center;gap:3px"
                                                onmouseover="this.style.background='#dc2626'"
                                                onmouseout="this.style.background='#ef4444'">
                                            <i class="ti ti-trash" style="font-size:12px"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- PAGINADOR OPERATIVOS --}}
            @if($operationalExpenses->hasPages())
            <div class="pagination-container">
                <span class="pagination-info">
                    Mostrando {{ $operationalExpenses->firstItem() }}–{{ $operationalExpenses->lastItem() }} de {{ $operationalExpenses->total() }} gasto(s) operativo(s)
                </span>
                <div class="pagination-links">
                    @if($operationalExpenses->onFirstPage())
                        <span class="disabled"><i class="ti ti-chevron-left"></i></span>
                    @else
                        <a href="{{ $operationalExpenses->previousPageUrl() }}"><i class="ti ti-chevron-left"></i></a>
                    @endif

                    @php
                        $current = $operationalExpenses->currentPage();
                        $last = $operationalExpenses->lastPage();
                        $range = 2;
                    @endphp

                    @for($i = 1; $i <= $last; $i++)
                        @if($i == 1 || $i == $last || abs($i - $current) <= $range)
                            @if($i == $current)
                                <span class="active-page">{{ $i }}</span>
                            @else
                                <a href="{{ $operationalExpenses->url($i) }}">{{ $i }}</a>
                            @endif
                        @elseif(abs($i - $current) == $range + 1)
                            <span class="dots">…</span>
                        @endif
                    @endfor

                    @if($operationalExpenses->hasMorePages())
                        <a href="{{ $operationalExpenses->nextPageUrl() }}"><i class="ti ti-chevron-right"></i></a>
                    @else
                        <span class="disabled"><i class="ti ti-chevron-right"></i></span>
                    @endif
                </div>
            </div>
            @endif

        @else
            <div style="text-align:center;padding:3rem 0">
                <i class="ti ti-category-off" style="font-size:48px;color:#cbd5e1;display:block;margin-bottom:.5rem"></i>
                <p style="color:#94a3b8;font-size:14px;margin:0">No hay gastos de gestión operativa registrados</p>
            </div>
        @endif
    </div>
</div>

{{-- ── TAB: RECARGAS ── --}}
<div id="tab-recargas" class="tab-content">
    <div style="background:#fff;border-radius:14px;border:1px solid #e2e8f0;padding:1.5rem">
        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:1rem;flex-wrap:wrap;gap:.5rem">
            <h4 style="margin:0;font-size:14px;color:#475569">
                <i class="ti ti-history" style="color:#2d2c37"></i>
                Historial de Recargas
                <span style="font-size:12px;color:#94a3b8;font-weight:400">({{ $recharges->total() }})</span>
            </h4>
            <div style="display:flex;gap:.5rem;align-items:center;flex-wrap:wrap">
                <a href="{{ route('finance.export.recharges', $project->id_proyect) }}" class="btn-export">
                    <i class="ti ti-file-excel"></i> Exportar Excel
                </a>
            </div>
        </div>
        
        @if($recharges->count() > 0)
            <div style="overflow-x:auto">
                <table style="width:100%;border-collapse:collapse;font-size:13px">
                    <thead>
                        <tr style="background:#f8fafc;border-bottom:2px solid #e2e8f0">
                            <th style="text-align:center;padding:.6rem .8rem;color:#64748b;font-weight:600;font-size:12px;text-transform:uppercase">#</th>
                            <th style="text-align:center;padding:.6rem .8rem;color:#64748b;font-weight:600;font-size:12px;text-transform:uppercase">Fecha</th>
                            <th style="text-align:center;padding:.6rem .8rem;color:#64748b;font-weight:600;font-size:12px;text-transform:uppercase">Monto</th>
                            <th style="text-align:center;padding:.6rem .8rem;color:#64748b;font-weight:600;font-size:12px;text-transform:uppercase">Balance Anterior</th>
                            <th style="text-align:center;padding:.6rem .8rem;color:#64748b;font-weight:600;font-size:12px;text-transform:uppercase">Nuevo Balance</th>
                            <th style="text-align:center;padding:.6rem .8rem;color:#64748b;font-weight:600;font-size:12px;text-transform:uppercase">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($recharges as $index => $recharge)
                            <tr style="border-bottom:1px solid #f1f5f9;transition:background .15s" onmouseover="this.style.background='#f8fafc'" onmouseout="this.style.background='transparent'">
                                <td style="text-align:center;padding:.6rem .8rem;color:#94a3b8;font-size:12px;font-weight:600">{{ $index + 1 }}</td>
                                <td style="text-align:center;padding:.6rem .8rem;color:#64748b;font-size:12px">
                                    {{ $recharge->recharge_date->format('d/m/Y') }}
                                </td>
                                <td style="padding:.6rem .8rem;text-align:center;font-weight:600;color:#10b981">
                                    +{{ $currencySymbol }} {{ number_format($recharge->amount, 2) }}
                                </td>
                                <td style="padding:.6rem .8rem;text-align:center;color:#64748b">
                                    {{ $currencySymbol }} {{ number_format($recharge->previous_balance, 2) }}
                                </td>
                                <td style="padding:.6rem .8rem;text-align:center;font-weight:600;color:#0f172a">
                                    {{ $currencySymbol }} {{ number_format($recharge->new_balance, 2) }}
                                </td>
                                <td style="padding:.6rem .8rem;text-align:center">
                                    <div style="display:flex;gap:4px;justify-content:center">
                                        <button onclick="openEditRechargeModal({{ json_encode($project->id_proyect) }}, {{ json_encode($recharge->id_recharge) }})"
                                                style="background:#10b981;color:#fff;border:none;border-radius:4px;padding:4px 8px;font-size:11px;cursor:pointer;display:inline-flex;align-items:center;gap:3px"
                                                onmouseover="this.style.background='#059669'"
                                                onmouseout="this.style.background='#10b981'">
                                            <i class="ti ti-edit" style="font-size:12px"></i>
                                        </button>
                                        <button onclick="deleteRecharge({{ json_encode($project->id_proyect) }}, {{ json_encode($recharge->id_recharge) }})" 
                                                style="background:#ef4444;color:#fff;border:none;border-radius:4px;padding:4px 8px;font-size:11px;cursor:pointer;display:inline-flex;align-items:center;gap:3px"
                                                onmouseover="this.style.background='#dc2626'"
                                                onmouseout="this.style.background='#ef4444'">
                                            <i class="ti ti-trash" style="font-size:12px"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- PAGINADOR RECARGAS --}}
            @if($recharges->hasPages())
            <div class="pagination-container">
                <span class="pagination-info">
                    Mostrando {{ $recharges->firstItem() }}–{{ $recharges->lastItem() }} de {{ $recharges->total() }} recarga(s)
                </span>
                <div class="pagination-links">
                    @if($recharges->onFirstPage())
                        <span class="disabled"><i class="ti ti-chevron-left"></i></span>
                    @else
                        <a href="{{ $recharges->previousPageUrl() }}"><i class="ti ti-chevron-left"></i></a>
                    @endif

                    @php
                        $current = $recharges->currentPage();
                        $last = $recharges->lastPage();
                        $range = 2;
                    @endphp

                    @for($i = 1; $i <= $last; $i++)
                        @if($i == 1 || $i == $last || abs($i - $current) <= $range)
                            @if($i == $current)
                                <span class="active-page">{{ $i }}</span>
                            @else
                                <a href="{{ $recharges->url($i) }}">{{ $i }}</a>
                            @endif
                        @elseif(abs($i - $current) == $range + 1)
                            <span class="dots">…</span>
                        @endif
                    @endfor

                    @if($recharges->hasMorePages())
                        <a href="{{ $recharges->nextPageUrl() }}"><i class="ti ti-chevron-right"></i></a>
                    @else
                        <span class="disabled"><i class="ti ti-chevron-right"></i></span>
                    @endif
                </div>
            </div>
            @endif

        @else
            <div style="text-align:center;padding:3rem 0">
                <i class="ti ti-history-off" style="font-size:48px;color:#cbd5e1;display:block;margin-bottom:.5rem"></i>
                <p style="color:#94a3b8;font-size:14px;margin:0">No hay recargas registradas</p>
            </div>
        @endif
    </div>
</div>

{{-- ── MODALES ── --}}
<!-- Expense Modal -->
<div id="expense-modal" style="display:none;position:fixed;inset:0;background:rgba(15,23,42,.55);z-index:1000;align-items:center;justify-content:center;padding:1rem">
    <div style="background:#fff;border-radius:14px;width:100%;max-width:520px;padding:1.6rem;animation:modalFadeIn .2s ease;box-shadow:0 20px 40px -10px rgba(0,0,0,.25)">
        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:1.3rem">
            <h3 style="font-size:16px;font-weight:700;color: #0f172a;margin:0;display:flex;align-items:center;gap:8px">
                <i class="ti ti-receipt" style="color: #32473c"></i>
                Registrar Gastos
            </h3>
            <button type="button" onclick="closeExpenseModal()" style="background:none;border:none;font-size:22px;color:#94a3b8;cursor:pointer;line-height:1;padding:0">&times;</button>
        </div>

        <form method="POST" id="expense-form" action="{{ route('finance.registerExpense', $project->id_proyect) }}">
            @csrf
            <div style="margin-bottom:1rem">
                <label style="display:block;font-size:12px;font-weight:600;color:#475569;margin-bottom:5px">Fecha de Compra</label>
                <input type="date" name="expense_date" required
                       style="width:100%;padding:.6rem .8rem;border:1px solid #e2e8f0;border-radius:8px;font-size:13px;color:#0f172a;outline:none;transition:border-color .15s"
                       onfocus="this.style.borderColor='#32473c'" onblur="this.style.borderColor='#e2e8f0'">
            </div>

            <div style="margin-bottom:1rem; display:none;">
                <label style="display:block;font-size:12px;font-weight:600;color:#475569;margin-bottom:5px">Persona que registra el Gasto</label>
                @php $user = auth()->user(); @endphp
                <input type="text" name="name_people" value="{{ $user->name }}"
                       style="width:100%;padding:.6rem .8rem;border:1px solid #e2e8f0;border-radius:8px;font-size:13px;color:#0f172a;outline:none;transition:border-color .15s;background:#f1f5f9"
                       onfocus="this.style.borderColor='#32473c'" onblur="this.style.borderColor='#e2e8f0'">
            </div>

            <div style="margin-bottom:1rem">
                <label style="display:block;font-size:12px;font-weight:600;color:#475569;margin-bottom:5px">Codigo de Reserva</label>
                <input type="text" name="reservation_code" required placeholder="28******A04"
                       style="width:100%;padding:.6rem .8rem;border:1px solid #e2e8f0;border-radius:8px;font-size:13px;color:#0f172a;outline:none;transition:border-color .15s"
                       onfocus="this.style.borderColor='#32473c'" onblur="this.style.borderColor='#e2e8f0'">
            </div>

            <div style="margin-bottom:1rem">
                <label style="display:block;font-size:12px;font-weight:600;color:#475569;margin-bottom:5px">Numero de File</label>
                <input type="text" name="file_number" placeholder="10-100-26" required
                       style="width:100%;padding:.6rem .8rem;border:1px solid #e2e8f0;border-radius:8px;font-size:13px;color:#0f172a;outline:none;transition:border-color .15s"
                       onfocus="this.style.borderColor='#32473c'" onblur="this.style.borderColor='#e2e8f0'">
            </div>

            <div style="margin-bottom:1rem">
                <label style="display:block;font-size:12px;font-weight:600;color:#475569;margin-bottom:5px">Monto del gasto</label>
                <div style="position:relative">
                    <span style="position:absolute;left:.8rem;top:50%;transform:translateY(-50%);color:#94a3b8;font-weight:600">$</span>
                    <input type="number" name="amount" required step="0.01" min="0.01" max="{{ $availableBalance }}"
                           placeholder="0.00"
                           style="width:100%;padding:.6rem .8rem .6rem 2rem;border:1px solid #e2e8f0;border-radius:8px;font-size:13px;color:#0f172a;outline:none;transition:border-color .15s;box-sizing:border-box"
                           onfocus="this.style.borderColor='#32473c'" onblur="this.style.borderColor='#e2e8f0'">
                </div>
                <div style="display:flex;justify-content:space-between;font-size:12px;color:#64748b;margin-top:4px">
                    <span>Balance disponible: <strong>${{ number_format($availableBalance, 2) }}</strong></span>
                </div>
            </div>

            <div style="display:flex;justify-content:flex-end;gap:.6rem">
                <button type="button" onclick="closeExpenseModal()" style="padding:.6rem 1.2rem;background:none;border:1px solid #e2e8f0;border-radius:8px;font-size:13px;font-weight:600;color:#64748b;cursor:pointer;transition:all .15s"
                        onmouseover="this.style.background='#f1f5f9'" onmouseout="this.style.background='none'">Cancelar</button>
                <button type="submit" style="padding:.6rem 1.4rem;background: #32473c;color:#fff;border:none;border-radius:8px;font-size:13px;font-weight:600;cursor:pointer;transition:background .15s;display:inline-flex;align-items:center;gap:6px"
                        onmouseover="this.style.background='#25503a'" onmouseout="this.style.background='#32473c'">
                    <i class="ti ti-device-floppy" style="font-size:14px"></i> Registrar Gasto
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Real Bank Balance Modal -->
<div id="real-balance-modal" style="display:none;position:fixed;inset:0;background:rgba(15,23,42,.55);z-index:1000;align-items:center;justify-content:center;padding:1rem">
    <div style="background:#fff;border-radius:14px;width:100%;max-width:520px;padding:1.6rem;animation:modalFadeIn .2s ease;box-shadow:0 20px 40px -10px rgba(0,0,0,.25)">
        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:1.3rem">
            <h3 style="font-size:16px;font-weight:700;color:#0f172a;margin:0;display:flex;align-items:center;gap:8px">
                <i class="ti ti-wallet" style="color:#0f766e"></i> Actualizar Balance Real
            </h3>
            <button type="button" onclick="closeRealBalanceModal()" style="background:none;border:none;font-size:22px;color:#94a3b8;cursor:pointer;line-height:1;padding:0">&times;</button>
        </div>

        <form id="real-balance-form" method="POST" action="{{ route('finance.updateRealBalance', $project->id_proyect) }}">
            @csrf
            <div style="margin-bottom:1rem">
                <label style="display:block;font-size:12px;font-weight:600;color:#475569;margin-bottom:5px">Balance real en el banco <span style="color:#ef4444">*</span></label>
                <div style="position:relative">
                    <span style="position:absolute;left:.8rem;top:50%;transform:translateY(-50%);color:#94a3b8;font-weight:600">S/</span>
                    <span style="position:absolute;left:.8rem;top:50%;transform:translateY(-50%);color:#94a3b8;font-weight:600">{{ $currencySymbol }}</span>
                    <input type="number" name="real_amount" value="{{ $realBalance ?? 0 }}" required step="0.01" min="0"
                           placeholder="0.00"
                           style="width:100%;padding:.6rem .8rem .6rem 2.2rem;border:1px solid #e2e8f0;border-radius:8px;font-size:13px;color:#0f172a;outline:none;transition:border-color .15s;box-sizing:border-box"
                           onfocus="this.style.borderColor='#0f766e'" onblur="this.style.borderColor='#e2e8f0'">
                </div>
            </div>

            <div style="display:flex;justify-content:flex-end;gap:.6rem">
                <button type="button" onclick="closeRealBalanceModal()" style="padding:.6rem 1.2rem;background:none;border:1px solid #e2e8f0;border-radius:8px;font-size:13px;font-weight:600;color:#64748b;cursor:pointer;transition:all .15s"
                        onmouseover="this.style.background='#f1f5f9'" onmouseout="this.style.background='none'">Cancelar</button>
                <button type="submit" style="padding:.6rem 1.4rem;background:#0f766e;color:#fff;border:none;border-radius:8px;font-size:13px;font-weight:600;cursor:pointer;transition:background .15s;display:inline-flex;align-items:center;gap:6px"
                        onmouseover="this.style.background='#115e59'" onmouseout="this.style.background='#0f766e'">
                    <i class="ti ti-device-floppy" style="font-size:14px"></i> Guardar
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Recharge Modal -->
<div id="recharge-modal" style="display:none;position:fixed;inset:0;background:rgba(15,23,42,.55);z-index:1000;align-items:center;justify-content:center;padding:1rem">
    <div style="background:#fff;border-radius:14px;width:100%;max-width:520px;padding:1.6rem;animation:modalFadeIn .2s ease;box-shadow:0 20px 40px -10px rgba(0,0,0,.25)">
        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:1.3rem">
            <h3 style="font-size:16px;font-weight:700;color:#0f172a;margin:0;display:flex;align-items:center;gap:8px">
                <i class="ti ti-coin" style="color:#10b981"></i> Recargar Balance
            </h3>
            <button type="button" onclick="closeRechargeModal()" style="background:none;border:none;font-size:22px;color:#94a3b8;cursor:pointer;line-height:1;padding:0">&times;</button>
        </div>

        <form id="recharge-form" method="POST" action="{{ route('finance.recharge', $project->id_proyect) }}">
            @csrf
            <div style="margin-bottom:1rem">
                <label style="display:block;font-size:12px;font-weight:600;color:#475569;margin-bottom:5px">Monto a recargar <span style="color:#ef4444">*</span></label>
                <div style="position:relative">
                    <span style="position:absolute;left:.8rem;top:50%;transform:translateY(-50%);color:#94a3b8;font-weight:600">$</span>
                    <input type="number" name="amount" required step="0.01" min="0.01"
                           placeholder="0.00"
                           style="width:100%;padding:.6rem .8rem .6rem 2rem;border:1px solid #e2e8f0;border-radius:8px;font-size:13px;color:#0f172a;outline:none;transition:border-color .15s;box-sizing:border-box"
                           onfocus="this.style.borderColor='#10b981'" onblur="this.style.borderColor='#e2e8f0'">
                </div>
                <div style="display:flex;justify-content:space-between;font-size:12px;color:#64748b;margin-top:4px">
                    <span>Balance actual: <strong>${{ number_format($project->balance->amount ?? 0, 2) }}</strong></span>
                </div>
            </div>

            <div style="display:flex;justify-content:flex-end;gap:.6rem">
                <button type="button" onclick="closeRechargeModal()" style="padding:.6rem 1.2rem;background:none;border:1px solid #e2e8f0;border-radius:8px;font-size:13px;font-weight:600;color:#64748b;cursor:pointer;transition:all .15s"
                        onmouseover="this.style.background='#f1f5f9'" onmouseout="this.style.background='none'">Cancelar</button>
                <button type="submit" style="padding:.6rem 1.4rem;background:#10b981;color:#fff;border:none;border-radius:8px;font-size:13px;font-weight:600;cursor:pointer;transition:background .15s;display:inline-flex;align-items:center;gap:6px"
                        onmouseover="this.style.background='#059669'" onmouseout="this.style.background='#10b981'">
                    <i class="ti ti-device-floppy" style="font-size:14px"></i> Recargar
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Initial Balance Modal -->
<div id="initial-balance-modal" style="display:none;position:fixed;inset:0;background:rgba(15,23,42,.55);z-index:1000;align-items:center;justify-content:center;padding:1rem">
    <div style="background:#fff;border-radius:14px;width:100%;max-width:520px;padding:1.6rem;animation:modalFadeIn .2s ease;box-shadow:0 20px 40px -10px rgba(0,0,0,.25)">
        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:1.3rem">
            <h3 style="font-size:16px;font-weight:700;color:#0f172a;margin:0;display:flex;align-items:center;gap:8px">
                <i class="ti ti-wallet" style="color:#6366f1"></i> Asignar Balance Inicial
            </h3>
            <button type="button" onclick="closeInitialBalanceModal()" style="background:none;border:none;font-size:22px;color:#94a3b8;cursor:pointer;line-height:1;padding:0">&times;</button>
        </div>

        <form id="initial-balance-form" method="POST" action="{{ route('finance.setInitialBalance', $project->id_proyect) }}">
            @csrf
            <div style="margin-bottom:1rem">
                <label style="display:block;font-size:12px;font-weight:600;color:#475569;margin-bottom:5px">Monto inicial <span style="color:#ef4444">*</span></label>
                <div style="position:relative">
                    <span style="position:absolute;left:.8rem;top:50%;transform:translateY(-50%);color:#94a3b8;font-weight:600">$</span>
                    <input type="number" name="amount" required step="0.01" min="0.01"
                           placeholder="0.00"
                           style="width:100%;padding:.6rem .8rem .6rem 2rem;border:1px solid #e2e8f0;border-radius:8px;font-size:13px;color:#0f172a;outline:none;transition:border-color .15s;box-sizing:border-box"
                           onfocus="this.style.borderColor='#6366f1'" onblur="this.style.borderColor='#e2e8f0'">
                </div>
            </div>

            <div style="display:flex;justify-content:flex-end;gap:.6rem">
                <button type="button" onclick="closeInitialBalanceModal()" style="padding:.6rem 1.2rem;background:none;border:1px solid #e2e8f0;border-radius:8px;font-size:13px;font-weight:600;color:#64748b;cursor:pointer;transition:all .15s"
                        onmouseover="this.style.background='#f1f5f9'" onmouseout="this.style.background='none'">Cancelar</button>
                <button type="submit" style="padding:.6rem 1.4rem;background:#6366f1;color:#fff;border:none;border-radius:8px;font-size:13px;font-weight:600;cursor:pointer;transition:background .15s;display:inline-flex;align-items:center;gap:6px"
                        onmouseover="this.style.background='#4f46e5'" onmouseout="this.style.background='#6366f1'">
                    <i class="ti ti-device-floppy" style="font-size:14px"></i> Asignar
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Other Expense Modal -->
<div id="other-expense-modal" style="display:none;position:fixed;inset:0;background:rgba(15,23,42,.55);z-index:1000;align-items:center;justify-content:center;padding:1rem">
    <div style="background:#fff;border-radius:14px;width:100%;max-width:520px;padding:1.6rem;animation:modalFadeIn .2s ease;box-shadow:0 20px 40px -10px rgba(0,0,0,.25)">
        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:1.3rem">
            <h3 style="font-size:16px;font-weight:700;color: #0f172a;margin:0;display:flex;align-items:center;gap:8px">
                <i class="ti ti-category" style="color: #1e1e29"></i> Gasto de Gestión Operativa
            </h3>
            <button type="button" onclick="closeOtherExpenseModal()" style="background:none;border:none;font-size:22px;color:#94a3b8;cursor:pointer;line-height:1;padding:0">&times;</button>
        </div>

        <form id="other-expense-form" method="POST" action="{{ route('finance.registerExpense', $project->id_proyect) }}">
            @csrf
            <input type="hidden" name="name_people" value="Gestión Operativa">
            
            <div style="margin-bottom:1.2rem">
                <label style="display:block;font-size:12px;font-weight:600;color:#475569;margin-bottom:6px">Tipo de Gasto <span style="color:#ef4444">*</span></label>
                <select name="reservation_code" required
                        style="width:100%;padding:.6rem .8rem;border:1px solid #e2e8f0;border-radius:8px;font-size:13px;color:#0f172a;outline:none;transition:border-color .15s;background:#fff"
                        onfocus="this.style.borderColor='#253c32'" onblur="this.style.borderColor='#e2e8f0'">
                    <option value="">Selecciona una opción...</option>
                    <option value="ITF">ITF</option>
                    <option value="Mant. de cta">Mantenimiento de Cuenta</option>
                    <option value="ENVIO DE EE.CC">Envio de EE.CC</option>
                    <option value="OTRO">Otro gasto de gestión operativa</option>
                </select>
            </div>

            <div style="margin-bottom:1rem">
                <label style="display:block;font-size:12px;font-weight:600;color:#475569;margin-bottom:5px">Fecha <span style="color:#ef4444">*</span></label>
                <input type="date" name="expense_date" required
                       style="width:100%;padding:.6rem .8rem;border:1px solid #e2e8f0;border-radius:8px;font-size:13px;color:#0f172a;outline:none;transition:border-color .15s"
                       onfocus="this.style.borderColor='#1e4632'" onblur="this.style.borderColor='#e2e8f0'">
            </div>

            <div style="margin-bottom:1rem">
                <label style="display:block;font-size:12px;font-weight:600;color:#475569;margin-bottom:5px">Monto <span style="color:#ef4444">*</span></label>
                <div style="position:relative">
                    <span style="position:absolute;left:.8rem;top:50%;transform:translateY(-50%);color:#94a3b8;font-weight:600">$</span>
                    <input type="number" name="amount" required step="0.01" min="0.01" max="{{ $availableBalance }}"
                           placeholder="0.00"
                           style="width:100%;padding:.6rem .8rem .6rem 2rem;border:1px solid #e2e8f0;border-radius:8px;font-size:13px;color:#0f172a;outline:none;transition:border-color .15s;box-sizing:border-box"
                           onfocus="this.style.borderColor='#b45309'" onblur="this.style.borderColor='#e2e8f0'">
                </div>
                <div style="display:flex;justify-content:space-between;font-size:12px;color:#64748b;margin-top:4px">
                    <span>Balance disponible: <strong>${{ number_format($availableBalance, 2) }}</strong></span>
                </div>
            </div>

            <div style="display:flex;justify-content:flex-end;gap:.6rem">
                <button type="button" onclick="closeOtherExpenseModal()" style="padding:.6rem 1.2rem;background:none;border:1px solid #e2e8f0;border-radius:8px;font-size:13px;font-weight:600;color:#64748b;cursor:pointer;transition:all .15s"
                        onmouseover="this.style.background='#f1f5f9'" onmouseout="this.style.background='none'">Cancelar</button>
                <button type="submit" style="padding:.6rem 1.4rem;background:#1e1e29;color:#fff;border:none;border-radius:8px;font-size:13px;font-weight:600;cursor:pointer;transition:background .15s;display:inline-flex;align-items:center;gap:6px"
                        onmouseover="this.style.background='#1e1e29'" onmouseout="this.style.background='#273e32'">
                    <i class="ti ti-device-floppy" style="font-size:14px"></i> Registrar
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Edit Expense Modal -->
<div id="edit-expense-modal" style="display:none;position:fixed;inset:0;background:rgba(15,23,42,.55);z-index:1000;align-items:center;justify-content:center;padding:1rem">
    <div style="background:#fff;border-radius:14px;width:100%;max-width:520px;padding:1.6rem;animation:modalFadeIn .2s ease;box-shadow:0 20px 40px -10px rgba(0,0,0,.25)">
        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:1.3rem">
            <h3 style="font-size:16px;font-weight:700;color:#0f172a;margin:0;display:flex;align-items:center;gap:8px">
                <i class="ti ti-edit" style="color:#6366f1"></i> Editar Gasto
            </h3>
            <button type="button" onclick="closeEditExpenseModal()" style="background:none;border:none;font-size:22px;color:#94a3b8;cursor:pointer;line-height:1;padding:0">&times;</button>
        </div>

        <input type="hidden" id="edit-expense-id" name="expense_id">
        <form id="edit-expense-form" method="POST" action="">
            @csrf @method('PUT')
            <div style="margin-bottom:1rem">
                <label style="display:block;font-size:12px;font-weight:600;color:#475569;margin-bottom:5px">Fecha de Compra <span style="color:#ef4444">*</span></label>
                <input type="date" id="edit-expense-date" name="expense_date" required
                       style="width:100%;padding:.6rem .8rem;border:1px solid #e2e8f0;border-radius:8px;font-size:13px;color:#0f172a;outline:none;transition:border-color .15s"
                       onfocus="this.style.borderColor='#6366f1'" onblur="this.style.borderColor='#e2e8f0'">
            </div>

            <div style="margin-bottom:1rem;display:none;">
                <input type="hidden" id="edit-name-people" name="name_people">
            </div>

            <div style="margin-bottom:1rem">
                <label style="display:block;font-size:12px;font-weight:600;color:#475569;margin-bottom:5px">Codigo de Reserva <span style="color:#ef4444">*</span></label>
                <input type="text" id="edit-reservation-code" name="reservation_code" required
                       style="width:100%;padding:.6rem .8rem;border:1px solid #e2e8f0;border-radius:8px;font-size:13px;color:#0f172a;outline:none;transition:border-color .15s"
                       onfocus="this.style.borderColor='#6366f1'" onblur="this.style.borderColor='#e2e8f0'">
            </div>

            <div style="margin-bottom:1rem">
                <label style="display:block;font-size:12px;font-weight:600;color:#475569;margin-bottom:5px">Numero de File</label>
                <input type="text" id="edit-file-number" name="file_number"
                       style="width:100%;padding:.6rem .8rem;border:1px solid #e2e8f0;border-radius:8px;font-size:13px;color:#0f172a;outline:none;transition:border-color .15s"
                       onfocus="this.style.borderColor='#6366f1'" onblur="this.style.borderColor='#e2e8f0'">
            </div>

            <div style="margin-bottom:1rem">
                <label style="display:block;font-size:12px;font-weight:600;color:#475569;margin-bottom:5px">Monto del gasto <span style="color:#ef4444">*</span></label>
                <div style="position:relative">
                    <span style="position:absolute;left:.8rem;top:50%;transform:translateY(-50%);color:#94a3b8;font-weight:600">$</span>
                    <input type="number" id="edit-amount" name="amount" required step="0.01" min="0.01"
                           placeholder="0.00"
                           style="width:100%;padding:.6rem .8rem .6rem 2rem;border:1px solid #e2e8f0;border-radius:8px;font-size:13px;color:#0f172a;outline:none;transition:border-color .15s;box-sizing:border-box"
                           onfocus="this.style.borderColor='#6366f1'" onblur="this.style.borderColor='#e2e8f0'">
                </div>
                <div style="display:flex;justify-content:space-between;font-size:12px;color:#64748b;margin-top:4px">
                    <span>Balance disponible: <strong id="edit_available_balance">${{ number_format($availableBalance, 2) }}</strong></span>
                </div>
            </div>

            <div style="display:flex;justify-content:flex-end;gap:.6rem">
                <button type="button" onclick="closeEditExpenseModal()" style="padding:.6rem 1.2rem;background:none;border:1px solid #e2e8f0;border-radius:8px;font-size:13px;font-weight:600;color:#64748b;cursor:pointer;transition:all .15s"
                        onmouseover="this.style.background='#f1f5f9'" onmouseout="this.style.background='none'">Cancelar</button>
                <button type="submit" style="padding:.6rem 1.4rem;background:#6366f1;color:#fff;border:none;border-radius:8px;font-size:13px;font-weight:600;cursor:pointer;transition:background .15s;display:inline-flex;align-items:center;gap:6px"
                        onmouseover="this.style.background='#4f46e5'" onmouseout="this.style.background='#6366f1'">
                    <i class="ti ti-device-floppy" style="font-size:14px"></i> Actualizar Gasto
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Edit Operational Modal -->
<div id="edit-operational-modal" style="display:none;position:fixed;inset:0;background:rgba(15,23,42,.55);z-index:1000;align-items:center;justify-content:center;padding:1rem">
    <div style="background:#fff;border-radius:14px;width:100%;max-width:520px;padding:1.6rem;animation:modalFadeIn .2s ease;box-shadow:0 20px 40px -10px rgba(0,0,0,.25)">
        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:1.3rem">
            <h3 style="font-size:16px;font-weight:700;color:#0f172a;margin:0;display:flex;align-items:center;gap:8px">
                <i class="ti ti-edit" style="color:#6366f1"></i> Editar Gasto Operativo
            </h3>
            <button type="button" onclick="closeEditOperationalModal()" style="background:none;border:none;font-size:22px;color:#94a3b8;cursor:pointer;line-height:1;padding:0">&times;</button>
        </div>

        <input type="hidden" id="edit-operational-id" name="expense_id">
        <form id="edit-operational-form" method="POST" action="">
            @csrf @method('PUT')
            <div style="margin-bottom:1rem">
                <label style="display:block;font-size:12px;font-weight:600;color:#475569;margin-bottom:5px">Fecha <span style="color:#ef4444">*</span></label>
                <input type="date" id="edit-operational-date" name="expense_date" required
                       style="width:100%;padding:.6rem .8rem;border:1px solid #e2e8f0;border-radius:8px;font-size:13px;color:#0f172a;outline:none;transition:border-color .15s"
                       onfocus="this.style.borderColor='#6366f1'" onblur="this.style.borderColor='#e2e8f0'">
            </div>

            <div style="margin-bottom:1rem; display:none;">
                <input type="hidden" id="edit-operational-name" name="name_people" value="Gestión Operativa">
            </div>

            <div style="margin-bottom:1rem">
                <label style="display:block;font-size:12px;font-weight:600;color:#475569;margin-bottom:5px">Tipo de Gasto <span style="color:#ef4444">*</span></label>
                <select id="edit-operational-type" name="reservation_code"
                        style="width:100%;padding:.6rem .8rem;border:1px solid #e2e8f0;border-radius:8px;font-size:13px;color:#0f172a;outline:none;transition:border-color .15s;background:#fff"
                        onfocus="this.style.borderColor='#6366f1'" onblur="this.style.borderColor='#e2e8f0'">
                    <option value="ITF">ITF</option>
                    <option value="Mant. de cta">Mantenimiento de Cuenta</option>
                    <option value="ENVIO DE EE.CC">Envio de EE.CC</option>
                    <option value="OTRO">Otro gasto de gestión operativa</option>
                </select>
            </div>

            <div style="margin-bottom:1rem">
                <label style="display:block;font-size:12px;font-weight:600;color:#475569;margin-bottom:5px">Monto <span style="color:#ef4444">*</span></label>
                <div style="position:relative">
                    <span style="position:absolute;left:.8rem;top:50%;transform:translateY(-50%);color:#94a3b8;font-weight:600">$</span>
                    <input type="number" id="edit-operational-amount" name="amount" required step="0.01" min="0.01"
                           placeholder="0.00"
                           style="width:100%;padding:.6rem .8rem .6rem 2rem;border:1px solid #e2e8f0;border-radius:8px;font-size:13px;color:#0f172a;outline:none;transition:border-color .15s;box-sizing:border-box"
                           onfocus="this.style.borderColor='#6366f1'" onblur="this.style.borderColor='#e2e8f0'">
                </div>
                <div style="display:flex;justify-content:space-between;font-size:12px;color:#64748b;margin-top:4px">
                    <span>Balance disponible: <strong id="edit-operational-balance">${{ number_format($availableBalance, 2) }}</strong></span>
                </div>
            </div>

            <div style="display:flex;justify-content:flex-end;gap:.6rem">
                <button type="button" onclick="closeEditOperationalModal()" style="padding:.6rem 1.2rem;background:none;border:1px solid #e2e8f0;border-radius:8px;font-size:13px;font-weight:600;color:#64748b;cursor:pointer;transition:all .15s"
                        onmouseover="this.style.background='#f1f5f9'" onmouseout="this.style.background='none'">Cancelar</button>
                <button type="submit" style="padding:.6rem 1.4rem;background:#6366f1;color:#fff;border:none;border-radius:8px;font-size:13px;font-weight:600;cursor:pointer;transition:background .15s;display:inline-flex;align-items:center;gap:6px"
                        onmouseover="this.style.background='#4f46e5'" onmouseout="this.style.background='#6366f1'">
                    <i class="ti ti-device-floppy" style="font-size:14px"></i> Actualizar
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Edit Recharge Modal -->
<div id="edit-recharge-modal" style="display:none;position:fixed;inset:0;background:rgba(15,23,42,.55);z-index:1000;align-items:center;justify-content:center;padding:1rem">
    <div style="background:#fff;border-radius:14px;width:100%;max-width:520px;padding:1.6rem;animation:modalFadeIn .2s ease;box-shadow:0 20px 40px -10px rgba(0,0,0,.25)">
        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:1.3rem">
            <h3 style="font-size:16px;font-weight:700;color:#0f172a;margin:0;display:flex;align-items:center;gap:8px">
                <i class="ti ti-edit" style="color:#10b981"></i> Editar Recarga
            </h3>
            <button type="button" onclick="closeEditRechargeModal()" style="background:none;border:none;font-size:22px;color:#94a3b8;cursor:pointer;line-height:1;padding:0">&times;</button>
        </div>

        <input type="hidden" id="edit-recharge-id" name="recharge_id">
        <form id="edit-recharge-form" method="POST" action="">
            @csrf @method('PUT')
            <div style="margin-bottom:1rem">
                <label style="display:block;font-size:12px;font-weight:600;color:#475569;margin-bottom:5px">Fecha de Recarga <span style="color:#ef4444">*</span></label>
                <input type="date" id="edit-recharge-date" name="recharge_date" required
                       style="width:100%;padding:.6rem .8rem;border:1px solid #e2e8f0;border-radius:8px;font-size:13px;color:#0f172a;outline:none;transition:border-color .15s"
                       onfocus="this.style.borderColor='#10b981'" onblur="this.style.borderColor='#e2e8f0'">
            </div>

            <div style="margin-bottom:1rem">
                <label style="display:block;font-size:12px;font-weight:600;color:#475569;margin-bottom:5px">Monto a recargar <span style="color:#ef4444">*</span></label>
                <div style="position:relative">
                    <span style="position:absolute;left:.8rem;top:50%;transform:translateY(-50%);color:#94a3b8;font-weight:600">$</span>
                    <input type="number" id="edit-recharge-amount" name="amount" required step="0.01" min="0.01"
                           placeholder="0.00"
                           style="width:100%;padding:.6rem .8rem .6rem 2rem;border:1px solid #e2e8f0;border-radius:8px;font-size:13px;color:#0f172a;outline:none;transition:border-color .15s;box-sizing:border-box"
                           onfocus="this.style.borderColor='#10b981'" onblur="this.style.borderColor='#e2e8f0'">
                </div>
            </div>

            <div style="display:flex;justify-content:flex-end;gap:.6rem">
                <button type="button" onclick="closeEditRechargeModal()" style="padding:.6rem 1.2rem;background:none;border:1px solid #e2e8f0;border-radius:8px;font-size:13px;font-weight:600;color:#64748b;cursor:pointer;transition:all .15s"
                        onmouseover="this.style.background='#f1f5f9'" onmouseout="this.style.background='none'">Cancelar</button>
                <button type="submit" style="padding:.6rem 1.4rem;background:#10b981;color:#fff;border:none;border-radius:8px;font-size:13px;font-weight:600;cursor:pointer;transition:background .15s;display:inline-flex;align-items:center;gap:6px"
                        onmouseover="this.style.background='#059669'" onmouseout="this.style.background='#10b981'">
                    <i class="ti ti-device-floppy" style="font-size:14px"></i> Actualizar Recarga
                </button>
            </div>
        </form>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    const PROJECT_ID = {{ json_encode($project->id_proyect) }};

    function switchTab(tabName, btn) {
        document.querySelectorAll('.tab-content').forEach(tab => tab.classList.remove('active'));
        document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
        document.getElementById('tab-' + tabName).classList.add('active');
        btn.classList.add('active');
    }

    // ── MODALES ──
    function openExpenseModal() {
        @if(!$project->balance || $project->balance->amount <= 0)
            Swal.fire({ icon: 'error', title: 'Sin balance asignado', text: 'Debes asignar un presupuesto primero.', confirmButtonColor: '#ef4444' });
            return;
        @endif
        @if($availableBalance <= 0)
            Swal.fire({ icon: 'error', title: 'Balance agotado', text: 'No hay plata', confirmButtonColor: '#ef4444' });
            return;
        @endif
        document.getElementById('expense-modal').style.display = 'flex';
    }
    function closeExpenseModal() { document.getElementById('expense-modal').style.display = 'none'; }

    function openRealBalanceModal() { document.getElementById('real-balance-modal').style.display = 'flex'; }
    function closeRealBalanceModal() { document.getElementById('real-balance-modal').style.display = 'none'; }

    function openRechargeModal() { document.getElementById('recharge-modal').style.display = 'flex'; }
    function closeRechargeModal() { document.getElementById('recharge-modal').style.display = 'none'; }

    function openSetInitialBalanceModal() { document.getElementById('initial-balance-modal').style.display = 'flex'; }
    function closeInitialBalanceModal() { document.getElementById('initial-balance-modal').style.display = 'none'; }

    function openOtherExpenseModal() {
        @if(!$project->balance || $project->balance->amount <= 0)
            Swal.fire({ icon: 'error', title: 'Sin balance asignado', text: 'Debes asignar un presupuesto primero.', confirmButtonColor: '#ef4444' });
            return;
        @endif
        @if($availableBalance <= 0)
            Swal.fire({ icon: 'error', title: 'Balance agotado', text: 'No hay plata', confirmButtonColor: '#ef4444' });
            return;
        @endif
        document.getElementById('other-expense-modal').style.display = 'flex';
    }
    function closeOtherExpenseModal() { document.getElementById('other-expense-modal').style.display = 'none'; }

    function closeEditExpenseModal() { document.getElementById('edit-expense-modal').style.display = 'none'; }
    function closeEditOperationalModal() { document.getElementById('edit-operational-modal').style.display = 'none'; }
    function closeEditRechargeModal() { document.getElementById('edit-recharge-modal').style.display = 'none'; }

    // ── EDIT FUNCTIONS ──
    function openEditExpenseModal(projectId, expenseId) {
        if (!projectId || !expenseId) return;
        fetch(`/finance/${projectId}/expense/${expenseId}/edit`)
            .then(r => r.json())
            .then(data => {
                const e = data.expense;
                document.getElementById('edit-expense-date').value = e.expense_date ? new Date(e.expense_date).toISOString().split('T')[0] : '';
                document.getElementById('edit-name-people').value = e.name_people || '';
                document.getElementById('edit-reservation-code').value = e.reservation_code || '';
                document.getElementById('edit-file-number').value = e.file_number || '';
                document.getElementById('edit-amount').value = e.amount || '';
                document.getElementById('edit-expense-id').value = expenseId;
                document.getElementById('edit_available_balance').textContent = '$ ' + (data.balance ? parseFloat(data.balance).toFixed(2) : '0.00');
                document.getElementById('edit-expense-form').action = `/finance/${projectId}/expense/${expenseId}`;
                document.getElementById('edit-expense-modal').style.display = 'flex';
            })
            .catch(() => Swal.fire({ icon: 'error', title: 'Error', text: 'Error al cargar los datos del gasto.', confirmButtonColor: '#ef4444' }));
    }

    function openEditOperationalModal(projectId, expenseId) {
        if (!projectId || !expenseId) return;
        fetch(`/finance/${projectId}/expense/${expenseId}/edit`)
            .then(r => r.json())
            .then(data => {
                const e = data.expense;
                document.getElementById('edit-operational-date').value = e.expense_date ? new Date(e.expense_date).toISOString().split('T')[0] : '';
                document.getElementById('edit-operational-name').value = e.name_people || 'Gestión Operativa';
                document.getElementById('edit-operational-type').value = e.reservation_code || 'OTRO';
                document.getElementById('edit-operational-amount').value = e.amount || '';
                document.getElementById('edit-operational-id').value = expenseId;
                document.getElementById('edit-operational-balance').textContent = '$ ' + (data.balance ? parseFloat(data.balance).toFixed(2) : '0.00');
                document.getElementById('edit-operational-form').action = `/finance/${projectId}/expense/${expenseId}`;
                document.getElementById('edit-operational-modal').style.display = 'flex';
            })
            .catch(() => Swal.fire({ icon: 'error', title: 'Error', text: 'Error al cargar los datos del gasto operativo.', confirmButtonColor: '#ef4444' }));
    }

    function openEditRechargeModal(projectId, rechargeId) {
        if (!projectId || !rechargeId) return;
        fetch(`/finance/${projectId}/recharge/${rechargeId}/edit`)
            .then(r => r.json())
            .then(data => {
                const r = data.recharge;
                document.getElementById('edit-recharge-date').value = r.recharge_date ? new Date(r.recharge_date).toISOString().split('T')[0] : '';
                document.getElementById('edit-recharge-amount').value = r.amount || '';
                document.getElementById('edit-recharge-id').value = rechargeId;
                document.getElementById('edit-recharge-form').action = `/finance/${projectId}/recharge/${rechargeId}`;
                document.getElementById('edit-recharge-modal').style.display = 'flex';
            })
            .catch(() => Swal.fire({ icon: 'error', title: 'Error', text: 'Error al cargar los datos de la recarga.', confirmButtonColor: '#ef4444' }));
    }

    // ── DELETE FUNCTIONS ──
    function deleteExpense(projectId, expenseId) {
        if (!projectId || !expenseId) return;
        Swal.fire({
            title: '¿Estás seguro?',
            text: 'Esta acción eliminará el gasto y devolverá el monto al balance.',
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
                form.action = `/finance/${projectId}/expense/${expenseId}`;
                form.innerHTML = `<input type="hidden" name="_token" value="{{ csrf_token() }}"><input type="hidden" name="_method" value="DELETE">`;
                document.body.appendChild(form);
                form.submit();
            }
        });
    }

    function deleteRecharge(projectId, rechargeId) {
        if (!projectId || !rechargeId) return;
        Swal.fire({
            title: '¿Estás seguro?',
            text: 'Esta acción eliminará la recarga y restará el monto del balance actual.',
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
                form.action = `/finance/${projectId}/recharge/${rechargeId}`;
                form.innerHTML = `<input type="hidden" name="_token" value="{{ csrf_token() }}"><input type="hidden" name="_method" value="DELETE">`;
                document.body.appendChild(form);
                form.submit();
            }
        });
    }

    // ── CERRAR MODALES CON CLICK FUERA Y ESC ──
    document.querySelectorAll('#expense-modal, #recharge-modal, #initial-balance-modal, #other-expense-modal, #edit-expense-modal, #edit-operational-modal, #edit-recharge-modal')
        .forEach(modal => {
            modal.addEventListener('click', function(e) { if (e.target === this) this.style.display = 'none'; });
        });

    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            document.querySelectorAll('[id$="-modal"]').forEach(m => m.style.display = 'none');
        }
    });

    // ── SPINNER EN BOTONES ──
    document.querySelectorAll('form').forEach(form => {
        form.addEventListener('submit', function() {
            const btn = this.querySelector('button[type="submit"]');
            if (btn) {
                btn.dataset.original = btn.innerHTML;
                btn.disabled = true;
                btn.innerHTML = '<i class="ti ti-loader" style="font-size:14px; animation: spin 1s linear infinite;"></i> Procesando...';
                btn.style.opacity = '0.7';
            }
        });
    });
</script>
@endsection