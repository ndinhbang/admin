import jwtDecode from 'jwt-decode'
import db from '../../utils/db'

/** ============ STATE =========**/
const state = {
    // isAuthenticated: false,
    tokenExpiredAt: 0,
    currentUser: null
}

/** ============ GETTERS =========**/
const getters = {
    // isAuthenticated: state => state.isAuthenticated,
    tokenExpiredAt: state => state.tokenExpiredAt,
    currentUser: state => {
        return state.currentUser
    }
}

/** ============ MUTATIONS =========**/
const mutations = {
    login: (state, expiredAt) => {
        // state.isAuthenticated = true
        state.tokenExpiredAt = expiredAt
    },
    logout: (state) => {
        // state.isAuthenticated = false
        state.currentUser = null
        state.tokenExpiredAt = 0
    },
    currentUser: (state, currentUser) => {
        state.currentUser = currentUser
    }
}

/** ============ ACTIONS =========**/
const actions = {
    async login({commit, dispatch}, data) {
        try {
            // const {data} = await Auth.login(credentials)
            await db.saveAuthTokens(data)
            const {expiredAt} = jwtDecode(data['access_token'])
            commit('login', expiredAt)
        } catch (err) {
            // handleException(err)
            console.log(err)
        }
    },

    async logout({commit}) {
        await db.clearAuthTokens()
        commit('logout')
    },

    setCurrentUser({commit}, userInfo) {
        commit('currentUser', userInfo);
    }
}

export default {
    // namespaced: true,
    state,
    actions,
    mutations,
    getters
}