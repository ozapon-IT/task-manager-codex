<!-- purpose: Modern dashboard with key stats and high-priority task list -->
<template>
  <section class="space-y-10">
    <header class="space-y-2">
      <p class="text-xs uppercase tracking-widest text-lightText dark:text-slate-400">Dashboard</p>
      <h1 class="text-3xl font-semibold text-lightHeading dark:text-slate-50">Welcome back, {{ appName }}</h1>
      <p class="text-sm text-lightText dark:text-slate-400">
        Keep an eye on high-impact tasks and ensure upcoming deadlines stay on track.
      </p>
    </header>

    <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-3">
      <article
        v-for="card in statCards"
        :key="card.label"
        class="rounded-2xl border border-lightBorder bg-lightCard p-5 shadow-md transition hover:border-blue-300/50 dark:border-slate-800 dark:bg-slate-900/70 dark:shadow-lg"
      >
        <div class="flex items-center justify-between">
          <div>
            <p class="text-xs uppercase tracking-wide text-lightText dark:text-slate-400">{{ card.label }}</p>
            <h2 class="mt-2 text-3xl font-semibold text-lightHeading dark:text-slate-50">{{ card.value }}</h2>
          </div>
          <span class="rounded-full bg-blue-50 p-3 text-blue-600 dark:bg-sky-500/20 dark:text-sky-300">
            <component :is="card.icon" class="h-6 w-6" />
          </span>
        </div>
        <p class="mt-3 text-xs text-lightText dark:text-slate-400">{{ card.caption }}</p>
      </article>
    </div>

    <section class="rounded-2xl border border-lightBorder bg-lightCard p-6 shadow-md transition-colors dark:border-slate-800 dark:bg-slate-900/70 dark:shadow-xl">
      <div class="mb-4 flex flex-wrap items-center justify-between gap-3">
        <div>
          <h2 class="text-lg font-semibold text-lightHeading dark:text-slate-100">Today&apos;s focus</h2>
          <p class="text-sm text-lightText dark:text-slate-400">High-priority work sorted by urgency.</p>
        </div>
        <NuxtLink
          to="/projects"
          class="inline-flex items-center gap-2 rounded-md border border-lightBorder px-3 py-2 text-xs font-semibold text-lightText transition hover:border-blue-400 hover:text-blue-600 dark:border-slate-700 dark:text-slate-300 dark:hover:text-sky-200"
        >
          <FolderIcon class="h-4 w-4 text-blue-500 dark:text-sky-300" />
          Go to projects
        </NuxtLink>
      </div>

      <div v-if="projectLoading" class="rounded-md border border-lightBorder bg-lightBg p-4 text-sm text-lightText dark:border-slate-800 dark:bg-slate-950/70 dark:text-slate-400">
        Loading tasks…
      </div>

      <div v-else-if="rows.length === 0" class="rounded-md border border-dashed border-lightBorder bg-lightBg p-4 text-sm text-lightText dark:border-slate-700 dark:bg-slate-950/40 dark:text-slate-400">
        Nothing urgent right now. Enjoy the calm or queue up the next milestone.
      </div>

      <div v-else class="overflow-x-auto">
        <table class="min-w-full divide-y divide-lightBorder bg-lightCard text-left text-sm dark:divide-slate-800 dark:bg-transparent">
          <thead class="bg-lightBg text-lightText dark:bg-slate-900/80 dark:text-slate-300">
            <tr>
              <th class="px-4 py-3 font-medium">Task</th>
              <th class="px-4 py-3 font-medium">Project</th>
              <th class="px-4 py-3 font-medium">Priority</th>
              <th class="px-4 py-3 font-medium">Due date</th>
              <th class="px-4 py-3 font-medium">Status</th>
              <th class="px-4 py-3 text-right font-medium">Action</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-lightBorder text-lightText dark:divide-slate-800 dark:text-slate-200">
            <tr v-for="row in rows" :key="row.task.id" class="hover:bg-blue-50/60 dark:hover:bg-slate-900/60">
              <td class="px-4 py-3">
                <div class="font-medium text-lightHeading dark:text-slate-100">{{ row.task.title }}</div>
                <p v-if="row.task.description" class="text-xs text-lightText dark:text-slate-400">
                  {{ row.task.description }}
                </p>
              </td>
              <td class="px-4 py-3 text-sm text-blue-600 dark:text-sky-200">
                <NuxtLink :to="`/projects/${row.project.id}`" class="hover:text-blue-700 dark:hover:text-sky-100">
                  {{ row.project.title }}
                </NuxtLink>
              </td>
              <td class="px-4 py-3">
                <span
                  :class="[
                    'inline-flex items-center gap-2 rounded-full px-3 py-1 text-xs font-semibold capitalize',
                    priorityPills[row.task.priority],
                  ]"
                >
                  <component :is="priorityIcons[row.task.priority]" class="h-3.5 w-3.5" />
                  {{ row.task.priority }}
                </span>
              </td>
              <td class="px-4 py-3 text-sm">
                <span v-if="row.task.due_date">{{ formatDate(row.task.due_date) }}</span>
                <span v-else class="text-xs text-lightText/70 dark:text-slate-500">—</span>
              </td>
              <td class="px-4 py-3 text-sm capitalize text-lightText dark:text-slate-200">
                {{ statusLabels[row.task.status] }}
              </td>
              <td class="px-4 py-3">
                <div class="flex justify-end gap-2 text-xs">
                  <button
                    class="inline-flex h-8 w-8 items-center justify-center rounded-full border border-emerald-200 bg-lightCard text-xs font-semibold text-emerald-600 transition hover:border-emerald-300 hover:bg-emerald-50 dark:border-emerald-400/60 dark:bg-transparent dark:text-emerald-300 dark:hover:bg-emerald-400/10"
                    :disabled="taskLoading"
                    :aria-label="row.task.status === 'done' ? 'Mark task as todo' : 'Mark task as done'"
                    @click="toggleTaskStatus(row.project.id, row.task)"
                  >
                    ✔︎
                  </button>
                  <button
                    class="inline-flex h-8 w-8 items-center justify-center rounded-full border border-lightBorder bg-lightCard text-xs font-semibold text-lightText transition hover:border-blue-400 hover:text-blue-600 dark:border-slate-600 dark:bg-transparent dark:text-slate-300 dark:hover:bg-sky-500/10 dark:hover:text-sky-200"
                    :aria-label="`Edit ${row.task.title}`"
                    @click="openEditModal(row.project, row.task)"
                  >
                    <PencilSquareIcon class="h-4 w-4" />
                  </button>
                  <button
                    class="inline-flex h-8 w-8 items-center justify-center rounded-full border border-red-200 bg-lightCard text-xs font-semibold text-red-600 transition hover:border-red-300 hover:bg-red-50 dark:border-red-500/60 dark:bg-transparent dark:text-red-300 dark:hover:bg-red-500/10 dark:hover:text-red-200"
                    :disabled="taskLoading"
                    :aria-label="`Delete ${row.task.title}`"
                    @click="deleteTask(row.project.id, row.task.id)"
                  >
                    <TrashIcon class="h-4 w-4" />
                  </button>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </section>
  </section>

  <TaskModal
    :open="taskModalOpen"
    title="Edit task"
    subtitle="Update task details."
    :task="taskModalPayload"
    :priority-options="priorityOptions"
    :status-options="statusOptions"
    @close="closeTaskModal"
    @save="handleTaskModalSave"
  />
