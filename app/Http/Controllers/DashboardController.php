<?php

namespace App\Http\Controllers;

use Illuminate\Database\Query\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    public function index(Request $request): Response|RedirectResponse
    {
        $userId = $request->session()->get('supabase_user.id');

        abort_unless($userId, 401);

        $profile = DB::table('profiles as p')
            ->leftJoin('jurusan as j', 'j.id', '=', 'p.jurusan_id')
            ->where('p.id', $userId)
            ->select(
                'p.id',
                'p.nama',
                'p.nim',
                'p.email',
                'p.role',
                'p.jurusan_id',
                DB::raw('coalesce(j.nama, p.jurusan) as jurusan_nama')
            )
            ->first();

        if ($profile && $profile->role === 'admin') {
            return redirect()->route('admin.dashboard');
        }

        $jurusanId = $profile?->jurusan_id;

        $pengumumanBase = $this->targetedQuery(
            DB::table('pengumuman as p')
                ->leftJoin('jurusan as j', 'j.id', '=', 'p.jurusan_id'),
            'p.jurusan_id',
            $jurusanId
        );

        $materiBase = $this->targetedQuery(
            DB::table('materi as m')
                ->leftJoin('jurusan as j', 'j.id', '=', 'm.jurusan_id'),
            'm.jurusan_id',
            $jurusanId
        );

        $eventsBase = $this->targetedQuery(
            DB::table('events as e')
                ->leftJoin('jurusan as j', 'j.id', '=', 'e.jurusan_id'),
            'e.jurusan_id',
            $jurusanId
        )->whereDate('e.tanggal', '>=', now()->toDateString());

        return Inertia::render('Dashboard', [
            'profile' => $profile,

            'stats' => [
                'pengumuman' => (clone $pengumumanBase)->count(),
                'materi' => (clone $materiBase)->count(),
                'events' => (clone $eventsBase)->count(),
            ],

            'pengumuman' => (clone $pengumumanBase)
                ->orderByDesc('p.created_at')
                ->limit(5)
                ->get([
                    'p.id',
                    'p.judul',
                    'p.kategori',
                    'p.created_at',
                    'j.nama as target_jurusan',
                ]),

            'materi' => (clone $materiBase)
                ->orderByDesc('m.created_at')
                ->limit(5)
                ->get([
                    'm.id',
                    'm.judul',
                    'm.mata_kuliah',
                    'm.created_at',
                    'j.nama as target_jurusan',
                ]),

            'events' => (clone $eventsBase)
                ->orderBy('e.tanggal')
                ->limit(5)
                ->get([
                    'e.id',
                    'e.nama_event',
                    'e.tanggal',
                    'e.lokasi',
                    'j.nama as target_jurusan',
                ]),
        ]);
    }

    public function admin(Request $request): Response
    {
        $userId = $request->session()->get('supabase_user.id');

        abort_unless($userId, 401);

        $profile = DB::table('profiles')
            ->where('id', $userId)
            ->select('id', 'nama', 'role')
            ->first();

        abort_unless($profile && $profile->role === 'admin', 403);

        $today = now()->toDateString();

        $stats = Cache::remember('admin_dashboard_stats_' . $today, now()->addMinutes(5), function () use ($today) {
            return [
                'mahasiswa' => DB::table('profiles')
                    ->where('role', 'mahasiswa')
                    ->count(),

                'pengumuman' => DB::table('pengumuman')->count(),

                'materi' => DB::table('materi')->count(),

                'events' => DB::table('events')
                    ->whereDate('tanggal', '>=', $today)
                    ->count(),
            ];
        });

        $mahasiswaAktivitas = DB::table('profiles as p')
            ->leftJoin('jurusan as j', 'j.id', '=', 'p.jurusan_id')
            ->where('p.role', 'mahasiswa')
            ->orderByDesc('p.created_at')
            ->limit(8)
            ->get([
                'p.id',
                'p.nama',
                'p.nim',
                'p.created_at',
                DB::raw('coalesce(j.nama, p.jurusan) as jurusan_nama'),
            ])
            ->map(fn ($item) => [
                'id' => 'mahasiswa-' . $item->id,
                'type' => 'mahasiswa',
                'title' => $item->nama . ' telah terdaftar sebagai mahasiswa.',
                'subtitle' => ($item->nim ?: '-') . ' · ' . ($item->jurusan_nama ?: '-'),
                'created_at' => $item->created_at,
            ]);

        $pengumumanAktivitas = DB::table('pengumuman as p')
            ->leftJoin('jurusan as j', 'j.id', '=', 'p.jurusan_id')
            ->orderByDesc('p.created_at')
            ->limit(8)
            ->get([
                'p.id',
                'p.judul',
                'p.created_at',
                'j.nama as target_jurusan',
            ])
            ->map(fn ($item) => [
                'id' => 'pengumuman-' . $item->id,
                'type' => 'pengumuman',
                'title' => 'Pengumuman "' . $item->judul . '" telah dipublikasikan.',
                'subtitle' => $item->target_jurusan ?: 'Semua Jurusan',
                'created_at' => $item->created_at,
            ]);

        $materiAktivitas = DB::table('materi as m')
            ->leftJoin('jurusan as j', 'j.id', '=', 'm.jurusan_id')
            ->orderByDesc('m.created_at')
            ->limit(8)
            ->get([
                'm.id',
                'm.judul',
                'm.mata_kuliah',
                'm.created_at',
                'j.nama as target_jurusan',
            ])
            ->map(fn ($item) => [
                'id' => 'materi-' . $item->id,
                'type' => 'materi',
                'title' => 'Materi "' . $item->judul . '" telah diupload.',
                'subtitle' => ($item->mata_kuliah ?: '-') . ' · ' . ($item->target_jurusan ?: 'Semua Jurusan'),
                'created_at' => $item->created_at,
            ]);

        $eventAktivitas = DB::table('events as e')
            ->leftJoin('jurusan as j', 'j.id', '=', 'e.jurusan_id')
            ->orderByDesc('e.created_at')
            ->limit(8)
            ->get([
                'e.id',
                'e.nama_event',
                'e.created_at',
                'j.nama as target_jurusan',
            ])
            ->map(fn ($item) => [
                'id' => 'event-' . $item->id,
                'type' => 'event',
                'title' => 'Event "' . $item->nama_event . '" telah ditambahkan.',
                'subtitle' => $item->target_jurusan ?: 'Semua Jurusan',
                'created_at' => $item->created_at,
            ]);

        $aktivitas = $mahasiswaAktivitas
            ->concat($pengumumanAktivitas)
            ->concat($materiAktivitas)
            ->concat($eventAktivitas)
            ->sortByDesc('created_at')
            ->take(20)
            ->values();

        $eventsTerbaru = DB::table('events as e')
            ->leftJoin('jurusan as j', 'j.id', '=', 'e.jurusan_id')
            ->whereDate('e.tanggal', '>=', $today)
            ->orderBy('e.tanggal')
            ->limit(5)
            ->get([
                'e.id',
                'e.nama_event',
                'e.tanggal',
                'e.lokasi',
                'j.nama as target_jurusan',
            ]);

        $trendBulanan = Cache::remember('admin_dashboard_trend_bulanan', now()->addMinutes(10), function () {
            return collect(range(5, 0))
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
        });

        return Inertia::render('Admin/Dashboard', [
            'stats' => $stats,
            'aktivitas' => $aktivitas,
            'eventsTerbaru' => $eventsTerbaru,
            'trendBulanan' => $trendBulanan,
        ]);
    }

    private function targetedQuery(Builder $query, string $column, ?string $jurusanId): Builder
    {
        return $query->where(function (Builder $builder) use ($column, $jurusanId): void {
            $builder->whereNull($column);

            if ($jurusanId) {
                $builder->orWhere($column, $jurusanId);
            }
        });
    }
}
