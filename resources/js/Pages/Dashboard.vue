<script setup>
import { Head, Link } from '@inertiajs/vue3'
import { computed } from 'vue'
import AppLayout from '@/Layouts/AppLayout.vue'

const props = defineProps({
    stats: {
        type: Object,
        default: () => ({
            pengumuman: 0,
            materi: 0,
            events: 0,
        }),
    },

    profile: {
        type: Object,
        default: null,
    },

    pengumuman: {
        type: Array,
        default: () => [],
    },

    materi: {
        type: Array,
        default: () => [],
    },

    events: {
        type: Array,
        default: () => [],
    },
})

const firstName = computed(() => {
    return props.profile?.nama
        ? props.profile.nama.trim().split(/\s+/)[0]
        : 'Mahasiswa'
})

const greetingText = computed(() => {
    const hour = new Date().getHours()

    if (hour < 11) {
        return 'Selamat Pagi'
    }

    if (hour < 15) {
        return 'Selamat Siang'
    }

    if (hour < 18) {
        return 'Selamat Sore'
    }

    return 'Selamat Malam'
})

function targetLabel(item) {
    return item.target_jurusan || 'Semua Jurusan'
}

function formatDate(value) {
    if (!value) {
        return '-'
    }

    return new Intl.DateTimeFormat('id-ID', {
        day: 'numeric',
        month: 'long',
        year: 'numeric',
    }).format(new Date(value))
}

function eventMonth(value) {
    if (!value) {
        return '-'
    }

    return new Intl.DateTimeFormat('id-ID', {
        month: 'short',
    }).format(new Date(value)).toUpperCase()
}

function eventDay(value) {
    if (!value) {
        return '-'
    }

    return new Intl.DateTimeFormat('id-ID', {
        day: 'numeric',
    }).format(new Date(value))
}

function announcementExcerpt(value) {
    if (!value) {
        return 'Informasi pengumuman tersedia.'
    }

    return value.length > 115
        ? `${value.slice(0, 115)}...`
        : value
}
</script>

