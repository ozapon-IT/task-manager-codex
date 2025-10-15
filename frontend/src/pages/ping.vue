<!-- purpose: Ping page checks Laravel API connectivity by calling /api/ping and showing JSON -->
<template>
  <main class="min-h-screen bg-surface">
    <div class="mx-auto max-w-xl px-4 py-16">
      <section class="rounded-xl border border-neutral-700 bg-neutral-900/80 p-6 shadow-lg">
        <header class="mb-4 flex items-center justify-between">
          <div>
            <h1 class="text-2xl font-semibold">API Ping</h1>
            <p class="text-sm text-neutral-400">Endpoint: {{ endpoint }}</p>
          </div>
          <button
            type="button"
            class="rounded-md border border-indigo-500 px-3 py-1 text-sm font-medium text-indigo-300 transition hover:bg-indigo-500/10"
            @click="refresh"
          >
            再取得
          </button>
        </header>

        <div v-if="pending" class="text-neutral-400">Loading…</div>
        <div v-else-if="error" class="text-red-400">
          エラーが発生しました: {{ error?.message }}
        </div>
        <div v-else class="space-y-4">
          <h2 class="text-sm font-semibold uppercase tracking-wide text-neutral-400">Response</h2>
          <pre class="overflow-x-auto rounded bg-neutral-950/60 p-4 text-xs text-emerald-300">{{ prettyJson }}</pre>
        </div>
      </section>
    </div>
  </main>
</template>

<script setup lang="ts">
const runtimeConfig = useRuntimeConfig()
const endpoint = `${runtimeConfig.public.apiBaseUrl}/ping`

const data = ref<{ pong: boolean } | null>(null)
const pending = ref(true)
const error = ref<Error | null>(null)

const fetchPing = async () => {
  pending.value = true
  error.value = null
  try {
    data.value = await $fetch(endpoint)
  } catch (err) {
    error.value = err as Error
    data.value = null
  } finally {
    pending.value = false
  }
}

const refresh = () => fetchPing()

await fetchPing()

const prettyJson = computed(() => (data.value ? JSON.stringify(data.value, null, 2) : '{}'))
</script>

<style scoped>
.bg-surface {
  background: radial-gradient(circle at top, rgba(59, 130, 246, 0.15), transparent 55%), #0f172a;
  min-height: 100vh;
}
</style>
