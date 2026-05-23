<?php

namespace App\Http\Controllers;

use Illuminate\Database\Query\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    public function index(Request $request): Response|RedirectResponse
    {
        $userId = $request->session()->get('supabase_user.id');

        $profile = DB::table('profiles as p')
            ->leftJoin('jurusan as j', 'j.id', '=', 'p.jurusan_id')
            ->where('p.id', $userId)
            ->select('p.*', DB::raw('coalesce(j.nama, p.jurusan) as jurusan_nama'))
            ->first();

        if ($profile && $profile->role === 'admin') {
            return redirect()->route('admin.dashboard');
        }

        $jurusanId = $profile?->jurusan_id;

        $pengumumanQuery = $this->targetedQuery(
            DB::table('pengumuman as p')
                ->leftJoin('jurusan as j', 'j.id', '=', 'p.jurusan_id')
                ->select('p.*', 'j.nama as target_jurusan'),
            $jurusanId
        );

        $materiQuery = $this->targetedQuery(
            DB::table('materi as m')
                ->leftJoin('jurusan as j', 'j.id', '=', 'm.jurusan_id')
                ->select('m.*', 'j.nama as target_jurusan'),
            $jurusanId
        );

        $eventsQuery = $this->targetedQuery(
            DB::table('events as e')
                ->leftJoin('jurusan as j', 'j.id', '=', 'e.jurusan_id')
                ->select('e.*', 'j.nama as target_jurusan'),
            $jurusanId
        )->whereDate('e.tanggal', '>=', now()->toDateString());

        return Inertia::render('Dashboard', [
            'stats' => [
                'pengumuman' => (clone $pengumumanQuery)->count(),
                'materi' => (clone $materiQuery)->count(),
                'events' => (clone $eventsQuery)->count(),
            ],
            'profile' => $profile,
            'pengumuman' => (clone $pengumumanQuery)
                ->orderByDesc('p.created_at')
                ->limit(5)
                ->get(),
            'materi' => (clone $materiQuery)
                ->orderByDesc('m.created_at')
                ->limit(5)
                ->get(),
            'events' => (clone $eventsQuery)
                ->orderBy('e.tanggal')
                ->limit(5)
                ->get(),
        ]);
    }

    public function admin(): Response
    {
        return Inertia::render('Admin/Dashboard', [
            'stats' => [
                'mahasiswa' => DB::table('profiles')->where('role', 'mahasiswa')->count(),
                'jurusan' => DB::table('jurusan')->where('aktif', true)->count(),
                'pengumuman' => DB::table('pengumuman')->count(),
                'materi' => DB::table('materi')->count(),
                'events' => DB::table('events')->count(),
            ],
            'jurusan' => DB::table('jurusan')->orderByDesc('aktif')->orderBy('nama')->get(),
            'mahasiswaTerbaru' => DB::table('profiles as p')
                ->leftJoin('jurusan as j', 'j.id', '=', 'p.jurusan_id')
                ->where('p.role', 'mahasiswa')
                ->select('p.*', DB::raw('coalesce(j.nama, p.jurusan) as jurusan_nama'))
                ->orderByDesc('p.created_at')
                ->limit(5)
                ->get(),
            'pengumumanTerbaru' => DB::table('pengumuman as p')
                ->leftJoin('jurusan as j', 'j.id', '=', 'p.jurusan_id')
                ->select('p.*', 'j.nama as target_jurusan')
                ->orderByDesc('p.created_at')
                ->limit(5)
                ->get(),
            'materiTerbaru' => DB::table('materi as m')
                ->leftJoin('jurusan as j', 'j.id', '=', 'm.jurusan_id')
                ->select('m.*', 'j.nama as target_jurusan')
                ->orderByDesc('m.created_at')
                ->limit(5)
                ->get(),
            'eventsTerbaru' => DB::table('events as e')
                ->leftJoin('jurusan as j', 'j.id', '=', 'e.jurusan_id')
                ->select('e.*', 'j.nama as target_jurusan')
                ->whereDate('e.tanggal', '>=', now()->toDateString())
                ->orderBy('e.tanggal')
                ->limit(5)
                ->get(),
        ]);
    }

    private function targetedQuery(Builder $query, ?string $jurusanId): Builder
    {
        return $query->where(function (Builder $builder) use ($jurusanId): void {
            $builder->whereNull('jurusan_id');

            if ($jurusanId) {
                $builder->orWhere('jurusan_id', $jurusanId);
            }
        });
    }
}
