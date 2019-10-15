import {Message} from 'element-ui'
import {logger} from '@/utils/logger'

/**
 * Display error message to client
 */
export function showMsg (msg, msgType = 'error') {
    Message({message: msg, type: msgType, duration: 5 * 1000, showClose: true})
}

/**
 * Handle unexpected exceptions
 */
export function handleException(errMsg, errData) {
    logger.report(errData)
    showMsg(errMsg)
}