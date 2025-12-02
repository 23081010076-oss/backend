<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        apiPrefix: 'api',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        // Register custom middleware aliases
        $middleware->alias([
            'role' => \App\Http\Middleware\CheckRole::class,
        ]);

        // =====================================================================
        // RATE LIMITING (Industri Standard - Wajib untuk Production!)
        // =====================================================================
        // Mencegah:
        // - Brute force attack pada login
        // - DDoS attack
        // - API abuse
        // =====================================================================
        
        // Rate limit untuk semua API routes
        $middleware->throttleApi('api');
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })
    ->booted(function () {
        // =====================================================================
        // KONFIGURASI RATE LIMITER
        // =====================================================================
        
        // Rate limit untuk API umum: 60 requests per menit
        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(60)->by(
                $request->user()?->id ?: $request->ip()
            )->response(function () {
                return response()->json([
                    'sukses' => false,
                    'pesan' => 'Terlalu banyak request. Coba lagi dalam 1 menit.',
                ], 429);
            });
        });

        // Rate limit ketat untuk login: 5 attempts per menit
        RateLimiter::for('login', function (Request $request) {
            return Limit::perMinute(5)->by(
                $request->input('email') . '|' . $request->ip()
            )->response(function () {
                return response()->json([
                    'sukses' => false,
                    'pesan' => 'Terlalu banyak percobaan login. Coba lagi dalam 1 menit.',
                ], 429);
            });
        });

        // Rate limit untuk register: 3 per menit
        RateLimiter::for('register', function (Request $request) {
            return Limit::perMinute(3)->by($request->ip())
                ->response(function () {
                    return response()->json([
                        'sukses' => false,
                        'pesan' => 'Terlalu banyak percobaan registrasi. Coba lagi dalam 1 menit.',
                    ], 429);
                });
        });

        // Rate limit untuk upload file: 10 per menit
        RateLimiter::for('uploads', function (Request $request) {
            return Limit::perMinute(10)->by(
                $request->user()?->id ?: $request->ip()
            )->response(function () {
                return response()->json([
                    'sukses' => false,
                    'pesan' => 'Terlalu banyak upload. Coba lagi dalam 1 menit.',
                ], 429);
            });
        });
    })->create();
