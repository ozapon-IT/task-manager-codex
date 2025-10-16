<!-- purpose: Show project details with editable metadata and task board powered by Pinia stores -->
<template>
  <section class="space-y-6">
    <nav class="text-sm text-lightText dark:text-slate-400">
      <NuxtLink to="/projects" class="text-blue-600 hover:text-blue-700 dark:text-sky-300 dark:hover:text-sky-200">Projects</NuxtLink>
      <span class="mx-2 text-lightText/70 dark:text-slate-500">/</span>
      <span class="text-lightHeading dark:text-slate-200">Project #{{ projectId }}</span>
    </nav>

    <div class="space-y-6">
      <article class="space-y-4 rounded-2xl border border-lightBorder bg-lightCard p-6 shadow-md transition-colors dark:border-slate-800 dark:bg-slate-900/80 dark:shadow-xl">
        <header class="space-y-2">
          <h1 class="text-3xl font-semibold text-lightHeading dark:text-slate-50">
            {{ project?.title ?? 'Loading project…' }}
          </h1>
          <p class="text-sm text-lightText dark:text-slate-400" v-if="project?.description">
            {{ project.description }}
          </p>
        </header>

        <div class="flex flex-wrap gap-4 text-sm text-lightText dark:text-slate-300">
          <div class="rounded-md border border-lightBorder bg-lightCard px-3 py-2 shadow-sm dark:border-slate-700 dark:bg-slate-950/70 dark:shadow-none">
            <span class="text-xs uppercase tracking-wide text-lightText dark:text-slate-500">Status</span>
            <div class="capitalize text-lightHeading dark:text-slate-200">
              {{ project ? statusLabels[project.status] : '—' }}
            </div>
          </div>
          <div class="rounded-md border border-lightBorder bg-lightCard px-3 py-2 shadow-sm dark:border-slate-700 dark:bg-slate-950/70 dark:shadow-none">
            <span class="text-xs uppercase tracking-wide text-lightText dark:text-slate-500">Due date</span>
            <div class="text-lightHeading dark:text-slate-200">
              <span v-if="project?.due_date">{{ formatDate(project.due_date) }}</span>
              <span v-else class="text-lightText/70 dark:text-slate-500">Not set</span>
            </div>
          </div>
          <div class="rounded-md border border-lightBorder bg-lightCard px-3 py-2 shadow-sm dark:border-slate-700 dark:bg-slate-950/70 dark:shadow-none">
            <span class="text-xs uppercase tracking-wide text-lightText dark:text-slate-500">Tasks</span>
            <div class="text-lightHeading dark:text-slate-200">{{ tasks.length }}</div>
          </div>
        </div>

        <div class="rounded-xl border border-lightBorder bg-lightBg p-4 shadow-sm transition-colors dark:border-slate-800 dark:bg-slate-950/60 dark:shadow-none">
          <h2 class="text-lg font-semibold text-lightHeading dark:text-slate-100">Update project</h2>
          <form class="mt-4 grid gap-4" @submit.prevent="submitProjectUpdate">
            <label class="flex flex-col gap-2">
              <span class="text-sm text-lightText dark:text-slate-300">Title</span>
              <input
                v-model="editForm.title"
                type="text"
                required
                class="rounded-md border border-lightBorder bg-lightCard px-3 py-2 text-sm text-lightHeading transition focus:border-blue-500 focus:outline-none dark:border-slate-700 dark:bg-slate-950 dark:text-slate-100 dark:focus:border-sky-500"
              />
            </label>

            <label class="flex flex-col gap-2">
              <span class="text-sm text-lightText dark:text-slate-300">Description</span>
              <textarea
                v-model="editForm.description"
                rows="3"
                class="rounded-md border border-lightBorder bg-lightCard px-3 py-2 text-sm text-lightHeading transition focus:border-blue-500 focus:outline-none dark:border-slate-700 dark:bg-slate-950 dark:text-slate-100 dark:focus:border-sky-500"
              />
            </label>

            <div class="grid gap-4 sm:grid-cols-2">
              <label class="flex flex-col gap-2">
                <span class="text-sm text-lightText dark:text-slate-300">Due date</span>
                <input
                  v-model="editForm.due_date"
                  type="date"
                  class="rounded-md border border-lightBorder bg-lightCard px-3 py-2 text-sm text-lightHeading transition focus:border-blue-500 focus:outline-none dark:border-slate-700 dark:bg-slate-950 dark:text-slate-100 dark:focus:border-sky-500"
                />
              </label>

              <label class="flex flex-col gap-2">
                <span class="text-sm text-lightText dark:text-slate-300">Status</span>
                <select
                  v-model="editForm.status"
                  class="rounded-md border border-lightBorder bg-lightCard px-3 py-2 text-sm text-lightHeading transition focus:border-blue-500 focus:outline-none dark:border-slate-700 dark:bg-slate-950 dark:text-slate-100 dark:focus:border-sky-500"
                >
                  <option v-for="status in statusOptions" :key="status" :value="status">
                    {{ statusLabels[status] }}
                  </option>
                </select>
              </label>
            </div>

            <div class="flex items-center gap-3">
              <button
                type="submit"
                class="inline-flex items-center rounded-lg bg-blue-600 px-4 py-2 text-sm font-semibold text-white transition hover:bg-blue-500 dark:bg-sky-500 dark:hover:bg-sky-400/80"
                :disabled="projectLoading"
              >
                <span v-if="projectLoading" class="mr-2 animate-spin">⏳</span>
                Save changes
              </button>
              <button
                type="button"
                class="text-sm text-lightText transition hover:text-lightHeading dark:text-slate-400 dark:hover:text-slate-200"
                @click="resetProjectForm"
              >
                Reset
              </button>
            </div>
          </form>
        </div>
      </article>

      <article class="space-y-4 rounded-2xl border border-lightBorder bg-lightCard p-6 shadow-md transition-colors dark:border-slate-800 dark:bg-slate-900/80 dark:shadow-xl">
        <header class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
          <div>
            <h2 class="text-xl font-semibold text-lightHeading dark:text-slate-100">Tasks</h2>
            <p class="text-sm text-lightText dark:text-slate-400">Track execution and unblock the team quickly.</p>
          </div>
          <button
            type="button"
            class="inline-flex items-center justify-center rounded-lg border border-lightBorder px-3 py-2 text-sm font-semibold text-blue-600 transition hover:border-blue-400 hover:text-blue-700 dark:border-slate-700 dark:text-slate-200 dark:hover:text-sky-200"
            @click="openCreateModal"
          >
            <span class="mr-2 text-lg">+</span>
            Add task
          </button>
        </header>

        <div v-if="taskError" class="rounded-md border border-red-200 bg-red-50 p-3 text-sm text-red-600 dark:border-red-500 dark:bg-red-500/10 dark:text-red-300">
          {{ taskError }}
        </div>

        <div v-if="!taskLoading && tasks.length === 0" class="text-sm text-lightText dark:text-slate-400">
          No tasks yet. Add one to kick things off.
        </div>

        <div v-else class="overflow-x-auto">
          <table class="min-w-full divide-y divide-lightBorder bg-lightCard text-left text-sm dark:divide-slate-800 dark:bg-transparent">
            <thead class="bg-lightBg text-lightText dark:bg-slate-900/80 dark:text-slate-300">
              <tr>
                <th class="px-4 py-3 font-medium">Task</th>
                <th class="px-4 py-3 font-medium">Status</th>
                <th class="px-4 py-3 font-medium">Priority</th>
                <th class="px-4 py-3 font-medium">Due</th>
                <th class="px-4 py-3 text-right font-medium">Actions</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-lightBorder text-lightText dark:divide-slate-800 dark:text-slate-200">
              <tr v-for="task in tasks" :key="task.id" class="hover:bg-blue-50/60 dark:hover:bg-slate-900/60">
                <td class="px-4 py-3">
                  <div class="font-medium text-lightHeading dark:text-slate-100">{{ task.title }}</div>
                  <p v-if="task.description" class="text-xs text-lightText dark:text-slate-400">
                    {{ task.description }}
                  </p>
                </td>
                <td class="px-4 py-3 capitalize text-lightText dark:text-slate-200">
                  {{ taskStatusLabels[task.status] }}
                </td>
                <td class="px-4 py-3 capitalize text-lightText dark:text-slate-200">
                  {{ taskPriorityLabels[task.priority] }}
                </td>
                <td class="px-4 py-3">
                  <span v-if="task.due_date">{{ formatDate(task.due_date) }}</span>
                  <span v-else class="text-xs text-lightText/70 dark:text-slate-500">—</span>
                </td>
                <td class="px-4 py-3">
                  <div class="flex justify-end gap-2 text-xs">
                    <button
                      class="inline-flex h-8 w-8 items-center justify-center rounded-full border border-emerald-200 bg-lightCard text-xs font-semibold text-emerald-600 transition hover:border-emerald-300 hover:bg-emerald-50 dark:border-emerald-400/60 dark:bg-transparent dark:text-emerald-300 dark:hover:bg-emerald-400/10"
                      :disabled="taskLoading"
                      :aria-label="task.status === 'done' ? 'Mark task as todo' : 'Mark task as done'"
                      @click="quickComplete(task)"
                    >
                      ✔︎
                    </button>
                    <button
                      class="inline-flex h-8 w-8 items-center justify-center rounded-full border border-lightBorder bg-lightCard text-xs font-semibold text-lightText transition hover:border-blue-400 hover:text-blue-600 dark:border-slate-600 dark:bg-transparent dark:text-slate-300 dark:hover:bg-sky-500/10 dark:hover:text-sky-200"
                      :disabled="taskLoading"
                      :aria-label="`Edit ${task.title}`"
                      @click="openEditModal(task)"
                    >
                      <PencilSquareIcon class="h-4 w-4" />
                    </button>
                    <button
                      class="inline-flex h-8 w-8 items-center justify-center rounded-full border border-red-200 bg-lightCard text-xs font-semibold text-red-600 transition hover:border-red-300 hover:bg-red-50 dark:border-red-500/60 dark:bg-transparent dark:text-red-300 dark:hover:bg-red-500/10 dark:hover:text-red-200"
                      :disabled="taskLoading"
                      :aria-label="`Delete ${task.title}`"
                      @click="removeTask(task)"
                    >
                      <TrashIcon class="h-4 w-4" />
                    </button>
                  </div>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </article>
    </div>

    <TaskModal
      :open="taskModalOpen"
      :title="taskModalMode === 'create' ? 'Create task' : 'Edit task'"
      :subtitle="taskModalMode === 'create' ? 'Add a new task for this project.' : 'Update task details.'"
      :task="taskModalPayload"
      :priority-options="taskPriorityOptions"
      :status-options="taskStatusOptions"
      @close="closeTaskModal"
      @save="handleTaskModalSave"
    />
  </section>
