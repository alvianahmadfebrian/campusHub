<script setup>
import { Head } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'

defineProps({
    items: {
        type: Array,
        default: () => [],
    },
})

function targetLabel(item) {
    return item.target_jurusan || 'Semua Jurusan'
}

function formatTanggal(value) {
    if (!value) return 'Tanggal belum tersedia'

    return new Intl.DateTimeFormat('id-ID', {
        day: 'numeric',
        month: 'long',
        year: 'numeric',
    }).format(new Date(value))
}
</script>

<template>
    <Head title="Event" />

    <AppLayout>
        <header class="page-header">
            <div>
                <span class="eyebrow">Agenda Kampus</span>
                <h1 class="title">Event Mahasiswa</h1>
                <p class="muted">Event umum dan kegiatan khusus jurusan kamu.</p>
            </div>
        </header>

        <div class="grid grid-3 section-gap">
            <p v-if="items.length === 0" class="card muted">Belum ada event yang tersedia.</p>

            <article v-for="item in items" :key="item.id" class="card event-card">
                <img
                    v-if="item.gambar_url"
                    :src="item.gambar_url"
                    :alt="item.nama_event"
                    class="event-image"
                />

                <div class="inline-badges">
                    <span class="badge success">{{ formatTanggal(item.tanggal) }}</span>
                    <span class="badge neutral">{{ targetLabel(item) }}</span>
                </div>
                <h2>{{ item.nama_event }}</h2>
                <p class="muted">{{ item.lokasi || 'Lokasi belum ditentukan' }}</p>
                <p class="body-text">{{ item.deskripsi || 'Tidak ada deskripsi.' }}</p>

                <a
                    v-if="item.link_pendaftaran"
                    class="btn secondary content-button"
                    :href="item.link_pendaftaran"
                    target="_blank"
                    rel="noreferrer"
                >
                    Daftar Event
                </a>
            </article>
        </div>
    </AppLayout>
</template>
