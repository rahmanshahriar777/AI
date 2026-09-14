<?php

return [
    /*
    |--------------------------------------------------------------------------
    | AI Providers Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration for primary and secondary fallback AI providers in NeoERP.
    | API keys and secrets are loaded securely from environment variables.
    |
    */

    'primary_provider' => env('AI_PRIMARY_PROVIDER', 'gemini'),
    'fallback_provider' => env('AI_FALLBACK_PROVIDER', 'openai'),

    'timeout_ms' => (int) env('AI_REQUEST_TIMEOUT_MS', 15000),
    'max_retries' => (int) env('AI_MAX_RETRIES', 2),

    'gemini' => [
        'api_key' => env('GEMINI_API_KEY'),
        'model' => env('AI_GEMINI_MODEL', 'gemini-flash-lite-latest'),
        'daily_cap' => (int) env('AI_DAILY_CAP_GEMINI', 100),
    ],

    'openai' => [
        'api_key' => env('OPENAI_API_KEY'),
        'model' => env('AI_FALLBACK_MODEL', 'gpt-4o-mini'),
        'daily_cap' => (int) env('AI_DAILY_CAP_OPENAI', 50),
    ],
];
