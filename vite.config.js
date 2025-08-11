import { defineConfig } from "vite";
import laravel from "laravel-vite-plugin";
import react from "@vitejs/plugin-react";

export default defineConfig({
    plugins: [
        laravel({
            input: [
                "resources/js/app.ts",
                "resources/js/admin.ts",
                "resources/js/instructor.ts",
                "resources/js/support.ts",
                "resources/js/upload-modal-manager.tsx",
                "resources/js/filepond.js",
                "resources/js/alert-manager.js",
                "resources/css/app.css",
                "resources/css/admin.css",
                "resources/css/adminlte-config-tabs.css",
                "resources/css/admin-settings.css",
                "resources/css/alert-utilities.css",
                "resources/css/filepond.css",
            ],
            publicDir: "public",
            outputDir: "public/build",
            manifest: true,
            refresh: true,
            watch: [
                "resources/views/**/*.blade.php",
                "resources/**/*.blade.php",
            ],
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
