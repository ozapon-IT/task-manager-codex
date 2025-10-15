<?php

/**
 * purpose: Configure Cross-Origin Resource Sharing to allow the Nuxt SPA to talk to the Laravel API.
 */

return [
    /*
    |--------------------------------------------------------------------------
    | Path Patterns
    |--------------------------------------------------------------------------
    |
    | Limit CORS handling to API and Sanctum CSRF routes. Other routes fall back
    | to the framework defaults and avoid unnecessary headers.
    |
    */
    'paths' => [
        'api/*',
        'sanctum/csrf-cookie',
    ],

    /*
    |--------------------------------------------------------------------------
    | Allowed HTTP Methods
    |--------------------------------------------------------------------------
    |
    | Allow the SPA to perform any HTTP verb, covering CRUD APIs and preflight
    | requests without the need to keep this list in sync manually.
    |
    */
    'allowed_methods' => ['*'],

    /*
    |--------------------------------------------------------------------------
    | Allowed Headers
    |--------------------------------------------------------------------------
    |
    | Permit all headers so custom auth tokens, tracing headers, and fetch-based
    | requests are accepted without whitelisting each header individually.
    |
    */
    'allowed_headers' => ['*'],

    /*
    |--------------------------------------------------------------------------
    | Allowed Origins
    |--------------------------------------------------------------------------
    |
    | Restrict cross-origin calls to the Nuxt local dev server. If additional
    | front-ends are added, append their URLs or patterns here.
    |
    */
    'allowed_origins' => [
        'http://localhost:3000',
    ],

    'allowed_origins_patterns' => [],

    /*
    |--------------------------------------------------------------------------
    | Exposed Headers
    |--------------------------------------------------------------------------
    |
    | No custom exposure needed for now. Add entries if the SPA must read
    | custom response headers (e.g., pagination metadata from headers).
    |
    */
    'exposed_headers' => [],

    /*
    |--------------------------------------------------------------------------
    | Max Age
    |--------------------------------------------------------------------------
    |
    | Cache preflight responses for an hour to reduce redundant OPTIONS calls.
    |
    */
    'max_age' => 3600,

    /*
    |--------------------------------------------------------------------------
    | Credentials
    |--------------------------------------------------------------------------
    |
    | Sanctum requires credentialed requests (cookies). Enable this to allow
    | session-based auth between Nuxt and Laravel.
    |
    */
    'supports_credentials' => true,
];
