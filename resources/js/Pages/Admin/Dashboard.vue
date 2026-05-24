<script setup>
import { Head, Link } from '@inertiajs/vue3'
import { computed, ref } from 'vue'
import AdminLayout from '@/Layouts/AdminLayout.vue'

const props = defineProps({
    stats: {
        type: Object,
        default: () => ({
            mahasiswa: 0,
            pengumuman: 0,
            materi: 0,
            events: 0,
        }),
    },

    aktivitas: {
        type: Array,
        default: () => [],
    },

    eventsTerbaru: {
        type: Array,
        default: () => [],
    },

    trendBulanan: {
        type: Array,
        default: () => [],
    },
})

const showAllActivities = ref(false)

const visibleActivities = computed(() => {
    return showAllActivities.value
        ? props.aktivitas
        : props.aktivitas.slice(0, 6)
})

const totalKonten = computed(() => {
    return Number(props.stats.pengumuman || 0)
        + Number(props.stats.materi || 0)
        + Number(props.stats.events || 0)
})

const distributionStyle = computed(() => {
    if (totalKonten.value === 0) {
        return {
            background: '#e2e8f0',
        }
    }

    const pengumumanAngle = (Number(props.stats.pengumuman || 0) / totalKonten.value) * 360
    const materiAngle = (Number(props.stats.materi || 0) / totalKonten.value) * 360
    const materiEnd = pengumumanAngle + materiAngle

    return {
        background: `conic-gradient(
            #3b82f6 0deg ${pengumumanAngle}deg,
            #f97316 ${pengumumanAngle}deg ${materiEnd}deg,
            #8b5cf6 ${materiEnd}deg 360deg
        )`,
    }
})

const maxTrendValue = computed(() => {
    const values = props.trendBulanan.flatMap((item) => [
        Number(item.pengumuman || 0),
        Number(item.materi || 0),
        Number(item.events || 0),
    ])

    return Math.max(...values, 1)
})

function toggleActivities() {
    showAllActivities.value = !showAllActivities.value
}

function barHeight(value) {
    const amount = Number(value || 0)

    if (amount === 0) {
        return '0%'
    }

    return `${Math.max((amount / maxTrendValue.value) * 100, 8)}%`
}

function formatBulan(tanggal) {
    if (!tanggal) {
        return '-'
    }

    return new Intl.DateTimeFormat('id-ID', {
        month: 'short',
    }).format(new Date(tanggal))
}

function formatTgl(tanggal) {
    if (!tanggal) {
        return '-'
    }

    return new Intl.DateTimeFormat('id-ID', {
        day: 'numeric',
    }).format(new Date(tanggal))
}

function activityBadge(type) {
    const badges = {
        mahasiswa: 'Mahasiswa',
        pengumuman: 'Pengumuman',
        materi: 'Materi',
        event: 'Event',
    }

    return badges[type] || 'Aktivitas'
}
</script>

