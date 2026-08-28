@extends('layouts.app')
@section('title', 'Detalle de ' . $supplier->supplier_name)
@section('content')
@section('pageContentClass', 'no-padding')

<style>
    /* ===== LAYOUT PRINCIPAL ===== */
    .supplier-detail-wrapper {
        display: flex;
        flex-direction: column;
        height: calc(100vh - 60px);
        background: #f8fafc;
        overflow: hidden;
    }

    /* ===== CABECERA DE INFORMACIÓN ===== */
    .supplier-header {
        background: #fff;
        border-bottom: 1px solid #e2e8f0;
        padding: 1.2rem 2rem;
        display: flex;
        align-items: center;
        gap: 1.5rem;
        flex-shrink: 0;
        flex-wrap: wrap;
    }

    .supplier-header .avatar-large {
        width: 56px;
        height: 56px;
        border-radius: 50%;
        background: linear-gradient(135deg, #0f172a, #1e293b);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 22px;
        font-weight: 700;
        color: #fff;
        flex-shrink: 0;
    }

    .supplier-header .header-info {
        flex: 1;
        min-width: 200px;
    }

    .supplier-header .header-info h1 {
        font-size: 20px;
        font-weight: 700;
        color: #0f172a;
        margin: 0;
    }

    .supplier-header .header-info .sub {
        font-size: 13px;
        color: #94a3b8;
        margin-top: 2px;
    }

    .supplier-header .header-meta {
        display: flex;
        align-items: center;
        gap: 1.5rem;
        flex-wrap: wrap;
    }

    .supplier-header .header-meta .meta-item {
        display: flex;
        align-items: center;
        gap: 6px;
        font-size: 13px;
        color: #475569;
    }

    .supplier-header .header-meta .meta-item i {
        color: #94a3b8;
        font-size: 16px;
    }

    .supplier-header .header-meta .meta-item .badge-chain {
        background: #f1f5f9;
        color: #475569;
        padding: 2px 10px;
        border-radius: 4px;
        font-size: 11px;
        font-weight: 500;
    }

    .supplier-header .header-meta .meta-item .badge-chain.independent {
        background: #e8edf4;
        color: #94a3b8;
    }

    .supplier-header .header-actions {
        display: flex;
        gap: 8px;
        flex-shrink: 0;
    }

    .supplier-header .header-actions .btn {
        padding: 7px 16px;
        border-radius: 8px;
        font-size: 13px;
        font-weight: 600;
        text-decoration: none;
        transition: all 0.15s;
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }

    .supplier-header .header-actions .btn-edit {
        background: #0f172a;
        color: #fff;
        border: none;
    }

    .supplier-header .header-actions .btn-edit:hover {
        background: #1e293b;
    }

    .supplier-header .header-actions .btn-back {
        background: #f1f5f9;
        color: #475569;
        border: 1px solid #e2e8f0;
    }

    .supplier-header .header-actions .btn-back:hover {
        background: #e8edf4;
    }

    /* ===== CONTENIDO PRINCIPAL ===== */
    .supplier-main-content {
        flex: 1;
        display: flex;
        flex-direction: column;
        overflow: hidden;
        background: #f8fafc;
    }

    .supplier-main-content .content-nav {
        display: flex;
        gap: 2px;
        background: #fff;
        padding: 0 2rem;
        border-bottom: 1px solid #e2e8f0;
        flex-shrink: 0;
        flex-wrap: wrap;
    }

    .supplier-main-content .content-nav button {
        padding: 14px 22px;
        border: none;
        border-bottom: 2px solid transparent;
        font-size: 13px;
        font-weight: 500;
        cursor: pointer;
        transition: all 0.15s;
        background: transparent;
        color: #94a3b8;
        display: inline-flex;
        align-items: center;
        gap: 8px;
    }

    .supplier-main-content .content-nav button:hover {
        color: #0f172a;
    }

    .supplier-main-content .content-nav button.active {
        color: #0f172a;
        border-bottom-color: #0f172a;
    }

    .supplier-main-content .content-nav button .count-badge {
        background: #f1f5f9;
        color: #475569;
        padding: 0 10px;
        border-radius: 12px;
        font-size: 11px;
        font-weight: 600;
    }

    .supplier-main-content .content-body {
        flex: 1;
        overflow-y: auto;
        padding: 1.8rem 2rem;
    }

    .supplier-main-content .content-body::-webkit-scrollbar {
        width: 6px;
    }
    .supplier-main-content .content-body::-webkit-scrollbar-thumb {
        background: #cbd5e1;
        border-radius: 4px;
    }

    .tab-panel {
        display: none;
        animation: fadeSlide 0.25s ease;
    }

    .tab-panel.active {
        display: block;
    }

    @keyframes fadeSlide {
        from {
            opacity: 0;
            transform: translateY(6px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    /* ===== DESCRIPCIÓN ===== */
    .description-box {
        background: #fff;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        padding: 1.5rem;
        margin-bottom: 1.8rem;
    }

    .description-box .label {
        font-size: 11px;
        font-weight: 600;
        color: #94a3b8;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        display: block;
        margin-bottom: 8px;
    }

    .description-box .text {
        font-size: 14px;
        color: #1e293b;
        line-height: 1.8;
        max-height: 160px;
        overflow-y: auto;
        padding-right: 4px;
    }

    .description-box .text::-webkit-scrollbar {
        width: 4px;
    }
    .description-box .text::-webkit-scrollbar-thumb {
        background: #e2e8f0;
        border-radius: 4px;
    }

    /* ===== SUB-NAV ===== */
    .sub-nav-tabs {
        display: flex;
        gap: 2px;
        background: #fff;
        padding: 4px;
        border-radius: 10px;
        border: 1px solid #e2e8f0;
        margin-bottom: 1.5rem;
        flex-wrap: wrap;
    }

    .sub-nav-tabs button {
        padding: 8px 18px;
        border: none;
        border-radius: 6px;
        font-size: 12px;
        font-weight: 500;
        cursor: pointer;
        transition: all 0.15s;
        background: transparent;
        color: #94a3b8;
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }

    .sub-nav-tabs button:hover {
        color: #0f172a;
        background: #f8fafc;
    }

    .sub-nav-tabs button.active {
        background: #0f172a;
        color: #fff;
    }

    .sub-panel {
        display: none;
        animation: fadeSlide 0.25s ease;
    }

    .sub-panel.active {
        display: block;
    }

    /* ===== GALERÍA ===== */
    .gallery-header-actions {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 1rem;
        flex-wrap: wrap;
        gap: 0.5rem;
    }

    .gallery-header-actions h4 {
        font-size: 14px;
        font-weight: 600;
        color: #0f172a;
        margin: 0;
    }

    .gallery-header-actions h4 i {
        color: #64748b;
    }

    .gallery-header-actions .gallery-counter {
        background: #f1f5f9;
        color: #475569;
        padding: 0 8px;
        border-radius: 4px;
        font-size: 12px;
        font-weight: 500;
        margin-left: 6px;
    }

    .btn-new-image {
        padding: 7px 16px;
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

    .btn-new-image:hover {
        background: #1e293b;
    }

    .gallery-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 12px;
    }

    .gallery-grid .gallery-item {
        position: relative;
        border-radius: 10px;
        overflow: hidden;
        aspect-ratio: 1/1;
        background: #f1f5f9;
        border: 1px solid #e2e8f0;
        transition: all 0.3s ease;
    }

    .gallery-grid .gallery-item .gallery-img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.4s ease;
        cursor: pointer;
    }

    .gallery-grid .gallery-item:hover .gallery-img {
        transform: scale(1.05);
    }

    .gallery-grid .gallery-item:hover {
        border-color: #0f172a;
        box-shadow: 0 4px 16px rgba(0, 0, 0, 0.1);
    }

    .gallery-grid .gallery-item .gallery-overlay {
        position: absolute;
        bottom: 0;
        left: 0;
        right: 0;
        padding: 20px 12px 12px;
        background: linear-gradient(transparent, rgba(0, 0, 0, 0.55));
        color: #fff;
        opacity: 0;
        transition: opacity 0.3s ease;
        display: flex;
        justify-content: space-between;
        align-items: flex-end;
    }

    .gallery-grid .gallery-item:hover .gallery-overlay {
        opacity: 1;
    }

    .gallery-grid .gallery-item .gallery-overlay .view-btn {
        font-size: 12px;
        font-weight: 500;
        display: flex;
        align-items: center;
        gap: 4px;
    }

    .gallery-grid .gallery-item .gallery-overlay .action-btns {
        display: flex;
        gap: 4px;
    }

    .gallery-grid .gallery-item .gallery-overlay .action-btns button {
        background: rgba(255, 255, 255, 0.15);
        border: none;
        color: #fff;
        width: 28px;
        height: 28px;
        border-radius: 6px;
        cursor: pointer;
        font-size: 13px;
        transition: all 0.2s;
        display: flex;
        align-items: center;
        justify-content: center;
        backdrop-filter: blur(4px);
    }

    .gallery-grid .gallery-item .gallery-overlay .action-btns button:hover {
        background: rgba(255, 255, 255, 0.3);
        transform: scale(1.1);
    }

    .gallery-grid .gallery-item .gallery-overlay .action-btns .btn-principal {
        background: rgba(22, 163, 74, 0.7);
    }

    .gallery-grid .gallery-item .gallery-overlay .action-btns .btn-principal:hover {
        background: rgba(22, 163, 74, 0.9);
    }

    .gallery-grid .gallery-item .gallery-overlay .action-btns .btn-delete-img {
        background: rgba(220, 38, 38, 0.7);
    }

    .gallery-grid .gallery-item .gallery-overlay .action-btns .btn-delete-img:hover {
        background: rgba(220, 38, 38, 0.9);
    }

    .gallery-grid .gallery-item .badge-principal-img {
        position: absolute;
        top: 8px;
        left: 8px;
        background: #16a34a;
        color: #fff;
        padding: 2px 10px;
        border-radius: 4px;
        font-size: 10px;
        font-weight: 600;
        z-index: 10;
        display: flex;
        align-items: center;
        gap: 4px;
    }

    .gallery-grid .gallery-item .badge-principal-img i {
        font-size: 12px;
    }

    /* ===== LIGHTBOX ===== */
    .image-lightbox {
        display: none;
        position: fixed;
        inset: 0;
        background: rgba(0, 0, 0, 0.85);
        backdrop-filter: blur(8px);
        z-index: 99999;
        align-items: center;
        justify-content: center;
        padding: 2rem;
    }

    .image-lightbox.show {
        display: flex;
    }

    .image-lightbox .lightbox-content {
        position: relative;
        max-width: 80%;
        max-height: 80%;
        border-radius: 10px;
        overflow: hidden;
        animation: zoomIn 0.3s ease;
    }

    @keyframes zoomIn {
        from {
            transform: scale(0.92);
            opacity: 0;
        }
        to {
            transform: scale(1);
            opacity: 1;
        }
    }

    .image-lightbox .lightbox-content img {
        width: 100%;
        height: 100%;
        object-fit: contain;
        max-height: 80vh;
    }

    .image-lightbox .close-lightbox {
        position: absolute;
        top: -44px;
        right: 0;
        background: none;
        border: none;
        color: #fff;
        font-size: 28px;
        cursor: pointer;
        opacity: 0.6;
        transition: opacity 0.2s;
    }

    .image-lightbox .close-lightbox:hover {
        opacity: 1;
    }

    .image-lightbox .nav-btn {
        position: absolute;
        top: 50%;
        transform: translateY(-50%);
        background: rgba(255, 255, 255, 0.08);
        border: none;
        color: #fff;
        width: 40px;
        height: 40px;
        border-radius: 50%;
        font-size: 20px;
        cursor: pointer;
        transition: all 0.2s;
        backdrop-filter: blur(4px);
        display: flex;
        align-items: center;
        justify-content: center;
        opacity: 0.5;
    }

    .image-lightbox .nav-btn:hover {
        background: rgba(255, 255, 255, 0.18);
        opacity: 1;
    }

    .image-lightbox .nav-btn.prev {
        left: -50px;
    }

    .image-lightbox .nav-btn.next {
        right: -50px;
    }

    .image-lightbox .counter {
        position: absolute;
        bottom: -38px;
        left: 50%;
        transform: translateX(-50%);
        color: rgba(255, 255, 255, 0.5);
        font-size: 13px;
    }

    /* ===== TABLAS ===== */
    .table-container {
        background: #fff;
        border: 1px solid #e2e8f0;
        border-radius: 10px;
        overflow: hidden;
    }

    .table-container table {
        width: 100%;
        border-collapse: collapse;
        font-size: 13px;
    }

    .table-container thead {
        background: #f8fafc;
        border-bottom: 1px solid #e2e8f0;
    }

    .table-container th {
        padding: 12px 16px;
        text-align: left;
        font-weight: 600;
        color: #475569;
        font-size: 11px;
        text-transform: uppercase;
        letter-spacing: 0.4px;
    }

    .table-container td {
        padding: 12px 16px;
        border-bottom: 1px solid #f1f5f9;
        color: #0f172a;
    }

    .table-container tr:last-child td {
        border-bottom: none;
    }

    .table-container tr:hover td {
        background: #fafbfc;
    }

    .badge-principal {
        background: #e8edf4;
        color: #0f172a;
        font-size: 10px;
        font-weight: 600;
        padding: 2px 10px;
        border-radius: 4px;
        text-transform: uppercase;
    }

    .badge-moneda {
        background: #f1f5f9;
        color: #475569;
        padding: 2px 10px;
        border-radius: 4px;
        font-size: 12px;
        font-weight: 500;
    }

    .empty-state {
        text-align: center;
        padding: 3rem 1.5rem;
        color: #94a3b8;
    }

    .empty-state i {
        font-size: 40px;
        display: block;
        margin-bottom: 12px;
        color: #cbd5e1;
    }

    .empty-state p {
        font-size: 15px;
        font-weight: 600;
        color: #0f172a;
        margin-bottom: 4px;
    }

    .empty-state span {
        font-size: 13px;
    }

    /* ===== BOTÓN NUEVO CONTACTO ===== */
    .btn-new-contact {
        padding: 7px 16px;
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

    .btn-new-contact:hover {
        background: #1e293b;
    }

    /* ===== SERVICIOS - ESTILO EZUS (TABLA PLANA) ===== */
    .service-header-actions {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 1.25rem;
        flex-wrap: wrap;
        gap: 0.75rem;
    }

    .service-header-left {
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }

    .service-header-left h3 {
        font-size: 16px;
        font-weight: 600;
        color: #0f172a;
        margin: 0;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .service-header-left h3 i {
        color: #64748b;
    }

    .service-total {
        font-size: 12px;
        color: #94a3b8;
        background: #f1f5f9;
        padding: 2px 12px;
        border-radius: 12px;
        font-weight: 500;
    }

    .btn-new-service {
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
        gap: 8px;
        transition: all 0.2s;
    }

    .btn-new-service:hover {
        background: #1e293b;
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(15, 23, 42, 0.15);
    }

    /* TABLA DE SERVICIOS ESTILO EZUS - PLANA */
    .service-table-wrapper {
        background: #fff;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        overflow: hidden;
    }

    .service-table {
        width: 100%;
        border-collapse: collapse;
        font-size: 13px;
    }

    .service-table thead {
        background: #f8fafc;
        border-bottom: 1px solid #e2e8f0;
    }

    .service-table thead th {
        padding: 12px 16px;
        text-align: left;
        font-weight: 600;
        color: #475569;
        font-size: 11px;
        text-transform: uppercase;
        letter-spacing: 0.4px;
    }

    .service-table tbody tr {
        border-bottom: 1px solid #f1f5f9;
        transition: background 0.15s;
    }

    .service-table tbody tr:last-child {
        border-bottom: none;
    }

    .service-table tbody tr:hover {
        background: #fafbfc;
    }

    .service-table tbody td {
        padding: 12px 16px;
        vertical-align: middle;
        color: #0f172a;
    }

    /* Columna de nombre con icono */
    .service-name-cell {
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .service-name-cell .service-icon-small {
        width: 36px;
        height: 36px;
        border-radius: 8px;
        background: linear-gradient(135deg, #0f172a, #334155);
        color: #fff;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 12px;
        font-weight: 700;
        flex-shrink: 0;
        letter-spacing: 0.5px;
    }

    .service-name-cell .service-name-text {
        font-weight: 600;
        color: #0f172a;
        font-size: 14px;
    }

    .service-name-cell .service-desc-text {
        font-size: 12px;
        color: #94a3b8;
        display: block;
        margin-top: 1px;
    }

    /* Badges en tabla */
    .badge-category-table {
        background: #f1f5f9;
        color: #475569;
        padding: 4px 12px;
        border-radius: 20px;
        font-size: 11px;
        font-weight: 500;
        display: inline-flex;
        align-items: center;
        gap: 4px;
        white-space: nowrap;
    }

    .badge-category-table i {
        font-size: 12px;
        color: #94a3b8;
    }

    .badge-tariff-count {
        background: #f1f5f9;
        color: #0f172a;
        padding: 2px 10px;
        border-radius: 12px;
        font-size: 11px;
        font-weight: 600;
    }

    .badge-tariff-count i {
        font-size: 11px;
        color: #64748b;
        margin-right: 3px;
    }

    /* Botones de acción en tabla */
    .action-buttons {
        display: flex;
        gap: 6px;
        align-items: center;
    }

    .action-buttons .btn-action-table {
        padding: 5px 12px;
        border-radius: 6px;
        font-size: 12px;
        font-weight: 500;
        text-decoration: none;
        border: none;
        cursor: pointer;
        transition: all 0.15s;
        display: inline-flex;
        align-items: center;
        gap: 4px;
    }

    .action-buttons .btn-edit-table {
        background: #e8edf4;
        color: #475569;
        border: 1px solid #e2e8f0;
    }

    .action-buttons .btn-edit-table:hover {
        background: #d1d9e6;
        border-color: #cbd5e1;
    }

    .action-buttons .btn-tariffs-table {
        background: #0f172a;
        color: #fff;
        border: 1px solid #0f172a;
    }

    .action-buttons .btn-tariffs-table:hover {
        background: #1e293b;
        border-color: #1e293b;
    }

    .action-buttons .btn-delete-table {
        background: #fee2e2;
        color: #991b1b;
        border: 1px solid #fecaca;
    }

    .action-buttons .btn-delete-table:hover {
        background: #fecaca;
        border-color: #fca5a5;
    }

    /* ===== PAGINACIÓN ===== */
    .service-pagination {
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 0.75rem;
        padding: 1rem 0.25rem 0;
        margin-top: 0.5rem;
    }

    .service-pagination .info-text {
        font-size: 13px;
        color: #94a3b8;
    }

    .service-pagination .pagination-links {
        display: flex;
        align-items: center;
        gap: 0.3rem;
    }

    .service-pagination .pagination-links a,
    .service-pagination .pagination-links span {
        padding: 0.3rem 0.7rem;
        border-radius: 6px;
        font-size: 13px;
        text-decoration: none;
        min-width: 32px;
        text-align: center;
        transition: all 0.15s;
    }

    .service-pagination .pagination-links .page-link {
        border: 1px solid #e2e8f0;
        color: #475569;
        background: #fff;
    }

    .service-pagination .pagination-links .page-link:hover {
        background: #f1f5f9;
        border-color: #cbd5e1;
    }

    .service-pagination .pagination-links .page-current {
        background: #0f172a;
        color: #fff;
        font-weight: 600;
        border: 1px solid #0f172a;
    }

    .service-pagination .pagination-links .page-disabled {
        border: 1px solid #e2e8f0;
        color: #cbd5e1;
        cursor: default;
        background: #fff;
    }

    .service-pagination .pagination-links .page-dots {
        color: #cbd5e1;
        padding: 0.3rem 0.1rem;
        border: none;
    }

    /* ===== MODALES ===== */
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
    .modal-overlay .modal-box .form-group select,
    .modal-overlay .modal-box .form-group textarea {
        width: 100%;
        padding: 9px 12px;
        border: 1.5px solid #e2e8f0;
        border-radius: 8px;
        font-size: 13px;
        outline: none;
        background: #fff;
        font-family: inherit;
        transition: border-color 0.15s;
    }

    .modal-overlay .modal-box .form-group input:focus,
    .modal-overlay .modal-box .form-group select:focus,
    .modal-overlay .modal-box .form-group textarea:focus {
        border-color: #0f172a;
    }

    .modal-overlay .modal-box .form-group textarea {
        resize: vertical;
        min-height: 80px;
    }

    .modal-overlay .modal-box .form-actions {
        display: flex;
        gap: 8px;
        margin-top: 0.5rem;
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

    .modal-overlay .modal-box .form-actions .btn-submit:disabled {
        opacity: 0.6;
        cursor: not-allowed;
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

    /* ===== PREVISUALIZACIÓN DE IMÁGENES EN MODAL ===== */
    .image-preview-container {
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
        margin-top: 8px;
    }

    .image-preview-container .preview-item {
        position: relative;
        width: 80px;
        height: 80px;
        border-radius: 8px;
        overflow: hidden;
        border: 2px solid #e2e8f0;
        transition: border-color .2s;
    }

    .image-preview-container .preview-item:hover {
        border-color: #0f172a;
    }

    .image-preview-container .preview-item img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .image-preview-container .preview-item .preview-remove {
        position: absolute;
        top: 4px;
        right: 4px;
        width: 20px;
        height: 20px;
        border-radius: 50%;
        background: rgba(15, 23, 42, 0.75);
        color: #fff;
        border: none;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 10px;
        padding: 0;
        transition: all .2s;
        opacity: 0;
    }

    .image-preview-container .preview-item:hover .preview-remove {
        opacity: 1;
    }

    .image-preview-container .preview-item .preview-remove:hover {
        background: #ef4444;
        transform: scale(1.1);
    }

    /* ===== RESPONSIVE ===== */
    @media (max-width: 820px) {
        .supplier-detail-wrapper {
            height: 100%;
            overflow: auto;
        }

        .supplier-header {
            padding: 1rem 1.2rem;
            flex-direction: column;
            align-items: stretch;
            gap: 0.8rem;
        }

        .supplier-header .header-meta {
            gap: 0.8rem;
        }

        .supplier-header .header-actions {
            justify-content: flex-end;
        }

        .supplier-main-content .content-nav {
            padding: 0 1rem;
        }

        .supplier-main-content .content-nav button {
            padding: 10px 14px;
            font-size: 12px;
        }

        .supplier-main-content .content-body {
            padding: 1.2rem 1rem;
        }

        .gallery-grid {
            grid-template-columns: repeat(3, 1fr);
            gap: 8px;
        }

        .image-lightbox .lightbox-content {
            max-width: 95%;
        }

        .image-lightbox .nav-btn.prev {
            left: 8px;
        }

        .image-lightbox .nav-btn.next {
            right: 8px;
        }

        .image-lightbox .nav-btn {
            width: 32px;
            height: 32px;
            font-size: 16px;
        }

        .service-table {
            font-size: 12px;
        }

        .service-table thead th,
        .service-table tbody td {
            padding: 10px 12px;
        }

        .service-name-cell .service-icon-small {
            width: 30px;
            height: 30px;
            font-size: 10px;
        }

        .service-name-cell .service-name-text {
            font-size: 13px;
        }

        .service-pagination {
            flex-direction: column;
            align-items: stretch;
            gap: 0.8rem;
        }

        .service-pagination .pagination-links {
            justify-content: center;
        }

        .service-header-actions {
            flex-direction: column;
            align-items: stretch;
            gap: 0.75rem;
        }

        .service-header-left {
            justify-content: space-between;
        }

        .btn-new-service {
            width: 100%;
            justify-content: center;
        }

        .action-buttons .btn-action-table {
            padding: 4px 10px;
            font-size: 11px;
        }
    }

    @media (max-width: 600px) {
        .service-table thead {
            display: none;
        }

        .service-table tbody tr {
            display: block;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            margin-bottom: 10px;
            background: #fff;
            padding: 8px 0;
        }

        .service-table tbody td {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 6px 12px;
            border-bottom: 1px solid #f1f5f9;
        }

        .service-table tbody td:last-child {
            border-bottom: none;
        }

        .service-table tbody td::before {
            content: attr(data-label);
            font-weight: 600;
            color: #475569;
            font-size: 10px;
            text-transform: uppercase;
            letter-spacing: 0.4px;
        }

        .service-name-cell {
            width: 100%;
        }

        .action-buttons {
            width: 100%;
            justify-content: flex-end;
        }

        .gallery-grid {
            grid-template-columns: repeat(3, 1fr);
            gap: 6px;
        }

        .supplier-main-content .content-nav button {
            padding: 8px 10px;
            font-size: 11px;
            min-width: 50px;
        }

        .supplier-main-content .content-body {
            padding: 1rem 0.8rem;
        }

        .sub-nav-tabs button {
            padding: 6px 10px;
            font-size: 11px;
        }

        .modal-overlay .modal-box {
            padding: 1.5rem 1.2rem;
        }

        .modal-overlay .modal-box .form-actions {
            flex-direction: column;
        }

        .modal-overlay .modal-box .form-actions .btn-cancel {
            width: 100%;
            text-align: center;
        }

        .supplier-header .header-meta {
            flex-direction: column;
            align-items: flex-start;
            gap: 0.4rem;
        }
    }

    /* ===== SPINNER ===== */
    .ti-spin {
        animation: spin 1s linear infinite;
    }

    @keyframes spin {
        from {
            transform: rotate(0deg);
        }
        to {
            transform: rotate(360deg);
        }
    }

    /* ===== ESTILOS PARA EL SELECTOR DINÁMICO ===== */
    .subcategory-loading {
        color: #94a3b8;
        font-size: 12px;
        padding: 4px 0;
        display: flex;
        align-items: center;
        gap: 6px;
    }

    .subcategory-loading .ti-spin {
        font-size: 14px;
    }

    /* ===== BOTONES PARA CREAR CATEGORÍA/SUBCATEGORÍA ===== */
    .btn-add-small {
        padding: 7px 12px;
        background: #1e293b;
        color: #fff;
        border: none;
        border-radius: 8px;
        font-size: 13px;
        font-weight: 500;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 4px;
        transition: all 0.15s;
        white-space: nowrap;
        flex-shrink: 0;
        height: 40px;
    }

    .btn-add-small:hover {
        background: #334155;
        transform: scale(1.02);
    }

    /* ===== CONTADOR DE TARIFAS ===== */
    .tariff-count-badge {
        background: #f1f5f9;
        color: #475569;
        padding: 2px 10px;
        border-radius: 12px;
        font-size: 11px;
        font-weight: 500;
        display: inline-flex;
        align-items: center;
        gap: 4px;
    }

    .tariff-count-badge i {
        font-size: 11px;
        color: #94a3b8;
    }

    .tariff-count-badge.has-tariffs {
        background: #dcfce7;
        color: #166534;
    }

    .tariff-count-badge.has-tariffs i {
        color: #16a34a;
    }

    .tariff-count-badge.no-tariffs {
        background: #fee2e2;
        color: #991b1b;
    }

    .tariff-count-badge.no-tariffs i {
        color: #dc2626;
    }
</style>

{{-- ===== LAYOUT PRINCIPAL ===== --}}
<div class="supplier-detail-wrapper">

    {{-- ===== CABECERA ===== --}}
    <div class="supplier-header">
        <div class="avatar-large">
            {{ strtoupper(substr($supplier->supplier_name, 0, 2)) }}
        </div>

        <div class="header-info">
            <h1>{{ $supplier->supplier_name }}</h1>
            <div class="sub">{{ $supplier->business_name ?? 'Sin razón social' }}</div>
        </div>

        <div class="header-meta">
            <div class="meta-item">
                <i class="ti ti-id"></i>
                <span>{{ $supplier->tax_code ?? '—' }}</span>
            </div>
            <div class="meta-item">
                <i class="ti ti-phone"></i>
                <span>{{ $supplier->general_phone ?? '—' }}</span>
            </div>
            <div class="meta-item">
                <i class="ti ti-mail"></i>
                <span style="font-size:12px">{{ $supplier->general_email ?? '—' }}</span>
            </div>
            <div class="meta-item">
                <i class="ti ti-tag"></i>
                <span>{{ $supplier->category->category_name ?? 'Sin categoría' }}</span>
            </div>
            <div class="meta-item">
                <i class="ti ti-link"></i>
                @if($supplier->chains->count() > 0)
                    @foreach($supplier->chains as $chain)
                        <span class="badge-chain">{{ $chain->name }}</span>
                    @endforeach
                @else
                    <span class="badge-chain independent">Independiente</span>
                @endif
            </div>
            <div class="meta-item">
                <i class="ti ti-calendar"></i>
                <span>{{ $supplier->created_at->format('d/m/Y H:i') }}</span>
            </div>
        </div>

        <div class="header-actions">
            <a href="{{ route('admin.suppliers.edit', $supplier->id_supplier) }}" class="btn btn-edit">
                <i class="ti ti-edit"></i> Editar
            </a>
            <a href="{{ route('admin.suppliers.index') }}" class="btn btn-back">
                <i class="ti ti-arrow-left"></i> Volver
            </a>
        </div>
    </div>

    {{-- ===== CONTENIDO PRINCIPAL ===== --}}
    <div class="supplier-main-content">

        {{-- NAVEGACIÓN PRINCIPAL --}}
        <div class="content-nav">
            <button class="active" data-tab="tab-info">
                <i class="ti ti-info-circle"></i> Información
            </button>
            <button data-tab="tab-services">
                <i class="ti ti-package"></i> Servicios
                <span class="count-badge">{{ $services->total() }}</span>
            </button>
        </div>

        <div class="content-body" style="background-color: transparent;">

            {{-- TAB: INFORMACIÓN --}}

            <div id="tab-info" class="tab-panel active">

                @php
                    $descriptionByLanguage = $supplier->descriptions
                        ->mapWithKeys(fn ($description) => [$description->id_language => $description->description])
                        ->toArray();
                    $defaultLanguage = $languages->firstWhere('code', 'es') ?? $languages->first();
                @endphp
                <div class="description-box" style="margin-top:18px;">
                    <label for="supplierLanguage" class="label">
                        <i class="ti ti-language"></i> Idioma
                    </label>
                    <form method="POST" action="{{ route('admin.suppliers.descriptions.store', $supplier->id_supplier) }}" style="margin-top:12px;">
                        @csrf
                        <div style="display:grid;gap:14px;">
                            <select name="id_language" id="supplierLanguage" required style="height:42px;border:1px solid #dbe3ef;border-radius:8px;padding:0 10px;">
                                @foreach($languages->sortBy(fn ($language) => $language->code === 'es' ? 0 : 1) as $language)
                                    <option value="{{ $language->id_language }}">{{ $language->name }} ({{ strtoupper($language->code) }})</option>
                                @endforeach
                            </select>
                            <label for="supplierDescription" class="label">
                                <i class="ti ti-align-left"></i> Descripción
                            </label>
                            <textarea name="description" id="supplierDescription" rows="5" required maxlength="5000" placeholder="Escribe la descripción en el idioma seleccionado..." style="border:1px solid #dbe3ef;border-radius:8px;padding:10px;resize:vertical;"></textarea>
                            <button type="submit" class="btn btn-edit" style="width:max-content;">
                                <i class="ti ti-device-floppy"></i> Guardar
                            </button>
                        </div>
                    </form>
                </div>

                {{-- SUB-NAVEGACIÓN --}}
                <div class="sub-nav-tabs">
                    <button class="active" data-sub="sub-gallery">
                        <i class="ti ti-photo"></i> Galería
                    </button>
                    <button data-sub="sub-contacts">
                        <i class="ti ti-users"></i> Contactos
                    </button>
                    <button data-sub="sub-banks">
                        <i class="ti ti-credit-card"></i> Bancos
                    </button>
                </div>

                {{-- SUB: GALERÍA --}}
                <div id="sub-gallery" class="sub-panel active">
                    @php
                        $galleryImages = $supplier->images->pluck('image_path')->toArray();
                        $totalImages = count($galleryImages);
                    @endphp

                    <div class="gallery-header-actions">
                        <h4>
                            <i class="ti ti-photo"></i> Galería
                            <span class="gallery-counter">{{ $totalImages }}</span>
                        </h4>
                        <button class="btn-new-image" onclick="openUploadImageModal()">
                            <i class="ti ti-plus"></i> Añadir Imágenes
                        </button>
                    </div>

                    @if($totalImages > 0)
                        <div class="gallery-grid">
                            @foreach($supplier->images as $index => $image)
                                <div class="gallery-item">
                                    <img src="{{ asset('storage/' . $image->image_path) }}"
                                         alt="Imagen {{ $index + 1 }}"
                                         class="gallery-img"
                                         loading="lazy"
                                         onclick="openLightbox({{ $index }})">

                                    @if($image->is_principal)
                                        <span class="badge-principal-img">
                                            <i class="ti ti-star-filled"></i> Principal
                                        </span>
                                    @endif

                                    <div class="gallery-overlay">
                                        <span class="view-btn">
                                            <i class="ti ti-eye"></i> Ver
                                        </span>
                                        <div class="action-btns">
                                            @if(!$image->is_principal)
                                                <button class="btn-principal"
                                                        onclick="event.stopPropagation(); setPrincipalImage({{ $image->id_supplier_image }})"
                                                        title="Establecer como principal">
                                                    <i class="ti ti-star"></i>
                                                </button>
                                            @endif
                                            <button class="btn-delete-img"
                                                    onclick="event.stopPropagation(); confirmDeleteImage({{ $image->id_supplier_image }}, '{{ basename($image->image_path) }}')"
                                                    title="Eliminar imagen">
                                                <i class="ti ti-trash"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        {{-- LIGHTBOX --}}
                        <div class="image-lightbox" id="lightbox" onclick="closeLightbox(event)">
                            <button class="close-lightbox" onclick="closeLightbox()">✕</button>
                            <button class="nav-btn prev" onclick="event.stopPropagation(); navigateLightbox(-1)">‹</button>
                            <div class="lightbox-content" onclick="event.stopPropagation()">
                                <img id="lightboxImage" src="" alt="Imagen ampliada">
                            </div>
                            <button class="nav-btn next" onclick="event.stopPropagation(); navigateLightbox(1)">›</button>
                            <div class="counter" id="lightboxCounter">1 / {{ $totalImages }}</div>
                        </div>
                    @else
                        <div style="text-align:center;padding:3rem;background:#fff;border:1px solid #e2e8f0;border-radius:10px;">
                            <i class="ti ti-photo-off" style="font-size:40px;color:#cbd5e1;display:block;margin-bottom:12px;"></i>
                            <p style="font-size:14px;font-weight:600;color:#0f172a;margin:0;">Sin imágenes</p>
                            <span style="font-size:13px;color:#94a3b8;">Este proveedor aún no tiene imágenes</span>
                        </div>
                    @endif
                </div>

                {{-- SUB: CONTACTOS --}}
                <div id="sub-contacts" class="sub-panel">
                    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:1rem;flex-wrap:wrap;gap:0.5rem;">
                        <h4 style="font-size:14px;font-weight:600;color:#0f172a;margin:0;">
                            <i class="ti ti-users" style="color:#64748b;"></i> Contactos
                            <span style="background:#f1f5f9;color:#475569;padding:0 8px;border-radius:4px;font-size:12px;font-weight:500;margin-left:6px;">
                                {{ $supplier->contacts->count() }}
                            </span>
                        </h4>
                        <button class="btn-new-contact" onclick="openCreateContactModal()">
                            <i class="ti ti-plus"></i> Nuevo Contacto
                        </button>
                    </div>

                    <div class="table-container">
                        <table>
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Nombre</th>
                                    <th>Cargo</th>
                                    <th>Teléfono</th>
                                    <th>Email</th>
                                    <th>Estado</th>
                                    <th style="text-align:center;width:120px;">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($supplier->contacts as $index => $contact)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td><strong>{{ $contact->name }} {{ $contact->last_names }}</strong></td>
                                        <td>{{ $contact->qualification ?? '—' }}</td>
                                        <td>
                                            @if($contact->first_phone)
                                                {{ $contact->first_phone }}
                                                @if($contact->second_phone)
                                                    <br><small style="color:#94a3b8">{{ $contact->second_phone }}</small>
                                                @endif
                                            @else
                                                —
                                            @endif
                                        </td>
                                        <td>{{ $contact->email ?? '—' }}</td>
                                        <td>
                                            @if($contact->es_principal)
                                                <span class="badge-principal">Principal</span>
                                            @else
                                                <span style="color:#94a3b8;font-size:12px">Secundario</span>
                                            @endif
                                        </td>
                                        <td style="text-align:center;">
                                            <div style="display:flex;gap:4px;justify-content:center;">
                                                <button type="button"
                                                        onclick="openEditContactModal({{ $contact->id_contacts }})"
                                                        style="background:none;border:none;color:#6366f1;cursor:pointer;font-size:16px;padding:4px;transition:color .15s;"
                                                        onmouseover="this.style.color='#4f46e5'"
                                                        onmouseout="this.style.color='#6366f1'"
                                                        title="Editar contacto">
                                                    <i class="ti ti-edit"></i>
                                                </button>
                                                <button type="button"
                                                        onclick="confirmDeleteContact({{ $contact->id_contacts }}, '{{ addslashes($contact->name) }}')"
                                                        style="background:none;border:none;color:#dc2626;cursor:pointer;font-size:16px;padding:4px;transition:color .15s;"
                                                        onmouseover="this.style.color='#991b1b'"
                                                        onmouseout="this.style.color='#dc2626'"
                                                        title="Eliminar contacto">
                                                    <i class="ti ti-trash"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7">
                                            <div class="empty-state">
                                                <i class="ti ti-users-minus"></i>
                                                <p>No hay contactos registrados</p>
                                                <span>Este proveedor aún no tiene contactos asociados</span>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- SUB: BANCOS --}}
                <div id="sub-banks" class="sub-panel">
                    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:1rem;flex-wrap:wrap;gap:0.5rem;">
                        <h4 style="font-size:14px;font-weight:600;color:#0f172a;margin:0;">
                            <i class="ti ti-credit-card" style="color:#64748b;"></i> Cuentas Bancarias
                            <span style="background:#f1f5f9;color:#475569;padding:0 8px;border-radius:4px;font-size:12px;font-weight:500;margin-left:6px;">
                                {{ $supplier->bankAccounts->count() }}
                            </span>
                        </h4>
                        <div style="display:flex;gap:8px;flex-wrap:wrap;">
                            <button class="btn-new-contact" onclick="openCreateBankAccountModal()">
                                <i class="ti ti-plus"></i> Nueva Cuenta
                            </button>
                            <button class="btn-new-contact" onclick="openCreateBankModal()" style="background:#1e293b;">
                                <i class="ti ti-building-bank"></i> Nuevo Banco
                            </button>
                        </div>
                    </div>

                    <div class="table-container">
                        <table>
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Banco</th>
                                    <th>Número de Cuenta</th>
                                    <th>CCI</th>
                                    <th>Moneda</th>
                                    <th style="text-align:center;width:120px;">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($supplier->bankAccounts as $index => $account)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td><strong>{{ $account->bank->bank_name ?? 'Banco no especificado' }}</strong></td>
                                        <td>
                                            <span style="font-family:monospace;font-weight:500;color:#0f172a">
                                                {{ $account->account_number }}
                                            </span>
                                        </td>
                                        <td>
                                            @if($account->cci)
                                                <span style="font-family:monospace;font-size:12px;color:#94a3b8">
                                                    {{ $account->cci }}
                                                </span>
                                            @else
                                                —
                                            @endif
                                        </td>
                                        <td>
                                            @if($account->currency)
                                                <span class="badge-moneda">{{ $account->currency }}</span>
                                            @else
                                                —
                                            @endif
                                        </td>
                                        <td style="text-align:center;">
                                            <div style="display:flex;gap:4px;justify-content:center;">
                                                <button type="button"
                                                        onclick="openEditBankAccountModal({{ $account->id_bank_account }})"
                                                        style="background:none;border:none;color:#6366f1;cursor:pointer;font-size:16px;padding:4px;transition:color .15s;"
                                                        onmouseover="this.style.color='#4f46e5'"
                                                        onmouseout="this.style.color='#6366f1'"
                                                        title="Editar cuenta bancaria">
                                                    <i class="ti ti-edit"></i>
                                                </button>
                                                <button type="button"
                                                        onclick="confirmDeleteBankAccount({{ $account->id_bank_account }})"
                                                        style="background:none;border:none;color:#dc2626;cursor:pointer;font-size:16px;padding:4px;transition:color .15s;"
                                                        onmouseover="this.style.color='#991b1b'"
                                                        onmouseout="this.style.color='#dc2626'"
                                                        title="Eliminar cuenta bancaria">
                                                    <i class="ti ti-trash"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6">
                                            <div class="empty-state">
                                                <i class="ti ti-credit-card-off"></i>
                                                <p>No hay cuentas bancarias registradas</p>
                                                <span>Este proveedor aún no tiene cuentas bancarias asociadas</span>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            {{-- ===== TAB: SERVICIOS ===== --}}
            <div id="tab-services" class="tab-panel">
                <div class="service-header-actions">
                    <div class="service-header-left">
                        <h3><i class="ti ti-package"></i> Servicios del Proveedor</h3>
                        <span class="service-total">{{ $services->total() }} servicios</span>
                    </div>
                    <button class="btn-new-service" onclick="openCreateServiceModal()">
                        <i class="ti ti-plus"></i> Nuevo Servicio
                    </button>
                </div>

                @if($services->isEmpty())
                    <div class="empty-state" style="background:#fff;border:1px solid #e2e8f0;border-radius:12px;padding:3rem 1.5rem;">
                        <i class="ti ti-package-off"></i>
                        <p>Este proveedor no tiene servicios registrados</p>
                        <span>Aún no se han agregado servicios para este proveedor</span>
                    </div>
                @else
                    <div class="service-table-wrapper">
                        <table class="service-table">
                            <thead>
                                <tr>
                                    <th style="width:30%;">Nombre</th>
                                    <th style="width:20%;">Categoría</th>
                                    <th style="width:15%;">Mercado</th>
                                    <th style="width:20%;">Fecha Registro</th>
                                    <th style="width:15%;text-align:center;">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($services as $service)
                                    @php
                                        $tariffCount = $service->tariffs->count();
                                    @endphp
                                    <tr>
                                        <td data-label="Nombre">
                                            <a href="{{ route('admin.tariffs.index', $service->id_service) }}"
                                               class="service-name-cell"
                                               style="text-decoration:none;color:inherit;cursor:pointer;">
                                                <div class="service-icon-small">
                                                    {{ strtoupper(substr($service->name_service, 0, 2)) }}
                                                </div>
                                                <div>
                                                    <div class="service-name-text" style="transition:color .15s"
                                                        onmouseover="this.style.color='#6366f1'"
                                                        onmouseout="this.style.color='#0f172a'">
                                                        {{ $service->name_service }}
                                                    </div>
                                                    @if($service->descriptions->isNotEmpty())
                                                    <div style="font-size:11px;color:#64748b;margin-top:4px;">
                                                        @foreach($service->descriptions as $description)
                                                            @if($description->description)
                                                                <span title="{{ $description->description }}">
                                                                    {{ $description->language?->name }}: {{ \Illuminate\Support\Str::limit($description->description, 75) }}
                                                                </span>@if(!$loop->last) <span style="margin:0 5px;">·</span> @endif
                                                            @endif
                                                        @endforeach
                                                    </div>
                                                    @endif
                                                </div>
                                            </a>
                                        </td>
                                        <td data-label="Categoría">
                                            <span class="badge-category-table">
                                                <i class="ti ti-tag"></i>
                                                {{ $service->category?->name ?? 'Sin categoría' }}
                                            </span>
                                        </td>
                                           <td data-label="Fecha Registro">
                                            {{ $service->labels->name_labels ?? '-' }}
                                        </td>
                                        <td data-label="Fecha Registro">
                                            {{ $service->created_at->format('d/m/Y') }}
                                        </td>
                                        <td data-label="Acciones">
                                            <div class="action-buttons" style="justify-content:center;">
                                               
                                                <a href="{{ route('admin.services.edit', $service->id_service) }}" 
                                                   class="btn-action-table btn-edit-table"
                                                   title="Editar servicio">
                                                    <i class="ti ti-edit"></i>
                                                </a>
                                                <button class="btn-action-table btn-delete-table" 
                                                        onclick="confirmDeleteService({{ $service->id_service }}, '{{ addslashes($service->name_service) }}')"
                                                        title="Eliminar servicio">
                                                    <i class="ti ti-trash"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    {{-- PAGINACIÓN --}}
                    <div class="service-pagination">
                        <span class="info-text">
                            Mostrando {{ $services->firstItem() }}–{{ $services->lastItem() }} de {{ $services->total() }} servicio(s)
                        </span>
                        <div class="pagination-links">
                            @if($services->onFirstPage())
                                <span class="page-disabled"><i class="ti ti-chevron-left"></i></span>
                            @else
                                <a href="{{ $services->previousPageUrl() }}" class="page-link"><i class="ti ti-chevron-left"></i></a>
                            @endif

                            @php
                                $current = $services->currentPage();
                                $last = $services->lastPage();
                                $range = 2;
                            @endphp

                            @for($i = 1; $i <= $last; $i++)
                                @if($i == 1 || $i == $last || abs($i - $current) <= $range)
                                    @if($i == $current)
                                        <span class="page-current">{{ $i }}</span>
                                    @else
                                        <a href="{{ $services->url($i) }}" class="page-link">{{ $i }}</a>
                                    @endif
                                @elseif(abs($i - $current) == $range + 1)
                                    <span class="page-dots">…</span>
                                @endif
                            @endfor

                            @if($services->hasMorePages())
                                <a href="{{ $services->nextPageUrl() }}" class="page-link"><i class="ti ti-chevron-right"></i></a>
                            @else
                                <span class="page-disabled"><i class="ti ti-chevron-right"></i></span>
                            @endif
                        </div>
                    </div>
                @endif
            </div>

        </div>{{-- /content-body --}}
    </div>{{-- /supplier-main-content --}}
</div>

<div class="modal-overlay" id="uploadImageModal">
    <div class="modal-box" style="max-width:540px;">
        <button class="modal-close-btn" onclick="closeUploadImageModal()">✕</button>

        <h3><i class="ti ti-photo-plus" style="color:#64748b;"></i> Subir Imágenes</h3>
        <p class="modal-sub">Selecciona una o más imágenes para este proveedor</p>

        <form id="uploadImageForm" action="{{ route('admin.suppliers.images.upload', $supplier->id_supplier) }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="form-group">
                <label>Imágenes <span class="required">*</span></label>
                <input type="file" name="images[]" id="uploadImagesInput" multiple accept="image/*" required>
                <div style="font-size:11px;color:#94a3b8;margin-top:4px;">
                    <i class="ti ti-info-circle"></i> JPG, PNG, WebP. Máx 2MB por imagen.
                </div>
            </div>

            <div id="uploadPreviewContainer" class="image-preview-container"></div>

            <div class="form-actions" style="margin-top:1.2rem;">
                <button type="submit" class="btn-submit" id="uploadImageBtn">
                    <i class="ti ti-cloud-upload"></i> Subir Imágenes
                </button>
                <button type="button" class="btn-cancel" onclick="closeUploadImageModal()">Cancelar</button>
            </div>

            <div class="form-message" id="uploadImageMessage"></div>
        </form>
    </div>
</div>

{{-- ===== MODAL CREAR CONTACTO ===== --}}
<div class="modal-overlay" id="createContactModal">
    <div class="modal-box">
        <button class="modal-close-btn" onclick="closeCreateContactModal()">✕</button>

        <h3><i class="ti ti-user-plus" style="color:#64748b;"></i> Nuevo Contacto</h3>
        <p class="modal-sub">Registra un nuevo contacto para este proveedor</p>

        <form id="createContactForm" action="{{ route('admin.contacts.store') }}" method="POST" target="_top">
            @csrf
            <input type="hidden" name="id_supplier" value="{{ $supplier->id_supplier }}">

            <div style="display:grid;grid-template-columns:1fr 1fr;gap:0.8rem;">
                <div class="form-group" style="margin-bottom:0;">
                    <label>Nombre <span class="required">*</span></label>
                    <input type="text" name="name" id="contactName" required
                           placeholder="Ej: Juan">
                </div>
                <div class="form-group" style="margin-bottom:0;">
                    <label>Apellidos <span class="required">*</span></label>
                    <input type="text" name="last_names" id="contactLastNames" required
                           placeholder="Ej: Pérez">
                </div>
            </div>

            <div class="form-group">
                <label>Cargo</label>
                <input type="text" name="qualification"
                       placeholder="Ej: Gerente de Ventas">
            </div>

            <div style="display:grid;grid-template-columns:1fr 1fr;gap:0.8rem;">
                <div class="form-group" style="margin-bottom:0;">
                    <label>Teléfono 1</label>
                    <input type="text" name="first_phone"
                           placeholder="Ej: +51 987654321">
                </div>
                <div class="form-group" style="margin-bottom:0;">
                    <label>Teléfono 2</label>
                    <input type="text" name="second_phone"
                           placeholder="Ej: +51 987654322">
                </div>
            </div>

            <div class="form-group">
                <label>Email</label>
                <input type="email" name="email"
                       placeholder="Ej: juan.perez@email.com">
            </div>

            <div class="form-group" style="margin-bottom:0;">
                <label style="display:flex;align-items:center;gap:8px;cursor:pointer;">
                    <input type="checkbox" name="es_principal" value="1"
                           style="width:16px;height:16px;accent-color:#0f172a;cursor:pointer;">
                    <span style="font-size:12px;text-transform:none;color:#0f172a;font-weight:500;">
                        Marcar como contacto principal
                    </span>
                </label>
            </div>

            <div class="form-actions" style="margin-top:1.2rem;">
                <button type="submit" class="btn-submit" id="submitContactBtn">
                    <i class="ti ti-device-floppy"></i> Guardar Contacto
                </button>
                <button type="button" class="btn-cancel" onclick="closeCreateContactModal()">Cancelar</button>
            </div>

            <div class="form-message" id="contactFormMessage"></div>
        </form>
    </div>
</div>

{{-- ===== MODAL EDITAR CONTACTO ===== --}}
<div class="modal-overlay" id="editContactModal">
    <div class="modal-box">
        <button class="modal-close-btn" onclick="closeEditContactModal()">✕</button>

        <h3><i class="ti ti-user-edit" style="color:#64748b;"></i> Editar Contacto</h3>
        <p class="modal-sub">Actualiza la información del contacto</p>

        <form id="editContactForm" method="POST" target="_top">
            @csrf
            @method('PUT')
            <input type="hidden" name="id_supplier" value="{{ $supplier->id_supplier }}">

            <div style="display:grid;grid-template-columns:1fr 1fr;gap:0.8rem;">
                <div class="form-group" style="margin-bottom:0;">
                    <label>Nombre <span class="required">*</span></label>
                    <input type="text" name="name" id="editContactName" required
                           placeholder="Ej: Juan">
                </div>
                <div class="form-group" style="margin-bottom:0;">
                    <label>Apellidos <span class="required">*</span></label>
                    <input type="text" name="last_names" id="editContactLastNames" required
                           placeholder="Ej: Pérez">
                </div>
            </div>

            <div class="form-group">
                <label>Cargo</label>
                <input type="text" name="qualification" id="editContactQualification"
                       placeholder="Ej: Gerente de Ventas">
            </div>

            <div style="display:grid;grid-template-columns:1fr 1fr;gap:0.8rem;">
                <div class="form-group" style="margin-bottom:0;">
                    <label>Teléfono 1</label>
                    <input type="text" name="first_phone" id="editContactFirstPhone"
                           placeholder="Ej: +51 987654321">
                </div>
                <div class="form-group" style="margin-bottom:0;">
                    <label>Teléfono 2</label>
                    <input type="text" name="second_phone" id="editContactSecondPhone"
                           placeholder="Ej: +51 987654322">
                </div>
            </div>

            <div class="form-group">
                <label>Email</label>
                <input type="email" name="email" id="editContactEmail"
                       placeholder="Ej: juan.perez@email.com">
            </div>

            <div class="form-group" style="margin-bottom:0;">
                <label style="display:flex;align-items:center;gap:8px;cursor:pointer;">
                    <input type="checkbox" name="es_principal" id="editContactPrincipal" value="1"
                           style="width:16px;height:16px;accent-color:#0f172a;cursor:pointer;">
                    <span style="font-size:12px;text-transform:none;color:#0f172a;font-weight:500;">
                        Marcar como contacto principal
                    </span>
                </label>
            </div>

            <div class="form-actions" style="margin-top:1.2rem;">
                <button type="submit" class="btn-submit" id="submitEditContactBtn">
                    <i class="ti ti-device-floppy"></i> Actualizar Contacto
                </button>
                <button type="button" class="btn-cancel" onclick="closeEditContactModal()">Cancelar</button>
            </div>

            <div class="form-message" id="editContactFormMessage"></div>
        </form>
    </div>
</div>

{{-- ===== MODAL CREAR NUEVO BANCO ===== --}}
<div class="modal-overlay" id="createBankModal">
    <div class="modal-box" style="max-width:420px;">
        <button class="modal-close-btn" onclick="closeCreateBankModal()">✕</button>

        <h3><i class="ti ti-building-bank" style="color:#64748b;"></i> Nuevo Banco</h3>
        <p class="modal-sub">Registra un nuevo banco para usar en las cuentas bancarias</p>

        <form id="createBankForm" method="POST" target="_top">
            @csrf
            <div class="form-group">
                <label>Nombre del Banco <span class="required">*</span></label>
                <input type="text" name="bank_name" id="bankNameInput" required
                       placeholder="Ej: Banco de Crédito"
                       maxlength="50">
                <div style="font-size:11px;color:#94a3b8;margin-top:4px;">
                    <i class="ti ti-info-circle"></i> Máximo 50 caracteres
                </div>
            </div>

            <div class="form-actions" style="margin-top:1.2rem;">
                <button type="submit" class="btn-submit" id="submitBankBtn">
                    <i class="ti ti-device-floppy"></i> Guardar Banco
                </button>
                <button type="button" class="btn-cancel" onclick="closeCreateBankModal()">Cancelar</button>
            </div>

            <div class="form-message" id="bankFormMessage"></div>
        </form>
    </div>
</div>

{{-- ===== MODAL CREAR CUENTA BANCARIA ===== --}}
<div class="modal-overlay" id="createBankAccountModal">
    <div class="modal-box">
        <button class="modal-close-btn" onclick="closeCreateBankAccountModal()">✕</button>

        <h3><i class="ti ti-credit-card-plus" style="color:#64748b;"></i> Nueva Cuenta Bancaria</h3>
        <p class="modal-sub">Asocia una cuenta bancaria a este proveedor</p>

        <form id="createBankAccountForm" method="POST" target="_top">
            @csrf
            <input type="hidden" name="id_supplier" value="{{ $supplier->id_supplier }}">

            <div class="form-group">
                <label>Banco <span class="required">*</span></label>
                <select name="id_bank" id="bankAccountBankSelect" required>
                    <option value="">Seleccionar banco</option>
                    @foreach($banks ?? [] as $bank)
                        <option value="{{ $bank->id_bank }}">{{ $bank->bank_name }}</option>
                    @endforeach
                </select>
                <div style="font-size:11px;color:#94a3b8;margin-top:4px;">
                    <i class="ti ti-info-circle"></i> ¿No encuentras el banco?
                    <a href="#" onclick="event.preventDefault(); closeCreateBankAccountModal(); openCreateBankModal();"
                       style="color:#0f172a;font-weight:600;text-decoration:underline;cursor:pointer;">
                        Crea uno nuevo
                    </a>
                </div>
            </div>

            <div class="form-group">
                <label>Número de Cuenta <span class="required">*</span></label>
                <input type="text" name="account_number" id="accountNumberInput" required
                       placeholder="Ej: 12345678901234567890"
                       maxlength="100">
            </div>

            <div class="form-group">
                <label>CCI (Código de Cuenta Interbancaria)</label>
                <input type="text" name="cci" id="cciInput"
                       placeholder="Ej: 123456789012345678901234"
                       maxlength="100">
            </div>

            <div class="form-group">
                <label>Moneda</label>
                <select name="currency" id="currencyInput">
                    <option value="">Seleccionar moneda</option>
                    <option value="PEN">PEN - Sol Peruano</option>
                    <option value="USD">USD - Dólar Americano</option>
                    <option value="EUR">EUR - Euro</option>
                </select>
            </div>

            <div class="form-actions" style="margin-top:1.2rem;">
                <button type="submit" class="btn-submit" id="submitBankAccountBtn">
                    <i class="ti ti-device-floppy"></i> Guardar Cuenta
                </button>
                <button type="button" class="btn-cancel" onclick="closeCreateBankAccountModal()">Cancelar</button>
            </div>

            <div class="form-message" id="bankAccountFormMessage"></div>
        </form>
    </div>
</div>

{{-- ===== MODAL EDITAR CUENTA BANCARIA ===== --}}
<div class="modal-overlay" id="editBankAccountModal">
    <div class="modal-box">
        <button class="modal-close-btn" onclick="closeEditBankAccountModal()">✕</button>

        <h3><i class="ti ti-credit-card-edit" style="color:#64748b;"></i> Editar Cuenta Bancaria</h3>
        <p class="modal-sub">Actualiza la información de la cuenta bancaria</p>

        <form id="editBankAccountForm" method="POST">
            @csrf
            @method('PUT')
            <input type="hidden" name="id_supplier" value="{{ $supplier->id_supplier }}">
            <input type="hidden" id="editBankAccountId" name="id_bank_account" value="">

            <div class="form-group">
                <label>Banco <span class="required">*</span></label>
                <select name="id_bank" id="editBankAccountBankSelect" required>
                    <option value="">Seleccionar banco</option>
                    @foreach($banks ?? [] as $bank)
                        <option value="{{ $bank->id_bank }}">{{ $bank->bank_name }}</option>
                    @endforeach
                </select>
                <div style="font-size:11px;color:#94a3b8;margin-top:4px;">
                    <i class="ti ti-info-circle"></i> ¿No encuentras el banco?
                    <a href="#" onclick="event.preventDefault(); closeEditBankAccountModal(); openCreateBankModal();"
                       style="color:#0f172a;font-weight:600;text-decoration:underline;cursor:pointer;">
                        Crea uno nuevo
                    </a>
                </div>
            </div>

            <div class="form-group">
                <label>Número de Cuenta <span class="required">*</span></label>
                <input type="text" name="account_number" id="editAccountNumber" required
                       placeholder="Ej: 12345678901234567890"
                       maxlength="100">
            </div>

            <div class="form-group">
                <label>CCI (Código de Cuenta Interbancaria)</label>
                <input type="text" name="cci" id="editCci"
                       placeholder="Ej: 123456789012345678901234"
                       maxlength="100">
            </div>

            <div class="form-group">
                <label>Moneda</label>
                <select name="currency" id="editCurrency">
                    <option value="">Seleccionar moneda</option>
                    <option value="PEN">PEN - Sol Peruano</option>
                    <option value="USD">USD - Dólar Americano</option>
                    <option value="EUR">EUR - Euro</option>
                </select>
            </div>

            <div class="form-actions" style="margin-top:1.2rem;">
                <button type="submit" class="btn-submit" id="submitEditBankAccountBtn">
                    <i class="ti ti-device-floppy"></i> Actualizar Cuenta
                </button>
                <button type="button" class="btn-cancel" onclick="closeEditBankAccountModal()">Cancelar</button>
            </div>

            <div class="form-message" id="editBankAccountFormMessage"></div>
        </form>
    </div>
</div>

{{-- ===== MODAL CREAR SERVICIO ===== --}}
<div class="modal-overlay" id="createServiceModal">
    <div class="modal-box">
        <button class="modal-close-btn" onclick="closeCreateServiceModal()">✕</button>

        <h3><i class="ti ti-plus" style="color:#64748b;"></i> Nuevo Servicio</h3>
        <p class="modal-sub">Registra un nuevo servicio para este proveedor</p>

        <form id="createServiceForm" action="{{ route('admin.services.store') }}" method="POST">
            @csrf
            <input type="hidden" name="id_supplier" value="{{ $supplier->id_supplier }}">

            <div class="form-group">
                <label>Nombre del Servicio <span class="required">*</span></label>
                <input type="text" name="name_service" id="serviceName" required
                       placeholder="Ej: Noche de Hospedaje, Tour Machu Picchu">
            </div>

            {{-- CATEGORÍA --}}
            <div class="form-group">
                <label>Categoría <span class="required">*</span></label>
                <div style="display:flex;gap:8px;">
                    <select name="id_category" id="serviceCategorySelect" required style="flex:1;">
                        <option value="">Seleccionar categoría</option>
                        @foreach($categories ?? [] as $category)
                            <option value="{{ $category->id_category }}" data-pricing-type="{{ $category->pricing_type }}">
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                    <button type="button" class="btn-add-small" onclick="openCreateCategoryModal()" title="Crear nueva categoría">
                        <i class="ti ti-plus"></i>
                    </button>
                </div>
            </div>

            <div class="form-group">
                <label>Label / Idioma</label>
                <select name="id_labels" id="serviceLabelsSelect" class="form-control">
                    @foreach($labels ?? [] as $label)
                        <option value="{{ $label->id_labels }}">{{ $label->name_labels }}</option>
                    @endforeach
                </select>
                <div style="font-size:11px;color:#94a3b8;margin-top:4px;">
                    <i class="ti ti-tag"></i> Etiqueta para identificar el servicio (ej: Español, Inglés, etc.)
                </div>
            </div>

            {{-- PRICING TYPE (oculto, se llena desde la categoría) --}}
            <input type="hidden" name="pricing_type" id="servicePricingType" value="">

            <div class="form-group">
                <label>Descripciones por idioma</label>
                @foreach($languages ?? [] as $language)
                    <textarea name="descriptions[{{ $language->id_language }}]" rows="2" style="margin-top:8px;" placeholder="{{ $language->name }}"></textarea>
                @endforeach
            </div>

            <div class="form-actions">
                <button type="submit" class="btn-submit" id="submitServiceBtn">
                    <i class="ti ti-device-floppy"></i> Guardar Servicio
                </button>
                <button type="button" class="btn-cancel" onclick="closeCreateServiceModal()">Cancelar</button>
            </div>

            <div class="form-message" id="serviceFormMessage"></div>
        </form>
    </div>
</div>

{{-- ===== MODAL CREAR NUEVA CATEGORÍA ===== --}}
<div class="modal-overlay" id="createCategoryModal">
    <div class="modal-box" style="max-width:420px;">
        <button class="modal-close-btn" onclick="closeCreateCategoryModal()">✕</button>

        <h3><i class="ti ti-tag" style="color:#64748b;"></i> Nueva Categoría</h3>
        <p class="modal-sub">Registra una nueva categoría para los servicios</p>

        <form id="createCategoryForm">
            @csrf
            <div class="form-group">
                <label>Nombre de la Categoría <span class="required">*</span></label>
                <input type="text" name="name" id="categoryNameInput" required
                       placeholder="Ej: Hospedaje, Excursiones, Traslados"
                       maxlength="50">
            </div>

            <div class="form-group" style="margin-bottom:0;">
                <label style="display:flex;align-items:center;gap:8px;cursor:pointer;">
                    <input type="checkbox" name="is_accommodation" value="1"
                           style="width:16px;height:16px;accent-color:#0f172a;cursor:pointer;">
                    <span style="font-size:12px;text-transform:none;color:#0f172a;font-weight:500;">
                        Es categoría de hospedaje (hoteles)
                    </span>
                </label>
            </div>

            <div class="form-actions" style="margin-top:1.2rem;">
                <button type="submit" class="btn-submit" id="submitCategoryBtn">
                    <i class="ti ti-device-floppy"></i> Guardar Categoría
                </button>
                <button type="button" class="btn-cancel" onclick="closeCreateCategoryModal()">Cancelar</button>
            </div>

            <div class="form-message" id="categoryFormMessage"></div>
        </form>
    </div>
</div>

{{-- ===== MODAL CREAR NUEVA SUBCATEGORÍA (para TARIFAS) ===== --}}
<div class="modal-overlay" id="createSubcategoryModal">
    <div class="modal-box" style="max-width:420px;">
        <button class="modal-close-btn" onclick="closeCreateSubcategoryModal()">✕</button>

        <h3><i class="ti ti-folder-plus" style="color:#64748b;"></i> Nueva Subcategoría</h3>
        <p class="modal-sub">Registra una nueva subcategoría/modalidad para las TARIFAS</p>

        <form id="createSubcategoryForm">
            @csrf
            <div class="form-group">
                <label>Categoría <span class="required">*</span></label>
                <select name="id_category" id="subcategoryCategorySelect" required>
                    <option value="">Seleccionar categoría</option>
                    @foreach($categories ?? [] as $category)
                        <option value="{{ $category->id_category }}">{{ $category->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="form-group">
                <label>Nombre de la Subcategoría <span class="required">*</span></label>
                <input type="text" name="name" id="subcategoryNameInput" required
                       placeholder="Ej: SGL, DBL, VIP, Regular, Sprinter"
                       maxlength="300">
                <div style="font-size:11px;color:#94a3b8;margin-top:4px;">
                    <i class="ti ti-info-circle"></i> 
                    Para hoteles: SGL, DBL, TPL. Para tours: VIP, Regular, Privado. Para transporte: Sedán, Van, Sprinter.
                </div>
            </div>

            <div class="form-actions" style="margin-top:1.2rem;">
                <button type="submit" class="btn-submit" id="submitSubcategoryBtn">
                    <i class="ti ti-device-floppy"></i> Guardar Subcategoría
                </button>
                <button type="button" class="btn-cancel" onclick="closeCreateSubcategoryModal()">Cancelar</button>
            </div>

            <div class="form-message" id="subcategoryFormMessage"></div>
        </form>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
const CSRF_TOKEN = '{{ csrf_token() }}';
const LIGHTBOX_IMAGES = @json($galleryImages);
const SUPPLIER_DESCRIPTIONS = @json($descriptionByLanguage);
const DEFAULT_SUPPLIER_LANGUAGE = @json($defaultLanguage?->id_language);

function loadSupplierDescription() {
    const language = document.getElementById('supplierLanguage').value;
    document.getElementById('supplierDescription').value = SUPPLIER_DESCRIPTIONS[language] || '';
}

document.getElementById('supplierLanguage')?.addEventListener('change', loadSupplierDescription);
document.addEventListener('DOMContentLoaded', () => {
    if (DEFAULT_SUPPLIER_LANGUAGE) {
        document.getElementById('supplierLanguage').value = DEFAULT_SUPPLIER_LANGUAGE;
        loadSupplierDescription();
    }
});

// ================================================================
// GALERÍA - LIGHTBOX
// ================================================================
let currentImageIndex = 0;

function openLightbox(index) {
    if (LIGHTBOX_IMAGES.length === 0) return;
    currentImageIndex = index;
    const modal = document.getElementById('lightbox');
    const img = document.getElementById('lightboxImage');
    const counter = document.getElementById('lightboxCounter');
    img.src = '{{ asset('storage') }}/' + LIGHTBOX_IMAGES[currentImageIndex];
    counter.textContent = `${currentImageIndex + 1} / ${LIGHTBOX_IMAGES.length}`;
    modal.classList.add('show');
    document.body.style.overflow = 'hidden';
}

function closeLightbox(event) {
    if (event && event.target !== event.currentTarget &&
        !event.target.closest('.lightbox-content') &&
        !event.target.closest('.nav-btn')) return;
    document.getElementById('lightbox').classList.remove('show');
    document.body.style.overflow = '';
}

function navigateLightbox(direction) {
    if (LIGHTBOX_IMAGES.length === 0) return;
    currentImageIndex = (currentImageIndex + direction + LIGHTBOX_IMAGES.length) % LIGHTBOX_IMAGES.length;
    const img = document.getElementById('lightboxImage');
    const counter = document.getElementById('lightboxCounter');
    img.src = '{{ asset('storage') }}/' + LIGHTBOX_IMAGES[currentImageIndex];
    counter.textContent = `${currentImageIndex + 1} / ${LIGHTBOX_IMAGES.length}`;
}

// ================================================================
// SUBIR IMÁGENES - MODAL
// ================================================================
let selectedUploadFiles = [];

function openUploadImageModal() {
    document.getElementById('uploadImageModal').classList.add('show');
    document.body.style.overflow = 'hidden';
    document.getElementById('uploadImageForm').reset();
    document.getElementById('uploadImageMessage').className = 'form-message';
    document.getElementById('uploadImageMessage').textContent = '';
    selectedUploadFiles = [];
    renderUploadPreviews();
}

function closeUploadImageModal() {
    document.getElementById('uploadImageModal').classList.remove('show');
    document.body.style.overflow = '';
}

document.getElementById('uploadImagesInput').addEventListener('change', function(e) {
    if (this.files && this.files.length > 0) {
        selectedUploadFiles = Array.from(this.files);
        renderUploadPreviews();
    }
});

function renderUploadPreviews() {
    const container = document.getElementById('uploadPreviewContainer');
    container.innerHTML = '';
    selectedUploadFiles.forEach((file, index) => {
        const reader = new FileReader();
        reader.onload = function(e) {
            const div = document.createElement('div');
            div.className = 'preview-item';
            div.innerHTML = `
                <img src="${e.target.result}" alt="Vista previa ${index + 1}">
                <button type="button" class="preview-remove" onclick="removeUploadFile(${index})">
                    <i class="ti ti-x"></i>
                </button>
            `;
            container.appendChild(div);
        };
        reader.readAsDataURL(file);
    });
}

function removeUploadFile(index) {
    selectedUploadFiles.splice(index, 1);
    const input = document.getElementById('uploadImagesInput');
    const dataTransfer = new DataTransfer();
    selectedUploadFiles.forEach(file => dataTransfer.items.add(file));
    input.files = dataTransfer.files;
    renderUploadPreviews();
}

document.getElementById('uploadImageForm').addEventListener('submit', function(e) {
    const btn = document.getElementById('uploadImageBtn');
    btn.disabled = true;
    btn.innerHTML = '<i class="ti ti-loader ti-spin"></i> Subiendo...';
});

// ================================================================
// ELIMINAR IMAGEN
// ================================================================
function confirmDeleteImage(imageId, fileName) {
    Swal.fire({
        title: '¿Eliminar imagen?',
        html: `Estás a punto de eliminar la imagen <strong>${fileName}</strong>.<br>Esta acción no se puede deshacer.`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#991b1b',
        cancelButtonColor: '#94a3b8',
        confirmButtonText: 'Sí, eliminar',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = `/proveedores/images/${imageId}`;
            form.innerHTML = `
                <input type="hidden" name="_token" value="${CSRF_TOKEN}">
                <input type="hidden" name="_method" value="DELETE">
            `;
            document.body.appendChild(form);
            form.submit();
        }
    });
}

// ================================================================
// ESTABLECER IMAGEN PRINCIPAL
// ================================================================
function setPrincipalImage(imageId) {
    Swal.fire({
        title: '¿Establecer como principal?',
        text: 'Esta imagen será la principal del proveedor.',
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#0f172a',
        cancelButtonColor: '#94a3b8',
        confirmButtonText: 'Sí, establecer',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = `/proveedores/images/${imageId}/principal`;
            form.innerHTML = `
                <input type="hidden" name="_token" value="${CSRF_TOKEN}">
            `;
            document.body.appendChild(form);
            form.submit();
        }
    });
}

// ================================================================
// NAVEGACIÓN PRINCIPAL
// ================================================================
document.querySelectorAll('.content-nav button').forEach(btn => {
    btn.addEventListener('click', function() {
        document.querySelectorAll('.content-nav button').forEach(b => b.classList.remove('active'));
        this.classList.add('active');
        document.querySelectorAll('.tab-panel').forEach(p => p.classList.remove('active'));
        document.getElementById(this.dataset.tab).classList.add('active');

        const url = new URL(window.location);
        url.searchParams.set('tab', this.dataset.tab);
        window.history.replaceState({}, '', url);
    });
});

document.querySelectorAll('.sub-nav-tabs button').forEach(btn => {
    btn.addEventListener('click', function() {
        document.querySelectorAll('.sub-nav-tabs button').forEach(b => b.classList.remove('active'));
        this.classList.add('active');
        document.querySelectorAll('.sub-panel').forEach(p => p.classList.remove('active'));
        document.getElementById(this.dataset.sub).classList.add('active');
    });
});

function confirmDeleteService(id, name) {
    Swal.fire({
        title: '¿Eliminar servicio?',
        html: `Estás a punto de eliminar <strong>${name}</strong>.<br>Esta acción no se puede deshacer.`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#991b1b',
        cancelButtonColor: '#94a3b8',
        confirmButtonText: 'Sí, eliminar',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = `/servicios/${id}`;
            form.innerHTML = `
                <input type="hidden" name="_token" value="${CSRF_TOKEN}">
                <input type="hidden" name="_method" value="DELETE">
            `;
            document.body.appendChild(form);
            form.submit();
        }
    });
}

function openCreateServiceModal() {
    document.getElementById('createServiceModal').classList.add('show');
    document.body.style.overflow = 'hidden';
    document.getElementById('createServiceForm').reset();
    document.getElementById('serviceFormMessage').className = 'form-message';
    document.getElementById('serviceFormMessage').textContent = '';
    document.getElementById('servicePricingType').value = '';
}

function closeCreateServiceModal() {
    document.getElementById('createServiceModal').classList.remove('show');
    document.body.style.overflow = '';
}

// ================================================================
// CREAR NUEVA CATEGORÍA
// ================================================================
function openCreateCategoryModal() {
    document.getElementById('createCategoryModal').classList.add('show');
    document.body.style.overflow = 'hidden';
    document.getElementById('createCategoryForm').reset();
    const message = document.getElementById('categoryFormMessage');
    message.className = 'form-message';
    message.textContent = '';
    document.getElementById('categoryNameInput').focus();
}

function closeCreateCategoryModal() {
    document.getElementById('createCategoryModal').classList.remove('show');
    document.body.style.overflow = '';
}

document.getElementById('createCategoryForm')?.addEventListener('submit', function(e) {
    e.preventDefault();

    const btn = document.getElementById('submitCategoryBtn');
    const message = document.getElementById('categoryFormMessage');

    btn.disabled = true;
    btn.innerHTML = '<i class="ti ti-loader ti-spin"></i> Guardando...';
    message.className = 'form-message';
    message.textContent = '';

    const formData = new FormData(this);

    fetch('{{ route("admin.services.category.store") }}', {
        method: 'POST',
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'X-CSRF-TOKEN': CSRF_TOKEN
        },
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        btn.disabled = false;
        btn.innerHTML = '<i class="ti ti-device-floppy"></i> Guardar Categoría';

        if (data.success) {
            message.className = 'form-message success';
            message.textContent = '✓ Categoría creada exitosamente';

            const select = document.getElementById('serviceCategorySelect');
            const option = document.createElement('option');
            option.value = data.category.id;
            option.textContent = data.category.name;
            option.dataset.pricingType = data.category.pricing_type || 'flat';
            select.appendChild(option);
            select.value = data.category.id;

            document.getElementById('servicePricingType').value = data.category.pricing_type || 'flat';

            const subCategorySelect = document.getElementById('subcategoryCategorySelect');
            const option2 = document.createElement('option');
            option2.value = data.category.id;
            option2.textContent = data.category.name;
            subCategorySelect.appendChild(option2);

            setTimeout(() => {
                closeCreateCategoryModal();
            }, 600);
        } else {
            message.className = 'form-message error';
            message.textContent = '✗ ' + (data.message || 'Error al crear la categoría');
        }
    })
    .catch(error => {
        btn.disabled = false;
        btn.innerHTML = '<i class="ti ti-device-floppy"></i> Guardar Categoría';
        message.className = 'form-message error';
        message.textContent = '✗ Error de conexión. Intenta nuevamente.';
        console.error('Error:', error);
    });
});

// ================================================================
// CREAR NUEVA SUBCATEGORÍA (para TARIFAS)
// ================================================================
function openCreateSubcategoryModal() {
    const categorySelect = document.getElementById('serviceCategorySelect');
    if (!categorySelect.value) {
        Swal.fire({
            icon: 'warning',
            title: 'Selecciona una categoría',
            text: 'Primero debes seleccionar o crear una categoría para la subcategoría.',
            confirmButtonColor: '#0f172a'
        });
        return;
    }

    document.getElementById('createSubcategoryModal').classList.add('show');
    document.body.style.overflow = 'hidden';
    document.getElementById('createSubcategoryForm').reset();
    const message = document.getElementById('subcategoryFormMessage');
    message.className = 'form-message';
    message.textContent = '';

    document.getElementById('subcategoryCategorySelect').value = categorySelect.value;
    document.getElementById('subcategoryNameInput').focus();
}

function closeCreateSubcategoryModal() {
    document.getElementById('createSubcategoryModal').classList.remove('show');
    document.body.style.overflow = '';
}

document.getElementById('createSubcategoryForm')?.addEventListener('submit', function(e) {
    e.preventDefault();

    const btn = document.getElementById('submitSubcategoryBtn');
    const message = document.getElementById('subcategoryFormMessage');

    btn.disabled = true;
    btn.innerHTML = '<i class="ti ti-loader ti-spin"></i> Guardando...';
    message.className = 'form-message';
    message.textContent = '';

    const formData = new FormData(this);

    fetch('{{ route("admin.services.subcategory.store") }}', {
        method: 'POST',
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'X-CSRF-TOKEN': CSRF_TOKEN
        },
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        btn.disabled = false;
        btn.innerHTML = '<i class="ti ti-device-floppy"></i> Guardar Subcategoría';

        if (data.success) {
            message.className = 'form-message success';
            message.textContent = '✓ Subcategoría creada exitosamente';

            setTimeout(() => {
                closeCreateSubcategoryModal();
            }, 600);
        } else {
            message.className = 'form-message error';
            message.textContent = '✗ ' + (data.message || 'Error al crear la subcategoría');
        }
    })
    .catch(error => {
        btn.disabled = false;
        btn.innerHTML = '<i class="ti ti-device-floppy"></i> Guardar Subcategoría';
        message.className = 'form-message error';
        message.textContent = '✗ Error de conexión. Intenta nuevamente.';
        console.error('Error:', error);
    });
});

// ================================================================
// CONTACTOS - CREAR
// ================================================================
function openCreateContactModal() {
    document.getElementById('createContactModal').classList.add('show');
    document.body.style.overflow = 'hidden';
    document.getElementById('createContactForm').reset();
    document.getElementById('contactFormMessage').className = 'form-message';
    document.getElementById('contactFormMessage').textContent = '';
}

function closeCreateContactModal() {
    document.getElementById('createContactModal').classList.remove('show');
    document.body.style.overflow = '';
}

document.getElementById('createContactForm').addEventListener('submit', function(e) {
    const submitBtn = document.getElementById('submitContactBtn');
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<i class="ti ti-loader ti-spin"></i> Guardando...';
});

// ================================================================
// EDITAR CONTACTO - MODAL
// ================================================================
let editContactId = null;

function openEditContactModal(contactId) {
    editContactId = contactId;
    const modal = document.getElementById('editContactModal');
    const form = document.getElementById('editContactForm');
    const message = document.getElementById('editContactFormMessage');

    message.className = 'form-message';
    message.textContent = '';

    fetch(`/contactos/${contactId}/edit-data`, {
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            const contact = data.contact;
            document.getElementById('editContactName').value = contact.name || '';
            document.getElementById('editContactLastNames').value = contact.last_names || '';
            document.getElementById('editContactQualification').value = contact.qualification || '';
            document.getElementById('editContactFirstPhone').value = contact.first_phone || '';
            document.getElementById('editContactSecondPhone').value = contact.second_phone || '';
            document.getElementById('editContactEmail').value = contact.email || '';
            document.getElementById('editContactPrincipal').checked = contact.es_principal === 1;

            form.action = `/contactos/${contactId}`;

            modal.classList.add('show');
            document.body.style.overflow = 'hidden';
        } else {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'No se pudo cargar la información del contacto',
                confirmButtonColor: '#ef4444'
            });
        }
    })
    .catch(error => {
        console.error('Error:', error);
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'Error al cargar los datos del contacto',
            confirmButtonColor: '#ef4444'
        });
    });
}

