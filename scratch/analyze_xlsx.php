<?php

require 'vendor/autoload.php';

use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;

class SimpleImport implements ToCollection
{
    public function collection(Collection $rows)
    {
        $headers = $rows->first();
        echo "Headers:\n";
        foreach ($headers as $i => $h) {
            echo "$i: $h\n";
        }

        $sample = $rows->get(1);
        echo "\nSample Data (Row 2):\n";
        print_r($sample->toArray());
    }
}

// Since we are running outside of Laravel container, this might not work easily with Facades.
// Let's use PhpSpreadsheet directly.

use PhpOffice\PhpSpreadsheet\IOFactory;

$spreadsheet = IOFactory::load('Gallo - Dados Atdr 2026.xlsx');
$sheet = $spreadsheet->getActiveSheet();
$rows = $sheet->toArray();

echo "Headers:\n";
foreach ($rows[0] as $i => $h) {
    echo "$i: $h\n";
}

echo "\nSample Data (Row 2):\n";
print_r($rows[1]);
