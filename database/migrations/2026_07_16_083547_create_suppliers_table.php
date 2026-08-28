<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    protected $connection = 'mysql';

    public function up(): void
    {
        Schema::create('suppliers', function (Blueprint $table) {
            $table->id('id_supplier');

            $table->foreignId('id_categories_suppliers')
                ->nullable()
                ->constrained('categories_suppliers', 'id_categories_suppliers')
                ->nullOnDelete();

            $table->foreignId('id_supplier_subcategory')
                ->nullable()
                ->constrained('supplier_subcategories', 'id_supplier_subcategory')
                ->nullOnDelete();

            $table->string('supplier_name', 100);
            $table->string('business_name', 150)->nullable();
            $table->string('tax_code', 20)->nullable();
            $table->string('general_phone', 20)->nullable();
            $table->string('general_email', 120)->nullable();

            $table->foreignId('id_cities')
                ->nullable()
                ->constrained('cities', 'id_cities')
                ->nullOnDelete();

            $table->string('address', 255)->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('suppliers');
    }
};
