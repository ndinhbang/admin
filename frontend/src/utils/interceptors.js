export function authInterceptor(config) {
    /** add auth token */
    return config;
}

export function loggerInterceptor(config){
    /** Add logging here */
    return config;
}