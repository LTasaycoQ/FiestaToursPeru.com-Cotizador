<?php
namespace App\Exports;

use App\Models\Client;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class ClientsExport implements FromCollection, WithHeadings, WithStyles, ShouldAutoSize, WithTitle, WithEvents
{
    const NAVY    = '0B1F3A';
    const GOLD    = 'C9A84C';
    const GOLD2   = 'E8C97A';
    const ROW_ALT = 'F8F5EE';

    private int $totalRows = 0;

    public function title(): string
    {
        return 'Clientes';
    }

    public function collection()
    {
        $clients = Client::with([
                'country',
                'city',
                'contacts' => function ($q) {
                    $q->orderBy('es_principal', 'desc')->orderBy('created_at');
                },
            ])
            ->orderBy('name_client')
            ->get();

        $rows = [];

        foreach ($clients as $client) {
            $firstContact = $client->contacts->first();

            $rows[] = [
                'nombre'   => $client->name_client,
                'pais'     => $client->country->name ?? '',
                'ciudad'   => $client->city->name ?? '',
                'contacto' => $firstContact ? $firstContact->name : '',
                'mail'     => $firstContact ? $firstContact->email : '',
                'telefono' => $this->getContactPhone($firstContact),
            ];

            foreach ($client->contacts->slice(1) as $contact) {
                $rows[] = [
                    'nombre'   => '', // Vacío para mantener agrupación
                    'pais'     => '',
                    'ciudad'   => '',
                    'contacto' => $contact->name ?? '',
                    'mail'     => $contact->email ?? '',
                    'telefono' => $this->getContactPhone($contact),
                ];
            }
        }

        $this->totalRows = count($rows);

        return collect($rows);
    }

    public function headings(): array
    {
        return [
            'NOMBRE',
            'PAIS',
            'CIUDAD',
            'Contacto',
            'Mail',
            'Telefono',
        ];
    }

    private function getContactPhone($contact): string
    {
        if (!$contact) return '';
        $phone = $contact->first_phone ?? '';
        if ($contact->second_phone) {
            $phone .= ' / ' . $contact->second_phone;
        }
        return $phone;
    }

    public function styles(Worksheet $sheet): array
    {
        return [];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $lastColumn = 'F'; // 6 columnas: NOMBRE, PAIS, CIUDAD, Contacto, Mail, Telefono
                $lastRow = $this->totalRows + 4;

                $sheet->insertNewRowBefore(1, 3);

                // FILA 1: Franja dorada
                $sheet->mergeCells("A1:{$lastColumn}1");
                $sheet->getRowDimension(1)->setRowHeight(6);
                $sheet->getStyle("A1:{$lastColumn}1")->getFill()
                    ->setFillType(Fill::FILL_SOLID)
                    ->getStartColor()->setRGB(self::GOLD);

                // FILA 2: Título
                $sheet->mergeCells("A2:{$lastColumn}2");
                $sheet->setCellValue('A2', 'FIESTA TOURS PERU  ·  Listado de Clientes');
                $sheet->getRowDimension(2)->setRowHeight(28);
                $sheet->getStyle('A2')->applyFromArray([
                    'font' => ['bold' => true, 'size' => 14, 'color' => ['argb' => 'FF' . self::GOLD], 'name' => 'Arial'],
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FF' . self::NAVY]],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_LEFT, 'vertical' => Alignment::VERTICAL_CENTER, 'indent' => 2],
                ]);

                // FILA 3: Subtítulo
                $sheet->mergeCells("A3:{$lastColumn}3");
                $sheet->setCellValue('A3', 'Generado: ' . now()->format('d/m/Y H:i') . ' hrs  ·  Documento confidencial  ·  Uso interno  ·  www.fiestatoursperu.com');
                $sheet->getRowDimension(3)->setRowHeight(16);
                $sheet->getStyle('A3')->applyFromArray([
                    'font' => ['italic' => true, 'size' => 8, 'color' => ['argb' => 'FF94A3B8'], 'name' => 'Arial'],
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FF' . self::NAVY]],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_LEFT, 'vertical' => Alignment::VERTICAL_CENTER, 'indent' => 2],
                ]);

                // FILA 4: Encabezados
                $headerRow = 4;
                $sheet->getRowDimension($headerRow)->setRowHeight(20);
                $sheet->getStyle("A{$headerRow}:{$lastColumn}{$headerRow}")->applyFromArray([
                    'font' => ['bold' => true, 'size' => 9, 'color' => ['argb' => 'FF' . self::GOLD], 'name' => 'Arial'],
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FF' . self::NAVY]],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                    'borders' => ['bottom' => ['borderStyle' => Border::BORDER_MEDIUM, 'color' => ['argb' => 'FF' . self::GOLD]]],
                ]);

                foreach (range('A', $lastColumn) as $col) {
                    $sheet->getStyle("{$col}{$headerRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                }

                // Datos
                $dataStart = $headerRow + 1;
                $dataEnd = $dataStart + $this->totalRows - 1;

                for ($row = $dataStart; $row <= $dataEnd; $row++) {
                    $sheet->getRowDimension($row)->setRowHeight(16);
                    $bgColor = ($row - $dataStart) % 2 === 0 ? 'FFFFFFFF' : 'FF' . self::ROW_ALT;

                    $sheet->getStyle("A{$row}:{$lastColumn}{$row}")->applyFromArray([
                        'font' => ['size' => 9, 'name' => 'Arial'],
                        'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => $bgColor]],
                        'alignment' => ['vertical' => Alignment::VERTICAL_CENTER],
                        'borders' => ['bottom' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['argb' => 'FFE2E8F0']]],
                    ]);

                    if ($sheet->getCell("A{$row}")->getValue() !== '') {
                        $sheet->getStyle("A{$row}")->getFont()->setBold(true);
                    }
                }

                // Total de registros (label en A:C, total en D)
                $totalsRow = $dataEnd + 1;
                $sheet->mergeCells("A{$totalsRow}:C{$totalsRow}");
                $sheet->setCellValue("A{$totalsRow}", 'TOTAL DE REGISTROS');
                $sheet->setCellValue("D{$totalsRow}", $this->totalRows);
                $sheet->getRowDimension($totalsRow)->setRowHeight(18);
                $sheet->getStyle("A{$totalsRow}:{$lastColumn}{$totalsRow}")->applyFromArray([
                    'font' => ['bold' => true, 'size' => 9, 'color' => ['argb' => 'FF' . self::NAVY]],
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FFFFF8E7']],
                    'alignment' => ['vertical' => Alignment::VERTICAL_CENTER],
                    'borders' => ['top' => ['borderStyle' => Border::BORDER_MEDIUM, 'color' => ['argb' => 'FF' . self::GOLD]]],
                ]);

                // Footer
                $bottomRow = $totalsRow + 1;
                $sheet->mergeCells("A{$bottomRow}:{$lastColumn}{$bottomRow}");
                $sheet->setCellValue("A{$bottomRow}", 'Fiesta Tours Peru © ' . now()->format('Y') . '  ·  Lima, Perú  ·  Sistema de Gestión Interna');
                $sheet->getRowDimension($bottomRow)->setRowHeight(14);
                $sheet->getStyle("A{$bottomRow}")->applyFromArray([
                    'font' => ['italic' => true, 'size' => 8, 'color' => ['argb' => 'FF' . self::NAVY]],
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FF' . self::GOLD2]],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                ]);

                // Anchos de columnas
                $sheet->getColumnDimension('A')->setWidth(30); // NOMBRE
                $sheet->getColumnDimension('B')->setWidth(20); // PAIS
                $sheet->getColumnDimension('C')->setWidth(20); // CIUDAD
                $sheet->getColumnDimension('D')->setWidth(25); // Contacto
                $sheet->getColumnDimension('E')->setWidth(30); // Mail
                $sheet->getColumnDimension('F')->setWidth(25); // Telefono

                $sheet->setAutoFilter("A{$headerRow}:{$lastColumn}{$headerRow}");
                $sheet->freezePane("A{$dataStart}");
            },
        ];
    }
}
