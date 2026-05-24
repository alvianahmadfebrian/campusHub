<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;

class AdminPageController extends Controller
{
    public function akademik(): Response
    {
        return Inertia::render('Admin/akademik', [
            'jurusan' => DB::table('jurusan')
                ->orderByDesc('aktif')
                ->orderBy('nama')
                ->get(),

            'pengumuman' => DB::table('pengumuman as p')
                ->leftJoin('jurusan as j', 'j.id', '=', 'p.jurusan_id')
                ->select(
                    'p.id',
                    'p.judul',
                    'p.kategori',
                    'p.isi',
                    'p.created_at',
                    'j.nama as target_jurusan'
                )
                ->orderByDesc('p.created_at')
                ->limit(20)
                ->get(),

            'materi' => DB::table('materi as m')
                ->leftJoin('jurusan as j', 'j.id', '=', 'm.jurusan_id')
                ->select(
                    'm.id',
                    'm.judul',
                    'm.mata_kuliah',
                    'm.deskripsi',
                    'm.created_at',
                    'j.nama as target_jurusan'
                )
                ->orderByDesc('m.created_at')
                ->limit(20)
                ->get(),

            'events' => DB::table('events as e')
                ->leftJoin('jurusan as j', 'j.id', '=', 'e.jurusan_id')
                ->select(
                    'e.id',
                    'e.nama_event',
                    'e.tanggal',
                    'e.lokasi',
                    'e.deskripsi',
                    'j.nama as target_jurusan'
                )
                ->orderByDesc('e.created_at')
                ->limit(20)
                ->get(),
        ]);
    }

