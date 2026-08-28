<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    protected $connection = 'mysql';

    public function up(): void
    {
        Schema::create('quote_accommodation_occupant', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_quote_accommodation')->constrained('quote_accommodation', 'id_quote_accommodation')->cascadeOnDelete();
            $table->foreignId('id_quote_passenger')->constrained('quote_passengers', 'id_quote_passenger')->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['id_quote_accommodation', 'id_quote_passenger'], 'uq_accommodation_passenger');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('quote_accommodation_occupant');
    }
};
