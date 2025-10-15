<template>
  <main class="landing">
    <section class="hero">
      <h1>{{ appName }}</h1>
      <p>Kick-start the Task Manager SPA by scaffolding layout, stores, and components.</p>
    </section>
    <section class="next-steps">
      <h2>Next Steps</h2>
      <ol>
        <li>Implement authentication screens and project dashboard.</li>
        <li>Wire Pinia stores to the Laravel API.</li>
        <li>Add Vitest component suites and Cypress end-to-end coverage.</li>
      </ol>
    </section>
  </main>
</template>

<script setup lang="ts">
const config = useRuntimeConfig()
const appName = computed(() => config.public.appName)
</script>

<style scoped>
.landing {
  display: grid;
  gap: 2rem;
  padding: 4rem 2rem;
  max-width: 960px;
  margin: 0 auto;
}

.hero h1 {
  font-size: clamp(2.5rem, 4vw, 3.5rem);
  margin: 0;
}

.next-steps ol {
  margin: 0;
  padding-left: 1.25rem;
}

.next-steps li + li {
  margin-top: 0.5rem;
}
</style>
