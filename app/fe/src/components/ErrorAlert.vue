<script setup lang="ts">
const { title, message } = defineProps<{ title: string; message: string }>()
</script>

<template>
  <div class="bg-red-500 text-white font-bold rounded-t px-4 py-2">{{ title }}</div>
  <div class="border border-t-0 border-red-400 rounded-b bg-red-100 px-4 py-3 text-red-700">
    <p>{{ message }}</p>
  </div>
</template>
