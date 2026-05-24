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

function formatDate(date) {
    if (!date) return '-'

    return new Intl.DateTimeFormat('id-ID', {
        day: '2-digit',
        month: 'short',
        year: 'numeric',
    }).format(new Date(date))
}
</script>

<template>
    <Head title="Materi" />

    <AppLayout>
        <section class="materi-page">
            <!-- HEADER -->
            <div class="materi-header">
                <div>
                    <span class="eyebrow">Pembelajaran</span>
                    <h1 class="title">Materi Kuliah</h1>
                    <p class="muted">
                        Browse dan download materi pembelajaran untuk jurusan kamu.
                    </p>
                </div>

                <div class="materi-view-btns">
                    <button class="materi-view active">☷</button>
                    <button class="materi-view">☰</button>
                </div>
            </div>

            <!-- FILTER -->
            <section class="materi-filter-card">
                <div class="materi-filter-grid">
                    <div class="materi-search">
                        <label>Search Material</label>
                        <input type="text" placeholder="Keywords: Algoritma, Statistik..." />
                    </div>

                    <div>
                        <label>Subject</label>
                        <select>
                            <option>All Subjects</option>
                        </select>
                    </div>

                    <div>
                        <label>File Type</label>
                        <select>
                            <option>All Types</option>
                        </select>
                    </div>

                    <button class="materi-apply-btn">
                        Apply
                    </button>
                </div>
            </section>

            <!-- EMPTY -->
            <div v-if="items.length === 0" class="materi-empty">
                <h3>Belum ada materi tersedia</h3>
                <p>Materi kuliah akan muncul di sini.</p>
            </div>

            <!-- GRID -->
            <div v-else class="materi-grid">
                <article
                    v-for="item in items"
                    :key="item.id"
                    class="materi-card"
                >
                    <div class="materi-card-top">
                        <div class="materi-icon">
                            📄
                        </div>

                        <span class="materi-chip">
                            {{ item.mata_kuliah || 'Materi Kuliah' }}
                        </span>
                    </div>

                    <h2 class="materi-card-title">
                        {{ item.judul }}
                    </h2>

                    <p class="materi-card-desc">
                        {{ item.deskripsi || 'Tidak ada deskripsi materi.' }}
                    </p>

                    <div class="materi-meta">
                        <span>📅 {{ formatDate(item.created_at) }}</span>
                        <span>{{ targetLabel(item) }}</span>
                    </div>

                    <div class="materi-card-footer">
                        <a
                            v-if="item.file_url"
                            :href="item.file_url"
                            target="_blank"
                            rel="noreferrer"
                            class="materi-download"
                        >
                            Buka File →
                        </a>
                    </div>
                </article>
            </div>
        </section>
    </AppLayout>
</template>

<style scoped>
* {
    box-sizing: border-box;
}

/* PAGE */

.materi-page {
    display: flex;
    flex-direction: column;
    gap: 20px;
}

/* HEADER */

.materi-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    gap: 20px;
    flex-wrap: wrap;
}

.materi-view-btns {
    display: flex;
    gap: 8px;
}

.materi-view {
    width: 38px;
    height: 38px;
    border-radius: 10px;
    border: 1px solid #dbe3ea;
    background: #fff;
    cursor: pointer;
    font-size: 16px;
    color: #64748b;
}

.materi-view.active {
    background: #ecfeff;
    color: #0d9488;
    border-color: #99f6e4;
}

/* FILTER */

.materi-filter-card {
    background: #fff;
    border: 1px solid #e2e8f0;
    border-radius: 16px;
    padding: 18px;
}

.materi-filter-grid {
    display: grid;
    grid-template-columns: 2fr 1fr 1fr auto;
    gap: 14px;
    align-items: end;
}

.materi-filter-grid label {
    display: block;
    margin-bottom: 8px;
    font-size: 12px;
    font-weight: 600;
    color: #64748b;
}

.materi-filter-grid input,
.materi-filter-grid select {
    width: 100%;
    height: 44px;
    border: 1px solid #dbe3ea;
    border-radius: 10px;
    padding: 0 14px;
    font-size: 14px;
    background: #fff;
    color: #0f172a;
    outline: none;
}

.materi-filter-grid input:focus,
.materi-filter-grid select:focus {
    border-color: #0d9488;
}

.materi-apply-btn {
    height: 44px;
    padding: 0 20px;
    border: 0;
    border-radius: 10px;
    background: #99f6e4;
    color: #0f766e;
    font-weight: 700;
    cursor: pointer;
}

/* EMPTY */

.materi-empty {
    padding: 60px 20px;
    text-align: center;
    border: 2px dashed #dbe3ea;
    border-radius: 18px;
    background: #fff;
}

.materi-empty h3 {
    margin-bottom: 6px;
    font-size: 18px;
}

.materi-empty p {
    color: #64748b;
}

/* GRID */

.materi-grid {
    display: grid;
    grid-template-columns: repeat(3, minmax(0, 1fr));
    gap: 18px;
}

/* CARD */

.materi-card {
    background: #fff;
    border: 1px solid #e2e8f0;
    border-radius: 18px;
    padding: 18px;
    display: flex;
    flex-direction: column;
    gap: 14px;
    transition: all .2s ease;
}

.materi-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 10px 30px rgba(15, 23, 42, .08);
}

.materi-card-top {
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 10px;
}

.materi-icon {
    width: 42px;
    height: 42px;
    border-radius: 12px;
    background: #fee2e2;
    display: grid;
    place-items: center;
    font-size: 18px;
}

.materi-chip {
    padding: 6px 12px;
    border-radius: 999px;
    background: #eef2ff;
    color: #4338ca;
    font-size: 11px;
    font-weight: 700;
}

.materi-card-title {
    font-size: 19px;
    line-height: 1.35;
    font-weight: 800;
    color: #0f172a;
}

.materi-card-desc {
    font-size: 14px;
    line-height: 1.7;
    color: #64748b;
    flex: 1;
}

.materi-meta {
    display: flex;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: 8px;
    padding-top: 10px;
    border-top: 1px solid #f1f5f9;
    font-size: 12px;
    color: #94a3b8;
}

.materi-card-footer {
    display: flex;
    justify-content: flex-end;
}

.materi-download {
    font-size: 13px;
    font-weight: 700;
    color: #0d9488;
    text-decoration: none;
}

.materi-download:hover {
    text-decoration: underline;
}

/* RESPONSIVE */

@media (max-width: 1000px) {
    .materi-grid {
        grid-template-columns: repeat(2, minmax(0, 1fr));
    }

    .materi-filter-grid {
        grid-template-columns: 1fr 1fr;
    }
}

@media (max-width: 640px) {
    .materi-grid {
        grid-template-columns: 1fr;
    }

    .materi-filter-grid {
        grid-template-columns: 1fr;
    }

    .materi-header {
        flex-direction: column;
    }
}
</style>