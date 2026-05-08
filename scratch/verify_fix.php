<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\EscalaConfig;
use App\Models\EscalaDiaria;
use Carbon\Carbon;

$config = EscalaConfig::where('nome', 'Aditamento 19/2026')->first();
if (!$config) {
    echo "Config not found\n";
    exit;
}

$registros = EscalaDiaria::with('user')
    ->where('escala_config_id', $config->id)
    ->whereIn('funcao', ['comandante', 'guarda'])
    ->get();

$registrosGrupados = $registros->groupBy(fn($r) => $r->data->toDateString());

$dias = [];
$dataAtual = $config->data_inicio->copy();
while ($dataAtual->lte($config->data_fim)) {
    $dataStr = $dataAtual->toDateString();
    $regsDia = $registrosGrupados->get($dataStr, collect());
    
    $mon = $regsDia->filter(fn($r) => $r->user->is_cfc)->sortBy('user.numero')->values();
    $atdr = $regsDia->filter(fn($r) => !$r->user->is_cfc)->sortBy('user.numero')->values();
    
    echo "Dia: $dataStr | Mon: " . $mon->count() . " | Atdr: " . $atdr->count() . "\n";
    foreach($mon as $m) echo "  [MON] " . ($m->user->nome_de_guerra ?? $m->user->name) . "\n";
    foreach($atdr as $a) echo "  [ATDR] " . ($a->user->nome_de_guerra ?? $a->user->name) . "\n";
    
    $dataAtual->addDay();
}
