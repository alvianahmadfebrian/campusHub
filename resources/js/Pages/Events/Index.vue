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
        day: '2-digit',
        month: 'short',
        year: 'numeric',
    }).format(new Date(value))
}

function eventStatus(item) {
    const now = new Date()
    const eventDate = item.tanggal ? new Date(item.tanggal) : null

    if (!eventDate) return 'Upcoming'
    return eventDate >= now ? 'Upcoming' : 'Selesai'
}
</script>

<template>
    <Head title="Event" />

    <AppLayout>
        <section class="event-page">
            <!-- HEADER -->
            <div class="event-header">
                <div>
                    <span class="eyebrow">Agenda Kampus</span>
                    <h1 class="title">Event Kampus</h1>
                    <p class="muted">
                        Temukan webinar, workshop, dan kegiatan terbaru di lingkungan kampus.
                    </p>
                </div>

            </div>

            <!-- FILTER -->
            <div class="event-toolbar">
                <div class="event-filters">
                    <button class="event-pill active">Semua</button>
                    <button class="event-pill">Webinar</button>
                    <button class="event-pill">Workshop</button>
                    <button class="event-pill">Lomba</button>
                </div>

                <div class="event-sort">
                    <span>Urutkan:</span>
                    <select>
                        <option>Terbaru</option>
                        <option>Terlama</option>
                    </select>
                </div>
            </div>

            <!-- EMPTY -->
            <div v-if="items.length === 0" class="event-empty">
                <h3>Belum ada event yang tersedia</h3>
                <p>Event kampus akan muncul di halaman ini.</p>
            </div>

            <!-- GRID -->
            <div v-else class="event-grid">
                <article
                    v-for="item in items"
                    :key="item.id"
                    class="event-list-card"
                >
                    <div class="event-img-wrap">
                        <img
                            v-if="item.gambar_url"
                            :src="item.gambar_url"
                            :alt="item.nama_event"
                        />

                        <div v-else class="event-img-placeholder">
                            Event Campus
                        </div>

                        <span
                            class="event-status"
                            :class="{ done: eventStatus(item) === 'Selesai' }"
                        >
                            {{ eventStatus(item) }}
                        </span>
                    </div>

                    <div class="event-card-body">
                        <span class="event-category">
                            {{ targetLabel(item) }}
                        </span>

                        <h2>{{ item.nama_event }}</h2>

                        <div class="event-info">
                            <p>📅 {{ formatTanggal(item.tanggal) }}</p>
                            <p>📍 {{ item.lokasi || 'Lokasi belum ditentukan' }}</p>
                        </div>

                        <p class="event-desc">
                            {{ item.deskripsi || 'Tidak ada deskripsi event.' }}
                        </p>

                        <div class="event-card-footer">
                            <strong>
                                {{ item.link_pendaftaran ? 'Registration Open' : 'Free Entry' }}
                            </strong>

                            <a
                                v-if="item.link_pendaftaran"
                                :href="item.link_pendaftaran"
                                target="_blank"
                                rel="noreferrer"
                            >
                                Detail →
                            </a>

                            <span v-else>Detail →</span>
                        </div>
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

.event-page {
    display: flex;
    flex-direction: column;
    gap: 20px;
}

/* HEADER */

.event-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    gap: 18px;
    flex-wrap: wrap;
}

.event-create-btn {
    border: 0;
    border-radius: 10px;
    background: #0d9488;
    color: white;
    padding: 11px 18px;
    font-size: 13px;
    font-weight: 700;
    cursor: pointer;
}

.event-create-btn:hover {
    background: #0f766e;
}

/* TOOLBAR */

.event-toolbar {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 16px;
    flex-wrap: wrap;
}

.event-filters {
    display: flex;
    gap: 8px;
    flex-wrap: wrap;
}

.event-pill {
    border: 1px solid var(--border);
    background: var(--surface);
    color: var(--muted);
    border-radius: 999px;
    padding: 8px 16px;
    font-size: 13px;
    font-weight: 600;
    cursor: pointer;
}

