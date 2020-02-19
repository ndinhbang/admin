require('dotenv').config();

const env = process.env;

require('laravel-echo-server').run({
        authHost: env.LARAVEL_ECHO_SERVER_AUTH_HOST,
        authEndpoint: '/broadcasting/auth',
        clients: [
            {
                appId: env.LARAVEL_ECHO_SERVER_APP_ID,
                key: env.LARAVEL_ECHO_SERVER_APP_KEY
            }
        ],
        database: 'redis',
        databaseConfig: {
            redis: {
                host: env.LARAVEL_ECHO_SERVER_REDIS_HOST,
                port: env.LARAVEL_ECHO_SERVER_REDIS_PORT,
                db: env.LARAVEL_ECHO_SERVER_REDIS_DB,
                keyPrefix: env.LARAVEL_ECHO_SERVER_REDIS_KEY_PREFIX
            },
            sqlite: {
                databasePath: '/database/laravel-echo-server.sqlite'
            }
        },
        devMode: env.LARAVEL_ECHO_SERVER_DEBUG,
        host: env.LARAVEL_ECHO_SERVER_HOST,
        port: env.LARAVEL_ECHO_SERVER_PORT,
        protocol: env.LARAVEL_ECHO_SERVER_PROTO,
        socketio: {},
        secureOptions: 67108864,
        sslCertPath: env.LARAVEL_ECHO_SERVER_SSL_CERT,
        sslKeyPath: env.LARAVEL_ECHO_SERVER_SSL_KEY,
        sslCertChainPath: env.LARAVEL_ECHO_SERVER_SSL_CHAIN,
        sslPassphrase: env.LARAVEL_ECHO_SERVER_SSL_PASS,
        subscribers: {
            http: true,
            redis: true
        },
        apiOriginAllow: {
            allowCors: true,
            allowOrigin: env.APP_URL,
            allowMethods: 'GET, POST',
            allowHeaders: 'Origin, Content-Type, X-Auth-Token, X-Requested-With, Accept, Authorization, X-CSRF-TOKEN, X-Socket-Id'
        }
    }
);
