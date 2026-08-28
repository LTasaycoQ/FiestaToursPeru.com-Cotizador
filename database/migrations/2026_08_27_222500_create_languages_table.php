<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    protected $connection = 'mysql';

    public function up(): void
    {
        Schema::create('languages', function (Blueprint $table) {
            $table->id('id_language');
            $table->string('name', 100);
            $table->string('code', 10)->unique();
            $table->string('status', 20)->default('active');
            $table->timestamps();
        });

        DB::table('languages')->insert([
            ['name' => 'Español', 'code' => 'es', 'status' => 'active', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Inglés', 'code' => 'en', 'status' => 'active', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Portugués', 'code' => 'pt', 'status' => 'active', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('languages');
    }
};
