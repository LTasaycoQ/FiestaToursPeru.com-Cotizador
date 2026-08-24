<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class LabelsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $labels = [
            ['name_labels' => 'LATINO', 'status' => 'active'],
            ['name_labels' => 'USA', 'status' => 'active'],
        ];

        foreach ($labels as $l) {
            DB::table('labels')->insert([
                'name_labels' => $l['name_labels'],
                'status' => $l['status'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