function closeEditContactModal() {
    document.getElementById('editContactModal').classList.remove('show');
    document.body.style.overflow = '';
    editContactId = null;
}

document.getElementById('editContactForm').addEventListener('submit', function(e) {
    const submitBtn = document.getElementById('submitEditContactBtn');
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<i class="ti ti-loader ti-spin"></i> Actualizando...';
});

function confirmDeleteContact(id, name) {
    Swal.fire({
        title: '¿Eliminar contacto?',
        html: `Estás a punto de eliminar <strong>${name}</strong>.<br>Esta acción no se puede deshacer.`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#991b1b',
        cancelButtonColor: '#94a3b8',
        confirmButtonText: 'Sí, eliminar',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = `/contactos/${id}`;
            form.innerHTML = `
                <input type="hidden" name="_token" value="${CSRF_TOKEN}">
                <input type="hidden" name="_method" value="DELETE">
            `;
            document.body.appendChild(form);
            form.submit();
        }
    });
}

// ================================================================
// BANCOS - CREAR NUEVO BANCO
// ================================================================
function openCreateBankModal() {
    document.getElementById('createBankModal').classList.add('show');
    document.body.style.overflow = 'hidden';
    document.getElementById('createBankForm').reset();
    const message = document.getElementById('bankFormMessage');
    message.className = 'form-message';
    message.textContent = '';
    document.getElementById('bankNameInput').focus();
}

