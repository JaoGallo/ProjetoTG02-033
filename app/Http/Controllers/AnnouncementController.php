<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Announcement;
use App\Models\User;
use App\Mail\AnnouncementMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;

class AnnouncementController extends Controller
{
    public function index(Request $request)
    {
        // Apenas instrutores acessam o index (gestão)
        $announcements = Announcement::with(['author', 'readers'])
                                    ->orderBy('created_at', 'desc')
                                    ->get();
        
        return view('announcements.index', compact('announcements'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'category' => 'required|in:geral,urgente,escala,instrucao',
            'priority' => 'boolean',
            'attachment' => 'nullable|file|max:5120', // Max 5MB
        ]);

        $turma = date('Y'); // Turma ativa por padrão

        $attachmentPath = null;
        if ($request->hasFile('attachment')) {
            $attachmentPath = $request->file('attachment')->store('announcements', 'public');
        }

        $announcement = Announcement::create([
            'title' => $request->title,
            'content' => $request->content,
            'category' => $request->category,
            'priority' => $request->has('priority'),
            'attachment' => $attachmentPath,
            'turma' => $turma,
            'user_id' => Auth::id(),
        ]);

        // Enviar e-mails para a turma ativa
        $recipients = User::where('turma', $turma)
                          ->whereIn('role', ['atirador', 'monitor'])
                          ->whereNotNull('email')
                          ->get();

        foreach ($recipients as $recipient) {
            Mail::to($recipient->email)->queue(new AnnouncementMail($announcement, $recipient));
        }

        return redirect()->route('dashboard')->with('success', 'Aviso publicado e enviado por e-mail!');
    }

    public function show(Announcement $aviso)
    {
        // Marcar como lido para o usuário atual (se for atirador ou monitor)
        $user = Auth::user();
        if (in_array($user->role, ['atirador', 'monitor'])) {
             if (!$aviso->readers()->where('user_id', $user->id)->exists()) {
                $aviso->readers()->attach($user->id, ['viewed_at' => now()]);
             }
        }

        return view('announcements.show', ['announcement' => $aviso]);
    }

    public function destroy(Announcement $aviso)
    {
        if ($aviso->attachment) {
            Storage::disk('public')->delete($aviso->attachment);
        }
        
        $aviso->delete();
        return redirect()->back()->with('success', 'Aviso removido.');
    }
}