    public function jadwal(): Response
    {
        $ready = Schema::hasTable('jadwal_kuliah');

        return Inertia::render('Admin/Jadwal', [
            'jadwalReady' => $ready,

            'jurusan' => DB::table('jurusan')
                ->where('aktif', true)
                ->orderBy('nama')
                ->get(['id', 'nama', 'kode']),

            'jadwal' => $ready
                ? DB::table('jadwal_kuliah as jk')
                    ->leftJoin('jurusan as j', 'j.id', '=', 'jk.jurusan_id')
                    ->select(
                        'jk.id',
                        'jk.mata_kuliah',
                        'jk.dosen',
                        'jk.hari',
                        'jk.jam_mulai',
                        'jk.jam_selesai',
                        'jk.ruangan',
                        'jk.semester',
                        'jk.jurusan_id',
                        'j.nama as target_jurusan'
                    )
                    ->orderByRaw("
                        case jk.hari
                            when 'Senin' then 1
                            when 'Selasa' then 2
                            when 'Rabu' then 3
                            when 'Kamis' then 4
                            when 'Jumat' then 5
                            when 'Sabtu' then 6
                            else 7
                        end
                    ")
                    ->orderBy('jk.jam_mulai')
                    ->get()
                : [],
        ]);
    }

    public function storeJadwal(Request $request): RedirectResponse
    {
        if (!Schema::hasTable('jadwal_kuliah')) {
            return back()->withErrors([
                'jadwal' => 'Tabel jadwal_kuliah belum dibuat. Jalankan SQL jadwal terlebih dahulu.',
            ]);
        }

        $validated = $request->validate([
            'mata_kuliah' => ['required', 'string', 'max:150'],
            'dosen' => ['required', 'string', 'max:150'],
            'hari' => ['required', 'in:Senin,Selasa,Rabu,Kamis,Jumat,Sabtu'],
            'jam_mulai' => ['required', 'date_format:H:i'],
            'jam_selesai' => ['required', 'date_format:H:i', 'after:jam_mulai'],
            'ruangan' => ['nullable', 'string', 'max:100'],
            'semester' => ['nullable', 'integer', 'min:1', 'max:14'],
            'jurusan_id' => ['nullable', 'uuid', 'exists:jurusan,id'],
        ]);

        DB::table('jadwal_kuliah')->insert([
            'id' => (string) Str::uuid(),
            'mata_kuliah' => $validated['mata_kuliah'],
            'dosen' => $validated['dosen'],
            'hari' => $validated['hari'],
            'jam_mulai' => $validated['jam_mulai'],
            'jam_selesai' => $validated['jam_selesai'],
            'ruangan' => $validated['ruangan'] ?? null,
            'semester' => $validated['semester'] ?? null,
            'jurusan_id' => $validated['jurusan_id'] ?: null,
            'created_by' => $request->session()->get('supabase_user.id'),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return back()->with('success', 'Jadwal kuliah berhasil ditambahkan.');
    }

    public function updateJadwal(Request $request, string $id): RedirectResponse
    {
        $validated = $request->validate([
            'mata_kuliah' => ['required', 'string', 'max:150'],
            'dosen' => ['required', 'string', 'max:150'],
            'hari' => ['required', 'in:Senin,Selasa,Rabu,Kamis,Jumat,Sabtu'],
            'jam_mulai' => ['required', 'date_format:H:i'],
            'jam_selesai' => ['required', 'date_format:H:i', 'after:jam_mulai'],
            'ruangan' => ['nullable', 'string', 'max:100'],
            'semester' => ['nullable', 'integer', 'min:1', 'max:14'],
            'jurusan_id' => ['nullable', 'uuid', 'exists:jurusan,id'],
        ]);

        $updated = DB::table('jadwal_kuliah')
            ->where('id', $id)
            ->update([
                'mata_kuliah' => $validated['mata_kuliah'],
                'dosen' => $validated['dosen'],
                'hari' => $validated['hari'],
                'jam_mulai' => $validated['jam_mulai'],
                'jam_selesai' => $validated['jam_selesai'],
                'ruangan' => $validated['ruangan'] ?? null,
                'semester' => $validated['semester'] ?? null,
                'jurusan_id' => $validated['jurusan_id'] ?: null,
                'updated_at' => now(),
            ]);

        abort_if($updated === 0, 404);

        return back()->with('success', 'Jadwal kuliah berhasil diperbarui.');
    }

    public function destroyJadwal(string $id): RedirectResponse
    {
        DB::table('jadwal_kuliah')
            ->where('id', $id)
            ->delete();

        return back()->with('success', 'Jadwal kuliah berhasil dihapus.');
    }

    public function laporan(): Response
    {
        $jurusan = DB::table('jurusan')
            ->orderBy('nama')
            ->get()
            ->map(function ($item) {
                return [
                    'id' => $item->id,
                    'nama' => $item->nama,
                    'kode' => $item->kode,
                    'aktif' => $item->aktif,
                    'mahasiswa' => DB::table('profiles')
                        ->where('role', 'mahasiswa')
                        ->where('jurusan_id', $item->id)
                        ->count(),
                    'pengumuman' => DB::table('pengumuman')
                        ->where('jurusan_id', $item->id)
                        ->count(),
                    'materi' => DB::table('materi')
                        ->where('jurusan_id', $item->id)
                        ->count(),
                    'events' => DB::table('events')
                        ->where('jurusan_id', $item->id)
                        ->count(),
                ];
            });

        return Inertia::render('Admin/Laporan', [
            'stats' => [
                'mahasiswa' => DB::table('profiles')
                    ->where('role', 'mahasiswa')
                    ->count(),

                'jurusan' => DB::table('jurusan')
                    ->where('aktif', true)
                    ->count(),

                'pengumuman' => DB::table('pengumuman')->count(),

                'materi' => DB::table('materi')->count(),

                'events' => DB::table('events')->count(),

                'jadwal' => Schema::hasTable('jadwal_kuliah')
                    ? DB::table('jadwal_kuliah')->count()
                    : 0,
            ],

            'perJurusan' => $jurusan,

            'pengumumanTerbaru' => DB::table('pengumuman')
                ->orderByDesc('created_at')
                ->limit(5)
                ->get(['id', 'judul', 'created_at']),

            'eventsMendatang' => DB::table('events')
                ->whereDate('tanggal', '>=', now()->toDateString())
                ->orderBy('tanggal')
                ->limit(5)
                ->get(['id', 'nama_event', 'tanggal', 'lokasi']),
        ]);
    }

    public function pengaturan(Request $request): Response
    {
        $userId = $request->session()->get('supabase_user.id');

        $profile = DB::table('profiles')
            ->where('id', $userId)
            ->first();

        return Inertia::render('Admin/Pengaturan', [
            'profile' => $profile,
            'email' => $request->session()->get('supabase_user.email'),
        ]);
    }

    public function updatePengaturan(Request $request): RedirectResponse
    {
        $userId = $request->session()->get('supabase_user.id');

        $validated = $request->validate([
            'nama' => ['required', 'string', 'max:120'],
            'avatar_url' => ['nullable', 'url', 'max:500'],
        ]);

        DB::table('profiles')
            ->where('id', $userId)
            ->update([
                'nama' => $validated['nama'],
                'avatar_url' => $validated['avatar_url'] ?: null,
                'updated_at' => now(),
            ]);

        return back()->with('success', 'Pengaturan profil admin berhasil disimpan.');
    }

        /**
     * Halaman manajemen konten admin.
     */
    public function konten(): Response
    {
        return Inertia::render('Admin/Konten', [
            'stats' => [
                'pengumuman' => DB::table('pengumuman')->count(),
                'materi' => DB::table('materi')->count(),
                'events' => DB::table('events')->count(),
            ],

            'jurusan' => DB::table('jurusan')
                ->where('aktif', true)
                ->orderBy('nama')
                ->get([
                    'id',
                    'nama',
                    'kode',
                ]),

            'pengumuman' => DB::table('pengumuman as p')
                ->leftJoin('jurusan as j', 'j.id', '=', 'p.jurusan_id')
                ->orderByDesc('p.created_at')
                ->limit(100)
                ->get([
                    'p.id',
                    'p.judul',
                    'p.kategori',
                    'p.isi',
                    'p.jurusan_id',
                    'p.created_at',
                    'j.nama as target_jurusan',
                ]),

            'materi' => DB::table('materi as m')
                ->leftJoin('jurusan as j', 'j.id', '=', 'm.jurusan_id')
                ->orderByDesc('m.created_at')
                ->limit(100)
                ->get([
                    'm.id',
                    'm.judul',
                    'm.mata_kuliah',
                    'm.deskripsi',
                    'm.jurusan_id',
                    'm.created_at',
                    'j.nama as target_jurusan',
                ]),

            'events' => DB::table('events as e')
                ->leftJoin('jurusan as j', 'j.id', '=', 'e.jurusan_id')
                ->orderByDesc('e.created_at')
                ->limit(100)
                ->get([
                    'e.id',
                    'e.nama_event',
                    'e.deskripsi',
                    'e.tanggal',
                    'e.lokasi',
                    'e.link_pendaftaran',
                    'e.jurusan_id',
                    'e.created_at',
                    'j.nama as target_jurusan',
                ]),
        ]);
    }

    /**
     * Update pengumuman dari halaman manajemen konten.
     */
    public function updateKontenPengumuman(Request $request, string $id): RedirectResponse
    {
        $validated = $request->validate([
            'judul' => ['required', 'string', 'max:200'],
            'kategori' => ['nullable', 'string', 'max:100'],
            'isi' => ['required', 'string'],
            'jurusan_id' => ['nullable', 'uuid', 'exists:jurusan,id'],
        ]);

        $payload = [
            'judul' => $validated['judul'],
            'kategori' => $validated['kategori'] ?: 'Umum',
            'isi' => $validated['isi'],
            'jurusan_id' => $validated['jurusan_id'] ?: null,
        ];

        $payload = $this->tambahkanUpdatedAt('pengumuman', $payload);

        DB::table('pengumuman')
            ->where('id', $id)
            ->update($payload);

        return back()->with('success', 'Pengumuman berhasil diperbarui.');
    }

    /**
     * Hapus pengumuman.
     */
    public function destroyKontenPengumuman(string $id): RedirectResponse
    {
        DB::table('pengumuman')
            ->where('id', $id)
            ->delete();

        return back()->with('success', 'Pengumuman berhasil dihapus.');
    }

    /**
     * Update informasi materi.
     * File materi tidak berubah; upload file baru dilakukan melalui menu Akademik.
     */
    public function updateKontenMateri(Request $request, string $id): RedirectResponse
    {
        $validated = $request->validate([
            'judul' => ['required', 'string', 'max:200'],
            'mata_kuliah' => ['required', 'string', 'max:150'],
            'deskripsi' => ['nullable', 'string'],
            'jurusan_id' => ['nullable', 'uuid', 'exists:jurusan,id'],
        ]);

        $payload = [
            'judul' => $validated['judul'],
            'mata_kuliah' => $validated['mata_kuliah'],
            'deskripsi' => $validated['deskripsi'] ?: null,
            'jurusan_id' => $validated['jurusan_id'] ?: null,
        ];

        $payload = $this->tambahkanUpdatedAt('materi', $payload);

        DB::table('materi')
            ->where('id', $id)
            ->update($payload);

        return back()->with('success', 'Informasi materi berhasil diperbarui.');
    }

    /**
     * Hapus materi dari daftar konten.
     */
    public function destroyKontenMateri(string $id): RedirectResponse
    {
        DB::table('materi')
            ->where('id', $id)
            ->delete();

        return back()->with('success', 'Materi berhasil dihapus.');
    }

    /**
     * Update event dari halaman manajemen konten.
     */
    public function updateKontenEvent(Request $request, string $id): RedirectResponse
    {
        $validated = $request->validate([
            'nama_event' => ['required', 'string', 'max:200'],
            'deskripsi' => ['nullable', 'string'],
            'tanggal' => ['required', 'date'],
            'lokasi' => ['nullable', 'string', 'max:200'],
            'link_pendaftaran' => ['nullable', 'url', 'max:500'],
            'jurusan_id' => ['nullable', 'uuid', 'exists:jurusan,id'],
        ]);

        $payload = [
            'nama_event' => $validated['nama_event'],
            'deskripsi' => $validated['deskripsi'] ?: null,
            'tanggal' => $validated['tanggal'],
            'lokasi' => $validated['lokasi'] ?: null,
            'link_pendaftaran' => $validated['link_pendaftaran'] ?: null,
            'jurusan_id' => $validated['jurusan_id'] ?: null,
        ];

        $payload = $this->tambahkanUpdatedAt('events', $payload);

        DB::table('events')
            ->where('id', $id)
            ->update($payload);

        return back()->with('success', 'Event berhasil diperbarui.');
    }

    /**
     * Hapus event.
     */
    public function destroyKontenEvent(string $id): RedirectResponse
    {
        DB::table('events')
            ->where('id', $id)
            ->delete();

        return back()->with('success', 'Event berhasil dihapus.');
    }

    /**
     * Menambahkan updated_at hanya jika kolom tersedia pada tabel.
     */
    private function tambahkanUpdatedAt(string $table, array $payload): array
    {
        if (Schema::hasColumn($table, 'updated_at')) {
            $payload['updated_at'] = now();
        }

        return $payload;
    }
}
