<!-- purpose: Project detail placeholder displaying selected project identifier -->
<template>
  <section class="space-y-4 rounded-2xl border border-slate-800 bg-slate-900/60 p-6 shadow-lg">
    <nav class="text-sm text-slate-400">
      <NuxtLink to="/projects" class="text-sky-300 hover:text-sky-200">Projects</NuxtLink>
      <span class="mx-2">/</span>
      <span class="text-slate-300">Project #{{ projectId }}</span>
    </nav>

    <header class="space-y-2">
      <h1 class="text-3xl font-bold text-slate-50">Project Detail #{{ projectId }}</h1>
      <p class="text-sm text-slate-400">
        Populate this view with project metadata, team members, timelines, and status indicators once the API is wired up.
      </p>
    </header>

    <div class="rounded-xl border border-dashed border-slate-700 bg-slate-900/30 p-6 text-sm text-slate-400">
      Add tabs for overview, tasks, activity, and files. This placeholder ensures routing is functional while backend integration is pending.
    </div>
  </section>
</template>

<script setup lang="ts">
const route = useRoute()
const projectId = computed(() => route.params.id)
</script>
