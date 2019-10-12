import http from '@/utils/http'

export default {
  all(filters) {
    return http.get(`users`, filters);
  },
  find(userId) {
    return http.get(`users/${userId}`);
  },
  create(user) {
    return http.post(`users`, user)
  },
  update(user) {
    return http.post(`users/${user.id}`, Object.assign({}, user, {_method: 'put'}))
  },
  delete(userId) {
    return http.delete(`users/${userId}`, {_method: 'delete'})
  },

}