<script setup>
import { Head, useForm } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'

defineProps({ items: Array })

const form = useForm({
    nama_event: '',
    deskripsi: '',
    tanggal: '',
    lokasi: '',
    link_pendaftaran: '',
    gambar: null,
})

function submit() {
    form.post('/events', {
        forceFormData: true,
        onSuccess: () => form.reset(),
    })
}
</script>

<template>
    <Head title="Event" />
    <AppLayout>
        <h1 class="title">Event Kampus</h1>
        <p class="muted">Seminar, lomba, workshop, dan kegiatan mahasiswa.</p>

        <div class="grid grid-2" style="margin-top: 22px; align-items: start;">
            <div class="card">
                <h2>Tambah Event</h2>
                <form @submit.prevent="submit">
                    <div class="form-row">
                        <label class="label">Nama Event</label>
                        <input class="input" v-model="form.nama_event" />
                        <div v-if="form.errors.nama_event" class="error">{{ form.errors.nama_event }}</div>
                    </div>
                    <div class="form-row">
                        <label class="label">Tanggal</label>
                        <input class="input" v-model="form.tanggal" type="date" />
                    </div>
                    <div class="form-row">
                        <label class="label">Lokasi</label>
                        <input class="input" v-model="form.lokasi" />
                    </div>
                    <div class="form-row">
                        <label class="label">Link Pendaftaran</label>
                        <input class="input" v-model="form.link_pendaftaran" placeholder="https://..." />
                    </div>
                    <div class="form-row">
                        <label class="label">Deskripsi</label>
                        <textarea class="input" rows="4" v-model="form.deskripsi"></textarea>
                    </div>
                    <div class="form-row">
                        <label class="label">Gambar Event</label>
                        <input class="input" type="file" accept="image/*" @input="form.gambar = $event.target.files[0]" />
                        <div v-if="form.errors.gambar" class="error">{{ form.errors.gambar }}</div>
                    </div>
                    <button class="btn" :disabled="form.processing">Simpan</button>
                </form>
            </div>

            <div class="card">
                <h2>Daftar Event</h2>
                <p v-if="items.length === 0" class="muted">Belum ada data.</p>
                <div v-for="item in items" :key="item.id" style="padding: 14px 0; border-bottom: 1px solid #eeeef4;">
                    <img v-if="item.gambar_url" :src="item.gambar_url" style="width:100%;max-height:180px;object-fit:cover;border-radius:14px;margin-bottom:10px;" />
                    <b>{{ item.nama_event }}</b>
                    <p class="muted">{{ item.tanggal || '-' }} · {{ item.lokasi || '-' }}</p>
                    <p>{{ item.deskripsi }}</p>
                    <a v-if="item.link_pendaftaran" :href="item.link_pendaftaran" target="_blank" style="color:#3730a3;font-weight:700;">Daftar</a>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
