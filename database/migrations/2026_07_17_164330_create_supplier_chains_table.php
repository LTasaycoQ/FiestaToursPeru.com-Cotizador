<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{

 protected $connection = 'mysql';
   public function up(): void
{
    Schema::create('supplier_chains', function (Blueprint $table) {
        $table->id('id_chainSupplier');
        $table->foreignId('id_chain')->constrained('chain', 'id_chain')->onDelete('cascade');
        $table->foreignId('id_supplier')->constrained('suppliers', 'id_supplier')->onDelete('cascade');

        $table->unique(['id_chain', 'id_supplier']);

        $table->timestamps();
        $table->softDeletes();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('supplier_chains');
    }
};
