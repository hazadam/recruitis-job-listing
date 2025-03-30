<script setup lang="ts">
import { RouterLink, RouterView } from 'vue-router'
import CompanyIcon from '@/components/icons/CompanyIcon.vue'
import ErrorBoundary from '@/components/ErrorBoundary.vue'
</script>

<template>
  <header>
    <div class="flex items-center justify-center">
      <CompanyIcon />
    </div>
    <div class="flex items-center justify-center mt-5">
      <nav class="nav-link">
        <RouterLink to="/">Home</RouterLink>
        <RouterLink to="/about">About</RouterLink>
      </nav>
    </div>
    <div
      class="mt-5 w-full h-px bg-gradient-to-r from-transparent via-gray-300 to-transparent"
    ></div>
  </header>

  <ErrorBoundary>
    <RouterView />
  </ErrorBoundary>
</template>

<style scoped>
a {
  padding: 1rem;
}
</style>
