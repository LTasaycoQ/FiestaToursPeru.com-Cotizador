<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class LanguageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $languages = [
            ['name' => 'Español', 'code' => 'es'],
            ['name' => 'Ingles', 'code' => 'en' ],
            ['name' => 'Portugues', 'code' => 'pt'],
        ];

        foreach ($languages as $l) {
            DB::table('languages')->insert([
                'name' => $l['name'],
                'code' => $l['code'],
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
