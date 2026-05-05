import XLSX from 'xlsx';
import fs from 'fs';

const filePath = './temp_gallo.xlsx';
const fileBuffer = fs.readFileSync(filePath);
const workbook = XLSX.read(fileBuffer);
const sheetName = workbook.SheetNames[0];
const worksheet = workbook.Sheets[sheetName];
const data = XLSX.utils.sheet_to_json(worksheet);

let seederContent = `<?php

namespace Database\\Seeders;

use Illuminate\\Database\\Seeder;
use App\\Models\\User;
use Illuminate\\Support\\Facades\\Hash;

class ImportShooters2026Seeder extends Seeder
{
    public function run(): void
    {
        $shooters = [
`;

data.forEach((item) => {
    const name = item['Nome Completo\\n'] || item['Nome Completo'] || '';
    const nomeDeGuerra = item['Nome de Guerra'] || item['Nome de guerra\\n'] || '';
    const ra = String(item['RA'] || item['RA2'] || '').replace(/\\s/g, '');
    const cpf = String(item['CPF'] || item['CPF\\n'] || '').replace(/[\\.-]/g, '');
    const email = item['E-mail pessoal'] || '';
    const numero = item['NR'] || '';
    const isCfc = String(item[' CFC'] || '').toUpperCase() === 'SIM';
    const telefone = String(item['TELEFONE '] || '');

    if (name && cpf) {
        seederContent += `            [
                'name' => '${name.trim().replace(/'/g, "\\'")}',
                'nome_de_guerra' => '${nomeDeGuerra.trim().replace(/'/g, "\\'")}',
                'ra' => '${ra}',
                'cpf' => '${cpf}',
                'email' => '${email}',
                'numero' => '${numero}',
                'is_cfc' => ${isCfc},
                'turma' => 2026,
                'role' => 'atirador',
                'password' => Hash::make('tg02033${numero}'),
                'telefone' => '${telefone}',
            ],\n`;
    }
});

seederContent += `        ];

        foreach ($shooters as $shooter) {
            User::updateOrCreate(['cpf' => $shooter['cpf']], $shooter);
        }
    }
}
`;

fs.writeFileSync('./database/seeders/ImportShooters2026Seeder.php', seederContent);
console.log('Seeder criado com sucesso!');
