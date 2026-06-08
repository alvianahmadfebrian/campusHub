<script setup>
import { Head, Link, router, useForm } from '@inertiajs/vue3'
import { computed, ref, watch } from 'vue'
import AppLayout from '@/Layouts/AppLayout.vue'

const props = defineProps({
    currentFolder: { type: Object, default: null },
    breadcrumbs: { type: Array, default: () => [] },
    folders: { type: Array, default: () => [] },
    files: { type: Array, default: () => [] },
    limits: { type: Object, default: () => ({ maxUploadMb: 50 }) },
})

const fileInput = ref(null)
const showCreate = ref(false)
const query = ref('')
const openMenu = ref(null)

const createFolderForm = useForm({
    nama: '',
    parent_id: props.currentFolder?.id || null,
})

const uploadForm = useForm({
    folder_id: props.currentFolder?.id || null,
    file: null,
})

const filteredFolders = computed(() => {
    const q = query.value.toLowerCase().trim()
    if (!q) return props.folders
    return props.folders.filter((folder) => folder.nama?.toLowerCase().includes(q))
})

const filteredFiles = computed(() => {
    const q = query.value.toLowerCase().trim()
    if (!q) return props.files
    return props.files.filter((file) => file.nama_tampilan?.toLowerCase().includes(q))
})

watch(
    () => props.currentFolder?.id,
    (folderId) => {
        createFolderForm.parent_id = folderId || null
        uploadForm.folder_id = folderId || null
        openMenu.value = null
    }
)

function toggleMenu(key) {
    openMenu.value = openMenu.value === key ? null : key
}

function createFolder() {
    createFolderForm.post('/drive/folders', {
        preserveScroll: true,
        onSuccess: () => {
            createFolderForm.reset('nama')
            createFolderForm.parent_id = props.currentFolder?.id || null
            showCreate.value = false
        },
    })
}

function openUploadPicker() {
    fileInput.value?.click()
}

function handleFileChange(event) {
    uploadForm.file = event.target.files?.[0] || null
    if (uploadForm.file) uploadFile()
}

function uploadFile() {
    uploadForm.post('/drive/files', {
        forceFormData: true,
        preserveScroll: true,
        onSuccess: () => {
            uploadForm.reset('file')
            uploadForm.folder_id = props.currentFolder?.id || null
            if (fileInput.value) fileInput.value.value = ''
        },
    })
}

function renameFolder(folder) {
    const nama = window.prompt('Nama folder baru:', folder.nama)
    if (!nama || nama.trim() === '' || nama.trim() === folder.nama) return
    router.patch(`/drive/folders/${folder.id}`, { nama: nama.trim() }, { preserveScroll: true })
}

function toggleFolder(folder) {
    router.patch(`/drive/folders/${folder.id}`, { is_public: !folder.is_public }, { preserveScroll: true })
}

function deleteFolder(folder) {
    if (!window.confirm(`Hapus folder "${folder.nama}" beserta seluruh file dan subfolder di dalamnya?`)) return
    router.delete(`/drive/folders/${folder.id}`)
}

function renameFile(file) {
    const nama = window.prompt('Nama file baru:', file.nama_tampilan)
    if (!nama || nama.trim() === '' || nama.trim() === file.nama_tampilan) return
    router.patch(`/drive/files/${file.id}`, { nama_tampilan: nama.trim() }, { preserveScroll: true })
}

function toggleFile(file) {
    router.patch(`/drive/files/${file.id}`, { is_public: !file.is_public }, { preserveScroll: true })
}

function deleteFile(file) {
    if (!window.confirm(`Hapus file "${file.nama_tampilan}"?`)) return
    router.delete(`/drive/files/${file.id}`, { preserveScroll: true })
}

async function copyLink(url) {
    if (!url) return
    try {
        await navigator.clipboard.writeText(url)
        window.alert('Link publik berhasil disalin.')
    } catch (error) {
        window.prompt('Salin link berikut:', url)
    }
}

function ukuran(bytes) {
    if (!bytes) return '—'
    const units = ['B', 'KB', 'MB', 'GB']
    const index = Math.min(Math.floor(Math.log(bytes) / Math.log(1024)), units.length - 1)
    return `${(bytes / Math.pow(1024, index)).toFixed(index === 0 ? 0 : 1)} ${units[index]}`
}

