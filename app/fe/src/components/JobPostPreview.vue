<script setup lang="ts">
import type { Job } from '@/lib/apiTypes.ts'
const props: { job: Job } = defineProps(['job'])
const { job } = props
const description =
  job.description.length > 256 ? job.description.slice(0, 256) + '...' : job.description // browsers will close open tags inside trimmed html on their own 🤞
</script>

<template>
  <div class="job-card mx-5 mb-10 rounded overflow-hidden shadow-lg">
    <div class="px-6 py-4">
      <div class="font-bold text-xl mb-2">{{ job.title }}</div>
      <p class="text-gray-700 text-base" v-html="description"></p>
      <div class="mt-4">
        <h3 class="text-lg font-semibold">Salary</h3>
        <p v-if="job.salary?.visible ?? false" class="text-gray-700">
          <span v-if="job.salary.is_range">{{ job.salary.min }} - {{ job.salary.max }}</span>
          <span v-else>{{ job.salary.amount }}</span>
          {{ job.salary.currency }} / {{ job.salary.unit }}
        </p>
        <span v-else>N/A</span>
      </div>
    </div>
    <div class="px-6 pt-4 pb-2">
      <span
        class="inline-block bg-gray-200 rounded-full px-3 py-1 text-sm font-semibold text-gray-700 mr-2 mb-2"
        >{{ job.employment.name }}</span
      >
    </div>
    <div class="mb-5 ml-5 mr-5 flex">
      <RouterLink
        class="ml-auto bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded"
        :to="`/job/${job.job_id}`"
        >Detail</RouterLink
      >
    </div>
  </div>
</template>

<style scoped>
.job-card {
  max-width: 400px;
  min-width: 150px;
}
</style>