function closeCreateBankModal() {
    document.getElementById('createBankModal').classList.remove('show');
    document.body.style.overflow = '';
}

document.getElementById('createBankForm').addEventListener('submit', function(e) {
    e.preventDefault();

    const btn = document.getElementById('submitBankBtn');
    const message = document.getElementById('bankFormMessage');

    btn.disabled = true;
    btn.innerHTML = '<i class="ti ti-loader ti-spin"></i> Guardando...';
    message.className = 'form-message';
    message.textContent = '';

    const formData = new FormData(this);

    fetch('{{ route("admin.suppliers.banks.store") }}', {
        method: 'POST',
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'X-CSRF-TOKEN': CSRF_TOKEN
        },
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        btn.disabled = false;
        btn.innerHTML = '<i class="ti ti-device-floppy"></i> Guardar Banco';

        if (data.success) {
            message.className = 'form-message success';
            message.textContent = '✓ Banco creado exitosamente';

            const select = document.getElementById('bankAccountBankSelect');
            const option = document.createElement('option');
            option.value = data.bank.id;
            option.textContent = data.bank.name;
            select.appendChild(option);
            select.value = data.bank.id;

            setTimeout(() => {
                closeCreateBankModal();
                openCreateBankAccountModal();
            }, 500);
        } else {
            message.className = 'form-message error';
            message.textContent = '✗ ' + (data.message || 'Error al crear el banco');
        }
    })
    .catch(error => {
        btn.disabled = false;
        btn.innerHTML = '<i class="ti ti-device-floppy"></i> Guardar Banco';
        message.className = 'form-message error';
        message.textContent = '✗ Error de conexión. Intenta nuevamente.';
        console.error('Error:', error);
    });
});

