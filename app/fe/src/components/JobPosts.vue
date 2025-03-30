<script setup lang="ts">
import type { Job } from '@/lib/apiTypes.ts'
import JobPostPreview from '@/components/JobPostPreview.vue'
import type { Pagination } from '@/stores/jobListing.ts'
import { useRoute, useRouter } from 'vue-router'
import { watch } from 'vue'

type Props = {
  jobs: Job[]
  pagination: Pagination
  changePage(page: number): void
}

const router = useRouter()
const route = useRoute()
const props = defineProps<Props>()
const { jobs, pagination, changePage } = props
const next = () => {
  const nextPage = pagination.page + 1
  router.push(`/list/${nextPage}`)
  changePage(nextPage)
}
const prev = () => {
  const nextPage = pagination.page - 1
  router.push(`/list/${nextPage}`)
  changePage(nextPage)
}

watch(
  () => route.params.page,
  () => {
    // vue router cache would display the same content even when moving back/forth in history
    // regardless of what "page" is in the URL
    if (typeof route.params.page === 'string' || undefined === route.params.page) {
      const pageInRoute = parseInt(route.params.page || '1')
      if (pageInRoute !== pagination.page) {
        changePage(pageInRoute)
      }
    }
  },
)
</script>

<template>
  <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3">
    <JobPostPreview v-for="job in jobs" :key="job.job_id" :job="job" />
  </div>
  <div v-if="jobs.length > 0" class="mt-5 flex items-center justify-center gap-2">
    <button
      v-if="pagination.page > 1"
      @click="prev"
      class="btn p-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700"
    >
      Prev
    </button>
    <RouterLink
      v-for="pageNumber in pagination.pages"
      :key="`${pageNumber}`"
      :class="`text-blue-600 visited:text-purple-600 ${pageNumber == pagination.page ? 'underline' : ''}`"
      @click="changePage(pageNumber)"
      :to="`/list/${pageNumber}`"
      >{{ pageNumber }}</RouterLink
    >
    <button
      v-if="(pagination.page || 0) < pagination.pages.length"
      @click="next"
      class="btn p-2 bg-blue-600 text-white rounded-lg font-medium hover:bg-blue-700"
    >
      Next
    </button>
  </div>
</template>

<style scoped></style>
