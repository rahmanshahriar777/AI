<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Google Gemini API Key
    |--------------------------------------------------------------------------
    |
    | Get your free API key from Google AI Studio:
    | https://aistudio.google.com/app/apikey
    |
    */
    'api_key' => env('GEMINI_API_KEY', ''),

    /*
    |--------------------------------------------------------------------------
    | Default Gemini Model
    |--------------------------------------------------------------------------
    |
    | Gemini 3.6 Flash / 3.7 Flash are fast and powerful for general ERP automation:
    | - High speed & low latency
    | - Full multimodal reasoning & text generation
    |
    | Other options: 'gemini-3.6-flash', 'gemini-3.7-flash', 'gemini-3.8-flash'
    |
    */
    'model' => env('GEMINI_MODEL', 'gemini-3.6-flash'),

    /*
    |--------------------------------------------------------------------------
    | API Base URL & Version
    |--------------------------------------------------------------------------
    */
    'base_url' => env('GEMINI_BASE_URL', 'https://generativelanguage.googleapis.com/v1beta'),

    /*
    |--------------------------------------------------------------------------
    | Generation Configuration Defaults
    |--------------------------------------------------------------------------
    */
    'temperature' => env('GEMINI_TEMPERATURE', 0.7),
    'max_output_tokens' => env('GEMINI_MAX_TOKENS', 2048),
    'timeout' => env('GEMINI_TIMEOUT', 30),
];
