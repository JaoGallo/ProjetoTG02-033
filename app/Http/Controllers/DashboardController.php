<?php

namespace App\Http\Controllers;

use App\Models\Announcement;
use App\Models\EscalaDiaria;

class DashboardController extends Controller
{
    public function index()
    {
        $turma = config('tg.turma_ativa');

        $announcements = Announcement::where('turma', $turma)
            ->with('author')
            ->orderBy('priority', 'desc')
            ->orderBy('created_at', 'desc')
            ->take(20)
            ->get();

        // Serviço do dia (Hoje)
        $todayService = EscalaDiaria::with('user')
            ->where('data', today()->toDateString())
            ->whereIn('funcao', ['comandante', 'guarda'])
            ->get();

        // Escalas (Geral: passadas e futuras)
        $nextScales = EscalaDiaria::with('user')
            ->where('data', '>=', now()->subDays(15)->toDateString())
            ->whereIn('funcao', ['comandante', 'guarda'])
            ->orderBy('data', 'asc')
            ->orderBy('funcao', 'desc')
            ->get()
            ->groupBy(fn($item) => $item->data->toDateString());

        return view('dashboard', compact('announcements', 'nextScales', 'todayService'));
    }
}