// ================================================================
// CUENTAS BANCARIAS - CREAR
// ================================================================
function openCreateBankAccountModal() {
    document.getElementById('createBankAccountModal').classList.add('show');
    document.body.style.overflow = 'hidden';
    document.getElementById('createBankAccountForm').reset();
    const message = document.getElementById('bankAccountFormMessage');
    message.className = 'form-message';
    message.textContent = '';
}

function closeCreateBankAccountModal() {
    document.getElementById('createBankAccountModal').classList.remove('show');
    document.body.style.overflow = '';
}

document.getElementById('createBankAccountForm').addEventListener('submit', function(e) {
    e.preventDefault();

    const btn = document.getElementById('submitBankAccountBtn');
    const message = document.getElementById('bankAccountFormMessage');

    btn.disabled = true;
    btn.innerHTML = '<i class="ti ti-loader ti-spin"></i> Guardando...';
    message.className = 'form-message';
    message.textContent = '';

    const formData = new FormData(this);

    fetch('{{ route("admin.bank-accounts.store") }}', {
        method: 'POST',
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'X-CSRF-TOKEN': CSRF_TOKEN
        },
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        btn.disabled = false;
        btn.innerHTML = '<i class="ti ti-device-floppy"></i> Guardar Cuenta';

        if (data.success) {
            message.className = 'form-message success';
            message.textContent = '✓ Cuenta bancaria creada exitosamente';

            setTimeout(() => {
                window.location.reload();
            }, 800);
        } else {
            message.className = 'form-message error';
            message.textContent = '✗ ' + (data.message || 'Error al crear la cuenta bancaria');
        }
    })
    .catch(error => {
        btn.disabled = false;
        btn.innerHTML = '<i class="ti ti-device-floppy"></i> Guardar Cuenta';
        message.className = 'form-message error';
        message.textContent = '✗ Error de conexión. Intenta nuevamente.';
        console.error('Error:', error);
    });
});

