<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    protected $connection = 'mysql';

    public function up(): void
    {
        Schema::create('detail_quote', function (Blueprint $table) {
            $table->id('id_detail_quote');

            // Relaciones
            $table->foreignId('id_quote_day')->constrained('quote_day', 'id_quote_day')->cascadeOnDelete();
            $table->foreignId('id_service')->constrained('service', 'id_service');
            $table->foreignId('id_tariff')->constrained('tariff', 'id_tariff')->comment('Tarifa aplicada al servicio');
            $table->foreignId('id_supplier')->constrained('suppliers', 'id_supplier')->comment('Proveedor del servicio');

            // Datos del detalle
            $table->integer('quantity')->default(1)->comment('Cantidad contratada del servicio');

            // Precios
            $table->decimal('unit_price', 12, 2)->default(0)->comment('Precio unitario de la tarifa');
            $table->decimal('subtotal', 12, 2)->default(0)->comment('Subtotal = unit_price * quantity');

            $table->timestamps();

            // Índices
            $table->index('id_quote_day');
            $table->index('id_service');
            $table->index('id_tariff');
            $table->index('id_supplier');
            $table->index(['id_quote_day', 'id_service']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('detail_quote');
    }
};
