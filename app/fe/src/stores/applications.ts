import { defineStore } from 'pinia'
import { ref } from 'vue'

export const useApplicationsStore = defineStore('applications', () => {
  type Application = {
    message: string
    name: string
    email: string
    phone: string
    submitted: boolean
  }
  const applications = ref<{ [key: number]: Application }>({})
  const editApplication = (jobId: number, application: Application): Application => {
    if (undefined === applications.value[jobId]) {
      applications.value[jobId] = application
    }
    applications.value[jobId].email = application.email
    applications.value[jobId].name = application.name
    applications.value[jobId].message = application.message
    applications.value[jobId].phone = application.phone
    applications.value[jobId].submitted = application.submitted

    return applications.value[jobId]
  }

  return { applications, editApplication }
})
