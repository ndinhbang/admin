import axios from 'axios'
import {authInterceptor} from '@/utils/interceptors'
import {Message} from 'element-ui'
import {VUE_API_URL} from '@/environment/index'

console.log(VUE_API_URL)

const config = {
    baseURL: VUE_API_URL,
    timeout: 5000
}

const http = axios.create(config)

/** Adding the request interceptors */
http.interceptors.request.use(authInterceptor)

/** Adding the response interceptors */
http.interceptors.response.use(
    function (response) {
        return response
    },
    function (error) {
        Message({
            message: error.message,
            type: 'error',
            duration: 5 * 1000,
            showClose: true
        })
        /** Do something with response error */
        return Promise.reject(error);
    }
)

export default http