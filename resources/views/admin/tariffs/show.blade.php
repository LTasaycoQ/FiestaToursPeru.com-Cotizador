@extends('layouts.app')

@section('title', 'Editar Tarifa - ' . $service->name_service)

@section('content')
<style>
    .edit-tariff-wrapper {
        padding: 1.8rem 2rem;
        width: 100%;
    }

    .edit-tariff-wrapper .back-link {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        color: #475569;
        text-decoration: none;
        font-size: 13px;
    }

    .edit-tariff-wrapper .back-link:hover {
        color: #0f172a;
    }

    .edit-tariff-wrapper .card {
        background: #fff;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        overflow: hidden;
        margin-bottom: 1.5rem;
    }

    .edit-tariff-wrapper .card-header {
        padding: 1rem 1.5rem;
        border-bottom: 1px solid #e2e8f0;
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 0.5rem;
    }

    .edit-tariff-wrapper .card-header h3 {
        font-size: 16px;
        font-weight: 600;
        color: #0f172a;
        margin: 0;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .edit-tariff-wrapper .card-header h3 i {
        color: #64748b;
    }

    .edit-tariff-wrapper .card-body {
        padding: 1.5rem;
    }

    .edit-tariff-wrapper .info-box {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        gap: 12px;
        padding: 12px 16px;
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        margin-bottom: 1.5rem;
    }

    .edit-tariff-wrapper .info-box .label {
        font-size: 11px;
        font-weight: 600;
        color: #64748b;
        text-transform: uppercase;
        letter-spacing: 0.4px;
    }

    .edit-tariff-wrapper .info-box .value {
        font-size: 15px;
        font-weight: 600;
        color: #0f172a;
    }

    .edit-tariff-wrapper .info-box .badge {
        padding: 3px 12px;
        border-radius: 20px;
        font-size: 11px;
        font-weight: 500;
        background: #e0f2fe;
        color: #0369a1;
    }

    .edit-tariff-wrapper .info-box .badge-pending {
        background: #fef3c7;
        color: #92400e;
        padding: 3px 10px;
        border-radius: 20px;
        font-size: 11px;
        font-weight: 600;
    }

    .edit-tariff-wrapper .info-box .badge-status {
        padding: 3px 10px;
        border-radius: 20px;
        font-size: 11px;
        font-weight: 600;
    }

    .edit-tariff-wrapper .info-box .badge-status.active {
        background: #dcfce7;
        color: #166534;
    }

    .edit-tariff-wrapper .info-box .badge-status.inactive {
        background: #f1f5f9;
        color: #64748b;
    }

    .edit-tariff-wrapper .info-box .badge-level {
        background: #ede9fe;
        color: #6d28d9;
        padding: 3px 10px;
        border-radius: 6px;
        font-size: 12px;
        font-weight: 600;
    }

    .edit-tariff-wrapper .info-box .badge-level.flat-badge {
        background: #dbeafe;
        color: #1e40af;
    }

    .edit-tariff-wrapper .form-group {
        margin-bottom: 1rem;
    }

    .edit-tariff-wrapper .form-group label {
        display: block;
        font-size: 11px;
        font-weight: 600;
        color: #475569;
        text-transform: uppercase;
        letter-spacing: 0.4px;
        margin-bottom: 4px;
    }

    .edit-tariff-wrapper .form-group label .required {
        color: #991b1b;
    }

    .edit-tariff-wrapper .form-group input,
    .edit-tariff-wrapper .form-group select {
        width: 100%;
        padding: 9px 12px;
        border: 1.5px solid #e2e8f0;
        border-radius: 8px;
        font-size: 13px;
        outline: none;
        background: #fff;
        transition: border-color 0.15s;
    }

    .edit-tariff-wrapper .form-group input:focus,
    .edit-tariff-wrapper .form-group select:focus {
        border-color: #0f172a;
    }

    .edit-tariff-wrapper .form-group input[disabled] {
        background: #f1f5f9;
        color: #94a3b8;
        cursor: not-allowed;
    }

    .edit-tariff-wrapper .form-row {
        display: grid;
        grid-template-columns: 1fr 1fr 1fr;
        gap: 0.8rem;
        align-items: end;
    }

    .edit-tariff-wrapper .form-row .form-group {
        margin-bottom: 0;
    }

    .edit-tariff-wrapper .form-row .form-group label {
        font-size: 10px;
        color: #64748b;
        text-transform: uppercase;
        letter-spacing: 0.4px;
    }

    .edit-tariff-wrapper .form-actions {
        display: flex;
        gap: 8px;
        margin-top: 1.5rem;
    }

    .edit-tariff-wrapper .form-actions .btn-submit {
        flex: 1;
        padding: 10px;
        background: #0f172a;
        color: #fff;
        border: none;
        border-radius: 8px;
        font-size: 14px;
        font-weight: 500;
        cursor: pointer;
        transition: all 0.15s;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 6px;
    }

    .edit-tariff-wrapper .form-actions .btn-submit:hover {
        background: #1e293b;
    }

    .edit-tariff-wrapper .form-actions .btn-submit:disabled {
        opacity: 0.6;
        cursor: not-allowed;
    }

    .edit-tariff-wrapper .form-actions .btn-cancel {
        padding: 10px 20px;
        background: #f1f5f9;
        color: #475569;
        border: none;
        border-radius: 8px;
        font-size: 14px;
        font-weight: 500;
        cursor: pointer;
        transition: all 0.15s;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        justify-content: center;
    }

    .edit-tariff-wrapper .form-actions .btn-cancel:hover {
        background: #e2e8f0;
    }

    .edit-tariff-wrapper .form-actions .btn-add-row {
        padding: 10px 16px;
        background: #f1f5f9;
        color: #475569;
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        font-size: 13px;
        font-weight: 500;
        cursor: pointer;
        transition: all 0.15s;
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }

    .edit-tariff-wrapper .form-actions .btn-add-row:hover {
        background: #e2e8f0;
    }

    .edit-tariff-wrapper .btn-add-season {
        padding: 8px 20px;
        background: #0f172a;
        color: #fff;
        border: none;
        border-radius: 8px;
        font-size: 13px;
        font-weight: 500;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        transition: all 0.15s;
    }

    .edit-tariff-wrapper .btn-add-season:hover {
        background: #1e293b;
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(15, 23, 42, 0.15);
    }

    .alert-success {
        padding: 12px 16px;
        border-radius: 8px;
        margin-bottom: 1rem;
        background: #dcfce7;
        color: #166534;
        border: 1px solid #86efac;
        font-size: 13px;
    }

    .alert-error {
        padding: 12px 16px;
        border-radius: 8px;
        margin-bottom: 1rem;
        background: #fee2e2;
        color: #991b1b;
        border: 1px solid #fca5a5;
        font-size: 13px;
    }

    .price-input-wrap {
        display: flex;
        align-items: stretch;
        width: 100%;
        border: 1.5px solid #e2e8f0;
        border-radius: 10px;
        overflow: hidden;
        background: #fff;
        transition: border-color 0.15s;
    }

    .price-input-wrap:focus-within {
        border-color: #0f172a;
    }

    .price-input-wrap input {
        border: none !important;
        border-radius: 0 !important;
        flex: 1;
        min-width: 0;
    }

    .price-input-wrap input:focus {
        border: none !important;
    }

    .price-input-wrap .currency-symbol {
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 0 14px;
        background: #f1f5f9;
        color: #475569;
        font-size: 12px;
        font-weight: 700;
        pointer-events: none;
        border-left: 1px solid #e2e8f0;
    }

    .badge-pending {
        background: #fef3c7;
        color: #92400e;
        padding: 3px 10px;
        border-radius: 20px;
        font-size: 11px;
        font-weight: 600;
    }

    .range-row {
        display: grid;
        grid-template-columns: 1fr 1fr 1fr auto;
        gap: 0.8rem;
        align-items: end;
        padding: 10px 12px;
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        margin-bottom: 0.8rem;
    }

    .range-row .form-group {
        margin-bottom: 0;
    }

    .range-row .form-group label {
        font-size: 10px;
        color: #64748b;
        text-transform: uppercase;
        letter-spacing: 0.4px;
        display: block;
        margin-bottom: 4px;
    }

    .range-row .btn-remove-row {
        padding: 6px 10px;
        background: #fee2e2;
        color: #991b1b;
        border: 1px solid #fecaca;
        border-radius: 6px;
        cursor: pointer;
        font-size: 16px;
        transition: all 0.15s;
        margin-bottom: 2px;
        height: 38px;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .range-row .btn-remove-row:hover {
        background: #fecaca;
    }

    .range-row .price-input-wrap {
        width: 100%;
    }

    .range-row .price-input-wrap .currency-symbol {
        right: 8px;
        font-size: 11px;
    }

    .ranges-container {
        margin-bottom: 1rem;
    }

    .empty-ranges {
        text-align: center;
        padding: 1.5rem;
        color: #94a3b8;
        border: 1px dashed #e2e8f0;
        border-radius: 8px;
        font-size: 13px;
    }

    .empty-ranges i {
        font-size: 24px;
        display: block;
        margin-bottom: 8px;
        color: #cbd5e1;
    }

    .current-ranges-title {
        font-size: 13px;
        font-weight: 600;
        color: #0f172a;
        margin-bottom: 0.8rem;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .current-ranges-title .count {
        font-weight: 400;
        color: #94a3b8;
        font-size: 12px;
    }

    .current-ranges-title .btn-add-level {
        margin-left: auto;
        width: 24px;
        height: 24px;
        border-radius: 50%;
        border: 1.5px solid #0f172a;
        background: #fff;
        color: #0f172a;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: all 0.15s;
        padding: 0;
        font-size: 14px;
    }

    .current-ranges-title .btn-add-level:hover {
        background: #0f172a;
        color: #fff;
        transform: rotate(90deg);
    }

    /* ===== TABLA DE NIVELES (rangos) ===== */
    .ranges-container {
        border: 1px solid #e2e8f0;
        border-radius: 10px;
        overflow: hidden;
        background: #fff;
    }

    .ranges-table-head {
        display: grid;
        grid-template-columns: 1fr 1fr 1fr 34px;
        gap: 0.8rem;
        padding: 8px 12px;
        background: #f8fafc;
        border-bottom: 1px solid #e2e8f0;
        font-size: 10px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.4px;
        color: #64748b;
    }

    .range-row {
        display: grid;
        grid-template-columns: 1fr 1fr 1fr 34px;
        gap: 0.8rem;
        align-items: center;
        padding: 8px 12px;
        background: #fff;
        border: none;
        border-bottom: 1px solid #f1f5f9;
        border-radius: 0;
        margin-bottom: 0;
    }

    .range-row:last-child {
        border-bottom: none;
    }

    .range-row .form-group label {
        display: none;
    }

    .range-row input {
        border: 1.5px solid transparent;
        background: #f8fafc;
        border-radius: 6px;
        padding: 7px 10px;
        font-size: 13px;
    }

    .range-row input:focus {
        border-color: #0f172a;
        background: #fff;
    }

    .range-row .price-input-wrap {
        border-radius: 6px;
    }

    .range-row .price-input-wrap input {
        background: #f8fafc;
    }

    .range-row .price-input-wrap:focus-within {
        background: #fff;
    }

    .range-row .price-input-wrap .currency-symbol {
        padding: 0 8px;
        font-size: 11px;
    }

    .range-row .btn-remove-row {
        height: 32px;
        width: 32px;
        padding: 0;
        margin-bottom: 0;
    }

    /* ===== TOGGLE ¿CON TARIFAS DE TEMPORADA? ===== */
    .season-toggle-row {
        display: flex;
        align-items: center;
        gap: 10px;
        cursor: pointer;
        user-select: none;
        margin-bottom: 1rem;
    }

    .season-toggle-row input[type="checkbox"] {
        width: 16px;
        height: 16px;
        accent-color: #0f172a;
        cursor: pointer;
    }

    .season-toggle-row span {
        font-size: 13px;
        font-weight: 600;
        color: #0f172a;
    }

    .toggle-check-indicator {
        margin-left: auto;
        width: 20px;
        height: 20px;
        border-radius: 50%;
        background: #22c55e;
        color: #fff;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 12px;
        line-height: 1;
    }

    .season-toggle-content {
        max-height: 3000px;
        opacity: 1;
        overflow: hidden;
        transition: max-height 0.3s ease, opacity 0.25s ease, margin 0.3s ease;
    }

    .season-toggle-content.is-collapsed {
        max-height: 0;
        opacity: 0;
        margin: 0;
        pointer-events: none;
    }

    /* ===== TEMPORADAS ===== */
    .seasonal-container {
        margin-top: 1.5rem;
        padding-top: 1.5rem;
        border-top: 2px solid #e2e8f0;
    }

    .seasonal-container .seasonal-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 1rem;
        margin-bottom: 1rem;
    }

    .seasonal-container .seasonal-title {
        font-size: 14px;
        font-weight: 600;
        color: #0f172a;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .seasonal-container .seasonal-title i {
        color: #0ea5e9;
    }

    .seasonal-container .seasonal-title .badge-seasonal {
        font-size: 11px;
        font-weight: 500;
        color: #0ea5e9;
        background: #e0f2fe;
        padding: 2px 12px;
        border-radius: 12px;
    }

    .season-card {
        background: #fff;
        border: 1px solid #e2e8f0;
        border-radius: 10px;
        margin-bottom: 1rem;
        overflow: hidden;
        transition: opacity 0.2s ease, transform 0.2s ease;
    }

    .season-card.row-removing {
        opacity: 0;
        transform: translateX(8px);
    }

    .season-card .season-header {
        padding: 10px 14px;
        background: #f8fafc;
        border-bottom: 1px solid #e2e8f0;
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 8px;
        cursor: pointer;
    }

    .season-card.is-collapsed .season-header {
        border-bottom: none;
    }

    .btn-season-toggle {
        width: 26px;
        height: 26px;
        border-radius: 6px;
        border: 1px solid #e2e8f0;
        background: #fff;
        color: #64748b;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: all 0.15s;
        flex-shrink: 0;
    }

    .btn-season-toggle:hover {
        background: #f1f5f9;
        color: #0f172a;
    }

    .btn-season-toggle i {
        transition: transform 0.2s ease;
    }

    .season-card.is-collapsed .btn-season-toggle i {
        transform: rotate(-90deg);
    }

    .season-body {
        max-height: 2000px;
        opacity: 1;
        overflow: hidden;
        transition: max-height 0.25s ease, opacity 0.2s ease, padding 0.25s ease;
    }

    .season-card.is-collapsed .season-body {
        max-height: 0;
        opacity: 0;
        padding-top: 0;
        padding-bottom: 0;
        pointer-events: none;
    }

    .season-card .season-header .season-name {
        font-size: 13px;
        font-weight: 600;
        color: #0f172a;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .season-card .season-header .season-name i {
        color: #0ea5e9;
    }

    .season-card .season-header .season-dates {
        font-size: 12px;
        color: #94a3b8;
    }

    .season-card .season-body {
        padding: 12px 14px;
    }

    .season-price-form {
        margin-top: 0.8rem;
        padding: 1rem;
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 8px;
    }

    .season-price-form .season-price-title {
        font-size: 12px;
        font-weight: 600;
        color: #0f172a;
        margin-bottom: 0.8rem;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .season-price-form .season-price-title .badge-rangos {
        font-size: 10px;
        font-weight: 500;
        color: #475569;
        background: #f1f5f9;
        padding: 2px 10px;
        border-radius: 12px;
    }

    .season-price-row {
        display: grid;
        grid-template-columns: 1fr 1fr 1.3fr;
        gap: 0.8rem;
        align-items: center;
        padding: 6px 10px;
        background: #fff;
        border: 1px solid #e2e8f0;
        border-radius: 6px;
        margin-bottom: 0.5rem;
    }

    .season-price-row .form-group {
        margin-bottom: 0;
    }

    .season-price-row .form-group label {
        font-size: 9px;
        color: #64748b;
        text-transform: uppercase;
        letter-spacing: 0.4px;
        display: block;
        margin-bottom: 2px;
    }

    .season-price-row .form-group input {
        padding: 4px 8px;
        font-size: 12px;
        width: 100%;
        border: 1.5px solid #e2e8f0;
        border-radius: 6px;
        outline: none;
    }

    .season-price-row .form-group input:focus {
        border-color: #0f172a;
    }

    .season-price-row .form-group input[disabled] {
        background: #f1f5f9;
        color: #94a3b8;
    }

    .season-price-row .rango-label {
        font-size: 12px;
        font-weight: 500;
        color: #0f172a;
        background: #f1f5f9;
        padding: 4px 10px;
        border-radius: 4px;
        text-align: center;
    }

    .season-price-row .price-input-wrap {
        border-radius: 6px;
    }

    .season-price-row .price-input-wrap input {
        padding: 4px 8px;
        font-size: 12px;
    }

    .season-price-row .price-input-wrap .currency-symbol {
        padding: 0 8px;
        font-size: 11px;
    }

    .btn-season-delete {
        padding: 4px 10px;
        background: #fee2e2;
        color: #991b1b;
        border: 1px solid #fecaca;
        border-radius: 4px;
        font-size: 12px;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 4px;
        transition: all 0.15s;
    }

    .btn-season-delete:hover {
        background: #fecaca;
    }

    .btn-save-season-prices {
        padding: 8px 20px;
        background: #0f172a;
        color: #fff;
        border: none;
        border-radius: 6px;
        font-size: 13px;
        font-weight: 500;
        cursor: pointer;
        transition: all 0.15s;
        margin-top: 0.5rem;
    }

    .btn-save-season-prices:hover {
        background: #1e293b;
    }

    .btn-save-season-prices:disabled {
        opacity: 0.6;
        cursor: not-allowed;
    }

    .btn-cancel-season-prices {
        padding: 8px 20px;
        background: #f1f5f9;
        color: #475569;
        border: 1px solid #e2e8f0;
        border-radius: 6px;
        font-size: 13px;
        font-weight: 500;
        cursor: pointer;
        transition: all 0.15s;
        margin-top: 0.5rem;
    }

    .btn-cancel-season-prices:hover {
        background: #e2e8f0;
    }

    .no-season-ranges {
        text-align: center;
        padding: 0.8rem;
        color: #94a3b8;
        font-size: 12px;
        border: 1px dashed #e2e8f0;
        border-radius: 6px;
    }

    .season-empty-state {
        text-align: center;
        padding: 1.5rem;
        color: #94a3b8;
        font-size: 13px;
        border: 1px dashed #e2e8f0;
        border-radius: 8px;
    }

    .season-empty-state i {
        font-size: 32px;
        display: block;
        margin-bottom: 8px;
        color: #cbd5e1;
    }

    /* ===== MODAL ===== */
    .modal-overlay {
        display: none;
        position: fixed;
        inset: 0;
        background: rgba(0, 0, 0, 0.5);
        backdrop-filter: blur(4px);
        z-index: 99999;
        align-items: center;
        justify-content: center;
        padding: 1.5rem;
    }

    .modal-overlay.show {
        display: flex;
    }

    .modal-overlay .modal-box {
        background: #fff;
        border-radius: 14px;
        max-width: 520px;
        width: 100%;
        padding: 1.8rem 2rem 2rem;
        position: relative;
        animation: fadeSlide 0.25s ease;
        max-height: 90vh;
        overflow-y: auto;
    }

    .modal-overlay .modal-box .modal-close-btn {
        position: absolute;
        top: 1rem;
        right: 1.2rem;
        background: none;
        border: none;
        font-size: 22px;
        color: #94a3b8;
        cursor: pointer;
        transition: transform 0.2s;
    }

    .modal-overlay .modal-box .modal-close-btn:hover {
        transform: rotate(90deg);
    }

    .modal-overlay .modal-box h3 {
        font-size: 18px;
        font-weight: 600;
        color: #0f172a;
        margin-bottom: 4px;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .modal-overlay .modal-box h3 i {
        color: #0ea5e9;
    }

    .modal-overlay .modal-box .modal-sub {
        color: #94a3b8;
        font-size: 13px;
        margin-bottom: 1.5rem;
    }

    .modal-overlay .modal-box .form-group {
        margin-bottom: 1rem;
    }

    .modal-overlay .modal-box .form-group label {
        display: block;
        font-size: 11px;
        font-weight: 600;
        color: #475569;
        text-transform: uppercase;
        letter-spacing: 0.4px;
        margin-bottom: 4px;
    }

    .modal-overlay .modal-box .form-group label .required {
        color: #991b1b;
    }

    .modal-overlay .modal-box .form-group input,
    .modal-overlay .modal-box .form-group select {
        width: 100%;
        padding: 9px 12px;
        border: 1.5px solid #e2e8f0;
        border-radius: 8px;
        font-size: 13px;
        outline: none;
        background: #fff;
        transition: border-color 0.15s;
    }

    .modal-overlay .modal-box .form-group input:focus,
    .modal-overlay .modal-box .form-group select:focus {
        border-color: #0f172a;
    }

    .modal-overlay .modal-box .form-row-modal {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 0.8rem;
    }

    .modal-overlay .modal-box .form-actions {
        display: flex;
        gap: 8px;
        margin-top: 1.2rem;
    }

    .modal-overlay .modal-box .form-actions .btn-submit {
        flex: 1;
        padding: 10px;
        background: #0f172a;
        color: #fff;
        border: none;
        border-radius: 8px;
        font-size: 14px;
        font-weight: 500;
        cursor: pointer;
        transition: all 0.15s;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 6px;
    }

    .modal-overlay .modal-box .form-actions .btn-submit:hover {
        background: #1e293b;
    }

    .modal-overlay .modal-box .form-actions .btn-cancel {
        padding: 10px 20px;
        background: #f1f5f9;
        color: #475569;
        border: none;
        border-radius: 8px;
        font-size: 14px;
        font-weight: 500;
        cursor: pointer;
        transition: all 0.15s;
    }

    .modal-overlay .modal-box .form-actions .btn-cancel:hover {
        background: #e2e8f0;
    }

    .modal-overlay .modal-box .form-message {
        margin-top: 12px;
        padding: 10px 14px;
        border-radius: 6px;
        font-size: 13px;
        display: none;
    }

    .modal-overlay .modal-box .form-message.success {
        display: block;
        background: #dcfce7;
        color: #166534;
        border: 1px solid #86efac;
    }

    .modal-overlay .modal-box .form-message.error {
        display: block;
        background: #fee2e2;
        color: #991b1b;
        border: 1px solid #fca5a5;
    }

    @keyframes fadeSlide {
        from { opacity: 0; transform: translateY(6px); }
        to { opacity: 1; transform: translateY(0); }
    }

    .card.is-refreshing {
        opacity: 0.55;
        pointer-events: none;
        transition: opacity 0.15s;
    }

    .range-row.row-entering,
    .season-price-row.row-entering {
        animation: rowIn 0.3s ease;
    }

    @keyframes rowIn {
        from { background: #f0fdf4; }
        to { background: transparent; }
    }

    @media (max-width: 600px) {
        .edit-tariff-wrapper { padding: 1rem; }
        .edit-tariff-wrapper .form-row { grid-template-columns: 1fr; }
        .edit-tariff-wrapper .form-actions { flex-direction: column; }
        .edit-tariff-wrapper .form-actions .btn-cancel { width: 100%; }
        .edit-tariff-wrapper .info-box { flex-direction: column; align-items: stretch; gap: 6px; }
        .ranges-table-head { display: none; }
        .range-row {
            grid-template-columns: 1fr 1fr;
            border-bottom: 1px solid #e2e8f0;
            padding: 10px 12px;
        }
        .range-row .form-group label { display: block; }
        .range-row .btn-remove-row { grid-column: span 2; width: 100%; }
        .season-toggle-row { flex-wrap: wrap; }
        .season-price-row { grid-template-columns: 1fr; }
        .modal-overlay .modal-box { max-width: 100%; margin: 1rem; padding: 1.5rem 1.2rem; }
        .modal-overlay .modal-box .form-row-modal { grid-template-columns: 1fr; }
        .modal-overlay .modal-box .form-actions { flex-direction: column; }
        .modal-overlay .modal-box .form-actions .btn-cancel { width: 100%; text-align: center; }
        .seasonal-container .seasonal-header { flex-direction: column; align-items: stretch; }
    }
</style>

<div class="edit-tariff-wrapper">

   <div class="card" id="baseTariffCard">
        <div class="card-header">
            <h3><i class="ti ti-edit"></i> Tarifa Base</h3>
            <a href="{{ route('admin.tariffs.index', $service->id_service) }}" class="back-link">
                <i class="ti ti-arrow-left"></i> Volver a Tarifas
            </a>

        </div>
        <div class="card-body">
            @if(session('success'))
                <div class="alert-success">{{ session('success') }}</div>
            @endif

            @if(session('error'))
                <div class="alert-error">{{ session('error') }}</div>
            @endif

            @if($errors->any())
                <div class="alert-error">
                    @foreach($errors->all() as $error)
                        {{ $error }}<br>
                    @endforeach
                </div>
            @endif

            {{-- INFO DE LA SUBCATEGORÍA --}}
            <div class="info-box">
                <span class="label"><i class="ti ti-tag"></i> Subcategoría:</span>
                <span class="value">{{ $subcategory->name ?? $tariff->subcategory->name ?? 'Sin subcategoría' }}</span>
            </div>

            <label class="season-toggle-row" style="margin-bottom:1rem;">
                <input type="checkbox" id="pricingModeToggle" {{ ($tariff->pricing_type ?? $service->pricing_type) === 'tiered' ? 'checked' : '' }}>
                <span>Usar tarifas por rango de personas</span>
            </label>

            @if(($tariff->pricing_type ?? $service->pricing_type) === 'flat')
                <form id="flatTariffForm" action="{{ route('admin.tariffs.update', [$service->id_service, $tariff->id_tariff]) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="form-group">
                        <label>Precio por persona <span class="required">*</span></label>
                        <div class="price-input-wrap">
                            <input type="number" name="price" class="form-control" 
                                   value="{{ old('price', $tariff->price) }}" 
                                   step="0.01" min="0" required placeholder="0.00">
                            <span class="currency-symbol">$</span>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Estado</label>
                        <select name="status" class="form-control">
                            <option value="active" {{ old('status', $tariff->status) === 'active' ? 'selected' : '' }}>Activa</option>
                            <option value="pending" {{ old('status', $tariff->status) === 'pending' ? 'selected' : '' }}>Pendiente</option>
                            <option value="inactive" {{ old('status', $tariff->status) === 'inactive' ? 'selected' : '' }}>Inactiva</option>
                        </select>
                    </div>

                    <div class="form-actions">
                        <button type="submit" class="btn-submit">
                            <i class="ti ti-device-floppy"></i> Guardar Cambios
                        </button>
                        <a href="{{ route('admin.tariffs.index', $service->id_service) }}" class="btn-cancel">
                            <i class="ti ti-x"></i> Cancelar
                        </a>
                    </div>
                </form>

            @else
                <form id="rangesForm" action="{{ route('admin.tariffs.updateRanges', [$service->id_service, $subcategory->id_subcategories ?? $tariff->id_subcategories]) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="form-group">
                        
                        <div class="current-ranges-title">
                            <i class="ti ti-stack-2"></i> Rangos configurados
                            <button type="button" class="btn-add-row" onclick="applyBaseRangeTemplate()" title="Usar rangos habituales hasta 30">
                                <i class="ti ti-wand"></i> Usar plantilla hasta 30
                            </button>
                            <button type="button" class="btn-add-level" onclick="addRange()" title="Agregar nivel">
                                <i class="ti ti-plus"></i>
                            </button>
                        </div>

                        <div class="ranges-table-head">
                            <span>Desde</span>
                            <span>Hasta</span>
                            <span>Precio</span>
                            <span></span>
                        </div>

                        <div class="ranges-container" id="rangesContainer">
                            @forelse($ranges as $index => $range)
                                <div class="range-row" data-index="{{ $index }}">
                                    <div class="form-group">
                                        <label>Desde</label>
                                        <input type="number" name="ranges[{{ $index }}][min]" class="form-control" 
                                               value="{{ $range->min_people_count }}" min="0" required>
                                    </div>
                                    <div class="form-group">
                                        <label>Hasta</label>
                                        <input type="number" name="ranges[{{ $index }}][max]" class="form-control" 
                                               value="{{ $range->max_people_count }}" min="0" placeholder="∞">
                                    </div>
                                    <div class="form-group">
                                        <label>Precio <span class="required">*</span></label>
                                        <div class="price-input-wrap">
                                            <input type="number" name="ranges[{{ $index }}][price]" class="form-control" 
                                                   value="{{ $range->price }}" step="0.01" min="0" required>
                                            <span class="currency-symbol">$</span>
                                        </div>
                                    </div>
                                    <button type="button" class="btn-remove-row" onclick="removeRange(this)" title="Eliminar rango">
                                        <i class="ti ti-x"></i>
                                    </button>
                                </div>
                            @empty
                                <div class="empty-ranges">
                                    <i class="ti ti-coin-off"></i>
                                    No hay rangos configurados para esta subcategoría.
                                </div>
                            @endforelse
                        </div>

                        <small style="display:block;font-size:11px;color:#94a3b8;margin-top:6px;">
                            <i class="ti ti-info-circle"></i> 
                            Ej: 1-2, 3-4, 5+. Deja "Hasta" vacío para "∞" (5+)
                        </small>
                    </div>

                    <div class="form-group">
                        <label>Estado</label>
                        <select name="status" class="form-control">
                            <option value="active" {{ old('status', $tariff->status ?? 'pending') === 'active' ? 'selected' : '' }}>Activa</option>
                            <option value="pending" {{ old('status', $tariff->status ?? 'pending') === 'pending' ? 'selected' : '' }}>Pendiente</option>
                            <option value="inactive" {{ old('status', $tariff->status ?? 'pending') === 'inactive' ? 'selected' : '' }}>Inactiva</option>
                        </select>
                    </div>

                    <div class="form-actions">
                        <button type="submit" class="btn-submit">
                            <i class="ti ti-device-floppy"></i> Guardar Rangos
                        </button>
                        <a href="{{ route('admin.tariffs.index', $service->id_service) }}" class="btn-cancel">
                            <i class="ti ti-x"></i> Cancelar
                        </a>
                    </div>
                </form>
            @endif
        </div>
    </div>

    {{-- ============================================================= --}}
    {{-- TEMPORADAS                                                   --}}
    {{-- ============================================================= --}}
    <div class="card" id="seasonsCard" data-has-seasons="{{ (isset($seasons) && $seasons->count() > 0) ? '1' : '0' }}">
        <div class="card-header">
            <h3><i class="ti ti-calendar-event"></i> Tarifas de Temporada</h3>
        </div>
        <div class="card-body">

            <label class="season-toggle-row">
                <input type="checkbox" id="seasonToggle" {{ (isset($seasons) && $seasons->count() > 0) ? 'checked' : '' }}>
                <span>¿Con tarifas de temporada?</span>
                <span class="toggle-check-indicator" id="seasonToggleIndicator" style="{{ (isset($seasons) && $seasons->count() > 0) ? '' : 'display:none;' }}">
                    <i class="ti ti-check"></i>
                </span>
            </label>

            <div class="season-toggle-content{{ (isset($seasons) && $seasons->count() > 0) ? '' : ' is-collapsed' }}" id="seasonToggleContent">

                <button class="btn-add-season" onclick="openCreateSeasonModal()" style="margin-bottom:1rem;">
                    <i class="ti ti-plus"></i> Nueva Temporada
                </button>

                @if(isset($availableSeasons) && $availableSeasons->isNotEmpty())
                    <button type="button" class="btn-add-season" onclick="openAssignSeasonModal()" style="margin:0 0 1rem 0;background:#0369a1;">
                        <i class="ti ti-calendar-plus"></i> Asignar temporada creada
                    </button>
                @endif

            @if(isset($seasons) && $seasons->count() > 0)
                <div>
                    <div class="seasonal-title" style="margin-bottom:1rem;">
                        <i class="ti ti-calendar"></i> Temporadas existentes
                        <span class="badge-seasonal">{{ $seasons->count() }} temporada(s)</span>
                    </div>

                    @foreach($seasons as $season)
                        @php
                            $seasonTariffs = $seasonTariffsGrouped->get($season->id_season, collect());
                            $hasPrices = $seasonTariffs->whereNotNull('price')->count() > 0;
                            $seasonPricingType = $seasonTariffs->first()?->pricing_type ?? $tariff->pricing_type ?? 'flat';
                        @endphp
                        <div class="season-card is-collapsed" data-season-id="{{ $season->id_season }}">
                            <div class="season-header" onclick="toggleSeasonCard(this)">
                                <span class="season-name">
                                    <i class="ti ti-calendar-event"></i>
                                    {{ $season->name }}
                                </span>
                                <span class="season-dates">
                                    <i class="ti ti-calendar"></i>
                                    {{ $season->start_date->format('d/m/Y') }} - {{ $season->end_date->format('d/m/Y') }}
                                    <span style="margin:0 8px;color:#e2e8f0;">|</span>
                                    @if($hasPrices)
                                        <span class="badge-status active">Precios asignados</span>
                                    @else
                                        <span class="badge-pending">Sin precios</span>
                                    @endif
                                </span>
                                <button class="btn-season-toggle" onclick="event.stopPropagation(); toggleSeasonCard(this.closest('.season-card').querySelector('.season-header'));" title="Mostrar/ocultar">
                                    <i class="ti ti-chevron-down"></i>
                                </button>
                                <button class="btn-season-delete" onclick="event.stopPropagation(); confirmDeleteSeason({{ $season->id_season }})">
                                    <i class="ti ti-trash"></i>
                                </button>
                            </div>

                            <div class="season-body">
                                {{-- FORMULARIO PARA ASIGNAR PRECIOS A LA TEMPORADA --}}
                                <div class="season-price-form">
                                    <div class="season-price-title">
                                        <i class="ti ti-coin"></i> Asignar precios
                                        <span class="badge-rangos">{{ $seasonTariffs->count() }} rangos</span>
                                    </div>

                                    <form class="seasonPriceForm" data-season-id="{{ $season->id_season }}" action="{{ route('admin.seasons.updateTariffs', [$service->id_service, $season->id_season]) }}" method="POST">
                                        @csrf
                                        @method('PUT')
                                        <input type="hidden" name="id_subcategories" value="{{ $subcategory->id_subcategories ?? $tariff->id_subcategories }}">
                                        <input type="hidden" name="pricing_type" value="{{ $seasonPricingType }}">

                                        @if($seasonPricingType === 'flat')
                                            @php($st = $seasonTariffs->first())
                                            <div class="season-price-row">
                                                <div class="form-group">
                                                    <label>Precio por persona</label>
                                                    <div class="price-input-wrap">
                                                        <input type="number" name="tariffs[0][price]" 
                                                               class="form-control" step="0.01" min="0" 
                                                               value="{{ old('tariffs.0.price', $st?->price) }}" 
                                                               placeholder="0.00" required>
                                                        <span class="currency-symbol">$</span>
                                                    </div>
                                                </div>
                                            </div>
                                        @else
                                            <div class="season-ranges-container">
                                                @forelse($seasonTariffs as $st)
                                                    <div class="season-price-row">
                                                        <div class="form-group">
                                                            <label>Desde</label>
                                                            <input type="number" name="tariffs[{{ $loop->index }}][min]" class="form-control" min="0" value="{{ old('tariffs.'.$loop->index.'.min', $st->min_people_count) }}" required>
                                                        </div>
                                                        <div class="form-group">
                                                            <label>Hasta</label>
                                                            <input type="number" name="tariffs[{{ $loop->index }}][max]" class="form-control" min="0" placeholder="∞" value="{{ old('tariffs.'.$loop->index.'.max', $st->max_people_count) }}">
                                                        </div>
                                                        <div class="form-group">
                                                            <label>Precio</label>
                                                            <div class="price-input-wrap">
                                                                <input type="number" name="tariffs[{{ $loop->index }}][price]" class="form-control" step="0.01" min="0" value="{{ old('tariffs.'.$loop->index.'.price', $st->price) }}" placeholder="0.00" required>
                                                                <span class="currency-symbol">$</span>
                                                            </div>
                                                        </div>
                                                        <button type="button" class="btn-remove-row" onclick="removeSeasonRange(this)" title="Eliminar rango">
                                                            <i class="ti ti-x"></i>
                                                        </button>
                                                    </div>
                                                @empty
                                                    <div class="no-season-ranges">
                                                        <i class="ti ti-coin-off" style="display:inline-block;margin-right:4px;"></i>
                                                        Agrega los rangos propios de esta temporada.
                                                    </div>
                                                @endforelse
                                            </div>

                                            <button type="button" class="btn-add-row" onclick="addSeasonRange(this.closest('.seasonPriceForm'))" style="margin-top:0.6rem;">
                                                <i class="ti ti-plus"></i> Agregar rango
                                            </button>
                                            <button type="button" class="btn-add-row" onclick="applySeasonRangeTemplate(this.closest('.seasonPriceForm'))" style="margin-top:0.6rem;">
                                                <i class="ti ti-wand"></i> Usar plantilla hasta 30
                                            </button>
                                        @endif

                                        <div style="display:flex;gap:8px;flex-wrap:wrap;margin-top:0.8rem;">
                                            <button type="submit" class="btn-save-season-prices">
                                                <i class="ti ti-device-floppy"></i> Guardar Precios
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="season-empty-state">
                    <i class="ti ti-calendar-off"></i>
                    No hay temporadas creadas para esta subcategoría.
                </div>
            @endif

            </div>
        </div>
    </div>
</div>

{{-- ===== MODAL CREAR TEMPORADA ===== --}}
<div class="modal-overlay" id="createSeasonModal">
    <div class="modal-box">
        <button class="modal-close-btn" onclick="closeCreateSeasonModal()">✕</button>

        <h3><i class="ti ti-calendar-plus"></i> Nueva Temporada</h3>
        <p class="modal-sub">Crea una temporada para asignar tarifas especiales por fechas</p>

        <form id="createSeasonForm" action="{{ route('admin.seasons.store', $service->id_service) }}" method="POST">
            @csrf
            <input type="hidden" name="id_subcategories" value="{{ $subcategory->id_subcategories ?? $tariff->id_subcategories }}">

            <div class="form-group">
                <label>Nombre <span class="required">*</span></label>
                <input type="text" name="name" class="form-control" placeholder="Ej: Alta Temporada, Navidad, Feriados" required>
            </div>

            <div class="form-row-modal">
                <div class="form-group" style="margin-bottom:0;">
                    <label>Fecha inicio <span class="required">*</span></label>
                    <input type="date" name="start_date" class="form-control" required>
                </div>
                <div class="form-group" style="margin-bottom:0;">
                    <label>Fecha fin <span class="required">*</span></label>
                    <input type="date" name="end_date" class="form-control" required>
                </div>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn-submit" id="submitSeasonBtn">
                    <i class="ti ti-device-floppy"></i> Crear Temporada
                </button>
                <button type="button" class="btn-cancel" onclick="closeCreateSeasonModal()">Cancelar</button>
            </div>

            <div class="form-message" id="seasonFormMessage"></div>
        </form>
    </div>
</div>

@if(isset($availableSeasons) && $availableSeasons->isNotEmpty())
    <div class="modal-overlay" id="assignSeasonModal">
        <div class="modal-box">
            <button class="modal-close-btn" type="button" onclick="closeAssignSeasonModal()">✕</button>

            <h3><i class="ti ti-calendar-plus"></i> Asignar temporada creada</h3>
            <p class="modal-sub">Selecciona una temporada existente para esta subcategoría</p>

            <form id="assignSeasonForm"
                  data-base-url="{{ url('/servicios/' . $service->id_service . '/temporadas') }}"
                  data-subcategory-id="{{ $subcategory->id_subcategories }}"
                  method="POST">
                @csrf

                <div class="form-group">
                    <label for="seasonToAssign">Temporada <span class="required">*</span></label>
                    <select id="seasonToAssign" name="season_id" class="form-control" required>
                        <option value="">Seleccionar temporada</option>
                        @foreach($availableSeasons as $availableSeason)
                            <option value="{{ $availableSeason->id_season }}">
                                {{ $availableSeason->name }} ({{ $availableSeason->start_date->format('d/m/Y') }} - {{ $availableSeason->end_date->format('d/m/Y') }})
                            </option>
                        @endforeach
                    </select>
                    <small>La temporada se asignará sin precio para configurarlo después.</small>
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn-submit">
                        <i class="ti ti-link"></i> Asignar temporada
                    </button>
                    <button type="button" class="btn-cancel" onclick="closeAssignSeasonModal()">Cancelar</button>
                </div>
            </form>
        </div>
    </div>
@endif

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
const CSRF_TOKEN = '{{ csrf_token() }}';
const DELETE_SEASON_URL_BASE = '/servicios/{{ $service->id_service }}/temporadas';

const Toast = Swal.mixin({
    toast: true,
    position: 'top-end',
    showConfirmButton: false,
    timer: 2500,
    timerProgressBar: true,
    didOpen: (toast) => {
        toast.onmouseenter = Swal.stopTimer;
        toast.onmouseleave = Swal.resumeTimer;
    }
});

// ============================================================
// RANGOS (agregar / quitar filas dinámicamente)
// ============================================================
function addRange() {
    const container = document.getElementById('rangesContainer');
    const emptyMsg = container.querySelector('.empty-ranges');
    if (emptyMsg) emptyMsg.remove();

    // El índice se recalcula desde el DOM en cada llamada, así nunca
    // queda desincronizado tras un refresco AJAX de la tarjeta.
    const index = container.querySelectorAll('.range-row').length;

    const div = document.createElement('div');
    div.className = 'range-row row-entering';
    div.dataset.index = index;
    div.innerHTML = `
        <div class="form-group">
            <label>Desde</label>
            <input type="number" name="ranges[${index}][min]" class="form-control" value="" min="0" required>
        </div>
        <div class="form-group">
            <label>Hasta</label>
            <input type="number" name="ranges[${index}][max]" class="form-control" value="" min="0" placeholder="∞">
        </div>
        <div class="form-group">
            <label>Precio <span class="required">*</span></label>
            <div class="price-input-wrap">
                <input type="number" name="ranges[${index}][price]" class="form-control" value="" step="0.01" min="0" required>
                <span class="currency-symbol">$</span>
            </div>
        </div>
        <button type="button" class="btn-remove-row" onclick="removeRange(this)" title="Eliminar rango">
            <i class="ti ti-x"></i>
        </button>
    `;
    container.appendChild(div);
    div.scrollIntoView({ behavior: 'smooth', block: 'center' });
}

function switchFlatToRanges() {
    const form = document.getElementById('flatTariffForm');
    const subcategoryId = @json($subcategory->id_subcategories ?? $tariff->id_subcategories);
    const csrfToken = form.querySelector('input[name="_token"]').value;
    const status = form.querySelector('[name="status"]').value;

    form.id = 'rangesForm';
    form.action = `{{ url('/servicios/' . $service->id_service . '/tarifas/subcategoria') }}/${subcategoryId}/rangos`;
    form.innerHTML = `
        <input type="hidden" name="_token" value="${csrfToken}">
        <input type="hidden" name="_method" value="PUT">
        <div class="form-group">
            <div class="current-ranges-title">
                <i class="ti ti-stack-2"></i> Rangos configurados
                <button type="button" class="btn-add-level" onclick="addRange()" title="Agregar nivel">
                    <i class="ti ti-plus"></i>
                </button>
            </div>
            <div class="ranges-table-head">
                <span>Desde</span>
                <span>Hasta</span>
                <span>Precio</span>
                <span></span>
            </div>
            <div class="ranges-container" id="rangesContainer">
                ${rangeRowTemplate(0)}
            </div>
            <small style="display:block;font-size:11px;color:#94a3b8;margin-top:6px;">
                Agrega los rangos necesarios. Al guardar, esta subcategoría usará tarifas por rango.
            </small>
        </div>
        <div class="form-group">
            <label>Estado</label>
            <select name="status" class="form-control">
                <option value="active" ${status === 'active' ? 'selected' : ''}>Activa</option>
                <option value="pending" ${status === 'pending' ? 'selected' : ''}>Pendiente</option>
                <option value="inactive" ${status === 'inactive' ? 'selected' : ''}>Inactiva</option>
            </select>
        </div>
        <div class="form-actions">
            <button type="submit" class="btn-submit"><i class="ti ti-device-floppy"></i> Guardar Rangos</button>
            <a href="{{ route('admin.tariffs.editSubcategory', [$service->id_service, $subcategory->id_subcategories ?? $tariff->id_subcategories]) }}" class="btn-cancel">Cancelar</a>
        </div>
    `;
}

function rangeRowTemplate(index) {
    return `
        <div class="range-row row-entering" data-index="${index}">
            <div class="form-group">
                <label>Desde</label>
                <input type="number" name="ranges[${index}][min]" class="form-control" min="0" required>
            </div>
            <div class="form-group">
                <label>Hasta</label>
                <input type="number" name="ranges[${index}][max]" class="form-control" min="0" placeholder="∞">
            </div>
            <div class="form-group">
                <label>Precio <span class="required">*</span></label>
                <div class="price-input-wrap">
                    <input type="number" name="ranges[${index}][price]" class="form-control" step="0.01" min="0" required>
                    <span class="currency-symbol">$</span>
                </div>
            </div>
            <button type="button" class="btn-remove-row" onclick="removeRange(this)" title="Eliminar rango">
                <i class="ti ti-x"></i>
            </button>
        </div>
    `;
}

function switchRangesToFlat() {
    const form = document.getElementById('rangesForm');
    const subcategoryId = @json($subcategory->id_subcategories ?? $tariff->id_subcategories);
    const csrfToken = form.querySelector('input[name="_token"]').value;

    form.id = 'flatTariffForm';
    form.action = `{{ url('/servicios/' . $service->id_service . '/tarifas/subcategoria') }}/${subcategoryId}/precio-unico`;
    form.innerHTML = `
        <input type="hidden" name="_token" value="${csrfToken}">
        <input type="hidden" name="_method" value="PUT">
        <div class="form-group">
            <label>Precio por persona <span class="required">*</span></label>
            <div class="price-input-wrap">
                <input type="number" name="price" class="form-control" step="0.01" min="0" required placeholder="0.00">
                <span class="currency-symbol">$</span>
            </div>
        </div>
        <div class="form-group">
            <label>Estado</label>
            <select name="status" class="form-control">
                <option value="active">Activa</option>
                <option value="pending">Pendiente</option>
                <option value="inactive">Inactiva</option>
            </select>
        </div>
        <div class="form-actions">
            <button type="submit" class="btn-submit"><i class="ti ti-device-floppy"></i> Guardar Precio</button>
            <a href="{{ route('admin.tariffs.editSubcategory', [$service->id_service, $subcategory->id_subcategories ?? $tariff->id_subcategories]) }}" class="btn-cancel">Cancelar</a>
        </div>
    `;
}

document.addEventListener('change', function(e) {
    if (e.target.id !== 'pricingModeToggle') {
        return;
    }

    if (e.target.checked && document.getElementById('flatTariffForm')) {
        switchFlatToRanges();
    } else if (!e.target.checked && document.getElementById('rangesForm')) {
        switchRangesToFlat();
    }

    refreshSeasonForms(e.target.checked);
});

function refreshSeasonForms(isTiered) {
    document.querySelectorAll('.season-price-form').forEach(container => {
        const currentForm = container.querySelector('.seasonPriceForm');
        if (!currentForm) {
            return;
        }

        const seasonId = currentForm.dataset.seasonId;
        const subcategoryId = currentForm.querySelector('[name="id_subcategories"]').value;
        container.innerHTML = seasonPriceFormTemplate(seasonId, subcategoryId, isTiered);
    });
}

function seasonPriceFormTemplate(seasonId, subcategoryId, isTiered) {
    const action = `{{ url('/servicios/' . $service->id_service . '/temporadas') }}/${seasonId}/tarifas`;
    const modeContent = isTiered ? `
        <div class="season-ranges-container">
            ${seasonRangeTemplate(0)}
        </div>
        <button type="button" class="btn-add-row" onclick="addSeasonRange(this.closest('.seasonPriceForm'))" style="margin-top:0.6rem;">
            <i class="ti ti-plus"></i> Agregar rango
        </button>
        <button type="button" class="btn-add-row" onclick="applySeasonRangeTemplate(this.closest('.seasonPriceForm'))" style="margin-top:0.6rem;">
            <i class="ti ti-wand"></i> Usar plantilla hasta 30
        </button>
    ` : `
        <div class="season-price-row">
            <div class="form-group">
                <label>Precio por persona</label>
                <div class="price-input-wrap">
                    <input type="number" name="tariffs[0][price]" class="form-control" step="0.01" min="0" placeholder="0.00" required>
                    <span class="currency-symbol">$</span>
                </div>
            </div>
        </div>
    `;

    return `
        <div class="season-price-title">
            <i class="ti ti-coin"></i> Asignar precios
        </div>
        <form class="seasonPriceForm" data-season-id="${seasonId}" action="${action}" method="POST">
            <input type="hidden" name="_token" value="${CSRF_TOKEN}">
            <input type="hidden" name="_method" value="PUT">
            <input type="hidden" name="id_subcategories" value="${subcategoryId}">
            <input type="hidden" name="pricing_type" value="${isTiered ? 'tiered' : 'flat'}">
            ${modeContent}
            <div style="display:flex;gap:8px;flex-wrap:wrap;margin-top:0.8rem;">
                <button type="submit" class="btn-save-season-prices">
                    <i class="ti ti-device-floppy"></i> Guardar Precios
                </button>
            </div>
        </form>
    `;
}

function seasonRangeTemplate(index) {
    return `
        <div class="season-price-row row-entering">
            <div class="form-group">
                <label>Desde</label>
                <input type="number" name="tariffs[${index}][min]" class="form-control" min="0" required>
            </div>
            <div class="form-group">
                <label>Hasta</label>
                <input type="number" name="tariffs[${index}][max]" class="form-control" min="0" placeholder="∞">
            </div>
            <div class="form-group">
                <label>Precio</label>
                <div class="price-input-wrap">
                    <input type="number" name="tariffs[${index}][price]" class="form-control" step="0.01" min="0" placeholder="0.00" required>
                    <span class="currency-symbol">$</span>
                </div>
            </div>
            <button type="button" class="btn-remove-row" onclick="removeSeasonRange(this)" title="Eliminar rango">
                <i class="ti ti-x"></i>
            </button>
        </div>
    `;
}

function removeRange(button) {
    const row = button.closest('.range-row');
    const container = document.getElementById('rangesContainer');
    const rows = container.querySelectorAll('.range-row');
    
    if (rows.length <= 1) {
        Swal.fire({
            icon: 'warning',
            title: 'No puedes eliminar el último rango',
            text: 'Debe haber al menos un rango configurado.',
            confirmButtonColor: '#0f172a'
        });
        return;
    }
    row.remove();
    renumberRanges();
}

function addSeasonRange(form) {
    const container = form.querySelector('.season-ranges-container');
    const emptyMessage = container.querySelector('.no-season-ranges');
    if (emptyMessage) emptyMessage.remove();

    const index = container.querySelectorAll('.season-price-row').length;
    const row = document.createElement('div');
    row.className = 'season-price-row row-entering';
    row.innerHTML = `
        <div class="form-group">
            <label>Desde</label>
            <input type="number" name="tariffs[${index}][min]" class="form-control" min="0" required>
        </div>
        <div class="form-group">
            <label>Hasta</label>
            <input type="number" name="tariffs[${index}][max]" class="form-control" min="0" placeholder="∞">
        </div>
        <div class="form-group">
            <label>Precio</label>
            <div class="price-input-wrap">
                <input type="number" name="tariffs[${index}][price]" class="form-control" step="0.01" min="0" placeholder="0.00" required>
                <span class="currency-symbol">$</span>
            </div>
        </div>
        <button type="button" class="btn-remove-row" onclick="removeSeasonRange(this)" title="Eliminar rango">
            <i class="ti ti-x"></i>
        </button>
    `;
    container.appendChild(row);
}

const standardTieredRanges = [
    { min: 1, max: 1 },
    { min: 2, max: 2 },
    { min: 3, max: 4 },
    { min: 5, max: 9 },
    { min: 10, max: 14 },
    { min: 15, max: 19 },
    { min: 20, max: 24 },
    { min: 25, max: 29 },
    { min: 30, max: 30 }
];

function confirmApplyRangeTemplate(hasExistingRanges) {
    if (!hasExistingRanges) return Promise.resolve(true);

    return Swal.fire({
        icon: 'warning',
        title: '¿Reemplazar rangos?',
        text: 'Los rangos actuales serán reemplazados por la plantilla hasta 30.',
        showCancelButton: true,
        confirmButtonColor: '#0f172a',
        confirmButtonText: 'Sí, aplicar plantilla',
        cancelButtonText: 'Cancelar'
    }).then(result => result.isConfirmed);
}

function applyBaseRangeTemplate() {
    const container = document.getElementById('rangesContainer');
    const hasExistingRanges = container.querySelectorAll('.range-row').length > 0;

    confirmApplyRangeTemplate(hasExistingRanges).then(confirmed => {
        if (!confirmed) return;

        container.innerHTML = standardTieredRanges.map((range, index) => `
            <div class="range-row row-entering" data-index="${index}">
                <div class="form-group">
                    <label>Desde</label>
                    <input type="number" name="ranges[${index}][min]" class="form-control" value="${range.min}" min="0" required>
                </div>
                <div class="form-group">
                    <label>Hasta</label>
                    <input type="number" name="ranges[${index}][max]" class="form-control" value="${range.max}" min="0" required>
                </div>
                <div class="form-group">
                    <label>Precio <span class="required">*</span></label>
                    <div class="price-input-wrap">
                        <input type="number" name="ranges[${index}][price]" class="form-control" value="" step="0.01" min="0" required>
                        <span class="currency-symbol">$</span>
                    </div>
                </div>
                <button type="button" class="btn-remove-row" onclick="removeRange(this)" title="Eliminar rango">
                    <i class="ti ti-x"></i>
                </button>
            </div>
        `).join('');
    });
}

function applySeasonRangeTemplate(form) {
    const container = form.querySelector('.season-ranges-container');
    const hasExistingRanges = container.querySelectorAll('.season-price-row').length > 0;

    confirmApplyRangeTemplate(hasExistingRanges).then(confirmed => {
        if (!confirmed) return;

        container.innerHTML = standardTieredRanges.map((range, index) => `
            <div class="season-price-row row-entering">
                <div class="form-group">
                    <label>Desde</label>
                    <input type="number" name="tariffs[${index}][min]" class="form-control" value="${range.min}" min="0" required>
                </div>
                <div class="form-group">
                    <label>Hasta</label>
                    <input type="number" name="tariffs[${index}][max]" class="form-control" value="${range.max}" min="0" required>
                </div>
                <div class="form-group">
                    <label>Precio</label>
                    <div class="price-input-wrap">
                        <input type="number" name="tariffs[${index}][price]" class="form-control" value="" step="0.01" min="0" required>
                        <span class="currency-symbol">$</span>
                    </div>
                </div>
                <button type="button" class="btn-remove-row" onclick="removeSeasonRange(this)" title="Eliminar rango">
                    <i class="ti ti-x"></i>
                </button>
            </div>
        `).join('');
    });
}

function removeSeasonRange(button) {
    const row = button.closest('.season-price-row');
    const container = row.closest('.season-ranges-container');
    const rows = container.querySelectorAll('.season-price-row');

    if (rows.length <= 1) {
        row.remove();
        container.innerHTML = '<div class="no-season-ranges"><i class="ti ti-coin-off" style="display:inline-block;margin-right:4px;"></i>Agrega los rangos propios de esta temporada.</div>';
        return;
    }

    row.remove();
    container.querySelectorAll('.season-price-row').forEach((seasonRow, index) => {
        seasonRow.querySelectorAll('input[name]').forEach(input => {
            input.name = input.name.replace(/tariffs\[\d+\]/, `tariffs[${index}]`);
        });
    });
}

// Renombra los "name" de los inputs restantes para que los índices
// queden consecutivos (0,1,2...) después de eliminar una fila.
function renumberRanges() {
    const rows = document.querySelectorAll('#rangesContainer .range-row');
    rows.forEach((row, i) => {
        row.dataset.index = i;
        row.querySelectorAll('input[name]').forEach(input => {
            input.name = input.name.replace(/ranges\[\d+\]/, `ranges[${i}]`);
        });
    });
}

// ============================================================
// Helper genérico: envía un formulario por AJAX y reemplaza una
// tarjeta (card) completa con el HTML actualizado que devuelve el
// servidor (Laravel redirige al mismo edit tras guardar, fetch
// sigue esa redirección solo). No requiere tocar los controladores.
// ============================================================
function ajaxFormSubmit(form, cardId, btn, callback) {
    const originalHtml = btn ? btn.innerHTML : null;
    if (btn) {
        btn.disabled = true;
        btn.innerHTML = '<i class="ti ti-loader ti-spin"></i> Guardando...';
    }

    fetch(form.action, {
        method: 'POST', // el hidden @method('PUT') hace el spoofing
        headers: { 'X-Requested-With': 'XMLHttpRequest' },
        body: new FormData(form)
    })
    .then(res => res.text())
    .then(html => {
        const doc = new DOMParser().parseFromString(html, 'text/html');
        const newCard = doc.getElementById(cardId);
        const oldCard = document.getElementById(cardId);
        const hasError = doc.querySelector('.alert-error');
        const hasValidationError = doc.querySelector('.alert-danger, .invalid-feedback');

        if (newCard && oldCard) oldCard.replaceWith(newCard);

        if (hasError || hasValidationError) {
            const errorElement = hasError || hasValidationError;
            Toast.fire({ icon: 'error', title: errorElement.textContent.trim().split('\n')[0] || 'Revisa los datos ingresados' });
            if (callback) callback(false);
        } else {
            Toast.fire({ icon: 'success', title: 'Guardado correctamente' });
            if (callback) callback(true);
        }
    })
    .catch(() => Toast.fire({ icon: 'error', title: 'Error de conexión, intenta nuevamente' }))
    .finally(() => {
        if (btn) {
            btn.disabled = false;
            btn.innerHTML = originalHtml;
        }
    });
}

// ============================================================
// TOGGLE ¿CON TARIFAS DE TEMPORADA?
// Delegado en document para que siga funcionando después de que
// #seasonsCard se reemplace vía AJAX.
// ============================================================
document.addEventListener('change', function(e) {
    if (e.target && e.target.id === 'seasonToggle') {
        const content = document.getElementById('seasonToggleContent');
        const indicator = document.getElementById('seasonToggleIndicator');
        if (e.target.checked) {
            content.classList.remove('is-collapsed');
            indicator.style.display = 'inline-flex';
        } else {
            content.classList.add('is-collapsed');
            indicator.style.display = 'none';
        }
    }
});

document.addEventListener('submit', function(e) {
    const form = e.target;

    if (form.id === 'flatTariffForm') {
        e.preventDefault();
        ajaxFormSubmit(form, 'baseTariffCard', form.querySelector('.btn-submit'));
    }

    if (form.id === 'rangesForm') {
        e.preventDefault();
        ajaxFormSubmit(form, 'baseTariffCard', form.querySelector('.btn-submit'));
    }

    // Precios de una temporada
    if (form.classList.contains('seasonPriceForm')) {
        e.preventDefault();
        ajaxFormSubmit(form, 'seasonsCard', form.querySelector('.btn-save-season-prices'));
    }

    // Crear temporada
    if (form.id === 'createSeasonForm') {
        e.preventDefault();
        ajaxFormSubmit(form, 'seasonsCard', document.getElementById('submitSeasonBtn'), function(success) {
            if (success) closeCreateSeasonModal();
        });
    }
});

// ============================================================
// MODAL CREAR TEMPORADA
// ============================================================
function openCreateSeasonModal() {
    document.getElementById('createSeasonModal').classList.add('show');
    document.body.style.overflow = 'hidden';
    document.getElementById('createSeasonForm').reset();
    document.getElementById('seasonFormMessage').className = 'form-message';
    document.getElementById('seasonFormMessage').textContent = '';
}

function closeCreateSeasonModal() {
    document.getElementById('createSeasonModal').classList.remove('show');
    document.body.style.overflow = '';
}

function openAssignSeasonModal() {
    document.getElementById('assignSeasonModal').classList.add('show');
    document.body.style.overflow = 'hidden';
}

function closeAssignSeasonModal() {
    document.getElementById('assignSeasonModal').classList.remove('show');
    document.body.style.overflow = '';
}

document.addEventListener('submit', function(e) {
    if (e.target.id !== 'assignSeasonForm') {
        return;
    }

    const form = e.target;
    const seasonId = form.querySelector('[name="season_id"]').value;

    if (seasonId) {
        form.action = `${form.dataset.baseUrl}/${seasonId}/subcategoria/${form.dataset.subcategoryId}`;
    }
});

// ============================================================
// PLEGAR / DESPLEGAR UNA TARJETA DE TEMPORADA
// ============================================================
function toggleSeasonCard(headerEl) {
    const card = headerEl.closest('.season-card');
    if (card) card.classList.toggle('is-collapsed');
}

function confirmDeleteSeason(seasonId) {
    Swal.fire({
        title: '¿Eliminar temporada?',
        text: 'Se eliminarán TODAS las tarifas asociadas a esta temporada.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#991b1b',
        cancelButtonColor: '#94a3b8',
        confirmButtonText: 'Sí, eliminar',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (!result.isConfirmed) return;

        const card = document.querySelector(`.season-card[data-season-id="${seasonId}"]`);
        if (card) card.classList.add('row-removing');

        const fd = new FormData();
        fd.append('_token', CSRF_TOKEN);
        fd.append('_method', 'DELETE');

        fetch(`${DELETE_SEASON_URL_BASE}/${seasonId}`, {
            method: 'POST',
            headers: { 'X-Requested-With': 'XMLHttpRequest' },
            body: fd
        })
        .then(res => res.text())
        .then(html => {
            const doc = new DOMParser().parseFromString(html, 'text/html');
            const newCard = doc.getElementById('seasonsCard');
            const oldCard = document.getElementById('seasonsCard');
            if (newCard && oldCard) oldCard.replaceWith(newCard);
            Toast.fire({ icon: 'success', title: 'Temporada eliminada' });
        })
        .catch(() => {
            if (card) card.classList.remove('row-removing');
            Toast.fire({ icon: 'error', title: 'No se pudo eliminar la temporada' });
        });
    });
}

document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeCreateSeasonModal();
        closeAssignSeasonModal();
    }
});

document.getElementById('createSeasonModal').addEventListener('click', function(e) {
    if (e.target === this) closeCreateSeasonModal();
});

const assignSeasonModal = document.getElementById('assignSeasonModal');
if (assignSeasonModal) {
    assignSeasonModal.addEventListener('click', function(e) {
        if (e.target === this) closeAssignSeasonModal();
    });
}
</script>
@endsection