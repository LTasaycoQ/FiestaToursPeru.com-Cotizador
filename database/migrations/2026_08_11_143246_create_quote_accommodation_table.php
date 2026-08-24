<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    protected $connection = 'mysql';

    public function up(): void
    {
        Schema::dropIfExists('quote_accommodation');

        Schema::create('quote_accommodation', function (Blueprint $table) {
            $table->id('id_quote_accommodation');

            $table->foreignId('id_quote')->constrained('quote', 'id_quote')->cascadeOnDelete();

            $table->unsignedTinyInteger('option_number')
                ->comment('1 = opción A de hotel, 2 = opción B de hotel');

            $table->foreignId('id_quote_day')->constrained('quote_day', 'id_quote_day')
                ->comment('Día específico donde se usa este hotel');

            $table->foreignId('id_service')->constrained('service', 'id_service')
                ->comment('Debe ser un service cuya categoría tenga is_accommodation = true');
            $table->foreignId('id_tariff')->constrained('tariff', 'id_tariff');
            $table->foreignId('id_supplier')->constrained('suppliers', 'id_supplier');

            $table->decimal('unit_price', 12, 2)->default(0)->comment('Precio por noche');
            $table->decimal('subtotal', 12, 2)->default(0)->comment('Subtotal = unit_price * 1 noche');

            $table->timestamps();

            $table->index(['id_quote', 'option_number']);
            $table->index('id_service');
            $table->index('id_quote_day');

            $table->unique(['id_quote', 'option_number', 'id_quote_day'], 'unique_hotel_per_day_option');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('quote_accommodation');
    }
};
