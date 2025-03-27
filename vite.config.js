import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.css',
                'resources/js/app.js',
            ],
            refresh: true,
        }),
    ],
    server: {
        host: '0.0.0.0', // Permite acceso externo (cambia si es necesario)
        port: 5173, // Puerto por defecto de Vite
        strictPort: true, // No cambia el puerto automáticamente si está en uso
        hmr: {
            host: '164.92.78.65', // Cambia esto a tu IP pública o dominio
        },
    },
});

