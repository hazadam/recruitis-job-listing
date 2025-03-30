<script setup lang="ts">
import JobPostDetail from '@/components/JobPostDetail.vue'
import SpinnerLoader from '@/components/SpinnerLoader.vue'
import type { Job } from '@/lib/apiTypes.ts'

const { id, job } = defineProps<{ id: string; job?: Job }>()
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
