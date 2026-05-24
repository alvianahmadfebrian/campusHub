<script setup>
import { Head, Link, router, useForm } from '@inertiajs/vue3'
import { ref, watch } from 'vue'
import AppLayout from '@/Layouts/AppLayout.vue'

const props = defineProps({
    currentFolder: {
        type: Object,
        default: null,
    },
    breadcrumbs: {
        type: Array,
        default: () => [],
    },
    folders: {
        type: Array,
        default: () => [],
    },
    files: {
        type: Array,
        default: () => [],
    },
    limits: {
        type: Object,
        default: () => ({
            maxUploadMb: 50,
        }),
    },
})

const fileInput = ref(null)

const createFolderForm = useForm({
    nama: '',
    parent_id: props.currentFolder?.id || null,
})

const uploadForm = useForm({
    folder_id: props.currentFolder?.id || null,
    file: null,
})

watch(
    () => props.currentFolder?.id,
    (folderId) => {
        createFolderForm.parent_id = folderId || null
        uploadForm.folder_id = folderId || null
    }
)

function createFolder() {
    createFolderForm.post('/drive/folders', {
        preserveScroll: true,
        onSuccess: () => {
            createFolderForm.reset('nama')
            createFolderForm.parent_id = props.currentFolder?.id || null
        },
    })
}

function handleFileChange(event) {
    uploadForm.file = event.target.files?.[0] || null
}

function uploadFile() {
    uploadForm.post('/drive/files', {
        forceFormData: true,
        preserveScroll: true,
        onSuccess: () => {
            uploadForm.reset('file')
            uploadForm.folder_id = props.currentFolder?.id || null

            if (fileInput.value) {
                fileInput.value.value = ''
            }
        },
    })
}

function renameFolder(folder) {
    const nama = window.prompt('Nama folder baru:', folder.nama)

    if (!nama || nama.trim() === '' || nama.trim() === folder.nama) {
        return
    }

    router.patch(
        `/drive/folders/${folder.id}`,
        {
            nama: nama.trim(),
        },
        {
            preserveScroll: true,
        }
    )
}

function toggleFolder(folder) {
    router.patch(
        `/drive/folders/${folder.id}`,
        {
            is_public: !folder.is_public,
        },
        {
            preserveScroll: true,
        }
    )
}

function deleteFolder(folder) {
    const yakin = window.confirm(
        `Hapus folder "${folder.nama}" beserta seluruh file dan subfolder di dalamnya?`
    )

    if (!yakin) {
        return
    }

    router.delete(`/drive/folders/${folder.id}`)
}

function renameFile(file) {
    const nama = window.prompt('Nama file baru:', file.nama_tampilan)

    if (!nama || nama.trim() === '' || nama.trim() === file.nama_tampilan) {
        return
    }

    router.patch(
        `/drive/files/${file.id}`,
        {
            nama_tampilan: nama.trim(),
        },
        {
            preserveScroll: true,
        }
    )
}

function toggleFile(file) {
    router.patch(
        `/drive/files/${file.id}`,
        {
            is_public: !file.is_public,
        },
        {
            preserveScroll: true,
        }
    )
}

function deleteFile(file) {
    const yakin = window.confirm(`Hapus file "${file.nama_tampilan}"?`)

    if (!yakin) {
        return
    }

    router.delete(`/drive/files/${file.id}`, {
        preserveScroll: true,
    })
}

async function copyLink(url) {
    if (!url) {
        return
    }

    try {
        await navigator.clipboard.writeText(url)
        window.alert('Link publik berhasil disalin.')
    } catch (error) {
        window.prompt('Salin link berikut:', url)
    }
}

function ukuran(bytes) {
    if (!bytes) {
        return '0 KB'
    }

    const units = ['B', 'KB', 'MB', 'GB']
    const index = Math.min(
        Math.floor(Math.log(bytes) / Math.log(1024)),
        units.length - 1
    )

    return `${(bytes / Math.pow(1024, index)).toFixed(index === 0 ? 0 : 1)} ${units[index]}`
}
</script>

