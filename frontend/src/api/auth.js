import http from '../utils/http'
// import db from '../../utils/db'

export default {
  login(data) {
    return http.post(`login`, data)
  },
  logout() {
    return http.post(`logout`);
  },
  getCurrentUser() {
    return http.get('user')
  },
}