<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;

class RedirectIfSupabaseAuthenticated
{
    public function handle(Request $request, Closure $next): Response
    {
        $userId = $request->session()->get('supabase_user.id');

        if (!$userId) {
            return $next($request);
        }

        $role = DB::table('profiles')
            ->where('id', $userId)
            ->value('role');

        return $role === 'admin'
            ? redirect()->route('admin.dashboard')
            : redirect()->route('dashboard');
    }
}
