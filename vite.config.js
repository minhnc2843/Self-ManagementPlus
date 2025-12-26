import { defineConfig } from "vite";
import laravel from "laravel-vite-plugin";

export default defineConfig({
    plugins: [
        laravel({
            input: [
                "resources/css/app.scss",
                "resources/js/app.js",
                // Chỉ giữ lại những file JS thực sự đứng độc lập,
                // còn main.js, store.js nên được import bên trong app.js
                "resources/js/main.js", 
                "resources/js/custom/finance.js",
            ],
            refresh: true,
        }),
    ],
    build: {
        chunkSizeWarningLimit: 1600,
    },
});