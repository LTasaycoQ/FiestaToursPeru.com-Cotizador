<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
     protected $connection = 'mysql';
    public function up(): void
    {
        Schema::create('supplier_images', function (Blueprint $table) {
            $table->id('id_supplier_image');

            $table->foreignId('id_supplier')
                  ->constrained('suppliers', 'id_supplier')
                  ->onDelete('cascade');

            $table->string('image_path', 500);
            $table->boolean('is_principal')->default(false);

            $table->timestamps();

            $table->softDeletes();

            $table->index('id_supplier');
            $table->index('is_principal');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('supplier_images');
    }
};
