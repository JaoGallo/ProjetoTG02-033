<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class AuthController extends Controller
{
    public function showLogin()
    {
        return view('login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'user' => ['required', 'string'],
            'password' => ['required'],
        ]);

        $loginField = $credentials['user'];
        $password = $credentials['password'];

        // Tentar logar via RA primeiro, depois via CPF
        // Ambas tentativas executam hash check, evitando timing attacks
        if (Auth::attempt(['ra' => $loginField, 'password' => $password])
            || Auth::attempt(['cpf' => $loginField, 'password' => $password])) {
            $request->session()->regenerate();
            return redirect()->intended('dashboard');
        }

        return back()->withErrors([
            'user' => 'As credenciais informadas não coincidem com nossos registros.',
        ])->onlyInput('user');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/');
    }
}
