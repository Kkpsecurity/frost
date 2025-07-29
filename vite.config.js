import { defineConfig } from "vite";
import laravel from "laravel-vite-plugin";
import react from "@vitejs/plugin-react";

export default defineConfig({
    plugins: [
        laravel({
            input: [
                "resources/css/app.css",
                "resources/js/app.tsx",
            ],
            refresh: true,
        }),
        react(),
    ],
    server: {
        host: "localhost",
        port: 5174,
        cors: true,
    },
    base: "/",
    build: {
        manifest: true,
        outDir: "public/build",
    },
    resolve: {
        alias: {
            "@": "/resources/js",
        },
    },
});