function folderUkuran(folder) {
    return ukuran(folder.ukuran_bytes || folder.size || folder.total_size)
}

function tanggal(item) {
    return item.updated_at || item.created_at || '1 Jun 2025'
}

function fileType(name) {
    const ext = name?.split('.').pop()?.toLowerCase()
    if (ext === 'pdf') return 'pdf'
    if (['doc', 'docx'].includes(ext)) return 'doc'
    if (['xls', 'xlsx', 'csv'].includes(ext)) return 'sheet'
    if (['jpg', 'jpeg', 'png', 'webp'].includes(ext)) return 'img'
    return 'file'
}
</script>

<template>
    <Head title="Drive" />

    <AppLayout>
        <div class="drive-page" @click="openMenu = null">
            <header class="drive-topbar">
                <div class="drive-search">
                    <svg viewBox="0 0 24 24" fill="none">
                        <path d="m21 21-4.2-4.2M11 18a7 7 0 1 1 0-14 7 7 0 0 1 0 14Z" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                    </svg>
                    <input v-model="query" type="search" placeholder="Telusuri di Drive" />
                    <svg viewBox="0 0 24 24" fill="none">
                        <path d="M4 7h10M18 7h2M4 17h2M10 17h10M8 5v4M16 15v4" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                    </svg>
                </div>
            </header>

            <main class="drive-shell">
                <section class="drive-toolbar">
                    <div>
                        <h1>Drive Saya <span>⌄</span></h1>
                        <nav class="breadcrumb">
                            <Link href="/drive">Drive Saya</Link>
                            <template v-for="crumb in breadcrumbs" :key="crumb.url">
                                <span>›</span>
                                <Link :href="crumb.url">{{ crumb.nama }}</Link>
                            </template>
                        </nav>
                    </div>

                    <div class="view-switch">
                        <button class="active" type="button">☰</button>
                        <button type="button">▦</button>
                        <button type="button">ⓘ</button>
                    </div>
                </section>

                <section class="action-row">
                    <div class="primary-actions">
                        <button class="new-btn" type="button" @click.stop="showCreate = !showCreate">
                            <span>＋</span> Baru <small>⌄</small>
                        </button>

                        <button class="upload-btn" type="button" :disabled="uploadForm.processing" @click.stop="openUploadPicker">
                            <span>⇧</span> {{ uploadForm.processing ? 'Mengunggah...' : 'Upload' }}
                        </button>
                    </div>
                </section>

                <input id="drive-file" ref="fileInput" class="file-picker" type="file" @change="handleFileChange" />

                <div v-if="$page.props.errors?.drive" class="error-box">{{ $page.props.errors.drive }}</div>
                <div v-if="uploadForm.errors.file" class="error-box">{{ uploadForm.errors.file }}</div>

                <section v-if="showCreate" class="create-folder-card" @click.stop>
                    <form @submit.prevent="createFolder">
                        <input v-model="createFolderForm.nama" type="text" placeholder="Nama folder baru" required />
                        <button type="submit" :disabled="createFolderForm.processing">
                            {{ createFolderForm.processing ? 'Membuat...' : 'Buat Folder' }}
                        </button>
                    </form>
                    <small v-if="createFolderForm.errors.nama">{{ createFolderForm.errors.nama }}</small>
                </section>

                <section class="drive-table">
                    <div class="table-head">
                        <div>Nama <span class="sort-arrow">↑</span></div>
                        <div>Pemilik</div>
                        <div>Terakhir diubah</div>
                        <div>Ukuran</div>
                        <div>⋮</div>
                    </div>

                    <Link v-for="folder in filteredFolders" :key="`folder-${folder.id}`" :href="folder.open_url" class="table-row">
                        <div class="name-cell">
                            <span class="folder-icon">
                                <svg viewBox="0 0 24 24" fill="currentColor">
                                    <path d="M10 4l2 2h8a2 2 0 0 1 2 2v10.2a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h6Z"/>
                                </svg>
                            </span>
                            <strong>{{ folder.nama }}</strong>
                        </div>

                        <div class="owner"><span class="avatar-mini">E</span> saya</div>
                        <div>{{ tanggal(folder) }}</div>
                        <div>{{ folderUkuran(folder) }}</div>

                        <div class="row-actions" @click.prevent.stop>
                            <button class="dots-btn" type="button" @click="toggleMenu(`folder-${folder.id}`)">⋮</button>

                            <div v-if="openMenu === `folder-${folder.id}`" class="menu-popover">
                                <button type="button" @click="toggleFolder(folder)">{{ folder.is_public ? 'Private' : 'Public' }}</button>
                                <button v-if="folder.share_url" type="button" @click="copyLink(folder.share_url)">Salin Link</button>
                                <button type="button" @click="renameFolder(folder)">Rename</button>
                                <button class="danger" type="button" @click="deleteFolder(folder)">Hapus</button>
                            </div>
                        </div>
                    </Link>

                    <div v-for="file in filteredFiles" :key="`file-${file.id}`" class="table-row">
                        <div class="name-cell">
                            <span class="file-icon" :class="fileType(file.nama_tampilan)">
                                {{ fileType(file.nama_tampilan) === 'pdf' ? 'PDF' : '▤' }}
                            </span>
                            <strong>{{ file.nama_tampilan }}</strong>
                        </div>

                        <div class="owner"><span class="avatar-mini">E</span> saya</div>
                        <div>{{ tanggal(file) }}</div>
                        <div>{{ ukuran(file.ukuran_bytes) }}</div>

                        <div class="row-actions" @click.stop>
                            <button class="dots-btn" type="button" @click="toggleMenu(`file-${file.id}`)">⋮</button>

                            <div v-if="openMenu === `file-${file.id}`" class="menu-popover">
                                <a :href="file.download_url">Download</a>
                                <button type="button" @click="toggleFile(file)">{{ file.is_public ? 'Private' : 'Public' }}</button>
                                <button v-if="file.share_url" type="button" @click="copyLink(file.share_url)">Salin Link</button>
                                <button type="button" @click="renameFile(file)">Rename</button>
                                <button class="danger" type="button" @click="deleteFile(file)">Hapus</button>
                            </div>
                        </div>
                    </div>

                    <div v-if="filteredFolders.length === 0 && filteredFiles.length === 0" class="empty-state">
                        <div class="empty-icon">☁</div>
                        <h2>Drive masih kosong</h2>
                        <p>Klik <b>+ Baru</b> untuk membuat folder atau <b>Upload</b> untuk mengunggah file.</p>
                    </div>
                </section>
            </main>
        </div>
    </AppLayout>
