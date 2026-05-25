<script setup>
import { Head } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'

defineProps({
    stats: { type: Object, default: () => ({}) },
    perJurusan: { type: Array, default: () => [] },
    pengumumanTerbaru: { type: Array, default: () => [] },
    eventsMendatang: { type: Array, default: () => [] },
})

function formatDate(value) {
    if (!value) return '-'

    return new Intl.DateTimeFormat('id-ID', {
        day: 'numeric',
        month: 'short',
        year: 'numeric',
    }).format(new Date(value))
}
</script>

<template>
    <Head title="Laporan Admin" />

    <AdminLayout>
        <div class="lp-page">

            <!-- HEADER -->
            <header class="lp-header">
                <div>
                    <p class="lp-eyebrow">LAPORAN SISTEM</p>
                    <h1 class="lp-title">Laporan &amp; Statistik</h1>
                    <p class="lp-subtitle">Ringkasan data akademik dan aktivitas CampusHub.</p>
                </div>
            </header>

            <!-- STAT CARDS -->
            <div class="lp-stats">
                <div class="lp-stat">
                    <div class="lp-stat-icon teal">
                        <svg viewBox="0 0 24 24" fill="none">
                            <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                            <circle cx="9" cy="7" r="4" stroke="currentColor" stroke-width="1.8"/>
                            <path d="M23 21v-2a4 4 0 0 0-3-3.87M16 3.13a4 4 0 0 1 0 7.75" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                        </svg>
                    </div>
                    <div class="lp-stat-body">
                        <span class="lp-stat-label">Mahasiswa</span>
                        <strong class="lp-stat-num">{{ stats.mahasiswa ?? 0 }}</strong>
                    </div>
                </div>

                <div class="lp-stat">
                    <div class="lp-stat-icon blue">
                        <svg viewBox="0 0 24 24" fill="none">
                            <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/>
                            <path d="M9 22V12h6v10" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/>
                        </svg>
                    </div>
                    <div class="lp-stat-body">
                        <span class="lp-stat-label">Jurusan Aktif</span>
                        <strong class="lp-stat-num">{{ stats.jurusan ?? 0 }}</strong>
                    </div>
                </div>

                <div class="lp-stat">
                    <div class="lp-stat-icon orange">
                        <svg viewBox="0 0 24 24" fill="none">
                            <path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/>
                            <path d="M13.73 21a2 2 0 0 1-3.46 0" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                        </svg>
                    </div>
                    <div class="lp-stat-body">
                        <span class="lp-stat-label">Pengumuman</span>
                        <strong class="lp-stat-num">{{ stats.pengumuman ?? 0 }}</strong>
                    </div>
                </div>

                <div class="lp-stat">
                    <div class="lp-stat-icon purple">
                        <svg viewBox="0 0 24 24" fill="none">
                            <path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                            <path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/>
                        </svg>
                    </div>
                    <div class="lp-stat-body">
                        <span class="lp-stat-label">Materi</span>
                        <strong class="lp-stat-num">{{ stats.materi ?? 0 }}</strong>
                    </div>
                </div>

                <div class="lp-stat">
                    <div class="lp-stat-icon green">
                        <svg viewBox="0 0 24 24" fill="none">
                            <rect x="3" y="4" width="18" height="18" rx="2" stroke="currentColor" stroke-width="1.8"/>
                            <path d="M16 2v4M8 2v4M3 10h18" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                        </svg>
                    </div>
                    <div class="lp-stat-body">
                        <span class="lp-stat-label">Event</span>
                        <strong class="lp-stat-num">{{ stats.events ?? 0 }}</strong>
                    </div>
                </div>

                <div class="lp-stat">
                    <div class="lp-stat-icon slate">
                        <svg viewBox="0 0 24 24" fill="none">
                            <rect x="3" y="3" width="18" height="18" rx="2" stroke="currentColor" stroke-width="1.8"/>
                            <path d="M3 9h18M9 21V9" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                        </svg>
                    </div>
                    <div class="lp-stat-body">
                        <span class="lp-stat-label">Jadwal</span>
                        <strong class="lp-stat-num">{{ stats.jadwal ?? 0 }}</strong>
                    </div>
                </div>
            </div>

            <!-- REKAP PER JURUSAN -->
            <section class="lp-card">
                <div class="lp-card-head">
                    <div>
                        <h2 class="lp-card-title">Rekap per Jurusan</h2>
                        <p class="lp-card-desc">Data mahasiswa, konten, dan event berdasarkan jurusan</p>
                    </div>
                    <span class="lp-count-badge">{{ perJurusan.length }} jurusan</span>
                </div>

                <div class="lp-table-wrap">
                    <table class="lp-table">
                        <thead>
                            <tr>
                                <th>Jurusan</th>
                                <th>Status</th>
                                <th>Mahasiswa</th>
                                <th>Pengumuman</th>
                                <th>Materi</th>
                                <th>Event</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="item in perJurusan" :key="item.id" class="lp-table-row">
                                <td>
                                    <div class="lp-jurusan-cell">
                                        <div class="lp-jurusan-avatar">
                                            {{ (item.kode || item.nama || '?').charAt(0).toUpperCase() }}
                                        </div>
                                        <div>
                                            <strong>{{ item.nama }}</strong>
                                            <small>{{ item.kode || '-' }}</small>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span class="lp-badge" :class="item.aktif ? 'aktif' : 'off'">
                                        <span class="lp-badge-dot"></span>
                                        {{ item.aktif ? 'Aktif' : 'Nonaktif' }}
                                    </span>
                                </td>
                                <td>
                                    <span class="lp-num-cell">{{ item.mahasiswa }}</span>
                                </td>
                                <td>
                                    <span class="lp-num-cell">{{ item.pengumuman }}</span>
                                </td>
                                <td>
                                    <span class="lp-num-cell">{{ item.materi }}</span>
                                </td>
                                <td>
                                    <span class="lp-num-cell">{{ item.events }}</span>
                                </td>
                            </tr>
                            <tr v-if="perJurusan.length === 0">
                                <td colspan="6" class="lp-empty-row">Belum ada data jurusan.</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </section>

            <!-- BOTTOM GRID -->
            <div class="lp-bottom-grid">

                <!-- PENGUMUMAN TERBARU -->
                <section class="lp-card">
                    <div class="lp-card-head">
                        <div>
                            <h2 class="lp-card-title">Pengumuman Terbaru</h2>
                            <p class="lp-card-desc">Pengumuman yang baru diterbitkan</p>
                        </div>
                        <div class="lp-card-icon orange">
                            <svg viewBox="0 0 24 24" fill="none">
                                <path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/>
                                <path d="M13.73 21a2 2 0 0 1-3.46 0" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                            </svg>
                        </div>
                    </div>

                    <div class="lp-list">
                        <div
                            v-for="item in pengumumanTerbaru"
                            :key="item.id"
                            class="lp-list-item"
                        >
                            <div class="lp-list-dot orange"></div>
                            <div class="lp-list-body">
                                <strong>{{ item.judul }}</strong>
                                <span>{{ formatDate(item.created_at) }}</span>
                            </div>
                        </div>

                        <p v-if="pengumumanTerbaru.length === 0" class="lp-empty">
                            Belum ada pengumuman.
                        </p>
                    </div>
                </section>

                <!-- EVENT MENDATANG -->
                <section class="lp-card">
                    <div class="lp-card-head">
                        <div>
                            <h2 class="lp-card-title">Event Mendatang</h2>
                            <p class="lp-card-desc">Kegiatan yang akan segera berlangsung</p>
                        </div>
                        <div class="lp-card-icon teal">
                            <svg viewBox="0 0 24 24" fill="none">
                                <rect x="3" y="4" width="18" height="18" rx="2" stroke="currentColor" stroke-width="1.8"/>
                                <path d="M16 2v4M8 2v4M3 10h18" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                            </svg>
                        </div>
                    </div>

                    <div class="lp-list">
                        <div
                            v-for="item in eventsMendatang"
                            :key="item.id"
                            class="lp-list-item"
                        >
                            <div class="lp-list-dot teal"></div>
                            <div class="lp-list-body">
                                <strong>{{ item.nama_event }}</strong>
                                <span>{{ formatDate(item.tanggal) }} · {{ item.lokasi || '-' }}</span>
                            </div>
                        </div>

                        <p v-if="eventsMendatang.length === 0" class="lp-empty">
                            Belum ada event mendatang.
                        </p>
                    </div>
                </section>

            </div>
        </div>
    </AdminLayout>
