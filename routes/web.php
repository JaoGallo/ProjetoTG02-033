<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PhotoController;
use App\Http\Controllers\EscalaController;

Route::get('/', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:5,1');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::middleware(['auth', 'first_access'])->group(function () {
    Route::get('/primeiro-acesso', [\App\Http\Controllers\PrimeiroAcessoController::class, 'index'])->name('primeiro-acesso');
    Route::post('/primeiro-acesso', [\App\Http\Controllers\PrimeiroAcessoController::class, 'store'])->name('primeiro-acesso.store');

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::get('/escalas', [EscalaController::class, 'index'])->name('escalas.index');

    Route::get('/perfil', [ProfileController::class, 'index'])->name('profile');
    Route::put('/perfil', [ProfileController::class, 'update'])->name('profile.update');
    
    // Foto de Perfil (Rota para o próprio ou para o instrutor gerenciar outros)
    Route::post('/usuarios/{user}/photo', [PhotoController::class, 'update'])->name('profile.photo');
    
    // Rotas exclusivas para Instrutores e Master
    Route::middleware('instructor')->group(function () {
        Route::get('/atiradores', [\App\Http\Controllers\AtiradorController::class, 'index'])->name('atiradores.index');
        Route::post('/atiradores', [\App\Http\Controllers\AtiradorController::class, 'store'])->name('atiradores.store');
        Route::put('/atiradores/{user}', [\App\Http\Controllers\AtiradorController::class, 'update'])->name('atiradores.update');
        Route::patch('/atiradores/{user}/toggle-cfc', [\App\Http\Controllers\AtiradorController::class, 'toggleCfc'])->name('atiradores.toggle-cfc');
        Route::delete('/atiradores/{user}', [\App\Http\Controllers\AtiradorController::class, 'destroy'])->name('atiradores.destroy');
        Route::post('/atiradores/import', [\App\Http\Controllers\AtiradorController::class, 'import'])->name('atiradores.import');

        // Gestão de Avisos
        Route::resource('avisos', \App\Http\Controllers\AnnouncementController::class)->except(['show', 'edit', 'update']);

        // Sistema de Escalas (QTS) — Novo Fluxo de ADTs
        Route::get('/escalas/criar', [EscalaController::class, 'criarAdt'])->name('escalas.criar');
        Route::post('/escalas/criar', [EscalaController::class, 'salvarAdt'])->name('escalas.store');

        Route::get('/escalas/{config}/editar', [EscalaController::class, 'edit'])->name('escalas.edit');
        Route::put('/escalas/{config}', [EscalaController::class, 'update'])->name('escalas.update');
        Route::delete('/escalas/{config}', [EscalaController::class, 'destroy'])->name('escalas.destroy');

        Route::get('/escalas/{grupo}/configurar', [EscalaController::class, 'configurar'])->name('escalas.configurar');
        Route::post('/escalas/{grupo}/configurar', [EscalaController::class, 'salvarConfig'])->name('escalas.salvarConfig');
        Route::get('/escalas/{grupo}/visualizar', [EscalaController::class, 'visualizar'])->name('escalas.visualizar');
        Route::get('/escalas/feriados', [EscalaController::class, 'feriados'])->name('escalas.feriados');
        Route::post('/escalas/feriados', [EscalaController::class, 'salvarFeriado'])->name('escalas.salvarFeriado');
        Route::delete('/escalas/feriados/{feriado}', [EscalaController::class, 'deletarFeriado'])->name('escalas.deletarFeriado');
        Route::post('/escalas/swap', [EscalaController::class, 'swap'])->name('escalas.swap');
    });

    // Boletim — acessível a todos autenticados
    Route::get('/escalas/boletim/{data}', [EscalaController::class, 'boletim'])->name('escalas.boletim');
    Route::get('/escalas/boletim/{data}/pdf', [EscalaController::class, 'exportarPdf'])->name('escalas.pdf');

    // Aviso — visualização para todos autenticados
    Route::get('/avisos/{aviso}', [\App\Http\Controllers\AnnouncementController::class, 'show'])->name('avisos.show');
});
