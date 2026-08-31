<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    protected $connection = 'mysql';

    public function up(): void
    {
        Schema::table('quote_day', function (Blueprint $table) {
            $table->string('name', 255)->nullable()->after('day_number')->comment('Nombre personalizado del día');
        });
    }

    public function down(): void
    {
        Schema::table('quote_day', function (Blueprint $table) {
            $table->dropColumn('name');
        });
    }
};
