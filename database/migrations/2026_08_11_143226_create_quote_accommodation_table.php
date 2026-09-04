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
            $table->foreignId('id_season')->nullable()->constrained('season', 'id_season')->nullOnDelete();

            $table->unsignedTinyInteger('option_number')
                ->comment('1 = opción A de hotel, 2 = opción B de hotel');

            $table->foreignId('id_quote_day')->constrained('quote_day', 'id_quote_day')
                ->comment('Día específico donde se usa este hotel');

            $table->foreignId('id_service')->constrained('service', 'id_service')
                ->comment('Debe ser un service cuya categoría tenga is_accommodation = true');
            $table->foreignId('id_tariff')->nullable()->constrained('tariff', 'id_tariff');
            $table->foreignId('id_supplier')->constrained('suppliers', 'id_supplier');
            $table->text('notes')->nullable()->nullable()->comment('Notas internas sobre el hotel, no se envía al cliente');
            $table->string('room_type', 32)->nullable()->comment('simple, doble, triple');
            $table->unsignedTinyInteger('room_capacity')->default(1)->comment('Capacidad de personas por habitación');
            $table->unsignedTinyInteger('room_count')->default(1)->comment('Cantidad de habitaciones del mismo tipo');

            $table->decimal('unit_price', 12, 2)->default(0)->comment('Precio por noche o por habitación');
            $table->decimal('subtotal', 12, 2)->default(0)->comment('Subtotal calculado por room_count');

            $table->timestamps();

            $table->index(['id_quote', 'option_number']);
            $table->index('id_service');
            $table->index('id_quote_day');
            $table->index('room_type');

            $table->unique(['id_quote', 'option_number', 'id_quote_day', 'id_service', 'room_type'], 'unique_hotel_room_type_per_day_option');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('quote_accommodation');
    }
};
