<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('tariff', function (Blueprint $table) {
            $table->enum('pricing_type', ['flat', 'tiered'])->default('flat')->after('id_season');
        });

        DB::statement('UPDATE tariff JOIN service ON service.id_service = tariff.id_service SET tariff.pricing_type = service.pricing_type WHERE service.pricing_type IS NOT NULL');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tariff', function (Blueprint $table) {
            $table->dropColumn('pricing_type');
        });
    }
};
