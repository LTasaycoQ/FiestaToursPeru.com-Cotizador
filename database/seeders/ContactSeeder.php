<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;


class ContactSeeder extends Seeder
{
     public function run(): void
    {
        $contact = [
            ['name' => 'Luis Angel','last_names' => 'Tasayco','qualification' => 'Desconocido no se','email' => 'luis@casadelsol.com', 'first_phone' => '14356789045',  'second_phone' => '909087678', 'id_client' => 1],
            ['name' => 'Diego ramos','last_names' => 'Sanchez','qualification' => 'Desconocido no se','email' => 'diego@casadelsol.com', 'first_phone' => '987867876',  'second_phone' => '965456879', 'id_client' => 2],
        ];

        foreach ($contact as $contacts) {
            DB::table('contacts')->insert([
                'name' => $contacts['name'],
                'last_names' => $contacts['last_names'],
                'qualification' => $contacts['qualification'],
                'email' => $contacts['email'],
                'first_phone' => $contacts['first_phone'],
                'second_phone' => $contacts['second_phone'],
                'id_client' => $contacts['id_client'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
