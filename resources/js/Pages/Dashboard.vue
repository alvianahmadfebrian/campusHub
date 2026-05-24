<script setup>
import { Head, Link, router, usePage } from '@inertiajs/vue3'
import { computed } from 'vue'

const page = usePage()
const user = computed(() => page.props.auth?.user || null)

const props = defineProps({
    stats: { type: Object, default: () => ({}) },
    profile: { type: Object, default: null },
    pengumuman: { type: Array, default: () => [] },
    materi: { type: Array, default: () => [] },
    events: { type: Array, default: () => [] },
})

function targetLabel(item) {
    return item.target_jurusan || 'Semua Jurusan'
}

function formatTanggal(tanggal) {
    if (!tanggal) return '-'
    return new Intl.DateTimeFormat('id-ID', {
        day: 'numeric', month: 'long', year: 'numeric',
    }).format(new Date(tanggal))
}

function formatTanggalEvent(tanggal) {
    if (!tanggal) return { bulan: '-', tgl: '-' }
    const d = new Date(tanggal)
    return {
        bulan: d.toLocaleDateString('id-ID', { month: 'short' }).toUpperCase(),
        tgl: d.getDate(),
    }
}

function greeting() {
    const h = new Date().getHours()
    if (h < 11) return 'Selamat Pagi'
    if (h < 15) return 'Selamat Siang'
    if (h < 18) return 'Selamat Sore'
    return 'Selamat Malam'
}

function logout() {
    router.post('/logout')
}

const isActive = (path) => {
    const currentPath = page.url.split('?')[0]
    return currentPath === path || currentPath.startsWith(`${path}/`)
}
</script>

