<script setup lang="ts">
const { title, message } = defineProps<{ title: string; message: string }>()
const emit = defineEmits<{
  (e: 'close'): void
}>()
</script>

<template>
  <div
    class="bg-teal-100 border-t-4 border-teal-500 rounded-b text-teal-900 px-4 py-3 shadow-md"
    role="alert"
  >
    <div class="flex">
      <div class="py-1">
        <svg
          class="fill-current h-6 w-6 text-teal-500 mr-4"
          xmlns="http://www.w3.org/2000/svg"
          viewBox="0 0 20 20"
        >
          <path
            d="M2.93 17.07A10 10 0 1 1 17.07 2.93 10 10 0 0 1 2.93 17.07zm12.73-1.41A8 8 0 1 0 4.34 4.34a8 8 0 0 0 11.32 11.32zM9 11V9h2v6H9v-4zm0-6h2v2H9V5z"
          />
        </svg>
      </div>
      <div>
        <p class="font-bold">{{ title }}</p>
        <p class="text-sm">{{ message }}</p>
      </div>
    </div>
    <div class="flex justify-end space-x-2 mt-5">
      <button
        @click="emit('close')"
        class="px-4 py-2 bg-gray-300 text-gray-800 rounded hover:bg-gray-400"
      >
        Dismiss
      </button>
    </div>
  </div>
</template>
