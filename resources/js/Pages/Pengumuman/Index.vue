<script setup>
import { Head, usePage } from '@inertiajs/vue3'
import { ref, computed } from 'vue'
import AppLayout from '@/Layouts/AppLayout.vue'

const page = usePage()
const isAdmin = computed(() => page.props.auth?.user?.role === 'admin')

const props = defineProps({
    items: { type: Array, default: () => [] },
})

// ── Filter & Search ──
const activeKategori = ref('Semua')
const searchQuery = ref('')
const currentPage = ref(1)
const perPage = 6

const kategoriList = computed(() => {
    const cats = ['Semua', ...new Set(props.items.map(i => i.kategori || 'Umum'))]
    return cats
})

const filtered = computed(() => {
    let list = props.items
    if (activeKategori.value !== 'Semua') {
        list = list.filter(i => (i.kategori || 'Umum') === activeKategori.value)
    }
    if (searchQuery.value.trim()) {
        const q = searchQuery.value.toLowerCase()
        list = list.filter(i =>
            i.judul?.toLowerCase().includes(q) || i.isi?.toLowerCase().includes(q)
        )
    }
    return list
})

const totalPages = computed(() => Math.ceil(filtered.value.length / perPage))

const paginated = computed(() => {
    const start = (currentPage.value - 1) * perPage
    return filtered.value.slice(start, start + perPage)
})

function setKategori(k) {
    activeKategori.value = k
    currentPage.value = 1
}

// ── Waktu relatif ──
function timeAgo(value) {
    if (!value) return '-'
    const diff = Math.floor((Date.now() - new Date(value)) / 1000)
    if (diff < 60) return 'Baru saja'
    if (diff < 3600) return `${Math.floor(diff / 60)} menit lalu`
    if (diff < 86400) return `${Math.floor(diff / 3600)} jam yang lalu`
    if (diff < 604800) return `${Math.floor(diff / 86400)} Hari Lalu`
    return new Intl.DateTimeFormat('id-ID', { day: 'numeric', month: 'long', year: 'numeric' }).format(new Date(value))
}

function formatTanggal(value) {
    if (!value) return '-'
    return new Intl.DateTimeFormat('id-ID', { day: 'numeric', month: 'short', year: 'numeric' }).format(new Date(value))
}

function targetLabel(item) {
    return item.target_jurusan || 'Semua Jurusan'
}

// ── Badge color ──
const badgeColor = {
    'Ujian': 'red',
    'Akademik': 'green',
    'Umum': 'gray',
}
function getBadgeClass(kategori) {
    return badgeColor[kategori] || 'gray'
}

// ── Detail modal ──
const selected = ref(null)
function openDetail(item) { selected.value = item }
function closeDetail() { selected.value = null }

// ── Form buat pengumuman (admin) ──
const showForm = ref(false)

// ── Pagination pages ──
const pageNumbers = computed(() => {
    const total = totalPages.value
    if (total <= 6) return Array.from({ length: total }, (_, i) => i + 1)
    const pages = [1]
    if (currentPage.value > 3) pages.push('...')
    for (let i = Math.max(2, currentPage.value - 1); i <= Math.min(total - 1, currentPage.value + 1); i++) {
        pages.push(i)
    }
    if (currentPage.value < total - 2) pages.push('...')
    pages.push(total)
    return pages
})
</script>

