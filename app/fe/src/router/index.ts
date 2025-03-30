import { createRouter, createWebHistory } from 'vue-router'
import JobListingView from '../views/JobListingView.vue'
import AboutView from '../views/AboutView.vue'
import JobDetailView from '@/views/JobDetailView.vue'
import UnhandledErrorView from '@/views/UnhandledErrorView.vue'

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes: [
    {
      path: '/',
      name: 'homepage',
      component: JobListingView,
      props: { page: 0 },
    },
    {
      path: '/list/:page?',
      name: 'listing',
      component: JobListingView,
      props: true,
    },
    {
      path: '/job/:id',
      name: 'job-detail',
      component: JobDetailView,
      props: true,
    },
    {
      path: '/about',
      name: 'about',
      component: AboutView,
    },
    {
      path: '/error',
      name: 'error',
      component: UnhandledErrorView,
    },
  ],
})

export default router