<template>
    <Head title="Drive" />

    <AppLayout>
        <div class="drive-page">
            <!-- HEADER -->
            <header class="drive-header">
                <div>
                    <p class="drive-eyebrow">Cloud Storage</p>
                    <h1 class="drive-title">Drive Saya</h1>
                    <p class="drive-subtitle">
                        Buat folder, simpan file, dan bagikan link publik tanpa membuka akses file private.
                    </p>
                </div>
            </header>

            <!-- BREADCRUMB -->
            <nav class="drive-breadcrumb" aria-label="Breadcrumb">
                <Link href="/drive" class="drive-breadcrumb-link">
                    Drive Saya
                </Link>

                <template v-for="crumb in breadcrumbs" :key="crumb.url">
                    <span class="drive-breadcrumb-divider">/</span>
                    <Link :href="crumb.url" class="drive-breadcrumb-link">
                        {{ crumb.nama }}
                    </Link>
                </template>
            </nav>

            <!-- ERROR -->
            <div v-if="$page.props.errors?.drive" class="drive-alert-error">
                {{ $page.props.errors.drive }}
            </div>

            <!-- FORM CREATE & UPLOAD -->
            <div class="drive-tools-grid">
                <!-- CREATE FOLDER -->
                <section class="drive-card">
                    <div class="drive-card-heading">
                        <div class="drive-heading-icon folder">
                            <svg viewBox="0 0 24 24" fill="none">
                                <path
                                    d="M3 7.5A2.5 2.5 0 0 1 5.5 5H10l2 2h6.5A2.5 2.5 0 0 1 21 9.5v7A2.5 2.5 0 0 1 18.5 19h-13A2.5 2.5 0 0 1 3 16.5v-9Z"
                                    stroke="currentColor"
                                    stroke-width="1.8"
                                    stroke-linejoin="round"
                                />
                            </svg>
                        </div>
                        <h2>Buat Folder</h2>
                    </div>

                    <form @submit.prevent="createFolder">
                        <div class="drive-form-row">
                            <label class="drive-label" for="folder-name">
                                Nama Folder
                            </label>

                            <input
                                id="folder-name"
                                v-model="createFolderForm.nama"
                                class="drive-input"
                                type="text"
                                placeholder="Contoh: Modul Semester 2"
                                required
                            />

                            <div v-if="createFolderForm.errors.nama" class="drive-error">
                                {{ createFolderForm.errors.nama }}
                            </div>
                        </div>

                        <button
                            class="drive-primary-button"
                            type="submit"
                            :disabled="createFolderForm.processing"
                        >
                            <svg viewBox="0 0 24 24" fill="none">
                                <path
                                    d="M12 5v14M5 12h14"
                                    stroke="currentColor"
                                    stroke-width="2"
                                    stroke-linecap="round"
                                />
                            </svg>

                            {{ createFolderForm.processing ? 'Membuat...' : 'Buat Folder' }}
                        </button>
                    </form>
                </section>

                <!-- UPLOAD FILE -->
                <section class="drive-card">
                    <div class="drive-card-heading">
                        <div class="drive-heading-icon file">
                            <svg viewBox="0 0 24 24" fill="none">
                                <path
                                    d="M14 3H7a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V8l-5-5Z"
                                    stroke="currentColor"
                                    stroke-width="1.8"
                                    stroke-linejoin="round"
                                />
                                <path
                                    d="M14 3v5h5"
                                    stroke="currentColor"
                                    stroke-width="1.8"
                                    stroke-linejoin="round"
                                />
                            </svg>
                        </div>
                        <h2>Upload File</h2>
                    </div>

                    <form @submit.prevent="uploadFile">
                        <div class="drive-form-row">
                            <label class="drive-label" for="drive-file">
                                Pilih File · Maks. {{ limits.maxUploadMb }} MB
                            </label>

                            <input
                                id="drive-file"
                                ref="fileInput"
                                class="drive-input drive-file-input"
                                type="file"
                                required
                                @change="handleFileChange"
                            />

                            <div v-if="uploadForm.errors.file" class="drive-error">
                                {{ uploadForm.errors.file }}
                            </div>
                        </div>

                        <button
                            class="drive-primary-button"
                            type="submit"
                            :disabled="uploadForm.processing"
                        >
                            <svg viewBox="0 0 24 24" fill="none">
                                <path
                                    d="M12 16V4M12 4l-4 4M12 4l4 4"
                                    stroke="currentColor"
                                    stroke-width="2"
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                />
                                <path
                                    d="M4 17v1a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2v-1"
                                    stroke="currentColor"
                                    stroke-width="2"
                                    stroke-linecap="round"
                                />
                            </svg>

                            {{ uploadForm.processing ? 'Mengunggah...' : 'Upload File' }}
                        </button>
                    </form>
                </section>
            </div>

            <!-- CURRENT FOLDER SHARE SETTINGS -->
            <section v-if="currentFolder" class="drive-card drive-current-folder">
                <div class="drive-current-main">
                    <div class="drive-item-icon folder">
                        <svg viewBox="0 0 24 24" fill="none">
                            <path
                                d="M3 7.5A2.5 2.5 0 0 1 5.5 5H10l2 2h6.5A2.5 2.5 0 0 1 21 9.5v7A2.5 2.5 0 0 1 18.5 19h-13A2.5 2.5 0 0 1 3 16.5v-9Z"
                                stroke="currentColor"
                                stroke-width="1.8"
                                stroke-linejoin="round"
                            />
                        </svg>
                    </div>

                    <div>
                        <h2 class="drive-current-title">
                            {{ currentFolder.nama }}
                        </h2>

                        <p class="drive-item-meta">
                            {{ currentFolder.is_public
                                ? 'Publik · Siapa pun dengan link dapat membuka folder ini'
                                : 'Private · Hanya kamu yang dapat membuka folder ini' }}
                        </p>
                    </div>
                </div>

                <div class="drive-action-group">
                    <span
                        class="drive-status"
                        :class="{ public: currentFolder.is_public }"
                    >
                        {{ currentFolder.is_public ? 'Public' : 'Private' }}
                    </span>

                    <button
                        type="button"
                        class="drive-icon-button"
                        :title="currentFolder.is_public ? 'Jadikan private' : 'Jadikan public'"
                        :aria-label="currentFolder.is_public ? 'Jadikan private' : 'Jadikan public'"
                        @click="toggleFolder(currentFolder)"
                    >
                        <svg v-if="currentFolder.is_public" viewBox="0 0 24 24" fill="none">
                            <path
                                d="M12 16c2.761 0 5-2.239 5-5s-2.239-5-5-5-5 2.239-5 5 2.239 5 5 5Z"
                                stroke="currentColor"
                                stroke-width="1.8"
                            />
                            <path
                                d="M2 12s3.5-6 10-6 10 6 10 6-3.5 6-10 6-10-6-10-6Z"
                                stroke="currentColor"
                                stroke-width="1.8"
                                stroke-linejoin="round"
                            />
                        </svg>

                        <svg v-else viewBox="0 0 24 24" fill="none">
                            <path
                                d="M7 11V8a5 5 0 0 1 10 0v3"
                                stroke="currentColor"
                                stroke-width="1.8"
                                stroke-linecap="round"
                            />
                            <rect
                                x="5"
                                y="11"
                                width="14"
                                height="10"
                                rx="2"
                                stroke="currentColor"
                                stroke-width="1.8"
                            />
                        </svg>
                    </button>

                    <button
                        v-if="currentFolder.share_url"
                        type="button"
                        class="drive-icon-button"
                        title="Salin link publik folder"
                        aria-label="Salin link publik folder"
                        @click="copyLink(currentFolder.share_url)"
                    >
                        <svg viewBox="0 0 24 24" fill="none">
                            <path
                                d="M10 13a5 5 0 0 0 7.07 0l2.83-2.83a5 5 0 0 0-7.07-7.07L11 4"
                                stroke="currentColor"
                                stroke-width="1.8"
                                stroke-linecap="round"
                                stroke-linejoin="round"
                            />
                            <path
                                d="M14 11a5 5 0 0 0-7.07 0L4.1 13.83a5 5 0 0 0 7.07 7.07L13 20"
                                stroke="currentColor"
                                stroke-width="1.8"
                                stroke-linecap="round"
                                stroke-linejoin="round"
                            />
                        </svg>
                    </button>
                </div>
            </section>

            <!-- FOLDERS -->
            <section class="drive-card">
                <div class="drive-section-heading">
                    <h2>Folder</h2>
                    <span>{{ folders.length }} folder</span>
                </div>

                <p v-if="folders.length === 0" class="drive-empty">
                    Belum ada folder di lokasi ini.
                </p>

                <div v-else class="drive-list">
                    <article
                        v-for="folder in folders"
                        :key="folder.id"
                        class="drive-item"
                    >
                        <Link :href="folder.open_url" class="drive-item-main">
                            <div class="drive-item-icon folder">
                                <svg viewBox="0 0 24 24" fill="none">
                                    <path
                                        d="M3 7.5A2.5 2.5 0 0 1 5.5 5H10l2 2h6.5A2.5 2.5 0 0 1 21 9.5v7A2.5 2.5 0 0 1 18.5 19h-13A2.5 2.5 0 0 1 3 16.5v-9Z"
                                        stroke="currentColor"
                                        stroke-width="1.8"
                                        stroke-linejoin="round"
                                    />
                                </svg>
                            </div>

                            <div class="drive-item-text">
                                <strong>{{ folder.nama }}</strong>
                                <span>{{ folder.is_public ? 'Public' : 'Private' }}</span>
                            </div>
                        </Link>

                        <div class="drive-action-group">
                            <!-- PUBLIC / PRIVATE -->
                            <button
                                type="button"
                                class="drive-icon-button"
                                :title="folder.is_public ? 'Jadikan private' : 'Jadikan public'"
                                :aria-label="folder.is_public ? 'Jadikan private' : 'Jadikan public'"
                                @click="toggleFolder(folder)"
                            >
                                <svg v-if="folder.is_public" viewBox="0 0 24 24" fill="none">
                                    <path
                                        d="M12 16c2.761 0 5-2.239 5-5s-2.239-5-5-5-5 2.239-5 5 2.239 5 5 5Z"
                                        stroke="currentColor"
                                        stroke-width="1.8"
                                    />
                                    <path
                                        d="M2 12s3.5-6 10-6 10 6 10 6-3.5 6-10 6-10-6-10-6Z"
                                        stroke="currentColor"
                                        stroke-width="1.8"
                                        stroke-linejoin="round"
                                    />
                                </svg>

                                <svg v-else viewBox="0 0 24 24" fill="none">
                                    <path
                                        d="M7 11V8a5 5 0 0 1 10 0v3"
                                        stroke="currentColor"
                                        stroke-width="1.8"
                                        stroke-linecap="round"
                                    />
                                    <rect
                                        x="5"
                                        y="11"
                                        width="14"
                                        height="10"
                                        rx="2"
                                        stroke="currentColor"
                                        stroke-width="1.8"
                                    />
                                </svg>
                            </button>

                            <!-- LINK -->
                            <button
                                v-if="folder.share_url"
                                type="button"
                                class="drive-icon-button"
                                title="Salin link folder"
                                aria-label="Salin link folder"
                                @click="copyLink(folder.share_url)"
                            >
                                <svg viewBox="0 0 24 24" fill="none">
                                    <path
                                        d="M10 13a5 5 0 0 0 7.07 0l2.83-2.83a5 5 0 0 0-7.07-7.07L11 4"
                                        stroke="currentColor"
                                        stroke-width="1.8"
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                    />
                                    <path
                                        d="M14 11a5 5 0 0 0-7.07 0L4.1 13.83a5 5 0 0 0 7.07 7.07L13 20"
                                        stroke="currentColor"
                                        stroke-width="1.8"
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                    />
                                </svg>
                            </button>

                            <!-- EDIT -->
                            <button
                                type="button"
                                class="drive-icon-button"
                                title="Ubah nama folder"
                                aria-label="Ubah nama folder"
                                @click="renameFolder(folder)"
                            >
                                <svg viewBox="0 0 24 24" fill="none">
                                    <path
                                        d="M4 20h4l10.5-10.5a2.121 2.121 0 1 0-3-3L5 17v3Z"
                                        stroke="currentColor"
                                        stroke-width="1.8"
                                        stroke-linejoin="round"
                                    />
                                </svg>
                            </button>

                            <!-- DELETE -->
                            <button
                                type="button"
                                class="drive-icon-button danger"
                                title="Hapus folder"
                                aria-label="Hapus folder"
                                @click="deleteFolder(folder)"
                            >
                                <svg viewBox="0 0 24 24" fill="none">
                                    <path
                                        d="M3 6h18"
                                        stroke="currentColor"
                                        stroke-width="1.8"
                                        stroke-linecap="round"
                                    />
                                    <path
                                        d="M8 6V4h8v2M7 6l1 14h8l1-14"
                                        stroke="currentColor"
                                        stroke-width="1.8"
                                        stroke-linejoin="round"
                                    />
                                </svg>
                            </button>
                        </div>
                    </article>
                </div>
            </section>

            <!-- FILES -->
            <section class="drive-card">
                <div class="drive-section-heading">
                    <h2>File</h2>
                    <span>{{ files.length }} file</span>
                </div>

                <p v-if="files.length === 0" class="drive-empty">
                    Belum ada file di lokasi ini.
                </p>

                <div v-else class="drive-list">
                    <article
                        v-for="file in files"
                        :key="file.id"
                        class="drive-item"
                    >
                        <div class="drive-item-main">
                            <div class="drive-item-icon file">
                                <svg viewBox="0 0 24 24" fill="none">
                                    <path
                                        d="M14 3H7a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V8l-5-5Z"
                                        stroke="currentColor"
                                        stroke-width="1.8"
                                        stroke-linejoin="round"
                                    />
                                    <path
                                        d="M14 3v5h5"
                                        stroke="currentColor"
                                        stroke-width="1.8"
                                        stroke-linejoin="round"
                                    />
                                </svg>
                            </div>

                            <div class="drive-item-text">
                                <strong>{{ file.nama_tampilan }}</strong>
                                <span>
                                    {{ ukuran(file.ukuran_bytes) }} ·
                                    {{ file.is_public ? 'Public' : 'Private' }}
                                </span>
                            </div>
                        </div>

                        <div class="drive-action-group">
                            <!-- DOWNLOAD -->
                            <a
                                :href="file.download_url"
                                class="drive-icon-button"
                                title="Download file"
                                aria-label="Download file"
                            >
                                <svg viewBox="0 0 24 24" fill="none">
                                    <path
                                        d="M12 4v10M12 14l-4-4M12 14l4-4"
                                        stroke="currentColor"
                                        stroke-width="1.8"
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                    />
                                    <path
                                        d="M4 18v1a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2v-1"
                                        stroke="currentColor"
                                        stroke-width="1.8"
                                        stroke-linecap="round"
                                    />
                                </svg>
                            </a>

                            <!-- PUBLIC / PRIVATE -->
                            <button
                                type="button"
                                class="drive-icon-button"
                                :title="file.is_public ? 'Jadikan private' : 'Jadikan public'"
                                :aria-label="file.is_public ? 'Jadikan private' : 'Jadikan public'"
                                @click="toggleFile(file)"
                            >
                                <svg v-if="file.is_public" viewBox="0 0 24 24" fill="none">
                                    <path
                                        d="M12 16c2.761 0 5-2.239 5-5s-2.239-5-5-5-5 2.239-5 5 2.239 5 5 5Z"
                                        stroke="currentColor"
                                        stroke-width="1.8"
                                    />
                                    <path
                                        d="M2 12s3.5-6 10-6 10 6 10 6-3.5 6-10 6-10-6-10-6Z"
                                        stroke="currentColor"
                                        stroke-width="1.8"
                                        stroke-linejoin="round"
                                    />
                                </svg>

                                <svg v-else viewBox="0 0 24 24" fill="none">
                                    <path
                                        d="M7 11V8a5 5 0 0 1 10 0v3"
                                        stroke="currentColor"
                                        stroke-width="1.8"
                                        stroke-linecap="round"
                                    />
                                    <rect
                                        x="5"
                                        y="11"
                                        width="14"
                                        height="10"
                                        rx="2"
                                        stroke="currentColor"
                                        stroke-width="1.8"
                                    />
                                </svg>
                            </button>

                            <!-- LINK -->
                            <button
                                v-if="file.share_url"
                                type="button"
                                class="drive-icon-button"
                                title="Salin link file"
                                aria-label="Salin link file"
                                @click="copyLink(file.share_url)"
                            >
                                <svg viewBox="0 0 24 24" fill="none">
                                    <path
                                        d="M10 13a5 5 0 0 0 7.07 0l2.83-2.83a5 5 0 0 0-7.07-7.07L11 4"
                                        stroke="currentColor"
                                        stroke-width="1.8"
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                    />
                                    <path
                                        d="M14 11a5 5 0 0 0-7.07 0L4.1 13.83a5 5 0 0 0 7.07 7.07L13 20"
                                        stroke="currentColor"
                                        stroke-width="1.8"
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                    />
                                </svg>
                            </button>

                            <!-- EDIT -->
                            <button
                                type="button"
                                class="drive-icon-button"
                                title="Ubah nama file"
                                aria-label="Ubah nama file"
                                @click="renameFile(file)"
                            >
                                <svg viewBox="0 0 24 24" fill="none">
                                    <path
                                        d="M4 20h4l10.5-10.5a2.121 2.121 0 1 0-3-3L5 17v3Z"
                                        stroke="currentColor"
                                        stroke-width="1.8"
                                        stroke-linejoin="round"
                                    />
                                </svg>
                            </button>

                            <!-- DELETE -->
                            <button
                                type="button"
                                class="drive-icon-button danger"
                                title="Hapus file"
                                aria-label="Hapus file"
                                @click="deleteFile(file)"
                            >
                                <svg viewBox="0 0 24 24" fill="none">
                                    <path
                                        d="M3 6h18"
                                        stroke="currentColor"
                                        stroke-width="1.8"
                                        stroke-linecap="round"
                                    />
                                    <path
                                        d="M8 6V4h8v2M7 6l1 14h8l1-14"
                                        stroke="currentColor"
                                        stroke-width="1.8"
                                        stroke-linejoin="round"
                                    />
                                </svg>
                            </button>
                        </div>
                    </article>
                </div>
            </section>
        </div>
    </AppLayout>