<template>
    <Head title="Dashboard Admin" />

    <AdminLayout>
        <div class="dashboard-page">
            <!-- HEADER -->
            <div class="admin-greeting-row">
                <div>
                    <h1 class="admin-greeting">Selamat Datang, Admin 👋</h1>

                    <p class="admin-greeting-sub">
                        Berikut adalah ringkasan aktivitas akademik CampusHub hari ini.
                    </p>
                </div>

                <div class="admin-greeting-actions">
                    <Link href="/admin/laporan" class="admin-action-btn outline">
                        <svg
                            width="15"
                            height="15"
                            viewBox="0 0 24 24"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="2"
                        >
                            <line x1="18" y1="20" x2="18" y2="10" />
                            <line x1="12" y1="20" x2="12" y2="4" />
                            <line x1="6" y1="20" x2="6" y2="14" />
                        </svg>
                        Lihat Laporan
                    </Link>

                    <Link href="/admin/akademik" class="admin-action-btn teal">
                        <svg
                            width="15"
                            height="15"
                            viewBox="0 0 24 24"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="2"
                        >
                            <path d="M22 10v6M2 10l10-5 10 5-10 5z" />
                            <path d="M6 12v5c3 3 9 3 12 0v-5" />
                        </svg>
                        Kelola Akademik
                    </Link>
                </div>
            </div>

            <!-- STAT CARDS -->
            <div class="admin-stats">
                <div class="admin-stat teal">
                    <div class="admin-stat-icon">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2" />
                            <circle cx="9" cy="7" r="4" />
                            <path d="M23 21v-2a4 4 0 0 0-3-3.87" />
                            <path d="M16 3.13a4 4 0 0 1 0 7.75" />
                        </svg>
                    </div>

                    <p class="admin-stat-label">Mahasiswa Aktif</p>
                    <p class="admin-stat-num">{{ stats.mahasiswa ?? 0 }}</p>

                    <span class="admin-stat-change">
                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                            <polyline points="18 15 12 9 6 15" />
                        </svg>
                        Terdaftar
                    </span>
                </div>

                <div class="admin-stat blue">
                    <div class="admin-stat-icon">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z" />
                            <polyline points="14 2 14 8 20 8" />
                        </svg>
                    </div>

                    <p class="admin-stat-label">Pengumuman</p>
                    <p class="admin-stat-num">{{ stats.pengumuman ?? 0 }}</p>

                    <span class="admin-stat-change">
                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                            <polyline points="18 15 12 9 6 15" />
                        </svg>
                        Terpublikasi
                    </span>
                </div>

                <div class="admin-stat orange">
                    <div class="admin-stat-icon">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z" />
                            <path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z" />
                        </svg>
                    </div>

                    <p class="admin-stat-label">Materi</p>
                    <p class="admin-stat-num">{{ stats.materi ?? 0 }}</p>

                    <span class="admin-stat-change">
                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                            <polyline points="18 15 12 9 6 15" />
                        </svg>
                        Terupload
                    </span>
                </div>

                <div class="admin-stat purple">
                    <div class="admin-stat-icon">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <rect x="3" y="4" width="18" height="18" rx="2" ry="2" />
                            <line x1="16" y1="2" x2="16" y2="6" />
                            <line x1="8" y1="2" x2="8" y2="6" />
                            <line x1="3" y1="10" x2="21" y2="10" />
                        </svg>
                    </div>

                    <p class="admin-stat-label">Event Mendatang</p>
                    <p class="admin-stat-num">{{ stats.events ?? 0 }}</p>

                    <span class="admin-stat-change">
                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                            <polyline points="18 15 12 9 6 15" />
                        </svg>
                        Terjadwal
                    </span>
                </div>
            </div>

            <!-- CONTENT ROW -->
            <div class="admin-mid-grid dashboard-content-grid">
                <!-- AKTIVITAS -->
                <section class="admin-card activity-card">
                    <div class="admin-card-head activity-card-head">
                        <h2>Aktivitas Terkini</h2>

                        <button type="button" class="see-all-button" @click="toggleActivities">
                            {{ showAllActivities ? 'Tampilkan Ringkas' : 'Lihat Semua' }}
                        </button>
                    </div>

                    <div class="activity-scroll">
                        <p v-if="visibleActivities.length === 0" class="admin-empty">
                            Belum ada aktivitas terbaru.
                        </p>

                        <div
                            v-for="item in visibleActivities"
                            :key="item.id"
                            class="admin-activity-item"
                        >
                            <div
                                class="admin-activity-icon"
                                :class="{
                                    green: item.type === 'mahasiswa',
                                    blue: item.type === 'pengumuman',
                                    orange: item.type === 'materi',
                                    purple: item.type === 'event',
                                }"
                            >
                                <svg
                                    v-if="item.type === 'mahasiswa'"
                                    width="16"
                                    height="16"
                                    viewBox="0 0 24 24"
                                    fill="none"
                                    stroke="currentColor"
                                    stroke-width="2"
                                >
                                    <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2" />
                                    <circle cx="12" cy="7" r="4" />
                                </svg>

                                <svg
                                    v-else-if="item.type === 'pengumuman'"
                                    width="16"
                                    height="16"
                                    viewBox="0 0 24 24"
                                    fill="none"
                                    stroke="currentColor"
                                    stroke-width="2"
                                >
                                    <path d="M4 4h16v13H7l-3 3V4z" />
                                    <path d="M8 9h8" />
                                    <path d="M8 12h5" />
                                </svg>

                                <svg
                                    v-else-if="item.type === 'materi'"
                                    width="16"
                                    height="16"
                                    viewBox="0 0 24 24"
                                    fill="none"
                                    stroke="currentColor"
                                    stroke-width="2"
                                >
                                    <path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z" />
                                    <path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z" />
                                </svg>

                                <svg
                                    v-else
                                    width="16"
                                    height="16"
                                    viewBox="0 0 24 24"
                                    fill="none"
                                    stroke="currentColor"
                                    stroke-width="2"
                                >
                                    <rect x="3" y="4" width="18" height="18" rx="2" />
                                    <line x1="16" y1="2" x2="16" y2="6" />
                                    <line x1="8" y1="2" x2="8" y2="6" />
                                    <line x1="3" y1="10" x2="21" y2="10" />
                                </svg>
                            </div>

                            <div class="admin-activity-body">
                                <p class="admin-activity-text">
                                    {{ item.title }}
                                </p>

                                <p class="admin-activity-time">
                                    {{ item.subtitle }}
                                </p>
                            </div>

                            <span
                                class="admin-activity-badge"
                                :class="{
                                    teal: item.type === 'mahasiswa',
                                    blue: item.type === 'pengumuman',
                                    orange: item.type === 'materi',
                                    purple: item.type === 'event',
                                }"
                            >
                                {{ activityBadge(item.type) }}
                            </span>
                        </div>
                    </div>
                </section>

                <!-- RIGHT COLUMN -->
                <div class="right-column">
                    <!-- EVENT -->
                    <section class="admin-card event-card">
                        <div class="admin-card-head">
                            <h2>Event Mendatang</h2>

                            <Link href="/admin/akademik" class="admin-see-all">
                                Kelola
                            </Link>
                        </div>

                        <p v-if="eventsTerbaru.length === 0" class="admin-empty">
                            Belum ada event mendatang.
                        </p>

                        <div
                            v-for="event in eventsTerbaru.slice(0, 3)"
                            :key="event.id"
                            class="admin-event-item"
                        >
                            <div class="admin-event-date">
                                <span class="admin-event-bulan">
                                    {{ formatBulan(event.tanggal) }}
                                </span>

                                <span class="admin-event-tgl">
                                    {{ formatTgl(event.tanggal) }}
                                </span>
                            </div>

                            <div class="admin-event-body">
                                <strong>{{ event.nama_event }}</strong>

                                <p>{{ event.lokasi || 'Lokasi belum ditentukan' }}</p>
                            </div>
                        </div>
                    </section>

                    <!-- DISTRIBUSI KONTEN -->
                    <section class="admin-card distribution-card">
                        <div class="admin-card-head">
                            <h2>Distribusi Konten</h2>
                        </div>

                        <div class="distribution-content">
                            <div class="donut" :style="distributionStyle">
                                <div class="donut-center">
                                    <strong>{{ totalKonten }}</strong>
                                    <span>Total</span>
                                </div>
                            </div>

                            <div class="distribution-legend">
                                <div class="legend-row">
                                    <span class="legend-dot blue"></span>
                                    <span>Pengumuman</span>
                                    <strong>{{ stats.pengumuman ?? 0 }}</strong>
                                </div>

                                <div class="legend-row">
                                    <span class="legend-dot orange"></span>
                                    <span>Materi</span>
                                    <strong>{{ stats.materi ?? 0 }}</strong>
                                </div>

                                <div class="legend-row">
                                    <span class="legend-dot purple"></span>
                                    <span>Event</span>
                                    <strong>{{ stats.events ?? 0 }}</strong>
                                </div>
                            </div>
                        </div>
                    </section>
                </div>
            </div>

            <!-- MONTHLY CHART -->
            <section class="admin-card monthly-chart-card">
                <div class="monthly-header">
                    <div>
                        <h2>Publikasi Konten 6 Bulan Terakhir</h2>
                        <p>Perbandingan jumlah pengumuman, materi, dan event yang ditambahkan.</p>
                    </div>

                    <div class="monthly-legend">
                        <span><i class="blue"></i>Pengumuman</span>
                        <span><i class="orange"></i>Materi</span>
                        <span><i class="purple"></i>Event</span>
                    </div>
                </div>

                <div class="bar-chart">
                    <div
                        v-for="item in trendBulanan"
                        :key="item.label"
                        class="month-column"
                    >
                        <div class="bars">
                            <div
                                class="bar blue"
                                :style="{ height: barHeight(item.pengumuman) }"
                                :title="`${item.pengumuman} pengumuman`"
                            >
                                <span v-if="item.pengumuman">{{ item.pengumuman }}</span>
                            </div>

                            <div
                                class="bar orange"
                                :style="{ height: barHeight(item.materi) }"
                                :title="`${item.materi} materi`"
                            >
                                <span v-if="item.materi">{{ item.materi }}</span>
                            </div>

                            <div
                                class="bar purple"
                                :style="{ height: barHeight(item.events) }"
                                :title="`${item.events} event`"
                            >
                                <span v-if="item.events">{{ item.events }}</span>
                            </div>
                        </div>

                        <p class="month-label">{{ item.label }}</p>
                    </div>
                </div>
            </section>
        </div>
    </AdminLayout>
