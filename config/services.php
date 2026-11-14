<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Mailgun, Postmark, AWS and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */

    'postmark' => [
        'key' => env('POSTMARK_API_KEY'),
    ],

    'resend' => [
        'key' => env('RESEND_API_KEY'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel' => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],

    // Asistente de IA (Google Gemini). Los valores se configuran en el archivo .env
    'gemini' => [
        'key'        => env('GEMINI_API_KEY'),
        // Modelos a probar en orden; si el primero falla se usa el siguiente
        'models'     => array_values(array_filter(array_map('trim', explode(',', env('GEMINI_MODELS', 'gemini-3.6-flash,gemini-3.1-flash-lite'))))),
        // Mantener en true. Solo poner GEMINI_VERIFY_SSL=false en local si Windows no tiene certificados SSL.
        'verify_ssl' => env('GEMINI_VERIFY_SSL', true),
        'timeout'    => (int) env('GEMINI_TIMEOUT', 30),
    ],

];
