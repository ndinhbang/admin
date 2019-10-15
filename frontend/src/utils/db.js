import Dexie from 'dexie'
import {encrypt, decrypt} from "@/utils/webCrypto"

export class AppIdxDb extends Dexie {
    constructor() {
        super("idxDb")
        // const db = this
        //
        // Define tables and indexes
        //
        this.version(1).stores({
            meta: '++id, crtk',
            vaults: '++id, kname, _encv'
        })

        // Let's physically map Contact class to contacts table.
        // This will make it possible to call loadEmailsAndPhones()
        // directly on retrieved database objects.
        // db.contacts.mapToClass(Contact);
    }

    /**
     * @param   {Uint8Array}    cryptoKey
     * @returns {Promise<boolean| Uint8Array>}
     */
    async setCryptoKey(cryptoKey) {
        if (typeof cryptoKey === 'undefined' || !cryptoKey instanceof Uint8Array) {
            throw new TypeError('Expected cryptoKey to be Uint8Array')
        }
        await this.open()
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
        if (typeof cryptoKey === 'undefined' || !cryptoKey instanceof Uint8Array) {
            throw new TypeError('cryptoKey is required and expected to be Uint8Array')
        }
        const secretKey = cryptoKey.slice(32, cryptoKey.length)
        obj._encv = encrypt(secretKey, obj.v)
        obj.v = 0
        await this.open()
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
        if (typeof cryptoKey === 'undefined' || !cryptoKey instanceof Uint8Array) {
            throw new TypeError('cryptoKey is required and expected to be Uint8Array')
        }
        const secretKey = cryptoKey.slice(32, cryptoKey.length)
        const obj = await this.vaults.where('kname').equals(tokenType).last()
        if (typeof obj === 'undefined') {
            return null
        }
        obj.v = decrypt(secretKey, obj._encv)
        return obj
    }
}

// singleton
const db = new AppIdxDb()

Object.freeze(db)
export default db