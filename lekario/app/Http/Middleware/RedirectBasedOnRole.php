<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RedirectBasedOnRole
{
    public function handle(Request $request, Closure $next): Response
    {
        if (auth()->check()) {
            if (auth()->user()->doctor) {
                return redirect()->route('doctor.dashboard');
            }
            return redirect()->route('dashboard');
        }

        return $next($request);
    }
}