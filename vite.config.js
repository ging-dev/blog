import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import { resolve } from 'path'
import tailwindcss from '@tailwindcss/vite'

process.env['APP_URL'] = 'http://localhost:8000'

export default defineConfig({
    plugins: [
        tailwindcss(),
        laravel({
            input: ['resources/app.ts', 'resources/editor.ts', 'style.css'],
            refresh: ['**.php', 'templates/**.twig'],
            hotFile: resolve(__dirname, 'hot'),
            publicDirectory: './',
        }),
    ],
    esbuild: {
        loader: "ts"
    }
});
