<script setup lang="ts">
import SpinnerLoader from '@/components/SpinnerLoader.vue'
import { storeToRefs } from 'pinia'
import { ref } from 'vue'
import { createAnswer } from '@/lib/apiClient.ts'
import SuccessAlert from '@/components/SuccessAlert.vue'
import ErrorAlert from '@/components/ErrorAlert.vue'

type Props = {
  show: boolean
  jobId: number
}

const { show, jobId }: Props = defineProps({
  show: Boolean,
  jobId: Number,
})
const emit = defineEmits<{
  (e: 'close'): void
}>()

const { editApplication } = window._appStores.applications
const { applications } = storeToRefs(window._appStores.applications)
const application =
  applications[jobId] ??
  editApplication(jobId, {
    email: '',
    message: '',
    phone: '',
  })
const error = ref('')
const loading = ref(false)
const apply = async () => {
  loading.value = true
  error.value = ''
  const { statusCode, response } = await createAnswer({
    job_id: jobId,
    name: application.value.name,
    cover_letter: application.value.message,
    phone: application.value.phone,
  })
  application.value.submitted = statusCode === 201
  if (statusCode !== 201) {
    error.value = (await response()).meta?.message || 'Unknown error'
  }
  loading.value = false
}
const close = () => {
  loading.value = false
  error.value = ''
  emit('close')
}
</script>

<template>
  <div v-if="show" class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50">
    <div
      class="bg-white p-6 rounded-lg shadow-lg max-w-md w-full transform transition-transform duration-300 ease-in-out scale-100"
    >
      <div v-if="loading" class="mb-5">
        <SpinnerLoader w="8" h="8" />
      </div>
      <div v-if="error !== ''" class="mb-10">
        <ErrorAlert title="Error!" :message="error" />
      </div>
      <form v-if="application.submitted === false" name="application" @submit.prevent>
        <h2 class="text-xl font-bold mb-4">Get yourself a job</h2>
        <div class="mb-4 mt-10">
          <label class="block text-gray-700 text-sm font-bold mb-2" for="name">
            First and last name
          </label>
          <input
            :disabled="loading"
            v-model="application.name"
            class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
            id="name"
            type="text"
            placeholder=""
          />
        </div>
        <div class="mb-6">
          <label class="block text-gray-700 text-sm font-bold mb-2" for="phone"> Phone </label>
          <input
            :disabled="loading"
            v-model="application.phone"
            class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
            id="phone"
            type="text"
            placeholder=""
          />
        </div>
        <div class="mb-6">
          <label class="block text-gray-700 text-sm font-bold mb-2" for="message">
            Leave a message
          </label>
          <textarea
            :disabled="loading"
            v-model="application.message"
            class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
            id="message"
            type="text"
            placeholder=""
          ></textarea>
        </div>
        <div class="flex justify-end space-x-2">
          <button
            @click="close"
            class="px-4 py-2 bg-gray-300 text-gray-800 rounded hover:bg-gray-400"
          >
            Dismiss
          </button>
          <button
            @click="apply"
            :class="`px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 ${loading ? 'cursor-not-allowed' : ''}`"
          >
            Apply
          </button>
        </div>
      </form>
      <SuccessAlert
        v-else
        title="You application has been submitted!"
        message="Check your email for confirmation and we wish you luck."
        @close="close"
      ></SuccessAlert>
    </div>
  </div>
</template>