</template>

<style scoped>
.lp-page {
    display: flex;
    flex-direction: column;
    gap: 20px;
}

/* ── HEADER ── */
.lp-header {
    display: flex;
    align-items: flex-start;
    justify-content: space-between;
    gap: 16px;
}

.lp-eyebrow {
    margin: 0 0 7px;
    color: #0d9488;
    font-size: 11px;
    font-weight: 800;
    letter-spacing: 0.11em;
}

.lp-title {
    margin: 0 0 6px;
    color: #0f172a;
    font-size: 30px;
    font-weight: 800;
    line-height: 1.12;
}

.lp-subtitle {
    margin: 0;
    color: #64748b;
    font-size: 14px;
}

/* ── STAT CARDS ── */
.lp-stats {
    display: grid;
    grid-template-columns: repeat(6, minmax(0, 1fr));
    gap: 12px;
}

.lp-stat {
    display: flex;
    align-items: center;
    gap: 14px;
    padding: 18px 16px;
    border: 1px solid #e2e8f0;
    border-radius: 16px;
    background: #ffffff;
    transition: box-shadow 0.2s;
}

.lp-stat:hover {
    box-shadow: 0 4px 16px rgba(0,0,0,0.06);
}

.lp-stat-icon {
    display: grid;
    place-items: center;
    flex-shrink: 0;
    width: 42px;
    height: 42px;
    border-radius: 12px;
}

