import axios from 'axios'
import {authInterceptor, httpErrorHandler} from '../utils/interceptors'
// import {Message} from 'element-ui'
import {VUE_API_URL} from '@/environment/index'
// import logger from "@/utils/logger"

const config = {
  baseURL: VUE_API_URL,
  timeout: 5000
}

const http = axios.create(config)

/** Adding the request interceptors */
http.interceptors.request.use(authInterceptor,
  (error) => {
    return Promise.reject(error)
})

/** Adding the response interceptors */
http.interceptors.response.use(
  function (response) {
    return response
  },
  httpErrorHandler
)

export default http