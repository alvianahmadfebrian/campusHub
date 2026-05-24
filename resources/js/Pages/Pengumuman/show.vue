<script setup>
import { Head, Link } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'

defineProps({
    pengumuman: Object,
    related: {
        type: Array,
        default: () => [],
    },
})
</script>

<template>
    <Head :title="pengumuman.judul" />

    <AppLayout>
        <section class="peng-detail-page">
            <main class="peng-detail-main">
                <div class="breadcrumb">
                    Dashboard > Pengumuman > Detail Pengumuman
                </div>

                <article class="peng-detail-card">
                    <div class="peng-detail-cover"></div>

                    <div class="peng-detail-body">
                        <span class="peng-badge green">
                            {{ pengumuman.kategori || 'Akademik' }}
                        </span>

                        <h1>{{ pengumuman.judul }}</h1>

                        <div class="peng-detail-meta">
                            <span>📅 {{ pengumuman.tanggal || pengumuman.created_at }}</span>
                            <span>👤 Admin Akademik</span>
                            <span>👁 1.240 Dilihat</span>
                        </div>

                        <div class="peng-detail-text">
                            {{ pengumuman.isi }}
                        </div>

                        <div class="peng-share">
                            <span>Bagikan pengumuman ini:</span>
                            <button>WhatsApp</button>
                            <button>Salin Tautan</button>
                        </div>
                    </div>
                </article>

                <section class="peng-attach-card">
                    <h2>📎 Lampiran Berkas</h2>

                    <div class="peng-attach-list">
                        <a v-if="pengumuman.file_url" :href="pengumuman.file_url" target="_blank">
                            <span>📄</span>
                            <div>
                                <strong>Lampiran Pengumuman</strong>
                                <small>PDF / Document</small>
                            </div>
                            <span>⬇</span>
                        </a>

                        <p v-else class="muted">Tidak ada lampiran.</p>
                    </div>
                </section>
            </main>

            <aside class="peng-detail-side">
                <section class="side-card">
                    <h2>Pengumuman Terkait</h2>

                    <div v-for="item in related" :key="item.id" class="related-item">
                        <span>{{ item.kategori || 'Akademik' }}</span>
                        <strong>{{ item.judul }}</strong>
                        <small>2 hari yang lalu</small>
                    </div>

                    <Link href="/pengumuman" class="side-btn">
                        Lihat Semua Pengumuman
                    </Link>
                </section>

                <section class="side-card">
                    <h2>Kategori Populer</h2>

                    <div class="inline-badges">
                        <span class="badge neutral">Akademik</span>
                        <span class="badge neutral">Beasiswa</span>
                        <span class="badge neutral">Lomba</span>
                        <span class="badge neutral">Seminar</span>
                        <span class="badge neutral">Organisasi</span>
                    </div>
                </section>
            </aside>
        </section>
    </AppLayout>
</template>