</template>

<script setup lang="ts">
import { storeToRefs } from 'pinia'
import { computed, reactive, ref, useRoute, useRouter, onBeforeRouteUpdate, onMounted, watch, nextTick } from '#imports'
import {
  useProjectStore,
  type ProjectPayload,
  type ProjectStatus,
  type Task as ProjectTask,
} from '~~/stores/project'
import {
  useTaskStore,
  type TaskPayload,
  type TaskPriority,
  type TaskStatus,
} from '~~/stores/task'
import TaskModal from '~/components/TaskModal.vue'
import { PencilSquareIcon, TrashIcon } from '@heroicons/vue/24/outline'

const route = useRoute()
const router = useRouter()
const projectId = computed(() => Number(route.params.id))

const projectStore = useProjectStore()
const taskStore = useTaskStore()

const { selected: project, loading: projectLoading, error: projectError } = storeToRefs(projectStore)
const { items: tasks, loading: taskLoading, error: taskError } = storeToRefs(taskStore)

const statusOptions: ProjectStatus[] = ['draft', 'active', 'completed', 'archived']
const statusLabels: Record<ProjectStatus, string> = {
  draft: 'Draft',
  active: 'Active',
  completed: 'Completed',
  archived: 'Archived',
}

const taskStatusOptions: TaskStatus[] = ['todo', 'in_progress', 'blocked', 'done']
const taskStatusLabels: Record<TaskStatus, string> = {
  todo: 'To do',
  in_progress: 'In progress',
  blocked: 'Blocked',
  done: 'Done',
}

