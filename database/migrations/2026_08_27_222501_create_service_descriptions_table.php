<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    protected $connection = 'mysql';

    public function up(): void
    {
        Schema::create('service_descriptions', function (Blueprint $table) {
            $table->id('id_service_description');
            $table->foreignId('id_service')->constrained('service', 'id_service')->cascadeOnDelete();
            $table->foreignId('id_language')->constrained('languages', 'id_language')->cascadeOnDelete();
            $table->string('service_title', 300)->nullable();
            $table->text('description')->nullable();
            $table->timestamps();
            $table->unique(['id_service', 'id_language']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('service_descriptions');
    }
};
