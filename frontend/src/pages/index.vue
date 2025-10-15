<!-- purpose: Dashboard landing page welcoming users and surfacing quick actions -->
<template>
  <section class="space-y-6 rounded-2xl border border-slate-800 bg-slate-900/60 p-8 shadow-lg">
    <header class="space-y-2">
      <p class="text-sm uppercase tracking-widest text-slate-400">Dashboard</p>
      <h2 class="text-3xl font-bold text-slate-50">Welcome to {{ appName }}</h2>
      <p class="text-sm text-slate-400">
        Track projects, prioritise tasks, and keep upcoming deadlines in view. Start by exploring your
        project backlog or creating a new workspace segment.
      </p>
    </header>

    <div class="grid gap-4 md:grid-cols-2">
      <NuxtLink
        to="/projects"
        class="group flex items-center justify-between rounded-xl border border-slate-800 bg-slate-950/70 px-5 py-4 transition hover:border-sky-500/50 hover:bg-slate-900"
      >
        <div>
          <h3 class="text-lg font-semibold text-slate-100 group-hover:text-sky-200">View Projects</h3>
          <p class="text-sm text-slate-400">Browse all active workstreams and their task load.</p>
        </div>
        <span class="text-sky-400">→</span>
      </NuxtLink>

      <NuxtLink
        to="/projects/new"
        class="flex items-center justify-between rounded-xl border border-dashed border-slate-700 bg-slate-950/40 px-5 py-4 text-slate-300 transition hover:border-sky-500/60 hover:text-sky-200"
      >
        <div>
          <h3 class="text-lg font-semibold">Create Project</h3>
          <p class="text-sm">Set milestones, assign owners, and sync due dates.</p>
        </div>
        <span class="text-slate-500">+</span>
      </NuxtLink>
    </div>
  </section>
</template>

<script setup lang="ts">
const config = useRuntimeConfig()
const appName = computed(() => config.public.appName ?? 'Task Manager')
</script>
