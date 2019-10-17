import Vue from 'vue'
import Router from 'vue-router'
import {VUE_APP_NAME} from '../environment/index'
import Home from '../views/Home.vue'
import Login from '../views/auth/Login'
import {auth, metadata, guest} from './middlewares/index'
// import auth from './middlewares/auth'
// import metadata from './middlewares/metadata'
import middlewarePipeline from './middlewarePipeline'
import store from '../store/index'

Vue.use(Router)

// const baseUrl = VUE_APP_ENV !== 'development' ? VUE_APP_URL : VUE_CLI_BASE_URL

const router = new Router({
    mode: 'history',
    scrollBehavior: () => ({y: 0}),
    // linkActiveClass: 'is-active',
    // base: baseUrl,
    routes: [
        {
            path: '/',
            name: 'home',
            meta: {
                middlewares: [auth, metadata],
                requireAuth: true,
                title: `${VUE_APP_NAME} - Home`,
            },
            component: Home,
        },
        {
            path: '/404',
            name: '404',
            meta: {
                requireAuth: false,
                title: `${VUE_APP_NAME} - 404`
            },
            component: () => import('../views/error/404'),
        },
        {
            path: '/login',
            name: 'login',
            meta: {
                middlewares: [guest, metadata],
                requireAuth: false,
                title: `${VUE_APP_NAME} - Login`
            },
            component: Login
        },
        // page not found, must be placed at the end !!!
        {path: '*', redirect: '/404'}
    ]
})

/**
 * @link https://blog.logrocket.com/vue-middleware-pipelines/
 */
router.beforeEach((to, from, next) => {
    if (!to.meta.middlewares || !to.meta.middlewares.length) {
        return next()
    }
    let middlewares = to.meta.middlewares.reverse()
    let context = {to, from, next, store}

    return middlewares[0]({
        ...context,
        next: middlewarePipeline(context, middlewares, 1)
    })
})


export default router
