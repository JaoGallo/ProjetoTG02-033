<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\EscalaDiaria;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ContabilizarPontosEscala extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tg:pontos-escala';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Contabiliza 24 pontos na ficha de frequencia para quem tirou guarda ou comando no dia anterior.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $ontem = Carbon::yesterday()->toDateString();
        
        $escalas = EscalaDiaria::where('data', $ontem)
            ->where('pontos_contabilizados', false)
            ->whereNotNull('user_id')
            ->get();

        if ($escalas->isEmpty()) {
            $this->info("Nenhum servico de escala pendente de pontuacao para a data {$ontem}.");
            return;
        }

        DB::beginTransaction();
        try {
            $count = 0;
            foreach ($escalas as $escala) {
                $user = $escala->user;
                if ($user) {
                    $user->servicos_escala = ($user->servicos_escala ?? 0) + 24;
                    $user->save();
                    
                    $escala->pontos_contabilizados = true;
                    $escala->save();
                    
                    $count++;
                }
            }
            DB::commit();
            $this->info("Foram contabilizados 24 pontos para {$count} atiradores referentes ao dia {$ontem}.");
        } catch (\Exception $e) {
            DB::rollBack();
            $this->error("Erro ao contabilizar pontos: " . $e->getMessage());
        }
    }
}
