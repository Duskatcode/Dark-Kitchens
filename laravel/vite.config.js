import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.css',
                'resources/css/pages/login.css',
                'resources/css/pages/register.css',
                'resources/css/pages/home.css',
                'resources/css/pages/dashboard.css',
                'resources/css/pages/admin-users.css',
                'resources/css/pages/admin-orders.css',
                'resources/css/pages/admin-roles.css',
                'resources/css/pages/client-menu.css',
                'resources/css/pages/cook-orders.css',
                'resources/js/app.js',
            ],
            refresh: true,
        }),
    ],
    server: {
        watch: {
            ignored: ['**/storage/framework/views/**'],
        },
    },
});
