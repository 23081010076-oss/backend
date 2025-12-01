<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Cross-Origin Resource Sharing (CORS) Configuration
    |--------------------------------------------------------------------------
    |
    | Here you may configure your settings for cross-origin resource sharing
    | or "CORS". This determines what cross-origin operations may execute
    | in web browsers. You are free to adjust these settings as needed.
    |
    | To learn more: https://developer.mozilla.org/en-US/docs/Web/HTTP/CORS
    |
    */

    /*
    |--------------------------------------------------------------------------
    | CORS Path
    |--------------------------------------------------------------------------
    | Path yang diizinkan untuk cross-origin request.
    | 'api/*' = semua endpoint yang dimulai dengan /api/
    | 'sanctum/csrf-cookie' = untuk Laravel Sanctum (jika digunakan)
    */
    'paths' => ['api/*', 'sanctum/csrf-cookie'],

    /*
    |--------------------------------------------------------------------------
    | Allowed Methods
    |--------------------------------------------------------------------------
    | HTTP methods yang diizinkan dari frontend.
    | '*' = semua method (GET, POST, PUT, PATCH, DELETE, OPTIONS)
    */
    'allowed_methods' => ['*'],

    /*
    |--------------------------------------------------------------------------
    | Allowed Origins
    |--------------------------------------------------------------------------
    | Domain frontend yang diizinkan mengakses API.
    | 
    | TIPS untuk Integrasi:
    | - localhost:3000 = React (Create React App)
    | - localhost:5173 = Vite (React/Vue/Svelte)
    | - localhost:8080 = Vue CLI / Android Emulator
    | - localhost:19006 = Expo (React Native)
    | - 10.0.2.2:8000 = Android Emulator ke localhost
    |
    | PRODUCTION: Ganti dengan domain frontend Anda
    | Contoh: 'https://myapp.com', 'https://www.myapp.com'
    */
    'allowed_origins' => [
        'http://localhost:3000',    // React CRA
        'http://localhost:5173',    // Vite
        'http://localhost:5174',    // Vite (port alternatif)
        'http://localhost:8000',    // Laravel local
        'http://localhost:8080',    // Vue CLI / Android
        'http://localhost:19006',   // Expo
        'http://127.0.0.1:3000',
        'http://127.0.0.1:5173',
        'http://127.0.0.1:8080',
    ],

    /*
    |--------------------------------------------------------------------------
    | Allowed Origins Patterns (Regex)
    |--------------------------------------------------------------------------
    | Pattern regex untuk domain yang diizinkan.
    | Berguna untuk subdomain dinamis.
    |
    | Contoh production:
    | 'https://*.myapp.com' - semua subdomain
    */
    'allowed_origins_patterns' => [
        // Uncomment untuk production dengan subdomain
        // '#^https://.*\.myapp\.com$#',
    ],

    /*
    |--------------------------------------------------------------------------
    | Allowed Headers
    |--------------------------------------------------------------------------
    | Header yang diizinkan dari frontend.
    | '*' = semua header (termasuk Authorization untuk JWT)
    */
    'allowed_headers' => ['*'],

    /*
    |--------------------------------------------------------------------------
    | Exposed Headers
    |--------------------------------------------------------------------------
    | Header yang bisa dibaca oleh frontend dari response.
    | Berguna jika perlu akses header custom.
    */
    'exposed_headers' => ['X-Total-Count', 'X-Page-Count'],

    /*
    |--------------------------------------------------------------------------
    | Max Age
    |--------------------------------------------------------------------------
    | Berapa lama (detik) browser meng-cache preflight request.
    | 86400 = 24 jam (hemat request OPTIONS)
    */
    'max_age' => 86400,

    /*
    |--------------------------------------------------------------------------
    | Supports Credentials
    |--------------------------------------------------------------------------
    | Apakah mengizinkan cookies/credentials dari frontend.
    | true = required untuk session-based auth
    | Untuk JWT bisa true atau false
    */
    'supports_credentials' => true,

];
