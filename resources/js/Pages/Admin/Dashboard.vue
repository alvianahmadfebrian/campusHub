<script setup>
import { Head, Link, router, useForm } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'

const props = defineProps({
    stats: { type: Object, default: () => ({}) },
    jurusan: { type: Array, default: () => [] },
    mahasiswaTerbaru: { type: Array, default: () => [] },
    pengumumanTerbaru: { type: Array, default: () => [] },
    materiTerbaru: { type: Array, default: () => [] },
    eventsTerbaru: { type: Array, default: () => [] },
})

const jurusanAktif = () => props.jurusan.filter((item) => item.aktif)

const jurusanForm = useForm({
    nama: '',
    kode: '',
})

const pengumumanForm = useForm({
    judul: '',
    kategori: 'Umum',
    isi: '',
    jurusan_id: '',
})

const materiForm = useForm({
    judul: '',
    mata_kuliah: '',
    deskripsi: '',
    jurusan_id: '',
    file: null,
})

const eventForm = useForm({
    nama_event: '',
    deskripsi: '',
    tanggal: '',
    lokasi: '',
    link_pendaftaran: '',
    jurusan_id: '',
    gambar: null,
})

function simpanJurusan() {
    jurusanForm.post('/admin/jurusan', {
        preserveScroll: true,
        onSuccess: () => jurusanForm.reset(),
    })
}

function toggleJurusan(id) {
    router.patch(`/admin/jurusan/${id}/toggle`, {}, {
        preserveScroll: true,
    })
}

function simpanPengumuman() {
    pengumumanForm.post('/admin/pengumuman', {
        preserveScroll: true,
        onSuccess: () => pengumumanForm.reset(),
    })
}

function simpanMateri() {
    materiForm.post('/admin/materi', {
        preserveScroll: true,
        forceFormData: true,
        onSuccess: () => materiForm.reset(),
    })
}

function simpanEvent() {
    eventForm.post('/admin/events', {
        preserveScroll: true,
        forceFormData: true,
        onSuccess: () => eventForm.reset(),
    })
}

function targetLabel(item) {
    return item.target_jurusan || 'Semua Jurusan'
}

function formatBulan(tanggal) {
    if (!tanggal) return '-'

    return new Intl.DateTimeFormat('id-ID', {
        month: 'short',
    }).format(new Date(tanggal))
}

function formatTgl(tanggal) {
    if (!tanggal) return '-'

    return new Intl.DateTimeFormat('id-ID', {
        day: 'numeric',
    }).format(new Date(tanggal))
}
</script>

