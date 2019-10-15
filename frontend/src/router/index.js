import Vue from 'vue'
import Router from 'vue-router'
import {setPageTitle} from '@/router/middlewares'
import {VUE_APP_NAME, VUE_CLI_BASE_URL, VUE_APP_URL, VUE_APP_ENV} from '@/environment/index'

Vue.use(Router)

const baseUrl = VUE_APP_ENV !== 'development' ? VUE_APP_URL : VUE_CLI_BASE_URL

// Route-level code splitting
const Login = () => import('@/views/auth/Login.vue')
const Home = () => import('@/views/Home.vue')

const router = new Router({
  mode: 'history',
  linkActiveClass: 'is-active',
  base: baseUrl,
  routes: [
    {
      path: '/',
      name: 'home',
      meta: {title: `${VUE_APP_NAME} - Home`},
      component: Home
    },
    {
      path: '/login',
      name: 'login',
      meta: {title: `${VUE_APP_NAME} - Login`},
      component: Login
    },
  ]
})


// router.beforeEach(initCurrentUserStateMiddleware)
// router.beforeEach(checkAccessMiddleware)
router.beforeEach(setPageTitle)

export default router
