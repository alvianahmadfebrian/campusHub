<script setup>
import { Head, router, useForm } from '@inertiajs/vue3'
import { computed, nextTick, ref, watch } from 'vue'
import AppLayout from '@/Layouts/AppLayout.vue'
import AdminLayout from '@/Layouts/AdminLayout.vue'

const props = defineProps({
    messages: {
        type: Array,
        default: () => [],
    },
    documents: {
        type: Array,
        default: () => [],
    },
    scope: {
        type: Object,
        default: () => ({
            nama: 'Pengguna',
            role: 'mahasiswa',
            jurusan: '-',
        }),
    },
    limits: {
        type: Object,
        default: () => ({
            documentMaxMb: 10,
            documentTypes: 'PDF, TXT, MD, CSV, JSON',
        }),
    },
})

const CurrentLayout = computed(() => {
    return props.scope.role === 'admin' ? AdminLayout : AppLayout
})

const fileInput = ref(null)
const threadRef = ref(null)
const pendingUploadedDocument = ref(false)

const suggestions = computed(() => {
    if (props.scope.role === 'admin') {
        return [
            'Berapa jumlah mahasiswa yang terdaftar?',
            'Ada pengumuman terbaru apa saja?',
            'Event mendatang yang tersedia apa saja?',
            'Bantu analisis dokumen yang saya upload.',
        ]
    }

    return [
        'Ada pengumuman terbaru untuk saya?',
        'Materi apa yang tersedia untuk jurusan saya?',
        'Event terdekat yang bisa saya ikuti?',
        'File apa saja yang ada di Drive saya?',
    ]
})

const form = useForm({
    message: '',
    document_id: '',
})

const uploadForm = useForm({
    file: null,
})

const activeDocument = computed(() => {
    return props.documents.find((document) => document.id === form.document_id) || null
})

watch(
    () => props.documents.map((document) => document.id).join(','),
    () => {
        if (!pendingUploadedDocument.value) return

        form.document_id = props.documents[0]?.id || ''
        pendingUploadedDocument.value = false
    }
)

watch(
    () => props.messages.length,
    async () => {
        await nextTick()
        scrollToBottom()
    },
    { immediate: true }
)

function scrollToBottom() {
    if (!threadRef.value) return
    threadRef.value.scrollTop = threadRef.value.scrollHeight
}

function useSuggestion(text) {
    form.message = text
}

function sendMessage() {
    if (!form.message.trim() || uploadForm.processing) return

    form.post('/chat/messages', {
        preserveScroll: true,
        onSuccess: () => {
            form.reset('message')
            form.document_id = ''
        },
    })
}

function openFilePicker() {
    fileInput.value?.click()
}

function handleFileSelected(event) {
    const file = event.target.files?.[0]
    if (!file) return

    uploadForm.file = file
    pendingUploadedDocument.value = true

    uploadForm.post('/chat/documents', {
        forceFormData: true,
        preserveScroll: true,
        onSuccess: () => {
            uploadForm.reset('file')
            if (fileInput.value) fileInput.value.value = ''
        },
        onError: () => {
            pendingUploadedDocument.value = false
            if (fileInput.value) fileInput.value.value = ''
        },
    })
}

function detachDocument() {
    form.document_id = ''
}

function deleteDocument(document) {
    if (!window.confirm(`Hapus dokumen "${document.nama_asli}" dari chatbot?`)) return

    form.document_id = ''

    router.delete(`/chat/documents/${document.id}`, {
        preserveScroll: true,
    })
}

function clearChat() {
    if (!window.confirm('Hapus seluruh riwayat percakapan chatbot?')) return

    router.delete('/chat', {
        preserveScroll: true,
    })
}

function formatTime(value) {
    if (!value) return ''

    return new Intl.DateTimeFormat('id-ID', {
        day: 'numeric',
        month: 'short',
        hour: '2-digit',
        minute: '2-digit',
    }).format(new Date(value))
}

