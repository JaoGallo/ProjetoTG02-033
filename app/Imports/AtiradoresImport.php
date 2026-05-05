<?php

namespace App\Imports;

use App\Models\User;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Hash;

class AtiradoresImport implements ToCollection, WithHeadingRow
{
    protected $turma;

    public function __construct($turma)
    {
        $this->turma = $turma;
    }

    public function collection(Collection $rows)
    {
        foreach ($rows as $row) {
            // Função para pegar valor ignorando \n ou espaços
            $getValue = function($keys) use ($row) {
                foreach ($row as $key => $value) {
                    foreach ($keys as $k) {
                        if (str_contains(strtolower($key), strtolower($k))) {
                            return $value;
                        }
                    }
                }
                return null;
            };

            $name = $getValue(['Nome Completo']);
            $nomeDeGuerra = $getValue(['Nome de Guerra']);
            $ra = str_replace(' ', '', (string) $getValue(['RA']));
            $cpf = preg_replace('/[^0-9]/', '', (string) $getValue(['CPF']));
            $email = $getValue(['E-mail pessoal']);
            $numero = $getValue(['NR']);
            $isCfc = str_contains(strtoupper((string) $getValue([' CFC'])), 'SIM');
            $telefone = (string) $getValue(['TELEFONE']);

            if ($name && $cpf) {
                User::updateOrCreate(
                    ['cpf' => $cpf],
                    [
                        'name' => trim($name),
                        'nome_de_guerra' => trim(strtoupper($nomeDeGuerra)),
                        'ra' => $ra,
                        'email' => $email,
                        'numero' => $numero,
                        'is_cfc' => $isCfc,
                        'turma' => $this->turma,
                        'role' => 'atirador',
                        'password' => Hash::make('tg02033' . $numero),
                        'telefone' => $telefone,
                    ]
                );
            }
        }
    }
}