</template>

<style scoped>
.drive-page {
    display: flex;
    flex-direction: column;
    gap: 20px;
}

.drive-header {
    display: flex;
    align-items: flex-start;
    justify-content: space-between;
    gap: 20px;
}

.drive-eyebrow {
    margin: 0 0 8px;
    color: #4338ca;
    font-size: 11px;
    font-weight: 800;
    letter-spacing: 0.1em;
    text-transform: uppercase;
}

.drive-title {
    margin: 0 0 8px;
    color: #0f172a;
    font-size: 30px;
    font-weight: 800;
    line-height: 1.15;
}

.drive-subtitle {
    margin: 0;
    color: #64748b;
    font-size: 14px;
    line-height: 1.55;
}

.drive-breadcrumb {
    display: flex;
    align-items: center;
    flex-wrap: wrap;
    gap: 9px;
    font-size: 13px;
}

.drive-breadcrumb-link {
    color: #4338ca;
    font-weight: 600;
    text-decoration: none;
}

.drive-breadcrumb-link:hover {
    text-decoration: underline;
}

.drive-breadcrumb-divider {
    color: #94a3b8;
}

.drive-alert-error {
    padding: 13px 15px;
    border: 1px solid #fecaca;
    border-radius: 12px;
    background: #fef2f2;
    color: #b91c1c;
    font-size: 14px;
}

