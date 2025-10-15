<!-- purpose: List view shell for projects with primary action placeholder -->
<template>
  <section class="space-y-6">
    <header class="flex flex-col gap-4 rounded-2xl border border-slate-800 bg-slate-900/60 p-6 shadow-md sm:flex-row sm:items-center sm:justify-between">
      <div>
        <p class="text-sm uppercase tracking-widest text-slate-500">Projects</p>
        <h2 class="text-2xl font-semibold text-slate-50">Project Portfolio</h2>
        <p class="text-sm text-slate-400">Organise client work, deadlines, and progress metrics.</p>
      </div>
      <button
        type="button"
        class="inline-flex items-center justify-center rounded-lg bg-sky-500 px-4 py-2 text-sm font-semibold text-white shadow transition hover:bg-sky-400"
      >
        + New Project
      </button>
    </header>

    <div class="grid gap-4 md:grid-cols-2">
      <article class="rounded-xl border border-slate-800 bg-slate-900/40 p-5 shadow">
        <h3 class="text-lg font-semibold text-slate-100">Sample Project</h3>
        <p class="mt-1 text-sm text-slate-400">
          Replace this placeholder with a project card listing once the API is connected.
        </p>
        <NuxtLink to="/projects/1" class="mt-4 inline-flex text-sm text-sky-300 hover:text-sky-200">
          View details →
        </NuxtLink>
      </article>

      <article class="rounded-xl border border-dashed border-slate-700 bg-slate-900/20 p-5">
        <h3 class="text-lg font-semibold text-slate-200">Coming Soon</h3>
        <p class="mt-1 text-sm text-slate-500">
          Integrate filters, statuses, and team ownership in upcoming iterations.
        </p>
      </article>
    </div>
  </section>
</template>
