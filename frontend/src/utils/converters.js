import {TextDecoder, TextEncoder} from 'text-encoding-shim'

const chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/'
const lookup = new Uint8Array(256)
for (let i = 0; i < chars.length; i++) {
    lookup[chars.charCodeAt(i)] = i
}

/**
 * Encodes ArrayBuffer into base64 string
 * @link https://github.com/niklasvh/base64-arraybuffer
 * @param {ArrayBuffer} arrayBuffer
 * @return {string}
 */
function encodeAb(arrayBuffer) {
    let bytes = new Uint8Array(arrayBuffer)
    let len = bytes.length
    let base64 = ''

    for (let i = 0; i < len; i += 3) {
        base64 += chars[bytes[i] >> 2]
        base64 += chars[((bytes[i] & 3) << 4) | (bytes[i + 1] >> 4)]
        base64 += chars[((bytes[i + 1] & 15) << 2) | (bytes[i + 2] >> 6)]
        base64 += chars[bytes[i + 2] & 63]
    }

    if ((len % 3) === 2) {
        base64 = base64.substring(0, base64.length - 1) + '='
    } else if (len % 3 === 1) {
        base64 = base64.substring(0, base64.length - 2) + '=='
    }

    return base64
}

export function arrayBufferToBase64(arrayBuffer) {
    if (typeof arrayBuffer !== 'object') {
        throw new TypeError('Expected input to be an ArrayBuffer Object')
    }
    return encodeAb(arrayBuffer)
}

/**
 * Decodes base64 string to ArrayBuffer
 * @link https://github.com/niklasvh/base64-arraybuffer
 * @param {string} base64
 * @return {ArrayBuffer}
 */
function decodeAb(base64) {
    let bufferLength = base64.length * 0.75
    let len = base64.length
    let p = 0
    let encoded1
    let encoded2
    let encoded3
    let encoded4

    if (base64[base64.length - 1] === '=') {
        bufferLength--
        if (base64[base64.length - 2] === '=') {
            bufferLength--
        }
    }

    let arrayBuffer = new ArrayBuffer(bufferLength)
    let bytes = new Uint8Array(arrayBuffer)

    for (let i = 0; i < len; i += 4) {
        encoded1 = lookup[base64.charCodeAt(i)]
        encoded2 = lookup[base64.charCodeAt(i + 1)]
        encoded3 = lookup[base64.charCodeAt(i + 2)]
        encoded4 = lookup[base64.charCodeAt(i + 3)]

        bytes[p++] = (encoded1 << 2) | (encoded2 >> 4)
        bytes[p++] = ((encoded2 & 15) << 4) | (encoded3 >> 2)
        bytes[p++] = ((encoded3 & 3) << 6) | (encoded4 & 63)
    }

    return arrayBuffer
}

export function base64ToArrayBuffer(b64) {
    if (typeof b64 !== 'string') {
        throw new TypeError('Expected input to be a base64 String')
    }
    return decodeAb(b64)
}

/**
 * Decode ArrayBuffer to utf-8 string
 * @param {TypedArray} arrayBuffer
 * @return {string}
 */
export function arrayBufferToString(arrayBuffer) {
    if (typeof arrayBuffer !== 'object') {
        throw new TypeError('Expected input to be an ArrayBuffer Object')
    }

    let decoder = new TextDecoder('utf-8') // eslint-disable-line
    return decoder.decode(arrayBuffer)
}

/**
 * Encode utf-8 string into ArrayBuffer
 * @param {string} str
 * @return {Uint8Array}
 */
export function stringToArrayBuffer(str) {
    if (typeof str !== 'string') {
        throw new TypeError('Expected input to be a String')
    }

    let encoder = new TextEncoder('utf-8') // eslint-disable-line
    return encoder.encode(str)
}

/**
 * Decode ArrayBuffer to hex string
 * @param {ArrayBuffer} arrayBuffer
 * @return {string}
 */
export function arrayBufferToHexString(arrayBuffer) {
    if (typeof arrayBuffer !== 'object') {
        throw new TypeError('Expected input to be an ArrayBuffer Object')
    }

    let byteArray = new Uint8Array(arrayBuffer)
    let hexString = ''
    let nextHexByte

    for (let i = 0; i < byteArray.byteLength; i++) {
        nextHexByte = byteArray[i].toString(16)

        if (nextHexByte.length < 2) {
            nextHexByte = '0' + nextHexByte
        }

        hexString += nextHexByte
    }

    return hexString
}

/**
 * Encode hex string to ArrayBuffer
 * @param {string} hexString
 * @return {ArrayBuffer}
 */
export function hexStringToArrayBuffer(hexString) {
    if (typeof hexString !== 'string') {
        throw new TypeError('Expected input of hexString to be a String')
    }

    if ((hexString.length % 2) !== 0) {
        throw new RangeError('Expected string to be an even number of characters')
    }

    let byteArray = new Uint8Array(hexString.length / 2)
    for (let i = 0; i < hexString.length; i += 2) {
        byteArray[i / 2] = parseInt(hexString.substring(i, i + 2), 16)
    }

    return byteArray.buffer
}

/**
 * Encode hex string to ArrayBuffer
 * @param {number|string} d
 * @param {boolean} unsigned | default: false
 * @return {ArrayBuffer}
 */
export function decimalToHex(d, unsigned = false) {
    unsigned = (typeof unsigned !== 'undefined') ? unsigned : false

    let h = null
    if (typeof d === 'number') {
        if (unsigned) {
            h = (d).toString(16)
            return h.length % 2 ? '000' + h : '00' + h
        } else {
            h = (d).toString(16)
            return h.length % 2 ? '0' + h : h
        }
    } else if (typeof d === 'string') {
        h = (d.length / 2).toString(16)
        return h.length % 2 ? '0' + h : h
    }
}