<template>
    <Head title="Dashboard Admin" />

    <AdminLayout>
        <!-- GREETING ROW -->
        <div class="admin-greeting-row">
            <div>
                <h1 class="admin-greeting">Selamat Datang, Admin 👋</h1>
                <p class="admin-greeting-sub">
                    Berikut adalah ringkasan aktivitas akademik hari ini.
                </p>
            </div>

            <div class="admin-greeting-actions">
                <!-- CHATBOT QUICK ACCESS -->
                <Link href="/chat" class="admin-action-btn outline">
                    <svg
                        width="15"
                        height="15"
                        viewBox="0 0 24 24"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="2"
                    >
                        <path d="M4 4h16a2 2 0 0 1 2 2v11a2 2 0 0 1-2 2H9l-5 3v-3H4a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2z" />
                        <path d="M8 10h8" />
                        <path d="M8 14h5" />
                    </svg>
                    Chatbot AI
                </Link>

                <button class="admin-action-btn teal">
                    <svg
                        width="15"
                        height="15"
                        viewBox="0 0 24 24"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="2"
                    >
                        <line x1="12" y1="5" x2="12" y2="19" />
                        <line x1="5" y1="12" x2="19" y2="12" />
                    </svg>
                    Buat Pengumuman
                </button>
            </div>
        </div>

        <!-- STAT CARDS -->
        <div class="admin-stats">
            <div class="admin-stat teal">
                <div class="admin-stat-icon">
                    <svg
                        width="20"
                        height="20"
                        viewBox="0 0 24 24"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="2"
                    >
                        <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2" />
                        <circle cx="9" cy="7" r="4" />
                        <path d="M23 21v-2a4 4 0 0 0-3-3.87" />
                        <path d="M16 3.13a4 4 0 0 1 0 7.75" />
                    </svg>
                </div>

                <p class="admin-stat-label">Mahasiswa Aktif</p>
                <p class="admin-stat-num">{{ stats.mahasiswa ?? 0 }}</p>

                <span class="admin-stat-change">
                    <svg
                        width="13"
                        height="13"
                        viewBox="0 0 24 24"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="2.5"
                    >
                        <polyline points="18 15 12 9 6 15" />
                    </svg>
                    Terdaftar
                </span>
            </div>

            <div class="admin-stat blue">
                <div class="admin-stat-icon">
                    <svg
                        width="20"
                        height="20"
                        viewBox="0 0 24 24"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="2"
                    >
                        <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z" />
                        <polyline points="14 2 14 8 20 8" />
                    </svg>
                </div>

                <p class="admin-stat-label">Pengumuman</p>
                <p class="admin-stat-num">{{ stats.pengumuman ?? 0 }}</p>

                <span class="admin-stat-change">
                    <svg
                        width="13"
                        height="13"
                        viewBox="0 0 24 24"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="2.5"
                    >
                        <polyline points="18 15 12 9 6 15" />
                    </svg>
                    Bulan ini
                </span>
            </div>

            <div class="admin-stat orange">
                <div class="admin-stat-icon">
                    <svg
                        width="20"
                        height="20"
                        viewBox="0 0 24 24"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="2"
                    >
                        <path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z" />
                        <path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z" />
                    </svg>
                </div>

                <p class="admin-stat-label">Materi</p>
                <p class="admin-stat-num">{{ stats.materi ?? 0 }}</p>

                <span class="admin-stat-change">
                    <svg
                        width="13"
                        height="13"
                        viewBox="0 0 24 24"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="2.5"
                    >
                        <polyline points="18 15 12 9 6 15" />
                    </svg>
                    Terupload
                </span>
            </div>

            <div class="admin-stat purple">
                <div class="admin-stat-icon">
                    <svg
                        width="20"
                        height="20"
                        viewBox="0 0 24 24"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="2"
                    >
                        <rect x="3" y="4" width="18" height="18" rx="2" ry="2" />
                        <line x1="16" y1="2" x2="16" y2="6" />
                        <line x1="8" y1="2" x2="8" y2="6" />
                        <line x1="3" y1="10" x2="21" y2="10" />
                    </svg>
                </div>

                <p class="admin-stat-label">Event</p>
                <p class="admin-stat-num">{{ stats.events ?? 0 }}</p>

                <span class="admin-stat-change">
                    <svg
                        width="13"
                        height="13"
                        viewBox="0 0 24 24"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="2.5"
                    >
                        <polyline points="18 15 12 9 6 15" />
                    </svg>
                    Mendatang
                </span>
            </div>
        </div>

        <!-- MID GRID -->
        <div class="admin-mid-grid">
            <!-- ACTIVITY -->
            <div class="admin-card">
                <div class="admin-card-head">
                    <h2>Aktivitas Terkini</h2>
                    <a href="#" class="admin-see-all">Lihat Semua</a>
                </div>

                <p
                    v-if="mahasiswaTerbaru.length === 0 && pengumumanTerbaru.length === 0"
                    class="admin-empty"
                >
                    Belum ada aktivitas.
                </p>

                <div
                    v-for="mahasiswa in mahasiswaTerbaru.slice(0, 3)"
                    :key="mahasiswa.id"
                    class="admin-activity-item"
                >
                    <div class="admin-activity-icon green">
                        <svg
                            width="16"
                            height="16"
                            viewBox="0 0 24 24"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="2"
                        >
                            <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2" />
                            <circle cx="12" cy="7" r="4" />
                        </svg>
                    </div>

                    <div class="admin-activity-body">
                        <p class="admin-activity-text">
                            <strong>{{ mahasiswa.nama }}</strong>
                            baru saja melakukan registrasi mahasiswa baru.
                        </p>

                        <p class="admin-activity-time">
                            {{ mahasiswa.nim || '-' }} · {{ mahasiswa.jurusan_nama || '-' }}
                        </p>
                    </div>

                    <span class="admin-activity-badge teal">Baru</span>
                </div>

                <div
                    v-for="item in pengumumanTerbaru.slice(0, 2)"
                    :key="item.id"
                    class="admin-activity-item"
                >
                    <div class="admin-activity-icon blue">
                        <svg
                            width="16"
                            height="16"
                            viewBox="0 0 24 24"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="2"
                        >
                            <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z" />
                            <polyline points="14 2 14 8 20 8" />
                        </svg>
                    </div>

                    <div class="admin-activity-body">
                        <p class="admin-activity-text">
                            Pengumuman <strong>"{{ item.judul }}"</strong> telah dipublikasikan.
                        </p>

                        <p class="admin-activity-time">{{ targetLabel(item) }}</p>
                    </div>

                    <span class="admin-activity-badge blue">Pengumuman</span>
                </div>
            </div>

            <!-- RIGHT COLUMN -->
            <div style="display: flex; flex-direction: column; gap: 16px;">
                <!-- EVENTS -->
                <div class="admin-card">
                    <div class="admin-card-head">
                        <h2>Event Mendatang</h2>
                        <Link href="/events" class="admin-see-all">Semua</Link>
                    </div>

                    <p v-if="eventsTerbaru.length === 0" class="admin-empty">
                        Belum ada event.
                    </p>

                    <div
                        v-for="event in eventsTerbaru.slice(0, 3)"
                        :key="event.id"
                        class="admin-event-item"
                    >
                        <div class="admin-event-date">
                            <span class="admin-event-bulan">{{ formatBulan(event.tanggal) }}</span>
                            <span class="admin-event-tgl">{{ formatTgl(event.tanggal) }}</span>
                        </div>

                        <div class="admin-event-body">
                            <strong>{{ event.nama_event }}</strong>
                            <p>{{ event.lokasi || 'Lokasi belum diset' }}</p>
                        </div>
                    </div>

                    <button class="admin-add-event-btn">
                        <svg
                            width="14"
                            height="14"
                            viewBox="0 0 24 24"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="2"
                        >
                            <line x1="12" y1="5" x2="12" y2="19" />
                            <line x1="5" y1="12" x2="19" y2="12" />
                        </svg>
                        Tambah Event Baru
                    </button>
                </div>

                <!-- CHATBOT HELP CARD -->
                <div class="admin-help-card">
                    <h3>Chatbot AI Admin</h3>

                    <p>
                        Tanyakan statistik umum, pengumuman, materi, event,
                        atau analisis dokumen private milik admin.
                    </p>

                    <Link href="/chat" class="admin-help-btn">
                        Buka Chatbot
                        <svg
                            width="13"
                            height="13"
                            viewBox="0 0 24 24"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="2.5"
                        >
                            <line x1="5" y1="12" x2="19" y2="12" />
                            <polyline points="12 5 19 12 12 19" />
                        </svg>
                    </Link>
                </div>
            </div>
        </div>

        <!-- BOTTOM FORMS -->
        <div class="admin-bottom-grid">
            <!-- JURUSAN -->
            <div class="admin-form-card">
                <h2>Kelola Jurusan</h2>

                <form @submit.prevent="simpanJurusan">
                    <div class="admin-form-row">
                        <label class="admin-label">Nama Jurusan</label>

                        <input
                            v-model="jurusanForm.nama"
                            class="admin-input"
                            placeholder="Teknik Informatika"
                            required
                        />

                        <div v-if="jurusanForm.errors.nama" class="admin-error">
                            {{ jurusanForm.errors.nama }}
                        </div>
                    </div>

                    <div class="admin-form-row">
                        <label class="admin-label">Kode</label>

                        <input
                            v-model="jurusanForm.kode"
                            class="admin-input"
                            placeholder="TI"
                        />

                        <div v-if="jurusanForm.errors.kode" class="admin-error">
                            {{ jurusanForm.errors.kode }}
                        </div>
                    </div>

                    <button
                        class="admin-btn full"
                        type="submit"
                        :disabled="jurusanForm.processing"
                    >
                        {{ jurusanForm.processing ? 'Menyimpan...' : 'Tambah Jurusan' }}
                    </button>
                </form>

                <div style="margin-top: 20px; border-top: 1px solid #f1f5f9; padding-top: 16px;">
                    <p v-if="props.jurusan.length === 0" class="admin-empty">
                        Belum ada jurusan.
                    </p>

                    <div
                        v-for="item in props.jurusan"
                        :key="item.id"
                        class="admin-list-item"
                    >
                        <div>
                            <strong>{{ item.nama }}</strong>
                            <p>{{ item.kode || 'Tanpa kode' }}</p>
                        </div>

                        <button
                            type="button"
                            class="admin-btn small"
                            :class="item.aktif ? 'danger' : 'secondary'"
                            @click="toggleJurusan(item.id)"
                        >
                            {{ item.aktif ? 'Nonaktifkan' : 'Aktifkan' }}
                        </button>
                    </div>
                </div>
            </div>

            <!-- PENGUMUMAN -->
            <div class="admin-form-card">
                <h2>Tambah Pengumuman</h2>

                <form @submit.prevent="simpanPengumuman">
                    <div class="admin-form-row">
                        <label class="admin-label">Target Jurusan</label>

                        <select v-model="pengumumanForm.jurusan_id" class="admin-input">
                            <option value="">Semua Jurusan</option>

                            <option
                                v-for="item in jurusanAktif()"
                                :key="item.id"
                                :value="item.id"
                            >
                                {{ item.nama }}
                            </option>
                        </select>
                    </div>

                    <div class="admin-form-row">
                        <label class="admin-label">Judul</label>

                        <input
                            v-model="pengumumanForm.judul"
                            class="admin-input"
                            placeholder="Judul pengumuman..."
                            required
                        />

                        <div v-if="pengumumanForm.errors.judul" class="admin-error">
                            {{ pengumumanForm.errors.judul }}
                        </div>
                    </div>

                    <div class="admin-form-row">
                        <label class="admin-label">Kategori</label>

                        <input
                            v-model="pengumumanForm.kategori"
                            class="admin-input"
                            placeholder="Umum"
                        />
                    </div>

                    <div class="admin-form-row">
                        <label class="admin-label">Isi Pengumuman</label>

                        <textarea
                            v-model="pengumumanForm.isi"
                            class="admin-input admin-textarea"
                            placeholder="Tulis isi pengumuman..."
                            required
                        ></textarea>

                        <div v-if="pengumumanForm.errors.isi" class="admin-error">
                            {{ pengumumanForm.errors.isi }}
                        </div>
                    </div>

                    <button
                        class="admin-btn full"
                        type="submit"
                        :disabled="pengumumanForm.processing"
                    >
                        {{ pengumumanForm.processing ? 'Menyimpan...' : 'Publikasikan' }}
                    </button>
                </form>
            </div>

            <!-- MATERI & EVENT -->
            <div style="display: flex; flex-direction: column; gap: 16px;">
                <!-- MATERI -->
                <div class="admin-form-card">
                    <h2>Upload Materi</h2>

                    <form @submit.prevent="simpanMateri">
                        <div class="admin-form-row">
                            <label class="admin-label">Target Jurusan</label>

                            <select v-model="materiForm.jurusan_id" class="admin-input">
                                <option value="">Semua Jurusan</option>

                                <option
                                    v-for="item in jurusanAktif()"
                                    :key="item.id"
                                    :value="item.id"
                                >
                                    {{ item.nama }}
                                </option>
                            </select>
                        </div>

                        <div class="admin-form-row">
                            <label class="admin-label">Judul</label>

                            <input
                                v-model="materiForm.judul"
                                class="admin-input"
                                required
                            />

                            <div v-if="materiForm.errors.judul" class="admin-error">
                                {{ materiForm.errors.judul }}
                            </div>
                        </div>

                        <div class="admin-form-row">
                            <label class="admin-label">Mata Kuliah</label>

                            <input
                                v-model="materiForm.mata_kuliah"
                                class="admin-input"
                                required
                            />
                        </div>

                        <div class="admin-form-row">
                            <label class="admin-label">File (PDF/DOC/PPT)</label>

                            <input
                                class="admin-input admin-file-input"
                                type="file"
                                accept=".pdf,.doc,.docx,.ppt,.pptx"
                                required
                                @input="materiForm.file = $event.target.files[0]"
                            />

                            <div v-if="materiForm.errors.file" class="admin-error">
                                {{ materiForm.errors.file }}
                            </div>
                        </div>

                        <button
                            class="admin-btn full"
                            type="submit"
                            :disabled="materiForm.processing"
                        >
                            {{ materiForm.processing ? 'Mengupload...' : 'Upload Materi' }}
                        </button>
                    </form>
                </div>

                <!-- EVENT -->
                <div class="admin-form-card">
                    <h2>Tambah Event</h2>

                    <form @submit.prevent="simpanEvent">
                        <div class="admin-form-row">
                            <label class="admin-label">Nama Event</label>

                            <input
                                v-model="eventForm.nama_event"
                                class="admin-input"
                                required
                            />

                            <div v-if="eventForm.errors.nama_event" class="admin-error">
                                {{ eventForm.errors.nama_event }}
                            </div>
                        </div>

                        <div class="admin-form-row">
                            <label class="admin-label">Tanggal</label>

                            <input
                                v-model="eventForm.tanggal"
                                class="admin-input"
                                type="date"
                                required
                            />
                        </div>

                        <div class="admin-form-row">
                            <label class="admin-label">Lokasi</label>

                            <input
                                v-model="eventForm.lokasi"
                                class="admin-input"
                                placeholder="Aula Utama..."
                            />
                        </div>

                        <div class="admin-form-row">
                            <label class="admin-label">Link Pendaftaran</label>

                            <input
                                v-model="eventForm.link_pendaftaran"
                                class="admin-input"
                                type="url"
                                placeholder="https://..."
                            />
                        </div>

                        <div class="admin-form-row">
                            <label class="admin-label">Gambar Event</label>

                            <input
                                class="admin-input admin-file-input"
                                type="file"
                                accept="image/*"
                                @input="eventForm.gambar = $event.target.files[0]"
                            />
                        </div>

                        <button
                            class="admin-btn full"
                            type="submit"
                            :disabled="eventForm.processing"
                        >
                            {{ eventForm.processing ? 'Menyimpan...' : 'Simpan Event' }}
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>