.lp-stat-icon svg {
    width: 20px;
    height: 20px;
}

.lp-stat-icon.teal   { background: #ccfbf1; color: #0d9488; }
.lp-stat-icon.blue   { background: #dbeafe; color: #2563eb; }
.lp-stat-icon.orange { background: #ffedd5; color: #ea580c; }
.lp-stat-icon.purple { background: #ede9fe; color: #7c3aed; }
.lp-stat-icon.green  { background: #dcfce7; color: #16a34a; }
.lp-stat-icon.slate  { background: #f1f5f9; color: #475569; }

.lp-stat-body {
    display: flex;
    flex-direction: column;
    gap: 3px;
    min-width: 0;
}

.lp-stat-label {
    color: #64748b;
    font-size: 11px;
    font-weight: 500;
    white-space: nowrap;
}

.lp-stat-num {
    color: #0f172a;
    font-size: 26px;
    font-weight: 800;
    line-height: 1;
    letter-spacing: -0.02em;
}

/* ── CARD ── */
.lp-card {
    padding: 20px 22px;
    border: 1px solid #e2e8f0;
    border-radius: 17px;
    background: #ffffff;
}

.lp-card-head {
    display: flex;
    align-items: flex-start;
    justify-content: space-between;
    gap: 12px;
    margin-bottom: 18px;
}

.lp-card-title {
    margin: 0 0 4px;
    color: #0f172a;
    font-size: 16px;
    font-weight: 700;
}

.lp-card-desc {
    margin: 0;
    color: #94a3b8;
    font-size: 12px;
}

.lp-count-badge {
    flex-shrink: 0;
    padding: 4px 11px;
    border-radius: 999px;
    background: #f0fdf9;
    border: 1px solid #99f6e4;
    color: #0d9488;
    font-size: 11px;
    font-weight: 700;
}

.lp-card-icon {
    display: grid;
    place-items: center;
    flex-shrink: 0;
    width: 38px;
    height: 38px;
    border-radius: 11px;
}

.lp-card-icon svg {
    width: 20px;
    height: 20px;
}

.lp-card-icon.teal   { background: #ccfbf1; color: #0d9488; }
.lp-card-icon.orange { background: #ffedd5; color: #ea580c; }

/* ── TABLE ── */
.lp-table-wrap {
    overflow-x: auto;
    border: 1px solid #e2e8f0;
    border-radius: 13px;
}

.lp-table {
    width: 100%;
    border-collapse: collapse;
}

.lp-table thead {
    background: #f8fafc;
}

.lp-table th {
    padding: 11px 16px;
    border-bottom: 1px solid #e2e8f0;
    color: #475569;
    font-size: 11px;
    font-weight: 700;
    letter-spacing: 0.05em;
    text-align: left;
    text-transform: uppercase;
    white-space: nowrap;
}

.lp-table td {
    padding: 13px 16px;
    border-bottom: 1px solid #f1f5f9;
    color: #334155;
    font-size: 13px;
    vertical-align: middle;
}

.lp-table-row:last-child td {
    border-bottom: none;
}

.lp-table-row:hover td {
    background: #fafcff;
}

.lp-jurusan-cell {
    display: flex;
    align-items: center;
    gap: 11px;
}

.lp-jurusan-avatar {
    display: grid;
    place-items: center;
    flex-shrink: 0;
    width: 34px;
    height: 34px;
    border-radius: 9px;
    background: #f0fdf9;
    border: 1px solid #99f6e4;
    color: #0d9488;
    font-size: 13px;
    font-weight: 800;
}

.lp-jurusan-cell strong {
    display: block;
    color: #0f172a;
    font-size: 13px;
    font-weight: 600;
}

.lp-jurusan-cell small {
    display: block;
    margin-top: 2px;
    color: #94a3b8;
    font-size: 11px;
}

.lp-badge {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    padding: 4px 10px;
    border-radius: 999px;
    font-size: 11px;
    font-weight: 700;
}

.lp-badge.aktif {
    background: #f0fdf9;
    color: #0d9488;
}

.lp-badge.off {
    background: #f1f5f9;
    color: #64748b;
}

.lp-badge-dot {
    width: 6px;
    height: 6px;
    border-radius: 50%;
    background: currentColor;
}

.lp-num-cell {
    display: inline-block;
    min-width: 28px;
    padding: 3px 10px;
    border-radius: 8px;
    background: #f8fafc;
    color: #334155;
    font-size: 13px;
    font-weight: 600;
    text-align: center;
}

.lp-empty-row {
    color: #94a3b8;
    font-size: 13px;
    text-align: center;
    padding: 24px !important;
}

/* ── BOTTOM GRID ── */
.lp-bottom-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 16px;
}

/* ── LIST ── */
.lp-list {
    display: flex;
    flex-direction: column;
    gap: 0;
}

.lp-list-item {
    display: flex;
    align-items: flex-start;
    gap: 12px;
    padding: 13px 0;
    border-top: 1px solid #f1f5f9;
}

.lp-list-item:first-child {
    border-top: none;
    padding-top: 0;
}

.lp-list-dot {
    flex-shrink: 0;
    margin-top: 5px;
    width: 8px;
    height: 8px;
    border-radius: 50%;
}

.lp-list-dot.teal   { background: #0d9488; }
.lp-list-dot.orange { background: #ea580c; }

.lp-list-body {
    display: flex;
    flex-direction: column;
    gap: 3px;
    min-width: 0;
}

.lp-list-body strong {
    display: block;
    color: #0f172a;
    font-size: 13px;
    font-weight: 600;
    line-height: 1.4;
}

.lp-list-body span {
    color: #64748b;
    font-size: 12px;
}

.lp-empty {
    margin: 8px 0 0;
    color: #94a3b8;
    font-size: 13px;
}

/* ── RESPONSIVE ── */
@media (max-width: 1200px) {
    .lp-stats {
        grid-template-columns: repeat(3, 1fr);
    }
}

@media (max-width: 800px) {
    .lp-stats {
        grid-template-columns: repeat(2, 1fr);
    }

    .lp-bottom-grid {
        grid-template-columns: 1fr;
    }
}

@media (max-width: 480px) {
    .lp-stats {
        grid-template-columns: 1fr;
    }

    .lp-title {
        font-size: 24px;
    }
}
</style>