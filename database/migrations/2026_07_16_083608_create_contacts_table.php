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
         Schema::create('contacts', function (Blueprint $table) {
            $table->id('id_contacts');
            $table->foreignId('id_client')->nullable()->constrained('clients', 'id_client')->onDelete('cascade');
            $table->foreignId('id_supplier')->nullable()->constrained('suppliers', 'id_supplier')->onDelete('cascade');

            $table->string('name', 100);
            $table->string('last_names', 100)->nullable();
            $table->string('qualification', 30)->nullable();
            $table->string('email', 80)->nullable();
            $table->string('first_phone', 20)->nullable();
            $table->string('second_phone', 20)->nullable();
            $table->boolean('es_principal')->default(false);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('contacts');
    }
};
