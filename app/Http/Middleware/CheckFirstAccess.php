<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckFirstAccess
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (auth()->check() && auth()->user()->role === 'atirador' && empty(auth()->user()->email)) {
            // Se estiver tentando acessar rotas que não sejam do fluxo de primeiro acesso ou logout
            if (! $request->routeIs('primeiro-acesso') && ! $request->routeIs('primeiro-acesso.store') && ! $request->routeIs('logout')) {
                return redirect()->route('primeiro-acesso');
            }
        }
        return $next($request);
    }
}
