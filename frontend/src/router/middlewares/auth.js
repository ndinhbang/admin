export function auth({to, next, store}) {
    const currentUser = store.getters['auth/currentUser'];
    const isAuthRoute = to.matched.some(item => item.meta.requireAuth);

    if (currentUser && (to.name === 'login' || to.name === 'register')) {
        return next({ name: 'home'})
    }
    if (isAuthRoute && currentUser) return next()
    if (isAuthRoute) return next({name: 'login'})
    return next(false)
}