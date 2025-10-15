// purpose: Pinia store managing task CRUD operations scoped to a project
import { defineStore } from 'pinia'
import type { Task } from './project'

export type TaskStatus = 'todo' | 'in_progress' | 'blocked' | 'done'
export type TaskPriority = 'low' | 'medium' | 'high' | 'critical'

export interface TaskPayload {
  title: string
  description?: string | null
  due_date?: string | null
  status?: TaskStatus
  priority?: TaskPriority
}

export type TaskUpdatePayload = Partial<TaskPayload>

export const useTaskStore = defineStore('task-store', () => {
  const items = ref<Task[]>([])
  const selected = ref<Task | null>(null)
  const loading = ref(false)
  const error = ref<string | null>(null)
  const projectId = ref<number | string | null>(null)

  const apiBaseUrl = computed(
    () => useRuntimeConfig().public.apiBaseUrl || 'http://localhost:8080/api'
  )

  const handleError = (err: unknown) => {
    error.value = err instanceof Error ? err.message : 'Unexpected error'
    console.warn('[task-store] API error', err)
  }

  const ensureProjectId = (pid?: number | string | null): number | string => {
    if (pid !== undefined && pid !== null) {
      projectId.value = pid
    }

    if (projectId.value === null) {
      throw new Error('Project ID must be set before interacting with tasks')
    }

    return projectId.value
  }

  const endpoint = (pid: number | string) => `${apiBaseUrl.value}/projects/${pid}/tasks`

  const fetchAll = async (pid?: number | string) => {
    loading.value = true
    error.value = null
    try {
      const resolvedPid = ensureProjectId(pid)
      const tasks = await $fetch<Task[]>(endpoint(resolvedPid))
      items.value = tasks
      console.log('[task-store] fetched tasks', { projectId: resolvedPid, count: tasks.length })
    } catch (err) {
      handleError(err)
    } finally {
      loading.value = false
    }
  }

  const fetchOne = async (taskId: number | string, pid?: number | string) => {
    loading.value = true
    error.value = null
    try {
      const resolvedPid = ensureProjectId(pid)
      const task = await $fetch<Task>(`${endpoint(resolvedPid)}/${taskId}`)
      selected.value = task
      console.log('[task-store] fetched task', { projectId: resolvedPid, taskId })
    } catch (err) {
      handleError(err)
    } finally {
      loading.value = false
    }
  }

  const create = async (payload: TaskPayload, pid?: number | string) => {
    loading.value = true
    error.value = null
    try {
      const resolvedPid = ensureProjectId(pid)
      await $fetch<Task>(endpoint(resolvedPid), {
        method: 'POST',
        body: payload,
      })
      console.log('[task-store] created task', { projectId: resolvedPid })
      await fetchAll(resolvedPid)
    } catch (err) {
      handleError(err)
    } finally {
      loading.value = false
    }
  }

  const update = async (taskId: number | string, payload: TaskUpdatePayload, pid?: number | string) => {
    loading.value = true
    error.value = null
    try {
      const resolvedPid = ensureProjectId(pid)
      await $fetch<Task>(`${endpoint(resolvedPid)}/${taskId}`, {
        method: 'PUT',
        body: payload,
      })
      console.log('[task-store] updated task', { projectId: resolvedPid, taskId })
      await fetchAll(resolvedPid)
      await fetchOne(taskId, resolvedPid)
    } catch (err) {
      handleError(err)
    } finally {
      loading.value = false
    }
  }

  const remove = async (taskId: number | string, pid?: number | string) => {
    loading.value = true
    error.value = null
    try {
      const resolvedPid = ensureProjectId(pid)
      await $fetch(`${endpoint(resolvedPid)}/${taskId}`, {
        method: 'DELETE',
      })
      console.log('[task-store] deleted task', { projectId: resolvedPid, taskId })
      await fetchAll(resolvedPid)
      if (selected.value?.id === Number(taskId)) {
        selected.value = null
      }
    } catch (err) {
      handleError(err)
    } finally {
      loading.value = false
    }
  }

  return {
    items,
    selected,
    loading,
    error,
    projectId,
    fetchAll,
    fetchOne,
    create,
    update,
    delete: remove,
  }
})