</template>

<script setup lang="ts">
import { storeToRefs } from 'pinia'
import { computed, onMounted, ref } from '#imports'
import {
  useProjectStore,
  type Project,
  type Task as ProjectTask,
} from '~~/stores/project'
import { useTaskStore, type TaskPayload } from '~~/stores/task'
import TaskModal from '~/components/TaskModal.vue'
import {
  ClipboardDocumentCheckIcon,
  CalendarIcon,
  RocketLaunchIcon,
  ExclamationTriangleIcon,
  FlagIcon,
  PencilSquareIcon,
  TrashIcon,
  FolderIcon,
} from '@heroicons/vue/24/outline'

const config = useRuntimeConfig()
const appName = computed(() => config.public.appName ?? 'Task Manager')

const projectStore = useProjectStore()
const taskStore = useTaskStore()
const { items: projects, loading: projectLoading } = storeToRefs(projectStore)
const { loading: taskLoading } = storeToRefs(taskStore)

const taskModalOpen = ref(false)
const taskModalProjectId = ref<number | null>(null)
const taskModalTask = ref<ProjectTask | null>(null)

const priorityOrder: Record<ProjectTask['priority'], number> = {
  critical: 0,
  high: 1,
  medium: 2,
  low: 3,
}

const statusLabels: Record<ProjectTask['status'], string> = {
  todo: 'To do',
  in_progress: 'In progress',
  blocked: 'Blocked',
  done: 'Done',
}

const statusOptions: ProjectTask['status'][] = ['todo', 'in_progress', 'blocked', 'done']
const priorityOptions: ProjectTask['priority'][] = ['low', 'medium', 'high', 'critical']

const priorityIcons: Record<ProjectTask['priority'], any> = {
  critical: ExclamationTriangleIcon,
  high: FlagIcon,
  medium: ClipboardDocumentCheckIcon,
  low: CalendarIcon,
}

