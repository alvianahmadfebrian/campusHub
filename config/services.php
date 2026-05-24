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

    'openrouter' => [
        'api_key' => env('OPENROUTER_API_KEY'),

        'endpoint' => env(
            'OPENROUTER_ENDPOINT',
            'https://openrouter.ai/api/v1/chat/completions'
        ),

        'models' => array_values(array_filter(array_map(
            'trim',
            explode(',', env(
                'OPENROUTER_MODELS',
                'openai/gpt-oss-120b:free,openai/gpt-oss-20b:free,baidu/cobuddy:free'
            ))
        ))),

        'site_url' => env('OPENROUTER_SITE_URL', env('APP_URL')),

        'app_title' => env(
            'OPENROUTER_APP_TITLE',
            env('APP_NAME', 'CampusHub') . ' Chatbot'
        ),

        'max_tokens' => (int) env('OPENROUTER_MAX_TOKENS', 1200),

        'pdf_engine' => env('OPENROUTER_PDF_ENGINE', 'cloudflare-ai'),

        'chat_document_max_mb' => (int) env('CHAT_DOCUMENT_MAX_MB', 10),
    ],

];
