// purpose: Configure Nuxt modules, runtime settings, and global styling for the SPA
// purpose: Configure Nuxt modules, runtime settings, and global styling for the SPA
import { defineNuxtConfig } from 'nuxt/config'

export default defineNuxtConfig({
  srcDir: 'src/',
  modules: ['@pinia/nuxt', '@nuxtjs/tailwindcss'],
  imports: {
    // Pinia stores はプロジェクトルートの /stores を読み込む
    dirs: ['stores']
  },
  devtools: { enabled: true },
  typescript: {
    strict: true,
    typeCheck: true
  },
  runtimeConfig: {
    public: {
      apiBaseUrl: process.env.NUXT_PUBLIC_API_BASE || 'http://localhost:8080/api',
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
