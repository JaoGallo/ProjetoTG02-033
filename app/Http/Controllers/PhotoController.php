<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;

class PhotoController extends Controller
{
    /**
     * Atualiza a foto do perfil do usuário.
     * 
     * Regras:
     * - Master: Altera de todos.
     * - Instrutor: Altera a própria e a de Atiradores. Não altera de outros Instrutores.
     * - Atirador: Não altera nenhuma (nem a própria).
     */
    public function update(Request $request, User $user)
    {
        /** @var \App\Models\User $currentUser */
        $currentUser = Auth::user();

        // Verificação de permissões
        if ($currentUser->role === 'atirador' || $currentUser->role === 'monitor') {
            abort(403, 'Você não tem permissão para alterar fotos.');
        }

        if ($currentUser->role === 'instructor') {
            // Se não for ele mesmo E não for um atirador, bloqueia
            if ($user->id !== $currentUser->id && $user->role !== 'atirador') {
                abort(403, 'Instrutores só podem alterar as próprias fotos ou as de atiradores.');
            }
        }

        $request->validate([
            'photo' => 'required|image|mimes:jpeg,png,jpg|max:2048',
        ], [
            'photo.required' => 'Selecione uma imagem primeiro.',
            'photo.image' => 'O arquivo deve ser uma imagem.',
            'photo.max' => 'A imagem não pode ter mais de 2MB.'
        ]);

        if ($request->hasFile('photo')) {
            // Remover foto antiga se existir
            if ($user->photo) {
                Storage::disk('public')->delete($user->photo);
            }

            // Salvar nova foto
            $filename = 'profile_' . $user->id . '_' . time() . '.' . $request->file('photo')->getClientOriginalExtension();
            $path = $request->file('photo')->storeAs('profiles', $filename, 'public');
            
            $user->update(['photo' => $path]);
        }

        return back()->with('success', 'Foto 3x4 atualizada com sucesso!');
    }
}
