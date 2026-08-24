<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CategorieSeeder extends Seeder
{
    public function run(): void
    {
        $category = [
            ['name' => 'Traslado', 'pricing_type' => 'tiered'],
            ['name' => 'Actividad', 'pricing_type' => 'tiered'],
            ['name' => 'Restaurante', 'pricing_type' => 'tiered'],
            ['name' => 'Turista', 'pricing_type' => 'flat'],
            ['name' => 'Turista Superior', 'pricing_type' => 'flat'],
            ['name' => 'Boutique', 'pricing_type' => 'flat'],
            ['name' => '5 start', 'pricing_type' => 'flat'],
        ];

        foreach ($category as $c) {
            DB::table('service_category')->insert([
                'name' => $c['name'],
                'pricing_type' => $c['pricing_type'],
                'is_accommodation' => 0,
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
