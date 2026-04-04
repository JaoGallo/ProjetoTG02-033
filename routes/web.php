<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PhotoController;

Route::get('/', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::middleware(['auth', 'first_access'])->group(function () {
    Route::get('/primeiro-acesso', [\App\Http\Controllers\PrimeiroAcessoController::class, 'index'])->name('primeiro-acesso');
    Route::post('/primeiro-acesso', [\App\Http\Controllers\PrimeiroAcessoController::class, 'store'])->name('primeiro-acesso.store');

    Route::get('/dashboard', function () {
        $announcements = \App\Models\Announcement::where('turma', date('Y'))
                                                ->orderBy('priority', 'desc')
                                                ->orderBy('created_at', 'desc')
                                                ->get();
        return view('dashboard', compact('announcements'));
    })->name('dashboard');

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

        // Gestão de Avisos (CRUD completo para instrutores)
        Route::resource('avisos', \App\Http\Controllers\AnnouncementController::class)->except(['show']);
    });

    // Rota de visualização de aviso (acessível para todos autenticados)
    Route::get('/avisos/{aviso}', [\App\Http\Controllers\AnnouncementController::class, 'show'])->name('avisos.show');
});

