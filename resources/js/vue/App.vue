<template>
  <component :is="route.meta.layout">
    <RouterView />
  </component>
</template>

<script setup>
import { RouterView, useRoute } from 'vue-router'

const route = useRoute()
</script>
