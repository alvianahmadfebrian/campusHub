<script setup>
import { Head, Link } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'

const props = defineProps({
    materi: { type: Array, default: () => [] },
    jurusan: { type: Array, default: () => [] },
})

function formatTanggal(tanggal) {
    if (!tanggal) return '-'
    return new Intl.DateTimeFormat('id-ID', { day: '2-digit', month: 'short', year: 'numeric' }).format(new Date(tanggal))
}

function formatSize(bytes) {
    if (!bytes) return '-'
    const mb = bytes / (1024 * 1024)
    return mb.toFixed(1) + ' MB'
}

function fileIcon(tipe) {
    if (!tipe) return '📄'
    if (tipe.includes('pdf')) return 'PDF'
    if (tipe.includes('doc')) return 'DOC'
    if (tipe.includes('ppt')) return 'PPT'
    if (tipe.includes('zip')) return 'ZIP'
    return 'FILE'
}

function fileColor(tipe) {
    if (!tipe) return 'blue'
    if (tipe.includes('pdf')) return 'red'
    if (tipe.includes('doc')) return 'blue'
    if (tipe.includes('ppt')) return 'orange'
    if (tipe.includes('zip')) return 'purple'
    return 'blue'
}

function kategoriColor(kategori) {
    const map = {
        'Sains & Matematika': 'teal',
        'Teknologi': 'blue',
        'Arsitektur': 'orange',
        'Sistem': 'purple',
        'Hukum': 'red',
        'Database': 'green',
    }
    return map[kategori] || 'gray'
}
</script>

<template>
    <Head title="Akademik - Materi Kuliah" />

    <AdminLayout>
        <!-- HEADER -->
        <div class="ak-header">
            <div>
                <div class="ak-breadcrumb">
                    <Link href="/admin/dashboard" class="ak-breadcrumb-link">Akademik</Link>
                    <span class="ak-breadcrumb-sep">›</span>
                    <span>Materi Kuliah</span>
                </div>
                <h1 class="ak-title">Materi Kuliah</h1>
                <p class="ak-sub">Kelola semua sumber daya belajar dari dosen di satu tempat.</p>
            </div>
            <div class="ak-header-right">
                <div class="ak-user-info">
                    <div class="ak-user-avatar">BS</div>
                    <div>
                        <p class="ak-user-name">Budi Santoso</p>
                        <p class="ak-user-nim">S1 Informatika</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- FILTER BAR -->
        <div class="ak-filter-bar">
            <div class="ak-filter-left">
                <select class="ak-select">
                    <option>Semua Mata Kuliah</option>
                    <option v-for="j in jurusan" :key="j.id">{{ j.nama }}</option>
                </select>
                <button class="ak-filter-btn">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="4" y1="6" x2="20" y2="6"/><line x1="8" y1="12" x2="16" y2="12"/><line x1="11" y1="18" x2="13" y2="18"/></svg>
                    Filter
                </button>
            </div>
        </div>

        <!-- MATERI GRID -->
        <div v-if="materi.length === 0" class="ak-empty">
            <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="#cbd5e1" stroke-width="1.5"><path d="M3 15v4c0 1.1.9 2 2 2h14a2 2 0 0 0 2-2v-4M17 9l-5 5-5-5M12 12.8V2.5"/></svg>
            <p>Mencari materi lain?</p>
            <span>Hubungi dosen pengampu atau ketua kelas jika materi yang Anda cari belum tersedia di portal ini.</span>
            <div class="ak-empty-actions">
                <button class="ak-btn outline">Lihat Arsip Semester Lalu</button>
                <button class="ak-btn teal">Hubungi Admin Akademik</button>
            </div>
        </div>

        <div v-else class="ak-grid">
            <div v-for="item in materi" :key="item.id" class="ak-card">
                <!-- Card Image/Banner -->
                <div class="ak-card-banner" :class="kategoriColor(item.kategori)">
                    <span class="ak-card-kategori-badge" :class="kategoriColor(item.kategori)">
                        {{ item.kategori || 'Umum' }}
                    </span>
                    <button v-if="item.status === 'verified'" class="ak-card-verified">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
                    </button>
                </div>

                <!-- Card Body -->
                <div class="ak-card-body">
                    <h3 class="ak-card-title">{{ item.judul }}</h3>
                    <p class="ak-card-matkul">{{ item.mata_kuliah }}</p>

                    <div class="ak-card-meta">
                        <div class="ak-meta-row">
                            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                            {{ item.dosen || 'Dosen tidak diset' }}
                        </div>
                        <div class="ak-meta-row">
                            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                            {{ formatTanggal(item.created_at) }}
                        </div>
                    </div>

                    <!-- File Download -->
                    <div class="ak-card-files">
                        <a
                            v-if="item.file_url"
                            :href="item.file_url"
                            target="_blank"
                            class="ak-file-btn"
                            :class="fileColor(item.tipe_file)"
                        >
                            <span class="ak-file-label">{{ fileIcon(item.tipe_file) }}</span>
                            {{ formatSize(item.ukuran_file) }}
                            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
                        </a>
                        <button class="ak-unduh-btn">
                            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
                            Unduh Materi
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- EMPTY STATE (no data) -->
        <div v-if="materi.length === 0" class="ak-empty-illustration">
            <div class="ak-cloud-icon">
                <svg width="52" height="52" viewBox="0 0 24 24" fill="none" stroke="#94a3b8" stroke-width="1"><path d="M18 10h-1.26A8 8 0 1 0 9 20h9a5 5 0 0 0 0-10z"/><line x1="12" y1="16" x2="12" y2="12"/><polyline points="8 14 12 18 16 14"/></svg>
            </div>
            <p class="ak-empty-title">Mencari materi lain?</p>
            <p class="ak-empty-sub">Hubungi dosen pengampu atau ketua kelas jika materi yang Anda cari belum tersedia di portal ini.</p>
            <div class="ak-empty-actions">
                <button class="ak-btn outline">Lihat Arsip Semester Lalu</button>
                <button class="ak-btn teal">Hubungi Admin Akademik</button>
            </div>
        </div>

        <p class="ak-footer-note">Menampilkan {{ materi.length }} dari {{ materi.length }} materi</p>
    </AdminLayout>
