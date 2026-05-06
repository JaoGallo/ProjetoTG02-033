<?php

namespace App\Http\Controllers;

use App\Models\EscalaConfig;
use App\Models\EscalaDiaria;
use App\Models\Feriado;
use App\Models\User;
use App\Services\EscalaService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Barryvdh\DomPDF\Facade\Pdf;

class EscalaController extends Controller
{
    public function __construct(private EscalaService $escalaService) {}

    // -----------------------------------------------------------------------
    // Painel Geral
    // -----------------------------------------------------------------------
    public function index()
    {
        $adts = EscalaConfig::orderBy('data_inicio', 'desc')->get();
        return view('escalas.index', compact('adts'));
    }

    public function criarAdt(Request $request)
    {
        $turma = (int) $request->get('turma', config('tg.turma_ativa', date('Y')));
        
        // Sugerir a próxima data baseada no último aditamento
        $ultimoAdt = EscalaConfig::orderBy('data_fim', 'desc')->first();
        $proximaDataSugerida = $ultimoAdt ? $ultimoAdt->data_fim->addDay()->toDateString() : date('Y-m-d');

        $dataRef = $request->get('data_inicio', $proximaDataSugerida);

        $monitores = User::where('is_cfc', true)->whereIn('role', ['atirador', 'monitor'])->where('turma', $turma)->orderBy('numero')->get();
        $atiradores = User::where('is_cfc', false)->whereIn('role', ['atirador', 'monitor'])->where('turma', $turma)->orderBy('numero')->get();
        
        $this->attachFolga($monitores, $dataRef);
        $this->attachFolga($atiradores, $dataRef);

        $anosNoBanco = User::whereNotNull('turma')->distinct()->pluck('turma')->toArray();
        $anoAtual = (int)date('Y');
        $anosPadrao = [$anoAtual - 1, $anoAtual];
        $turmasDisponiveis = collect(array_merge($anosNoBanco, $anosPadrao))
            ->unique()
            ->filter(fn($y) => $y <= $anoAtual)
            ->sortDesc();
        
        return view('escalas.criar_adt', compact('monitores', 'atiradores', 'turmasDisponiveis', 'turma', 'dataRef'));
    }

    public function salvarAdt(Request $request)
    {
        $request->validate([
            'nome' => 'required|string|max:100',
            'data_inicio' => 'required|date',
            'dia1_monitores' => 'required|array|min:1',
            'dia1_atiradores' => 'required|array|min:4',
        ]);

        try {
            DB::beginTransaction();

            $dataInicio = Carbon::parse($request->data_inicio);
            $dataFim = $dataInicio->copy()->addDays(6);

            $config = EscalaConfig::create([
                'nome' => $request->nome,
                'grupo' => 'Unified',
                'data_inicio' => $dataInicio,
                'data_fim' => $dataFim,
                'qnt_cmt_dia' => count($request->dia1_monitores),
                'qnt_gd_dia' => count($request->dia1_atiradores),
                'dias_iniciais' => 0,
                'valor_inicial' => 1,
                'status' => 'publicado',
                'part2_instrucao' => $request->part2_instrucao,
                'part3_assuntos_gerais' => $request->part3_assuntos_gerais,
                'part4_justica_disciplina' => $request->part4_justica_disciplina,
            ]);

            $this->escalaService->gerarAditamentoCompleto($config, $request->dia1_monitores, $request->dia1_atiradores);

            DB::commit();
            return redirect()->route('escalas.index')->with('success', 'Aditamento gerado com sucesso!');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Falha ao gerar escala: ' . $e->getMessage()])->withInput();
        }
    }

    // -----------------------------------------------------------------------
    // Edição / Exclusão
    // -----------------------------------------------------------------------
    public function destroy(EscalaConfig $config)
    {
        $isAdmin = auth()->check() && in_array(auth()->user()->role, ['master', 'instructor']);
        if (!$config->data_inicio->isFuture() && !$isAdmin) {
            return back()->withErrors(['error' => 'Apenas aditamentos futuros podem ser excluídos.']);
        }
        $config->delete(); 
        return redirect()->route('escalas.index')->with('success', 'Aditamento excluído com sucesso!');
    }

