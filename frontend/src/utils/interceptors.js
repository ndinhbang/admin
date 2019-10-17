import axios from 'axios'
import db from '../utils/db'
import {Message} from "element-ui"
import logger from '../utils/logger'
import {VUE_API_URL} from '../environment/index'
import store from '../store/index'
import router from '../router/index'

export async function authInterceptor(config) {
    const cryptoKey = await db.getCryptoKey();
    const authToken = cryptoKey ? await db.getToken(cryptoKey, 'access_token') : null;
    /** add auth token to header **/
    if (authToken) {
        config.headers.Authorization = `Bearer ${authToken}`;
    }

    // if (config.url.includes('/api/logout')) // do smth
    return config
}


export function loggerInterceptor(config) {
    /** Add logging here **/
    return config;
}

export async function httpErrorHandler(error) {
    // let errorMsg = error.message
    if (error.response) {

        /** The request was made and the server responded with a status code **/
            // that falls out of the range of 2xx
        const errorResponse = error.response

        // Form validation errors
        if (isFormValidationError(errorResponse)) {
            return handleFormValidationError(errorResponse)
        }
        // Token expired
        if (isTokenExpiredError(errorResponse)) {
            try {
                return await resetTokenAndReattemptRequest(error)
            } catch (err) {
                return await logoutAndRedirect()
            }
        }

        if (errorResponse.status === 401) {
            return await logoutAndRedirect()
        }

    } else if (error.request) {
        /** The request was made but no response was received **/
        // `error.request` is an instance of XMLHttpRequest in the browser
        await logger.report(error.request)
    } else {
        /** Something happened in setting up the request that triggered an Error **/
        await logger.report('Error', error.message)
    }


    /** Do something with response error */
    console.log(error)
    // return Promise.reject(error)
}

// private functions
function isFormValidationError(errorResponse) {
    return errorResponse.status === 422
}

function handleFormValidationError(errorResponse) {
    let errorMsg = 'Whoops, something went wrong!'
    if (errorResponse.hasOwnProperty('errors')) {
        const obj = errorResponse.errors
        // get the first error message
        errorMsg = obj[Object.keys(obj)[0]]
    } else if (errorResponse.hasOwnProperty('message')) {
        errorMsg = errorResponse.message
    }

    Message({
        message: errorMsg,
        type: 'error',
        duration: 5 * 1000,
        showClose: true
    })
}

/**
 * @link https://www.techynovice.com/setting-up-JWT-token-refresh-mechanism-with-axios/
 * @param errorResponse
 * @returns {boolean}
 */

let isRefreshingToken = false
let subscribers = []

function isTokenExpiredError(errorResponse) {
    return errorResponse.status === 401
        && store.getters['auth/tokenExpiredAt'] < (Date.now().valueOf() / 1000)
}

async function resetTokenAndReattemptRequest(error) {
    const {response: errorResponse} = error;

    const retryOriginalRequest = new Promise(resolve => {
        addSubscriber(access_token => {
            errorResponse.config.headers.Authorization = `Bearer ${access_token}`;
            resolve(axios(errorResponse.config));
        });
    });

    if (!isRefreshingToken) {
        isRefreshingToken = true;
        try {
            // Get the refresh token to refresh the JWT token
            const cryptoKey = await db.getCryptoKey();
            const resetToken = cryptoKey ? await db.getToken(cryptoKey, 'refresh_token') : null;
            // Refresing ...
            const response = await axios({
                method: 'post',
                url: `${VUE_API_URL}/refresh-token`,
                data: {
                    refresh_token: resetToken
                }
            });

            const newToken = response.data['access_token'];
            await db.saveAuthTokens(response.data);
            // Done ...
            isRefreshingToken = false;
            onAccessTokenFetched(newToken);
        } catch (err) {
            logger.log('Refresh token has expired')
            throw err
        }
    }
    return retryOriginalRequest;
}

function onAccessTokenFetched(access_token) {
    // When the refresh is successful, we start retrying the requests one by one and empty the queue
    subscribers.forEach(callback => callback(access_token));
    subscribers = [];
}

function addSubscriber(callback) {
    subscribers.push(callback);
}

async function logoutAndRedirect() {
    await db.clearAuthTokens()
    return await router.push({name: 'login'})
}