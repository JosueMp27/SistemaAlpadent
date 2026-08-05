// Documentacion de archivo: Configuracion de Vite para compilar los assets frontend de Laravel.
// Define entradas CSS/JS, activa el plugin de Laravel, Tailwind y recarga automatica de vistas durante desarrollo.
import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import tailwindcss from '@tailwindcss/vite';

export default defineConfig({
    plugins: [
        // Documentacion: laravel-vite-plugin conecta Vite con @vite en Blade y genera el manifest de assets.
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js'],
            refresh: true,
        }),
        // Documentacion: Tailwind procesa las clases usadas en Blade/JS segun resources/css/app.css.
        tailwindcss(),
    ],
    server: {
        watch: {
            // Documentacion: evita que vistas compiladas por Laravel disparen recargas infinitas en desarrollo.
            ignored: ['**/storage/framework/views/**'],
        },
    },
});