</template>

<style scoped>
/* HEADER */
.ak-header {
    display: flex;
    align-items: flex-start;
    justify-content: space-between;
    gap: 16px;
    flex-wrap: wrap;
    margin-bottom: 20px;
}

.ak-breadcrumb {
    display: flex;
    align-items: center;
    gap: 6px;
    font-size: 13px;
    color: #64748b;
    margin-bottom: 6px;
}

.ak-breadcrumb-link {
    color: #0d9488;
    font-weight: 600;
    text-decoration: none;
}

.ak-breadcrumb-sep { color: #cbd5e1; }

.ak-title {
    font-size: 26px;
    font-weight: 800;
    letter-spacing: -0.04em;
    color: #0f172a;
    margin: 0 0 4px;
}

.ak-sub {
    font-size: 14px;
    color: #64748b;
    margin: 0;
}

.ak-user-info {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 8px 14px 8px 8px;
    background: #fff;
    border: 1px solid #e2e8f0;
    border-radius: 12px;
}

.ak-user-avatar {
    width: 36px;
    height: 36px;
    border-radius: 50%;
    background: #ccfbf1;
    color: #0d9488;
    font-weight: 700;
    font-size: 13px;
    display: grid;
    place-items: center;
}

.ak-user-name { font-size: 13px; font-weight: 600; color: #0f172a; margin: 0; }
.ak-user-nim { font-size: 11px; color: #94a3b8; margin: 0; }

/* FILTER */
.ak-filter-bar {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 12px;
    margin-bottom: 20px;
}

.ak-filter-left {
    display: flex;
    align-items: center;
    gap: 10px;
}

.ak-select {
    padding: 9px 14px;
    border: 1.5px solid #e2e8f0;
    border-radius: 10px;
    font-size: 13px;
    font-weight: 500;
    color: #374151;
    background: #fff;
    outline: none;
    cursor: pointer;
    font-family: inherit;
}

.ak-filter-btn {
    display: flex;
    align-items: center;
    gap: 6px;
    padding: 9px 14px;
    border: 1.5px solid #e2e8f0;
    border-radius: 10px;
    font-size: 13px;
    font-weight: 500;
    background: #fff;
    color: #374151;
    cursor: pointer;
    font-family: inherit;
    transition: all 0.15s;
}

.ak-filter-btn:hover { background: #f8fafc; }

/* GRID */
.ak-grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 16px;
    margin-bottom: 20px;
}

/* CARD */
.ak-card {
    background: #fff;
    border: 1px solid #e2e8f0;
    border-radius: 14px;
    overflow: hidden;
    transition: transform 0.15s, box-shadow 0.15s;
}

.ak-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 24px rgba(0,0,0,0.08);
}

.ak-card-banner {
    height: 100px;
    position: relative;
    display: flex;
    align-items: flex-start;
    justify-content: space-between;
    padding: 12px;
}

.ak-card-banner.teal { background: linear-gradient(135deg, #0d9488, #06b6d4); }
.ak-card-banner.blue { background: linear-gradient(135deg, #2563eb, #3b82f6); }
.ak-card-banner.orange { background: linear-gradient(135deg, #ea580c, #f97316); }
.ak-card-banner.purple { background: linear-gradient(135deg, #7c3aed, #8b5cf6); }
.ak-card-banner.red { background: linear-gradient(135deg, #dc2626, #ef4444); }
.ak-card-banner.green { background: linear-gradient(135deg, #16a34a, #22c55e); }
.ak-card-banner.gray { background: linear-gradient(135deg, #475569, #64748b); }

.ak-card-kategori-badge {
    display: inline-flex;
    padding: 4px 10px;
    border-radius: 999px;
    font-size: 11px;
    font-weight: 700;
    background: rgba(255,255,255,0.25);
    color: #fff;
    backdrop-filter: blur(4px);
}

.ak-card-verified {
    width: 26px;
    height: 26px;
    border-radius: 50%;
    background: rgba(255,255,255,0.2);
    border: none;
    display: grid;
    place-items: center;
    color: #fff;
    cursor: pointer;
}

.ak-card-body { padding: 14px; }

.ak-card-title {
    font-size: 14px;
    font-weight: 700;
    color: #0f172a;
    margin: 0 0 4px;
    line-height: 1.4;
}

.ak-card-matkul {
    font-size: 12px;
    color: #64748b;
    margin: 0 0 10px;
}

.ak-card-meta { margin-bottom: 12px; display: flex; flex-direction: column; gap: 4px; }

.ak-meta-row {
    display: flex;
    align-items: center;
    gap: 5px;
    font-size: 12px;
    color: #64748b;
}

.ak-card-files {
    display: flex;
    align-items: center;
    gap: 8px;
    flex-wrap: wrap;
}

.ak-file-btn {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    padding: 6px 10px;
    border-radius: 8px;
    font-size: 11px;
    font-weight: 700;
    text-decoration: none;
    transition: opacity 0.15s;
}

.ak-file-btn.red { background: #fee2e2; color: #dc2626; }
.ak-file-btn.blue { background: #dbeafe; color: #2563eb; }
.ak-file-btn.orange { background: #ffedd5; color: #ea580c; }
.ak-file-btn.purple { background: #ede9fe; color: #7c3aed; }

.ak-file-label { font-weight: 800; }

.ak-unduh-btn {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    padding: 6px 12px;
    border-radius: 8px;
    font-size: 12px;
    font-weight: 600;
    background: #f0fdf9;
    border: 1px solid #99f6e4;
    color: #0d9488;
    cursor: pointer;
    font-family: inherit;
    transition: all 0.15s;
}

.ak-unduh-btn:hover { background: #ccfbf1; }

/* BUTTONS */
.ak-btn {
    display: inline-flex;
    align-items: center;
    gap: 7px;
    padding: 10px 18px;
    border-radius: 10px;
    font-size: 13px;
    font-weight: 600;
    cursor: pointer;
    border: none;
    font-family: inherit;
    transition: all 0.15s;
}

.ak-btn.teal { background: #0d9488; color: #fff; }
.ak-btn.teal:hover { background: #0f766e; }
.ak-btn.outline { background: #fff; color: #374151; border: 1.5px solid #e2e8f0; }
.ak-btn.outline:hover { background: #f8fafc; }

/* EMPTY */
.ak-empty-illustration {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 10px;
    padding: 60px 20px;
    text-align: center;
}

.ak-cloud-icon { color: #94a3b8; margin-bottom: 8px; }

.ak-empty-title {
    font-size: 16px;
    font-weight: 700;
    color: #334155;
    margin: 0;
}

.ak-empty-sub {
    font-size: 13px;
    color: #94a3b8;
    max-width: 400px;
    line-height: 1.6;
    margin: 0;
}

.ak-empty-actions {
    display: flex;
    gap: 10px;
    margin-top: 8px;
    flex-wrap: wrap;
    justify-content: center;
}

/* FOOTER */
.ak-footer-note {
    font-size: 12px;
    color: #94a3b8;
    text-align: center;
    margin-top: 8px;
}

/* RESPONSIVE */
@media (max-width: 1200px) { .ak-grid { grid-template-columns: repeat(3, 1fr); } }
@media (max-width: 900px) { .ak-grid { grid-template-columns: repeat(2, 1fr); } }
@media (max-width: 600px) { .ak-grid { grid-template-columns: 1fr; } }
</style>