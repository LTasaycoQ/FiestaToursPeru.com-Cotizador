<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithTitle;

class ServicesTemplateExport implements FromArray, WithTitle
{
    public function title(): string
    {
        return 'Cotización';
    }

    public function array(): array
    {
        return [
            ['PROVEEDOR', 'CATEGORIA', 'Traslados Tours y Paquetes', 'Regular Economico', '', 'Regular VIP', '', 'Servicios Privados', '', '', '', '', '', '', '', ''],
            ['', '','', 'Min 1', 'Min 2', 'Min 1', 'Min 2', '1', '2', '3/4', '5/9', '10/14', '15/19', '20/24', '25/29', '30/up'],
        ];
    }
}
