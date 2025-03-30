<script setup lang="ts">
import SpinnerLoader from '@/components/SpinnerLoader.vue'
import { useJobListingStore } from '@/stores/jobListing.ts'
import JobPosts from '@/components/JobPosts.vue'
import { ref } from 'vue'
import { storeToRefs } from 'pinia'

const props = defineProps(['page'])
const page = ref(parseInt(props.page) || 0)
const jobListingStore = useJobListingStore()
const changePage = jobListingStore.changePage
const { jobs, pagination, loading } = storeToRefs(jobListingStore)

const initialLoad = () => {
  changePage(1)
  page.value = 1
}

if (props.page > 0 && pagination.value.page !== props.page) {
  changePage(parseInt(props.page))
}
</script>

<template>
  <main class="mt-5 flex items-center justify-center">
    <button
      v-if="page === 0 && pagination.page === 0"
      @click="initialLoad"
      class="bg-transparent hover:bg-blue-500 text-blue-700 font-semibold hover:text-white py-2 px-4 border border-blue-500 hover:border-transparent rounded"
    >
      Begin browsing jobs
    </button>
    <div v-if="loading" class="mt-5">
      <SpinnerLoader />
    </div>
    <div v-if="loading === false" class="mt-5">
      <JobPosts :jobs :pagination :changePage />
    </div>
  </main>
</template>
