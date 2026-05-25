<script setup>
import { Head, router, useForm } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'

const props = defineProps({
    jurusan: { type: Array, default: () => [] },
    pengumuman: { type: Array, default: () => [] },
    materi: { type: Array, default: () => [] },
    events: { type: Array, default: () => [] },
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

function formatDate(value) {
    if (!value) return '-'

    return new Intl.DateTimeFormat('id-ID', {
        day: 'numeric',
        month: 'short',
        year: 'numeric',
    }).format(new Date(value))
}
</script>

<template>
    <Head title="Akademik Admin" />

    <AdminLayout>
        <div class="ak-page">
            <div class="ak-header">
                <div>
                    <p class="ak-eyebrow">MANAJEMEN AKADEMIK</p>
                    <h1>Akademik</h1>
                    <p>Kelola jurusan, pengumuman, materi kuliah, dan event kampus.</p>
                </div>
            </div>

            <div class="ak-form-grid">
                <!-- JURUSAN -->
                <section class="ak-card">
                    <h2>Kelola Jurusan</h2>

                    <form @submit.prevent="simpanJurusan">
                        <label>Nama Jurusan</label>
                        <input v-model="jurusanForm.nama" placeholder="Ilmu Komputer" required />
                        <span v-if="jurusanForm.errors.nama" class="error">{{ jurusanForm.errors.nama }}</span>

                        <label>Kode</label>
                        <input v-model="jurusanForm.kode" placeholder="ILKOM" />
                        <span v-if="jurusanForm.errors.kode" class="error">{{ jurusanForm.errors.kode }}</span>

                        <button type="submit" :disabled="jurusanForm.processing">
                            {{ jurusanForm.processing ? 'Menyimpan...' : 'Tambah Jurusan' }}
                        </button>
                    </form>

                    <div class="ak-small-list">
                        <div v-for="item in jurusan" :key="item.id" class="ak-row">
                            <div>
                                <strong>{{ item.nama }}</strong>
                                <small>{{ item.kode || '-' }}</small>
                            </div>

                            <button
                                type="button"
                                class="secondary"
                                @click="toggleJurusan(item.id)"
                            >
                                {{ item.aktif ? 'Nonaktifkan' : 'Aktifkan' }}
                            </button>
                        </div>
                    </div>
                </section>

                <!-- PENGUMUMAN -->
                <section class="ak-card">
                    <h2>Tambah Pengumuman</h2>

                    <form @submit.prevent="simpanPengumuman">
                        <label>Target Jurusan</label>
                        <select v-model="pengumumanForm.jurusan_id">
                            <option value="">Semua Jurusan</option>
                            <option v-for="item in jurusanAktif()" :key="item.id" :value="item.id">
                                {{ item.nama }}
                            </option>
                        </select>

                        <label>Judul</label>
                        <input v-model="pengumumanForm.judul" required />

                        <label>Kategori</label>
                        <input v-model="pengumumanForm.kategori" />

                        <label>Isi</label>
                        <textarea v-model="pengumumanForm.isi" required></textarea>

                        <button type="submit" :disabled="pengumumanForm.processing">
                            {{ pengumumanForm.processing ? 'Menyimpan...' : 'Publikasikan' }}
                        </button>
                    </form>
                </section>

                <!-- MATERI -->
                <section class="ak-card">
                    <h2>Upload Materi</h2>

                    <form @submit.prevent="simpanMateri">
                        <label>Target Jurusan</label>
                        <select v-model="materiForm.jurusan_id">
                            <option value="">Semua Jurusan</option>
                            <option v-for="item in jurusanAktif()" :key="item.id" :value="item.id">
                                {{ item.nama }}
                            </option>
                        </select>

                        <label>Judul</label>
                        <input v-model="materiForm.judul" required />

                        <label>Mata Kuliah</label>
                        <input v-model="materiForm.mata_kuliah" required />

                        <label>Deskripsi</label>
                        <textarea v-model="materiForm.deskripsi"></textarea>

                        <label>File Materi</label>
                        <input
                            type="file"
                            accept=".pdf,.doc,.docx,.ppt,.pptx"
                            required
                            @change="materiForm.file = $event.target.files[0]"
                        />

                        <button type="submit" :disabled="materiForm.processing">
                            {{ materiForm.processing ? 'Mengupload...' : 'Upload Materi' }}
                        </button>
                    </form>
                </section>

                <!-- EVENT -->
                <section class="ak-card">
                    <h2>Tambah Event</h2>

                    <form @submit.prevent="simpanEvent">
                        <label>Target Jurusan</label>
                        <select v-model="eventForm.jurusan_id">
                            <option value="">Semua Jurusan</option>
                            <option v-for="item in jurusanAktif()" :key="item.id" :value="item.id">
                                {{ item.nama }}
                            </option>
                        </select>

                        <label>Nama Event</label>
                        <input v-model="eventForm.nama_event" required />

                        <div class="ak-two">
                            <div>
                                <label>Tanggal</label>
                                <input v-model="eventForm.tanggal" type="date" required />
                            </div>

                            <div>
                                <label>Lokasi</label>
                                <input v-model="eventForm.lokasi" />
                            </div>
                        </div>

                        <label>Deskripsi</label>
                        <textarea v-model="eventForm.deskripsi"></textarea>

                        <label>Link Pendaftaran</label>
                        <input v-model="eventForm.link_pendaftaran" type="url" />

                        <label>Gambar</label>
                        <input
                            type="file"
                            accept="image/*"
                            @change="eventForm.gambar = $event.target.files[0]"
                        />

                        <button type="submit" :disabled="eventForm.processing">
                            {{ eventForm.processing ? 'Menyimpan...' : 'Simpan Event' }}
                        </button>
                    </form>
                </section>
            </div>

            <div class="ak-table-grid">
                <section class="ak-card">
                    <div class="section-head">
                        <h2>Pengumuman Terbaru</h2>
                        <span>{{ pengumuman.length }} data</span>
                    </div>

                    <div v-for="item in pengumuman" :key="item.id" class="content-row">
                        <strong>{{ item.judul }}</strong>
                        <p>{{ item.kategori || 'Umum' }} · {{ targetLabel(item) }}</p>
                        <small>{{ formatDate(item.created_at) }}</small>
                    </div>
                </section>

                <section class="ak-card">
                    <div class="section-head">
                        <h2>Materi Terbaru</h2>
                        <span>{{ materi.length }} data</span>
                    </div>

                    <div v-for="item in materi" :key="item.id" class="content-row">
                        <strong>{{ item.judul }}</strong>
                        <p>{{ item.mata_kuliah }} · {{ targetLabel(item) }}</p>
                        <small>{{ formatDate(item.created_at) }}</small>
                    </div>
                </section>

                <section class="ak-card">
                    <div class="section-head">
                        <h2>Event Terbaru</h2>
                        <span>{{ events.length }} data</span>
                    </div>

                    <div v-for="item in events" :key="item.id" class="content-row">
                        <strong>{{ item.nama_event }}</strong>
                        <p>{{ item.lokasi || '-' }} · {{ targetLabel(item) }}</p>
                        <small>{{ formatDate(item.tanggal) }}</small>
                    </div>
                </section>
            </div>
        </div>
    </AdminLayout>
</template>

<style scoped>
    .ak-page {
    display: flex;
    flex-direction: column;
    gap: 20px;

    --ak-bg: var(--bg-primary, #f8fafc);
    --ak-card: var(--bg-card, #ffffff);
    --ak-border: var(--border-color, #e2e8f0);
    --ak-text: var(--text-primary, #0f172a);
    --ak-text-soft: var(--text-secondary, #64748b);
    --ak-input: var(--bg-input, #ffffff);

}

.ak-header h1 {
    margin: 4px 0 6px;
  color: var(--ak-text);
    font-size: 30px;
}

.ak-header p:not(.ak-eyebrow) {
    margin: 0;
   color: var(--ak-text-soft);
}

.ak-eyebrow {
    margin: 0;
    color: #0f9488;
    font-size: 11px;
    font-weight: 800;
    letter-spacing: .1em;
}

.ak-form-grid {
    display: grid;
    grid-template-columns: repeat(2, minmax(0, 1fr));
    gap: 16px;
}

.ak-table-grid {
    display: grid;
    grid-template-columns: repeat(3, minmax(0, 1fr));
    gap: 16px;
}

.ak-card {
    padding: 18px;
   border: 1px solid var(--ak-border);
    border-radius: 16px;
    background: var(--ak-card);
}

.ak-card h2 {
    margin: 0 0 15px;
   color: var(--ak-text);
    font-size: 17px;
}

.ak-card form {
    display: flex;
    flex-direction: column;
    gap: 8px;
}

.ak-card label {
    margin-top: 5px;
    color: #475569;
    font-size: 12px;
    font-weight: 700;
}

.ak-card input,
.ak-card select,
.ak-card textarea {
    width: 100%;
    padding: 10px 12px;
    border: 1px solid var(--ak-border);
    border-radius: 10px;
    font: inherit;
    font-size: 13px;

    background: var(--ak-input);
    color: var(--ak-text);
}

.ak-card textarea {
    min-height: 82px;
    resize: vertical;
}

.ak-card button {
    margin-top: 8px;
    padding: 10px 13px;
    border: 0;
    border-radius: 10px;
    background: #0f9488;
    color: white;
    cursor: pointer;
    font-weight: 700;
}

.ak-card button.secondary {
    margin: 0;
    padding: 7px 9px;
    background: #eef2ff;
    color: #4338ca;
    font-size: 11px;
}

.ak-two {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 10px;
}

.ak-small-list {
    margin-top: 16px;
    padding-top: 12px;
    border-top: 1px solid #e2e8f0;
}

.ak-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 8px;
    padding: 9px 0;
}

.ak-row small {
    display: block;
   color: var(--ak-text-soft);
}

.section-head {
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.section-head span {
   color: var(--ak-text-soft);
    font-size: 12px;
}

.content-row {
    padding: 11px 0;
    border-top: 1px solid #f1f5f9;
}

.content-row p,
.content-row small {
    margin: 4px 0 0;
   color: var(--ak-text-soft);
    font-size: 12px;
}

.error {
    color: #dc2626;
    font-size: 12px;
}

@media (max-width: 1000px) {
    .ak-form-grid,
    .ak-table-grid {
        grid-template-columns: 1fr;
    }
}
</style>
