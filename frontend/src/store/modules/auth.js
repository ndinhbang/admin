import auth from '@/api/auth'

const state = {
  isAuthenticated: false,
  user: {}
}

const mutations = {
  login: (state) => {
    state.isAuthenticated = true
  },
  logout: (state) => {
    state.isAuthenticated = false
  }
}

const actions = {
  async login({commit}, credentials) {
    try {
      let response = await auth.login(credentials)
      const {data} = response
      commit('login')
      console.log(response)
    } catch (e) {
      // custom handle errors
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