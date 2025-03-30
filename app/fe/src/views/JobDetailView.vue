<script setup lang="ts">
import JobPostDetail from '@/components/JobPostDetail.vue'
import SpinnerLoader from '@/components/SpinnerLoader.vue'
const { id, job } = defineProps({
  id: String,
  job: Object,
})
</script>

<template>
  <Suspense>
    <JobPostDetail :id :job />
    <template #fallback>
      <div class="mt-10"><SpinnerLoader /></div>
    </template>
  </Suspense>
</template>

<style scoped></style>
