<script setup lang="ts">
const { w, h } = defineProps<{ w?: number; h?: number }>()
</script>
<template>
  <div class="flex items-center justify-center">
    <div
      :class="`w-${w ?? 12} h-${h ?? 12} border-4 border-t-transparent border-gray-500 rounded-full animate-spin`"
    ></div>
  </div>
</template>
