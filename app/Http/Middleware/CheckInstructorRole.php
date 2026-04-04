<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckInstructorRole
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (auth()->check() && !in_array(auth()->user()->role, ['master', 'instructor'])) {
            return redirect()->route('dashboard')->with('error', 'Acesso negado. Ação restrita a instrutores.');
        }
        return $next($request);
    }
}
