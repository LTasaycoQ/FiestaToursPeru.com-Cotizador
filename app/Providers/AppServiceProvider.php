<?php

namespace App\Providers;

use App\Models\Client;
use App\Models\Contact;
use App\Models\Supplier;
use App\Models\CategorySupplier;
use App\Models\Destination;
use App\Models\Chain;
use App\Models\Quotation;
use Illuminate\Database\Schema\Builder;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void {}

    public function boot(): void
    {
        Builder::defaultStringLength(191);

        // PKs personalizadas
        Route::bind('client', function ($value) {
            return Client::withTrashed()->where('id_client', $value)->firstOrFail();
        });

        Route::bind('contact', function ($value) {
            return Contact::withTrashed()->where('id_contacts', $value)->firstOrFail();
        });

        Route::bind('category', function ($value) {
            return CategorySupplier::where('id_categories_suppliers', $value)->firstOrFail();
        });

        Route::bind('supplier', function ($value) {
            return Supplier::withTrashed()->where('id_supplier', $value)->firstOrFail();
        });

        Route::bind('chain', function ($value) {
            return Chain::where('id_chain', $value)->firstOrFail();
        });

        Route::bind('quotation', function ($value) {
            return Quotation::withTrashed()->where('id_quotation', $value)->firstOrFail();
        });
    }
}