.drive-tools-grid {
    display: grid;
    grid-template-columns: repeat(2, minmax(0, 1fr));
    gap: 16px;
}

.drive-card {
    padding: 18px;
    border: 1px solid #e2e8f0;
    border-radius: 17px;
    background: #ffffff;
}

.drive-card-heading {
    display: flex;
    align-items: center;
    gap: 11px;
    margin-bottom: 16px;
}

.drive-card-heading h2 {
    margin: 0;
    color: #0f172a;
    font-size: 17px;
    font-weight: 750;
}

.drive-heading-icon {
    display: grid;
    place-items: center;
    width: 38px;
    height: 38px;
    border-radius: 11px;
}

.drive-heading-icon svg {
    width: 21px;
    height: 21px;
}

.drive-heading-icon.folder {
    color: #d97706;
    background: #fffbeb;
}

.drive-heading-icon.file {
    color: #4338ca;
    background: #eef2ff;
}

.drive-form-row {
    margin-bottom: 13px;
}

.drive-label {
    display: block;
    margin-bottom: 7px;
    color: #475569;
    font-size: 12px;
    font-weight: 650;
}

.drive-input {
    display: block;
    width: 100%;
    padding: 11px 13px;
    border: 1px solid #cbd5e1;
    border-radius: 11px;
    background: #ffffff;
    color: #0f172a;
    font-size: 14px;
    outline: none;
}

