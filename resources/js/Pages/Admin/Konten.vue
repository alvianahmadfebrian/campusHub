<script setup>
import { Head, Link, router, useForm } from '@inertiajs/vue3'
import { computed, ref } from 'vue'
import AdminLayout from '@/Layouts/AdminLayout.vue'

const props = defineProps({
    stats: {
        type: Object,
        default: () => ({
            pengumuman: 0,
            materi: 0,
            events: 0,
        }),
    },

    jurusan: {
        type: Array,
        default: () => [],
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

const activeTab = ref('pengumuman')
const search = ref('')
const editingType = ref('')
const editingItem = ref(null)

const tabs = [
    {
        key: 'pengumuman',
        label: 'Pengumuman',
    },
    {
        key: 'materi',
        label: 'Materi',
    },
    {
        key: 'events',
        label: 'Event',
    },
]

const editForm = useForm({
    judul: '',
    kategori: '',
    isi: '',
    mata_kuliah: '',
    deskripsi: '',
    nama_event: '',
    tanggal: '',
    lokasi: '',
    link_pendaftaran: '',
    jurusan_id: '',
})

const displayedItems = computed(() => {
    const keyword = search.value.trim().toLowerCase()

    let items = []

    if (activeTab.value === 'pengumuman') {
        items = props.pengumuman
    }

    if (activeTab.value === 'materi') {
        items = props.materi
    }

    if (activeTab.value === 'events') {
        items = props.events
    }

    if (!keyword) {
        return items
    }

    return items.filter((item) => {
        const searchable = [
            item.judul,
            item.kategori,
            item.isi,
            item.mata_kuliah,
            item.deskripsi,
            item.nama_event,
            item.lokasi,
            item.target_jurusan,
        ]
            .filter(Boolean)
            .join(' ')
            .toLowerCase()

        return searchable.includes(keyword)
    })
})

function countForTab(tab) {
    if (tab === 'pengumuman') {
        return props.stats.pengumuman ?? 0
    }

    if (tab === 'materi') {
        return props.stats.materi ?? 0
    }

    return props.stats.events ?? 0
}

function targetLabel(item) {
    return item.target_jurusan || 'Semua Jurusan'
}

function formatDate(value) {
    if (!value) {
        return '-'
    }

    return new Intl.DateTimeFormat('id-ID', {
        day: 'numeric',
        month: 'short',
        year: 'numeric',
    }).format(new Date(value))
}

function shorten(value, length = 90) {
    if (!value) {
        return '-'
    }

    return value.length > length
        ? `${value.slice(0, length)}...`
        : value
}

function openEdit(item, type) {
    editingType.value = type
    editingItem.value = item

    editForm.clearErrors()
    editForm.reset()

    editForm.jurusan_id = item.jurusan_id || ''

    if (type === 'pengumuman') {
        editForm.judul = item.judul || ''
        editForm.kategori = item.kategori || 'Umum'
        editForm.isi = item.isi || ''
    }

    if (type === 'materi') {
        editForm.judul = item.judul || ''
        editForm.mata_kuliah = item.mata_kuliah || ''
        editForm.deskripsi = item.deskripsi || ''
    }

    if (type === 'events') {
        editForm.nama_event = item.nama_event || ''
        editForm.deskripsi = item.deskripsi || ''
        editForm.tanggal = item.tanggal
            ? String(item.tanggal).slice(0, 10)
            : ''
        editForm.lokasi = item.lokasi || ''
        editForm.link_pendaftaran = item.link_pendaftaran || ''
    }
}

function closeEdit() {
    editingType.value = ''
    editingItem.value = null
    editForm.reset()
    editForm.clearErrors()
}

function submitEdit() {
    if (!editingItem.value) {
        return
    }

    let url = ''

    if (editingType.value === 'pengumuman') {
        url = `/admin/konten/pengumuman/${editingItem.value.id}`
    }

    if (editingType.value === 'materi') {
        url = `/admin/konten/materi/${editingItem.value.id}`
    }

    if (editingType.value === 'events') {
        url = `/admin/konten/events/${editingItem.value.id}`
    }

    editForm.patch(url, {
        preserveScroll: true,
        onSuccess: () => closeEdit(),
    })
}

function destroyItem(item, type) {
    let label = ''
    let url = ''

    if (type === 'pengumuman') {
        label = item.judul
        url = `/admin/konten/pengumuman/${item.id}`
    }

    if (type === 'materi') {
        label = item.judul
        url = `/admin/konten/materi/${item.id}`
    }

    if (type === 'events') {
        label = item.nama_event
        url = `/admin/konten/events/${item.id}`
    }

    const confirmed = window.confirm(`Hapus konten "${label}"?`)

    if (!confirmed) {
        return
    }

    router.delete(url, {
        preserveScroll: true,
    })
}
</script>

<template>
    <Head title="Manajemen Konten Admin" />

    <AdminLayout>
        <div class="content-page">
            <!-- HEADER -->
            <header class="content-header">
                <div>
                    <p class="content-eyebrow">MANAJEMEN KONTEN</p>

                    <h1>Manajemen Konten</h1>

                    <p>
                        Kelola konten yang sudah dipublikasikan pada portal akademik.
                    </p>
                </div>

                <Link href="/admin/akademik" class="create-button">
                    <svg viewBox="0 0 24 24" fill="none">
                        <path
                            d="M12 5v14M5 12h14"
                            stroke="currentColor"
                            stroke-width="2"
                            stroke-linecap="round"
                        />
                    </svg>

                    Tambah Konten
                </Link>
            </header>

            <!-- STATISTIC -->
            <div class="content-stats">
                <button
                    type="button"
                    class="stat-card blue"
                    :class="{ active: activeTab === 'pengumuman' }"
                    @click="activeTab = 'pengumuman'"
                >
                    <div class="stat-icon">
                        <svg viewBox="0 0 24 24" fill="none">
                            <path d="M4 4h16v13H7l-3 3V4z" stroke="currentColor" stroke-width="1.8" />
                            <path d="M8 9h8M8 12h5" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" />
                        </svg>
                    </div>

                    <span>Pengumuman</span>
                    <strong>{{ stats.pengumuman ?? 0 }}</strong>
                </button>

                <button
                    type="button"
                    class="stat-card orange"
                    :class="{ active: activeTab === 'materi' }"
                    @click="activeTab = 'materi'"
                >
                    <div class="stat-icon">
                        <svg viewBox="0 0 24 24" fill="none">
                            <path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z" stroke="currentColor" stroke-width="1.8" />
                            <path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z" stroke="currentColor" stroke-width="1.8" />
                        </svg>
                    </div>

                    <span>Materi</span>
                    <strong>{{ stats.materi ?? 0 }}</strong>
                </button>

                <button
                    type="button"
                    class="stat-card purple"
                    :class="{ active: activeTab === 'events' }"
                    @click="activeTab = 'events'"
                >
                    <div class="stat-icon">
                        <svg viewBox="0 0 24 24" fill="none">
                            <rect x="3" y="4" width="18" height="17" rx="2" stroke="currentColor" stroke-width="1.8" />
                            <path d="M8 2v5M16 2v5M3 10h18" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" />
                        </svg>
                    </div>

                    <span>Event</span>
                    <strong>{{ stats.events ?? 0 }}</strong>
                </button>
            </div>

            <!-- MAIN CARD -->
            <section class="content-card">
                <div class="content-toolbar">
                    <div class="content-tabs">
                        <button
                            v-for="tab in tabs"
                            :key="tab.key"
                            type="button"
                            :class="{ active: activeTab === tab.key }"
                            @click="activeTab = tab.key"
                        >
                            {{ tab.label }}
                            <span>{{ countForTab(tab.key) }}</span>
                        </button>
                    </div>

                    <div class="content-search">
                        <svg viewBox="0 0 24 24" fill="none">
                            <circle cx="11" cy="11" r="7" stroke="currentColor" stroke-width="2" />
                            <path d="m20 20-4-4" stroke="currentColor" stroke-width="2" stroke-linecap="round" />
                        </svg>

                        <input
                            v-model="search"
                            type="text"
                            placeholder="Cari konten..."
                        />
                    </div>
                </div>

                <div class="table-container">
                    <!-- PENGUMUMAN -->
                    <table v-if="activeTab === 'pengumuman'" class="content-table">
                        <thead>
                            <tr>
                                <th>Judul</th>
                                <th>Kategori</th>
                                <th>Target</th>
                                <th>Tanggal</th>
                                <th>Isi</th>
                                <th class="action-heading">Aksi</th>
                            </tr>
                        </thead>

                        <tbody>
                            <tr v-for="item in displayedItems" :key="item.id">
                                <td class="title-cell">{{ item.judul }}</td>
                                <td>
                                    <span class="category blue">
                                        {{ item.kategori || 'Umum' }}
                                    </span>
                                </td>
                                <td>{{ targetLabel(item) }}</td>
                                <td>{{ formatDate(item.created_at) }}</td>
                                <td class="description-cell">
                                    {{ shorten(item.isi) }}
                                </td>
                                <td>
                                    <div class="action-group">
                                        <button
                                            type="button"
                                            class="icon-button"
                                            title="Edit"
                                            @click="openEdit(item, 'pengumuman')"
                                        >
                                            <svg viewBox="0 0 24 24" fill="none">
                                                <path d="M4 20h4l10.5-10.5a2.12 2.12 0 0 0-3-3L5 17v3Z" stroke="currentColor" stroke-width="1.8" />
                                            </svg>
                                        </button>

                                        <button
                                            type="button"
                                            class="icon-button delete"
                                            title="Hapus"
                                            @click="destroyItem(item, 'pengumuman')"
                                        >
                                            <svg viewBox="0 0 24 24" fill="none">
                                                <path d="M3 6h18M8 6V4h8v2M7 6l1 14h8l1-14" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round" />
                                            </svg>
                                        </button>
                                    </div>
                                </td>
                            </tr>

                            <tr v-if="displayedItems.length === 0">
                                <td colspan="6" class="empty-row">
                                    Tidak ada pengumuman ditemukan.
                                </td>
                            </tr>
                        </tbody>
                    </table>

                    <!-- MATERI -->
                    <table v-if="activeTab === 'materi'" class="content-table">
                        <thead>
                            <tr>
                                <th>Judul</th>
                                <th>Mata Kuliah</th>
                                <th>Target</th>
                                <th>Tanggal</th>
                                <th>Deskripsi</th>
                                <th class="action-heading">Aksi</th>
                            </tr>
                        </thead>

                        <tbody>
                            <tr v-for="item in displayedItems" :key="item.id">
                                <td class="title-cell">{{ item.judul }}</td>
                                <td>
                                    <span class="category orange">
                                        {{ item.mata_kuliah }}
                                    </span>
                                </td>
                                <td>{{ targetLabel(item) }}</td>
                                <td>{{ formatDate(item.created_at) }}</td>
                                <td class="description-cell">
                                    {{ shorten(item.deskripsi) }}
                                </td>
                                <td>
                                    <div class="action-group">
                                        <button
                                            type="button"
                                            class="icon-button"
                                            title="Edit informasi materi"
                                            @click="openEdit(item, 'materi')"
                                        >
                                            <svg viewBox="0 0 24 24" fill="none">
                                                <path d="M4 20h4l10.5-10.5a2.12 2.12 0 0 0-3-3L5 17v3Z" stroke="currentColor" stroke-width="1.8" />
                                            </svg>
                                        </button>

                                        <button
                                            type="button"
                                            class="icon-button delete"
                                            title="Hapus"
                                            @click="destroyItem(item, 'materi')"
                                        >
                                            <svg viewBox="0 0 24 24" fill="none">
                                                <path d="M3 6h18M8 6V4h8v2M7 6l1 14h8l1-14" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round" />
                                            </svg>
                                        </button>
                                    </div>
                                </td>
                            </tr>

                            <tr v-if="displayedItems.length === 0">
                                <td colspan="6" class="empty-row">
                                    Tidak ada materi ditemukan.
                                </td>
                            </tr>
                        </tbody>
                    </table>

                    <!-- EVENTS -->
                    <table v-if="activeTab === 'events'" class="content-table">
                        <thead>
                            <tr>
                                <th>Nama Event</th>
                                <th>Tanggal Event</th>
                                <th>Lokasi</th>
                                <th>Target</th>
                                <th>Deskripsi</th>
                                <th class="action-heading">Aksi</th>
                            </tr>
                        </thead>

                        <tbody>
                            <tr v-for="item in displayedItems" :key="item.id">
                                <td class="title-cell">{{ item.nama_event }}</td>
                                <td>
                                    <span class="category purple">
                                        {{ formatDate(item.tanggal) }}
                                    </span>
                                </td>
                                <td>{{ item.lokasi || '-' }}</td>
                                <td>{{ targetLabel(item) }}</td>
                                <td class="description-cell">
                                    {{ shorten(item.deskripsi) }}
                                </td>
                                <td>
                                    <div class="action-group">
                                        <button
                                            type="button"
                                            class="icon-button"
                                            title="Edit"
                                            @click="openEdit(item, 'events')"
                                        >
                                            <svg viewBox="0 0 24 24" fill="none">
                                                <path d="M4 20h4l10.5-10.5a2.12 2.12 0 0 0-3-3L5 17v3Z" stroke="currentColor" stroke-width="1.8" />
                                            </svg>
                                        </button>

                                        <button
                                            type="button"
                                            class="icon-button delete"
                                            title="Hapus"
                                            @click="destroyItem(item, 'events')"
                                        >
                                            <svg viewBox="0 0 24 24" fill="none">
                                                <path d="M3 6h18M8 6V4h8v2M7 6l1 14h8l1-14" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round" />
                                            </svg>
                                        </button>
                                    </div>
                                </td>
                            </tr>

                            <tr v-if="displayedItems.length === 0">
                                <td colspan="6" class="empty-row">
                                    Tidak ada event ditemukan.
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </section>
        </div>

        <!-- MODAL EDIT -->
        <div
            v-if="editingItem"
            class="modal-backdrop"
            @click.self="closeEdit"
        >
            <section class="edit-modal">
                <div class="modal-header">
                    <div>
                        <p class="modal-eyebrow">EDIT KONTEN</p>

                        <h2 v-if="editingType === 'pengumuman'">
                            Edit Pengumuman
                        </h2>

                        <h2 v-if="editingType === 'materi'">
                            Edit Materi
                        </h2>

                        <h2 v-if="editingType === 'events'">
                            Edit Event
                        </h2>
                    </div>

                    <button type="button" class="close-button" @click="closeEdit">
                        <svg viewBox="0 0 24 24" fill="none">
                            <path d="M18 6 6 18M6 6l12 12" stroke="currentColor" stroke-width="2" stroke-linecap="round" />
                        </svg>
                    </button>
                </div>

                <form class="edit-form" @submit.prevent="submitEdit">
                    <!-- PENGUMUMAN -->
                    <template v-if="editingType === 'pengumuman'">
                        <label>Judul</label>
                        <input v-model="editForm.judul" type="text" required />
                        <small v-if="editForm.errors.judul" class="form-error">{{ editForm.errors.judul }}</small>

                        <label>Kategori</label>
                        <input v-model="editForm.kategori" type="text" />

                        <label>Target Jurusan</label>
                        <select v-model="editForm.jurusan_id">
                            <option value="">Semua Jurusan</option>

                            <option
                                v-for="item in jurusan"
                                :key="item.id"
                                :value="item.id"
                            >
                                {{ item.nama }}
                            </option>
                        </select>

                        <label>Isi Pengumuman</label>
                        <textarea v-model="editForm.isi" rows="6" required></textarea>
                        <small v-if="editForm.errors.isi" class="form-error">{{ editForm.errors.isi }}</small>
                    </template>

                    <!-- MATERI -->
                    <template v-if="editingType === 'materi'">
                        <label>Judul Materi</label>
                        <input v-model="editForm.judul" type="text" required />
                        <small v-if="editForm.errors.judul" class="form-error">{{ editForm.errors.judul }}</small>

                        <label>Mata Kuliah</label>
                        <input v-model="editForm.mata_kuliah" type="text" required />
                        <small v-if="editForm.errors.mata_kuliah" class="form-error">{{ editForm.errors.mata_kuliah }}</small>

                        <label>Target Jurusan</label>
                        <select v-model="editForm.jurusan_id">
                            <option value="">Semua Jurusan</option>

                            <option
                                v-for="item in jurusan"
                                :key="item.id"
                                :value="item.id"
                            >
                                {{ item.nama }}
                            </option>
                        </select>

                        <label>Deskripsi</label>
                        <textarea v-model="editForm.deskripsi" rows="5"></textarea>

                        <p class="form-note">
                            File materi tidak berubah dari halaman ini. Upload file materi baru dilakukan melalui menu Akademik.
                        </p>
                    </template>

                    <!-- EVENTS -->
                    <template v-if="editingType === 'events'">
                        <label>Nama Event</label>
                        <input v-model="editForm.nama_event" type="text" required />

                        <div class="two-column">
                            <div>
                                <label>Tanggal</label>
                                <input v-model="editForm.tanggal" type="date" required />
                            </div>

                            <div>
                                <label>Lokasi</label>
                                <input v-model="editForm.lokasi" type="text" />
                            </div>
                        </div>

                        <label>Target Jurusan</label>
                        <select v-model="editForm.jurusan_id">
                            <option value="">Semua Jurusan</option>

                            <option
                                v-for="item in jurusan"
                                :key="item.id"
                                :value="item.id"
                            >
                                {{ item.nama }}
                            </option>
                        </select>

                        <label>Deskripsi</label>
                        <textarea v-model="editForm.deskripsi" rows="4"></textarea>

                        <label>Link Pendaftaran</label>
                        <input v-model="editForm.link_pendaftaran" type="url" placeholder="https://..." />
                        <small v-if="editForm.errors.link_pendaftaran" class="form-error">
                            {{ editForm.errors.link_pendaftaran }}
                        </small>
                    </template>

                    <div class="modal-actions">
                        <button type="button" class="cancel-button" @click="closeEdit">
                            Batal
                        </button>

                        <button
                            type="submit"
                            class="save-button"
                            :disabled="editForm.processing"
                        >
                            {{ editForm.processing ? 'Menyimpan...' : 'Simpan Perubahan' }}
                        </button>
                    </div>
                </form>
            </section>
        </div>
    </AdminLayout>
</template>

<style scoped>
.content-page {
    display: flex;
    flex-direction: column;
    gap: 20px;
}

.content-header {
    display: flex;
    align-items: flex-start;
    justify-content: space-between;
    gap: 18px;
}

.content-eyebrow {
    margin: 0 0 6px;
    color: #0f9488;
    font-size: 11px;
    font-weight: 800;
    letter-spacing: 0.1em;
}

.content-header h1 {
    margin: 0 0 7px;
    color: #0f172a;
    font-size: 30px;
}

.content-header p:not(.content-eyebrow) {
    margin: 0;
    color: #64748b;
    font-size: 14px;
}

.create-button {
    display: inline-flex;
    align-items: center;
    gap: 7px;
    padding: 11px 15px;
    border-radius: 10px;
    background: #0f9488;
    color: #ffffff;
    font-size: 13px;
    font-weight: 700;
    text-decoration: none;
}

.create-button:hover {
    background: #0f766e;
}

.create-button svg {
    width: 17px;
    height: 17px;
}

.content-stats {
    display: grid;
    grid-template-columns: repeat(3, minmax(0, 1fr));
    gap: 14px;
}

.stat-card {
    display: flex;
    align-items: center;
    gap: 13px;
    padding: 17px;
    border: 1px solid transparent;
    border-radius: 15px;
    cursor: pointer;
    text-align: left;
}

.stat-card.active {
    border-color: currentColor;
}

.stat-card span {
    flex: 1;
    color: #475569;
    font-size: 13px;
    font-weight: 600;
}

.stat-card strong {
    color: #0f172a;
    font-size: 27px;
}

.stat-card.blue {
    background: #eff6ff;
    color: #2563eb;
}

.stat-card.orange {
    background: #fff7ed;
    color: #ea580c;
}

.stat-card.purple {
    background: #f5f3ff;
    color: #7c3aed;
}

.stat-icon {
    display: grid;
    place-items: center;
    width: 43px;
    height: 43px;
    border-radius: 12px;
    background: rgba(255, 255, 255, 0.72);
}

.stat-icon svg {
    width: 22px;
    height: 22px;
}

.content-card {
    overflow: hidden;
    border: 1px solid #e2e8f0;
    border-radius: 16px;
    background: #ffffff;
}

.content-toolbar {
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 17px;
    padding: 17px 18px;
    border-bottom: 1px solid #e2e8f0;
}

.content-tabs {
    display: flex;
    gap: 8px;
}

.content-tabs button {
    display: inline-flex;
    align-items: center;
    gap: 7px;
    padding: 9px 13px;
    border: none;
    border-radius: 10px;
    background: #f8fafc;
    color: #64748b;
    cursor: pointer;
    font-weight: 700;
}

.content-tabs button.active {
    background: #ecfdf5;
    color: #0f766e;
}

.content-tabs span {
    padding: 2px 6px;
    border-radius: 999px;
    background: #ffffff;
    font-size: 11px;
}

.content-search {
    position: relative;
    width: 280px;
}

.content-search svg {
    position: absolute;
    top: 50%;
    left: 11px;
    width: 16px;
    height: 16px;
    color: #94a3b8;
    transform: translateY(-50%);
}

.content-search input {
    width: 100%;
    padding: 10px 12px 10px 35px;
    border: 1px solid #e2e8f0;
    border-radius: 10px;
    font: inherit;
    font-size: 13px;
    outline: none;
}

.content-search input:focus {
    border-color: #0f9488;
}

.table-container {
    overflow-x: auto;
}

.content-table {
    width: 100%;
    border-collapse: collapse;
}

.content-table th {
    padding: 12px 16px;
    background: #f8fafc;
    color: #64748b;
    font-size: 11px;
    font-weight: 750;
    letter-spacing: 0.045em;
    text-align: left;
    text-transform: uppercase;
}

.content-table td {
    padding: 14px 16px;
    border-bottom: 1px solid #eef2f7;
    color: #475569;
    font-size: 13px;
    vertical-align: middle;
}

.title-cell {
    color: #0f172a !important;
    font-weight: 700;
}

.description-cell {
    max-width: 310px;
}

.category {
    display: inline-flex;
    padding: 5px 9px;
    border-radius: 999px;
    font-size: 11px;
    font-weight: 700;
}

.category.blue {
    background: #dbeafe;
    color: #2563eb;
}

.category.orange {
    background: #ffedd5;
    color: #ea580c;
}

.category.purple {
    background: #ede9fe;
    color: #7c3aed;
}

.action-heading {
    width: 102px;
}

.action-group {
    display: flex;
    gap: 7px;
}

.icon-button {
    display: grid;
    place-items: center;
    width: 35px;
    height: 35px;
    border: 1px solid #e2e8f0;
    border-radius: 9px;
    background: #ffffff;
    color: #475569;
    cursor: pointer;
}

.icon-button:hover {
    border-color: #ccfbf1;
    background: #f0fdfa;
    color: #0f766e;
}

.icon-button.delete {
    color: #dc2626;
}

.icon-button.delete:hover {
    border-color: #fecaca;
    background: #fef2f2;
}

.icon-button svg {
    width: 17px;
    height: 17px;
}

.empty-row {
    padding: 42px 15px !important;
    color: #94a3b8 !important;
    text-align: center;
}

.modal-backdrop {
    position: fixed;
    z-index: 100;
    inset: 0;
    display: grid;
    place-items: center;
    padding: 20px;
    background: rgba(15, 23, 42, 0.44);
}

.edit-modal {
    width: min(570px, 100%);
    max-height: calc(100vh - 40px);
    overflow-y: auto;
    padding: 21px;
    border-radius: 17px;
    background: #ffffff;
    box-shadow: 0 24px 64px rgba(15, 23, 42, 0.2);
}

.modal-header {
    display: flex;
    align-items: flex-start;
    justify-content: space-between;
    margin-bottom: 17px;
}

.modal-eyebrow {
    margin: 0 0 5px;
    color: #0f9488;
    font-size: 10px;
    font-weight: 800;
    letter-spacing: 0.1em;
}

.modal-header h2 {
    margin: 0;
    color: #0f172a;
    font-size: 21px;
}

.close-button {
    display: grid;
    place-items: center;
    width: 34px;
    height: 34px;
    border: none;
    border-radius: 9px;
    background: #f8fafc;
    color: #64748b;
    cursor: pointer;
}

.close-button svg {
    width: 18px;
    height: 18px;
}

.edit-form {
    display: flex;
    flex-direction: column;
    gap: 8px;
}

.edit-form label {
    margin-top: 6px;
    color: #475569;
    font-size: 12px;
    font-weight: 700;
}

.edit-form input,
.edit-form select,
.edit-form textarea {
    width: 100%;
    padding: 11px 12px;
    border: 1px solid #cbd5e1;
    border-radius: 10px;
    color: #0f172a;
    font: inherit;
    font-size: 13px;
    outline: none;
}

.edit-form textarea {
    resize: vertical;
}

.edit-form input:focus,
.edit-form select:focus,
.edit-form textarea:focus {
    border-color: #0f9488;
}

.form-error {
    color: #dc2626;
    font-size: 12px;
}

.form-note {
    margin: 7px 0 0;
    padding: 9px 11px;
    border-radius: 9px;
    background: #f8fafc;
    color: #64748b;
    font-size: 12px;
    line-height: 1.5;
}

.two-column {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 11px;
}

.two-column div {
    display: flex;
    flex-direction: column;
    gap: 8px;
}

.modal-actions {
    display: flex;
    justify-content: flex-end;
    gap: 9px;
    margin-top: 18px;
}

.cancel-button,
.save-button {
    padding: 10px 15px;
    border: none;
    border-radius: 10px;
    cursor: pointer;
    font-weight: 700;
}

.cancel-button {
    background: #f1f5f9;
    color: #475569;
}

.save-button {
    background: #0f9488;
    color: #ffffff;
}

.save-button:disabled {
    opacity: 0.6;
    cursor: not-allowed;
}

@media (max-width: 850px) {
    .content-stats {
        grid-template-columns: 1fr;
    }

    .content-toolbar {
        flex-direction: column;
        align-items: stretch;
    }

    .content-search {
        width: 100%;
    }
}

@media (max-width: 560px) {
    .content-header {
        flex-direction: column;
    }

    .content-tabs {
        flex-wrap: wrap;
    }

    .two-column {
        grid-template-columns: 1fr;
    }
}
</style>
