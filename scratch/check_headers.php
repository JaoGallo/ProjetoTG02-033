<?php
require 'vendor/autoload.php';
use PhpOffice\PhpSpreadsheet\IOFactory;

$file = 'Gallo - Dados Atdr 2026.xlsx';
if (!file_exists($file)) {
    die("Arquivo não encontrado: $file\n");
}

$spreadsheet = IOFactory::load($file);
$sheet = $spreadsheet->getActiveSheet();
$rows = $sheet->toArray();

echo "--- HEADERS ENCONTRADOS ---\n";
if (isset($rows[0])) {
    foreach ($rows[0] as $i => $header) {
        echo "Coluna $i: [$header]\n";
    }
} else {
    echo "Nenhuma linha encontrada.\n";
}

echo "\n--- PRIMEIRA LINHA DE DADOS ---\n";
if (isset($rows[1])) {
    print_r($rows[1]);
}