const priorityPills: Record<ProjectTask['priority'], string> = {
  critical:
    'border border-red-200 bg-red-50 text-red-600 dark:border-red-500/40 dark:bg-red-500/10 dark:text-red-300',
  high:
    'border border-amber-200 bg-amber-50 text-amber-600 dark:border-amber-500/40 dark:bg-amber-500/10 dark:text-amber-300',
  medium:
    'border border-blue-200 bg-blue-50 text-blue-600 dark:border-sky-500/40 dark:bg-sky-500/10 dark:text-sky-200',
  low:
    'border border-lightBorder bg-lightBg text-lightText dark:border-slate-500/30 dark:bg-slate-500/10 dark:text-slate-200',
}

const allTasks = computed(() =>
  projects.value.flatMap((project) =>
    (project.tasks ?? []).map((task) => ({ project, task }))
  )
)

const isTaskCompleted = (task: ProjectTask) => task.status === 'done'

const activeTasks = computed(() => allTasks.value.filter(({ task }) => !isTaskCompleted(task)))

const today = computed(() => new Date().toISOString().slice(0, 10))

const withinDays = (date: string | null | undefined, days: number) => {
  if (!date) return false
  const target = new Date(date)
  const now = new Date()
  const diff = (target.getTime() - now.setHours(0, 0, 0, 0)) / (1000 * 60 * 60 * 24)
  return diff >= 0 && diff <= days
}

const ongoingProjects = computed(() => projects.value.filter((p) => p.status !== 'completed'))

const todaysTasks = computed(() =>
  activeTasks.value.filter(({ task }) => task.due_date === today.value)
)

const nextThreeDays = computed(() =>
  activeTasks.value.filter(({ task }) => withinDays(task.due_date, 3))
)

const statCards = computed(() => [
  {
    label: 'Ongoing projects',
    value: ongoingProjects.value.length,
    caption: 'Projects actively moving toward completion.',
    icon: RocketLaunchIcon,
  },
  {
    label: "Today's tasks",
    value: todaysTasks.value.length,
    caption: 'Due before midnight today.',
    icon: ClipboardDocumentCheckIcon,
  },
  {
    label: 'Due within 3 days',
    value: nextThreeDays.value.length,
    caption: 'Upcoming deadlines to keep in focus.',
    icon: CalendarIcon,
  },
])

const rows = computed(() =>
  [...activeTasks.value].sort((a, b) => {
    const priorityDiff = priorityOrder[a.task.priority] - priorityOrder[b.task.priority]
    if (priorityDiff !== 0) {
      return priorityDiff
    }

    const aDue = a.task.due_date ?? ''
    const bDue = b.task.due_date ?? ''
    return aDue.localeCompare(bDue)
  })
)

const toggleTaskStatus = async (projectId: number, task: ProjectTask) => {
  const nextStatus = task.status === 'done' ? 'todo' : 'done'
  await taskStore.update(task.id, { status: nextStatus }, projectId)
  await projectStore.fetchAll()
}

const normalizePayload = (payload: TaskPayload) => ({
  title: payload.title.trim(),
  description: payload.description?.trim() || null,
  due_date: payload.due_date ? payload.due_date : null,
  status: payload.status,
  priority: payload.priority,
})

const openEditModal = (project: Project, task: ProjectTask) => {
  taskModalProjectId.value = project.id
  taskModalTask.value = { ...task }
  taskModalOpen.value = true
}

const closeTaskModal = () => {
  taskModalOpen.value = false
  taskModalProjectId.value = null
  taskModalTask.value = null
}

const taskModalPayload = computed<TaskPayload | null>(() => {
  if (!taskModalTask.value) return null
  return {
    title: taskModalTask.value.title,
    description: taskModalTask.value.description ?? '',
    due_date: taskModalTask.value.due_date ?? '',
    status: taskModalTask.value.status,
    priority: taskModalTask.value.priority,
  }
})

const handleTaskModalSave = async (payload: TaskPayload) => {
  if (taskModalProjectId.value === null || !taskModalTask.value) {
    closeTaskModal()
    return
  }
  await taskStore.update(taskModalTask.value.id, normalizePayload(payload), taskModalProjectId.value)
  await projectStore.fetchAll()
  closeTaskModal()
}

const deleteTask = async (projectId: number, taskId: number) => {
  if (!confirm('Delete this task?')) {
    return
  }
  await taskStore.delete(taskId, projectId)
  await projectStore.fetchAll()
  if (taskModalTask.value?.id === taskId) {
    closeTaskModal()
  }
}

const formatDate = (value: string): string => {
  try {
    return new Intl.DateTimeFormat('en', { dateStyle: 'medium' }).format(new Date(value))
  } catch {
    return value
  }
}

onMounted(async () => {
  if (!projects.value.length) {
    await projectStore.fetchAll()
  }
})
</script>
