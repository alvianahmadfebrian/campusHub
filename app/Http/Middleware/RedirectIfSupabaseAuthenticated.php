<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RedirectIfSupabaseAuthenticated
{
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->session()->has('supabase_user')) {
            return redirect()->route('dashboard');
        }

        return $next($request);
    }
}
