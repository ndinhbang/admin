import Vue from 'vue'
import Router from 'vue-router'
import {APP_NAME} from '@/.env'
import { setPageTitle } from '@/router/middlewares'
import Home from '@/views/Home.vue'


Vue.use(Router)

// Route-level code splitting
const LoginView = () => import(/* webpackChunkName: "login.view" */'@/views/auth/Login.vue')


const router = new Router({
    mode: 'history',
    linkActiveClass: 'is-active',
    base: process.env.BASE_URL,
    routes: [
        {
            path: '/',
            name: 'home',
            meta: { title: `${APP_NAME} - Home` },
            component: Home
        },
        {
            path: '/login',
            name: 'login',
            meta: { title: `${APP_NAME} - Login` },
            component: LoginView
        },
    ]
})


// router.beforeEach(initCurrentUserStateMiddleware)
// router.beforeEach(checkAccessMiddleware)
router.beforeEach(setPageTitle)

export default router