<template>
    <Head title="Dashboard Mahasiswa" />

    <div class="db-shell">
        <!-- SIDEBAR -->
        <aside class="db-sidebar">
            <div class="db-brand">
                <div class="db-brand-icon">
                    <svg width="18" height="18" viewBox="0 0 20 20" fill="none">
                        <path d="M10 2L18 6V10C18 14.418 14.418 18 10 18C5.582 18 2 14.418 2 10V6L10 2Z" fill="white" fill-opacity="0.35"/>
                        <path d="M7 10L9.5 12.5L14 8" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </div>
                <div>
                    <p class="db-brand-name">CampusHub</p>
                    <p class="db-brand-sub">Student Portal</p>
                </div>
            </div>

            <nav class="db-nav">
                <Link href="/dashboard" class="db-nav-item" :class="{ active: isActive('/dashboard') }">
                    <svg width="18" height="18" viewBox="0 0 20 20" fill="none">
                        <rect x="2" y="2" width="7" height="7" rx="1.5" stroke="currentColor" stroke-width="1.5"/>
                        <rect x="11" y="2" width="7" height="7" rx="1.5" stroke="currentColor" stroke-width="1.5"/>
                        <rect x="2" y="11" width="7" height="7" rx="1.5" stroke="currentColor" stroke-width="1.5"/>
                        <rect x="11" y="11" width="7" height="7" rx="1.5" stroke="currentColor" stroke-width="1.5"/>
                    </svg>
                    Dashboard
                </Link>
                <Link href="/profile" class="db-nav-item" :class="{ active: isActive('/profile') }">
                    <svg width="18" height="18" viewBox="0 0 20 20" fill="none">
                        <circle cx="10" cy="7" r="3.5" stroke="currentColor" stroke-width="1.5"/>
                        <path d="M3 17c0-3.314 3.134-6 7-6s7 2.686 7 6" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
                    </svg>
                    Profil Mahasiswa
                </Link>
                <Link href="/pengumuman" class="db-nav-item" :class="{ active: isActive('/pengumuman') }">
                    <svg width="18" height="18" viewBox="0 0 20 20" fill="none">
                        <path d="M4 4h12a1 1 0 011 1v8a1 1 0 01-1 1H4a1 1 0 01-1-1V5a1 1 0 011-1z" stroke="currentColor" stroke-width="1.5"/>
                        <path d="M7 8h6M7 11h4" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
                    </svg>
                    Pengumuman
                </Link>
                <Link href="/materi" class="db-nav-item" :class="{ active: isActive('/materi') }">
                    <svg width="18" height="18" viewBox="0 0 20 20" fill="none">
                        <path d="M4 3h8l4 4v10a1 1 0 01-1 1H4a1 1 0 01-1-1V4a1 1 0 011-1z" stroke="currentColor" stroke-width="1.5"/>
                        <path d="M12 3v4h4M7 10h6M7 13h4" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
                    </svg>
                    Materi Kuliah
                </Link>
                <Link href="/events" class="db-nav-item" :class="{ active: isActive('/events') }">
                    <svg width="18" height="18" viewBox="0 0 20 20" fill="none">
                        <rect x="2" y="4" width="16" height="14" rx="2" stroke="currentColor" stroke-width="1.5"/>
                        <path d="M6 2v4M14 2v4M2 9h16" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
                    </svg>
                    Event Kampus
                </Link>
                <Link href="/drive" class="db-nav-item" :class="{ active: isActive('/drive') }">
                    <svg width="18" height="18" viewBox="0 0 20 20" fill="none">
                        <path d="M2.5 6a2 2 0 012-2h4l2 2H16a2 2 0 012 2v7.5a2 2 0 01-2 2H4.5a2 2 0 01-2-2V6z" stroke="currentColor" stroke-width="1.5" stroke-linejoin="round"/>
                    </svg>
                    Drive
                </Link>
            </nav>

            <div class="db-sidebar-footer">
                <a href="#" class="db-footer-link">
                    <svg width="16" height="16" viewBox="0 0 20 20" fill="none">
                        <circle cx="10" cy="10" r="8" stroke="currentColor" stroke-width="1.5"/>
                        <path d="M10 9v5M10 7h.01" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
                    </svg>
                    Help Center
                </a>
                <button @click="logout" class="db-footer-link logout-btn">
                    <svg width="16" height="16" viewBox="0 0 20 20" fill="none">
                        <path d="M13 15l4-5-4-5M17 10H7" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                        <path d="M7 3H4a1 1 0 00-1 1v12a1 1 0 001 1h3" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
                    </svg>
                    Logout
                </button>
            </div>
        </aside>

        <!-- MAIN CONTENT -->
        <div class="db-main">
            <!-- TOP BAR -->
            <header class="db-topbar">
                <div class="db-search">
                    <svg width="16" height="16" viewBox="0 0 20 20" fill="none">
                        <circle cx="9" cy="9" r="6" stroke="currentColor" stroke-width="1.5"/>
                        <path d="M13.5 13.5L17 17" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
                    </svg>
                    <input type="text" placeholder="Cari" />
                </div>
                <div class="db-topbar-right">
                    <button class="db-icon-btn">
                        <svg width="18" height="18" viewBox="0 0 20 20" fill="none">
                            <path d="M10 2a6 6 0 016 6c0 3.5 1 5 1 5H3s1-1.5 1-5a6 6 0 016-6z" stroke="currentColor" stroke-width="1.5"/>
                            <path d="M8.5 16.5a1.5 1.5 0 003 0" stroke="currentColor" stroke-width="1.5"/>
                        </svg>
                    </button>
                    <button class="db-icon-btn">
                        <svg width="18" height="18" viewBox="0 0 20 20" fill="none">
                            <circle cx="10" cy="10" r="2" stroke="currentColor" stroke-width="1.5"/>
                            <path d="M10 2v2M10 16v2M2 10h2M16 10h2M4.22 4.22l1.42 1.42M14.36 14.36l1.42 1.42M4.22 15.78l1.42-1.42M14.36 5.64l1.42-1.42" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
                        </svg>
                    </button>
                    <div class="db-user-pill">
                        <div class="db-user-avatar">
                            <img v-if="profile?.avatar_url" :src="profile.avatar_url" :alt="profile.nama" />
                            <span v-else>{{ profile?.nama?.charAt(0)?.toUpperCase() || 'M' }}</span>
                        </div>
                        <div class="db-user-info">
                            <p class="db-user-name">{{ profile?.nama || user?.email || 'Mahasiswa' }}</p>
                            <p class="db-user-nim">{{ profile?.nim || '' }}</p>
                        </div>
                    </div>
                </div>
            </header>

            <!-- PAGE BODY -->
            <div class="db-body">
                <!-- Flash message -->
                <div v-if="$page.props.flash?.success" class="db-flash">
                    {{ $page.props.flash.success }}
                </div>

                <!-- Greeting -->
                <div class="db-greeting-row">
                    <div>
                        <h1 class="db-greeting">{{ greeting() }}, {{ profile?.nama?.split(' ')[0] || 'Mahasiswa' }}</h1>
                        <p class="db-greeting-sub">Berikut adalah ringkasan aktivitas akademik Anda hari ini.</p>
                    </div>
                    <div class="db-greeting-actions">
                        <Link href="/profile" class="db-action-btn teal">
                            <svg width="15" height="15" viewBox="0 0 20 20" fill="none">
                                <path d="M10 2a6 6 0 016 6c0 3.5 1 5 1 5H3s1-1.5 1-5a6 6 0 016-6z" stroke="currentColor" stroke-width="1.5"/>
                            </svg>
                            Edit Profil
                        </Link>
                    </div>
                </div>

                <!-- Stat Cards -->
                <div class="db-stats">
                    <article class="db-stat teal">
                        <div class="db-stat-icon">
                            <svg width="22" height="22" viewBox="0 0 20 20" fill="none">
                                <path d="M4 4h12a1 1 0 011 1v8a1 1 0 01-1 1H4a1 1 0 01-1-1V5a1 1 0 011-1z" stroke="currentColor" stroke-width="1.5"/>
                                <path d="M7 8h6M7 11h4" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
                            </svg>
                        </div>
                        <p class="db-stat-label">Pengumuman Baru</p>
                        <h2 class="db-stat-num">{{ stats.pengumuman ?? 0 }}</h2>
                        <Link href="/pengumuman" class="db-stat-link">Lihat informasi →</Link>
                    </article>

                    <article class="db-stat green">
                        <div class="db-stat-icon">
                            <svg width="22" height="22" viewBox="0 0 20 20" fill="none">
                                <path d="M4 3h8l4 4v10a1 1 0 01-1 1H4a1 1 0 01-1-1V4a1 1 0 011-1z" stroke="currentColor" stroke-width="1.5"/>
                                <path d="M12 3v4h4" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
                            </svg>
                        </div>
                        <p class="db-stat-label">Materi Minggu Ini</p>
                        <h2 class="db-stat-num">{{ stats.materi ?? 0 }}</h2>
                        <Link href="/materi" class="db-stat-link">Buka materi →</Link>
                    </article>

                    <article class="db-stat orange">
                        <div class="db-stat-icon">
                            <svg width="22" height="22" viewBox="0 0 20 20" fill="none">
                                <rect x="2" y="4" width="16" height="14" rx="2" stroke="currentColor" stroke-width="1.5"/>
                                <path d="M6 2v4M14 2v4M2 9h16" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
                            </svg>
                        </div>
                        <p class="db-stat-label">Event Terdaftar</p>
                        <h2 class="db-stat-num">{{ stats.events ?? 0 }}</h2>
                        <Link href="/events" class="db-stat-link">Lihat agenda →</Link>
                    </article>

                    <article class="db-stat purple">
                        <div class="db-stat-icon">
                            <svg width="22" height="22" viewBox="0 0 20 20" fill="none">
                                <circle cx="10" cy="7" r="3.5" stroke="currentColor" stroke-width="1.5"/>
                                <path d="M3 17c0-3.314 3.134-6 7-6s7 2.686 7 6" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
                            </svg>
                        </div>
                        <p class="db-stat-label">Profil Lengkap</p>
                        <h2 class="db-stat-num">{{ profile ? '100%' : '0%' }}</h2>
                        <Link href="/profile" class="db-stat-link">Ubah profil →</Link>
                    </article>
                </div>

                <!-- Pengumuman + Event -->
                <div class="db-mid-grid">
                    <!-- Pengumuman -->
                    <section class="db-card">
                        <div class="db-card-head">
                            <h2>Pengumuman Terbaru</h2>
                            <Link href="/pengumuman" class="db-see-all">Lihat Semua →</Link>
                        </div>

                        <p v-if="pengumuman.length === 0" class="db-empty">Belum ada pengumuman untuk jurusan kamu.</p>

                        <div v-for="item in pengumuman" :key="item.id" class="db-announcement">
                            <div class="db-ann-icon">
                                <svg width="16" height="16" viewBox="0 0 20 20" fill="none">
                                    <path d="M4 4h12a1 1 0 011 1v8a1 1 0 01-1 1H4a1 1 0 01-1-1V5a1 1 0 011-1z" stroke="currentColor" stroke-width="1.5"/>
                                    <path d="M7 8h6M7 11h4" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
                                </svg>
                            </div>
                            <div class="db-ann-body">
                                <div class="db-ann-title-row">
                                    <strong>{{ item.judul }}</strong>
                                    <span class="db-badge" :class="item.kategori === 'Urgent' ? 'red' : 'blue'">{{ item.kategori || 'Info' }}</span>
                                </div>
                                <p class="db-ann-preview">{{ item.isi?.substring(0, 100) }}{{ item.isi?.length > 100 ? '...' : '' }}</p>
                                <p class="db-ann-meta">{{ formatTanggal(item.created_at) }} · {{ targetLabel(item) }}</p>
                            </div>
                        </div>
                    </section>

                    <!-- Event Mendatang -->
                    <section class="db-card">
                        <div class="db-card-head">
                            <h2>Event Mendatang</h2>
                        </div>

                        <p v-if="events.length === 0" class="db-empty">Belum ada event mendatang untuk jurusan kamu.</p>

                        <div v-for="event in events" :key="event.id" class="db-event-item">
                            <div class="db-event-date">
                                <span class="db-event-bulan">{{ formatTanggalEvent(event.tanggal).bulan }}</span>
                                <span class="db-event-tgl">{{ formatTanggalEvent(event.tanggal).tgl }}</span>
                            </div>
                            <div class="db-event-body">
                                <strong>{{ event.nama_event }}</strong>
                                <p>{{ event.lokasi || 'Lokasi TBD' }}</p>
                                <p class="db-event-time" v-if="event.waktu">{{ event.waktu }}</p>
                            </div>
                        </div>

                        <Link href="/events" class="db-kalender-link">Lihat Kalender Akademik →</Link>

                        <!-- Butuh Bantuan -->
                        <div class="db-help-card">
                            <h3>Butuh Bantuan?</h3>
                            <p>Hubungi admin akademik atau chat langsung dengan pembimbing akademik Anda.</p>
                            <a href="#" class="db-help-btn">Chat Admin Akademik</a>
                        </div>
                    </section>
                </div>

                <!-- Akses Cepat Materi -->
                <section class="db-card db-materi-section">
                    <div class="db-card-head">
                        <h2>Akses Cepat Materi</h2>
                        <Link href="/materi" class="db-see-all">Lihat Semua →</Link>
                    </div>

                    <p v-if="materi.length === 0" class="db-empty">Belum ada materi untuk jurusan kamu.</p>

                    <div class="db-materi-grid">
                        <a
                            v-for="item in materi.slice(0, 3)"
                            :key="item.id"
                            :href="item.file_url || '#'"
                            target="_blank"
                            rel="noreferrer"
                            class="db-materi-card"
                        >
                            <div class="db-materi-img">
                                <svg width="28" height="28" viewBox="0 0 20 20" fill="none">
                                    <path d="M4 3h8l4 4v10a1 1 0 01-1 1H4a1 1 0 01-1-1V4a1 1 0 011-1z" fill="rgba(255,255,255,0.15)" stroke="white" stroke-width="1.5"/>
                                    <path d="M12 3v4h4M7 10h6M7 13h4" stroke="white" stroke-width="1.5" stroke-linecap="round"/>
                                </svg>
                                <div class="db-materi-overlay"></div>
                            </div>
                            <div class="db-materi-info">
                                <strong>{{ item.judul }}</strong>
                                <p>{{ item.mata_kuliah || 'Materi' }} · {{ targetLabel(item) }}</p>
                            </div>
                        </a>
                    </div>
                </section>

                <!-- Footer -->
                <footer class="db-footer">
                    <span>© 2025 CampusHub Student Portal. All rights reserved.</span>
                    <div class="db-footer-links">
                        <a href="#">Privacy Policy</a>
                        <a href="#">Terms of Service</a>
                    </div>
                </footer>
            </div>
        </div>
    </div>
</template>