</template>

<style>
.drive-page {
    --bg: #f8fbff;
    --text: #172033;
    --muted: #64748b;
    --line: #e7edf5;
    --teal: #009f8b;

    min-height: calc(100vh - 56px);
    margin: -18px;
    padding: 0 28px 28px;
    background: var(--bg);
    color: var(--text);
    font-family: Inter, ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
}

.drive-topbar {
    display: flex;
    align-items: center;
    min-height: 74px;
    padding-left: 58px;
}

.drive-search {
    display: flex;
    align-items: center;
    gap: 16px;
    width: min(820px, 58vw);
    height: 54px;
    padding: 0 20px;
    border-radius: 999px;
    background: #edf3fb;
    color: #526174;
}

.drive-search svg {
    width: 21px;
    height: 21px;
    flex-shrink: 0;
}

.drive-search input {
    flex: 1;
    border: 0;
    outline: 0;
    background: transparent;
    color: #334155;
    font-size: 15px;
}

.drive-shell {
    min-height: calc(100vh - 118px);
    padding: 42px 44px 28px;
    border-radius: 24px;
    background: #fff;
    box-shadow: 0 1px 0 rgba(15, 23, 42, .03);
}

.drive-toolbar {
    display: flex;
    align-items: flex-start;
    justify-content: space-between;
    gap: 24px;
    margin-bottom: 30px;
}

.drive-toolbar h1 {
    margin: 0 0 16px;
    color: #172033;
    font-size: 29px;
    font-weight: 600;
    letter-spacing: -.04em;
    line-height: 1;
}

