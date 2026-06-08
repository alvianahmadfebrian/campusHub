<script setup>
import { Link, router, usePage } from '@inertiajs/vue3'
import { computed, onBeforeUnmount, ref, watch } from 'vue'
import { useDarkMode } from '@/composables/useDarkMode'

const page = usePage()
const { isDark, toggleDark } = useDarkMode()

const sidebarOpen = ref(false)

const user = computed(() => page.props.auth?.user ?? {})
const flash = computed(() => page.props.flash ?? {})
const currentPath = computed(() => page.url.split('?')[0])

function isActive(path) {
    return currentPath.value === path || currentPath.value.startsWith(`${path}/`)
}

function openSidebar() {
    sidebarOpen.value = true
}

function closeSidebar() {
    sidebarOpen.value = false
}

function logout() {
    closeSidebar()
    router.post('/logout')
}

watch(
    () => page.url,
    () => closeSidebar()
)

watch(sidebarOpen, (isOpen) => {
    if (typeof document !== 'undefined') {
        document.body.style.overflow = isOpen ? 'hidden' : ''
    }
})

onBeforeUnmount(() => {
    if (typeof document !== 'undefined') {
        document.body.style.overflow = ''
    }
})
</script>

<template>
    <div class="admin-shell">
        <transition name="sidebar-fade">
            <div
                v-show="sidebarOpen"
                class="admin-sidebar-overlay"
                aria-hidden="true"
                @click="closeSidebar"
            ></div>
        </transition>

        <aside class="admin-sidebar" :class="{ 'mobile-open': sidebarOpen }">
            <button
                type="button"
                class="admin-sidebar-close"
                aria-label="Tutup menu"
                @click="closeSidebar"
            >
                <svg width="19" height="19" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M18 6 6 18"/>
                    <path d="M6 6l12 12"/>
                </svg>
            </button>

            <div class="admin-brand">
                <div class="admin-brand-icon">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                        <path d="M22 10v6M2 10l10-5 10 5-10 5z"/>
                        <path d="M6 12v5c3 3 9 3 12 0v-5"/>
                    </svg>
                </div>

                <div class="admin-brand-texts">
                    <p class="admin-brand-name">CampusHub</p>
                    <p class="admin-brand-sub">Portal Akademik</p>
                </div>
            </div>

            <nav class="admin-nav">
                <Link href="/admin/dashboard" class="admin-nav-item" :class="{ active: isActive('/admin/dashboard') }" @click="closeSidebar">
                    Dashboard
                </Link>

                <Link href="/admin/akademik" class="admin-nav-item" :class="{ active: isActive('/admin/akademik') }" @click="closeSidebar">
                    Akademik
                </Link>

                <Link href="/admin/konten" class="admin-nav-item" :class="{ active: isActive('/admin/konten') }" @click="closeSidebar">
                    Manajemen Konten
                </Link>

                <Link href="/drive" class="admin-nav-item" :class="{ active: isActive('/drive') }" @click="closeSidebar">
                    Drive
                </Link>

                <Link href="/chat" class="admin-nav-item" :class="{ active: isActive('/chat') }" @click="closeSidebar">
                    Chatbot AI
                </Link>

                <Link href="/admin/laporan" class="admin-nav-item" :class="{ active: isActive('/admin/laporan') }" @click="closeSidebar">
                    Laporan
                </Link>

                <Link href="/admin/pengaturan" class="admin-nav-item" :class="{ active: isActive('/admin/pengaturan') }" @click="closeSidebar">
                    Pengaturan
                </Link>
            </nav>

            <div class="admin-sidebar-footer">
                <button
                    type="button"
                    class="admin-footer-link admin-theme-toggle"
                    @click="toggleDark"
                >
                    {{ isDark ? 'Mode Terang' : 'Mode Gelap' }}
                </button>

                <Link href="/dashboard" class="admin-footer-link" @click="closeSidebar">
                    Portal Mahasiswa
                </Link>

                <button
                    type="button"
                    class="admin-footer-link admin-logout-btn"
                    @click="logout"
                >
                    Logout
                </button>
            </div>
        </aside>

        <div class="admin-main">
            <header class="admin-topbar">
                <button
                    type="button"
                    class="admin-mobile-menu-btn"
                    aria-label="Buka menu navigasi"
                    :aria-expanded="sidebarOpen"
                    @click="openSidebar"
                >
                    ☰
                </button>

                <div class="admin-topbar-spacer"></div>

                <div class="admin-topbar-right">
                    <button
                        type="button"
                        class="admin-icon-btn"
                        :title="isDark ? 'Mode Terang' : 'Mode Gelap'"
                        @click="toggleDark"
                    >
                        {{ isDark ? '☀️' : '🌙' }}
                    </button>

                    <div class="admin-user-pill">
                        <div class="admin-user-avatar">
                            <img
                                v-if="user.avatar || user.avatar_url"
                                :src="user.avatar || user.avatar_url"
                                alt="avatar"
                            />

                            <span v-else>
                                {{ (user.nama || 'A').charAt(0).toUpperCase() }}
                            </span>
                        </div>

                        <div class="admin-user-meta">
                            <p class="admin-user-name">
                                {{ user.nama || 'Administrator' }}
                            </p>

                            <p class="admin-user-role">
                                Admin CampusHub
                            </p>
                        </div>
                    </div>
                </div>
            </header>

            <div
                v-if="flash.success"
                class="admin-flash"
                style="margin: 16px 28px 0;"
            >
                {{ flash.success }}
            </div>

            <main class="admin-body">
                <slot />
            </main>
        </div>
    </div>
