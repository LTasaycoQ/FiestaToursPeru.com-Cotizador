 
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    protected $connection = 'mysql';

    public function up(): void
    {
        Schema::create('supplier_descriptions', function (Blueprint $table) {
            $table->id('id_supplier_description');
            $table->foreignId('id_supplier')
                ->constrained('suppliers', 'id_supplier')
                ->cascadeOnDelete();
            $table->foreignId('id_language')
                ->constrained('languages', 'id_language')
                ->cascadeOnDelete();
            $table->text('description')->nullable();
            $table->timestamps();
            $table->unique(['id_supplier', 'id_language']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('supplier_descriptions');
    }
};
