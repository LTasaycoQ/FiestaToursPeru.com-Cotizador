<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    protected $connection = 'mysql';

    public function up(): void
    {
        Schema::table('quote', function (Blueprint $table) {
            $table->foreignId('id_language')->nullable()->after('id_labels')->constrained('languages', 'id_language')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('quote', function (Blueprint $table) {
            $table->dropConstrainedForeignId('id_language');
        });
    }
};
