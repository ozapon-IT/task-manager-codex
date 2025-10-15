<!-- purpose: Show project details with editable metadata and task board powered by Pinia stores -->
<template>
  <section class="space-y-6">
    <nav class="text-sm text-slate-400">
      <NuxtLink to="/projects" class="text-sky-300 hover:text-sky-200">Projects</NuxtLink>
      <span class="mx-2">/</span>
      <span class="text-slate-200">Project #{{ projectId }}</span>
    </nav>

    <div class="grid gap-6 lg:grid-cols-[2fr_3fr]">
      <article class="space-y-4 rounded-2xl border border-slate-800 bg-slate-900/80 p-6 shadow-lg">
        <header class="space-y-2">
          <h1 class="text-3xl font-semibold text-slate-50">
            {{ project?.title ?? 'Loading project…' }}
          </h1>
          <p class="text-sm text-slate-400" v-if="project?.description">
            {{ project.description }}
          </p>
        </header>

        <div class="flex flex-wrap gap-4 text-sm text-slate-300">
          <div class="rounded-md border border-slate-700 bg-slate-950/70 px-3 py-2">
            <span class="text-xs uppercase tracking-wide text-slate-500">Status</span>
            <div class="capitalize text-slate-200">
              {{ project ? statusLabels[project.status] : '—' }}
            </div>
          </div>
          <div class="rounded-md border border-slate-700 bg-slate-950/70 px-3 py-2">
            <span class="text-xs uppercase tracking-wide text-slate-500">Due date</span>
            <div>
              <span v-if="project?.due_date">{{ formatDate(project.due_date) }}</span>
              <span v-else class="text-slate-500">Not set</span>
            </div>
          </div>
          <div class="rounded-md border border-slate-700 bg-slate-950/70 px-3 py-2">
            <span class="text-xs uppercase tracking-wide text-slate-500">Tasks</span>
            <div>{{ tasks.length }}</div>
          </div>
        </div>

        <div class="rounded-xl border border-slate-800 bg-slate-950/60 p-4">
          <h2 class="text-lg font-semibold text-slate-100">Update project</h2>
          <form class="mt-4 grid gap-4" @submit.prevent="submitProjectUpdate">
            <label class="flex flex-col gap-2">
              <span class="text-sm text-slate-300">Title</span>
              <input
                v-model="editForm.title"
                type="text"
                required
                class="rounded-md border border-slate-700 bg-slate-950 px-3 py-2 text-sm text-slate-100 focus:border-sky-500 focus:outline-none"
              />
            </label>

            <label class="flex flex-col gap-2">
              <span class="text-sm text-slate-300">Description</span>
              <textarea
                v-model="editForm.description"
                rows="3"
                class="rounded-md border border-slate-700 bg-slate-950 px-3 py-2 text-sm text-slate-100 focus:border-sky-500 focus:outline-none"
              />
            </label>

            <div class="grid gap-4 sm:grid-cols-2">
              <label class="flex flex-col gap-2">
                <span class="text-sm text-slate-300">Due date</span>
                <input
                  v-model="editForm.due_date"
                  type="date"
                  class="rounded-md border border-slate-700 bg-slate-950 px-3 py-2 text-sm text-slate-100 focus:border-sky-500 focus:outline-none"
                />
              </label>

              <label class="flex flex-col gap-2">
                <span class="text-sm text-slate-300">Status</span>
                <select
                  v-model="editForm.status"
                  class="rounded-md border border-slate-700 bg-slate-950 px-3 py-2 text-sm text-slate-100 focus:border-sky-500 focus:outline-none"
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
                class="inline-flex items-center rounded-lg bg-sky-500 px-4 py-2 text-sm font-semibold text-white transition hover:bg-sky-400"
                :disabled="projectLoading"
              >
                <span v-if="projectLoading" class="mr-2 animate-spin">⏳</span>
                Save changes
              </button>
              <button
                type="button"
                class="text-sm text-slate-400 hover:text-slate-200"
                @click="resetProjectForm"
              >
                Reset
              </button>
            </div>
          </form>
        </div>
      </article>

      <article class="space-y-4 rounded-2xl border border-slate-800 bg-slate-900/80 p-6 shadow-lg">
        <header class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
          <div>
            <h2 class="text-xl font-semibold text-slate-100">Tasks</h2>
            <p class="text-sm text-slate-400">Track execution and unblock the team quickly.</p>
          </div>
          <button
            type="button"
            class="inline-flex items-center justify-center rounded-lg border border-slate-700 px-3 py-2 text-sm font-semibold text-slate-200 transition hover:border-sky-500 hover:text-sky-200"
            @click="toggleTaskComposer"
          >
            <span class="mr-2 text-lg">+</span>
            Add task
          </button>
        </header>

        <div
          v-if="showTaskComposer"
          class="rounded-xl border border-slate-800 bg-slate-950/70 p-4"
        >
          <h3 class="text-sm font-semibold text-slate-200">New task</h3>
          <form class="mt-3 grid gap-4 sm:grid-cols-2" @submit.prevent="submitTaskCreate">
            <label class="flex flex-col gap-2 sm:col-span-2">
              <span class="text-sm text-slate-300">Title</span>
              <input
                v-model="taskForm.title"
                type="text"
                required
                class="rounded-md border border-slate-700 bg-slate-950 px-3 py-2 text-sm text-slate-100 focus:border-sky-500 focus:outline-none"
              />
            </label>

            <label class="flex flex-col gap-2 sm:col-span-2">
              <span class="text-sm text-slate-300">Description</span>
              <textarea
                v-model="taskForm.description"
                rows="3"
                class="rounded-md border border-slate-700 bg-slate-950 px-3 py-2 text-sm text-slate-100 focus:border-sky-500 focus:outline-none"
              />
            </label>

            <label class="flex flex-col gap-2">
              <span class="text-sm text-slate-300">Due date</span>
              <input
                v-model="taskForm.due_date"
                type="date"
                class="rounded-md border border-slate-700 bg-slate-950 px-3 py-2 text-sm text-slate-100 focus:border-sky-500 focus:outline-none"
              />
            </label>

            <label class="flex flex-col gap-2">
              <span class="text-sm text-slate-300">Status</span>
              <select
                v-model="taskForm.status"
                class="rounded-md border border-slate-700 bg-slate-950 px-3 py-2 text-sm text-slate-100 focus:border-sky-500 focus:outline-none"
              >
                <option v-for="status in taskStatusOptions" :key="status" :value="status">
                  {{ taskStatusLabels[status] }}
                </option>
              </select>
            </label>

            <label class="flex flex-col gap-2">
              <span class="text-sm text-slate-300">Priority</span>
              <select
                v-model="taskForm.priority"
                class="rounded-md border border-slate-700 bg-slate-950 px-3 py-2 text-sm text-slate-100 focus:border-sky-500 focus:outline-none"
              >
                <option v-for="priority in taskPriorityOptions" :key="priority" :value="priority">
                  {{ taskPriorityLabels[priority] }}
                </option>
              </select>
            </label>

            <div class="flex items-center gap-3 sm:col-span-2">
              <button
                type="submit"
                class="inline-flex items-center rounded-lg bg-emerald-500 px-4 py-2 text-sm font-semibold text-white transition hover:bg-emerald-400"
                :disabled="taskLoading"
              >
                <span v-if="taskLoading" class="mr-2 animate-spin">⏳</span>
                Create task
              </button>
              <button
                type="button"
                class="text-sm text-slate-400 hover:text-slate-200"
                @click="toggleTaskComposer"
              >
                Cancel
              </button>
            </div>
          </form>
        </div>

        <div v-if="taskError" class="rounded-md border border-red-500 bg-red-500/10 p-3 text-sm text-red-300">
          {{ taskError }}
        </div>

        <div v-if="!taskLoading && tasks.length === 0" class="text-sm text-slate-400">
          No tasks yet. Add one to kick things off.
        </div>

        <div v-else class="overflow-x-auto">
          <table class="min-w-full divide-y divide-slate-800 text-left text-sm">
            <thead class="bg-slate-900/80 text-slate-300">
              <tr>
                <th class="px-4 py-3 font-medium">Task</th>
                <th class="px-4 py-3 font-medium">Status</th>
                <th class="px-4 py-3 font-medium">Priority</th>
                <th class="px-4 py-3 font-medium">Due</th>
                <th class="px-4 py-3 text-right font-medium">Actions</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-slate-800 text-slate-200">
              <template v-for="task in tasks" :key="task.id">
                <tr v-if="editingTaskId !== task.id" :key="`view-${task.id}`" class="hover:bg-slate-900/60">
                  <td class="px-4 py-3">
                    <div class="font-medium text-slate-100">{{ task.title }}</div>
                    <p v-if="task.description" class="text-xs text-slate-400">
                      {{ task.description }}
                    </p>
                  </td>
                  <td class="px-4 py-3 capitalize">{{ taskStatusLabels[task.status] }}</td>
                  <td class="px-4 py-3 capitalize">{{ taskPriorityLabels[task.priority] }}</td>
                  <td class="px-4 py-3">
                    <span v-if="task.due_date">{{ formatDate(task.due_date) }}</span>
                    <span v-else class="text-xs text-slate-500">—</span>
                  </td>
                  <td class="px-4 py-3 text-right space-x-2">
                    <button
                      class="rounded-md border border-sky-600 px-3 py-1 text-xs font-semibold text-sky-300 transition hover:bg-sky-600/10"
                      :disabled="taskLoading"
                      @click="quickComplete(task)"
                    >
                      Mark done
                    </button>
                    <button
                      class="rounded-md border border-slate-500 px-3 py-1 text-xs font-semibold text-slate-300 transition hover:bg-slate-500/10"
                      :disabled="taskLoading"
                      @click="startTaskEdit(task)"
                    >
                      Edit
                    </button>
                    <button
                      class="rounded-md border border-red-500 px-3 py-1 text-xs font-semibold text-red-300 transition hover:bg-red-500/10"
                      :disabled="taskLoading"
                      @click="removeTask(task)"
                    >
                      Delete
                    </button>
                  </td>
                </tr>
                <tr v-else :key="`edit-${task.id}`" class="bg-slate-900/60">
                  <td colspan="5" class="px-4 py-4">
                    <form class="grid gap-4 sm:grid-cols-2" @submit.prevent="submitTaskEdit">
                      <label class="flex flex-col gap-2 sm:col-span-2">
                        <span class="text-sm text-slate-300">Title</span>
                        <input
                          v-model="taskEditForm.title"
                          type="text"
                          required
                          class="rounded-md border border-slate-700 bg-slate-950 px-3 py-2 text-sm text-slate-100 focus:border-sky-500 focus:outline-none"
                        />
                      </label>
                      <label class="flex flex-col gap-2 sm:col-span-2">
                        <span class="text-sm text-slate-300">Description</span>
                        <textarea
                          v-model="taskEditForm.description"
                          rows="2"
                          class="rounded-md border border-slate-700 bg-slate-950 px-3 py-2 text-sm text-slate-100 focus:border-sky-500 focus:outline-none"
                        />
                      </label>
                      <label class="flex flex-col gap-2">
                        <span class="text-sm text-slate-300">Due date</span>
                        <input
                          v-model="taskEditForm.due_date"
                          type="date"
                          class="rounded-md border border-slate-700 bg-slate-950 px-3 py-2 text-sm text-slate-100 focus:border-sky-500 focus:outline-none"
                        />
                      </label>
                      <label class="flex flex-col gap-2">
                        <span class="text-sm text-slate-300">Status</span>
                        <select
                          v-model="taskEditForm.status"
                          class="rounded-md border border-slate-700 bg-slate-950 px-3 py-2 text-sm text-slate-100 focus:border-sky-500 focus:outline-none"
                        >
                          <option v-for="status in taskStatusOptions" :key="status" :value="status">
                            {{ taskStatusLabels[status] }}
                          </option>
                        </select>
                      </label>
                      <label class="flex flex-col gap-2">
                        <span class="text-sm text-slate-300">Priority</span>
                        <select
                          v-model="taskEditForm.priority"
                          class="rounded-md border border-slate-700 bg-slate-950 px-3 py-2 text-sm text-slate-100 focus:border-sky-500 focus:outline-none"
                        >
                          <option v-for="priority in taskPriorityOptions" :key="priority" :value="priority">
                            {{ taskPriorityLabels[priority] }}
                          </option>
                        </select>
                      </label>
                      <div class="flex items-center gap-3 sm:col-span-2">
                        <button
                          type="submit"
                          class="inline-flex items-center rounded-lg bg-sky-500 px-4 py-2 text-sm font-semibold text-white transition hover:bg-sky-400"
                          :disabled="taskLoading"
                        >
                          <span v-if="taskLoading" class="mr-2 animate-spin">⏳</span>
                          Save task
                        </button>
                        <button
                          type="button"
                          class="text-sm text-slate-400 hover:text-slate-200"
                          @click="cancelTaskEdit"
                        >
                          Cancel
                        </button>
                      </div>
                    </form>
                  </td>
                </tr>
              </template>
            </tbody>
          </table>
        </div>
      </article>
    </div>
  </section>
