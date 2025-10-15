<?php

// purpose: configure HTTP middleware stack for the Laravel API including CORS support

namespace App\Http;

use Illuminate\Foundation\Http\Kernel as HttpKernel;

class Kernel extends HttpKernel
{
    /**
     * Global HTTP middleware that run during every request to the application.
     *
     * @var array<int, string>
     */
    protected $middleware = [
        // Trust proxy headers (X-Forwarded-*) when running behind load balancers.
        \App\Http\Middleware\TrustProxies::class,

        // Prevent requests from other origins without explicit permission.
        \Illuminate\Http\Middleware\HandleCors::class,

        // Manage maintenance mode and refuse requests when down.
        \App\Http\Middleware\PreventRequestsDuringMaintenance::class,

        // Validate POST request size to avoid overflows.
        \Illuminate\Foundation\Http\Middleware\ValidatePostSize::class,

        // Trim whitespace from string inputs.
        \App\Http\Middleware\TrimStrings::class,

        // Convert empty strings to null for consistency.
        \Illuminate\Foundation\Http\Middleware\ConvertEmptyStringsToNull::class,
    ];

    /**
     * Route middleware groups.
     *
     * @var array<string, array<int, string>>
     */
    protected $middlewareGroups = [
        'web' => [
            \App\Http\Middleware\EncryptCookies::class,
            \Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse::class,
            \Illuminate\Session\Middleware\StartSession::class,
            // Share validation errors from the session to views.
            \Illuminate\View\Middleware\ShareErrorsFromSession::class,
            \App\Http\Middleware\VerifyCsrfToken::class,
            // Substitute route parameters with model bindings.
            \Illuminate\Routing\Middleware\SubstituteBindings::class,
        ],

        'api' => [
            // Nuxtからのリクエストを処理するためのCORSミドルウェア
            \Illuminate\Http\Middleware\HandleCors::class,
            // Apply API rate limiting configuration.
            \Illuminate\Routing\Middleware\ThrottleRequests::class . ':api',
            // Resolve route parameters to models.
            \Illuminate\Routing\Middleware\SubstituteBindings::class,
        ],
    ];

    /**
     * Route middleware aliases.
     *
     * @var array<string, string>
     */
    protected $middlewareAliases = [
        'auth' => \App\Http\Middleware\Authenticate::class,
        'auth.basic' => \Illuminate\Auth\Middleware\AuthenticateWithBasicAuth::class,
        'cache.headers' => \Illuminate\Http\Middleware\SetCacheHeaders::class,
        'can' => \Illuminate\Auth\Middleware\Authorize::class,
        'guest' => \App\Http\Middleware\RedirectIfAuthenticated::class,
        'password.confirm' => \Illuminate\Auth\Middleware\RequirePassword::class,
        'signed' => \Illuminate\Routing\Middleware\ValidateSignature::class,
        'throttle' => \Illuminate\Routing\Middleware\ThrottleRequests::class,
        'verified' => \Illuminate\Auth\Middleware\EnsureEmailIsVerified::class,
    ];
}