<template>
    <Head title="Dashboard Mahasiswa" />

    <AppLayout>
        <div class="dashboard-page">
            <!-- GREETING -->
            <header class="dashboard-header">
                <div>
                    <h1>{{ greetingText }}, {{ firstName }} 👋</h1>

                    <p>
                        Berikut adalah ringkasan aktivitas akademik Anda hari ini.
                    </p>
                </div>

                <Link href="/profile" class="profile-button">
                    <svg viewBox="0 0 24 24" fill="none">
                        <circle cx="12" cy="8" r="4" stroke="currentColor" stroke-width="2" />
                        <path d="M4 21c0-4 3.6-7 8-7s8 3 8 7" stroke="currentColor" stroke-width="2" stroke-linecap="round" />
                    </svg>
                    Edit Profil
                </Link>
            </header>

            <!-- STATISTICS -->
            <section class="stats-grid">
                <article class="stat-card announcement">
                    <div class="stat-icon">
                        <svg viewBox="0 0 24 24" fill="none">
                            <path d="M4 5h16v13H8l-4 3V5Z" stroke="currentColor" stroke-width="2" stroke-linejoin="round" />
                            <path d="M8 10h8M8 14h5" stroke="currentColor" stroke-width="2" stroke-linecap="round" />
                        </svg>
                    </div>

                    <p>Pengumuman Baru</p>
                    <strong>{{ stats.pengumuman ?? 0 }}</strong>

                    <Link href="/pengumuman">
                        Lihat informasi →
                    </Link>
                </article>

                <article class="stat-card material">
                    <div class="stat-icon">
                        <svg viewBox="0 0 24 24" fill="none">
                            <path d="M5 3h10l4 4v14H5V3Z" stroke="currentColor" stroke-width="2" stroke-linejoin="round" />
                            <path d="M15 3v5h4M9 12h6M9 16h6" stroke="currentColor" stroke-width="2" stroke-linecap="round" />
                        </svg>
                    </div>

                    <p>Materi Tersedia</p>
                    <strong>{{ stats.materi ?? 0 }}</strong>

                    <Link href="/materi">
                        Buka materi →
                    </Link>
                </article>

                <article class="stat-card event">
                    <div class="stat-icon">
                        <svg viewBox="0 0 24 24" fill="none">
                            <rect x="3" y="5" width="18" height="16" rx="2" stroke="currentColor" stroke-width="2" />
                            <path d="M8 3v5M16 3v5M3 10h18" stroke="currentColor" stroke-width="2" stroke-linecap="round" />
                        </svg>
                    </div>

                    <p>Event Mendatang</p>
                    <strong>{{ stats.events ?? 0 }}</strong>

                    <Link href="/events">
                        Lihat agenda →
                    </Link>
                </article>

                <article class="stat-card profile">
                    <div class="stat-icon">
                        <svg viewBox="0 0 24 24" fill="none">
                            <circle cx="12" cy="8" r="4" stroke="currentColor" stroke-width="2" />
                            <path d="M4 21c0-4 3.6-7 8-7s8 3 8 7" stroke="currentColor" stroke-width="2" stroke-linecap="round" />
                        </svg>
                    </div>

                    <p>Profil Mahasiswa</p>
                    <strong>{{ profile ? 'Lengkap' : 'Belum' }}</strong>

                    <Link href="/profile">
                        Perbarui profil →
                    </Link>
                </article>
            </section>

            <!-- MAIN SECTIONS -->
            <div class="dashboard-main-grid">
                <!-- ANNOUNCEMENTS -->
                <section class="dashboard-card announcement-card">
                    <div class="card-header">
                        <h2>Pengumuman Terbaru</h2>

                        <Link href="/pengumuman">
                            Lihat Semua →
                        </Link>
                    </div>

                    <p v-if="pengumuman.length === 0" class="empty-message">
                        Belum ada pengumuman untuk jurusan Anda.
                    </p>

                    <article
                        v-for="item in pengumuman"
                        :key="item.id"
                        class="announcement-item"
                    >
                        <div class="announcement-icon">
                            <svg viewBox="0 0 24 24" fill="none">
                                <path d="M4 5h16v13H8l-4 3V5Z" stroke="currentColor" stroke-width="2" stroke-linejoin="round" />
                                <path d="M8 10h8M8 14h5" stroke="currentColor" stroke-width="2" stroke-linecap="round" />
                            </svg>
                        </div>

                        <div class="announcement-content">
                            <div class="announcement-title">
                                <strong>{{ item.judul }}</strong>

                                <span
                                    class="badge"
                                    :class="{ urgent: item.kategori === 'Urgent' }"
                                >
                                    {{ item.kategori || 'Info' }}
                                </span>
                            </div>

                            <p>
                                {{ announcementExcerpt(item.isi) }}
                            </p>

                            <small>
                                {{ formatDate(item.created_at) }} · {{ targetLabel(item) }}
                            </small>
                        </div>
                    </article>
                </section>

                <!-- EVENTS -->
                <section class="dashboard-card right-card">
                    <div class="card-header">
                        <h2>Event Mendatang</h2>

                        <Link href="/events">
                            Semua →
                        </Link>
                    </div>

                    <p v-if="events.length === 0" class="empty-message">
                        Belum ada event mendatang.
                    </p>

                    <article
                        v-for="item in events"
                        :key="item.id"
                        class="event-item"
                    >
                        <div class="event-date">
                            <span>{{ eventMonth(item.tanggal) }}</span>
                            <strong>{{ eventDay(item.tanggal) }}</strong>
                        </div>

                        <div class="event-content">
                            <strong>{{ item.nama_event }}</strong>
                            <p>{{ item.lokasi || 'Lokasi belum ditentukan' }}</p>
                            <small>{{ targetLabel(item) }}</small>
                        </div>
                    </article>

                    <Link href="/events" class="calendar-link">
                        Lihat Kalender Akademik →
                    </Link>

                    <div class="help-card">
                        <div class="help-icon">
                            <svg viewBox="0 0 24 24" fill="none">
                                <path
                                    d="M4 4h16a2 2 0 0 1 2 2v11a2 2 0 0 1-2 2H9l-5 3v-3a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2Z"
                                    stroke="currentColor"
                                    stroke-width="2"
                                    stroke-linejoin="round"
                                />
                                <path d="M8 10h8M8 14h5" stroke="currentColor" stroke-width="2" stroke-linecap="round" />
                            </svg>
                        </div>

                        <div>
                            <h3>Butuh Bantuan?</h3>

                            <p>
                                Tanyakan informasi akademik atau analisis dokumen melalui chatbot.
                            </p>

                            <Link href="/chat">
                                Buka Chatbot →
                            </Link>
                        </div>
                    </div>
                </section>
            </div>

            <!-- MATERIALS -->
            <section class="dashboard-card material-section">
                <div class="card-header">
                    <h2>Akses Cepat Materi</h2>

                    <Link href="/materi">
                        Lihat Semua →
                    </Link>
                </div>

                <p v-if="materi.length === 0" class="empty-message">
                    Belum ada materi yang tersedia.
                </p>

                <div class="material-grid">
                    <article
                        v-for="item in materi.slice(0, 3)"
                        :key="item.id"
                        class="material-item"
                    >
                        <div class="material-preview">
                            <svg viewBox="0 0 24 24" fill="none">
                                <path d="M5 3h10l4 4v14H5V3Z" stroke="currentColor" stroke-width="2" stroke-linejoin="round" />
                                <path d="M15 3v5h4M9 12h6M9 16h6" stroke="currentColor" stroke-width="2" stroke-linecap="round" />
                            </svg>
                        </div>

                        <div class="material-info">
                            <strong>{{ item.judul }}</strong>

                            <p>
                                {{ item.mata_kuliah || 'Materi Kuliah' }}
                            </p>

                            <small>{{ targetLabel(item) }}</small>
                        </div>

                        <a
                            v-if="item.file_url"
                            :href="item.file_url"
                            target="_blank"
                            rel="noreferrer"
                            class="download-button"
                        >
                            Buka
                        </a>
                    </article>
                </div>
            </section>

            <!-- FOOTER -->
            <footer class="dashboard-footer">
                <p>© 2026 CampusHub Student Portal. All rights reserved.</p>

                <div>
                    <a href="#">Privacy Policy</a>
                    <a href="#">Terms of Service</a>
                </div>
            </footer>
        </div>
    </AppLayout>
