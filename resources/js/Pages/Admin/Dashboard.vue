<script setup>
import { Head, Link, router, useForm } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'

const props = defineProps({
    stats: {
        type: Object,
        default: () => ({}),
    },
    jurusan: {
        type: Array,
        default: () => [],
    },
    mahasiswaTerbaru: {
        type: Array,
        default: () => [],
    },
    pengumumanTerbaru: {
        type: Array,
        default: () => [],
    },
    materiTerbaru: {
        type: Array,
        default: () => [],
    },
    eventsTerbaru: {
        type: Array,
        default: () => [],
    },
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
    router.patch(`/admin/jurusan/${id}/toggle`, {}, { preserveScroll: true })
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

function formatTanggal(tanggal) {
    if (!tanggal) return '-'

    return new Intl.DateTimeFormat('id-ID', {
        day: 'numeric',
        month: 'short',
        year: 'numeric',
    }).format(new Date(tanggal))
}
</script>

<template>
    <Head title="Dashboard Admin" />

    <AppLayout>
        <header class="page-header">
            <div>
                <span class="eyebrow">Administrator</span>
                <h1 class="title">Dashboard Admin</h1>
                <p class="muted">Kelola jurusan dan kirim konten untuk semua mahasiswa atau program studi tertentu.</p>
            </div>
            <div class="header-actions">
                <Link href="/pengumuman" class="btn secondary">Lihat Konten</Link>
            </div>
        </header>

        <div class="grid grid-5 section-gap">
            <article class="card stat-card">
                <p class="muted">Mahasiswa</p>
                <h2>{{ stats.mahasiswa ?? 0 }}</h2>
            </article>
            <article class="card stat-card">
                <p class="muted">Jurusan Aktif</p>
                <h2>{{ stats.jurusan ?? 0 }}</h2>
            </article>
            <article class="card stat-card">
                <p class="muted">Pengumuman</p>
                <h2>{{ stats.pengumuman ?? 0 }}</h2>
            </article>
            <article class="card stat-card">
                <p class="muted">Materi</p>
                <h2>{{ stats.materi ?? 0 }}</h2>
            </article>
            <article class="card stat-card">
                <p class="muted">Event</p>
                <h2>{{ stats.events ?? 0 }}</h2>
            </article>
        </div>

        <div class="grid grid-2 section-gap">
            <section class="card">
                <h2>Tambah Jurusan</h2>
                <form @submit.prevent="simpanJurusan">
                    <div class="grid grid-2">
                        <div class="form-row">
                            <label class="label">Nama Jurusan</label>
                            <input v-model="jurusanForm.nama" class="input" placeholder="Teknik Informatika" required />
                            <div v-if="jurusanForm.errors.nama" class="error">{{ jurusanForm.errors.nama }}</div>
                        </div>
                        <div class="form-row">
                            <label class="label">Kode</label>
                            <input v-model="jurusanForm.kode" class="input" placeholder="TI" />
                            <div v-if="jurusanForm.errors.kode" class="error">{{ jurusanForm.errors.kode }}</div>
                        </div>
                    </div>
                    <button class="btn" type="submit" :disabled="jurusanForm.processing">
                        {{ jurusanForm.processing ? 'Menyimpan...' : 'Tambah Jurusan' }}
                    </button>
                </form>
            </section>

            <section class="card">
                <div class="card-heading">
                    <h2>Daftar Jurusan</h2>
                    <span class="badge">{{ props.jurusan.length }} data</span>
                </div>
                <p v-if="props.jurusan.length === 0" class="muted">Belum ada jurusan. Tambahkan jurusan agar mahasiswa bisa register.</p>
                <div v-for="item in props.jurusan" :key="item.id" class="list-item">
                    <div>
                        <strong>{{ item.nama }}</strong>
                        <p class="muted">{{ item.kode || 'Tanpa kode' }}</p>
                    </div>
                    <button
                        type="button"
                        class="btn small"
                        :class="item.aktif ? 'secondary' : ''"
                        @click="toggleJurusan(item.id)"
                    >
                        {{ item.aktif ? 'Nonaktifkan' : 'Aktifkan' }}
                    </button>
                </div>
            </section>
        </div>

        <div class="grid grid-3 section-gap admin-forms">
            <section class="card">
                <h2>Tambah Pengumuman</h2>
                <form @submit.prevent="simpanPengumuman">
                    <div class="form-row">
                        <label class="label">Target Jurusan</label>
                        <select v-model="pengumumanForm.jurusan_id" class="input">
                            <option value="">Semua Jurusan</option>
                            <option v-for="item in jurusanAktif()" :key="item.id" :value="item.id">{{ item.nama }}</option>
                        </select>
                    </div>
                    <div class="form-row">
                        <label class="label">Judul</label>
                        <input v-model="pengumumanForm.judul" class="input" required />
                        <div v-if="pengumumanForm.errors.judul" class="error">{{ pengumumanForm.errors.judul }}</div>
                    </div>
                    <div class="form-row">
                        <label class="label">Kategori</label>
                        <input v-model="pengumumanForm.kategori" class="input" />
                    </div>
                    <div class="form-row">
                        <label class="label">Isi</label>
                        <textarea v-model="pengumumanForm.isi" class="input textarea" required></textarea>
                        <div v-if="pengumumanForm.errors.isi" class="error">{{ pengumumanForm.errors.isi }}</div>
                    </div>
                    <button class="btn full" :disabled="pengumumanForm.processing">Simpan</button>
                </form>
            </section>

            <section class="card">
                <h2>Upload Materi</h2>
                <form @submit.prevent="simpanMateri">
                    <div class="form-row">
                        <label class="label">Target Jurusan</label>
                        <select v-model="materiForm.jurusan_id" class="input">
                            <option value="">Semua Jurusan</option>
                            <option v-for="item in jurusanAktif()" :key="item.id" :value="item.id">{{ item.nama }}</option>
                        </select>
                    </div>
                    <div class="form-row">
                        <label class="label">Judul</label>
                        <input v-model="materiForm.judul" class="input" required />
                        <div v-if="materiForm.errors.judul" class="error">{{ materiForm.errors.judul }}</div>
                    </div>
                    <div class="form-row">
                        <label class="label">Mata Kuliah</label>
                        <input v-model="materiForm.mata_kuliah" class="input" required />
                    </div>
                    <div class="form-row">
                        <label class="label">Deskripsi</label>
                        <textarea v-model="materiForm.deskripsi" class="input textarea"></textarea>
                    </div>
                    <div class="form-row">
                        <label class="label">File</label>
                        <input class="input file-input" type="file" accept=".pdf,.doc,.docx,.ppt,.pptx" @input="materiForm.file = $event.target.files[0]" required />
                        <div v-if="materiForm.errors.file" class="error">{{ materiForm.errors.file }}</div>
                    </div>
                    <button class="btn full" :disabled="materiForm.processing">Upload</button>
                </form>
            </section>

            <section class="card">
                <h2>Tambah Event</h2>
                <form @submit.prevent="simpanEvent">
                    <div class="form-row">
                        <label class="label">Target Jurusan</label>
                        <select v-model="eventForm.jurusan_id" class="input">
                            <option value="">Semua Jurusan</option>
                            <option v-for="item in jurusanAktif()" :key="item.id" :value="item.id">{{ item.nama }}</option>
                        </select>
                    </div>
                    <div class="form-row">
                        <label class="label">Nama Event</label>
                        <input v-model="eventForm.nama_event" class="input" required />
                        <div v-if="eventForm.errors.nama_event" class="error">{{ eventForm.errors.nama_event }}</div>
                    </div>
                    <div class="form-row">
                        <label class="label">Tanggal</label>
                        <input v-model="eventForm.tanggal" class="input" type="date" required />
                    </div>
                    <div class="form-row">
                        <label class="label">Lokasi</label>
                        <input v-model="eventForm.lokasi" class="input" />
                    </div>
                    <div class="form-row">
                        <label class="label">Link Pendaftaran</label>
                        <input v-model="eventForm.link_pendaftaran" class="input" type="url" placeholder="https://..." />
                    </div>
                    <div class="form-row">
                        <label class="label">Gambar</label>
                        <input class="input file-input" type="file" accept="image/*" @input="eventForm.gambar = $event.target.files[0]" />
                        <div v-if="eventForm.errors.gambar" class="error">{{ eventForm.errors.gambar }}</div>
                    </div>
                    <div class="form-row">
                        <label class="label">Deskripsi</label>
                        <textarea v-model="eventForm.deskripsi" class="input textarea"></textarea>
                    </div>
                    <button class="btn full" :disabled="eventForm.processing">Simpan</button>
                </form>
            </section>
        </div>

        <div class="grid grid-4 section-gap">
            <section class="card">
                <div class="card-heading"><h2>Mahasiswa Baru</h2></div>
                <p v-if="mahasiswaTerbaru.length === 0" class="muted">Belum ada mahasiswa.</p>
                <div v-for="mahasiswa in mahasiswaTerbaru" :key="mahasiswa.id" class="list-item">
                    <div>
                        <strong>{{ mahasiswa.nama }}</strong>
                        <p class="muted">{{ mahasiswa.nim || '-' }} · {{ mahasiswa.jurusan_nama || '-' }}</p>
                    </div>
                </div>
            </section>

            <section class="card">
                <div class="card-heading"><h2>Pengumuman</h2><Link href="/pengumuman" class="text-link">Semua</Link></div>
                <p v-if="pengumumanTerbaru.length === 0" class="muted">Belum ada pengumuman.</p>
                <div v-for="item in pengumumanTerbaru" :key="item.id" class="list-item stacked">
                    <strong>{{ item.judul }}</strong>
                    <span class="badge neutral">{{ targetLabel(item) }}</span>
                </div>
            </section>

            <section class="card">
                <div class="card-heading"><h2>Materi Terbaru</h2><Link href="/materi" class="text-link">Semua</Link></div>
                <p v-if="materiTerbaru.length === 0" class="muted">Belum ada materi.</p>
                <div v-for="item in materiTerbaru" :key="item.id" class="list-item stacked">
                    <strong>{{ item.judul }}</strong>
                    <span class="badge neutral">{{ targetLabel(item) }}</span>
                </div>
            </section>

            <section class="card">
                <div class="card-heading"><h2>Event Mendatang</h2><Link href="/events" class="text-link">Semua</Link></div>
                <p v-if="eventsTerbaru.length === 0" class="muted">Belum ada event.</p>
                <div v-for="event in eventsTerbaru" :key="event.id" class="list-item stacked">
                    <strong>{{ event.nama_event }}</strong>
                    <p class="muted">{{ formatTanggal(event.tanggal) }}</p>
                    <span class="badge neutral">{{ targetLabel(event) }}</span>
                </div>
            </section>
        </div>
    </AppLayout>
</template>
