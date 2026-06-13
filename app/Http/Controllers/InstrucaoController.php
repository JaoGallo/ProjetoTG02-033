<?php

namespace App\Http\Controllers;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;

class InstrucaoController extends Controller
{
    public function index(Request $request)
    {
        $turma = $request->input('turma', '1');
        $ano = $request->input('ano', config('tg.turma_ativa', date('Y')));

        // 1ª Turma: números 1-50, 2ª Turma: números 51-100
        $numInicio = $turma == '1' ? 1 : 51;
        $numFim = $turma == '1' ? 50 : 100;

        $turmaAno = $ano;

        // Buscar atiradores da turma ativa com números no range
        $atiradores = User::whereIn('role', ['atirador', 'monitor'])
            ->where('turma', $turmaAno)
            ->whereBetween('numero', [$numInicio, $numFim])
            ->orderBy('numero')
            ->get()
            ->keyBy('numero');

        return view('frequencia.index', compact('turma', 'numInicio', 'numFim', 'atiradores', 'ano'));
    }

    public function showIndividual(User $user, Request $request)
    {
        // Validar se é atirador/monitor
        if (!in_array($user->role, ['atirador', 'monitor'])) {
            abort(404);
        }

        $ano = $request->input('ano', config('tg.turma_ativa', date('Y')));
        
        // Criar array de meses com informações de dias
        $meses = [];
        $nomesMeses = ['JAN', 'FEV', 'MAR', 'ABR', 'MAI', 'JUN', 'JUL', 'AGO', 'SET', 'OUT', 'NOV', 'DEZ'];
        
        for ($mes = 1; $mes <= 12; $mes++) {
            $primeiroDia = Carbon::create($ano, $mes, 1);
            $ultimoDia = $primeiroDia->copy()->endOfMonth();
            $diasDoMes = [];
            
            for ($dia = 1; $dia <= $ultimoDia->day; $dia++) {
                $data = Carbon::create($ano, $mes, $dia);
                $diasDoMes[] = [
                    'dia' => $dia,
                    'data' => $data->format('Y-m-d'),
                    'diaSemana' => $data->dayName,
                ];
            }
            
            $meses[] = [
                'mes' => $mes,
                'nome' => $nomesMeses[$mes - 1],
                'dias' => $diasDoMes,
                'maxDias' => $ultimoDia->day
            ];
        }

        return view('frequencia.individual', compact('user', 'ano', 'meses'));
    }

    public function salvar(Request $request)
    {
        $data = $request->input('data', []);

        foreach ($data as $userId => $fields) {
            $user = User::find($userId);
            if (! $user) {
                continue;
            }

            // Campos numéricos — normalizar para integers
            $user->servicos_escala = isset($fields['servicos_escala']) ? intval($fields['servicos_escala']) : ($user->servicos_escala ?? 0);
            $user->treinamentos = isset($fields['treinamentos']) ? intval($fields['treinamentos']) : ($user->treinamentos ?? 0);
            $user->marchas_estac_eld = isset($fields['marchas_estac_eld']) ? intval($fields['marchas_estac_eld']) : ($user->marchas_estac_eld ?? 0);
            $user->acoes_comunitarias = isset($fields['acoes_comunitarias']) ? intval($fields['acoes_comunitarias']) : ($user->acoes_comunitarias ?? 0);
            $user->tempo_cfc = isset($fields['tempo_cfc']) ? intval($fields['tempo_cfc']) : ($user->tempo_cfc ?? 0);
            $user->pontos_perdidos = isset($fields['pontos_perdidos']) ? intval($fields['pontos_perdidos']) : ($user->pontos_perdidos ?? 0);

            // Texto
            $user->status = isset($fields['status']) ? substr($fields['status'], 0, 191) : ($user->status ?? null);

            $user->save();
        }

        return redirect()->route('frequencia.index', ['turma' => $request->input('turma', '1'), 'ano' => $request->input('ano')])->with('success', 'Dados salvos com sucesso.');
    }
}
