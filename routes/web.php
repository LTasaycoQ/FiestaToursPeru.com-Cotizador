<?php

use App\Http\Controllers\Admin\BankAccountController;
use App\Http\Controllers\Admin\CategorySupplierController;
use App\Http\Controllers\Admin\ChainController;
use App\Http\Controllers\Admin\ClientController;
use App\Http\Controllers\Admin\ContactController;
use App\Http\Controllers\Admin\Finance\FinanceController;
use App\Http\Controllers\Admin\QuoteController;
use App\Http\Controllers\Admin\SeasonController;
use App\Http\Controllers\Admin\ServiceController;
use App\Http\Controllers\Admin\SupplierController;
use App\Http\Controllers\Admin\TariffController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AvatarController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\GeoController;
use App\Http\Controllers\PerfilController;
use App\Http\Controllers\SupportController;
use Illuminate\Support\Facades\Route;

Route::get('/', fn () => redirect()->route('login'));

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.post');
});

Route::prefix('api/geo')->group(function () {
    Route::get('/paises', [GeoController::class, 'paises']);
    Route::get('/ciudades', [GeoController::class, 'ciudades']);
});

Route::get('/avatar/{filename}', [AvatarController::class, 'show'])->name('avatar.show');

Route::middleware('auth')->group(function () {

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    Route::get('/perfil', [PerfilController::class, 'show'])->name('perfil');
    Route::get('/perfil/editar', [PerfilController::class, 'edit'])->name('perfil.edit');
    Route::put('/perfil', [PerfilController::class, 'update'])->name('perfil.update');

    Route::prefix('clientes')->name('admin.clients.')->group(function () {
        Route::get('/', [ClientController::class, 'index'])->name('index');
        Route::get('/exportar/pdf', [ClientController::class, 'exportPdf'])->name('export.pdf');
        Route::get('/exportar/excel', [ClientController::class, 'exportExcel'])->name('export.excel');
        Route::get('/plantilla', [ClientController::class, 'downloadTemplate'])->name('template');
        Route::get('/importar', [ClientController::class, 'importView'])->name('import.view');
        Route::post('/importar', [ClientController::class, 'import'])->name('import');
        Route::post('/', [ClientController::class, 'store'])->name('store');

        Route::get('/crear', [ClientController::class, 'create'])->name('create');
        Route::get('/{client}/editar', [ClientController::class, 'edit'])->name('edit');
        Route::put('/{client}', [ClientController::class, 'update'])->name('update');
        Route::delete('/{client}', [ClientController::class, 'destroy'])->name('destroy');

        Route::post('/{client}/restore', [ClientController::class, 'restore'])->name('restore');
        Route::delete('/{client}/force', [ClientController::class, 'forceDestroy'])->name('force-destroy');

        Route::delete('/bulk', [ClientController::class, 'bulkDestroy'])->name('bulk-destroy');
        Route::post('/bulk/restore', [ClientController::class, 'bulkRestore'])->name('bulk-restore');
        Route::delete('/bulk/force', [ClientController::class, 'bulkForceDestroy'])->name('bulk-force-destroy');
    });

    Route::prefix('contactos')->name('admin.contacts.')->group(function () {
        Route::get('/', [ContactController::class, 'index'])->name('index');
        Route::get('/crear', [ContactController::class, 'create'])->name('create');
        Route::post('/', [ContactController::class, 'store'])->name('store');
        Route::get('/{contact}/edit-data', [ContactController::class, 'editData'])->name('edit-data');

        Route::get('/export/excel', [ContactController::class, 'exportExcel'])->name('export.excel');

        Route::delete('/bulk', [ContactController::class, 'bulkDestroy'])->name('bulk-destroy');
        Route::post('/bulk/restore', [ContactController::class, 'bulkRestore'])->name('bulk-restore');
        Route::delete('/bulk/force', [ContactController::class, 'bulkForceDestroy'])->name('bulk-force-destroy');

        Route::get('/{contact}/editar', [ContactController::class, 'edit'])->name('edit');
        Route::put('/{contact}', [ContactController::class, 'update'])->name('update');
        Route::delete('/{contact}', [ContactController::class, 'destroy'])->name('destroy');

        Route::post('/{contact}/restore', [ContactController::class, 'restore'])->name('restore');
        Route::delete('/{contact}/force', [ContactController::class, 'forceDestroy'])->name('force-destroy');
    });

    Route::get('/contactos/exportar/excel', [ContactController::class, 'exportExcel'])->name('admin.contacts.export.excel');

    Route::prefix('categorias-proveedores')->name('admin.categories.')->group(function () {
        Route::get('/', [CategorySupplierController::class, 'index'])->name('index');
        Route::get('/crear', [CategorySupplierController::class, 'create'])->name('create');
        Route::post('/', [CategorySupplierController::class, 'store'])->name('store');
        Route::get('/{category}/editar', [CategorySupplierController::class, 'edit'])->name('edit');
        Route::put('/{category}', [CategorySupplierController::class, 'update'])->name('update');
        Route::delete('/{category}', [CategorySupplierController::class, 'destroy'])->name('destroy');
    });

    Route::prefix('cadenas')->name('admin.chains.')->group(function () {
        Route::get('/', [ChainController::class, 'index'])->name('index');
        Route::get('/crear', [ChainController::class, 'create'])->name('create');
        Route::post('/', [ChainController::class, 'store'])->name('store');
        Route::get('/{chain}', [ChainController::class, 'show'])->name('show');
        Route::get('/{chain}/editar', [ChainController::class, 'edit'])->name('edit');
        Route::put('/{chain}', [ChainController::class, 'update'])->name('update');
        Route::delete('/{chain}', [ChainController::class, 'destroy'])->name('destroy');
    });

    Route::prefix('proveedores')->name('admin.suppliers.')->group(function () {
        Route::get('/', [SupplierController::class, 'index'])->name('index');
        Route::get('/crear', [SupplierController::class, 'create'])->name('create');
        Route::post('/', [SupplierController::class, 'store'])->name('store');

        Route::post('/bancos', [SupplierController::class, 'storeBank'])->name('banks.store');

        Route::get('/exportar/pdf', [SupplierController::class, 'exportPdfAll'])->name('export.pdf.all');
        Route::get('/exportar/excel', [SupplierController::class, 'exportExcel'])->name('export.excel');
        Route::get('/importar', [SupplierController::class, 'importView'])->name('import.view');
        Route::post('/importar', [SupplierController::class, 'import'])->name('import');
        Route::get('/plantilla', [SupplierController::class, 'downloadTemplate'])->name('template');

        Route::post('/{id}/images', [SupplierController::class, 'uploadImages'])->name('images.upload');
        Route::post('/images/{id}/principal', [SupplierController::class, 'setPrincipalImage'])->name('images.principal');
        Route::delete('/images/{id}', [SupplierController::class, 'deleteImage'])->name('images.delete');

        Route::get('/{supplier}', [SupplierController::class, 'show'])->name('show');

        Route::get('/{supplier}/editar', [SupplierController::class, 'edit'])->name('edit');
        Route::put('/{supplier}', [SupplierController::class, 'update'])->name('update');
        Route::delete('/{supplier}', [SupplierController::class, 'destroy'])->name('destroy');
        Route::get('/{supplier}/pdf', [SupplierController::class, 'exportPdf'])->name('pdf');

        Route::post('/{supplier}/restore', [SupplierController::class, 'restore'])->name('restore');
        Route::delete('/{supplier}/force', [SupplierController::class, 'forceDestroy'])->name('force-destroy');

        Route::delete('/bulk', [SupplierController::class, 'bulkDestroy'])->name('bulk.destroy');
        Route::post('/bulk/restore', [SupplierController::class, 'bulkRestore'])->name('bulk.restore');
        Route::delete('/bulk/force', [SupplierController::class, 'bulkForceDestroy'])->name('bulk.force.destroy');

        Route::get('/{supplier}/productos', [SupplierController::class, 'products'])->name('productos');
        Route::post('/{supplier}/descripciones', [SupplierController::class, 'storeDescription'])->name('descriptions.store');
    });

    // ============================================================
    // CUENTAS BANCARIAS (ADMIN) - COMPLETO CON EDICIÓN
    // ============================================================
    Route::prefix('admin/bank-accounts')->name('admin.bank-accounts.')->group(function () {
        Route::post('/', [BankAccountController::class, 'store'])->name('store');
        Route::get('/{id}/edit-data', [BankAccountController::class, 'editData'])->name('edit-data');
        Route::put('/{id}', [BankAccountController::class, 'update'])->name('update');
        Route::delete('/{id}', [BankAccountController::class, 'destroy'])->name('destroy');
    });

    Route::prefix('cotizaciones')->name('admin.quotes.')->group(function () {
        Route::get('/', [QuoteController::class, 'index'])->name('index');
        Route::get('/crear', [QuoteController::class, 'create'])->name('create');
        Route::post('/', [QuoteController::class, 'store'])->name('store');

        Route::get('/get-contacts-by-client/{clientId}', [QuoteController::class, 'getContactsByClient'])
            ->name('get.contacts.by.client');

        Route::get('/get-suppliers', [QuoteController::class, 'getSuppliers'])
            ->name('get.suppliers');

        Route::get('/get-services-by-supplier/{supplierId}', [QuoteController::class, 'getServicesBySupplier'])
            ->name('get.services.by.supplier');

        Route::get('/get-services-by-language/{languageId}', [QuoteController::class, 'getServicesByLanguage'])
            ->name('get.services.by.language');

        Route::get('/get-filtered-services', [QuoteController::class, 'getFilteredServices'])
            ->name('get.filtered.services');

        Route::get('/get-tariffs-by-service/{serviceId}', [QuoteController::class, 'getTariffsByService'])
            ->name('get.tariffs.by.service');

        Route::get('/{quote}', [QuoteController::class, 'show'])->name('show');
        Route::get('/{quote}/editar', [QuoteController::class, 'edit'])->name('edit');
        Route::get('/{quote}/exportar/excel', [QuoteController::class, 'exportExcel'])->name('export.excel');
        Route::put('/{quote}', [QuoteController::class, 'update'])->name('update');
        Route::post('/{quote}/cotizar', [QuoteController::class, 'quote'])->name('quote');
        Route::delete('/{quote}', [QuoteController::class, 'destroy'])->name('destroy');

        Route::post('/{quote}/duplicate', [QuoteController::class, 'duplicate'])->name('duplicate');
        Route::post('/{quote}/status', [QuoteController::class, 'changeStatus'])->name('status');

        Route::post('/{quote}/services', [QuoteController::class, 'addService'])->name('add-service');
        Route::put('/{quote}/services/{detail}', [QuoteController::class, 'updateServiceDetail'])->name('update-service');
        Route::delete('/{quote}/services/{detail}', [QuoteController::class, 'removeService'])->name('remove-service');

        Route::post('/{quote}/accommodations', [QuoteController::class, 'addAccommodation'])->name('add-accommodation');

        Route::post('/{quote}/accommodation-to-day', [QuoteController::class, 'addAccommodationToDay'])->name('add-accommodation-to-day');
        Route::delete('/{quote}/accommodations/{accommodation}', [QuoteController::class, 'removeAccommodation'])->name('remove-accommodation');

        // Pasajeros y asignaciones de alojamiento
        Route::post('/{quote}/passengers', [QuoteController::class, 'addPassenger'])->name('add-passenger');
        Route::delete('/{quote}/passengers/{passenger}', [QuoteController::class, 'removePassenger'])->name('remove-passenger');

        Route::post('/{quote}/accommodations/{accommodation}/occupants', [QuoteController::class, 'assignOccupant'])->name('assign-occupant');
        Route::delete('/{quote}/accommodations/{accommodation}/occupants/{passenger}', [QuoteController::class, 'removeOccupant'])->name('remove-occupant');
    });

    Route::prefix('servicios')->name('admin.services.')->group(function () {
        Route::get('/', [ServiceController::class, 'index'])->name('index');
        Route::get('/importar', [ServiceController::class, 'importView'])->name('import.view');
        Route::post('/importar', [ServiceController::class, 'import'])->name('import');
        Route::get('/plantilla', [ServiceController::class, 'downloadTemplate'])->name('template');
        Route::get('/crear', [ServiceController::class, 'create'])->name('create');

        Route::post('/', [ServiceController::class, 'store'])->name('store');

        Route::get('/subcategorias/{categoryId}', [ServiceController::class, 'getSubcategoriesByCategory'])->name('subcategories');

        Route::post('/categoria', [ServiceController::class, 'storeCategory'])->name('category.store');

        Route::post('/subcategoria', [ServiceController::class, 'storeSubcategory'])->name('subcategory.store');

        Route::post('/{service}/update-availability', [ServiceController::class, 'updateAvailability'])->name('update-availability');

        Route::get('/itinerario', [ServiceController::class, 'getItineraryServices'])->name('itinerary');
        Route::get('/hospedaje', [ServiceController::class, 'getAccommodationServices'])->name('accommodation');

        Route::get('/{service}/editar', [ServiceController::class, 'edit'])->name('edit');
        Route::put('/{service}', [ServiceController::class, 'update'])->name('update');
        Route::delete('/{service}', [ServiceController::class, 'destroy'])->name('destroy');
        Route::post('/{service}/descripciones', [ServiceController::class, 'storeDescription'])->name('descriptions.store');
    });

    Route::prefix('servicios/{service}/tarifas')->name('admin.tariffs.')->group(function () {
        Route::get('/precios', [TariffController::class, 'show'])->name('show');
        Route::put('/precios', [TariffController::class, 'updatePrice'])->name('updatePrice');

        Route::get('/subcategoria/{subcategory}/editar', [TariffController::class, 'editSubcategory'])->name('editSubcategory');
        Route::delete('/subcategoria/{subcategory}', [TariffController::class, 'destroySubcategory'])->name('destroySubcategory');
        Route::put('/subcategoria/{subcategory}/rangos', [TariffController::class, 'updateRanges'])->name('updateRanges');
        Route::put('/subcategoria/{subcategory}/precio-unico', [TariffController::class, 'updateFlat'])->name('updateFlat');

        Route::get('/', [TariffController::class, 'index'])->name('index');
        Route::post('/', [TariffController::class, 'store'])->name('store');
        Route::get('/{tariff}/edit', [TariffController::class, 'edit'])->name('edit');
        Route::put('/{tariff}', [TariffController::class, 'update'])->name('update');
        Route::delete('/{tariff}', [TariffController::class, 'destroy'])->name('destroy');
    });

    Route::prefix('servicios/{service}/temporadas')->name('admin.seasons.')->group(function () {
        Route::post('/', [SeasonController::class, 'store'])->name('store');
        Route::post('/{season}/subcategoria/{subcategory}', [SeasonController::class, 'assignToSubcategory'])->name('assignToSubcategory');
        Route::put('/{season}/tarifas', [SeasonController::class, 'updateTariffs'])->name('updateTariffs');
        Route::delete('/{season}', [SeasonController::class, 'destroy'])->name('destroy');
    });

    Route::middleware('role:admin')->prefix('admin')->name('admin.')->group(function () {
        Route::get('/usuarios', [UserController::class, 'index'])->name('usuarios');
        Route::get('/usuarios/crear', [UserController::class, 'create'])->name('usuarios.create');
        Route::post('/usuarios', [UserController::class, 'store'])->name('usuarios.store');
        Route::get('/usuarios/{user}/editar', [UserController::class, 'edit'])->name('usuarios.edit');
        Route::put('/usuarios/{user}', [UserController::class, 'update'])->name('usuarios.update');
        Route::delete('/usuarios/{user}', [UserController::class, 'destroy'])->name('usuarios.destroy');
    });

    Route::prefix('finance')->group(function () {

        Route::get('/', [FinanceController::class, 'index'])->name('finance.index');
        Route::get('/{id}', [FinanceController::class, 'show'])->name('finance.show');
        Route::post('/', [FinanceController::class, 'store'])->name('finance.store');
        Route::put('/{id}', [FinanceController::class, 'update'])->name('finance.update');
        Route::delete('/{id}', [FinanceController::class, 'destroy'])->name('finance.destroy');

        Route::post('/{id}/expense', [FinanceController::class, 'registerExpense'])->name('finance.registerExpense');
        Route::get('/{id}/expense/{expenseId}/edit', [FinanceController::class, 'editExpense'])->name('finance.editExpense');
        Route::put('/{id}/expense/{expenseId}', [FinanceController::class, 'updateExpense'])->name('finance.updateExpense');
        Route::delete('/{id}/expense/{expenseId}', [FinanceController::class, 'destroyExpense'])->name('finance.destroyExpense');

        Route::post('/{id}/recharge', [FinanceController::class, 'rechargeBalance'])->name('finance.recharge');
        Route::post('/{id}/initial-balance', [FinanceController::class, 'setInitialBalance'])->name('finance.setInitialBalance');
        Route::get('/{id}/recharge/{rechargeId}/edit', [FinanceController::class, 'editRecharge'])->name('finance.editRecharge');
        Route::put('/{id}/recharge/{rechargeId}', [FinanceController::class, 'updateRecharge'])->name('finance.updateRecharge');
        Route::delete('/{id}/recharge/{rechargeId}', [FinanceController::class, 'destroyRecharge'])->name('finance.destroyRecharge');

        Route::get('/{id}/export/all', [FinanceController::class, 'exportAll'])->name('finance.export.all');
        Route::get('/{id}/export/recharges', [FinanceController::class, 'exportRecharges'])->name('finance.export.recharges');
    });

    Route::post('/support/send', [SupportController::class, 'sendMessage'])->name('support.send');

});
