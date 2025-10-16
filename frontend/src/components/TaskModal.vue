<!-- purpose: Modal used for creating and editing tasks with shared form controls -->
<template>
  <Teleport to="body">
    <Transition name="fade">
      <div
        v-if="open"
        class="fixed inset-0 z-50 flex items-center justify-center px-4 py-6 sm:px-6"
        role="dialog"
        aria-modal="true"
        tabindex="-1"
        @keydown.esc.prevent="handleClose"
      >
        <div class="absolute inset-0 bg-slate-900/40 backdrop-blur-sm transition dark:bg-slate-950/80" @click="handleClose" />

        <div
          class="relative w-full max-w-xl rounded-2xl border border-lightBorder bg-lightCard p-6 text-lightHeading shadow-2xl transition-colors dark:border-slate-800 dark:bg-slate-900/95 dark:text-slate-100"
        >
          <header class="mb-4 flex items-start justify-between gap-4">
            <div>
              <h2 class="text-lg font-semibold text-lightHeading dark:text-slate-50">{{ title }}</h2>
              <p v-if="subtitle" class="text-xs text-lightText dark:text-slate-400">{{ subtitle }}</p>
            </div>
            <button
              class="rounded-full border border-lightBorder px-2 py-1 text-xs text-lightText transition hover:border-blue-400 hover:text-blue-600 dark:border-slate-600 dark:text-slate-300 dark:hover:border-slate-400 dark:hover:text-white"
              @click="handleClose"
            >
              Esc
            </button>
          </header>

          <form class="grid gap-4" @submit.prevent="handleSave">
            <label class="flex flex-col gap-2">
              <span class="text-sm text-lightText dark:text-slate-300">Title</span>
              <input
                v-model="form.title"
                type="text"
                required
                class="rounded-md border border-lightBorder bg-lightCard px-3 py-2 text-sm text-lightHeading transition focus:border-blue-500 focus:outline-none dark:border-slate-700 dark:bg-slate-950 dark:text-slate-100 dark:focus:border-sky-500"
              />
            </label>

            <label class="flex flex-col gap-2">
              <span class="text-sm text-lightText dark:text-slate-300">Description</span>
              <textarea
                v-model="form.description"
                rows="3"
                class="rounded-md border border-lightBorder bg-lightCard px-3 py-2 text-sm text-lightHeading transition focus:border-blue-500 focus:outline-none dark:border-slate-700 dark:bg-slate-950 dark:text-slate-100 dark:focus:border-sky-500"
              />
            </label>

            <div class="grid gap-4 sm:grid-cols-3">
              <label class="flex flex-col gap-2 sm:col-span-1">
                <span class="text-sm text-lightText dark:text-slate-300">Priority</span>
                <select
                  v-model="form.priority"
                  class="rounded-md border border-lightBorder bg-lightCard px-3 py-2 text-sm text-lightHeading transition focus:border-blue-500 focus:outline-none dark:border-slate-700 dark:bg-slate-950 dark:text-slate-100 dark:focus:border-sky-500"
                >
                  <option v-for="priority in priorityOptions" :key="priority" :value="priority">
                    {{ priority }}
                  </option>
                </select>
              </label>

              <label class="flex flex-col gap-2 sm:col-span-1">
                <span class="text-sm text-lightText dark:text-slate-300">Status</span>
                <select
                  v-model="form.status"
                  class="rounded-md border border-lightBorder bg-lightCard px-3 py-2 text-sm text-lightHeading transition focus:border-blue-500 focus:outline-none dark:border-slate-700 dark:bg-slate-950 dark:text-slate-100 dark:focus:border-sky-500"
                >
                  <option v-for="status in statusOptions" :key="status" :value="status">
                    {{ status }}
                  </option>
                </select>
              </label>

              <label class="flex flex-col gap-2 sm:col-span-1">
                <span class="text-sm text-lightText dark:text-slate-300">Due date</span>
                <input
                  v-model="form.due_date"
                  type="date"
                  class="rounded-md border border-lightBorder bg-lightCard px-3 py-2 text-sm text-lightHeading transition focus:border-blue-500 focus:outline-none dark:border-slate-700 dark:bg-slate-950 dark:text-slate-100 dark:focus:border-sky-500"
                />
              </label>
            </div>

            <footer class="mt-2 flex justify-end gap-3">
              <button
                type="button"
                class="rounded-lg border border-lightBorder px-4 py-2 text-sm font-medium text-lightText transition hover:border-blue-400 hover:text-lightHeading dark:border-slate-700 dark:text-slate-300 dark:hover:border-slate-500 dark:hover:text-white"
                @click="handleClose"
              >
                Cancel
              </button>
              <button
                type="submit"
                class="rounded-lg bg-blue-600 px-4 py-2 text-sm font-semibold text-white transition hover:bg-blue-500 dark:bg-sky-500 dark:hover:bg-sky-400/80"
              >
                Save
              </button>
            </footer>
          </form>
        </div>
      </div>
    </Transition>
  </Teleport>
</template>

<script setup lang="ts">
import { reactive, watch } from 'vue'

interface TaskForm {
  title: string
  description: string | null
  priority: string
  status: string
  due_date: string | null
}

const props = defineProps({
  open: { type: Boolean, required: true },
  title: { type: String, default: 'Task' },
  subtitle: { type: String, default: '' },
  task: {
    type: Object as () => Partial<TaskForm> | null,
    default: null,
  },
  priorityOptions: {
    type: Array as () => string[],
    default: () => ['low', 'medium', 'high', 'critical'],
  },
  statusOptions: {
    type: Array as () => string[],
    default: () => ['todo', 'in_progress', 'blocked', 'done'],
  },
})

const emit = defineEmits<{
  (e: 'close'): void
  (e: 'save', payload: TaskForm): void
}>()

const form = reactive<TaskForm>({
  title: '',
  description: '',
  priority: props.priorityOptions[0],
  status: props.statusOptions[0],
  due_date: '',
})

const toDateInputValue = (value: string | null | undefined): string => {
  if (!value) {
    return ''
  }
  return value.length >= 10 ? value.slice(0, 10) : value
}

const setForm = () => {
  form.title = props.task?.title ?? ''
  form.description = props.task?.description ?? ''
  form.priority = props.task?.priority ?? props.priorityOptions[0]
  form.status = props.task?.status ?? props.statusOptions[0]
  form.due_date = toDateInputValue(props.task?.due_date)
}

watch(
  () => props.open,
  (open) => {
    if (open) {
      setForm()
    }
  }
)

watch(
  () => props.task,
  () => {
    if (props.open) {
      setForm()
    }
  },
  { deep: true }
)

const handleClose = () => {
  emit('close')
}

const handleSave = () => {
  emit('save', { ...form })
}
</script>

<style scoped>
.fade-enter-active,
.fade-leave-active {
  transition: opacity 0.15s ease;
}
.fade-enter-from,
.fade-leave-to {
  opacity: 0;
}
</style>
