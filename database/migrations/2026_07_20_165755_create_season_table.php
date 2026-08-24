<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    protected $connection = 'mysql';

    public function up(): void
    {
        Schema::create('season', function (Blueprint $table) {
            $table->id('id_season');
            $table->string('name', 100)->comment('Ej: Jun/July, Temporada Alta, etc.');
            $table->date('start_date');
            $table->date('end_date');

            $table->foreignId('id_service')
                ->constrained('service', 'id_service')
                ->onDelete('cascade');

            $table->string('status', 20)->default('active');
            $table->timestamps();

            $table->index('id_service');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('season');
    }
};
