import { defineConfig } from "vite";
import laravel from "laravel-vite-plugin";
import react from "@vitejs/plugin-react";
import fs from "fs";

export default defineConfig({
    plugins: [
        laravel({
            input: [
                // Core Laravel TypeScript files
                "resources/js/admin.ts",
                "resources/js/instructor.ts",
                "resources/js/support.ts",
                "resources/js/student.ts",

                // Laravel JavaScript modules
                "resources/js/modules/filepond.js",
                "resources/js/modules/site.js",

                // Student Offline Video Room
                // "resources/js/React/Student/Offline/OfflineVideoRoom.tsx",

                // Games
                // "resources/js/games/SpeedTicTacToe.tsx",

                // CSS files (consolidated structure)
                "resources/css/style.css", // Main stylesheet with all imports
                "resources/css/app.css", // App-specific styles
                "resources/css/admin.css", // Admin styles

                // Component CSS files
                "resources/css/components/welcome-hero.css",
                "resources/css/components/blog.css",
                "resources/css/components/courses.css",
                "resources/css/components/getting-started.css",
                "resources/css/components/alerts.css",
                "resources/css/components/footer.css",
                "resources/css/components/header.css",
                "resources/css/components/topbar.css",
                "resources/css/components/loader.css",
            ],
            refresh: true,
        }),
        react({
            include: "**/*.{jsx,tsx}",
        }),
    ],
    // Dev server config: bind to the local site hostname so @vite/client shares origin
    // If local TLS certs exist under ./certs, enable HTTPS + wss HMR; otherwise fall back to HTTP + ws.
    server: (() => {
        const host = "frost.test";
        const port = 5173;
        const keyPath = "certs/frost.test-key.pem";
        const certPath = "certs/frost.test.pem";
        const useHttps = fs.existsSync(keyPath) && fs.existsSync(certPath);

        const base = {
            host,
            port,
            strictPort: true,
            hmr: {
                protocol: useHttps ? "wss" : "ws",
                host,
                port,
            },
            origin: `${useHttps ? "https" : "http"}://${host}:${port}`,
            // Allow cross-origin requests from the app host and include credentials
            cors: {
                origin: `${useHttps ? "https" : "http"}://${host}`,
                credentials: true,
            },
            headers: {
                "Access-Control-Allow-Origin": `${
                    useHttps ? "https" : "http"
                }://${host}`,
                "Access-Control-Allow-Credentials": "true",
            },
        };

        if (useHttps) {
            try {
                base.https = {
                    key: fs.readFileSync(keyPath),
                    cert: fs.readFileSync(certPath),
                };
            } catch (e) {
                // If certs cannot be read for any reason, ensure we still return a valid config and log to console
                // eslint-disable-next-line no-console
                console.warn(
                    "vite.config: failed to read TLS certs, falling back to HTTP",
                    e && e.message
                );
            }
        }

        return base;
    })(),
    resolve: {
        alias: {
            "@": "/resources/js",
        },
    },
    optimizeDeps: {
        exclude: ["vendor/**/*", "node_modules/**/*"],
    },
    build: {
        rollupOptions: {
            output: {
                // Force new hash by including timestamp in manualChunks
                manualChunks: undefined,
                // Ensure content-based hashing
                entryFileNames: `assets/[name]-[hash].js`,
                chunkFileNames: `assets/[name]-[hash].js`,
                assetFileNames: `assets/[name]-[hash].[ext]`,
            },
        },
    },
});
