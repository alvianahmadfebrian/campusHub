<script setup>
import { Head, useForm } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'

defineProps({ items: Array })

const form = useForm({
    judul: '',
    kategori: '',
    isi: '',
})

function submit() {
    form.post('/pengumuman', {
        onSuccess: () => form.reset(),
    })
}
</script>

<template>
    <Head title="Pengumuman" />
    <AppLayout>
        <h1 class="title">Pengumuman</h1>
        <p class="muted">Info kampus, jadwal, kelas kosong, dan pengumuman akademik.</p>

        <div class="grid grid-2" style="margin-top: 22px; align-items: start;">
            <div class="card">
                <h2>Tambah Pengumuman</h2>
                <form @submit.prevent="submit">
                    <div class="form-row">
                        <label class="label">Judul</label>
                        <input class="input" v-model="form.judul" />
                        <div v-if="form.errors.judul" class="error">{{ form.errors.judul }}</div>
                    </div>
                    <div class="form-row">
                        <label class="label">Kategori</label>
                        <input class="input" v-model="form.kategori" placeholder="Akademik / Kampus / Ujian" />
                    </div>
                    <div class="form-row">
                        <label class="label">Isi</label>
                        <textarea class="input" rows="5" v-model="form.isi"></textarea>
                        <div v-if="form.errors.isi" class="error">{{ form.errors.isi }}</div>
                    </div>
                    <button class="btn" :disabled="form.processing">Simpan</button>
                </form>
            </div>

            <div class="card">
                <h2>Daftar Pengumuman</h2>
                <p v-if="items.length === 0" class="muted">Belum ada data.</p>
                <div v-for="item in items" :key="item.id" style="padding: 14px 0; border-bottom: 1px solid #eeeef4;">
                    <b>{{ item.judul }}</b>
                    <p class="muted">{{ item.kategori || 'Umum' }}</p>
                    <p>{{ item.isi }}</p>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
