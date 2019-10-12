import { stringToArrayBuffer } from '@/utils/converters'

export const cryptoLib = window.crypto || window.msCrypto
export const cryptoApi = cryptoLib.subtle || cryptoLib.webkitSubtle
/**
 *
 * Method for getting random salt using cryptographically secure PRNG
 * @param size    {number}    default: 16 (12 for AES-GCM)
 * @return Uint8Array
 */
export function getRandomSalt(size) {
    size = (typeof size !== 'undefined') ? size : 16
    if (typeof size !== 'number') {
        throw new TypeError('Expected input of size to be a Number')
    }
    return cryptoLib.getRandomValues(new Uint8Array(size))
}

/**
 *
 * Method for generating symmetric AES 256 bit key derived from passphrase and salt
 * @param    passphrase        {String}        default: "undefined" any passphrase string
 * @param    salt              {ArrayBuffer}   default: "undefined" any salt, may be unique user ID for example
 * @param    iterations        {Number}        default: "300000" number of iterations is 300 000 (Recommended)
 * @param    hash              {String}        default: "SHA-512" hash algorithm
 * @return   {CryptoKey}
 */
export async function keyFromPassphrase(passphrase, salt, iterations, hash) {
    iterations = (typeof iterations !== 'undefined') ? iterations : 300000
    hash = (typeof hash !== 'undefined') ? hash : 'SHA-512'

    try {
        // convert passphrase string to ArrayBuffer
        let passphraseBuffer = stringToArrayBuffer(passphrase)
        // construct base CryptoKey
        const baseKey = await cryptoApi.importKey(
            'raw',
            passphraseBuffer,
            {
                name: 'PBKDF2'
            },
            false,
            ['deriveKey']
        )
        // derive a secret key for using in AES-GCM algorithm
        return await cryptoApi.deriveKey(
            {
                name: 'PBKDF2',
                salt: salt,
                iterations: iterations,
                hash: hash
            },
            baseKey,
            {
                name: 'AES-GCM',
                length: 256
            },
            false,
            ['encrypt', 'decrypt']
        )
    } catch (err) {
        throw err
    }
}

/**
 * Generates symmetric / shared key for AES encryption
 * Uses Galois/Counter Mode (GCM) for operation by default.
 * @param bits              {Integer}      default: "256" accepts 128 and 256
 * @param usage             {Array}        default: "['encrypt', 'decrypt', 'wrapKey', 'unwrapKey']"
 * @param extractable       {Boolean}      default: "false" whether the key can be exported
 * @param cipher            {String}       default: "AES-GCM" Cipher block mode operation
 * @return {CryptoKey}
 */
export async function getSharedKey(bits, usage, extractable, cipher) {
    bits = (typeof bits !== 'undefined') ? bits : 256
    usage = (typeof usage !== 'undefined') ? usage : ['encrypt', 'decrypt', 'wrapKey', 'unwrapKey']
    extractable = (typeof extractable !== 'undefined') ? extractable : true
    cipher = (typeof cipher !== 'undefined') ? cipher : 'AES-GCM'

    try {
        // generate session key
        return await cryptoApi.generateKey(
            {
                name: cipher,
                length: bits
            },
            extractable,
            usage
        )
    } catch (err) {
        throw err
    }
}

/**
 *
 * Encrypts data using symmetric / session key
 * Uses Galois/Counter Mode (GCM) for operation, it need initial vector length: 12
 * @param   sharedKey       {CryptoKey}      default: "undefined" symmetric / shared key
 * @param   data            {ArrayBuffer}    default: "undefined" any data to be encrypted
 * @param   ivAb            {ArrayBuffer}    default: "undefined" initial vector
 * @return {ArrayBuffer}
 */
export async function encrypt(sharedKey, data, ivAb) {
    try {
        // let ivAb = cryptoLib.getRandomValues(new Uint8Array(12))
        return await cryptoApi.encrypt(
            {
                name: 'AES-GCM',
                iv: ivAb,
                tagLength: 128
            },
            sharedKey,
            data
        )
    } catch (err) {
        throw err
    }
}

/**
 * Decrypts data using symmetric / session key
 * Uses Galois/Counter Mode (GCM) for operation.
 * @param   sharedKey           {CryptoKey}      default: "undefined" symmetric or session key
 * @param   encryptedAb         {ArrayBuffer}    default: "undefined" any data to be decrypted
 * @param   ivAb                {ArrayBuffer}    default: "undefined" initial vector
 * @return  {ArrayBuffer}
 */
export async function decrypt(sharedKey, encryptedAb, ivAb) {
    try {
        return await cryptoApi.decrypt(
            {
                name: 'AES-GCM',
                iv: ivAb,
                tagLength: 128
            },
            sharedKey,
            encryptedAb
        )
    } catch (err) {
        throw err
    }
}

/**
 *
 * Method for generating asymmetric Elliptic Curve Diffie-Hellman key pair
 * - curve           {String}      default: "P-256" uses P-256 curve
 * - usage           {Array}       default: "['deriveKey', 'deriveBits']" contains all available options at default
 * - type            {String}      default: "ECDH" uses Elliptic Curve Diffie-Hellman
 * - extractable     {Boolean}     default: "true" whether the key is extractable
 * @return keyPair
 */
export async function getECKeyPair(curve, usage, type, extractable) {
    curve = (typeof curve !== 'undefined') ? curve : 'P-256'
    usage = (typeof usage !== 'undefined') ? usage : ['deriveKey', 'deriveBits']
    type = (typeof type !== 'undefined') ? type : 'ECDH'
    extractable = (typeof extractable !== 'undefined') ? extractable : true

    try {
        return await cryptoApi.generateKey(
            {
                name: type,
                namedCurve: curve
            },
            extractable,
            usage
        )
    } catch (err) {
        throw err
    }
}

/**
 * Method for derive symmetric key from Elliptic Curve Diffie-Hellman shared secret key
 * @link: https://mdn.github.io/dom-examples/web-crypto/derive-key/index.html
 * @param   privateKey          {CryptoKey}      default: Alice/Bob private key
 * @param   publicKey           {CryptoKey}      default: Bob/Alice public key
 * @return  {CryptoKey}
 */
export async function deriveECSharedKey(privateKey, publicKey) {
    return await cryptoApi.deriveKey(
        {
            name: "ECDH",
            public: publicKey
        },
        privateKey,
        {
            name: "AES-GCM",
            length: 256
        },
        false,
        ["encrypt", "decrypt"]
    )
}
