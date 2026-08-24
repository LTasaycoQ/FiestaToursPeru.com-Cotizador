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
        Schema::create('bank_account', function (Blueprint $table) {
            $table->id('id_bank_account');
            $table->foreignId('id_bank')->nullable()->constrained('bank', 'id_bank')->onDelete('cascade');
            $table->foreignId('id_supplier')->nullable()->constrained('suppliers', 'id_supplier')->onDelete('cascade');
            $table->string('account_number', 100)->nullable();
            $table->string('cci', 100)->nullable();
            $table->string('currency', 40)->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bank_account');
    }
};
