<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\EscalaConfig;
use App\Models\EscalaDiaria;

$config = EscalaConfig::where('nome', 'Aditamento 19/2026')->first();
if (!$config) {
    echo "Config not found\n";
    exit;
}

echo "Config ID: " . $config->id . "\n";
echo "Config Name: " . $config->nome . "\n";

$registros = EscalaDiaria::where('escala_config_id', $config->id)->get();
echo "Registros count: " . $registros->count() . "\n";

foreach ($registros as $reg) {
    echo "ID: " . $reg->id . " | User ID: " . $reg->user_id . " | Data: " . $reg->data->toDateString() . " | Funcao: " . $reg->funcao . "\n";
    $user = \App\Models\User::find($reg->user_id);
    if ($user) {
        echo "  User: " . $user->name . " | Nome de Guerra: " . $user->nome_de_guerra . " | Is CFC: " . ($user->is_cfc ? 'Yes' : 'No') . "\n";
    } else {
        echo "  User NOT FOUND\n";
    }
}
