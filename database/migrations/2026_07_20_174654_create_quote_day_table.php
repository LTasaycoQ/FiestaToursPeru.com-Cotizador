<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    protected $connection = 'mysql';

    public function up(): void
    {
        Schema::create('quote_day', function (Blueprint $table) {
            $table->id('id_quote_day');

            $table->foreignId('id_quote')->constrained('quote', 'id_quote')->cascadeOnDelete();

            $table->unsignedSmallInteger('day_number')->comment('1, 2, 3... relativo al itinerario');
            $table->date('date')->nullable()->comment('Fecha real: start_date + (day_number - 1)');

            $table->timestamps();

            // Un mismo día no puede repetirse dentro de la misma cotización
            $table->unique(['id_quote', 'day_number']);
            $table->index('id_quote');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('quote_day');
    }
};