const taskPriorityOptions: TaskPriority[] = ['low', 'medium', 'high', 'critical']
const taskPriorityLabels: Record<TaskPriority, string> = {
  low: 'Low',
  medium: 'Medium',
  high: 'High',
  critical: 'Critical',
}

const editForm = reactive<ProjectPayload>({
  title: '',
  description: '',
  due_date: '',
  status: 'draft',
})

const taskModalOpen = ref(false)
const taskModalMode = ref<'create' | 'edit'>('create')
const taskModalTask = ref<ProjectTask | null>(null)
const taskModalPayload = computed<TaskPayload | null>(() => {
  if (!taskModalTask.value) {
    return null
  }
  return {
    title: taskModalTask.value.title,
    description: taskModalTask.value.description ?? '',
    due_date: toDateInputValue(taskModalTask.value.due_date),
    status: taskModalTask.value.status,
    priority: taskModalTask.value.priority,
  }
})

const toDateInputValue = (value: string | null | undefined): string => {
  if (!value) {
    return ''
  }
  return value.length >= 10 ? value.slice(0, 10) : value
}

const hydrateProjectForm = () => {
  if (!project.value) {
    return
  }

  editForm.title = project.value.title
  editForm.description = project.value.description ?? ''
  editForm.due_date = toDateInputValue(project.value.due_date)
  editForm.status = project.value.status
}

