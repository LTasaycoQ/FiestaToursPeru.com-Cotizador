<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SubCategoriesSeeder extends Seeder
{
    public function run(): void
    {
        $subcategory = [
            ['id_category' => 1, 'name' => 'Regular Economico'],
            ['id_category' => 1, 'name' => 'Regular Vip'],
            ['id_category' => 1, 'name' => 'Privado'],
            ['id_category' => 2, 'name' => 'Regular Economico'],
            ['id_category' => 2, 'name' => 'Regular Vip'],
            ['id_category' => 2, 'name' => 'Privado'],
            ['id_category' => 3, 'name' => 'Regular Economico'],
            ['id_category' => 3, 'name' => 'Regular Vip'],
            ['id_category' => 3, 'name' => 'Privado'],
            ['id_category' => 4, 'name' => 'SGL'],
            ['id_category' => 4, 'name' => 'DBL'],
            ['id_category' => 4, 'name' => 'TPL'],
            ['id_category' => 5, 'name' => 'SGL'],
            ['id_category' => 5, 'name' => 'DBL'],
            ['id_category' => 5, 'name' => 'TPL'],
            ['id_category' => 6, 'name' => 'SGL'],
            ['id_category' => 6, 'name' => 'DBL'],
            ['id_category' => 6, 'name' => 'TPL'],
            ['id_category' => 7, 'name' => 'SGL'],
            ['id_category' => 7, 'name' => 'DBL'],
            ['id_category' => 7, 'name' => 'TPL'],
        ];

        foreach ($subcategory as $s) {
            DB::table('sub_categorie')->insert([
                'id_category' => $s['id_category'],
                'name' => $s['name'],
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