    public function edit(EscalaConfig $config, Request $request)
    {
        $turma = (int) $request->get('turma', config('tg.turma_ativa', date('Y')));
        $selectedDate = $request->get('dia', $config->data_inicio->toDateString());

        $monitores = User::where('is_cfc', true)->whereIn('role', ['atirador', 'monitor'])->where('turma', $turma)->orderBy('numero')->get();
        $atiradores = User::where('is_cfc', false)->whereIn('role', ['atirador', 'monitor'])->where('turma', $turma)->orderBy('numero')->get();

        $this->attachFolga($monitores, $selectedDate);
        $this->attachFolga($atiradores, $selectedDate);

        $anosNoBanco = User::whereNotNull('turma')->distinct()->pluck('turma')->toArray();
        $anoAtual = (int)date('Y');
        $anosPadrao = [$anoAtual - 1, $anoAtual];
        $turmasDisponiveis = collect(array_merge($anosNoBanco, $anosPadrao))
            ->unique()
            ->filter(fn($y) => $y <= $anoAtual)
            ->sortDesc();

        $registros = EscalaDiaria::where('escala_config_id', $config->id)
            ->get()
            ->groupBy(fn($r) => $r->data->toDateString());
            
        $diasAdt = [];
        $dataAtual = $config->data_inicio->copy();
        while ($dataAtual->lte($config->data_fim)) {
            $dataStr = $dataAtual->toDateString();
            $diasAdt[$dataStr] = [
                'data' => $dataAtual->copy(),
                'monitores_ids' => isset($registros[$dataStr]) ? $registros[$dataStr]->where('funcao', 'comandante')->pluck('user_id')->toArray() : [],
                'atiradores_ids' => isset($registros[$dataStr]) ? $registros[$dataStr]->where('funcao', 'guarda')->pluck('user_id')->toArray() : [],
            ];
            $dataAtual->addDay();
        }

        $selectedDate = $request->get('dia', $config->data_inicio->toDateString());
        if (!array_key_exists($selectedDate, $diasAdt)) {
            $selectedDate = $config->data_inicio->toDateString();
        }

        return view('escalas.editar_adt', compact('config', 'monitores', 'atiradores', 'diasAdt', 'selectedDate', 'turmasDisponiveis', 'turma'));
    }

