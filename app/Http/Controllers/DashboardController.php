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

public function admin(Request $request): Response
{
    $userId = $request->session()->get('supabase_user.id');

    if (!$userId) {
        abort(401, 'Silakan login terlebih dahulu.');
    }

    $profile = DB::table('profiles')
        ->where('id', $userId)
        ->first();

    if (!$profile || $profile->role !== 'admin') {
        abort(403, 'Anda tidak memiliki akses ke halaman admin.');
    }

    $mahasiswaAktivitas = DB::table('profiles as p')
        ->leftJoin('jurusan as j', 'j.id', '=', 'p.jurusan_id')
        ->where('p.role', 'mahasiswa')
        ->orderByDesc('p.created_at')
        ->limit(30)
        ->get([
            'p.id',
            'p.nama',
            'p.nim',
            'p.created_at',
            DB::raw('coalesce(j.nama, p.jurusan) as jurusan_nama'),
        ])
        ->map(function ($item) {
            return [
                'id' => 'mahasiswa-' . $item->id,
                'type' => 'mahasiswa',
                'title' => $item->nama . ' telah terdaftar sebagai mahasiswa.',
                'subtitle' => ($item->nim ?: '-') . ' · ' . ($item->jurusan_nama ?: '-'),
                'created_at' => $item->created_at,
            ];
        });

    $pengumumanAktivitas = DB::table('pengumuman as p')
        ->leftJoin('jurusan as j', 'j.id', '=', 'p.jurusan_id')
        ->orderByDesc('p.created_at')
        ->limit(30)
        ->get([
            'p.id',
            'p.judul',
            'p.created_at',
            'j.nama as target_jurusan',
        ])
        ->map(function ($item) {
            return [
                'id' => 'pengumuman-' . $item->id,
                'type' => 'pengumuman',
                'title' => 'Pengumuman "' . $item->judul . '" telah dipublikasikan.',
                'subtitle' => $item->target_jurusan ?: 'Semua Jurusan',
                'created_at' => $item->created_at,
            ];
        });

    $materiAktivitas = DB::table('materi as m')
        ->leftJoin('jurusan as j', 'j.id', '=', 'm.jurusan_id')
        ->orderByDesc('m.created_at')
        ->limit(30)
        ->get([
            'm.id',
            'm.judul',
            'm.mata_kuliah',
            'm.created_at',
            'j.nama as target_jurusan',
        ])
        ->map(function ($item) {
            return [
                'id' => 'materi-' . $item->id,
                'type' => 'materi',
                'title' => 'Materi "' . $item->judul . '" telah diupload.',
                'subtitle' => ($item->mata_kuliah ?: '-') . ' · ' . ($item->target_jurusan ?: 'Semua Jurusan'),
                'created_at' => $item->created_at,
            ];
        });

    $eventAktivitas = DB::table('events as e')
        ->leftJoin('jurusan as j', 'j.id', '=', 'e.jurusan_id')
        ->orderByDesc('e.created_at')
        ->limit(30)
        ->get([
            'e.id',
            'e.nama_event',
            'e.created_at',
            'j.nama as target_jurusan',
        ])
        ->map(function ($item) {
            return [
                'id' => 'event-' . $item->id,
                'type' => 'event',
                'title' => 'Event "' . $item->nama_event . '" telah ditambahkan.',
                'subtitle' => $item->target_jurusan ?: 'Semua Jurusan',
                'created_at' => $item->created_at,
            ];
        });

    $aktivitas = $mahasiswaAktivitas
        ->concat($pengumumanAktivitas)
        ->concat($materiAktivitas)
        ->concat($eventAktivitas)
        ->sortByDesc('created_at')
        ->take(80)
        ->values();

    $trendBulanan = collect(range(5, 0))
        ->map(function ($offset) {
            $bulan = now()->copy()->startOfMonth()->subMonths($offset);
            $akhirBulan = $bulan->copy()->endOfMonth();

            return [
                'label' => $bulan->translatedFormat('M'),
                'pengumuman' => DB::table('pengumuman')
                    ->whereBetween('created_at', [$bulan, $akhirBulan])
                    ->count(),

                'materi' => DB::table('materi')
                    ->whereBetween('created_at', [$bulan, $akhirBulan])
                    ->count(),

                'events' => DB::table('events')
                    ->whereBetween('created_at', [$bulan, $akhirBulan])
                    ->count(),
            ];
        })
        ->values();

    return Inertia::render('Admin/Dashboard', [
        'stats' => [
            'mahasiswa' => DB::table('profiles')
                ->where('role', 'mahasiswa')
                ->count(),

            'pengumuman' => DB::table('pengumuman')->count(),

            'materi' => DB::table('materi')->count(),

            'events' => DB::table('events')
                ->whereDate('tanggal', '>=', now()->toDateString())
                ->count(),
        ],

        'aktivitas' => $aktivitas,

        'eventsTerbaru' => DB::table('events as e')
            ->leftJoin('jurusan as j', 'j.id', '=', 'e.jurusan_id')
            ->whereDate('e.tanggal', '>=', now()->toDateString())
            ->orderBy('e.tanggal')
            ->limit(5)
            ->get([
                'e.id',
                'e.nama_event',
                'e.tanggal',
                'e.lokasi',
                'j.nama as target_jurusan',
            ]),

        'trendBulanan' => $trendBulanan,
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
