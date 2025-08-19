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
                "resources/js/site.js",
                "resources/css/app.css",
                "resources/css/admin.css",
                "resources/css/adminlte-config-tabs.css",
                "resources/css/admin-settings.css",
                "resources/css/alert-utilities.css",
                "resources/css/filepond.css",
                "resources/css/site.css",
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
        react({
            include: "**/*.{jsx,tsx}",
            jsxRuntime: "automatic",
        }),
    ],
    server: {
        host: "localhost",
        port: 5174,
        cors: {
            origin: [
                "http://frost.test",
                "http://localhost",
                "http://127.0.0.1",
            ],
            methods: ["GET", "POST", "PUT", "DELETE", "PATCH", "OPTIONS"],
            credentials: true,
        },
        headers: {
            "Access-Control-Allow-Origin": "*",
            "Access-Control-Allow-Methods":
                "GET, POST, PUT, DELETE, PATCH, OPTIONS",
            "Access-Control-Allow-Headers":
                "Origin, X-Requested-With, Content-Type, Accept, Authorization, Cache-Control",
        },
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
