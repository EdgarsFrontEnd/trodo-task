<template>
  <transition :name="transitionName">
    <div class="expand-content" ref="expandRef">
      <slot></slot>
    </div>
  </transition>
</template>

<script setup>
import { ref, onMounted, onUpdated } from 'vue'

const transitionName = 'expand-height'
const expandRef = ref(null)

onMounted(() => {
  setupTransition()
})

onUpdated(() => {
  setupTransition()
})

const setupTransition = () => {
  const el = expandRef.value
  el.style.height = 'auto'
  const height = el.scrollHeight + 'px'
  el.style.height = '0'
  // Force reflow to apply initial height
  el.offsetHeight
  el.style.height = height
}
</script>

<style scoped>
.expand-content {
  overflow: hidden;
  transition: height 0.5s;
}
</style>