.drive-input:focus {
    border-color: #4338ca;
    box-shadow: 0 0 0 3px rgba(67, 56, 202, 0.12);
}

.drive-file-input {
    padding: 8px 10px;
}

.drive-error {
    margin-top: 6px;
    color: #dc2626;
    font-size: 12px;
}

.drive-primary-button {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 7px;
    padding: 10px 14px;
    border: 0;
    border-radius: 10px;
    background: #4338ca;
    color: #ffffff;
    cursor: pointer;
    font-size: 13px;
    font-weight: 700;
    transition: background 0.2s ease, opacity 0.2s ease;
}

.drive-primary-button:hover {
    background: #3730a3;
}

.drive-primary-button:disabled {
    cursor: not-allowed;
    opacity: 0.6;
}

.drive-primary-button svg {
    width: 17px;
    height: 17px;
}

.drive-current-folder {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 18px;
}

.drive-current-main {
    display: flex;
    align-items: center;
    gap: 13px;
}

.drive-current-title {
    margin: 0 0 5px;
    color: #0f172a;
    font-size: 16px;
    font-weight: 700;
}

.drive-section-heading {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 12px;
    margin-bottom: 15px;
}

.drive-section-heading h2 {
    margin: 0;
    color: #0f172a;
    font-size: 17px;
    font-weight: 750;
}

