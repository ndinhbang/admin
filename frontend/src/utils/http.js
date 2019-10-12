import axios from 'axios'
import {authInterceptor} from '@/utils/interceptors'
import {APP_URL} from '../.env'
import {Message} from 'element-ui'

const config = {
    baseURL: APP_URL,
    timeout: 5000
}

const http = axios.create(config)

/** Adding the request interceptors */
http.interceptors.request.use(authInterceptor)

/** Adding the response interceptors */
http.interceptors.response.use(
    response => {
        return response;
    },
    error => {
        Message({
            message: error.message,
            type: 'error',
            duration: 5 * 1000
        })
        /** Do something with response error */
        return Promise.reject(error);
    }
)

export default http