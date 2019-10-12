import http from '@/utils/http'

export default {
    login(data) {
        return http.post(`login`, data);
    },
    logout() {
        return http.post(`logout`);
    }
}