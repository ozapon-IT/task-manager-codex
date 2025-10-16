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
    typeCheck: false,
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
      script: [
        {
          tagPosition: 'head',
          type: 'text/javascript',
          innerHTML: `
            (function(){
              try {
                var storageKey = 'tm-theme';
                var stored = localStorage.getItem(storageKey);
                var systemPref = window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light';
                var theme = stored === null ? systemPref : stored;
                document.documentElement.classList.toggle('dark', theme === 'dark');
                document.documentElement.setAttribute('data-theme', theme);
              } catch(e) {}
            })();
          `,
        },
      ],
      __dangerouslyDisableSanitizersByTagID: {
        default: ['innerHTML'],
      },
    }
  }
})