// ================================================================
// ELIMINAR CUENTA BANCARIA
// ================================================================
function confirmDeleteBankAccount(accountId) {
    Swal.fire({
        title: '¿Eliminar cuenta bancaria?',
        text: 'Esta acción no se puede deshacer.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#991b1b',
        cancelButtonColor: '#94a3b8',
        confirmButtonText: 'Sí, eliminar',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = `/admin/bank-accounts/${accountId}`;
            form.innerHTML = `
                <input type="hidden" name="_token" value="${CSRF_TOKEN}">
                <input type="hidden" name="_method" value="DELETE">
            `;
            document.body.appendChild(form);
            form.submit();
        }
    });
}

// ================================================================
// EDITAR CUENTA BANCARIA - MODAL
// ================================================================
let editBankAccountId = null;

function openEditBankAccountModal(accountId) {
    editBankAccountId = accountId;
    const modal = document.getElementById('editBankAccountModal');
    const message = document.getElementById('editBankAccountFormMessage');

    message.className = 'form-message';
    message.textContent = '';

    fetch(`/admin/bank-accounts/${accountId}/edit-data`, {
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            const account = data.account;
            document.getElementById('editBankAccountId').value = account.id_bank_account;
            document.getElementById('editBankAccountBankSelect').value = account.id_bank;
            document.getElementById('editAccountNumber').value = account.account_number || '';
            document.getElementById('editCci').value = account.cci || '';
            document.getElementById('editCurrency').value = account.currency || '';

            modal.classList.add('show');
            document.body.style.overflow = 'hidden';
        } else {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'No se pudo cargar la información de la cuenta bancaria',
                confirmButtonColor: '#ef4444'
            });
        }
    })
    .catch(error => {
        console.error('Error:', error);
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'Error al cargar los datos de la cuenta bancaria',
            confirmButtonColor: '#ef4444'
        });
    });
}

