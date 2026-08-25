<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    protected $connection = 'mysql';

    public function up(): void
    {
        Schema::create('service', function (Blueprint $table) {
            $table->id('id_service');
            $table->string('name_service', 300);

            $table->foreignId('id_supplier')
                ->nullable()
                ->constrained('suppliers', 'id_supplier')
                ->nullOnDelete();

            $table->foreignId('id_category')
                ->nullable()
                ->constrained('service_category', 'id_category')
                ->nullOnDelete();

            $table->foreignId('id_labels')
                ->nullable()
                ->constrained('labels', 'id_labels')
                ->nullOnDelete();

            $table->string('description', 900)->nullable();
            $table->string('imagen', 300)->nullable();
            $table->string('availability_days', 100)->nullable();
            $table->enum('pricing_type', ['flat', 'tiered'])->nullable();
            $table->string('status', 20)->default('active');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('service');
    }
};
