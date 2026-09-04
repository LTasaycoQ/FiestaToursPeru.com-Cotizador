<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ClientSeeder extends Seeder
{
    public function run(): void
    {
        $clients = [
            ['name_client' => 'Ventura Viagens', 'business_name' => 'VENTURAS & AVENTURAS VIAGENS E TURISMO LTDA', 'tax_code' => '84317439113', 'type_client' => 'cliente', 'general_phone' => '473164817', 'general_email' => 'info@venturasviagens'],
            ['name_client' => 'Marco Tours', 'business_name' => 'MARCO TOURS E.I.R.LTDA.', 'tax_code' => '84317439113', 'type_client' => 'cliente', 'general_phone' => '473164817', 'general_email' => 'info@venturasviagens'],
        ];

        foreach ($clients as $client) {
            DB::table('clients')->insert([
                'name_client' => $client['name_client'],
                'business_name' => $client['business_name'],
                'tax_code' => $client['tax_code'],
                'type_client' => $client['type_client'],
                'general_phone' => $client['general_phone'],
                'general_email' => $client['general_email'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