<template>
    <Head title="Pengumuman" />

    <AppLayout>
        <div class="peng-page">

            <!-- Header -->
            <div class="peng-header">
                <div>
                    <h1 class="peng-title">Pengumuman Terkini</h1>
                    <p class="peng-sub">Tetap terinformasi dengan berita terbaru dari kampus.</p>
                </div>
                <div class="peng-header-right">
                    <div class="peng-sort">
                        <select class="peng-select">
                            <option>Terbaru</option>
                            <option>Terlama</option>
                        </select>
                    </div>
                    <button v-if="isAdmin" class="peng-btn-create" @click="showForm = true">
                        <svg width="14" height="14" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="10" y1="4" x2="10" y2="16"/><line x1="4" y1="10" x2="16" y2="10"/></svg>
                        Buat Pengumuman
                    </button>
                </div>
            </div>

            <!-- Filter pills -->
            <div class="peng-filters">
                <button
                    v-for="k in kategoriList"
                    :key="k"
                    class="peng-pill"
                    :class="{ active: activeKategori === k }"
                    @click="setKategori(k)"
                >{{ k }}</button>
            </div>

            <!-- Empty state -->
            <div v-if="paginated.length === 0" class="peng-empty">
                <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="#cbd5e1" stroke-width="1.5"><rect x="3" y="3" width="18" height="18" rx="3"/><path d="M8 10h8M8 14h5"/></svg>
                <p>Belum Ada Pengumuman Baru</p>
                <span>Gunakan tombol "Buat Pengumuman" di pojok kanan atas<br>untuk mempublikasikan berita baru hari ini.</span>
            </div>

            <!-- Grid -->
            <div v-else class="peng-grid">
                <article
                    v-for="item in paginated"
                    :key="item.id"
                    class="peng-card"
                    @click="openDetail(item)"
                >
                    <div class="peng-card-top">
                        <span class="peng-badge" :class="getBadgeClass(item.kategori || 'Umum')">
                            {{ item.kategori || 'Umum' }}
                        </span>
                        <span class="peng-time">{{ timeAgo(item.created_at) }}</span>
                    </div>

                    <h3 class="peng-card-title">{{ item.judul }}</h3>
                    <p class="peng-card-preview">{{ item.isi?.substring(0, 120) }}{{ item.isi?.length > 120 ? '...' : '' }}</p>

                    <div class="peng-card-footer">
                        <span v-if="item.file_url" class="peng-meta-icon">
                            <svg width="13" height="13" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M4 3h8l4 4v10a1 1 0 01-1 1H4a1 1 0 01-1-1V4a1 1 0 011-1z"/></svg>
                            PDF Form
                        </span>
                        <span v-else class="peng-meta-icon">
                            <svg width="13" height="13" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.5"><rect x="2" y="4" width="16" height="14" rx="2"/><path d="m18 7-8 6-8-6"/></svg>
                            {{ formatTanggal(item.created_at) }}
                        </span>
                        <button class="peng-arrow">
                            <svg width="14" height="14" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="2"><path d="M5 10h10M12 7l3 3-3 3"/></svg>
                        </button>
                    </div>
                </article>
            </div>

            <!-- Pagination -->
            <div v-if="totalPages > 1" class="peng-pagination">
                <span class="peng-count">Menampilkan {{ (currentPage - 1) * perPage + 1 }}–{{ Math.min(currentPage * perPage, filtered.length) }} dari {{ filtered.length }} pengumuman</span>
                <div class="peng-pages">
                    <button
                        class="peng-page-btn"
                        :disabled="currentPage === 1"
                        @click="currentPage--"
                    >&lsaquo;</button>
                    <template v-for="p in pageNumbers" :key="p">
                        <span v-if="p === '...'" class="peng-page-dots">...</span>
                        <button
                            v-else
                            class="peng-page-btn"
                            :class="{ active: currentPage === p }"
                            @click="currentPage = p"
                        >{{ p }}</button>
                    </template>
                    <button
                        class="peng-page-btn"
                        :disabled="currentPage === totalPages"
                        @click="currentPage++"
                    >&rsaquo;</button>
                </div>
            </div>

            <!-- Footer -->
            <footer class="peng-footer">© 2023 CampusHub Academic Portal. Hak Cipta Dilindungi.</footer>
        </div>

        <!-- ── DETAIL MODAL ── -->
        <teleport to="body">
            <div v-if="selected" class="modal-backdrop" @click.self="closeDetail">
                <div class="modal-box">
                    <div class="modal-head">
                        <div class="modal-badges">
                            <span class="peng-badge" :class="getBadgeClass(selected.kategori || 'Umum')">{{ selected.kategori || 'Umum' }}</span>
                            <span class="peng-badge gray">{{ targetLabel(selected) }}</span>
                        </div>
                        <button class="modal-close" @click="closeDetail">
                            <svg width="18" height="18" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="2"><path d="M5 5l10 10M15 5l-10 10"/></svg>
                        </button>
                    </div>
                    <h2 class="modal-title">{{ selected.judul }}</h2>
                    <p class="modal-meta">{{ timeAgo(selected.created_at) }} · {{ formatTanggal(selected.created_at) }}</p>
                    <div class="modal-divider"></div>
                    <p class="modal-body">{{ selected.isi }}</p>
                    <a v-if="selected.file_url" :href="selected.file_url" target="_blank" class="modal-file-btn">
                        <svg width="14" height="14" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M4 3h8l4 4v10a1 1 0 01-1 1H4a1 1 0 01-1-1V4a1 1 0 011-1z"/><path d="M12 3v4h4"/></svg>
                        Unduh Lampiran
                    </a>
                </div>
            </div>
        </teleport>
    </AppLayout>
</template>