function closeEditBankAccountModal() {
    document.getElementById('editBankAccountModal').classList.remove('show');
    document.body.style.overflow = '';
    editBankAccountId = null;
}

document.getElementById('editBankAccountForm')?.addEventListener('submit', function(e) {
    e.preventDefault();

    const btn = document.getElementById('submitEditBankAccountBtn');
    const message = document.getElementById('editBankAccountFormMessage');

    btn.disabled = true;
    btn.innerHTML = '<i class="ti ti-loader ti-spin"></i> Actualizando...';
    message.className = 'form-message';
    message.textContent = '';

    const formData = new FormData(this);
    const accountId = document.getElementById('editBankAccountId').value;

    fetch(`/admin/bank-accounts/${accountId}`, {
        method: 'POST',
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'X-CSRF-TOKEN': CSRF_TOKEN
        },
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        btn.disabled = false;
        btn.innerHTML = '<i class="ti ti-device-floppy"></i> Actualizar Cuenta';

        if (data.success) {
            message.className = 'form-message success';
            message.textContent = '✓ Cuenta bancaria actualizada exitosamente';

            setTimeout(() => {
                window.location.reload();
            }, 800);
        } else {
            message.className = 'form-message error';
            message.textContent = '✗ ' + (data.message || 'Error al actualizar la cuenta bancaria');
        }
    })
    .catch(error => {
        btn.disabled = false;
        btn.innerHTML = '<i class="ti ti-device-floppy"></i> Actualizar Cuenta';
        message.className = 'form-message error';
        message.textContent = '✗ Error de conexión. Intenta nuevamente.';
        console.error('Error:', error);
    });
});

