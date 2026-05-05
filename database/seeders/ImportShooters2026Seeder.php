<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class ImportShooters2026Seeder extends Seeder
{
    public function run(): void
    {
        $shooters = [
        ];

        foreach ($shooters as $shooter) {
            User::updateOrCreate(['cpf' => $shooter['cpf']], $shooter);
        }
    }
}
