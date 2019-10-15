import Vue from 'vue'
import {handleException} from "./helpers";

// @link: https://medium.com/js-dojo/error-exception-handling-in-vue-js-application-6c26eeb6b3e4
Vue.config.errorHandler = (err, vm, info) => {
    handleException(err, {err, vm, info})
}
/**
 * Global errors
 */
// window.onerror = function (message, source, lineno, colno, error) {
//     handleException(message, {message, source, lineno, colno, error})
// }

// Chrome & Safari
// TODO: need polyfill for Firefox
// window.addEventListener("unhandledrejection", function (e) {
//     e.preventDefault() // stop print error to console
//     handleException(e.reason, e)
// })