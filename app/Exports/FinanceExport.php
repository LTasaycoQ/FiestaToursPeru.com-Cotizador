<?php

namespace App\Exports;

use App\Models\Finance\ProyectModel;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Color;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class FinanceExport implements FromCollection, ShouldAutoSize, WithEvents, WithHeadings, WithStyles, WithTitle
{
    const NAVY = '0B1F3A';

    const GOLD = 'C9A84C';

    const GOLD2 = 'E8C97A';

    const ROW_ALT = 'F8F5EE';

    private int $projectId;

    private string $type;

    private int $totalRows = 0;

    public function __construct(int $projectId, string $type = 'all')
    {
        $this->projectId = $projectId;
        $this->type = $type;
    }

    public function title(): string
    {
        $project = ProyectModel::find($this->projectId);
        $name = $project ? $project->name : 'Proyecto';

        $titles = [
            'all' => 'Todos los Gastos',
            'expenses' => 'Gastos Regulares',
            'operational' => 'Gestion Operativa',
            'recharges' => 'Recargas',
        ];

        return $name.' - '.($titles[$this->type] ?? 'Reporte');
    }

    public function collection()
    {
        $project = ProyectModel::with(['balance', 'expenses'])->find($this->projectId);

        if (! $project) {
            $this->totalRows = 0;

            return collect([]);
        }

        $rows = [];

        switch ($this->type) {
            case 'all':
                // ─── TODOS LOS GASTOS JUNTOS ───
                foreach ($project->expenses as $expense) {
                    $tipo = $this->getTipoGasto($expense);
                    $rows[] = [
                        $expense->expense_date ? $expense->expense_date->format('d/m/Y') : '',
                        $expense->name_people ?? '',
                        $expense->reservation_code ?? '',
                        $expense->file_number ?? '',
                        $expense->amount,
                        $tipo,
                    ];
                }
                break;

            case 'expenses':
                // ─── SOLO GASTOS REGULARES ───
                $expenses = $project->expenses->filter(function ($expense) {
                    return ! is_null($expense->file_number) && $expense->file_number !== '';
                });

                foreach ($expenses as $expense) {
                    $rows[] = [
                        $expense->expense_date ? $expense->expense_date->format('d/m/Y') : '',
                        $expense->name_people ?? '',
                        $expense->reservation_code ?? '',
                        $expense->file_number ?? '',
                        $expense->amount,
                        'REGULAR',
                    ];
                }
                break;

            case 'operational':
                // ─── SOLO GASTOS OPERATIVOS ───
                $expenses = $project->expenses->filter(function ($expense) {
                    return is_null($expense->file_number) || $expense->file_number === '';
                });

                foreach ($expenses as $expense) {
                    $rows[] = [
                        $expense->expense_date ? $expense->expense_date->format('d/m/Y') : '',
                        $expense->name_people ?? 'Gestion Operativa',
                        $expense->reservation_code ?? 'OTRO',
                        '',
                        $expense->amount,
                        'OPERATIVO',
                    ];
                }
                break;

            case 'recharges':
                // ─── RECARGAS ───
                $recharges = $project->balance ? $project->balance->recharges : collect();

                foreach ($recharges as $recharge) {
                    $rows[] = [
                        $recharge->recharge_date ? $recharge->recharge_date->format('d/m/Y') : '',
                        'RECARGA',
                        '',
                        '',
                        $recharge->amount,
                        'RECARGA',
                    ];
                }
                break;
        }

        $this->totalRows = count($rows);

        return collect($rows);
    }

    private function getTipoGasto($expense): string
    {
        if (is_null($expense->file_number) || $expense->file_number === '') {
            return 'OPERATIVO';
        }

        return 'REGULAR';
    }

    public function headings(): array
    {
        switch ($this->type) {
            case 'all':
                return ['FECHA', 'PERSONA', 'CODIGO', 'N° FILE', 'MONTO', 'TIPO'];
            case 'expenses':
                return ['FECHA', 'PERSONA', 'CODIGO', 'N° FILE', 'MONTO', 'TIPO'];
            case 'operational':
                return ['FECHA', 'PERSONA', 'TIPO', 'N° FILE', 'MONTO', 'TIPO'];
            case 'recharges':
                return ['FECHA', 'DESCRIPCION', '', '', 'MONTO', 'TIPO'];
            default:
                return [];
        }
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

                $headers = $this->headings();
                $lastColumn = chr(64 + count($headers));

                // Insertar 3 filas para encabezado
                $sheet->insertNewRowBefore(1, 3);

                // FILA 1: Franja dorada
                $sheet->mergeCells("A1:{$lastColumn}1");
                $sheet->getRowDimension(1)->setRowHeight(6);
                $sheet->getStyle("A1:{$lastColumn}1")->getFill()
                    ->setFillType(Fill::FILL_SOLID)
                    ->getStartColor()->setRGB(self::GOLD);

                // FILA 2: Título
                $sheet->mergeCells("A2:{$lastColumn}2");
                $titles = [
                    'all' => 'Todos los Gastos del Proyecto',
                    'expenses' => 'Gastos Regulares',
                    'operational' => 'Gastos de Gestion Operativa',
                    'recharges' => 'Historial de Recargas',
                ];
                $sheet->setCellValue('A2', 'FIESTA TOURS PERU  ·  '.($titles[$this->type] ?? 'Reporte'));
                $sheet->getRowDimension(2)->setRowHeight(28);
                $sheet->getStyle('A2')->applyFromArray([
                    'font' => ['bold' => true, 'size' => 14, 'color' => ['argb' => 'FF'.self::GOLD], 'name' => 'Arial'],
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FF'.self::NAVY]],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_LEFT, 'vertical' => Alignment::VERTICAL_CENTER, 'indent' => 2],
                ]);

                // FILA 3: Subtítulo
                $sheet->mergeCells("A3:{$lastColumn}3");
                $sheet->setCellValue('A3', 'Generado: '.now()->format('d/m/Y H:i').' hrs  ·  Documento confidencial  ·  Uso interno  ·  www.fiestatoursperu.com');
                $sheet->getRowDimension(3)->setRowHeight(16);
                $sheet->getStyle('A3')->applyFromArray([
                    'font' => ['italic' => true, 'size' => 8, 'color' => ['argb' => 'FF94A3B8'], 'name' => 'Arial'],
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FF'.self::NAVY]],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_LEFT, 'vertical' => Alignment::VERTICAL_CENTER, 'indent' => 2],
                ]);

                // FILA 4: Encabezados
                $headerRow = 4;
                $sheet->getRowDimension($headerRow)->setRowHeight(20);
                $sheet->getStyle("A{$headerRow}:{$lastColumn}{$headerRow}")->applyFromArray([
                    'font' => ['bold' => true, 'size' => 9, 'color' => ['argb' => 'FF'.self::GOLD], 'name' => 'Arial'],
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FF'.self::NAVY]],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                    'borders' => ['bottom' => ['borderStyle' => Border::BORDER_MEDIUM, 'color' => ['argb' => 'FF'.self::GOLD]]],
                ]);

                foreach (range('A', $lastColumn) as $col) {
                    $sheet->getStyle("{$col}{$headerRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                }

                // Datos
                $dataStart = $headerRow + 1;
                $dataEnd = $dataStart + $this->totalRows - 1;

                for ($row = $dataStart; $row <= $dataEnd; $row++) {
                    $sheet->getRowDimension($row)->setRowHeight(16);

                    // Color de fondo según tipo de gasto
                    $tipo = $sheet->getCell("F{$row}")->getValue();
                    $bgColor = ($row - $dataStart) % 2 === 0 ? 'FFFFFFFF' : 'FF'.self::ROW_ALT;

                    // Si es OPERATIVO, un tono más claro
                    if ($tipo === 'OPERATIVO') {
                        $bgColor = ($row - $dataStart) % 2 === 0 ? 'FFFFF3E0' : 'FFFFE8C8';
                    }

                    // Si es RECARGA, tono verde
                    if ($tipo === 'RECARGA') {
                        $bgColor = ($row - $dataStart) % 2 === 0 ? 'FFE8F5E9' : 'FFC8E6C9';
                    }

                    $sheet->getStyle("A{$row}:{$lastColumn}{$row}")->applyFromArray([
                        'font' => ['size' => 9, 'name' => 'Arial'],
                        'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => $bgColor]],
                        'alignment' => ['vertical' => Alignment::VERTICAL_CENTER],
                        'borders' => ['bottom' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['argb' => 'FFE2E8F0']]],
                    ]);

                    // Formato de moneda para la columna MONTO (columna E)
                    $sheet->getStyle("E{$row}")->getNumberFormat()
                        ->setFormatCode('"$"#,##0.00');

                    // Color del texto según tipo
                    if ($tipo === 'OPERATIVO') {
                        $sheet->getStyle("F{$row}")->getFont()->setColor(new Color('FFD35400'))->setBold(true);
                    } elseif ($tipo === 'RECARGA') {
                        $sheet->getStyle("F{$row}")->getFont()->setColor(new Color('FF27AE60'))->setBold(true);
                    } else {
                        $sheet->getStyle("F{$row}")->getFont()->setColor(new Color('FF2C3E50'))->setBold(true);
                    }
                }

                // Total de registros
                $totalsRow = $dataEnd + 1;
                $sheet->mergeCells("A{$totalsRow}:D{$totalsRow}");
                $sheet->setCellValue("A{$totalsRow}", 'TOTAL DE REGISTROS');
                $sheet->setCellValue("E{$totalsRow}", $this->totalRows);
                $sheet->getRowDimension($totalsRow)->setRowHeight(18);
                $sheet->getStyle("A{$totalsRow}:{$lastColumn}{$totalsRow}")->applyFromArray([
                    'font' => ['bold' => true, 'size' => 9, 'color' => ['argb' => 'FF'.self::NAVY]],
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FFFFF8E7']],
                    'alignment' => ['vertical' => Alignment::VERTICAL_CENTER],
                    'borders' => ['top' => ['borderStyle' => Border::BORDER_MEDIUM, 'color' => ['argb' => 'FF'.self::GOLD]]],
                ]);

                // Footer
                $bottomRow = $totalsRow + 1;
                $sheet->mergeCells("A{$bottomRow}:{$lastColumn}{$bottomRow}");
                $sheet->setCellValue("A{$bottomRow}", 'Fiesta Tours Peru © '.now()->format('Y').'  ·  Lima, Peru  ·  Sistema de Gestion Interna');
                $sheet->getRowDimension($bottomRow)->setRowHeight(14);
                $sheet->getStyle("A{$bottomRow}")->applyFromArray([
                    'font' => ['italic' => true, 'size' => 8, 'color' => ['argb' => 'FF'.self::NAVY]],
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FF'.self::GOLD2]],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                ]);

                // Anchos de columnas
                $sheet->getColumnDimension('A')->setWidth(18);  // FECHA
                $sheet->getColumnDimension('B')->setWidth(30);  // PERSONA
                $sheet->getColumnDimension('C')->setWidth(18);  // CODIGO
                $sheet->getColumnDimension('D')->setWidth(18);  // N° FILE
                $sheet->getColumnDimension('E')->setWidth(16);  // MONTO
                $sheet->getColumnDimension('F')->setWidth(14);  // TIPO

                $sheet->setAutoFilter("A{$headerRow}:{$lastColumn}{$headerRow}");
                $sheet->freezePane("A{$dataStart}");
            },
        ];
    }
}
