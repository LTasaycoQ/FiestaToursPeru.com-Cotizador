<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    protected $connection = 'mysql';

    public function up(): void
    {
        Schema::table('detail_quote', function (Blueprint $table): void {
            $table->boolean('is_optional')->default(false)->after('notes');
        });
    }

    public function down(): void
    {
        Schema::table('detail_quote', function (Blueprint $table): void {
            $table->dropColumn('is_optional');
        });
    }
};
