<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    protected $connection = 'mysql';

    public function up(): void
    {
        Schema::create('service_category', function (Blueprint $table) {
            $table->id('id_category');
            $table->string('name', 50); // Ej: Hospedaje, Excursiones, Traslados
            $table->enum('pricing_type', ['flat', 'tiered'])->default('tiered');
            $table->boolean('is_accommodation')->default(false);
            $table->string('status', 20)->default('active');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('service_category');
    }
};
