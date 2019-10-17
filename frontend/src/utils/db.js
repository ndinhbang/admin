import Dexie from 'dexie'
import {decrypt, encrypt, generateCryptoKey} from "../utils/webCrypto"

/**
 * @link https://medium.com/@ole.ersoy/having-fun-with-dexiejs-and-typescript-1c52514a090
 */
export class AppIdxDb extends Dexie {
    constructor() {
        super("idxDb")
        // const db = this
        // Define tables and indexes
        this.version(1).stores({
            meta: '++id, crtk',
            vaults: '++id, kname, _encv'
        })
    }

    /**
     * @param   {Uint8Array}    cryptoKey
     * @returns {Promise<boolean| Uint8Array>}
     */
    async setCryptoKey(cryptoKey) {
        if (typeof cryptoKey === 'undefined' || !(cryptoKey instanceof Uint8Array)) {
            throw new TypeError('Expected cryptoKey to be Uint8Array')
        }
        // await this.open()
        return await this.meta.add({crtk: cryptoKey})
    }

    /**
     * @returns {Promise<Uint8Array | null>}
     */
    async getCryptoKey() {
        const record = await db.meta.toCollection().last()
        if (typeof record === 'undefined') {
            return null
        }
        return record.crtk
    }

    /**
     * @param   {Uint8Array}    cryptoKey
     * @param   {Object}        obj
     * @returns {Promise<*>}
     */
    async setToken(cryptoKey, obj) {
        if (typeof cryptoKey === 'undefined' || !(cryptoKey instanceof Uint8Array)) {
            throw new TypeError('cryptoKey is required and expected to be Uint8Array')
        }
        const secretKey = cryptoKey.slice(12, cryptoKey.length)
        obj._encv = encrypt(secretKey, obj.v)
        obj.v = 0
        // await this.open()
        return await this.vaults.add(obj)
    }

    /**
     *
     * @param   {Uint8Array}    cryptoKey
     * @param   {String}        tokenType    default: "access_token"
     * @returns {Promise<T>|null}
     */
    async getToken(cryptoKey, tokenType) {
        tokenType = (typeof tokenType !== 'undefined') ? tokenType : 'access_token'
        if (typeof cryptoKey === 'undefined' || !(cryptoKey instanceof Uint8Array)) {
            throw new TypeError('cryptoKey is required and expected to be Uint8Array')
        }
        const secretKey = cryptoKey.slice(12, cryptoKey.length)
        const obj = await this.vaults.where('kname').equals(tokenType).last()
        if (typeof obj === 'undefined') {
            return null
        }
        return decrypt(secretKey, obj._encv)
    }

    /**
     * Save tokens to indexedDb
     */
    async saveAuthTokens(data) {
        await db.clearAuthTokens()
        return db.transaction('rw', db.vaults, db.meta, function () {
            const cryptoKey = generateCryptoKey();
            // parallel runing
            return Promise.all([
                // set cryptoKey to indexedDb
                db.setCryptoKey(cryptoKey),
                // save tokens to vaults
                db.setToken(cryptoKey, {kname: 'access_token', v: data['access_token']}),
                db.setToken(cryptoKey, {kname: 'refresh_token', v: data['refresh_token']})
            ])
        })
    }

    clearAuthTokens() {
        return db.transaction('rw', db.vaults, db.meta, function () {
            return Promise.all([
                db.meta.clear(),
                db.vaults.clear()
            ])
        })
    }
}

// singleton
const db = new AppIdxDb()
db.open()
Object.freeze(db)
export default db