<?php

namespace App\Exports;

use App\Models\Supplier;
use Illuminate\Support\Collection as SupportCollection;
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

class SuppliersExport implements FromCollection, WithHeadings, WithStyles, ShouldAutoSize, WithTitle, WithEvents
{
    const NAVY    = '0B1F3A';
    const GOLD    = 'C9A84C';
    const GOLD2   = 'E8C97A';
    const ROW_ALT = 'F8F5EE';

    private int $totalRows = 0;

    /** @var \Illuminate\Support\Collection|null Proveedores a exportar (opcional, si no se pasa se consultan todos) */
    protected $suppliers;

    public function __construct($suppliers = null)
    {
        $this->suppliers = $suppliers;
    }

    public function title(): string
    {
        return 'Proveedores';
    }

    public function collection()
    {
        $suppliers = $this->suppliers instanceof SupportCollection
            ? $this->suppliers
            : $this->querySuppliers();

        // Nos aseguramos de tener las relaciones cargadas (por si vienen de afuera sin eager loading)
        $suppliers->loadMissing(['country', 'city', 'category', 'contacts' => function ($q) {
            $q->orderBy('es_principal', 'desc')->orderBy('created_at');
        }]);

        $rows = [];

        foreach ($suppliers as $supplier) {
            $firstContact = $supplier->contacts->first();

            $rows[] = [
                'proveedor'     => $supplier->supplier_name,
                'razon_social'  => $supplier->business_name ?? '',
                'ruc'           => $supplier->tax_code ?? '',
                'pais'          => $supplier->country->name ?? '',
                'ciudad'        => $supplier->city->name ?? '',
                'categoria'     => $supplier->category->category_name ?? '',
                'contacto'      => $firstContact ? trim($firstContact->name . ' ' . ($firstContact->last_names ?? '')) : '',
                'mail'          => $firstContact ? ($firstContact->email ?? '') : '',
                'telefono'      => $this->getContactPhone($firstContact),
                'direccion'     => $supplier->address ?? '',
            ];

            foreach ($supplier->contacts->slice(1) as $contact) {
                $rows[] = [
                    'proveedor'     => '', // Vacío para mantener agrupación
                    'razon_social'  => '',
                    'ruc'           => '',
                    'pais'          => '',
                    'ciudad'        => '',
                    'categoria'     => '',
                    'contacto'      => trim($contact->name . ' ' . ($contact->last_names ?? '')),
                    'mail'          => $contact->email ?? '',
                    'telefono'      => $this->getContactPhone($contact),
                    'direccion'     => '',
                ];
            }
        }

        $this->totalRows = count($rows);

        return collect($rows);
    }

    private function querySuppliers()
    {
        return Supplier::with([
                'country',
                'city',
                'category',
                'contacts' => function ($q) {
                    $q->orderBy('es_principal', 'desc')->orderBy('created_at');
                },
            ])
            ->orderBy('supplier_name')
            ->get();
    }

    public function headings(): array
    {
        return [
            'PROVEEDOR',
            'RAZON_SOCIAL',
            'RUC',
            'PAIS',
            'CIUDAD',
            'CATEGORIA',
            'Contacto',
            'Mail',
            'Telefono',
            'DIRECCION',
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
                $lastColumn = 'J'; // 10 columnas: PROVEEDOR, RAZON_SOCIAL, RUC, PAIS, CIUDAD, CATEGORIA, Contacto, Mail, Telefono, DIRECCION
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
                $sheet->setCellValue('A2', 'FIESTA TOURS PERU  ·  Listado de Proveedores');
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

                // Total de registros (label en A:D, total en E)
                $totalsRow = $dataEnd + 1;
                $sheet->mergeCells("A{$totalsRow}:D{$totalsRow}");
                $sheet->setCellValue("A{$totalsRow}", 'TOTAL DE REGISTROS');
                $sheet->setCellValue("E{$totalsRow}", $this->totalRows);
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
                $sheet->getColumnDimension('A')->setWidth(28); // PROVEEDOR
                $sheet->getColumnDimension('B')->setWidth(28); // RAZON_SOCIAL
                $sheet->getColumnDimension('C')->setWidth(14); // RUC
                $sheet->getColumnDimension('D')->setWidth(18); // PAIS
                $sheet->getColumnDimension('E')->setWidth(18); // CIUDAD
                $sheet->getColumnDimension('F')->setWidth(20); // CATEGORIA
                $sheet->getColumnDimension('G')->setWidth(25); // Contacto
                $sheet->getColumnDimension('H')->setWidth(30); // Mail
                $sheet->getColumnDimension('I')->setWidth(25); // Telefono
                $sheet->getColumnDimension('J')->setWidth(30); // DIRECCION

                $sheet->setAutoFilter("A{$headerRow}:{$lastColumn}{$headerRow}");
                $sheet->freezePane("A{$dataStart}");
            },
        ];
    }
}
