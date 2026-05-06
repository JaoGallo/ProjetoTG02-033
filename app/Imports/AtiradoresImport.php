<?php

namespace App\Imports;

use App\Models\User;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithCalculatedFormulas;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Hash;

class AtiradoresImport implements ToCollection, WithCalculatedFormulas
{
    protected $turma;

    public function __construct($turma)
    {
        $this->turma = $turma;
    }

    public function collection(Collection $rows)
    {
        if ($rows->isEmpty()) return;

        $headerRow = $rows->first()->toArray();
        $dataRows = $rows->slice(1);

        // Mapear índices das colunas
        $findColumn = function($aliases) use ($headerRow) {
            foreach ($aliases as $alias) {
                foreach ($headerRow as $index => $header) {
                    $headerClean = trim(strtolower((string)$header));
                    $aliasClean = trim(strtolower((string)$alias));
                    
                    if ($headerClean === $aliasClean || str_contains($headerClean, $aliasClean)) {
                        // Se for RA, evitar a coluna bugada com strings gigantes (baseado no valor da primeira linha de dados se disponível)
                        return $index;
                    }
                }
            }
            return null;
        };

        $idxName = $findColumn(['Nome Completo', 'Colunas1', 'Nome']);
        $idxGuerra = $findColumn([' NOME DE GUERRA', 'NOME DE GUERRA', 'Nome de Guerra', 'Guerra']);
        $idxCPF = $findColumn(['CPF']);
        $idxRA = $findColumn(['RA2', 'RA', 'Registro']);
        $idxNR = $findColumn(['NR', 'Número', 'Nº', 'N']); // Prioriza "NR" exato se possível
        $idxEmail = $findColumn(['E-mail pessoal', 'Email']);
        $idxTelefone = $findColumn(['TELEFONE', 'Telefone', 'Celular']);

        foreach ($dataRows as $row) {
            $row = $row->toArray();
            
            $name = $idxName !== null ? ($row[$idxName] ?? null) : null;
            $cpf = $idxCPF !== null ? preg_replace('/[^0-9]/', '', (string)($row[$idxCPF] ?? '')) : null;
            
            if (!$name || !$cpf) continue;

            $nomeGuerra = $idxGuerra !== null ? ($row[$idxGuerra] ?? null) : null;
            if (!$nomeGuerra) {
                $nomeGuerra = explode(' ', trim((string)$name))[0];
            }

            $numero = $idxNR !== null ? (int)($row[$idxNR] ?? 0) : 0;
            $ra = $idxRA !== null ? str_replace([' ', '-', '.'], '', (string)($row[$idxRA] ?? '')) : '';
            $email = $idxEmail !== null ? ($row[$idxEmail] ?? null) : null;
            $telefone = $idxTelefone !== null ? (string)($row[$idxTelefone] ?? '') : '';
            
            $senha = 'tg02033' . str_pad($numero, 2, '0', STR_PAD_LEFT);

            User::updateOrCreate(
                ['cpf' => $cpf],
                [
                    'name' => trim((string)$name),
                    'nome_de_guerra' => trim(strtoupper((string)$nomeGuerra)),
                    'ra' => $ra,
                    'email' => $email,
                    'numero' => $numero,
                    'is_cfc' => false,
                    'turma' => $this->turma,
                    'role' => 'atirador',
                    'password' => Hash::make($senha),
                    'telefone' => $telefone,
                ]
            );
        }
    }
}
