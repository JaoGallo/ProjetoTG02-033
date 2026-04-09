<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Faker\Factory as Faker;

class ShooterSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Apagar atiradores e monitores existentes
        User::whereIn('role', ['atirador', 'monitor'])->delete();

        $faker = Faker::create('pt_BR');
        $usedWarNames = [];

        // 2. Inserir 100 atiradores com Nomes de Guerra únicos
        for ($i = 1; $i <= 100; $i++) {
            $name = $faker->firstNameMale . ' ' . $faker->lastName . ' ' . $faker->lastName;
            $nomeDeGuerra = '';
            
            // Loop para garantir que encontramos um nome de guerra único para este atirador
            $foundUnique = false;
            while (!$foundUnique) {
                // Tenta as partes do nome gerado
                $nameParts = explode(' ', $name);
                foreach (array_reverse($nameParts) as $part) {
                    $candidate = mb_strtoupper($part, 'UTF-8');

                    if (mb_strlen($candidate) > 2 && !in_array($candidate, $usedWarNames)) {
                        $nomeDeGuerra = $candidate;
                        $usedWarNames[] = $candidate;
                        $foundUnique = true;
                        break;
                    }
                }
                
                // Se não encontrou no nome atual, gera um nome completamente novo e tenta de novo
                if (!$foundUnique) {
                    $name = $faker->firstNameMale . ' ' . $faker->lastName . ' ' . $faker->lastName;
                }
            }
            
            $ra = '25' . str_pad($i, 5, '0', STR_PAD_LEFT);
            $cpf = $faker->unique()->numerify('###########');

            User::create([
                'name' => $name,
                'nome_de_guerra' => $nomeDeGuerra,
                'email' => $faker->unique()->safeEmail,
                'ra' => $ra,
                'cpf' => $cpf,
                'role' => 'atirador',
                'points' => rand(0, 40),
                'faults' => rand(0, 10),
                'numero' => $i,
                'turma' => 2025,
                'is_cfc' => rand(0, 1) == 1,
                'password' => Hash::make('tg02033' . $i),
                'telefone' => $faker->cellphoneNumber,
            ]);
        }
    }
}
