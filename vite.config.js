import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import path from 'path';
import { glob } from 'glob'; // Cần cài thêm: npm install glob --save-dev

export default defineConfig({
    plugins: [
        laravel({
            input: [
                // 1. File CSS/JS chính
                'resources/css/app.scss',
                'resources/js/app.js',
                
                // 2. Tự động lấy tất cả file JS trong folder custom để minify riêng
                ...glob.sync('resources/js/custom/**/*.js'),
                
                // 3. Nếu muốn minify cả plugins cũ (nếu cần thiết)
                // ...glob.sync('resources/js/plugins-old/**/*.js'), 
            ],
            refresh: true,
        }),
    ],
    resolve: {
        alias: {
            // Giúp import dễ hơn trong file JS: import ... from '@/'
            '@': '/resources/js',
        },
    },
    build: {
        // Tối ưu chia nhỏ file chunk để load nhanh hơn
        chunkSizeWarningLimit: 1600,
    }
});
// import { defineConfig } from "vite";
// import laravel from "laravel-vite-plugin";

// export default defineConfig({
//     plugins: [
//         laravel({
//             input: [
//                 "resources/css/app.scss",
//                 "resources/js/app.js",
//                 // Chỉ giữ lại những file JS thực sự đứng độc lập,
//                 // còn main.js, store.js nên được import bên trong app.js
//                 "resources/js/main.js", 
//                 "resources/js/custom/finance.js",
//             ],
//             refresh: true,
//         }),
//     ],
//     build: {
//         chunkSizeWarningLimit: 1600,
//     },
// });