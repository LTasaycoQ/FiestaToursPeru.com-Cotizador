<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    protected $connection = 'mysql';

    public function up(): void
    {
        Schema::create('supplier_subcategories', function (Blueprint $table) {
            $table->id('id_supplier_subcategory');

            $table->foreignId('id_categories_suppliers')
                ->constrained('categories_suppliers', 'id_categories_suppliers')
                ->cascadeOnDelete();

            $table->string('name', 100);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('supplier_subcategories');
    }
};
