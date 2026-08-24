<?php
namespace App\Exports;

use App\Models\Contact;
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

class ContactsExport implements FromCollection, WithHeadings, WithStyles, ShouldAutoSize, WithTitle, WithEvents
{
    const NAVY    = '0B1F3A';
    const GOLD    = 'C9A84C';
    const GOLD2   = 'E8C97A';
    const ROW_ALT = 'F8F5EE';

    // 10 columnas: ID, Nombre, Apellidos, Cargo, Email, Teléfono 1, Teléfono 2,
    // Cliente, Proveedor, Principal, Fecha Registro
    const FIXED_COLUMNS = 11;

    private int $totalRows;

    /** @var int|null Si se especifica, filtra solo los contactos de este cliente */
    private ?int $clientId;

    public function __construct(?int $clientId = null)
    {
        $this->clientId = $clientId;
    }

    public function title(): string
    {
        return 'Contactos';
    }

    public function collection()
    {
        $query = Contact::with(['client', 'supplier'])
            ->orderBy('name');

        if ($this->clientId) {
            $query->where('id_client', $this->clientId);
        }

        $contacts = $query->get();
        $this->totalRows = $contacts->count();

        return $contacts->map(function ($contact) {
            return [
                'id'             => $contact->id_contacts,
                'name'           => $contact->name ?? '',
                'last_names'     => $contact->last_names ?? '',
                'qualification'  => $contact->qualification ?? '',
                'email'          => $contact->email ?? '',
                'first_phone'    => $contact->first_phone ?? '',
                'second_phone'   => $contact->second_phone ?? '',
                'client'         => $contact->client->name_client ?? '',
                'supplier'       => $contact->supplier->supplier_name ?? '',
                'principal'      => $contact->es_principal ? 'Sí' : 'No',
                'fecha_registro' => $contact->created_at->format('d/m/Y'),
            ];
        });
    }

