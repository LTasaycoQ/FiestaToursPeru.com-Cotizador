<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SupplierCategoriesSeeder extends Seeder
{
    public function run(): void
    {
        $suppliersCategories = [
            ['category_name' => 'Operador'],
            ['category_name' => 'Alojamiento'],
            ['category_name' => 'Restaurante'],
        ];

        foreach ($suppliersCategories as $s) {
            DB::table('service_category')->insert([
                'category_name' => $s['category_name'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