const resetProjectForm = () => {
  hydrateProjectForm()
}

const submitProjectUpdate = async () => {
  if (!project.value) {
    return
  }

  await projectStore.update(project.value.id, {
    title: editForm.title.trim(),
    description: editForm.description?.trim() || null,
    due_date: editForm.due_date || null,
    status: editForm.status,
  })

  await projectStore.fetchOne(project.value.id)
  hydrateProjectForm()
}

const removeTask = async (task: { id: number }) => {
  if (!confirm('Delete this task?')) {
    return
  }
  await taskStore.delete(task.id, projectId.value)
  if (taskModalTask.value?.id === task.id) {
    closeTaskModal()
  }
  await refreshTasks()
}

const refreshTasks = async () => {
  await taskStore.fetchAll(projectId.value)
  await projectStore.fetchOne(projectId.value)
}

const quickComplete = async (task: ProjectTask) => {
  const nextStatus = task.status === 'done' ? 'todo' : 'done'
  await taskStore.update(
    task.id,
    {
      status: nextStatus,
    },
    projectId.value
  )
  await refreshTasks()
}

const openCreateModal = () => {
  taskModalMode.value = 'create'
  taskModalTask.value = null
  taskModalOpen.value = true
}

const openEditModal = (task: ProjectTask) => {
  taskModalMode.value = 'edit'
  taskModalTask.value = { ...task }
  taskModalOpen.value = true
}

const closeTaskModal = () => {
  taskModalOpen.value = false
  taskModalTask.value = null
  taskModalMode.value = 'create'
}

const normalizePayload = (payload: TaskPayload) => ({
  title: payload.title.trim(),
  description: payload.description?.trim() || null,
  due_date: payload.due_date ? payload.due_date : null,
  status: payload.status,
  priority: payload.priority,
})

const handleTaskModalSave = async (payload: TaskPayload) => {
  const body = normalizePayload(payload)
  if (taskModalMode.value === 'create') {
    await taskStore.create(body, projectId.value)
  } else if (taskModalTask.value) {
    await taskStore.update(taskModalTask.value.id, body, projectId.value)
  }
  await refreshTasks()
  closeTaskModal()
}

const formatDate = (value: string): string => {
  try {
    return new Intl.DateTimeFormat('en', { dateStyle: 'medium' }).format(new Date(value))
  } catch {
    return value
  }
}

const loadProject = async (id: number) => {
  if (Number.isNaN(id)) {
    router.replace('/projects')
    return
  }

  await projectStore.fetchOne(id)
  await taskStore.fetchAll(id)
  hydrateProjectForm()
  await focusFromQuery()
}

const focusFromQuery = async () => {
  const focusId = route.query.focus ? Number(route.query.focus) : null
  if (!focusId) {
    return
  }

  const target = tasks.value.find((task) => task.id === focusId)
  if (!target) {
    return
  }

  openEditModal(target)
  await nextTick()
  router.replace({ path: route.path })
}

watch(
  () => project.value?.id,
  () => {
    hydrateProjectForm()
  }
)

watch(
  () => route.query.focus,
  async () => {
    await focusFromQuery()
  }
)

onMounted(async () => {
  await loadProject(projectId.value)
})

onBeforeRouteUpdate(async (to) => {
  const nextId = Number(to.params.id)
  await loadProject(nextId)
})

watch(projectError, (val) => {
  if (val) {
    console.warn('[project-page] error', val)
  }
})

watch(taskError, (val) => {
  if (val) {
    console.warn('[project-page] task error', val)
  }
})
</script>
