<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    protected $connection = 'mysql';

    public function up(): void
    {
        Schema::create('quote_passengers', function (Blueprint $table) {
            $table->id('id_quote_passenger');
            $table->foreignId('id_quote')->constrained('quote', 'id_quote')->cascadeOnDelete();
            $table->string('name', 200);
            $table->string('document', 100)->nullable();
            $table->timestamps();

            $table->index(['id_quote']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('quote_passengers');
    }
};
