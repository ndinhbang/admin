import Vue from 'vue'
import { logger } from '@/utils/logger'

// @link: https://medium.com/js-dojo/error-exception-handling-in-vue-js-application-6c26eeb6b3e4
Vue.config.errorHandler = (err, vm, info) => {
    logger.logToServer({ err, vm, info })
}

window.onerror = function(message, source, lineno, colno, error) {
    logger.logToServer({ message, source, lineno, colno, error });
}