<!-- purpose: Shared layout with a minimal top header and responsive content area -->
<template>
  <div class="min-h-screen bg-lightBg text-lightText transition-colors duration-300 dark:bg-slate-950 dark:text-slate-100">
    <header class="sticky top-0 z-40 border-b border-lightBorder bg-lightCard backdrop-blur shadow-sm transition-colors duration-300 dark:border-slate-800/70 dark:bg-slate-950/90 dark:shadow-none">
      <div class="flex w-full items-center justify-between gap-4 px-4 py-3 sm:px-6">
        <NuxtLink to="/" class="flex items-center gap-2 text-lg font-semibold tracking-wide text-lightHeading transition-colors hover:text-blue-600 dark:text-slate-100 dark:hover:text-sky-300">
          Task Manager
        </NuxtLink>

        <div class="flex items-center gap-2">
          <nav class="flex items-center gap-2 text-sm font-medium text-lightText dark:text-slate-300">
            <NuxtLink
              v-for="item in navItems"
              :key="item.to"
              :to="item.to"
              class="group flex items-center gap-2 rounded-md px-3 py-2 text-slate-600 transition hover:bg-blue-50 hover:text-blue-600 dark:text-slate-300 dark:hover:bg-slate-900/70 dark:hover:text-sky-200"
              active-class="bg-blue-100 text-blue-700 shadow-sm dark:bg-sky-500/20 dark:text-sky-200 dark:shadow-sm"
            >
              <component :is="item.icon" class="h-4 w-4 text-current transition-colors dark:text-current" />
              <span class="hidden sm:inline">{{ item.label }}</span>
            </NuxtLink>
          </nav>
          <button
            type="button"
            class="inline-flex h-9 w-9 items-center justify-center rounded-full border border-lightBorder text-lightText transition hover:border-blue-400 hover:text-blue-600 dark:border-slate-600 dark:text-slate-200 dark:hover:border-sky-400 dark:hover:text-sky-200"
            :aria-label="isDark ? 'ライトモードに切り替え' : 'ダークモードに切り替え'"
            @click="toggleTheme"
          >
            <SunIcon v-if="isDark" class="h-5 w-5" />
            <MoonIcon v-else class="h-5 w-5" />
          </button>
        </div>
      </div>
    </header>

    <main class="mx-auto max-w-6xl px-4 py-8 transition-colors duration-300 md:px-6">
      <slot />
    </main>
  </div>
</template>

<script setup lang="ts">
import { computed, onMounted, useHead, useState } from '#imports'
import { HomeIcon, FolderIcon, MoonIcon, SunIcon } from '@heroicons/vue/24/outline'

const navItems = [
  { to: '/', label: 'Dashboard', icon: HomeIcon },
  { to: '/projects', label: 'Projects', icon: FolderIcon },
]

const THEME_STORAGE_KEY = 'tm-theme'

const theme = useState<'light' | 'dark'>('app-theme', () => 'light')
const isDark = computed(() => theme.value === 'dark')

useHead(() => ({
  htmlAttrs: {
    'data-theme': theme.value,
  },
}))

const applyTheme = (value: 'light' | 'dark') => {
  theme.value = value
  if (process.client) {
    const root = document.documentElement
    root.classList.toggle('dark', value === 'dark')
    root.setAttribute('data-theme', value)
    localStorage.setItem(THEME_STORAGE_KEY, value)
  }
}

const toggleTheme = () => {
  applyTheme(isDark.value ? 'light' : 'dark')
}

onMounted(() => {
  if (!process.client) {
    return
  }
  const stored = localStorage.getItem(THEME_STORAGE_KEY)
  if (stored === 'light' || stored === 'dark') {
    applyTheme(stored)
    return
  }
  const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches
  applyTheme(prefersDark ? 'dark' : 'light')
})
</script>
