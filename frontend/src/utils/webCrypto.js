import nacl from 'tweetnacl'
import {arrayBufferToString, stringToArrayBuffer} from '@/utils/converters'

const cryptoLib = window.crypto || window.msCrypto
const cryptoApi = cryptoLib.subtle || cryptoLib.webkitSubtle

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
 *
 * Method to derive bit from passphrase and salt
 * @param    passphrase        {String}        default: "undefined" any passphrase string
 * @param    iterations        {Number}        default: "300000" number of iterations is 300 000 (Recommended)
 * @param    hash              {String}        default: "SHA-512" hash algorithm
 * @return   {Uint8Array}
 */
export async function keyFromPassphrase(passphrase, iterations, hash) {
    try {
        iterations = (typeof iterations !== 'undefined') ? iterations : 300000
        hash = (typeof hash !== 'undefined') ? hash : 'SHA-512'

        // Uint8Array
        const salt = getRandomValues(32)
        // convert passphrase string to ArrayBuffer
        const passphraseBuffer = stringToArrayBuffer(passphrase)
        // construct base CryptoKey
        const baseKey = await cryptoApi.importKey(
            'raw',
            passphraseBuffer,
            {
                name: 'PBKDF2'
            },
            false,
            ['deriveBits']
        )
        const derivedBits = await cryptoApi.deriveBits(
            {
                name: 'PBKDF2',
                salt: salt,
                iterations: iterations,
                hash: hash
            },
            baseKey,
            256
        )
        // concat 2 buffer
        const derived = new Uint8Array(derivedBits)
        const combined = new Uint8Array(salt.length + derived.length)
        combined.set(salt)
        combined.set(derived, salt.length)

        return combined
    } catch (err) {
        throw err
    }
}

/**
 * Secret-key authenticated encryption
 * Implements xsalsa20-poly1305
 * @param   {Uint8Array} sharedKey   default: "undefined", salt(32 bytes) + cryptoKey
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
 *
 * @param {Uint8Array} sharedKey default: "undefined", salt(32 bytes) + cryptoKey
 * @param {Uint8Array} encv
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