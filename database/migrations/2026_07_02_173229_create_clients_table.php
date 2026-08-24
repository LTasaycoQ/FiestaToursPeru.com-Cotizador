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
        Schema::create('clients', function (Blueprint $table) {
            $table->id('id_client');
            $table->string('name_client', 120)->nullable();
            $table->string('business_name', 150)->nullable();
            $table->string('tax_code', 20)->nullable();
            $table->string('type_client', 20)->nullable();
            $table->string('general_phone', 20)->nullable();
            $table->string('general_email', 120)->nullable();

            // Ubicación geográfica (datos planos desde GeoNames)
            $table->foreignId('id_cities')->nullable()->constrained('cities', 'id_cities')->onDelete('cascade');
            $table->string('address', 255)->nullable();

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('clients');
    }
};