.event-pill.active,
.event-pill:hover {
    background: #0d9488;
    border-color: #0d9488;
    color: white;
}

.event-sort {
    display: flex;
    align-items: center;
    gap: 8px;
    color: #64748b;
    font-size: 13px;
}

.event-sort select {
    border: 1px solid var(--border);
    border-radius: 9px;
    padding: 8px 12px;
    background: var(--surface);
    color: var(--text);
    outline: none;
}

/* EMPTY */

.event-empty {
    background: var(--surface);
    border: 2px dashed var(--border);
    border-radius: 16px;
    padding: 60px 20px;
    text-align: center;
}

.event-empty h3 {
    margin-bottom: 6px;
    font-size: 18px;
}

.event-empty p {
    color: var(--muted);
}

/* GRID */

.event-grid {
    display: grid;
    grid-template-columns: repeat(3, minmax(0, 1fr));
    gap: 18px;
}

/* CARD */

.event-list-card {
    background: var(--surface);
    border: 1px solid var(--border);
    border-radius: 16px;
    overflow: hidden;
    transition: all .2s ease;
}

.event-list-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 12px 32px rgba(15, 23, 42, .1);
}

.event-img-wrap {
    height: 170px;
    position: relative;
    overflow: hidden;
    background: #0f172a;
}

.event-img-wrap img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.event-img-placeholder {
    width: 100%;
    height: 100%;
    display: grid;
    place-items: center;
    background:
        linear-gradient(to bottom, rgba(15, 23, 42, .15), rgba(15, 23, 42, .65)),
        linear-gradient(135deg, #0d9488, #0f172a);
    color: white;
    font-weight: 800;
    letter-spacing: .04em;
    text-transform: uppercase;
}

.event-status {
    position: absolute;
    top: 12px;
    left: 12px;
    padding: 6px 12px;
    border-radius: 999px;
    background: #0d9488;
    color: white;
    font-size: 11px;
    font-weight: 700;
}

.event-status.done {
    background: #64748b;
}

.event-card-body {
    padding: 16px;
}

.event-category {
    color: #0d9488;
    font-size: 11px;
    font-weight: 800;
    text-transform: uppercase;
    letter-spacing: .08em;
}

.event-card-body h2 {
    margin: 8px 0 12px;
    font-size: 18px;
    line-height: 1.35;
    color: var(--text);
}

.event-info {
    display: grid;
    gap: 6px;
    color: var(--muted);
    font-size: 13px;
}

.event-info p {
    margin: 0;
}

.event-desc {
    margin: 12px 0 0;
    color: var(--muted);
    font-size: 13px;
    line-height: 1.6;
}

.event-card-footer {
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 12px;
    margin-top: 16px;
    padding-top: 14px;
    border-top: 1px solid var(--border);
}

.event-card-footer strong {
    font-size: 13px;
    color: var(--text);
}

.event-card-footer a,
.event-card-footer span {
    color: #0d9488;
    font-size: 13px;
    font-weight: 700;
}

/* PAGINATION */

.event-pagination {
    display: flex;
    justify-content: center;
    align-items: center;
    gap: 8px;
    margin-top: 8px;
}

.event-pagination button {
    min-width: 34px;
    height: 34px;
    border: 1px solid var(--border);
    background: var(--surface);
    border-radius: 999px;
    color: var(--muted);
    cursor: pointer;
    font-weight: 600;
}

.event-pagination button.active,
.event-pagination button:hover {
    background: #0d9488;
    color: white;
    border-color: #0d9488;
}

.event-pagination span {
    color: #94a3b8;
}

/* RESPONSIVE */

@media (max-width: 1100px) {
    .event-grid {
        grid-template-columns: repeat(2, minmax(0, 1fr));
    }
}

@media (max-width: 640px) {
    .event-grid {
        grid-template-columns: 1fr;
    }

    .event-header {
        flex-direction: column;
    }

    .event-toolbar {
        align-items: flex-start;
        flex-direction: column;
    }

    .event-sort {
        width: 100%;
        justify-content: space-between;
    }
}
</style>