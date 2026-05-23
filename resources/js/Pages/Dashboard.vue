<script setup>
import { Head, Link } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'

defineProps({
    stats: {
        type: Object,
        default: () => ({}),
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

function targetLabel(item) {
    return item.target_jurusan || 'Semua Jurusan'
}

function formatTanggal(tanggal) {
    if (!tanggal) return '-'

    return new Intl.DateTimeFormat('id-ID', {
        day: 'numeric',
        month: 'long',
        year: 'numeric',
    }).format(new Date(tanggal))
}
</script>

<template>
    <Head title="Dashboard Mahasiswa" />

    <AppLayout>
        <header class="page-header">
            <div>
                <span class="eyebrow">Mahasiswa</span>
                <h1 class="title">Dashboard Mahasiswa</h1>
                <p class="muted">Informasi umum dan konten yang ditujukan untuk jurusan kamu.</p>
            </div>
            <Link class="btn secondary" href="/profile">Edit Profil</Link>
        </header>

        <div class="grid grid-3 section-gap">
            <article class="card stat-card">
                <p class="muted">Pengumuman</p>
                <h2>{{ stats.pengumuman ?? 0 }}</h2>
                <Link href="/pengumuman" class="text-link">Lihat informasi</Link>
            </article>

            <article class="card stat-card">
                <p class="muted">Materi tersedia</p>
                <h2>{{ stats.materi ?? 0 }}</h2>
                <Link href="/materi" class="text-link">Buka materi</Link>
            </article>

            <article class="card stat-card">
                <p class="muted">Event mendatang</p>
                <h2>{{ stats.events ?? 0 }}</h2>
                <Link href="/events" class="text-link">Lihat agenda</Link>
            </article>
        </div>

        <div class="grid grid-2 section-gap">
            <section class="card">
                <div class="card-heading">
                    <h2>Profil</h2>
                    <Link class="text-link" href="/profile">Ubah</Link>
                </div>

                <p v-if="!profile" class="muted">Profil belum lengkap.</p>

                <div v-else class="profile-summary">
                    <div class="avatar-placeholder">
                        <img v-if="profile.avatar_url" :src="profile.avatar_url" :alt="profile.nama" />
                        <span v-else>{{ profile.nama?.charAt(0)?.toUpperCase() || 'M' }}</span>
                    </div>

                    <div>
                        <strong>{{ profile.nama }}</strong>
                        <p class="muted">{{ profile.nim || 'NIM belum diisi' }}</p>
                        <p class="muted">
                            {{ profile.jurusan_nama || 'Jurusan belum dipilih' }} · Semester {{ profile.semester || '-' }}
                        </p>
                    </div>
                </div>
            </section>

            <section class="card">
                <div class="card-heading">
                    <h2>Pengumuman Terbaru</h2>
                    <Link href="/pengumuman" class="text-link">Semua</Link>
                </div>

                <p v-if="pengumuman.length === 0" class="muted">Belum ada pengumuman untuk jurusan kamu.</p>

                <div v-for="item in pengumuman" :key="item.id" class="list-item">
                    <div>
                        <strong>{{ item.judul }}</strong>
                        <p class="muted">{{ item.kategori || 'Umum' }}</p>
                    </div>
                    <span class="badge neutral">{{ targetLabel(item) }}</span>
                </div>
            </section>
        </div>

        <div class="grid grid-2 section-gap">
            <section class="card">
                <div class="card-heading">
                    <h2>Materi Terbaru</h2>
                    <Link href="/materi" class="text-link">Semua</Link>
                </div>

                <p v-if="materi.length === 0" class="muted">Belum ada materi untuk jurusan kamu.</p>

                <div v-for="item in materi" :key="item.id" class="list-item">
                    <div>
                        <strong>{{ item.judul }}</strong>
                        <p class="muted">{{ item.mata_kuliah || 'Mata kuliah' }} · {{ targetLabel(item) }}</p>
                    </div>
                    <a v-if="item.file_url" class="text-link" :href="item.file_url" target="_blank" rel="noreferrer">Buka</a>
                </div>
            </section>

            <section class="card">
                <div class="card-heading">
                    <h2>Agenda Mendatang</h2>
                    <Link href="/events" class="text-link">Semua</Link>
                </div>

                <p v-if="events.length === 0" class="muted">Belum ada event mendatang untuk jurusan kamu.</p>

                <div v-for="event in events" :key="event.id" class="list-item">
                    <div>
                        <strong>{{ event.nama_event }}</strong>
                        <p class="muted">
                            {{ formatTanggal(event.tanggal) }}
                            <span v-if="event.lokasi"> · {{ event.lokasi }}</span>
                        </p>
                    </div>
                    <span class="badge success">{{ targetLabel(event) }}</span>
                </div>
            </section>
        </div>
    </AppLayout>
</template>