</template>

<style scoped>
.dashboard-page {
    display: flex;
    flex-direction: column;
    gap: 20px;
}

.dashboard-content-grid {
    align-items: stretch;
}

.activity-card {
    display: flex;
    flex-direction: column;
    height: 432px;
    overflow: hidden;
}

.activity-card-head {
    flex-shrink: 0;
}

.see-all-button {
    padding: 0;
    border: none;
    background: transparent;
    color: #0f9488;
    cursor: pointer;
    font-size: 12px;
    font-weight: 700;
}

.see-all-button:hover {
    text-decoration: underline;
}

.activity-scroll {
    flex: 1;
    min-height: 0;
    overflow-y: auto;
    padding-right: 7px;
}

.activity-scroll::-webkit-scrollbar {
    width: 6px;
}

.activity-scroll::-webkit-scrollbar-track {
    background: transparent;
}

.activity-scroll::-webkit-scrollbar-thumb {
    border-radius: 999px;
    background: #cbd5e1;
}

.right-column {
    display: flex;
    flex-direction: column;
    gap: 16px;
}

.event-card {
    min-height: 184px;
}

.distribution-card {
    flex: 1;
}

.distribution-content {
    display: flex;
    align-items: center;
    gap: 30px;
    padding-top: 5px;
}

.donut {
    display: grid;
    place-items: center;
    flex-shrink: 0;
    width: 118px;
    height: 118px;
    border-radius: 50%;
}

