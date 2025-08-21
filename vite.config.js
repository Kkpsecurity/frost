import { defineConfig } from "vite";
import laravel from "laravel-vite-plugin";
import react from "@vitejs/plugin-react";

export default defineConfig({
    plugins: [
        laravel({
            input: [
                // Core Laravel TypeScript files
                "resources/js/app.ts",
                "resources/js/admin.ts",
                "resources/js/instructor.ts",
                "resources/js/support.ts",
                "resources/js/student.ts",

                // Laravel JavaScript modules
                "resources/js/modules/filepond.js",
                "resources/js/modules/site.js",

                // Laravel JavaScript components
                "resources/js/components/alert-manager.js",

                // React Components (proper path)
                "resources/js/React/Components/UploadModalManager.tsx",

                // CSS files (updated paths)
                "resources/css/app.css",
                "resources/css/style.css",
                "resources/css/pages/admin.css",
                "resources/css/vendor/adminlte-config-tabs.css",
                "resources/css/pages/admin-settings.css",
                "resources/css/components/alerts.css",
                "resources/css/vendor/filepond.css",
                "resources/css/pages/site.css",
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
