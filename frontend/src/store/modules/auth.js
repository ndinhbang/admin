import Auth from '@/api/auth'
import db from '@/utils/db'
import {keyFromPassphrase} from '@/utils/webCrypto'

const state = {
    isAuthenticated: false,
    user: {}
}

const mutations = {
    login: (state, loginStatus) => {
        state.isAuthenticated = loginStatus
    },
    logout: (state, {}) => {
        state.isAuthenticated = false
    }
}

const actions = {
    async login({commit}, credentials) {
        const {data} = await Auth.login(credentials)
        try {
            // derive crypto key from passphrase for encrypt/decrypt data later
            const cryptoKey = await keyFromPassphrase(credentials.password)
            await db.transaction('rw', db.vaults, db.meta, function () {
                // parallel runing
                return Promise.all([
                    // set cryptoKey to indexedDb
                    db.setCryptoKey(cryptoKey),
                    // save tokens to vaults
                    db.setToken(cryptoKey, {kname: 'access_token', v: data['access_token']}),
                    db.setToken(cryptoKey, {kname: 'refresh_token', v: data['refresh_token']})
                ])
            })
            commit('login', true)
        } catch (err) {
            // handleException(err)
            console.log(err)
        }
    },

    async logout() {

    }
}

const getters = {
    isAuthenticated: state => !!state.isAuthenticated
}

export default {
    // namespaced: true,
    state,
    actions,
    mutations,
    getters
}