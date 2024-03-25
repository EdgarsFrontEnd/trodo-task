<template>
  <main>
    <RouterView />
  </main>
</template>

<script setup>
import { RouterView } from 'vue-router'
import { onMounted, onBeforeUnmount } from 'vue'

onMounted(() => {
  const scrollpos = localStorage.getItem('scrollpos')
  if (scrollpos) {
    window.scrollTo(0, scrollpos)
  }
})

onBeforeUnmount(() => {
  localStorage.setItem('scrollpos', window.scrollY)
})
</script>
