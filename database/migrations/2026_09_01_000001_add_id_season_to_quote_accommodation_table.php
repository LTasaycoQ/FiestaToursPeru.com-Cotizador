<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    protected $connection = 'mysql';

    public function up(): void
    {
        Schema::table('quote_accommodation', function (Blueprint $table) {
            $table->foreignId('id_season')->nullable()->after('id_tariff')->constrained('season', 'id_season')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('quote_accommodation', function (Blueprint $table) {
            $table->dropConstrainedForeignId('id_season');
        });
    }
};
