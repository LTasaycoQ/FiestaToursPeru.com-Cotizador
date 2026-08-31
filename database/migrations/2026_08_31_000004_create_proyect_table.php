<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    protected $connection = "budget_manager"; 

    public function up(): void
    {
        Schema::create('proyect', function (Blueprint $table) {
            $table->id('id_proyect');
            $table->foreignId('id_balance')
                ->nullable()
                ->constrained('balance', 'id_balance')
                ->cascadeOnDelete();
            $table->string('name', 255);
            $table->string('currency', 10)->default('S/');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('proyect');
    }
};
