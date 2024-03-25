import { createRouter, createWebHistory } from 'vue-router'

// Layouts
import EmptyLayout from '@/layouts/EmptyLayout.vue'
import DefaultLayout from '@/layouts/DefaultLayout.vue'

// Views
const HomeView = () => import('@/views/HomeView.vue')
const ExchangeHistory = () => import('@/views/ExchangeHistory.vue')
const NotFoundView = () => import('@/views/NotFoundView.vue')

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes: [
    {
      path: '/',
      name: 'Home',
      component: HomeView,
      meta: { layout: DefaultLayout }
    },
    {
      path: '/historical-rates/',
      name: 'ExchangeHistory',
      component: ExchangeHistory,
      meta: { layout: DefaultLayout },
      props: true
    },
    {
      path: '/:pathMatch(.*)*',
      name: 'NotFound',
      component: NotFoundView,
      meta: { layout: EmptyLayout }
    }
  ]
})

// Sets the default layout for routes without meta.layout defined
router.beforeEach((to, from, next) => {
  if (!to.meta.layout) {
    to.meta.layout = EmptyLayout
  }
  next()
})

export default router