.drive-section-heading span {
    color: #64748b;
    font-size: 13px;
}

.drive-empty {
    margin: 0;
    padding: 12px 0 3px;
    color: #64748b;
    font-size: 14px;
}

.drive-list {
    display: flex;
    flex-direction: column;
    gap: 10px;
}

.drive-item {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 15px;
    padding: 12px;
    border: 1px solid #e2e8f0;
    border-radius: 13px;
    transition: border-color 0.2s ease, background 0.2s ease;
}

.drive-item:hover {
    border-color: #cbd5e1;
    background: #fbfcff;
}

.drive-item-main {
    display: flex;
    align-items: center;
    gap: 12px;
    min-width: 0;
    color: inherit;
    text-decoration: none;
}

.drive-item-icon {
    display: grid;
    place-items: center;
    flex-shrink: 0;
    width: 42px;
    height: 42px;
    border-radius: 12px;
}

.drive-item-icon svg {
    width: 23px;
    height: 23px;
}

.drive-item-icon.folder {
    color: #d97706;
    background: #fef3c7;
}

.drive-item-icon.file {
    color: #4338ca;
    background: #eef2ff;
}

.drive-item-text {
    display: flex;
    flex-direction: column;
    gap: 4px;
    min-width: 0;
}

.drive-item-text strong {
    overflow: hidden;
    color: #0f172a;
    font-size: 14px;
    font-weight: 700;
    text-overflow: ellipsis;
    white-space: nowrap;
}

