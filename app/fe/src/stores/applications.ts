import { defineStore } from 'pinia'
import { type Ref, ref } from 'vue'

export const useApplicationsStore = defineStore('applications', () => {
  type Application = {
    message: string
    email: string
    phone: string
    submitted: boolean
  }
  const applications = ref<{ [key: number]: Ref<Application> }>({})
  const editApplication = (jobId: number, application: Application): Ref<Application> => {
    if (undefined === applications[jobId]) {
      applications[jobId] = ref<Application>({
        message: '',
        email: '',
        phone: '',
        submitted: false,
      })
    }
    applications[jobId].value.email = application.email
    applications[jobId].value.message = application.message
    applications[jobId].value.phone = application.phone

    return applications[jobId]
  }

  return { applications, editApplication }
})
