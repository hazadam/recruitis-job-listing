<script setup lang="ts">
import { onErrorCaptured } from 'vue'
import { useRouter } from 'vue-router'

const router = useRouter()
onErrorCaptured((err: Error) => {
  router.push('/error')
  console.log(err) // send to sentry or something
})
</script>

<template>
  <slot></slot>
</template>
