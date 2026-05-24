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
            <header>
                <p class="eyebrow">LAPORAN SISTEM</p>
                <h1>Laporan</h1>
                <p class="subtitle">Ringkasan data akademik dan aktivitas CampusHub.</p>
            </header>

            <div class="stats">
                <div class="stat">
                    <span>Mahasiswa</span>
                    <strong>{{ stats.mahasiswa ?? 0 }}</strong>
                </div>
                <div class="stat">
                    <span>Jurusan Aktif</span>
                    <strong>{{ stats.jurusan ?? 0 }}</strong>
                </div>
                <div class="stat">
                    <span>Pengumuman</span>
                    <strong>{{ stats.pengumuman ?? 0 }}</strong>
                </div>
                <div class="stat">
                    <span>Materi</span>
                    <strong>{{ stats.materi ?? 0 }}</strong>
                </div>
                <div class="stat">
                    <span>Event</span>
                    <strong>{{ stats.events ?? 0 }}</strong>
                </div>
                <div class="stat">
                    <span>Jadwal</span>
                    <strong>{{ stats.jadwal ?? 0 }}</strong>
                </div>
            </div>

            <section class="card">
                <div class="card-head">
                    <h2>Rekap per Jurusan</h2>
                </div>

                <div class="table-wrap">
                    <table>
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
                            <tr v-for="item in perJurusan" :key="item.id">
                                <td>
                                    <strong>{{ item.nama }}</strong>
                                    <small>{{ item.kode || '-' }}</small>
                                </td>
                                <td>
                                    <span class="badge" :class="{ off: !item.aktif }">
                                        {{ item.aktif ? 'Aktif' : 'Nonaktif' }}
                                    </span>
                                </td>
                                <td>{{ item.mahasiswa }}</td>
                                <td>{{ item.pengumuman }}</td>
                                <td>{{ item.materi }}</td>
                                <td>{{ item.events }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </section>

            <div class="bottom-grid">
                <section class="card">
                    <h2>Pengumuman Terbaru</h2>

                    <div v-for="item in pengumumanTerbaru" :key="item.id" class="list-row">
                        <strong>{{ item.judul }}</strong>
                        <span>{{ formatDate(item.created_at) }}</span>
                    </div>

                    <p v-if="pengumumanTerbaru.length === 0" class="empty">
                        Belum ada pengumuman.
                    </p>
                </section>

                <section class="card">
                    <h2>Event Mendatang</h2>

                    <div v-for="item in eventsMendatang" :key="item.id" class="list-row">
                        <strong>{{ item.nama_event }}</strong>
                        <span>{{ formatDate(item.tanggal) }} · {{ item.lokasi || '-' }}</span>
                    </div>

                    <p v-if="eventsMendatang.length === 0" class="empty">
                        Belum ada event mendatang.
                    </p>
                </section>
            </div>
        </div>
    </AdminLayout>
</template>

<style scoped>
.lp-page {
    display: flex;
    flex-direction: column;
    gap: 19px;
}

.eyebrow {
    margin: 0;
    color: #0f9488;
    font-size: 11px;
    font-weight: 800;
    letter-spacing: .1em;
}

header h1 {
    margin: 5px 0 7px;
    color: #0f172a;
    font-size: 30px;
}

.subtitle {
    margin: 0;
    color: #64748b;
}

.stats {
    display: grid;
    grid-template-columns: repeat(6, minmax(0, 1fr));
    gap: 12px;
}

.stat,
.card {
    padding: 17px;
    border: 1px solid #e2e8f0;
    border-radius: 15px;
    background: white;
}

.stat span {
    display: block;
    color: #64748b;
    font-size: 12px;
}

.stat strong {
    display: block;
    margin-top: 8px;
    color: #0f172a;
    font-size: 28px;
}

.card h2 {
    margin: 0 0 15px;
    color: #0f172a;
    font-size: 17px;
}

.table-wrap {
    overflow-x: auto;
}

table {
    width: 100%;
    border-collapse: collapse;
}

th {
    padding: 11px 12px;
    background: #f8fafc;
    color: #64748b;
    font-size: 11px;
    text-align: left;
}

td {
    padding: 12px;
    border-bottom: 1px solid #eef2f7;
    color: #334155;
    font-size: 13px;
}

td strong,
td small {
    display: block;
}

td small {
    margin-top: 3px;
    color: #64748b;
}

.badge {
    padding: 5px 9px;
    border-radius: 999px;
    background: #ecfdf5;
    color: #047857;
    font-size: 11px;
    font-weight: 700;
}

.badge.off {
    background: #f1f5f9;
    color: #64748b;
}

.bottom-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 16px;
}

.list-row {
    padding: 11px 0;
    border-top: 1px solid #eef2f7;
}

.list-row strong,
.list-row span {
    display: block;
}

.list-row span,
.empty {
    margin-top: 4px;
    color: #64748b;
    font-size: 12px;
}

@media (max-width: 1100px) {
    .stats {
        grid-template-columns: repeat(3, 1fr);
    }
}

@media (max-width: 700px) {
    .stats,
    .bottom-grid {
        grid-template-columns: 1fr;
    }
}
</style>
