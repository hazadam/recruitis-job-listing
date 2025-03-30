<script setup lang="ts">
import { fetchJob } from '@/lib/apiClient.ts'
import ApplyNow from '@/components/ApplyNow.vue'
import { ref } from 'vue'
import type { Job } from '@/lib/apiTypes.ts'
import { useRouter } from 'vue-router'

type Props = {
  job?: Job // was thinking of caching locally somewhere since I'm fetching the payload for list purposes anyway
  id: string
}

const router = useRouter()
const props = defineProps<Props>()
const job = props.job || (await fetchJob(props.id)).payload
const applyNow = ref(false)
</script>

<template>
  <div class="max-w-100 mx-auto bg-white rounded-2xl shadow-lg p-6">
    <h1 class="text-2xl text-center font-bold text-gray-800 mb-10 mt-10">{{ job.title }}</h1>
    <p class="text-gray-600 mb-4" v-html="job.description"></p>
    <div class="mt-4">
      <h3 class="text-lg font-semibold">Salary</h3>
      <p v-if="job.salary?.visible ?? false" class="text-gray-700">
        <span v-if="job.salary.is_range">{{ job.salary.min }} - {{ job.salary.max }}</span>
        <span v-else>{{ job.salary.max || job.salary.min }}</span>
        {{ job.salary.currency }} / {{ job.salary.unit }}
      </p>
      <span v-else>N/A</span>
    </div>
    <div class="px-6 pt-4 pb-2">
      <span
        class="inline-block bg-gray-200 rounded-full px-3 py-1 text-sm font-semibold text-gray-700 mr-2 mb-2"
        >{{ job.employment.name }}</span
      >
    </div>
    <div class="mt-5 flex center-items justify-between">
      <button
        class="bg-gray-500 text-white py-2 px-5 rounded-lg font-medium transition"
        @click="router.back()"
      >
        Back
      </button>
      <button
        class="bg-blue-600 text-white py-2 px-5 rounded-lg font-medium hover:bg-blue-700 transition"
        @click="applyNow = true"
      >
        Apply Now
      </button>
    </div>
  </div>
  <Transition>
    <ApplyNow :job-id="job.job_id" :show="applyNow" @close="() => (applyNow = false)" />
  </Transition>
</template>

<style scoped></style>
