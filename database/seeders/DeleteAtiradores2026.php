<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;

class DeleteAtiradores2026 extends Seeder
{
    public function run(): void
    {
        $count = User::where('role', 'atirador')->where('turma', 2026)->delete();
        echo "Sucesso: $count atiradores de 2026 foram removidos.\n";
    }
}
