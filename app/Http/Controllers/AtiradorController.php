<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use App\Imports\AtiradoresImport;
use Maatwebsite\Excel\Facades\Excel;

class AtiradorController extends Controller
{
    public function index(Request $request)
    {
        $turma = $request->input('turma', config('tg.turma_ativa'));
        $atiradores = User::whereIn('role', ['atirador', 'monitor'])
                          ->where('turma', $turma)
                          ->orderBy('numero')
                          ->get();

        return view('atiradores.index', compact('atiradores', 'turma'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nome' => 'required|string|max:255',
            'nome_de_guerra' => 'required|string|max:255',
            'numero' => 'required|integer',
            'ra' => 'required|numeric|digits_between:1,12|unique:users,ra',
            'cpf' => 'required|string|size:11|unique:users,cpf',
            'turma' => 'nullable|integer',
            'telefone' => 'nullable|string|max:20',
        ]);

        $turma = $request->turma ?: config('tg.turma_ativa');
        
        // Senha padrão conforme a regra
        $senhaPadrao = 'Atirador' . $request->numero . str_replace(' ', '', $request->nome_de_guerra);

        User::create([
            'name' => $request->nome,
            'nome_de_guerra' => $request->nome_de_guerra,
            'numero' => $request->numero,
            'ra' => $request->ra,
            'cpf' => $request->cpf,
            'telefone' => $request->telefone,
            'turma' => $turma,
            'role' => 'atirador',
            'is_cfc' => false,
            'password' => bcrypt($senhaPadrao),
        ]);

        return redirect()->back()->with('success', 'Atirador adicionado com sucesso!');
    }

    public function update(Request $request, User $user)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'nome_de_guerra' => 'required|string|max:255',
            'numero' => 'required|integer',
            'ra' => ['required', 'numeric', 'digits_between:1,12', Rule::unique('users')->ignore($user->id)],
            'cpf' => ['required', 'string', 'size:11', Rule::unique('users')->ignore($user->id)],
            'turma' => 'required|integer',
            'telefone' => 'nullable|string|max:20',
            'password' => 'nullable|string|min:8|confirmed',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $data = $request->only(['name', 'nome_de_guerra', 'numero', 'ra', 'cpf', 'turma', 'telefone']);
        $data['is_cfc'] = $request->has('is_cfc');

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        if ($request->hasFile('photo')) {
            // Remover foto antiga
            if ($user->photo) {
                Storage::disk('public')->delete($user->photo);
            }
            $filename = 'profile_' . $user->id . '_' . time() . '.' . $request->file('photo')->getClientOriginalExtension();
            $data['photo'] = $request->file('photo')->storeAs('profiles', $filename, 'public');
        }

        $user->update($data);

        return redirect()->back()->with('success', 'Dados do atirador atualizados com sucesso!');
    }

    public function toggleCfc(User $user)
    {
        $user->is_cfc = !$user->is_cfc;
        $user->save();

        return redirect()->back()->with('success', 'Status de CFC atualizado.');
    }

    public function import(Request $request)
    {
        $request->validate([
            'excel_file' => 'required|mimes:xlsx,xls,csv|max:5120',
            'turma' => 'required|integer',
        ]);

        try {
            Excel::import(new AtiradoresImport($request->turma), $request->file('excel_file'));
            return redirect()->back()->with('success', 'Importação concluída com sucesso!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Erro ao importar: ' . $e->getMessage());
        }
    }

    public function destroy(User $user)
    {
        $user->delete();
        return redirect()->back()->with('success', 'Atirador removido.');
    }
}
