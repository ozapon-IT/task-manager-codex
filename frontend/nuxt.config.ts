// purpose: Configure Nuxt modules, runtime settings, and global styling for the SPA
import { defineNuxtConfig } from 'nuxt/config'

export default defineNuxtConfig({
  srcDir: 'src/',
  modules: ['@pinia/nuxt', '@nuxtjs/tailwindcss'],
  devtools: { enabled: true },
  typescript: {
    strict: true,
    typeCheck: true
  },
  runtimeConfig: {
    public: {
      apiBase: process.env.NUXT_PUBLIC_API_BASE || 'http://localhost:8080/api',
      appName: process.env.NUXT_PUBLIC_APP_NAME || 'Task Manager'
    }
  },
  css: ['@/assets/styles/main.css'],
  app: {
    head: {
      titleTemplate: '%s · Task Manager',
      htmlAttrs: {
        'data-theme': 'dark'
      }
    }
  }
})
