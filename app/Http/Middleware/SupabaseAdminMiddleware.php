<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;

class SupabaseAdminMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        $userId = $request->session()->get('supabase_user.id');

        if (!$userId) {
            return redirect()
                ->route('login')
                ->withErrors(['email' => 'Silakan login terlebih dahulu.']);
        }

        $role = DB::table('profiles')
            ->where('id', $userId)
            ->value('role');

        if ($role !== 'admin') {
            abort(403, 'Anda tidak memiliki akses ke halaman admin.');
        }

        return $next($request);
    }
}
