<script setup>
import { Head, router, useForm } from '@inertiajs/vue3'
import { ref } from 'vue'
import AdminLayout from '@/Layouts/AdminLayout.vue'

const props = defineProps({
    jadwalReady: { type: Boolean, default: false },
    jurusan: { type: Array, default: () => [] },
    jadwal: { type: Array, default: () => [] },
})

const editingId = ref(null)

const form = useForm({
    mata_kuliah: '',
    dosen: '',
    hari: 'Senin',
    jam_mulai: '',
    jam_selesai: '',
    ruangan: '',
    semester: '',
    jurusan_id: '',
})

function resetForm() {
    editingId.value = null
    form.reset()
    form.clearErrors()
    form.hari = 'Senin'
}

function submit() {
    if (editingId.value) {
        form.patch(`/admin/jadwal/${editingId.value}`, {
            preserveScroll: true,
            onSuccess: () => resetForm(),
        })

        return
    }

    form.post('/admin/jadwal', {
        preserveScroll: true,
        onSuccess: () => resetForm(),
    })
}

function edit(item) {
    editingId.value = item.id
    form.mata_kuliah = item.mata_kuliah
    form.dosen = item.dosen
    form.hari = item.hari
    form.jam_mulai = String(item.jam_mulai).slice(0, 5)
    form.jam_selesai = String(item.jam_selesai).slice(0, 5)
    form.ruangan = item.ruangan || ''
    form.semester = item.semester || ''
    form.jurusan_id = item.jurusan_id || ''
}

function hapus(item) {
    if (!window.confirm(`Hapus jadwal ${item.mata_kuliah}?`)) {
        return
    }

    router.delete(`/admin/jadwal/${item.id}`, {
        preserveScroll: true,
    })
}
</script>

<template>
    <Head title="Jadwal Kuliah Admin" />

    <AdminLayout>
        <div class="jd-page">
            <div class="jd-header">
                <div>
                    <p class="jd-eyebrow">MANAJEMEN JADWAL</p>
                    <h1>Jadwal Kuliah</h1>
                    <p>Atur jadwal mata kuliah berdasarkan jurusan dan semester.</p>
                </div>
            </div>

            <div v-if="!jadwalReady" class="jd-alert">
                Tabel <b>jadwal_kuliah</b> belum dibuat. Jalankan SQL jadwal terlebih dahulu di Supabase.
            </div>

            <div v-if="$page.props.errors?.jadwal" class="jd-alert danger">
                {{ $page.props.errors.jadwal }}
            </div>

            <div class="jd-grid">
                <section class="jd-card form-card">
                    <h2>{{ editingId ? 'Edit Jadwal' : 'Tambah Jadwal' }}</h2>

                    <form @submit.prevent="submit">
                        <label>Mata Kuliah</label>
                        <input v-model="form.mata_kuliah" required placeholder="Algoritma dan Pemrograman" />

                        <label>Dosen</label>
                        <input v-model="form.dosen" required placeholder="Nama dosen" />

                        <div class="two">
                            <div>
                                <label>Hari</label>
                                <select v-model="form.hari">
                                    <option>Senin</option>
                                    <option>Selasa</option>
                                    <option>Rabu</option>
                                    <option>Kamis</option>
                                    <option>Jumat</option>
                                    <option>Sabtu</option>
                                </select>
                            </div>

                            <div>
                                <label>Semester</label>
                                <input v-model="form.semester" type="number" min="1" max="14" />
                            </div>
                        </div>

                        <div class="two">
                            <div>
                                <label>Jam Mulai</label>
                                <input v-model="form.jam_mulai" type="time" required />
                            </div>

                            <div>
                                <label>Jam Selesai</label>
                                <input v-model="form.jam_selesai" type="time" required />
                            </div>
                        </div>

                        <label>Ruangan</label>
                        <input v-model="form.ruangan" placeholder="Lab Komputer 1" />

                        <label>Target Jurusan</label>
                        <select v-model="form.jurusan_id">
                            <option value="">Semua Jurusan</option>
                            <option v-for="item in jurusan" :key="item.id" :value="item.id">
                                {{ item.nama }}
                            </option>
                        </select>

                        <div v-if="Object.keys(form.errors).length" class="form-error">
                            <p v-for="(error, key) in form.errors" :key="key">{{ error }}</p>
                        </div>

                        <div class="actions">
                            <button class="primary" type="submit" :disabled="form.processing || !jadwalReady">
                                {{ editingId ? 'Simpan Perubahan' : 'Tambah Jadwal' }}
                            </button>

                            <button
                                v-if="editingId"
                                class="secondary"
                                type="button"
                                @click="resetForm"
                            >
                                Batal
                            </button>
                        </div>
                    </form>
                </section>

                <section class="jd-card list-card">
                    <div class="section-head">
                        <h2>Daftar Jadwal</h2>
                        <span>{{ jadwal.length }} jadwal</span>
                    </div>

                    <p v-if="jadwal.length === 0" class="empty">
                        Belum ada jadwal kuliah.
                    </p>

                    <div v-for="item in jadwal" :key="item.id" class="schedule">
                        <div class="day">
                            <strong>{{ item.hari }}</strong>
                            <span>{{ String(item.jam_mulai).slice(0, 5) }} - {{ String(item.jam_selesai).slice(0, 5) }}</span>
                        </div>

                        <div class="detail">
                            <strong>{{ item.mata_kuliah }}</strong>
                            <p>{{ item.dosen }} · {{ item.ruangan || 'Ruangan belum diset' }}</p>
                            <small>
                                {{ item.target_jurusan || 'Semua Jurusan' }}
                                <template v-if="item.semester"> · Semester {{ item.semester }}</template>
                            </small>
                        </div>

                        <div class="item-actions">
                            <button type="button" class="icon" title="Edit" @click="edit(item)">
                                ✎
                            </button>

                            <button type="button" class="icon delete" title="Hapus" @click="hapus(item)">
                                🗑
                            </button>
                        </div>
                    </div>
                </section>
            </div>
        </div>
    </AdminLayout>