    public function headings(): array
    {
        return [
            'ID',
            'Nombre',
            'Apellidos',
            'Cargo',
            'Email',
            'Teléfono 1',
            'Teléfono 2',
            'Cliente',
            'Proveedor',
            'Principal',
            'Fecha Registro',
        ];
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

                $totalColumns = self::FIXED_COLUMNS;
                $lastColumn   = $this->getColumnLetter($totalColumns);
                $lastRow      = $this->totalRows + 4;

                $sheet->insertNewRowBefore(1, 3);

                // FILA 1: Franja dorada
                $sheet->mergeCells("A1:{$lastColumn}1");
                $sheet->getRowDimension(1)->setRowHeight(6);
                $sheet->getStyle("A1:{$lastColumn}1")->getFill()
                    ->setFillType(Fill::FILL_SOLID)
                    ->getStartColor()->setRGB(self::GOLD);

                // FILA 2: Título
                $sheet->mergeCells("A2:{$lastColumn}2");
                $sheet->setCellValue('A2', 'FIESTA TOURS PERU  ·  Listado de Contactos');
                $sheet->getRowDimension(2)->setRowHeight(28);
                $sheet->getStyle('A2')->applyFromArray([
                    'font'      => ['bold' => true, 'size' => 14, 'color' => ['argb' => 'FF'.self::GOLD], 'name' => 'Arial'],
                    'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FF'.self::NAVY]],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_LEFT, 'vertical' => Alignment::VERTICAL_CENTER, 'indent' => 2],
                ]);

                // FILA 3: Subtítulo
                $sheet->mergeCells("A3:{$lastColumn}3");
                $sheet->setCellValue('A3', 'Generado: '.now()->format('d/m/Y H:i').' hrs  ·  Documento confidencial  ·  Uso interno  ·  www.fiestatoursperu.com');
                $sheet->getRowDimension(3)->setRowHeight(16);
                $sheet->getStyle('A3')->applyFromArray([
                    'font'      => ['italic' => true, 'size' => 8, 'color' => ['argb' => 'FF94A3B8'], 'name' => 'Arial'],
                    'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FF'.self::NAVY]],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_LEFT, 'vertical' => Alignment::VERTICAL_CENTER, 'indent' => 2],
                ]);

                // FILA 4: Encabezados
                $headerRow = 4;
                $sheet->getRowDimension($headerRow)->setRowHeight(20);
                $sheet->getStyle("A{$headerRow}:{$lastColumn}{$headerRow}")->applyFromArray([
                    'font'      => ['bold' => true, 'size' => 9, 'color' => ['argb' => 'FF'.self::GOLD], 'name' => 'Arial'],
                    'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FF'.self::NAVY]],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                    'borders'   => ['bottom' => ['borderStyle' => Border::BORDER_MEDIUM, 'color' => ['argb' => 'FF'.self::GOLD]]],
                ]);

                // Datos
                $dataStart = $headerRow + 1;
                $dataEnd   = $dataStart + $this->totalRows - 1;

                for ($row = $dataStart; $row <= $dataEnd; $row++) {
                    $sheet->getRowDimension($row)->setRowHeight(16);
                    $bgColor = ($row - $dataStart) % 2 === 0 ? 'FFFFFFFF' : 'FF'.self::ROW_ALT;

                    $sheet->getStyle("A{$row}:{$lastColumn}{$row}")->applyFromArray([
                        'font'      => ['size' => 9, 'name' => 'Arial'],
                        'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => $bgColor]],
                        'alignment' => ['vertical' => Alignment::VERTICAL_CENTER],
                        'borders'   => ['bottom' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['argb' => 'FFE2E8F0']]],
                    ]);

                    $sheet->getStyle("B{$row}")->getFont()->setBold(true);
                }

                // Total de registros
                $totalsRow = $dataEnd + 1;
                $sheet->mergeCells("A{$totalsRow}:D{$totalsRow}");
                $sheet->setCellValue("A{$totalsRow}", 'TOTAL DE REGISTROS');
                $sheet->setCellValue("E{$totalsRow}", $this->totalRows);
                $sheet->getRowDimension($totalsRow)->setRowHeight(18);
                $sheet->getStyle("A{$totalsRow}:{$lastColumn}{$totalsRow}")->applyFromArray([
                    'font'      => ['bold' => true, 'size' => 9, 'color' => ['argb' => 'FF'.self::NAVY]],
                    'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FFFFF8E7']],
                    'alignment' => ['vertical' => Alignment::VERTICAL_CENTER],
                    'borders'   => ['top' => ['borderStyle' => Border::BORDER_MEDIUM, 'color' => ['argb' => 'FF'.self::GOLD]]],
                ]);

                // Footer
                $bottomRow = $totalsRow + 1;
                $sheet->mergeCells("A{$bottomRow}:{$lastColumn}{$bottomRow}");
                $sheet->setCellValue("A{$bottomRow}", 'Fiesta Tours Peru © '.now()->format('Y').'  ·  Lima, Perú  ·  Sistema de Gestión Interna');
                $sheet->getRowDimension($bottomRow)->setRowHeight(14);
                $sheet->getStyle("A{$bottomRow}")->applyFromArray([
                    'font'      => ['italic' => true, 'size' => 8, 'color' => ['argb' => 'FF'.self::NAVY]],
                    'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FF'.self::GOLD2]],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                ]);

                // Anchos de columnas
                $widths = [
                    'A' => 6,  'B' => 18, 'C' => 18, 'D' => 16,
                    'E' => 24, 'F' => 14, 'G' => 14, 'H' => 22,
                    'I' => 22, 'J' => 10, 'K' => 14,
                ];
                foreach ($widths as $col => $width) {
                    $sheet->getColumnDimension($col)->setWidth($width);
                }

                $sheet->freezePane("A{$dataStart}");
            },
        ];
    }

    private function getColumnLetter(int $index): string
    {
        $letter = '';
        while ($index > 0) {
            $index--;
            $letter = chr(65 + ($index % 26)) . $letter;
            $index = intdiv($index, 26);
        }
        return $letter;
    }
}
