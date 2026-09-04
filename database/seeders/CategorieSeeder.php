<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CategorieSeeder extends Seeder
{
    public function run(): void
    {
        $category = [
            ['name' => 'Traslado', 'pricing_type' => 'tiered', 'is_accomodation' => 0,],
            ['name' => 'Tour', 'pricing_type' => 'tiered','is_accomodation' => 0,],
            ['name' => 'Actividad', 'pricing_type' => 'tiered','is_accomodation' => 0,],
            ['name' => 'Restaurante', 'pricing_type' => 'tiered','is_accomodation' => 0,],
            ['name' => 'Turista', 'pricing_type' => 'flat','is_accomodation' => 1],
            ['name' => 'Turista Superior', 'pricing_type' => 'flat','is_accomodation' => 1],
            ['name' => 'Boutique', 'pricing_type' => 'flat','is_accomodation' => 1],
            ['name' => '5 start', 'pricing_type' => 'flat','is_accomodation' => 1],
        ];

        foreach ($category as $c) {
            DB::table('service_category')->insert([
                'name' => $c['name'],
                'pricing_type' => $c['pricing_type'],
                'is_accommodation' => $c['is_accomodation'],
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
