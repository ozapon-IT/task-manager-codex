// purpose: Pinia store coordinating project CRUD operations against the Laravel API
import { defineStore } from 'pinia'

export type ProjectStatus = 'draft' | 'active' | 'completed' | 'archived'

export interface ProjectPayload {
  title: string
  description?: string | null
  due_date?: string | null
  status?: ProjectStatus
}

export interface Project {
  id: number
  title: string
  description: string | null
  due_date: string | null
  status: ProjectStatus
  created_at?: string
  updated_at?: string
  tasks?: Task[]
}

export interface Task {
  id: number
  project_id: number
  title: string
  description: string | null
  due_date: string | null
  status: 'todo' | 'in_progress' | 'blocked' | 'done'
  priority: 'low' | 'medium' | 'high' | 'critical'
  created_at?: string
  updated_at?: string
}

type ProjectCollectionResponse = { data: Project[] }

export const useProjectStore = defineStore('project-store', () => {
  const items = ref<Project[]>([])
  const selected = ref<Project | null>(null)
  const loading = ref(false)
  const error = ref<string | null>(null)

  const apiBaseUrl = computed(
    () => useRuntimeConfig().public.apiBaseUrl || 'http://localhost:8080/api'
  )

  const resolveProjects = (payload: Project[] | ProjectCollectionResponse): Project[] => {
    if (Array.isArray(payload)) {
      return payload
    }

    if (payload && Array.isArray(payload.data)) {
      return payload.data
    }

    return []
  }

  const handleError = (err: unknown) => {
    error.value = err instanceof Error ? err.message : 'Unexpected error'
    console.warn('[project-store] API error', err)
  }

  const fetchAll = async () => {
    loading.value = true
    error.value = null
    try {
      const payload = await $fetch<Project[] | ProjectCollectionResponse>(
        `${apiBaseUrl.value}/projects`
      )
      items.value = resolveProjects(payload)
      console.log('[project-store] fetched projects', items.value.length)
    } catch (err) {
      handleError(err)
    } finally {
      loading.value = false
    }
  }

  const fetchOne = async (projectId: number | string) => {
    loading.value = true
    error.value = null
    try {
      const project = await $fetch<Project>(`${apiBaseUrl.value}/projects/${projectId}`)
      selected.value = project
      console.log('[project-store] fetched project', projectId)
    } catch (err) {
      handleError(err)
    } finally {
      loading.value = false
    }
  }

  const create = async (payload: ProjectPayload) => {
    loading.value = true
    error.value = null
    try {
      const project = await $fetch<Project>(`${apiBaseUrl.value}/projects`, {
        method: 'POST',
        body: payload,
      })
      console.log('[project-store] created project', project.id)
      await fetchAll()
      selected.value = project
    } catch (err) {
      handleError(err)
    } finally {
      loading.value = false
    }
  }

  const update = async (projectId: number | string, payload: ProjectPayload) => {
    loading.value = true
    error.value = null
    try {
      const project = await $fetch<Project>(`${apiBaseUrl.value}/projects/${projectId}`, {
        method: 'PUT',
        body: payload,
      })
      console.log('[project-store] updated project', projectId)
      await fetchAll()
      selected.value = project
    } catch (err) {
      handleError(err)
    } finally {
      loading.value = false
    }
  }

  const remove = async (projectId: number | string) => {
    loading.value = true
    error.value = null
    try {
      await $fetch(`${apiBaseUrl.value}/projects/${projectId}`, {
        method: 'DELETE',
      })
      console.log('[project-store] deleted project', projectId)
      await fetchAll()
      if (selected.value?.id === Number(projectId)) {
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
    fetchAll,
    fetchOne,
    create,
    update,
    delete: remove,
  }
})
