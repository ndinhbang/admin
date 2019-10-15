import store from '@/store/index'
// import * as authService from '../services/auth.service'

/**
 * Current user state initialization
 * @WARN Must be always first in middleware chain
 */
export function initCurrentUserStateMiddleware(to, from, next) {
    // const currentUserId = $store.state.user.currentUser.id
    //
    // if (authService.getRefreshToken() && !currentUserId) {
    //     return authService.refreshTokens()
    //         .then(() => $store.dispatch('user/getCurrent'))
    //         .then(() => next())
    //         .catch(error => console.log(error))
    // }
    next()
}

/**
 * Check access permission to auth routes
 */
export function checkAccessMiddleware(to, from, next) {
    const isAuthenticated = store.getters['auth/isAuthenticated']
    const isAuthRoute = to.matched.some(item => item.meta.requireAuth)

    console.log(isAuthenticated, isAuthRoute)

    if (isAuthRoute && isAuthenticated) return next()
    if (isAuthRoute) return next({name: 'login'})
    next()
}

/**
 * Set document meta title
 */
export function setMetadataMiddleware(to, from, next) {
    const title = to.matched.find(item => item.meta.title)
    if (title) window.document.title = title.meta.title
    next()
}
