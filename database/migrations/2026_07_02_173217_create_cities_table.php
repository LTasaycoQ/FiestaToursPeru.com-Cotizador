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


        Schema::create('cities', function (Blueprint $table) {
            $table->id('id_cities');
            $table->foreignId('country_id')->constrained('countries', 'id_countries')->onDelete('cascade');
            $table->string('name', 100);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cities');
    }
};
