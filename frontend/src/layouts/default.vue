<!-- purpose: Shared app shell with header, sidebar, and content slot styled for dark mode -->
<template>
  <div class="min-h-screen bg-slate-950 text-slate-100">
    <header class="header-glow border-b border-slate-800">
      <div class="mx-auto flex max-w-6xl items-center justify-between px-6 py-4">
        <h1 class="text-lg font-semibold tracking-wide">Task Manager</h1>
        <NuxtLink to="/ping" class="text-sm text-sky-300 transition hover:text-sky-200">
          API Ping
        </NuxtLink>
      </div>
    </header>

    <div class="mx-auto flex max-w-6xl gap-6 px-4 py-8 md:px-6">
      <aside class="hidden w-56 flex-shrink-0 rounded-xl border border-slate-800 bg-slate-900/70 p-4 shadow-lg md:block">
        <nav class="space-y-2 text-sm">
          <NuxtLink
            to="/"
            class="block rounded-md px-3 py-2 font-medium transition hover:bg-slate-800/80"
            active-class="bg-slate-800 text-white"
            exact
          >
            Dashboard
          </NuxtLink>
          <NuxtLink
            to="/projects"
            class="block rounded-md px-3 py-2 font-medium transition hover:bg-slate-800/80"
            active-class="bg-slate-800 text-white"
          >
            Projects
          </NuxtLink>
        </nav>
      </aside>

      <main class="flex-1">
        <slot />
      </main>
    </div>
  </div>
</template>

<style scoped>
.header-glow {
  box-shadow: inset 0 -1px 0 rgba(15, 23, 42, 0.35);
}
</style>
