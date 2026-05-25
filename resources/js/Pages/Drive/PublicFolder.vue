<script setup>
import { Head, Link } from '@inertiajs/vue3'

defineProps({
    rootFolder: { type: Object, required: true },
    currentFolder: { type: Object, required: true },
    breadcrumbs: { type: Array, default: () => [] },
    folders: { type: Array, default: () => [] },
    files: { type: Array, default: () => [] },
})

function ukuran(bytes) {
    if (!bytes) return '—'
    const units = ['B', 'KB', 'MB', 'GB']
    const index = Math.min(Math.floor(Math.log(bytes) / Math.log(1024)), units.length - 1)
    return `${(bytes / Math.pow(1024, index)).toFixed(index === 0 ? 0 : 1)} ${units[index]}`
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
    <Head :title="`${currentFolder.nama} - Shared Drive`" />

    <div class="shared-drive-page">
        <header class="shared-topbar">
            <div class="brand">
                <span class="brand-logo">C</span>
                <div>
                    <strong>CampusHub Drive</strong>
                    <small>Tautan Publik</small>
                </div>
            </div>

            <div class="search-box">
                <svg viewBox="0 0 24 24" fill="none">
                    <path
                        d="m21 21-4.2-4.2M11 18a7 7 0 1 1 0-14 7 7 0 0 1 0 14Z"
                        stroke="currentColor"
                        stroke-width="2"
                        stroke-linecap="round"
                    />
                </svg>
                <input type="text" placeholder="Telusuri di Drive" disabled />
            </div>

            <span class="public-badge">Public</span>
        </header>

        <main class="shared-shell">
            <section class="shared-toolbar">
                <div>
                    <h1>{{ rootFolder.nama }} <span>⌄</span></h1>

                    <nav class="breadcrumb">
                        <template v-for="(crumb, index) in breadcrumbs" :key="crumb.url">
                            <span v-if="index > 0">›</span>
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

            <section class="summary-card">
                <div>
                    <span class="eyebrow">Folder Dibagikan</span>
                    <h2>{{ currentFolder.nama }}</h2>
                    <p>Folder ini dibagikan secara publik. Tidak perlu login untuk membuka file.</p>
                </div>

                <div class="summary-count">
                    <strong>{{ folders.length }}</strong>
                    <span>folder</span>
                </div>

                <div class="summary-count">
                    <strong>{{ files.length }}</strong>
                    <span>file</span>
                </div>
            </section>

            <section class="drive-table">
                <div class="table-head">
                    <div>Nama <span class="sort-arrow">↑</span></div>
                    <div>Tipe</div>
                    <div>Akses</div>
                    <div>Ukuran</div>
                    <div>⋮</div>
                </div>

                <Link
                    v-for="folder in folders"
                    :key="`folder-${folder.id}`"
                    :href="folder.url"
                    class="table-row"
                >
                    <div class="name-cell">
                        <span class="folder-icon">
                            <svg viewBox="0 0 24 24" fill="currentColor">
                                <path d="M10 4l2 2h8a2 2 0 0 1 2 2v10.2a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h6Z" />
                            </svg>
                        </span>
                        <strong>{{ folder.nama }}</strong>
                    </div>

                    <div>Folder</div>
                    <div><span class="access-pill">Public</span></div>
                    <div>—</div>
                    <div class="row-action">Buka</div>
                </Link>

                <article
                    v-for="file in files"
                    :key="`file-${file.id}`"
                    class="table-row"
                >
                    <div class="name-cell">
                        <span class="file-icon" :class="fileType(file.nama_tampilan)">
                            {{ fileType(file.nama_tampilan) === 'pdf' ? 'PDF' : '▤' }}
                        </span>
                        <strong>{{ file.nama_tampilan }}</strong>
                    </div>

                    <div>File</div>
                    <div><span class="access-pill">Public</span></div>
                    <div>{{ ukuran(file.ukuran_bytes) }}</div>
                    <div class="row-action">
                        <a :href="file.url" target="_blank" rel="noopener">Buka</a>
                    </div>
                </article>

                <div v-if="folders.length === 0 && files.length === 0" class="empty-state">
                    <div class="empty-icon">☁</div>
                    <h2>Folder masih kosong</h2>
                    <p>Belum ada folder atau file yang dibagikan di sini.</p>
                </div>
            </section>
        </main>
    </div>
</template>

<style scoped>
.shared-drive-page {
    --bg: #f8fbff;
    --panel: #fff;
    --text: #172033;
    --muted: #64748b;
    --line: #e7edf5;
    --teal: #009f8b;
    min-height: 100vh;
    background: var(--bg);
    color: var(--text);
    font-family: Inter, ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
}

.shared-topbar {
    display: grid;
    grid-template-columns: 260px minmax(320px, 820px) auto;
    align-items: center;
    gap: 28px;
    min-height: 76px;
    padding: 0 36px;
    border-bottom: 1px solid var(--line);
    background: #fff;
}

.brand {
    display: flex;
    align-items: center;
    gap: 12px;
}

.brand-logo {
    display: grid;
    place-items: center;
    width: 38px;
    height: 38px;
    border-radius: 11px;
    background: var(--teal);
    color: #fff;
    font-weight: 800;
}

.brand strong {
    display: block;
    font-size: 15px;
}

.brand small {
    display: block;
    color: var(--muted);
    font-size: 12px;
}

.search-box {
    display: flex;
    align-items: center;
    gap: 14px;
    height: 54px;
    padding: 0 20px;
    border-radius: 999px;
    background: #edf3fb;
    color: #526174;
}

.search-box svg {
    width: 21px;
    height: 21px;
}

.search-box input {
    flex: 1;
    border: 0;
    outline: 0;
    background: transparent;
    color: #334155;
    font-size: 15px;
}

.public-badge {
    justify-self: end;
    padding: 9px 14px;
    border-radius: 999px;
    background: #d8f8e9;
    color: var(--teal);
    font-size: 13px;
    font-weight: 800;
}

.shared-shell {
    width: min(1280px, calc(100% - 64px));
    min-height: calc(100vh - 116px);
    margin: 24px auto;
    padding: 46px 48px 28px;
    border-radius: 26px;
    background: rgba(255, 255, 255, .96);
}

.shared-toolbar {
    display: flex;
    align-items: flex-start;
    justify-content: space-between;
    gap: 24px;
    margin-bottom: 30px;
}

.shared-toolbar h1 {
    margin: 0 0 18px;
    color: #172033;
    font-size: 29px;
    font-weight: 600;
    letter-spacing: -.04em;
    line-height: 1;
}

.shared-toolbar h1 span {
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

.summary-card {
    display: grid;
    grid-template-columns: 1fr auto auto;
    align-items: center;
    gap: 20px;
    margin-bottom: 34px;
    padding: 24px;
    border: 1px solid var(--line);
    border-radius: 22px;
    background: #fbfdff;
}

.eyebrow {
    display: inline-flex;
    margin-bottom: 8px;
    padding: 6px 10px;
    border-radius: 999px;
    background: #d8f8e9;
    color: var(--teal);
    font-size: 12px;
    font-weight: 800;
}

.summary-card h2 {
    margin: 0 0 6px;
    font-size: 24px;
    letter-spacing: -.03em;
}

.summary-card p {
    margin: 0;
    color: var(--muted);
    font-size: 14px;
}

.summary-count {
    display: grid;
    place-items: center;
    min-width: 86px;
    min-height: 76px;
    border-radius: 18px;
    background: #fff;
    border: 1px solid var(--line);
}

.summary-count strong {
    font-size: 24px;
}

.summary-count span {
    color: var(--muted);
    font-size: 13px;
}

.table-head,
.table-row {
    display: grid;
    grid-template-columns: minmax(300px, 1.55fr) minmax(120px, .45fr) minmax(140px, .5fr) minmax(120px, .42fr) 86px;
    align-items: center;
    column-gap: 24px;
}

.table-head {
    min-height: 60px;
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
}

.sort-arrow {
    margin-left: 8px;
    color: #1e293b;
    font-size: 22px;
}

.table-row {
    min-height: 82px;
    border-bottom: 1px solid var(--line);
    color: #53657f;
    font-size: 15px;
    text-decoration: none;
    transition: background .16s ease, box-shadow .16s ease;
}

.table-row:hover {
    background: #fbfdff;
    box-shadow: inset 4px 0 0 rgba(0, 159, 139, .18);
}

.name-cell {
    display: flex;
    align-items: center;
    gap: 18px;
    min-width: 0;
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

.access-pill {
    display: inline-flex;
    padding: 7px 11px;
    border-radius: 999px;
    background: #d8f8e9;
    color: var(--teal);
    font-size: 12px;
    font-weight: 800;
}

.row-action {
    padding-right: 22px;
    text-align: right;
    color: var(--teal);
    font-weight: 700;
}

.row-action a {
    color: var(--teal);
    text-decoration: none;
}

.empty-state {
    min-height: 320px;
    display: grid;
    place-items: center;
    align-content: center;
    gap: 6px;
    color: #64748b;
    text-align: center;
}

.empty-icon {
    font-size: 34px;
}

.empty-state h2 {
    margin: 0;
    color: #172033;
    font-size: 18px;
}

.empty-state p {
    margin: 0;
    font-size: 13px;
}

@media (max-width: 900px) {
    .shared-topbar {
        grid-template-columns: 1fr;
        padding: 18px;
    }

    .public-badge {
        justify-self: start;
    }

    .shared-shell {
        width: calc(100% - 28px);
        padding: 24px 18px;
    }

    .summary-card {
        grid-template-columns: 1fr;
    }

    .table-head {
        display: none;
    }

    .table-row {
        grid-template-columns: 1fr;
        gap: 8px;
        padding: 14px 0;
    }

    .name-cell {
        padding-left: 0;
    }

    .row-action {
        text-align: left;
        padding-right: 0;
    }
}
</style>
