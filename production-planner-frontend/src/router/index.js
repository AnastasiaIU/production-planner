import { createRouter, createWebHistory } from 'vue-router'
import PlannerView from '../views/PlannerView.vue'
import { useAuthStore } from '@/stores/auth'

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes: [
    {
      path: '/',
      name: 'planner',
      component: PlannerView
    },
    {
      path: '/plans',
      name: 'plans',
      component: () => import('../views/PlansView.vue'),
      meta: { requiresAuth: true }
    },
    {
      path: '/login',
      name: 'login',
      component: () => import('../views/LoginView.vue')
    },
    {
      path: '/register',
      name: 'register',
      component: () => import('../views/RegisterView.vue')
    },
    {
      path: '/admin-panel',
      component: () => import('../views/AdminView.vue'),
      meta: { requiresAuth: true, requiresAdmin: true },
      children: [
        {
          path: '',
          redirect: '/admin-panel/users'
        },
        {
          path: 'users',
          name: 'adminUsers',
          component: () => import('../components/AdminUsers.vue')
        },
        {
          path: 'pictures',
          name: 'adminPictures',
          component: () => import('../components/AdminPictures.vue')
        },
        {
          path: 'json',
          name: 'adminJson',
          component: () => import('../components/AdminJson.vue')
        }
      ]
    }
  ],
})

router.beforeEach((to, from, next) => {
  const authStore = useAuthStore()
  const user = authStore.user

  // Not logged in? Redirect to login
  if (to.meta.requiresAuth && !authStore.isAuthenticated) {
    next({ name: 'login' })
  }

  // Redirect non-admins to the homepage
  if (to.meta.requiresAdmin && user?.role !== 'Admin') {
    return next({ name: 'planner' })
  }

  next()
})

export default router
