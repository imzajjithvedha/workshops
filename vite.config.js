import { defineConfig } from "vite";
import laravel from "laravel-vite-plugin";
import tailwindcss from "@tailwindcss/vite";

export default defineConfig({
    plugins: [
        laravel({
            input: [
                "resources/css/app.css",
                "resources/js/app.js",
                "resources/js/index.js",
            ],
            refresh: true,
        }),
        tailwindcss(),
    ],
    build: {
        chunkSizeWarningLimit: 800,
        rollupOptions: {
            output: {
                manualChunks(id) {
                    if (id.includes("node_modules")) {
                        const match = id.match(
                            /node_modules\/\.?(?:pnpm\/)?([^\/]+)/,
                        );
                        const pkg = match
                            ? match[1].replace("@", "")
                            : "vendor";
                        return `vendor-${pkg}`;
                    }
                },
            },
        },
    },
});