</template>

<style scoped>
.dashboard-page {
    display: flex;
    flex-direction: column;
    gap: 20px;
}

.dashboard-header {
    display: flex;
    align-items: flex-start;
    justify-content: space-between;
    gap: 18px;
}

.dashboard-header h1 {
    margin: 0 0 7px;
    color: #0f172a;
    font-size: 29px;
    letter-spacing: -0.035em;
}

.dashboard-header p {
    margin: 0;
    color: #64748b;
    font-size: 14px;
}

.profile-button {
    display: inline-flex;
    align-items: center;
    gap: 7px;
    padding: 11px 15px;
    border-radius: 10px;
    background: #0f9488;
    color: #ffffff;
    font-size: 13px;
    font-weight: 700;
    text-decoration: none;
}

.profile-button:hover {
    background: #0f766e;
}

.profile-button svg {
    width: 16px;
    height: 16px;
}

.stats-grid {
    display: grid;
    grid-template-columns: repeat(4, minmax(0, 1fr));
    gap: 13px;
}

.stat-card {
    padding: 17px;
    border-radius: 16px;
}

.stat-card.announcement {
    background: #dbeafe;
    color: #2563eb;
}

.stat-card.material {
    background: #d1fae5;
    color: #059669;
}

.stat-card.event {
    background: #ffedd5;
    color: #ea580c;
}

.stat-card.profile {
    background: #ede9fe;
    color: #7c3aed;
}

.stat-icon {
    display: grid;
    place-items: center;
    width: 39px;
    height: 39px;
    margin-bottom: 13px;
    border-radius: 10px;
    background: rgba(255, 255, 255, 0.48);
}

.stat-icon svg {
    width: 21px;
    height: 21px;
}

.stat-card p {
    margin: 0 0 6px;
    color: #475569;
    font-size: 12px;
}

.stat-card strong {
    display: block;
    margin-bottom: 11px;
    font-size: 30px;
    line-height: 1;
}

.stat-card.profile strong {
    font-size: 22px;
}

.stat-card a {
    color: inherit;
    font-size: 12px;
    font-weight: 700;
    text-decoration: none;
}

.dashboard-main-grid {
    display: grid;
    grid-template-columns: minmax(0, 1.65fr) minmax(300px, 1fr);
    gap: 16px;
}

.dashboard-card {
    padding: 19px;
    border: 1px solid #e2e8f0;
    border-radius: 17px;
    background: #ffffff;
}

.card-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 13px;
    margin-bottom: 16px;
}

.card-header h2 {
    margin: 0;
    color: #0f172a;
    font-size: 17px;
}

.card-header a {
    color: #0f9488;
    font-size: 12px;
    font-weight: 700;
    text-decoration: none;
}

.empty-message {
    margin: 15px 0;
    color: #64748b;
    font-size: 13px;
}

.announcement-item {
    display: flex;
    gap: 12px;
    padding: 13px 0;
    border-bottom: 1px solid #eef2f7;
}

.announcement-item:last-child {
    border-bottom: none;
}

.announcement-icon {
    display: grid;
    place-items: center;
    flex-shrink: 0;
    width: 39px;
    height: 39px;
    border-radius: 10px;
    background: #dbeafe;
    color: #2563eb;
}

.announcement-icon svg {
    width: 18px;
    height: 18px;
}

.announcement-content {
    flex: 1;
    min-width: 0;
}

.announcement-title {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 9px;
    margin-bottom: 5px;
}

.announcement-title strong {
    overflow: hidden;
    color: #0f172a;
    font-size: 13px;
    text-overflow: ellipsis;
    white-space: nowrap;
}