    public function update(Request $request, EscalaConfig $config)
    {
        $request->validate([
            'data_edit' => 'required|date',
            'dia_monitores' => 'required|array|min:1',
            'dia_atiradores' => 'required|array|min:4',
        ]);

        $dataEdit = Carbon::parse($request->data_edit);
        $isAdmin = auth()->check() && in_array(auth()->user()->role, ['master', 'instructor']);

        if ($dataEdit->isPast() && !$dataEdit->isToday() && !$isAdmin) {
            return back()->withErrors(['error' => 'Não é possível editar dias que já passaram.']);
        }

        try {
            DB::beginTransaction();

            EscalaDiaria::where('escala_config_id', $config->id)
                ->where('data', $dataEdit->toDateString())
                ->delete();

            $batch = [];
            $now = now();
            $dataStr = $dataEdit->toDateString();

            foreach ($request->dia_monitores as $uid) {
                $batch[] = [
                    'escala_config_id' => $config->id,
                    'user_id' => $uid,
                    'data' => $dataStr,
                    'funcao' => 'comandante',
                    'valor' => 'Cmt',
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
            }

            foreach ($request->dia_atiradores as $uid) {
                $batch[] = [
                    'escala_config_id' => $config->id,
                    'user_id' => $uid,
                    'data' => $dataStr,
                    'funcao' => 'guarda',
                    'valor' => 'Gd',
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
            }

            DB::table('escala_diaria')->insert($batch);

            // Salvar as outras partes do Aditamento (2ª, 3ª e 4ª)
            $config->update([
                'part2_instrucao' => $request->part2_instrucao,
                'part3_assuntos_gerais' => $request->part3_assuntos_gerais,
                'part4_justica_disciplina' => $request->part4_justica_disciplina,
            ]);

            DB::commit();
            return redirect()->route('escalas.edit', $config->id)->with('success', 'Dia ' . $dataEdit->format('d/m') . ' atualizado com sucesso!');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Falha ao editar o dia: ' . $e->getMessage()]);
        }
    }

    // -----------------------------------------------------------------------
    // -----------------------------------------------------------------------
    public function configurar(string $grupo)
    {
        abort_unless(in_array($grupo, ['Mon', 'Atdr']), 404);

        $config = EscalaConfig::where('grupo', $grupo)->first();

        $isCfc = $grupo === 'Mon';
        $integrantes = User::where('role', 'atirador')
            ->where('turma', config('tg.turma_ativa'))
            ->where('is_cfc', $isCfc)
            ->orderBy('numero')
            ->get();

        $feriados = Feriado::orderBy('data')->get();

        return view('escalas.configurar', compact('grupo', 'config', 'integrantes', 'feriados'));
    }

    public function salvarConfig(Request $request, string $grupo)
    {
        abort_unless(in_array($grupo, ['Mon', 'Atdr']), 404);

        $request->validate([
            'data_inicio'   => 'required|date',
            'data_fim'      => 'required|date|after:data_inicio',
            'qnt_cmt_dia'   => 'required|integer|min:1',
            'qnt_gd_dia'    => 'required|integer|min:1',
            'dias_iniciais' => 'required|integer|min:0|max:100',
            'valor_inicial' => 'required|integer|min:0',
        ]);

        $config = EscalaConfig::updateOrCreate(
            ['grupo' => $grupo],
            [
                'data_inicio'   => $request->data_inicio,
                'data_fim'      => $request->data_fim,
                'qnt_cmt_dia'   => $request->qnt_cmt_dia,
                'qnt_gd_dia'    => $request->qnt_gd_dia,
                'dias_iniciais' => $request->dias_iniciais,
                'valor_inicial' => $request->valor_inicial,
                'gerada_em'     => null,
            ]
        );

        // Gerar a escala
        $this->escalaService->gerarEscala($grupo, $config->fresh());

        return redirect()
            ->route('escalas.visualizar', $grupo)
            ->with('success', "Escala do grupo {$grupo} gerada com sucesso!");
    }

    // -----------------------------------------------------------------------
    // Visualização — Matriz
    // -----------------------------------------------------------------------
    public function visualizar(string $grupo, Request $request)
    {
        abort_unless(in_array($grupo, ['Mon', 'Atdr']), 404);

        $config = EscalaConfig::where('grupo', $grupo)->firstOrFail();

        $isCfc = $grupo === 'Mon';
        $integrantes = User::where('role', 'atirador')
            ->where('turma', config('tg.turma_ativa'))
            ->where('is_cfc', $isCfc)
            ->orderBy('numero')
            ->get();

        // Filtro de período (30 dias por vez para não sobrecarregar)
        $inicio = Carbon::parse($request->get('inicio', $config->data_inicio));
        $fim    = $inicio->copy()->addDays(29);
        if ($fim->gt($config->data_fim)) {
            $fim = Carbon::parse($config->data_fim);
        }

        $datas = collect();
        for ($d = $inicio->copy(); $d->lte($fim); $d->addDay()) {
            $datas->push($d->copy());
        }

        // Buscar todos os registros do período para estes integrantes
        $userIds = $integrantes->pluck('id');
        $registros = EscalaDiaria::whereIn('user_id', $userIds)
            ->whereBetween('data', [$inicio->toDateString(), $fim->toDateString()])
            ->get()
            ->groupBy(fn($r) => $r->user_id . '_' . $r->data->toDateString());

        // Totais por dia (Qnt = Cmt + Gd por dia)
        $totaisDia = EscalaDiaria::whereIn('user_id', $userIds)
            ->whereBetween('data', [$inicio->toDateString(), $fim->toDateString()])
            ->whereIn('funcao', ['guarda', 'comandante'])
            ->selectRaw('data, COUNT(*) as total')
            ->groupBy('data')
            ->pluck('total', 'data');

        $feriados = Feriado::whereBetween('data', [$inicio->toDateString(), $fim->toDateString()])
            ->pluck('motivo', 'data')
            ->transform(fn($m, $d) => $m ?? 'Feriado')
            ->toArray();

        $prevInicio = $inicio->copy()->subDays(30);
        $nextInicio = $inicio->copy()->addDays(30);
        $canPrev    = $prevInicio->gte($config->data_inicio);
        $canNext    = $nextInicio->lte($config->data_fim);

        return view('escalas.visualizar', compact(
            'grupo', 'config', 'integrantes', 'datas',
            'registros', 'totaisDia', 'feriados',
            'inicio', 'fim', 'prevInicio', 'nextInicio', 'canPrev', 'canNext'
        ));
    }

    // -----------------------------------------------------------------------
    // Boletim do Dia
    // -----------------------------------------------------------------------
    public function boletim(string $data)
    {
        $carbon = Carbon::parse($data);
        $dados  = $this->escalaService->getBoletimDia($data);
        return view('escalas.boletim', compact('dados', 'carbon'));
    }

    public function exportarPdf(string $data)
    {
        $carbon = Carbon::parse($data);
        $dados  = $this->escalaService->getBoletimDia($data);

        $pdf = Pdf::loadView('escalas.boletim_pdf', compact('dados', 'carbon'))
                  ->setPaper('a4', 'portrait');

        $filename = 'boletim_' . $carbon->format('d-m-Y') . '.pdf';
        return $pdf->download($filename);
    }

    // -----------------------------------------------------------------------
    // Feriados
    // -----------------------------------------------------------------------
    public function feriados()
    {
        $feriados = Feriado::orderBy('data')->get();
        return view('escalas.feriados', compact('feriados'));
    }

    public function salvarFeriado(Request $request)
    {
        $request->validate([
            'data'   => 'required|date|unique:feriados,data',
            'motivo' => 'nullable|string|max:200',
        ]);

        Feriado::create($request->only('data', 'motivo'));

        return redirect()->back()->with('success', 'Feriado adicionado.');
    }

    public function deletarFeriado(Feriado $feriado)
    {
        $feriado->delete();
        return redirect()->back()->with('success', 'Feriado removido.');
    }

    // -----------------------------------------------------------------------
    // Troca Manual
    // -----------------------------------------------------------------------
    public function swap(Request $request)
    {
        $isAdmin = auth()->check() && in_array(auth()->user()->role, ['master', 'instructor']);
        
        $rules = [
            'data'                    => 'required|date',
            'integrante_origem_id'    => 'required|exists:users,id',
            'integrante_destino_id'   => 'required|exists:users,id|different:integrante_origem_id',
            'motivo'                  => 'nullable|string|max:500',
        ];

        if (!$isAdmin) {
            $rules['data'] .= '|after_or_equal:today';
        }

        $request->validate($rules);

        $data    = $request->data;
        $idA     = $request->integrante_origem_id;
        $idB     = $request->integrante_destino_id;

        try {
            DB::beginTransaction();

            $regA = EscalaDiaria::where('user_id', $idA)->where('data', $data)->firstOrFail();
            $regB = EscalaDiaria::where('user_id', $idB)->where('data', $data)->firstOrFail();

            // Verifica se pertencem ao mesmo ADT
            if ($regA->escala_config_id !== $regB->escala_config_id) {
                DB::rollBack();
                return back()->withErrors(['error' => 'Os integrantes pertencem a aditamentos diferentes.']);
            }

            // Troca os valores
            [$regA->valor, $regB->valor] = [$regB->valor, $regA->valor];
            [$regA->funcao, $regB->funcao] = [$regB->funcao, $regA->funcao];

            $regA->save();
            $regB->save();

            // Registra auditoria
            \App\Models\Troca::create([
                'data'                  => $data,
                'integrante_origem_id'  => $idA,
                'integrante_destino_id' => $idB,
                'motivo'                => $request->motivo,
                'criado_por'            => auth()->id(),
            ]);

            DB::commit();
            return redirect()->back()->with('success', 'Troca de serviço registrada com sucesso!');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Falha ao realizar troca: ' . $e->getMessage()]);
        }
    }

    private function attachFolga($users, $referenceDate = null)
    {
        if ($users->isEmpty()) return $users;

        $ref = $referenceDate ? Carbon::parse($referenceDate) : now();
        $userIds = $users->pluck('id');
        
        $historico = DB::table('escala_diaria')
            ->select('user_id', DB::raw('MAX(data) as last_date'))
            ->whereIn('user_id', $userIds)
            ->groupBy('user_id')
            ->pluck('last_date', 'user_id')
            ->toArray();

        foreach ($users as $user) {
            if (isset($historico[$user->id])) {
                $lastDate = Carbon::parse($historico[$user->id]);
                
                if ($lastDate->gt($ref)) {
                    $user->folga_dias = -1; // Escalado no futuro em relação à referência
                    $user->ultima_escala = $lastDate->format('d/m/Y');
                } else {
                    $user->folga_dias = (int) $lastDate->diffInDays($ref);
                    $user->ultima_escala = $lastDate->format('d/m/Y');
                }
            } else {
                $user->folga_dias = 999; 
                $user->ultima_escala = 'Nunca';
            }
        }
        return $users;
    }
}
