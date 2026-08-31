<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    protected $connection = 'mysql';

    public function up(): void
    {
        Schema::create('service_images', function (Blueprint $table) {
            $table->id('id_service_image');
            $table->foreignId('id_service')->constrained('service', 'id_service')->cascadeOnDelete();
            $table->string('image_path', 500);
            $table->boolean('is_principal')->default(false);
            $table->timestamps();
            $table->softDeletes();

            $table->index('id_service');
            $table->index('is_principal');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('service_images');
    }
};
