<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class AtiradoresTemplateExport implements FromArray, WithHeadings, WithStyles
{
    public function headings(): array
    {
        return [
            'NR',
            'Nome Completo',
            'NOME DE GUERRA',
            'CPF',
            'RA',
            'E-mail pessoal',
            'TELEFONE',
        ];
    }

    public function array(): array
    {
        // Retorna uma linha de exemplo para orientar o preenchimento
        return [
            [
                '01',
                'JOÃO DA SILVA SANTOS',
                'SANTOS',
                '12345678901',
                '123456789012',
                'joao@email.com',
                '(17) 99999-9999',
            ],
        ];
    }

    public function styles(Worksheet $sheet)
    {
        // Estilizar o cabeçalho
        $sheet->getStyle('A1:G1')->applyFromArray([
            'font' => [
                'bold' => true,
                'color' => ['rgb' => 'FFFFFF'],
                'size' => 11,
            ],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => ['rgb' => '4A5C48'],
            ],
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
            ],
        ]);

        // Estilizar a linha de exemplo com fundo claro
        $sheet->getStyle('A2:G2')->applyFromArray([
            'font' => [
                'italic' => true,
                'color' => ['rgb' => '999999'],
            ],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'F5F5F5'],
            ],
        ]);

        // Ajustar largura das colunas
        $sheet->getColumnDimension('A')->setWidth(8);   // NR
        $sheet->getColumnDimension('B')->setWidth(35);  // Nome Completo
        $sheet->getColumnDimension('C')->setWidth(20);  // Nome de Guerra
        $sheet->getColumnDimension('D')->setWidth(15);  // CPF
        $sheet->getColumnDimension('E')->setWidth(15);  // RA
        $sheet->getColumnDimension('F')->setWidth(30);  // E-mail
        $sheet->getColumnDimension('G')->setWidth(20);  // Telefone

        return [];
    }
}