.drive-toolbar h1 span {
    margin-left: 4px;
    font-size: 16px;
}

.breadcrumb {
    display: flex;
    align-items: center;
    gap: 10px;
    color: #94a3b8;
    font-size: 14px;
}

.breadcrumb a {
    color: #526174;
    font-weight: 550;
    text-decoration: none;
}

.view-switch {
    display: flex;
    align-items: center;
    overflow: hidden;
    border: 1px solid #d6dfeb;
    border-radius: 999px;
    background: #fff;
}

.view-switch button {
    width: 52px;
    height: 42px;
    border: 0;
    border-left: 1px solid #d6dfeb;
    background: transparent;
    color: #475569;
    cursor: pointer;
    font-size: 17px;
}

.view-switch button:first-child {
    border-left: 0;
}

.view-switch .active {
    background: #dff3ef;
    color: var(--teal);
}

.action-row {
    display: flex;
    align-items: center;
    justify-content: flex-end;
    margin: -8px 0 32px;
}

.primary-actions {
    display: flex;
    align-items: center;
    gap: 12px;
}

.new-btn,
.upload-btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    min-height: 42px;
    padding: 0 18px;
    border-radius: 12px;
    cursor: pointer;
    font-size: 14px;
    font-weight: 650;
    transition: .18s ease;
}

.new-btn {
    min-width: 120px;
    border: 1px solid #dbe3ee;
    background: #fff;
    color: #334155;
    box-shadow: 0 2px 8px rgba(15, 23, 42, .04);
}

.new-btn:hover {
    background: #f8fafc;
    border-color: #cbd5e1;
}

.new-btn > span {
    color: var(--teal);
    font-size: 21px;
    line-height: 1;
}

.new-btn small {
    color: #64748b;
    font-size: 11px;
}

.upload-btn {
    min-width: 120px;
    border: 1px solid var(--teal);
    background: var(--teal);
    color: #fff;
    box-shadow: 0 8px 18px rgba(0, 159, 139, .18);
}

.upload-btn:hover {
    transform: translateY(-1px);
}

.file-picker {
    display: none;
}

.error-box {
    margin: -12px 0 20px;
    padding: 13px 15px;
    border-radius: 13px;
    background: #fce8e6;
    color: #a50e0e;
    font-size: 14px;
}

.create-folder-card {
    margin: -12px 0 22px;
    padding: 16px;
    border: 1px solid var(--line);
    border-radius: 16px;
    background: #fbfdff;
}

.create-folder-card form {
    display: flex;
    gap: 12px;
}

.create-folder-card input {
    flex: 1;
    min-height: 44px;
    padding: 0 14px;
    border: 1px solid #dbe3ee;
    border-radius: 12px;
    outline: 0;
}

.create-folder-card button {
    min-height: 44px;
    padding: 0 18px;
    border: 0;
    border-radius: 999px;
    background: var(--teal);
    color: #fff;
    cursor: pointer;
    font-weight: 700;
}

.create-folder-card small {
    display: block;
    margin-top: 8px;
    color: #d93025;
}

.drive-table {
    width: 100%;
}

.table-head,
.table-row {
    display: grid;
    grid-template-columns: minmax(300px, 1.55fr) minmax(160px, .52fr) minmax(190px, .72fr) minmax(120px, .42fr) 86px;
    align-items: center;
    column-gap: 24px;
}

.table-head {
    min-height: 54px;
    border-top: 1px solid var(--line);
    border-bottom: 1px solid var(--line);
    color: #46566d;
    font-size: 14px;
    font-weight: 700;
}

.table-head > div:first-child {
    padding-left: 18px;
}

.table-head > div:last-child {
    text-align: right;
    padding-right: 24px;
    font-size: 22px;
    letter-spacing: 2px;
}

.sort-arrow {
    margin-left: 8px;
    color: #1e293b;
    font-size: 21px;
    font-weight: 600;
}

.table-row {
    min-height: 76px;
    border-bottom: 1px solid var(--line);
    color: #53657f;
    font-size: 15px;
    text-decoration: none;
    transition: background .16s ease, box-shadow .16s ease;
}

.table-row:hover {
    background: #fbfdff;
    box-shadow: inset 4px 0 0 rgba(0, 159, 139, .14);
}