</template>

<style scoped>
.admin-mobile-menu-btn,
.admin-sidebar-close {
    display: none;
}

.admin-sidebar-overlay {
    display: none;
}

.sidebar-fade-enter-active,
.sidebar-fade-leave-active {
    transition: opacity .18s ease;
}

.sidebar-fade-enter-from,
.sidebar-fade-leave-to {
    opacity: 0;
}

@media (max-width: 900px) {
    .admin-shell {
        display: block !important;
        min-height: 100vh;
    }

    .admin-mobile-menu-btn {
        display: grid;
        place-items: center;
        flex-shrink: 0;
        width: 39px;
        height: 39px;
        border: 1px solid var(--admin-border, #e2e8f0);
        border-radius: 11px;
        background: var(--admin-surface, #fff);
        color: var(--admin-text, #334155);
        cursor: pointer;
    }

    .admin-sidebar-overlay {
        position: fixed;
        inset: 0;
        z-index: 999;
        display: block;
        background: rgba(15,23,42,.38);
    }

    .admin-sidebar {
        position: fixed !important;
        z-index: 1000;
        top: 0;
        left: 0;
        bottom: 0;
        width: 265px !important;
        height: 100vh !important;
        overflow-y: auto;
        transform: translateX(-104%);
        transition: transform .22s ease;
        will-change: transform;
    }

    .admin-sidebar.mobile-open {
        transform: translateX(0);
    }

    .admin-sidebar-close {
        position: absolute;
        top: 16px;
        right: 12px;
        z-index: 2;
        display: grid;
        place-items: center;
        width: 35px;
        height: 35px;
        border: 1px solid var(--admin-border, #e2e8f0);
        border-radius: 10px;
        background: var(--admin-surface, #fff);
        cursor: pointer;
    }

    .admin-brand {
        padding-right: 54px !important;
    }

    .admin-main {
        width: 100% !important;
        margin-left: 0 !important;
    }

    .admin-topbar {
        display: flex;
        align-items: center;
        gap: 14px;
    }

    .admin-topbar-spacer {
        flex: 1;
    }

    .admin-topbar-right {
        margin-left: auto;
        display: flex;
        align-items: center;
        gap: 14px;
    }

    .admin-user-pill {
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 6px 12px 6px 6px;
        border: 1px solid var(--admin-border, #e2e8f0);
        border-radius: 14px;
        background: var(--admin-surface, #ffffff);
    }

    .admin-user-avatar {
        display: grid;
        place-items: center;
        width: 36px;
        height: 36px;
        overflow: hidden;
        border-radius: 999px;
        background: #d1fae5;
        color: #0f766e;
        font-size: 14px;
        font-weight: 700;
        flex-shrink: 0;
    }

    .admin-user-avatar img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .admin-user-meta {
        line-height: 1.1;
    }

    .admin-user-name {
        margin: 0;
        color: var(--admin-text, #0f172a);
        font-size: 13px;
        font-weight: 700;
    }

    .admin-user-role {
        margin: 3px 0 0;
        color: var(--admin-muted, #64748b);
        font-size: 11px;
    }
}

@media (max-width: 560px) {
    .admin-user-meta {
        display: none;
    }

    .admin-user-pill {
        padding-right: 6px;
    }
}
</style>
