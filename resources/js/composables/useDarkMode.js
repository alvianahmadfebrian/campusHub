import { ref, watch } from 'vue'

const isDark = ref(false)

function applyTheme(dark) {
    if (typeof document !== 'undefined') {
        document.documentElement.classList.toggle('dark', dark)
    }
}

// Initialise theme once on client-side load (module level)
if (typeof window !== 'undefined') {
    const savedTheme = localStorage.getItem('theme')
    if (savedTheme) {
        isDark.value = savedTheme === 'dark'
    } else {
        isDark.value = window.matchMedia('(prefers-color-scheme: dark)').matches
    }

    // Apply initial state
    applyTheme(isDark.value)

    // Watch isDark at module level so it stays active across page transitions
    watch(isDark, (newValue) => {
        applyTheme(newValue)
        localStorage.setItem('theme', newValue ? 'dark' : 'light')
    }, { immediate: true })
}

export function useDarkMode() {
    function toggleDark() {
        isDark.value = !isDark.value
    }

    return {
        isDark,
        toggleDark,
    }
}