.drive-item-text span,
.drive-item-meta {
    color: #64748b;
    font-size: 12px;
}

.drive-action-group {
    display: flex;
    align-items: center;
    gap: 7px;
    flex-shrink: 0;
}

.drive-status {
    padding: 5px 9px;
    border-radius: 999px;
    background: #f1f5f9;
    color: #475569;
    font-size: 11px;
    font-weight: 700;
}

.drive-status.public {
    background: #ecfdf5;
    color: #047857;
}

.drive-icon-button {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 37px;
    height: 37px;
    border: 1px solid #e2e8f0;
    border-radius: 10px;
    background: #ffffff;
    color: #475569;
    cursor: pointer;
    text-decoration: none;
    transition: border-color 0.2s ease, color 0.2s ease, background 0.2s ease;
}

.drive-icon-button:hover {
    border-color: #c7d2fe;
    background: #eef2ff;
    color: #4338ca;
}

.drive-icon-button svg {
    width: 18px;
    height: 18px;
}

.drive-icon-button.danger {
    color: #dc2626;
}

.drive-icon-button.danger:hover {
    border-color: #fecaca;
    background: #fef2f2;
    color: #b91c1c;
}

@media (max-width: 900px) {
    .drive-tools-grid {
        grid-template-columns: 1fr;
    }

    .drive-current-folder,
    .drive-item {
        flex-direction: column;
        align-items: flex-start;
    }

    .drive-action-group {
        width: 100%;
        justify-content: flex-end;
    }
}

@media (max-width: 560px) {
    .drive-title {
        font-size: 25px;
    }

    .drive-action-group {
        justify-content: flex-start;
        flex-wrap: wrap;
    }

    .drive-item-text strong {
        white-space: normal;
    }
}
</style>
