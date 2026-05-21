<script setup>
import { Head, useForm } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'

defineProps({ items: Array })

const form = useForm({
    judul: '',
    mata_kuliah: '',
    deskripsi: '',
    file: null,
})

function submit() {
    form.post('/materi', {
        forceFormData: true,
        onSuccess: () => form.reset(),
    })
}
</script>

<template>
    <Head title="Materi" />
    <AppLayout>
        <h1 class="title">Materi Kuliah</h1>
        <p class="muted">Upload PDF, DOC, atau PPT ke Supabase Storage.</p>

        <div class="grid grid-2" style="margin-top: 22px; align-items: start;">
            <div class="card">
                <h2>Upload Materi</h2>
                <form @submit.prevent="submit">
                    <div class="form-row">
                        <label class="label">Judul</label>
                        <input class="input" v-model="form.judul" />
                        <div v-if="form.errors.judul" class="error">{{ form.errors.judul }}</div>
                    </div>
                    <div class="form-row">
                        <label class="label">Mata Kuliah</label>
                        <input class="input" v-model="form.mata_kuliah" />
                        <div v-if="form.errors.mata_kuliah" class="error">{{ form.errors.mata_kuliah }}</div>
                    </div>
                    <div class="form-row">
                        <label class="label">Deskripsi</label>
                        <textarea class="input" rows="4" v-model="form.deskripsi"></textarea>
                    </div>
                    <div class="form-row">
                        <label class="label">File Materi</label>
                        <input class="input" type="file" accept=".pdf,.doc,.docx,.ppt,.pptx" @input="form.file = $event.target.files[0]" />
                        <div v-if="form.errors.file" class="error">{{ form.errors.file }}</div>
                    </div>
                    <button class="btn" :disabled="form.processing">Upload</button>
                </form>
            </div>

            <div class="card">
                <h2>Daftar Materi</h2>
                <p v-if="items.length === 0" class="muted">Belum ada data.</p>
                <table v-else class="table">
                    <thead>
                        <tr><th>Judul</th><th>Mata Kuliah</th><th>File</th></tr>
                    </thead>
                    <tbody>
                        <tr v-for="item in items" :key="item.id">
                            <td>
                                <b>{{ item.judul }}</b>
                                <div class="muted">{{ item.deskripsi }}</div>
                            </td>
                            <td>{{ item.mata_kuliah }}</td>
                            <td><a :href="item.file_url" target="_blank" style="color:#3730a3;font-weight:700;">Buka</a></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </AppLayout>
</template>
