import auth from '@/api/auth'

const state = {
    list: [], // list of all users
    chosen: {} // single user of choice
}

const mutations = {
    save: (state, users) => {
      state.users = users
    },
    create: (state, user) => {
      //
    },
    update: (state, user) => {

    },
    remove: (state, userId) => {

    }
}

const actions = {
  async all({commit}, filters) {

    try {
      const {data} = await auth.login(credentials)
    } catch (e) {
      // handle errors
    }
  }
}

const getters = {

}

export default {
    // namespaced: true,
    state,
    actions,
    mutations,
    getters
}