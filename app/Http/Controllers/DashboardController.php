<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    public function index(Request $request): Response
    {
        $userId = $request->session()->get('supabase_user.id');

        return Inertia::render('Dashboard', [
            'stats' => [
                'pengumuman' => DB::table('pengumuman')->count(),
                'materi' => DB::table('materi')->count(),
                'events' => DB::table('events')->count(),
            ],
            'profile' => DB::table('profiles')->where('id', $userId)->first(),
            'pengumuman' => DB::table('pengumuman')->latest('created_at')->limit(5)->get(),
            'materi' => DB::table('materi')->latest('created_at')->limit(5)->get(),
            'events' => DB::table('events')->latest('created_at')->limit(5)->get(),
        ]);
    }
}