.badge {
    flex-shrink: 0;
    padding: 4px 8px;
    border-radius: 999px;
    background: #dbeafe;
    color: #2563eb;
    font-size: 10px;
    font-weight: 700;
}

.badge.urgent {
    background: #fee2e2;
    color: #dc2626;
}

.announcement-content p {
    margin: 0 0 6px;
    color: #64748b;
    font-size: 12px;
    line-height: 1.55;
}

.announcement-content small {
    color: #94a3b8;
    font-size: 11px;
}

.event-item {
    display: flex;
    gap: 12px;
    padding: 11px 0;
    border-bottom: 1px solid #eef2f7;
}

.event-date {
    display: grid;
    place-items: center;
    flex-shrink: 0;
    width: 49px;
    height: 52px;
    border-radius: 10px;
    background: #ecfdf5;
    color: #0f766e;
}

.event-date span {
    font-size: 10px;
    font-weight: 700;
}

.event-date strong {
    font-size: 19px;
    line-height: 1;
}

.event-content strong {
    color: #0f172a;
    font-size: 13px;
}

.event-content p {
    margin: 4px 0;
    color: #64748b;
    font-size: 12px;
}

.event-content small {
    color: #94a3b8;
    font-size: 11px;
}

.calendar-link {
    display: block;
    margin: 15px 0 17px;
    color: #0f9488;
    font-size: 12px;
    font-weight: 700;
    text-decoration: none;
}

.help-card {
    display: flex;
    gap: 12px;
    padding: 14px;
    border: 1px solid #ccfbf1;
    border-radius: 13px;
    background: #f0fdfa;
}

.help-icon {
    display: grid;
    place-items: center;
    flex-shrink: 0;
    width: 39px;
    height: 39px;
    border-radius: 10px;
    background: #ccfbf1;
    color: #0f766e;
}

.help-icon svg {
    width: 19px;
    height: 19px;
}

.help-card h3 {
    margin: 0 0 5px;
    color: #0f172a;
    font-size: 14px;
}

.help-card p {
    margin: 0 0 8px;
    color: #64748b;
    font-size: 11px;
    line-height: 1.55;
}

.help-card a {
    color: #0f766e;
    font-size: 12px;
    font-weight: 700;
    text-decoration: none;
}

.material-grid {
    display: grid;
    grid-template-columns: repeat(3, minmax(0, 1fr));
    gap: 12px;
}

.material-item {
    position: relative;
    display: flex;
    align-items: center;
    gap: 11px;
    padding: 12px;
    border: 1px solid #eef2f7;
    border-radius: 13px;
}

.material-preview {
    display: grid;
    place-items: center;
    flex-shrink: 0;
    width: 44px;
    height: 44px;
    border-radius: 11px;
    background: #ecfdf5;
    color: #0f9488;
}

.material-preview svg {
    width: 23px;
    height: 23px;
}

.material-info {
    flex: 1;
    min-width: 0;
}

.material-info strong {
    display: block;
    overflow: hidden;
    margin-bottom: 4px;
    color: #0f172a;
    font-size: 12px;
    text-overflow: ellipsis;
    white-space: nowrap;
}

.material-info p,
.material-info small {
    display: block;
    margin: 0;
    color: #64748b;
    font-size: 11px;
}

.download-button {
    padding: 7px 9px;
    border-radius: 8px;
    background: #f0fdfa;
    color: #0f766e;
    font-size: 11px;
    font-weight: 700;
    text-decoration: none;
}

.dashboard-footer {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 15px;
    margin-top: 4px;
    padding: 15px 0 2px;
    color: #94a3b8;
    font-size: 11px;
}

.dashboard-footer p {
    margin: 0;
}

.dashboard-footer div {
    display: flex;
    gap: 16px;
}

.dashboard-footer a {
    color: #94a3b8;
    text-decoration: none;
}

@media (max-width: 1100px) {
    .stats-grid {
        grid-template-columns: repeat(2, minmax(0, 1fr));
    }

    .dashboard-main-grid {
        grid-template-columns: 1fr;
    }

    .material-grid {
        grid-template-columns: repeat(2, minmax(0, 1fr));
    }
}

@media (max-width: 620px) {
    .dashboard-header {
        flex-direction: column;
    }

    .dashboard-header h1 {
        font-size: 25px;
    }

    .profile-button {
        justify-content: center;
        width: 100%;
    }

    .stats-grid,
    .material-grid {
        grid-template-columns: 1fr;
    }

    .dashboard-card {
        padding: 15px;
    }

    .announcement-title {
        align-items: flex-start;
        flex-direction: column;
    }

    .dashboard-footer {
        align-items: flex-start;
        flex-direction: column;
    }
}
</style>
