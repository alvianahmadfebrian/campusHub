<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
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

    /*
    |--------------------------------------------------------------------------
    | Supabase
    |--------------------------------------------------------------------------
    */

    'supabase' => [
        'url' => env('SUPABASE_URL'),
        'anon_key' => env('SUPABASE_ANON_KEY'),

        'storage' => [
            'key_id' => env('SUPABASE_STORAGE_KEY_ID'),
            'secret_key' => env('SUPABASE_STORAGE_SECRET_ACCESS_KEY'),
            'region' => env('SUPABASE_STORAGE_REGION', 'ap-south-1'),
            'endpoint' => env('SUPABASE_STORAGE_ENDPOINT'),

            'buckets' => [
                'profile' => env('SUPABASE_BUCKET_PROFILE', 'profile-photos'),
                'materi' => env('SUPABASE_BUCKET_MATERI', 'materi-files'),
                'event' => env('SUPABASE_BUCKET_EVENT', 'event-images'),
                'drive' => env('SUPABASE_BUCKET_DRIVE', 'drive-files'),
            ],
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | OpenRouter Chatbot
    |--------------------------------------------------------------------------
    */

'groq' => [
    'api_key' => env('GROQ_API_KEY'),

    'endpoint' => env(
        'GROQ_ENDPOINT',
        'https://api.groq.com/openai/v1/chat/completions'
    ),

    'model' => env(
        'GROQ_MODEL',
        'llama-3.1-8b-instant'
    ),

    'max_tokens' => (int) env('GROQ_MAX_TOKENS', 1200),

    'chat_document_max_mb' => (int) env('CHAT_DOCUMENT_MAX_MB', 20),
],

];
