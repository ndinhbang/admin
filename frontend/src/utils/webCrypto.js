import nacl from 'tweetnacl'
import {arrayBufferToString, stringToArrayBuffer} from '../utils/converters'

const cryptoLib = window.crypto || window.msCrypto
// const cryptoApi = cryptoLib.subtle || cryptoLib.webkitSubtle

/**
 *
 * Method for getting random salt using cryptographically secure PRNG
 * @param size    {number}    default: 16
 * @return Uint8Array
 */
export function getRandomValues(size) {
    size = (typeof size !== 'undefined') ? size : 16
    if (typeof size !== 'number') {
        throw new TypeError('Expected input of size to be a Number')
    }
    return cryptoLib.getRandomValues(new Uint8Array(size))
}

/**
 * Generate secret-key used to encrypt/decrypt string
 * @returns {Uint8Array}
 */
export function generateCryptoKey() {
    const salt = getRandomValues(12) // Uint8Array
    const secretKey = getRandomValues(32) // Uint8Array
    const combined = new Uint8Array(salt.length + secretKey.length)
    combined.set(salt)
    combined.set(secretKey, salt.length)
    return combined
}

/**
 * Secret-key authenticated encryption
 * Implements xsalsa20-poly1305
 * @param   {Uint8Array} sharedKey
 * @param   {string}    stringified  default: "undefined", utf-8 string
 * @returns {Uint8Array}
 */
export function encrypt(sharedKey, stringified) {
    const nonce = getRandomValues(nacl.secretbox.nonceLength)
    const encoded = stringToArrayBuffer(stringified)
    const encrypted = nacl.secretbox(encoded, nonce, sharedKey)
    // concat buffer
    const encv = new Uint8Array(nonce.length + encrypted.length)
    encv.set(nonce)
    encv.set(encrypted, nonce.length)
    return encv
}

/**
 * @param {Uint8Array} sharedKey
 * @param {Uint8Array} encv
 * @return {String}
 */
export function decrypt(sharedKey, encv) {
    // get real encrypted messages
    const nonce = encv.slice(0, nacl.secretbox.nonceLength)
    const encrypted = encv.slice(
        nacl.secretbox.nonceLength,
        encv.length
    )
    const abv = nacl.secretbox.open(encrypted, nonce, sharedKey)

    return arrayBufferToString(abv)
}