.name-cell,
.owner {
    display: flex;
    align-items: center;
    gap: 18px;
    min-width: 0;
}

.name-cell {
    padding-left: 26px;
}

.name-cell strong {
    overflow: hidden;
    color: #334155;
    font-size: 15px;
    font-weight: 550;
    text-overflow: ellipsis;
    white-space: nowrap;
}

.folder-icon {
    display: inline-grid;
    place-items: center;
    width: 34px;
    height: 34px;
    color: #334155;
    flex-shrink: 0;
}

.folder-icon svg {
    width: 34px;
    height: 34px;
}

.file-icon {
    display: inline-grid;
    place-items: center;
    width: 30px;
    height: 34px;
    border-radius: 5px;
    color: #fff;
    font-size: 9px;
    font-weight: 800;
    flex-shrink: 0;
}

.file-icon.pdf { background: #ef4444; }
.file-icon.doc { background: #4285f4; }
.file-icon.sheet { background: #16a34a; }
.file-icon.img { background: #fbbc04; }
.file-icon.file { background: #64748b; }

.avatar-mini {
    display: inline-grid;
    place-items: center;
    width: 32px;
    height: 32px;
    border-radius: 999px;
    background: #d8f8e9;
    color: #009f8b;
    font-size: 13px;
    font-weight: 800;
    flex-shrink: 0;
}

.row-actions {
    position: relative;
    display: flex;
    align-items: center;
    justify-content: flex-end;
    padding-right: 20px;
}

.dots-btn {
    display: inline-grid;
    place-items: center;
    width: 34px;
    height: 34px;
    border: 0;
    border-radius: 999px;
    background: transparent;
    color: #24364f;
    cursor: pointer;
    font-size: 24px;
    line-height: 1;
}

.dots-btn:hover {
    background: #edf3fb;
}

.menu-popover {
    position: absolute;
    top: 38px;
    right: 18px;
    z-index: 50;
    min-width: 158px;
    padding: 8px;
    border: 1px solid #dbe3ee;
    border-radius: 14px;
    background: #fff;
    box-shadow: 0 16px 40px rgba(15, 23, 42, .14);
}

.menu-popover button,
.menu-popover a {
    display: flex;
    align-items: center;
    width: 100%;
    min-height: 36px;
    padding: 0 12px;
    border: 0;
    border-radius: 10px;
    background: transparent;
    color: #334155;
    cursor: pointer;
    font-size: 13px;
    text-align: left;
    text-decoration: none;
}

.menu-popover button:hover,
.menu-popover a:hover {
    background: #f1f5f9;
}

.menu-popover .danger {
    color: #dc2626;
}

.empty-state {
    min-height: 330px;
    display: grid;
    place-items: center;
    align-content: center;
    gap: 6px;
    color: #64748b;
    text-align: center;
}

.empty-icon {
    color: #64748b;
    font-size: 34px;
}

.empty-state h2 {
    margin: 0;
    color: #172033;
    font-size: 18px;
    font-weight: 700;
}

.empty-state p {
    margin: 0;
    font-size: 13px;
}

@media (max-width: 1100px) {
    .drive-topbar {
        padding-left: 0;
    }

    .drive-search {
        width: 100%;
    }

    .drive-shell {
        padding: 34px 28px;
    }

    .action-row {
        justify-content: flex-start;
    }

    .table-head,
    .table-row {
        grid-template-columns: minmax(260px, 1.4fr) minmax(120px, .5fr) minmax(150px, .65fr) minmax(80px, .35fr) 70px;
    }
}

@media (max-width: 760px) {
    .drive-page {
        margin: -12px;
        padding: 0 14px 20px;
    }

    .drive-shell {
        padding: 24px 18px;
        border-radius: 22px;
    }

    .drive-toolbar {
        flex-direction: column;
    }

    .table-head {
        display: none;
    }

    .table-row {
        grid-template-columns: 1fr;
        gap: 8px;
        padding: 14px 0;
    }

    .name-cell,
    .table-head > div:first-child {
        padding-left: 0;
    }

    .row-actions {
        justify-content: flex-start;
        padding-right: 0;
    }

    .menu-popover {
        left: 0;
        right: auto;
    }

    .create-folder-card form {
        flex-direction: column;
    }
}

/* ============================================
   DARK MODE — Drive/Index.vue
   ============================================ */

/* CSS variables */
html.dark .drive-page {
    --bg: #0f172a;
    --text: #e2e8f0;
    --muted: #64748b;
    --line: #1e293b;
    --teal: #2dd4bf;
}

/* Outer page background */
html.dark .drive-page {
    background: #0f172a;
    color: #e2e8f0;
}

/* Inner shell card */
html.dark .drive-shell {
    background: #111827;
    box-shadow: 0 1px 0 rgba(0,0,0,.3);
}

/* ---- Search bar ---- */
html.dark .drive-search {
    background: #1e293b;
    color: #94a3b8;
}
html.dark .drive-search svg {
    color: #64748b;
}
html.dark .drive-search input {
    color: #e2e8f0;
    background: transparent;
}
html.dark .drive-search input::placeholder {
    color: #475569;
}

/* ---- Toolbar: heading + breadcrumb ---- */
html.dark .drive-toolbar h1 {
    color: #f1f5f9;
}
html.dark .drive-toolbar h1 span {
    color: #64748b;
}
html.dark .breadcrumb {
    color: #475569;
}
html.dark .breadcrumb a {
    color: #94a3b8;
}

/* ---- View-switch buttons ---- */
html.dark .view-switch {
    background: #1e293b;
    border-color: #273549;
}
html.dark .view-switch button {
    color: #64748b;
    border-left-color: #273549;
    background: transparent;
}
html.dark .view-switch .active {
    background: #134e4a;
    color: #2dd4bf;
}

/* ---- Action buttons ---- */
html.dark .new-btn {
    background: #1e293b;
    border-color: #273549;
    color: #e2e8f0;
    box-shadow: none;
}
html.dark .new-btn:hover {
    background: #273549;
    border-color: #334155;
}
html.dark .new-btn > span {
    color: #2dd4bf;
}
html.dark .new-btn small {
    color: #64748b;
}
html.dark .upload-btn {
    background: #009f8b;
    border-color: #009f8b;
    color: #fff;
}

/* ---- Create folder card ---- */
html.dark .create-folder-card {
    background: #1e293b;
    border-color: #273549;
}
html.dark .create-folder-card input {
    background: #0f172a;
    border-color: #334155;
    color: #e2e8f0;
}
html.dark .create-folder-card input::placeholder {
    color: #475569;
}

/* ---- Table head ---- */
html.dark .table-head {
    color: #64748b;
    border-top-color: #1e293b;
    border-bottom-color: #1e293b;
}
html.dark .sort-arrow {
    color: #475569;
}

/* ---- Table rows ---- */
html.dark .table-row {
    color: #94a3b8;
    border-bottom-color: #1e293b;
}
html.dark .table-row:hover {
    background: #1a2744;
    box-shadow: inset 4px 0 0 rgba(45, 212, 191, .2);
}

/* File/folder name */
html.dark .name-cell strong {
    color: #e2e8f0;
}

/* Folder icon */
html.dark .folder-icon {
    color: #64748b;
}

/* Owner */
html.dark .owner {
    color: #64748b;
}
html.dark .avatar-mini {
    background: #134e4a;
    color: #2dd4bf;
}

/* ---- 3-dots menu button ---- */
html.dark .dots-btn {
    color: #64748b;
}
html.dark .dots-btn:hover {
    background: #1e293b;
    color: #e2e8f0;
}

/* ---- Popover menu ---- */
html.dark .menu-popover {
    background: #1e293b;
    border-color: #273549;
    box-shadow: 0 16px 40px rgba(0,0,0,.5);
}
html.dark .menu-popover button,
html.dark .menu-popover a {
    color: #e2e8f0;
    background: transparent;
}
html.dark .menu-popover button:hover,
html.dark .menu-popover a:hover {
    background: #273549;
}
html.dark .menu-popover .danger {
    color: #f87171;
}

/* ---- Empty state ---- */
html.dark .empty-state h2 {
    color: #e2e8f0;
}
html.dark .empty-state p {
    color: #475569;
}
</style>