// ================================================================
// ENVÍO DEL FORMULARIO DE SERVICIO
// ================================================================
document.getElementById('createServiceForm')?.addEventListener('submit', function(e) {
    const submitBtn = document.getElementById('submitServiceBtn');
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<i class="ti ti-loader ti-spin"></i> Guardando...';
});

// ================================================================
// CIERRE DE MODALES CON ESC Y CLICK FUERA
// ================================================================
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeUploadImageModal();
        closeCreateServiceModal();
        closeCreateContactModal();
        closeEditContactModal();
        closeCreateBankModal();
        closeCreateBankAccountModal();
        closeEditBankAccountModal();
        closeCreateCategoryModal();
        closeCreateSubcategoryModal();
        closeLightbox();
    }
});

// Cierres con click fuera
document.getElementById('uploadImageModal').addEventListener('click', function(e) {
    if (e.target === this) closeUploadImageModal();
});
document.getElementById('createServiceModal').addEventListener('click', function(e) {
    if (e.target === this) closeCreateServiceModal();
});
document.getElementById('createContactModal').addEventListener('click', function(e) {
    if (e.target === this) closeCreateContactModal();
});
document.getElementById('editContactModal').addEventListener('click', function(e) {
    if (e.target === this) closeEditContactModal();
});
document.getElementById('createBankModal').addEventListener('click', function(e) {
    if (e.target === this) closeCreateBankModal();
});
document.getElementById('createBankAccountModal').addEventListener('click', function(e) {
    if (e.target === this) closeCreateBankAccountModal();
});
document.getElementById('editBankAccountModal').addEventListener('click', function(e) {
    if (e.target === this) closeEditBankAccountModal();
});
document.getElementById('createCategoryModal').addEventListener('click', function(e) {
    if (e.target === this) closeCreateCategoryModal();
});
document.getElementById('createSubcategoryModal').addEventListener('click', function(e) {
    if (e.target === this) closeCreateSubcategoryModal();
});

// ================================================================
// RESTAURAR PESTAÑA DESDE URL
// ================================================================
document.addEventListener('DOMContentLoaded', function() {
    const params = new URLSearchParams(window.location.search);
    const tab = params.get('tab');
    if (tab) {
        const btn = document.querySelector(`.content-nav button[data-tab="${tab}"]`);
        if (btn) btn.click();
    }

    const urlParams = new URLSearchParams(window.location.search);
    const contactSuccess = urlParams.get('contact_success');
    const contactError = urlParams.get('contact_error');

    if (contactSuccess) {
        Swal.fire({
            icon: 'success',
            title: '¡Éxito!',
            text: contactSuccess,
            timer: 3000,
            showConfirmButton: false
        });
        urlParams.delete('contact_success');
        window.history.replaceState({}, '', '?' + urlParams.toString());
    }

    if (contactError) {
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: contactError,
            timer: 4000,
            showConfirmButton: false
        });
        urlParams.delete('contact_error');
        window.history.replaceState({}, '', '?' + urlParams.toString());
    }
});
</script>
@endsection