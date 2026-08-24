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
            ['name_client' => 'Casa del Sol Machu Picchu', 'business_name' => 'OPERADOR TURISTICO LOS ANDES S.A.C. - OTAND SAC', 'tax_code' => '123456789', 'type_client' => 'cliente', 'general_phone' => '123456789', 'general_email' => 'info@casadelsol.com'],
            ['name_client' => 'RUNCU AREQUIPA', 'business_name' => 'HOSTAL VALLECITO INN E.I.R.LTDA.', 'tax_code' => '1234565466789', 'type_client' => 'cliente', 'general_phone' => '12344356789', 'general_email' => 'info@runcu.com'],
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
