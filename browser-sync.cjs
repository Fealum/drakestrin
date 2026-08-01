module.exports = {
    proxy: {
        target: process.env.BROWSERSYNC_PROXY || 'http://127.0.0.1',
        proxyReq: [
            function preserveBrowserHost(proxyReq, req) {
                proxyReq.setHeader('host', req.headers.host);
            },
        ],
    },
    host: '0.0.0.0',
    port: 3000,
    ui: false,
    open: false,
    notify: false,
    ghostMode: false,
    reloadDebounce: 150,
    files: [
        'resources/views/**/*.blade.php',
        'app/View/Components/**/*.php',
        'routes/**/*.php',
        'public/css/**/*.css',
        'public/js/**/*.js',
    ],
};
