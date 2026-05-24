<script setup>
import { Head, Link } from '@inertiajs/vue3'

defineProps({
    rootFolder: { type: Object, required: true },
    currentFolder: { type: Object, required: true },
    breadcrumbs: { type: Array, default: () => [] },
    folders: { type: Array, default: () => [] },
    files: { type: Array, default: () => [] },
})

function ukuran(bytes) {
    if (!bytes) return '0 KB'
    const units = ['B', 'KB', 'MB', 'GB']
    const index = Math.min(Math.floor(Math.log(bytes) / Math.log(1024)), units.length - 1)
    return `${(bytes / Math.pow(1024, index)).toFixed(index === 0 ? 0 : 1)} ${units[index]}`
}
</script>

<template>
    <Head :title="`${currentFolder.nama} - Shared Drive`" />

    <div class="public-drive-page">
        <header class="public-drive-nav">
            <div class="container nav-inner">
                <div class="brand">
                    <span class="brand-mark">C</span>
                    CampusHub Drive
                </div>
                <span class="badge success">Tautan Publik</span>
            </div>
        </header>

        <main class="container public-drive-content">
            <span class="eyebrow">Folder Dibagikan</span>
            <h1 class="title">{{ rootFolder.nama }}</h1>
            <p class="muted">Folder ini dibagikan secara publik. Tidak perlu login untuk membuka file.</p>

            <div class="drive-breadcrumb section-gap">
                <template v-for="(crumb, index) in breadcrumbs" :key="crumb.url">
                    <span v-if="index > 0">/</span>
                    <Link :href="crumb.url" class="text-link">{{ crumb.nama }}</Link>
                </template>
            </div>

            <section class="card section-gap">
                <div class="card-heading">
                    <h2>{{ currentFolder.nama }}</h2>
                    <span class="muted">{{ folders.length }} folder · {{ files.length }} file</span>
                </div>

                <p v-if="folders.length === 0 && files.length === 0" class="muted empty-state">
                    Folder ini masih kosong.
                </p>

                <div class="drive-list">
                    <Link v-for="folder in folders" :key="folder.id" :href="folder.url" class="drive-item drive-public-link">
                        <div class="drive-item-main">
                            <span class="drive-icon">📁</span>
                            <strong>{{ folder.nama }}</strong>
                        </div>
                        <span class="text-link">Buka</span>
                    </Link>

                    <article v-for="file in files" :key="file.id" class="drive-item">
                        <div class="drive-item-main">
                            <span class="drive-icon file">📄</span>
                            <div>
                                <strong>{{ file.nama_tampilan }}</strong>
                                <p class="muted">{{ ukuran(file.ukuran_bytes) }}</p>
                            </div>
                        </div>
                        <a :href="file.url" class="btn secondary small" target="_blank" rel="noopener">Buka File</a>
                    </article>
                </div>
            </section>
        </main>
    </div>
</template>
