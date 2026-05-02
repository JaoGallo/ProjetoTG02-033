<?php

namespace App\Services;

use App\Models\EscalaConfig;
use App\Models\EscalaDiaria;
use App\Models\FilaEstado;
use App\Models\Feriado;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class EscalaService
{
    /**
     * Gera um aditamento completo de 7 dias começando por uma escolha manual no Dia 1.
     */
    public function gerarAditamentoCompleto(\App\Models\EscalaConfig $config, array $dia1MonIds, array $dia1AtdrIds): void
    {
        $dataInicio = $config->data_inicio;
        $dataFim = $config->data_fim;
        $now = now();

        // 1. Limpar registros existentes para este período
        DB::table('escala_diaria')->where('escala_config_id', $config->id)->delete();

        // 2. Carregar tropa (Monitores e Atiradores)
        $turma = config('tg.turma_ativa');
        $todosMonitores = User::where('is_cfc', true)->where('role', 'atirador')->where('turma', $turma)->orderBy('numero')->get();
        $todosAtiradores = User::where('is_cfc', false)->where('role', 'atirador')->where('turma', $turma)->orderBy('numero')->get();

        // 3. Registrar o DIA 1 (Manual)
        $this->registrarDiaManual($config->id, $dataInicio, $dia1MonIds, $dia1AtdrIds);

        // 4. Buscar histórico de serviço para calcular as Folgas
        $historico = DB::table('escala_diaria')
            ->select('user_id', DB::raw('MAX(data) as last_date'))
            ->where('data', '<', $dataInicio->toDateString())
            ->groupBy('user_id')
            ->pluck('last_date', 'user_id')
            ->toArray();

        $lastService = [];
        foreach ($todosMonitores as $m) $lastService[$m->id] = $historico[$m->id] ?? '1900-01-01';
        foreach ($todosAtiradores as $a) $lastService[$a->id] = $historico[$a->id] ?? '1900-01-01';

        // Atualiza a data de quem já pegou serviço no Dia 1
        foreach ($dia1MonIds as $uid) $lastService[$uid] = $dataInicio->toDateString();
        foreach ($dia1AtdrIds as $uid) $lastService[$uid] = $dataInicio->toDateString();

        // 5. Gerar Dias 2 a 7 (Automático)
        $dataAtual = $dataInicio->copy()->addDay();
        while ($dataAtual->lte($dataFim)) {
            // Ordena os monitores (quem serviu há mais tempo fica no topo, critério de desempate é o número)
            $sortedMon = $todosMonitores->sort(function ($a, $b) use ($lastService) {
                $dateA = $lastService[$a->id];
                $dateB = $lastService[$b->id];
                if ($dateA === $dateB) return $a->numero <=> $b->numero;
                return $dateA <=> $dateB;
            })->values();

            // Ordena os atiradores pelo mesmo critério de Folgas
            $sortedAtdr = $todosAtiradores->sort(function ($a, $b) use ($lastService) {
                $dateA = $lastService[$a->id];
                $dateB = $lastService[$b->id];
                if ($dateA === $dateB) return $a->numero <=> $b->numero;
                return $dateA <=> $dateB;
            })->values();

            // Pegar os próximos da fila
            $monHojeIds = $sortedMon->take(count($dia1MonIds))->pluck('id')->toArray();
            $atdrHojeIds = $sortedAtdr->take(count($dia1AtdrIds))->pluck('id')->toArray();

            $this->registrarDiaManual($config->id, $dataAtual, $monHojeIds, $atdrHojeIds);

            // Rotacionar: atualizar a data de serviço de quem serviu hoje
            foreach ($monHojeIds as $uid) $lastService[$uid] = $dataAtual->toDateString();
            foreach ($atdrHojeIds as $uid) $lastService[$uid] = $dataAtual->toDateString();

            $dataAtual->addDay();
        }

        $config->update(['gerada_em' => now()]);
    }

    private function registrarDiaManual($configId, $data, $monIds, $atdrIds)
    {
        $batch = [];
        $now = now();
        $dataStr = $data->toDateString();

        foreach ($monIds as $uid) {
            $batch[] = [
                'escala_config_id' => $configId,
                'user_id' => $uid,
                'data' => $dataStr,
                'funcao' => 'comandante',
                'valor' => 'Cmt',
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }

        foreach ($atdrIds as $uid) {
            $batch[] = [
                'escala_config_id' => $configId,
                'user_id' => $uid,
                'data' => $dataStr,
                'funcao' => 'guarda',
                'valor' => 'Gd',
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }

        DB::table('escala_diaria')->insert($batch);
    }



    /**
     * Retorna os dados do boletim para uma data específica.
     */
    public function getBoletimDia(string $data): array
    {
        $registros = EscalaDiaria::with('user')
            ->where('data', $data)
            ->whereIn('funcao', ['comandante', 'guarda'])
            ->get()
            ->sortBy(fn($r) => [$r->funcao === 'guarda' ? 1 : 0, $r->user->numero]);

        $mon  = $registros->filter(fn($r) => $r->user->is_cfc)->values();
        $atdr = $registros->filter(fn($r) => !$r->user->is_cfc)->values();

        return [
            'data'   => Carbon::parse($data),
            'mon'    => $mon,
            'atdr'   => $atdr,
        ];
    }
}
