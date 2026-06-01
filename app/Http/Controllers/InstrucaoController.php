<?php

namespace App\Http\Controllers;

use App\Models\User;
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
