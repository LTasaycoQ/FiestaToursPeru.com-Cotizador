<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    protected $connection = 'mysql';

    public function up(): void
    {
        Schema::table('season', function (Blueprint $table) {
            $table->dropForeign(['id_service']);
            $table->dropIndex(['id_service']);
            $table->dropColumn('id_service');
        });
    }

    public function down(): void
    {
        Schema::table('season', function (Blueprint $table) {
            $table->foreignId('id_service')
                ->nullable()
                ->constrained('service', 'id_service')
                ->nullOnDelete();
        });
    }
};
