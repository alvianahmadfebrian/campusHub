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
</script>

<template>
    <Head title="Materi" />

    <AppLayout>
        <header class="page-header">
            <div>
                <span class="eyebrow">Pembelajaran</span>
                <h1 class="title">Materi Kuliah</h1>
                <p class="muted">Materi umum dan materi khusus jurusan kamu.</p>
            </div>
        </header>

        <div class="grid grid-3 section-gap">
            <p v-if="items.length === 0" class="card muted">Belum ada materi yang tersedia.</p>

            <article v-for="item in items" :key="item.id" class="card content-card">
                <div class="inline-badges">
                    <span class="badge">{{ item.mata_kuliah || 'Materi' }}</span>
                    <span class="badge neutral">{{ targetLabel(item) }}</span>
                </div>
                <h2>{{ item.judul }}</h2>
                <p class="muted body-text">{{ item.deskripsi || 'Tidak ada deskripsi.' }}</p>
                <a
                    v-if="item.file_url"
                    class="btn secondary content-button"
                    :href="item.file_url"
                    target="_blank"
                    rel="noreferrer"
                >
                    Buka File
                </a>
            </article>
        </div>
    </AppLayout>
</template>
