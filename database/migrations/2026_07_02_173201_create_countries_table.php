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
        Schema::create('countries', function (Blueprint $table) {
            $table->id('id_countries');
            $table->string('name', 100);
            $table->string('capital', 100)->nullable();
            $table->string('nationality', 100)->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('countries');
    }
};
