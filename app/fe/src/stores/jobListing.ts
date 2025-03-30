import { reactive, ref, shallowRef, watch } from 'vue'
import { defineStore } from 'pinia'
import type { Job } from '@/lib/apiTypes.ts'
import { fetchJobs as fetchJobsFromApi } from '@/lib/apiClient.ts'

export type Pagination = {
  limit: number
  page: number
  pages: number[]
}

const defaultPagination = { limit: 12, page: 0 }

export const useJobListingStore = defineStore('jobListing', () => {
  const aborts = new Set<() => void>()
  const loading = ref<boolean>(false)
  const jobs = shallowRef<Job[]>([])
  const pagination = reactive<Pagination>(defaultPagination)

  function changePage(page: number): void {
    if (page < 0) {
      throw new Error('Page cannot be negative')
    }

    pagination.page = page
  }

  watch(
    () => pagination.page,
    () => {
      loading.value = true
    },
  )

  watch(
    () => pagination.page,
    async (): Promise<void> => {
      try {
        // not using Suspense just to demonstrate different approach
        aborts.entries().forEach(([cb]) => cb())
        const [abort, response] = fetchJobsFromApi(pagination.limit, pagination.page)
        aborts.add(abort)
        const result = await response()
        pagination.pages = [
          ...Array(parseInt(Math.ceil(result.meta.entries_total / pagination.limit))).keys(),
        ].map((p) => p + 1)
        jobs.value = result.payload
        loading.value = false
        aborts.delete(abort)
      } catch (e) {
        if (e instanceof DOMException && e.name === 'AbortError') {
          return
        }
        throw new Error('Could not fetch job postings from API. Please try again later.', {
          cause: e,
        })
      }
    },
  )

  return { jobs, pagination, loading, changePage }
})
