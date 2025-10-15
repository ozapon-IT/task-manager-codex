<!-- purpose: Render project list with creation form and CRUD actions wired to Pinia store -->
<template>
  <section class="space-y-8">
    <header
      class="flex flex-col gap-4 rounded-2xl border border-slate-800 bg-slate-900/70 p-6 shadow-lg sm:flex-row sm:items-center sm:justify-between"
    >
      <div>
        <p class="text-xs uppercase tracking-widest text-slate-400">Projects</p>
        <h1 class="text-3xl font-semibold text-slate-50">Project Portfolio</h1>
        <p class="text-sm text-slate-400">
          Manage client workstreams, track delivery deadlines, and dive into task progress.
        </p>
      </div>
      <button
        type="button"
        class="inline-flex items-center justify-center rounded-lg bg-sky-500 px-4 py-2 text-sm font-semibold text-white shadow transition hover:bg-sky-400"
        @click="toggleCreateForm"
      >
        <span class="mr-2 text-lg">+</span>
        New Project
      </button>
    </header>

    <div
      v-if="showCreate"
      class="rounded-2xl border border-slate-800 bg-slate-900/80 p-6 shadow-inner"
    >
      <h2 class="text-lg font-semibold text-slate-100">Create Project</h2>
      <p class="text-sm text-slate-400">Fill in the basics and start planning immediately.</p>
      <form class="mt-4 grid gap-4 md:grid-cols-2" @submit.prevent="submitCreate">
        <label class="flex flex-col gap-2 md:col-span-1">
          <span class="text-sm text-slate-300">Title</span>
          <input
            v-model="form.title"
            type="text"
            required
            class="rounded-md border border-slate-700 bg-slate-950 px-3 py-2 text-sm text-slate-100 focus:border-sky-500 focus:outline-none"
          />
        </label>

        <label class="flex flex-col gap-2 md:col-span-1">
          <span class="text-sm text-slate-300">Due date</span>
          <input
            v-model="form.due_date"
            type="date"
            class="rounded-md border border-slate-700 bg-slate-950 px-3 py-2 text-sm text-slate-100 focus:border-sky-500 focus:outline-none"
          />
        </label>

        <label class="flex flex-col gap-2 md:col-span-2">
          <span class="text-sm text-slate-300">Description</span>
          <textarea
            v-model="form.description"
            rows="3"
            class="rounded-md border border-slate-700 bg-slate-950 px-3 py-2 text-sm text-slate-100 focus:border-sky-500 focus:outline-none"
          />
        </label>

        <label class="flex flex-col gap-2 md:col-span-1">
          <span class="text-sm text-slate-300">Status</span>
          <select
            v-model="form.status"
            class="rounded-md border border-slate-700 bg-slate-950 px-3 py-2 text-sm text-slate-100 focus:border-sky-500 focus:outline-none"
          >
            <option v-for="status in statusOptions" :key="status" :value="status">
              {{ statusLabels[status] }}
            </option>
          </select>
        </label>

        <div class="flex items-center gap-3 md:col-span-2">
          <button
            type="submit"
            class="inline-flex items-center rounded-lg bg-sky-500 px-4 py-2 text-sm font-semibold text-white transition hover:bg-sky-400"
            :disabled="loading"
          >
            <span v-if="loading" class="mr-2 animate-spin">⏳</span>
            Create
          </button>
          <button
            type="button"
            class="text-sm text-slate-400 hover:text-slate-200"
            @click="closeCreateForm"
          >
            Cancel
          </button>
        </div>
      </form>
    </div>

    <div class="rounded-2xl border border-slate-800 bg-slate-900/70 p-6 shadow">
      <div class="mb-4 flex items-center justify-between">
        <h2 class="text-lg font-semibold text-slate-100">All Projects</h2>
        <div class="text-sm text-slate-400">
          <span v-if="loading">Loading projects…</span>
          <span v-else>{{ projects.length }} projects</span>
        </div>
      </div>

      <div v-if="error" class="mb-4 rounded-md border border-red-500 bg-red-500/10 p-3 text-sm text-red-300">
        {{ error }}
      </div>

      <div v-if="!loading && projects.length === 0" class="text-sm text-slate-400">
        No projects yet. Create one to get started!
      </div>

      <div v-else class="overflow-x-auto">
        <table class="min-w-full divide-y divide-slate-800 text-left text-sm">
          <thead class="bg-slate-900/80 text-slate-300">
            <tr>
              <th class="px-4 py-3 font-medium">Title</th>
              <th class="px-4 py-3 font-medium">Status</th>
              <th class="px-4 py-3 font-medium">Due</th>
              <th class="px-4 py-3 font-medium">Tasks</th>
              <th class="px-4 py-3 text-right font-medium">Actions</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-slate-800 text-slate-200">
            <tr v-for="project in projects" :key="project.id" class="hover:bg-slate-900/60">
              <td class="px-4 py-3">
                <NuxtLink
                  :to="`/projects/${project.id}`"
                  class="font-semibold text-sky-300 hover:text-sky-200"
                >
                  {{ project.title }}
                </NuxtLink>
                <p v-if="project.description" class="text-xs text-slate-400">
                  {{ project.description }}
                </p>
              </td>
              <td class="px-4 py-3 capitalize">{{ statusLabels[project.status] }}</td>
              <td class="px-4 py-3">
                <span v-if="project.due_date">{{ formatDate(project.due_date) }}</span>
                <span v-else class="text-xs text-slate-500">—</span>
              </td>
              <td class="px-4 py-3">
                {{ project.tasks?.length ?? '—' }}
              </td>
              <td class="px-4 py-3 text-right">
                <button
                  class="rounded-md border border-slate-700 px-3 py-1 text-xs font-medium text-slate-300 transition hover:border-red-500 hover:text-red-400"
                  :disabled="loading"
                  @click="removeProject(project.id)"
                >
                  Delete
                </button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </section>
</template>

<script setup lang="ts">
import { storeToRefs } from 'pinia'
import { useProjectStore, type ProjectPayload, type ProjectStatus } from '~~/stores/project'

const projectStore = useProjectStore()
const { items: projects, loading, error } = storeToRefs(projectStore)

const statusOptions: ProjectStatus[] = ['draft', 'active', 'completed', 'archived']
const statusLabels: Record<ProjectStatus, string> = {
  draft: 'Draft',
  active: 'Active',
  completed: 'Completed',
  archived: 'Archived',
}

const showCreate = ref(false)
const form = reactive<ProjectPayload>({
  title: '',
  description: '',
  due_date: '',
  status: 'draft',
})

const resetForm = () => {
  form.title = ''
  form.description = ''
  form.due_date = ''
  form.status = 'draft'
}

const toggleCreateForm = () => {
  showCreate.value = !showCreate.value
}

const closeCreateForm = () => {
  showCreate.value = false
  resetForm()
}

const submitCreate = async () => {
  await projectStore.create({
    title: form.title.trim(),
    description: form.description?.trim() || null,
    due_date: form.due_date || null,
    status: form.status,
  })
  closeCreateForm()
}

const removeProject = async (projectId: number) => {
  if (!confirm('Are you sure you want to delete this project?')) {
    return
  }
  await projectStore.delete(projectId)
}

const formatDate = (value: string): string => {
  try {
    return new Intl.DateTimeFormat('en', { dateStyle: 'medium' }).format(new Date(value))
  } catch {
    return value
  }
}

onMounted(async () => {
  await projectStore.fetchAll()
})
</script>
