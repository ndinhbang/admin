require('dotenv').config();

const env = process.env;

process.on('uncaughtException', function (err) {
    console.error(err.stack);
    console.log("Node NOT Exiting...");
});
// another solution: https://nodejs.org/api/cli.html#cli_abort_on_uncaught_exception

require('laravel-echo-server').run({
        authHost: env.SOCKET_AUTH_HOST,
        authEndpoint: env.SOCKET_AUTH_ENDPOINT,
        clients: [
            {
                appId: env.SOCKET_CLIENT_ID,
                key: env.SOCKET_CLIENT_KEY
            }
        ],
        database: 'redis',
        databaseConfig: {
            redis: {
                host: env.REDIS_HOST,
                port: env.REDIS_PORT,
                db: env.REDIS_DB,
                keyPrefix: env.REDIS_PREFIX
            },
            sqlite: {
                databasePath: '/database/laravel-echo-server.sqlite'
            }
        },
        devMode: env.SOCKET_DEBUG,
        host: env.SOCKET_HOST,
        port: env.SOCKET_PORT,
        protocol: env.SOCKET_PROTOCOL,
        socketio: {},
        secureOptions: 67108864,
        sslCertPath: env.SOCKET_SSL_CERT_PATH,
        sslKeyPath: env.SOCKET_SSL_KEY_PATH,
        sslCertChainPath: env.SOCKET_SSL_CHAIN_PATH,
        sslPassphrase: env.SOCKET_SSL_PASSPHRASE,
        subscribers: {
            http: true,
            redis: true
        },
        apiOriginAllow: {
            allowCors: env.SOCKET_ALLOW_CORS,
            allowOrigin: env.SOCKET_ALLOW_ORIGIN,
            allowMethods: env.SOCKET_ALLOW_METHODS,
            allowHeaders: env.SOCKET_ALLOW_HEADERS
        }
    }
);
