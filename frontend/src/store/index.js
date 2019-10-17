import Vue from 'vue'
import Vuex from 'vuex'
import VuexPersistence from 'vuex-persist'
// import localforage from 'localforage'
import modules from './modules'

Vue.use(Vuex)

const vuexStorage = new VuexPersistence({
    key: 'localDb',
    storage: window.localStorage,
    // You can change this explicitly use
    // either window.localStorage  or window.sessionStorage
    // However we are going to make use of localForage
})

// console.log(process.env.NODE_ENV)

const store = new Vuex.Store({
    modules,
    plugins: [vuexStorage.plugin],
    strict: process.env.NODE_ENV !== 'production'
})

export default store