.donut-center {
    display: grid;
    place-items: center;
    width: 72px;
    height: 72px;
    border-radius: 50%;
    background: #ffffff;
}

.donut-center strong {
    color: #0f172a;
    font-size: 22px;
    line-height: 1;
}

.donut-center span {
    color: #64748b;
    font-size: 11px;
}

.distribution-legend {
    display: flex;
    flex: 1;
    flex-direction: column;
    gap: 13px;
}

.legend-row {
    display: grid;
    grid-template-columns: 11px 1fr auto;
    align-items: center;
    gap: 9px;
    color: #64748b;
    font-size: 12px;
}

.legend-row strong {
    color: #0f172a;
    font-size: 13px;
}

.legend-dot {
    width: 10px;
    height: 10px;
    border-radius: 50%;
}

.legend-dot.blue {
    background: #3b82f6;
}

.legend-dot.orange {
    background: #f97316;
}

.legend-dot.purple {
    background: #8b5cf6;
}

.monthly-chart-card {
    padding-bottom: 18px;
}

.monthly-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    gap: 18px;
    margin-bottom: 18px;
}

.monthly-header h2 {
    margin: 0 0 5px;
    color: #0f172a;
    font-size: 17px;
}

.monthly-header p {
    margin: 0;
    color: #64748b;
    font-size: 12px;
}

.monthly-legend {
    display: flex;
    align-items: center;
    gap: 16px;
    color: #64748b;
    font-size: 12px;
}

.monthly-legend span {
    display: inline-flex;
    align-items: center;
    gap: 6px;
}

.monthly-legend i {
    display: inline-block;
    width: 9px;
    height: 9px;
    border-radius: 3px;
}

.monthly-legend i.blue {
    background: #3b82f6;
}

.monthly-legend i.orange {
    background: #f97316;
}

.monthly-legend i.purple {
    background: #8b5cf6;
}

.bar-chart {
    display: grid;
    grid-template-columns: repeat(6, minmax(70px, 1fr));
    gap: 22px;
    height: 215px;
    padding: 10px 26px 0;
    border-bottom: 1px solid #e2e8f0;
}

.month-column {
    display: flex;
    flex-direction: column;
    justify-content: flex-end;
    gap: 9px;
}

.bars {
    display: flex;
    align-items: flex-end;
    justify-content: center;
    gap: 7px;
    height: 170px;
}

.bar {
    position: relative;
    width: 22px;
    min-height: 0;
    border-radius: 7px 7px 3px 3px;
    transition: height 0.25s ease;
}

.bar span {
    position: absolute;
    top: -19px;
    left: 50%;
    transform: translateX(-50%);
    color: #64748b;
    font-size: 10px;
    font-weight: 700;
}

.bar.blue {
    background: #3b82f6;
}

.bar.orange {
    background: #f97316;
}

.bar.purple {
    background: #8b5cf6;
}

.month-label {
    margin: 0 0 -25px;
    color: #64748b;
    font-size: 12px;
    font-weight: 600;
    text-align: center;
}

.admin-activity-icon.orange {
    background: #ffedd5;
    color: #ea580c;
}

.admin-activity-icon.purple {
    background: #ede9fe;
    color: #7c3aed;
}

.admin-activity-badge.orange {
    background: #ffedd5;
    color: #ea580c;
}

.admin-activity-badge.purple {
    background: #ede9fe;
    color: #7c3aed;
}

@media (max-width: 1100px) {
    .distribution-content {
        flex-direction: column;
        align-items: flex-start;
    }

    .bar-chart {
        overflow-x: auto;
    }
}

@media (max-width: 760px) {
    .monthly-header {
        flex-direction: column;
    }

    .bar-chart {
        grid-template-columns: repeat(6, 78px);
    }
}
</style>