function formatSize(bytes) {
    const size = Number(bytes || 0)

    if (size < 1024) return `${size} B`
    if (size < 1024 * 1024) return `${(size / 1024).toFixed(1)} KB`

    return `${(size / (1024 * 1024)).toFixed(1)} MB`
}

function cleanAssistantText(content) {
    return String(content ?? '')
        .replace(/\r\n?/g, '\n')
        .replace(/```[a-zA-Z0-9_-]*\n?/g, '')
        .replace(/```/g, '')
        .replace(/^Berikut isi file\s+[^:]+:\s*/i, '')
        .replace(/^Berikut adalah isi file\s+[^:]+:\s*/i, '')
        .replace(/^Berikut kode\s+[^:]+:\s*/i, '')
        .trim()
}

function escapeHtml(value) {
    return String(value ?? '')
        .replaceAll('&', '&amp;')
        .replaceAll('<', '&lt;')
        .replaceAll('>', '&gt;')
        .replaceAll('"', '&quot;')
        .replaceAll("'", '&#039;')
}

function renderInline(text) {
    return text
        .replace(/`([^`]+)`/g, '<code>$1</code>')
        .replace(/\*\*([^*]+)\*\*/g, '<strong>$1</strong>')
        .replace(/\*([^*]+)\*/g, '<em>$1</em>')
}

function parseTableRow(line) {
    return line
        .trim()
        .replace(/^\|/, '')
        .replace(/\|$/, '')
        .split('|')
        .map((cell) => renderInline(cell.trim()))
}

function isTableDivider(line) {
    const cells = line
        .trim()
        .replace(/^\|/, '')
        .replace(/\|$/, '')
        .split('|')

    return cells.length > 0 && cells.every((cell) => /^\s*:?-{3,}:?\s*$/.test(cell))
}

function looksLikeCode(line) {
    const trimmed = line.trim()

    return (
        trimmed.startsWith('&lt;?php') ||
        trimmed.startsWith('&lt;!DOCTYPE') ||
        trimmed.startsWith('&lt;html') ||
        trimmed.startsWith('&lt;head') ||
        trimmed.startsWith('&lt;body') ||
        trimmed.startsWith('&lt;style') ||
        trimmed.startsWith('&lt;script') ||
        trimmed.startsWith('&lt;template') ||
        trimmed.startsWith('&lt;/') ||
        trimmed.startsWith('//') ||
        trimmed.startsWith('/*') ||
        trimmed.startsWith('*') ||
        trimmed.startsWith('*/') ||
        trimmed.startsWith('import ') ||
        trimmed.startsWith('export ') ||
        trimmed.startsWith('const ') ||
        trimmed.startsWith('let ') ||
        trimmed.startsWith('var ') ||
        trimmed.startsWith('function ') ||
        trimmed.startsWith('if ') ||
        trimmed.startsWith('return ') ||
        trimmed.includes('{') ||
        trimmed.includes('}') ||
        trimmed.includes(';') ||
        trimmed.includes('=&gt;')
    )
}

function renderAssistantMessage(content) {
    const lines = escapeHtml(cleanAssistantText(content)).split('\n')

    let html = ''
    let index = 0
    let paragraph = []
    let codeBlock = []

    function flushParagraph() {
        if (paragraph.length === 0) return

        html += `<p>${paragraph
            .map((line) => renderInline(line.trim()))
            .join('<br>')}</p>`

        paragraph = []
    }

    function flushCode() {
        if (codeBlock.length === 0) return

        html += `<pre class="chat-code-block"><code>${codeBlock.join('\n')}</code></pre>`
        codeBlock = []
    }

    while (index < lines.length) {
        const line = lines[index]
        const trimmed = line.trim()

        if (!trimmed) {
            flushParagraph()

            if (codeBlock.length > 0) {
                codeBlock.push('')
            }

            index++
            continue
        }

        if (looksLikeCode(line)) {
            flushParagraph()
            codeBlock.push(line)
            index++
            continue
        }

        if (codeBlock.length > 0) {
            flushCode()
        }

        if (
            trimmed.includes('|') &&
            lines[index + 1] &&
            isTableDivider(lines[index + 1])
        ) {
            flushParagraph()

            const headers = parseTableRow(trimmed)
            const rows = []

            index += 2

            while (
                index < lines.length &&
                lines[index].trim() !== '' &&
                lines[index].includes('|')
            ) {
                rows.push(parseTableRow(lines[index]))
                index++
            }

            html += `
                <div class="chat-table-container">
                    <table class="chat-result-table">
                        <thead>
                            <tr>
                                ${headers.map((header) => `<th>${header}</th>`).join('')}
                            </tr>
                        </thead>
                        <tbody>
                            ${rows.map((row) => `
                                <tr>
                                    ${row.map((cell) => `<td>${cell}</td>`).join('')}
                                </tr>
                            `).join('')}
                        </tbody>
                    </table>
                </div>
            `

            continue
        }

        const heading = trimmed.match(/^(#{1,3})\s+(.+)$/)

        if (heading) {
            flushParagraph()

            const level = heading[1].length + 2
            html += `<h${level}>${renderInline(heading[2])}</h${level}>`

            index++
            continue
        }

        if (/^[-*]\s+/.test(trimmed)) {
            flushParagraph()

            let list = '<ul>'

            while (
                index < lines.length &&
                /^[-*]\s+/.test(lines[index].trim())
            ) {
                const item = lines[index].trim().replace(/^[-*]\s+/, '')
                list += `<li>${renderInline(item)}</li>`
                index++
            }

            list += '</ul>'
            html += list
            continue
        }

        if (/^\d+\.\s+/.test(trimmed)) {
            flushParagraph()

            let list = '<ol>'

            while (
                index < lines.length &&
                /^\d+\.\s+/.test(lines[index].trim())
            ) {
                const item = lines[index].trim().replace(/^\d+\.\s+/, '')
                list += `<li>${renderInline(item)}</li>`
                index++
            }

            list += '</ol>'
            html += list
            continue
        }

        paragraph.push(line)
        index++
    }

    flushParagraph()
    flushCode()

    return html || '<p>Tidak ada jawaban.</p>'
}
</script>

<template>
    <Head title="Chatbot CampusHub" />

    <component :is="CurrentLayout">
        <div class="chat-page">
            <header class="chat-header">
                <div>
                    <p class="chat-eyebrow">AI ASSISTANT</p>

                    <h1 class="chat-title">
                        {{ scope.role === 'admin' ? 'Chatbot Admin CampusHub' : 'Chatbot CampusHub' }}
                    </h1>

                    <p class="chat-subtitle">
                        <template v-if="scope.role === 'admin'">
                            Tanya statistik akademik, konten kampus, atau lampirkan dokumen private milik admin.
                        </template>

                        <template v-else>
                            Tanya informasi akademik, Drive, atau lampirkan dokumen pribadi untuk dianalisis.
                        </template>
                    </p>
                </div>

                <button
                    v-if="messages.length > 0"
                    type="button"
                    class="chat-clear"
                    @click="clearChat"
                >
                    <svg viewBox="0 0 24 24" fill="none">
                        <path d="M3 6h18" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" />
                        <path d="M8 6V4h8v2M7 6l1 14h8l1-14" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round" />
                    </svg>

                    Hapus Riwayat
                </button>
            </header>

            <section class="chat-security">
                <div class="chat-security-icon">
                    <svg viewBox="0 0 24 24" fill="none">
                        <path d="M12 3 20 7v5c0 5-3.5 8-8 9-4.5-1-8-4-8-9V7l8-4Z" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round" />
                        <path d="m9 12 2 2 4-5" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" />
                    </svg>
                </div>

                <div>
                    <strong>Akses aman untuk {{ scope.nama }}</strong>

                    <p v-if="scope.role === 'admin'">
                        Chatbot memakai statistik umum dan konten akademik yang dapat diakses admin.
                        Dokumen private mahasiswa lain dan Drive pribadi pengguna lain tidak diberikan ke chatbot.
                    </p>

                    <p v-else>
                        Chatbot hanya memakai data yang dapat dilihat akun login ini.
                        Jurusan: <b>{{ scope.jurusan }}</b> · Role: <b>{{ scope.role }}</b>.
                        Dokumen hanya dapat digunakan oleh akunmu sendiri.
                    </p>
                </div>
            </section>

            <section class="chat-shell">
                <div ref="threadRef" class="chat-thread">
                    <div v-if="messages.length === 0" class="chat-welcome">
                        <div class="chat-bot-avatar">
                            <svg viewBox="0 0 24 24" fill="none">
                                <path d="M12 3v3M8 3h8M5 9a3 3 0 0 1 3-3h8a3 3 0 0 1 3 3v7a3 3 0 0 1-3 3H8a3 3 0 0 1-3-3V9Z" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round" />
                                <circle cx="9" cy="12" r="1" fill="currentColor" />
                                <circle cx="15" cy="12" r="1" fill="currentColor" />
                                <path d="M9 16h6" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" />
                            </svg>
                        </div>

                        <h2>Halo! Ada yang bisa saya bantu?</h2>

                        <p>
                            Klik tombol <b>+</b> untuk melampirkan PDF atau dokumen,
                            lalu langsung tanyakan isinya.
                        </p>

                        <div class="chat-suggestions">
                            <button
                                v-for="suggestion in suggestions"
                                :key="suggestion"
                                type="button"
                                @click="useSuggestion(suggestion)"
                            >
                                {{ suggestion }}
                            </button>
                        </div>
                    </div>

                    <article
                        v-for="message in messages"
                        :key="message.id"
                        class="chat-message"
                        :class="message.role"
                    >
                        <div class="chat-avatar">
                            <svg v-if="message.role === 'assistant'" viewBox="0 0 24 24" fill="none">
                                <path d="M12 3v3M8 3h8M5 9a3 3 0 0 1 3-3h8a3 3 0 0 1 3 3v7a3 3 0 0 1-3 3H8a3 3 0 0 1-3-3V9Z" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round" />
                                <circle cx="9" cy="12" r="1" fill="currentColor" />
                                <circle cx="15" cy="12" r="1" fill="currentColor" />
                            </svg>

                            <span v-else>
                                {{ (scope.nama || 'U').charAt(0).toUpperCase() }}
                            </span>
                        </div>

                        <div class="chat-bubble-wrap">
                            <div
                                v-if="message.role === 'assistant'"
                                class="chat-bubble chat-rich-content"
                                v-html="renderAssistantMessage(message.content)"
                            ></div>

                            <div v-else class="chat-bubble">
                                <div v-if="message.document_name" class="message-document">
                                    <svg viewBox="0 0 24 24" fill="none">
                                        <path d="M14 3H7a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V8l-5-5Z" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round" />
                                        <path d="M14 3v5h5" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round" />
                                    </svg>

                                    {{ message.document_name }}
                                </div>

                                {{ message.content }}
                            </div>

                            <div class="chat-meta">
                                {{ formatTime(message.created_at) }}
                            </div>
                        </div>
                    </article>
                </div>

                <form class="chat-composer" @submit.prevent="sendMessage">
                    <div v-if="form.errors.message" class="chat-error">
                        {{ form.errors.message }}
                    </div>

                    <div v-if="form.errors.document_id" class="chat-error">
                        {{ form.errors.document_id }}
                    </div>

                    <div v-if="uploadForm.errors.file" class="chat-error">
                        {{ uploadForm.errors.file }}
                    </div>

                    <div v-if="activeDocument" class="attached-document">
                        <div class="attached-document-icon">
                            <svg viewBox="0 0 24 24" fill="none">
                                <path d="M14 3H7a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V8l-5-5Z" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round" />
                                <path d="M14 3v5h5" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round" />
                            </svg>
                        </div>

                        <div class="attached-document-info">
                            <strong>{{ activeDocument.nama_asli }}</strong>

                            <span>
                                {{ activeDocument.extension.toUpperCase() }}
                                · {{ formatSize(activeDocument.ukuran_bytes) }}
                                · Digunakan satu kali untuk pertanyaan berikutnya
                            </span>
                        </div>

                        <button type="button" class="attached-remove" title="Lepas lampiran" @click="detachDocument">
                            <svg viewBox="0 0 24 24" fill="none">
                                <path d="M18 6 6 18M6 6l12 12" stroke="currentColor" stroke-width="2" stroke-linecap="round" />
                            </svg>
                        </button>

                        <button type="button" class="attached-delete" title="Hapus dokumen" @click="deleteDocument(activeDocument)">
                            <svg viewBox="0 0 24 24" fill="none">
                                <path d="M3 6h18" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" />
                                <path d="M8 6V4h8v2M7 6l1 14h8l1-14" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round" />
                            </svg>
                        </button>
                    </div>

                    <div class="composer-box">
                        <button
                            type="button"
                            class="composer-plus"
                            :disabled="uploadForm.processing"
                            title="Lampirkan dokumen"
                            @click="openFilePicker"
                        >
                            <svg v-if="!uploadForm.processing" viewBox="0 0 24 24" fill="none">
                                <path d="M12 5v14M5 12h14" stroke="currentColor" stroke-width="2" stroke-linecap="round" />
                            </svg>

                            <svg v-else class="spinner" viewBox="0 0 24 24" fill="none">
                                <path d="M12 3a9 9 0 1 1-6.364 2.636" stroke="currentColor" stroke-width="2" stroke-linecap="round" />
                            </svg>
                        </button>

                        <input
                            ref="fileInput"
                            class="hidden-file-input"
                            type="file"
                            accept=".pdf,.txt,.md,.csv,.json"
                            @change="handleFileSelected"
                        />

                        <textarea
                            v-model="form.message"
                            class="chat-input"
                            :placeholder="activeDocument
                                ? 'Tanyakan sesuatu tentang dokumen ini...'
                                : 'Tanyakan sesuatu tentang CampusHub...'"
                            rows="1"
                            maxlength="3000"
                            @keydown.enter.exact.prevent="sendMessage"
                        ></textarea>

                        <button
                            class="chat-send"
                            type="submit"
                            :disabled="form.processing || uploadForm.processing || !form.message.trim()"
                            title="Kirim pesan"
                        >
                            <svg viewBox="0 0 24 24" fill="none">
                                <path d="M22 2 11 13" stroke="currentColor" stroke-width="2" stroke-linecap="round" />
                                <path d="m22 2-7 20-4-9-9-4 20-7Z" stroke="currentColor" stroke-width="2" stroke-linejoin="round" />
                            </svg>
                        </button>
                    </div>

                    <p class="chat-note">
                        Klik + untuk upload {{ limits.documentTypes }} · Maks. {{ limits.documentMaxMb }} MB
                    </p>
                </form>
            </section>
        </div>
    </component>
</template>

<style scoped>
.chat-page {
    display: flex;
    flex-direction: column;
    gap: 16px;
}

.chat-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    gap: 18px;
}

.chat-eyebrow {
    margin: 0 0 7px;
    color: #0d9488;
    font-size: 11px;
    font-weight: 800;
    letter-spacing: 0.11em;
}

.chat-title {
    margin: 0 0 8px;
    color: var(--text);
    font-size: 30px;
    line-height: 1.12;
}

.chat-subtitle {
    margin: 0;
    color: var(--muted);
    font-size: 14px;
}

.chat-clear {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 10px 13px;
    border: 1px solid var(--border);
    border-radius: 11px;
    background: var(--surface);
    color: #dc2626;
    cursor: pointer;
    font-weight: 650;
}

.chat-clear:hover {
    border-color: #fecaca;
    background: #fef2f2;
}

.chat-clear svg {
    width: 17px;
    height: 17px;
}

.chat-security {
    display: flex;
    align-items: flex-start;
    gap: 12px;
    padding: 13px 15px;
    border-radius: 14px;
    border: 1px solid #99f6e4;
    background: #f0fdf9;
    color: #134e4a;
}

.chat-security-icon {
    display: grid;
    place-items: center;
    flex-shrink: 0;
    width: 35px;
    height: 35px;
    border-radius: 10px;
    background: #ccfbf1;
}

.chat-security-icon svg {
    width: 20px;
    height: 20px;
}

.chat-security strong {
    display: block;
    margin: 1px 0 4px;
    font-size: 14px;
}

.chat-security p {
    margin: 0;
    font-size: 13px;
    line-height: 1.55;
}

.chat-shell {
    overflow: hidden;
    border: 1px solid var(--border);
    border-radius: 18px;
    background: var(--surface);
}

.chat-thread {
    height: calc(100vh - 390px);
    min-height: 410px;
    overflow-y: auto;
    padding: 22px;
    background: var(--background);
}

.chat-welcome {
    max-width: 680px;
    margin: 48px auto;
    text-align: center;
}

.chat-bot-avatar {
    display: grid;
    place-items: center;
    width: 58px;
    height: 58px;
    margin: 0 auto 15px;
    border-radius: 17px;
    color: #0d9488;
    background: #f0fdf9;
}

.chat-bot-avatar svg {
    width: 31px;
    height: 31px;
}

.chat-welcome h2 {
    margin: 0 0 8px;
    color: var(--text);
    font-size: 21px;
}

.chat-welcome p {
    margin: 0 auto 20px;
    color: var(--muted);
    line-height: 1.55;
}

.chat-suggestions {
    display: grid;
    grid-template-columns: repeat(2, minmax(0, 1fr));
    gap: 9px;
}

.chat-suggestions button {
    padding: 12px 13px;
    border: 1px solid var(--border);
    border-radius: 12px;
    background: var(--surface);
    color: var(--text);
    cursor: pointer;
    text-align: left;
    font-size: 13px;
}

.chat-suggestions button:hover {
    border-color: var(--primary);
    background: var(--primary-soft);
    color: var(--primary);
}

.chat-message {
    display: flex;
    align-items: flex-end;
    gap: 10px;
    margin-bottom: 17px;
}

.chat-message.user {
    flex-direction: row-reverse;
}

.chat-avatar {
    display: grid;
    place-items: center;
    flex-shrink: 0;
    width: 35px;
    height: 35px;
    border-radius: 10px;
    background: var(--primary-soft);
    color: var(--primary);
    font-size: 14px;
    font-weight: 700;
}

.chat-avatar svg {
    width: 20px;
    height: 20px;
}

.chat-message.user .chat-avatar {
    background: #0d9488;
    color: #ffffff;
}

.chat-bubble-wrap {
    max-width: min(84%, 920px);
}

.chat-message.user .chat-bubble-wrap {
    text-align: right;
}

.chat-bubble {
    padding: 12px 14px;
    border: 1px solid var(--border);
    border-radius: 15px 15px 15px 4px;
    background: var(--surface);
    color: var(--text);
    font-size: 14px;
    line-height: 1.65;
    text-align: left;
    white-space: pre-wrap;
    word-break: break-word;
}

.chat-message.user .chat-bubble {
    border-radius: 15px 15px 4px 15px;
    border-color: #0d9488;
    background: #0d9488;
    color: #ffffff;
}

.message-document {
    display: flex;
    align-items: center;
    gap: 7px;
    margin-bottom: 8px;
    padding: 7px 9px;
    border-radius: 9px;
    background: rgba(255, 255, 255, 0.14);
    font-size: 12px;
    font-weight: 600;
}

.message-document svg {
    width: 15px;
    height: 15px;
}

.chat-rich-content {
    padding: 16px 18px;
    white-space: normal;
}

.chat-rich-content :deep(p) {
    margin: 0 0 12px;
    color: var(--text);
    line-height: 1.75;
}

.chat-rich-content :deep(p:last-child) {
    margin-bottom: 0;
}

.chat-rich-content :deep(strong) {
    color: var(--text);
    font-weight: 700;
}

.chat-rich-content :deep(code) {
    display: inline-flex;
    padding: 2px 6px;
    border-radius: 6px;
    background: #eef6ff;
    color: #0d9488;
    font-family: Consolas, Monaco, monospace;
    font-size: 12px;
}

.chat-rich-content :deep(.chat-code-block) {
    overflow-x: auto;
    margin: 12px 0;
    padding: 14px 16px;
    border: 1px solid #1e293b;
    border-radius: 14px;
    background: #0f172a;
    color: #e2e8f0;
    font-family: Consolas, Monaco, monospace;
    font-size: 13px;
    line-height: 1.65;
    white-space: pre;
}

.chat-rich-content :deep(.chat-code-block code) {
    display: block;
    padding: 0;
    border-radius: 0;
    background: transparent;
    color: inherit;
    font-size: inherit;
}

.chat-rich-content :deep(h3),
.chat-rich-content :deep(h4),
.chat-rich-content :deep(h5) {
    margin: 0 0 10px;
    color: var(--text);
    font-weight: 750;
}

.chat-rich-content :deep(h3) {
    font-size: 17px;
}

.chat-rich-content :deep(h4) {
    font-size: 15px;
}

.chat-rich-content :deep(h5) {
    font-size: 14px;
}

.chat-rich-content :deep(ul),
.chat-rich-content :deep(ol) {
    margin: 10px 0 14px;
    padding-left: 22px;
    color: var(--text);
}

.chat-rich-content :deep(li) {
    margin-bottom: 7px;
    line-height: 1.65;
}

.chat-rich-content :deep(.chat-table-container) {
    overflow-x: auto;
    margin: 14px 0;
    border: 1px solid var(--border);
    border-radius: 13px;
    background: var(--surface);
}

.chat-rich-content :deep(.chat-result-table) {
    width: 100%;
    border-collapse: collapse;
    font-size: 13px;
}

.chat-rich-content :deep(.chat-result-table thead) {
    background: var(--background);
}

.chat-rich-content :deep(.chat-result-table th) {
    padding: 11px 13px;
    border-bottom: 1px solid var(--border);
    color: var(--muted);
    font-size: 11px;
    font-weight: 750;
    letter-spacing: 0.05em;
    text-align: left;
    text-transform: uppercase;
    white-space: nowrap;
}

.chat-rich-content :deep(.chat-result-table td) {
    padding: 12px 13px;
    border-bottom: 1px solid var(--border);
    color: var(--text);
    vertical-align: top;
}

.chat-rich-content :deep(.chat-result-table tbody tr:last-child td) {
    border-bottom: none;
}

.chat-rich-content :deep(.chat-result-table tbody tr:hover) {
    background: #f8faff;
}

.chat-meta {
    margin-top: 5px;
    color: #94a3b8;
    font-size: 11px;
}

.chat-composer {
    padding: 13px 15px 12px;
    border-top: 1px solid var(--border);
    background: var(--surface);
}

.chat-error {
    margin-bottom: 10px;
    padding: 10px 12px;
    border-radius: 10px;
    background: #fef2f2;
    color: #b91c1c;
    font-size: 13px;
}

.attached-document {
    display: flex;
    align-items: center;
    gap: 10px;
    max-width: 550px;
    margin-bottom: 10px;
    padding: 9px 10px;
    border: 1px solid var(--border);
    border-radius: 13px;
    background: var(--background);
}

.attached-document-icon {
    display: grid;
    place-items: center;
    flex-shrink: 0;
    width: 37px;
    height: 37px;
    border-radius: 10px;
    background: var(--primary-soft);
    color: var(--primary);
}

.attached-document-icon svg {
    width: 20px;
    height: 20px;
}

.attached-document-info {
    display: flex;
    flex: 1;
    flex-direction: column;
    gap: 2px;
    min-width: 0;
}

.attached-document-info strong {
    overflow: hidden;
    color: var(--text);
    font-size: 12px;
    text-overflow: ellipsis;
    white-space: nowrap;
}

.attached-document-info span {
    color: var(--muted);
    font-size: 11px;
}

.attached-remove,
.attached-delete {
    display: grid;
    place-items: center;
    flex-shrink: 0;
    width: 30px;
    height: 30px;
    border: none;
    border-radius: 8px;
    background: transparent;
    cursor: pointer;
}

.attached-remove {
    color: #64748b;
}

.attached-remove:hover {
    background: #e2e8f0;
    color: #0f172a;
}

.attached-delete {
    color: #dc2626;
}

.attached-delete:hover {
    background: #fef2f2;
}

.attached-remove svg,
.attached-delete svg {
    width: 16px;
    height: 16px;
}

.composer-box {
    display: flex;
    align-items: flex-end;
    gap: 10px;
    padding: 8px 9px;
    border: 1px solid var(--border);
    border-radius: 20px;
    background: var(--background);
}

.composer-box:focus-within {
    border-color: var(--primary);
    box-shadow: 0 0 0 3px rgba(13, 148, 136, 0.1);
}

.composer-plus {
    display: grid;
    place-items: center;
    flex-shrink: 0;
    width: 41px;
    height: 41px;
    border: none;
    border-radius: 999px;
    background: var(--surface);
    color: var(--muted);
    cursor: pointer;
}

.composer-plus:hover {
    background: #e2e8f0;
    color: #0f172a;
}

.composer-plus:disabled {
    cursor: not-allowed;
    opacity: 0.6;
}

.composer-plus svg {
    width: 21px;
    height: 21px;
}

.spinner {
    animation: spin 0.8s linear infinite;
}

.hidden-file-input {
    display: none;
}

.chat-input {
    flex: 1;
    min-height: 41px;
    max-height: 130px;
    padding: 11px 4px;
    border: none;
    background: transparent;
    color: var(--text);
    font: inherit;
    font-size: 14px;
    outline: none;
    resize: vertical;
}

.chat-input::placeholder {
    color: #94a3b8;
}

.chat-send {
    display: grid;
    place-items: center;
    flex-shrink: 0;
    width: 43px;
    height: 43px;
    border: none;
    border-radius: 999px;
    background: #0d9488;
    color: #ffffff;
    cursor: pointer;
}

.chat-send:hover {
    background: #0f766e;
}

.chat-send:disabled {
    cursor: not-allowed;
    opacity: 0.45;
}

.chat-send svg {
    width: 20px;
    height: 20px;
}

.chat-note {
    margin: 8px 4px 0;
    color: #94a3b8;
    font-size: 11px;
}

@keyframes spin {
    to {
        transform: rotate(360deg);
    }
}

@media (max-width: 700px) {
    .chat-header {
        flex-direction: column;
    }

    .chat-thread {
        min-height: 350px;
        padding: 14px;
    }

    .chat-suggestions {
        grid-template-columns: 1fr;
    }

    .chat-bubble-wrap {
        max-width: 88%;
    }

    .attached-document {
        max-width: 100%;
    }
}

:global(html.dark) .chat-security {
    border-color: #0d9488;
    background: #134e4a;
    color: #99f6e4;
}

:global(html.dark) .chat-security-icon {
    background: rgba(13, 148, 136, 0.3);
}

:global(html.dark) .chat-thread {
    scrollbar-color: #334155 transparent;
}

:global(html.dark) .chat-thread::-webkit-scrollbar-thumb {
    background: #334155;
}
</style>
