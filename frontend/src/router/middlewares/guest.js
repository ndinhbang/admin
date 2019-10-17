export function guest({to, next, store}) {
    if (store.getters['auth/currentUser'] && to.name === 'login') {
        return next({name: 'home'})
    }
    return next()
}