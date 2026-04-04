<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class PrimeiroAcessoController extends Controller
{
    public function index()
    {
        // Se já tem email configurado, redireciona pro dashboard
        if (!empty(auth()->user()->email)) {
            return redirect()->route('dashboard');
        }
        return view('primeiro_acesso');
    }

    public function store(Request $request)
    {
        if (!empty(auth()->user()->email)) {
            return redirect()->route('dashboard');
        }

        $request->validate([
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'confirmed', Password::defaults()],
        ]);

        $user = auth()->user();
        $user->email = $request->email;
        $user->password = Hash::make($request->password);
        $user->save();

        return redirect()->route('dashboard')->with('success', 'Conta configurada com sucesso. Bem-vindo!');
    }
}