</template>

<style scoped>
.jd-page {
    display: flex;
    flex-direction: column;
    gap: 18px;
}

.jd-eyebrow {
    margin: 0;
    color: #0f9488;
    font-size: 11px;
    font-weight: 800;
    letter-spacing: .1em;
}

.jd-header h1 {
    margin: 5px 0 7px;
    color: #0f172a;
    font-size: 30px;
}

.jd-header p {
    margin: 0;
    color: #64748b;
}

.jd-alert {
    padding: 13px 15px;
    border: 1px solid #fde68a;
    border-radius: 12px;
    background: #fffbeb;
    color: #92400e;
    font-size: 13px;
}

.jd-alert.danger {
    border-color: #fecaca;
    background: #fef2f2;
    color: #b91c1c;
}

.jd-grid {
    display: grid;
    grid-template-columns: 380px minmax(0, 1fr);
    gap: 16px;
}

.jd-card {
    padding: 19px;
    border: 1px solid #e2e8f0;
    border-radius: 17px;
    background: white;
}

.jd-card h2 {
    margin: 0 0 16px;
    color: #0f172a;
    font-size: 18px;
}

.form-card form {
    display: flex;
    flex-direction: column;
    gap: 8px;
}

.form-card label {
    margin-top: 5px;
    color: #475569;
    font-size: 12px;
    font-weight: 700;
}

.form-card input,
.form-card select {
    width: 100%;
    padding: 10px 11px;
    border: 1px solid #cbd5e1;
    border-radius: 10px;
    font: inherit;
    font-size: 13px;
}

.two {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 9px;
}

.two div {
    display: flex;
    flex-direction: column;
    gap: 8px;
}

.actions {
    display: flex;
    gap: 8px;
    margin-top: 12px;
}

.actions button {
    padding: 10px 12px;
    border: 0;
    border-radius: 10px;
    cursor: pointer;
    font-weight: 700;
}

.actions .primary {
    background: #0f9488;
    color: white;
}

.actions .secondary {
    background: #f1f5f9;
    color: #475569;
}

.form-error {
    margin-top: 8px;
    padding: 8px 10px;
    border-radius: 9px;
    background: #fef2f2;
    color: #dc2626;
    font-size: 12px;
}

.form-error p {
    margin: 3px 0;
}

.section-head {
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.section-head span {
    color: #64748b;
    font-size: 13px;
}

.empty {
    color: #64748b;
    font-size: 14px;
}

.schedule {
    display: flex;
    align-items: center;
    gap: 14px;
    margin-top: 12px;
    padding: 13px;
    border: 1px solid #e2e8f0;
    border-radius: 13px;
}

.day {
    width: 108px;
    flex-shrink: 0;
    padding: 10px;
    border-radius: 11px;
    background: #ecfdf5;
    color: #047857;
}

.day strong,
.day span {
    display: block;
}

.day span {
    margin-top: 5px;
    font-size: 11px;
}

.detail {
    min-width: 0;
    flex: 1;
}

.detail strong {
    color: #0f172a;
}

.detail p,
.detail small {
    margin: 4px 0 0;
    color: #64748b;
    font-size: 12px;
}

.item-actions {
    display: flex;
    gap: 6px;
}

.icon {
    width: 34px;
    height: 34px;
    border: 1px solid #e2e8f0;
    border-radius: 9px;
    background: white;
    cursor: pointer;
}

.icon.delete {
    color: #dc2626;
}

@media (max-width: 1000px) {
    .jd-grid {
        grid-template-columns: 1fr;
    }
}
</style>
