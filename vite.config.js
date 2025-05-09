import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import vue from '@vitejs/plugin-vue';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/js/app.js'],
            refresh: true,
        }),
        vue({
            template: {
                transformAssetUrls: {
                    base: '/api-productes/public',
                },
            },
        }),
    ],
    server: {
        proxy: {
            '/api': {
                target: 'http://localhost/api-productes/public',
                changeOrigin: true,
                rewrite: (path) => path.replace(/^\/api/, '/api'),
                secure: false,
            },
        },
    },
    resolve: {
        alias: {
            '@': '/resources/js',
        },
    },
});