</template>

<script setup lang="ts">
import { storeToRefs } from 'pinia'
import { useRoute, useRouter, onBeforeRouteUpdate } from '#imports'
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

const taskForm = reactive<TaskPayload>({
  title: '',
  description: '',
  due_date: '',
  status: 'todo',
  priority: 'medium',
})

const showTaskComposer = ref(false)
const editingTaskId = ref<number | null>(null)
const taskEditForm = reactive<TaskPayload>({
  title: '',
  description: '',
  due_date: '',
  status: 'todo',
  priority: 'medium',
})

const hydrateProjectForm = () => {
  if (!project.value) {
    return
  }

  editForm.title = project.value.title
  editForm.description = project.value.description ?? ''
  editForm.due_date = project.value.due_date ?? ''
  editForm.status = project.value.status
}

const resetProjectForm = () => {
  hydrateProjectForm()
}

const toggleTaskComposer = () => {
  showTaskComposer.value = !showTaskComposer.value
  if (!showTaskComposer.value) {
    resetTaskForm()
  }
}

const resetTaskForm = () => {
  taskForm.title = ''
  taskForm.description = ''
  taskForm.due_date = ''
  taskForm.status = 'todo'
  taskForm.priority = 'medium'
}

const resetTaskEditForm = () => {
  editingTaskId.value = null
  taskEditForm.title = ''
  taskEditForm.description = ''
  taskEditForm.due_date = ''
  taskEditForm.status = 'todo'
  taskEditForm.priority = 'medium'
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

const submitTaskCreate = async () => {
  await taskStore.create(
    {
      title: taskForm.title.trim(),
      description: taskForm.description?.trim() || null,
      due_date: taskForm.due_date || null,
      status: taskForm.status,
      priority: taskForm.priority,
    },
    projectId.value
  )
  resetTaskForm()
  showTaskComposer.value = false
}

const removeTask = async (task: { id: number }) => {
  if (!confirm('Delete this task?')) {
    return
  }
  await taskStore.delete(task.id, projectId.value)
}

const quickComplete = async (task: { id: number }) => {
  await taskStore.update(
    task.id,
    {
      status: 'done',
    },
    projectId.value
  )
}

const startTaskEdit = (task: ProjectTask) => {
  editingTaskId.value = task.id
  taskEditForm.title = task.title
  taskEditForm.description = task.description ?? ''
  taskEditForm.due_date = task.due_date ?? ''
  taskEditForm.status = task.status
  taskEditForm.priority = task.priority
}

const cancelTaskEdit = () => {
  resetTaskEditForm()
}

const submitTaskEdit = async () => {
  if (editingTaskId.value === null) {
    return
  }

  await taskStore.update(
    editingTaskId.value,
    {
      title: taskEditForm.title.trim(),
      description: taskEditForm.description?.trim() || null,
      due_date: taskEditForm.due_date || null,
      status: taskEditForm.status,
      priority: taskEditForm.priority,
    },
    projectId.value
  )

  resetTaskEditForm()
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
}

watch(
  () => project.value?.id,
  () => {
    hydrateProjectForm()
  }
)

watch(
  tasks,
  (nextTasks) => {
    if (editingTaskId.value !== null && !nextTasks.some((task) => task.id === editingTaskId.value)) {
      resetTaskEditForm()
    }
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
