<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    protected $connection = 'mysql';

    public function up(): void
    {
        Schema::create('tariff', function (Blueprint $table) {
            $table->id('id_tariff');

            $table->foreignId('id_service')
                ->constrained('service', 'id_service')
                ->cascadeOnDelete();

            $table->foreignId('id_subcategories')
                ->constrained('sub_categorie', 'id_subcategories')
                ->cascadeOnDelete();

            $table->foreignId('id_season')->nullable()
                ->constrained('season', 'id_season')
                ->nullOnDelete();

            $table->string('pricing_type', 100)->nullable()->comment('Tipo de tarifa: por persona, por habitación, por grupo, etc.');

            $table->integer('min_people_count')->nullable();
            $table->integer('max_people_count')->nullable();

            $table->decimal('price', 10, 2)->nullable();

            $table->string('status', 20)->default('active');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tariff');
    }
};
