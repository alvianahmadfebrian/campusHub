<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SupabaseAuthMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! $request->session()->has('supabase_user')) {
            return redirect()->route('login')->withErrors([
                'email' => 'Silakan login dulu.',
            ]);
        }

        return $next($request);
    }
}
