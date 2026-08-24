<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            UserSeeder::class,
            LabelsSeeder::class,
            ClientSeeder::class,
            ContactSeeder::class,
            // CategorieSeeder::class,
            // SubCategoriesSeeder::class,
        ]);
    }
}
