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
    if (!value) return '-'
    return new Intl.DateTimeFormat('id-ID', {
        day: 'numeric',
        month: 'long',
        year: 'numeric',
    }).format(new Date(value))
}
</script>

<template>
    <Head title="Pengumuman" />

    <AppLayout>
        <header class="page-header">
            <div>
                <span class="eyebrow">Informasi Kampus</span>
                <h1 class="title">Pengumuman</h1>
                <p class="muted">Pengumuman umum dan informasi khusus untuk jurusan kamu.</p>
            </div>
        </header>

        <div class="content-list section-gap">
            <p v-if="items.length === 0" class="card muted">Belum ada pengumuman yang tersedia.</p>

            <article v-for="item in items" :key="item.id" class="card announcement">
                <div class="card-heading">
                    <div>
                        <h2>{{ item.judul }}</h2>
                        <p class="muted">{{ formatTanggal(item.created_at) }}</p>
                    </div>
                    <div class="inline-badges">
                        <span class="badge neutral">{{ targetLabel(item) }}</span>
                        <span class="badge">{{ item.kategori || 'Umum' }}</span>
                    </div>
                </div>
                <p class="body-text">{{ item.isi }}</p>
            </article>
        </div>
    </AppLayout>
</template>
