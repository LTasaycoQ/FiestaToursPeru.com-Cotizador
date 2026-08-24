<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    protected $connection = 'mysql';

    public function up(): void
    {
        Schema::create('sub_categorie', function (Blueprint $table) {
            $table->id('id_subcategories');

            $table->foreignId('id_category')
                ->constrained('service_category', 'id_category')
                ->cascadeOnDelete();

            $table->string('name', 300); // Ej: SGL, DBL, TPL (para Hoteles) / Regular, VIP, Privado (para Tours)
            $table->string('status', 20)->default('active');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sub_categorie');
    }
};
