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

    'mayar' => [
        'api_key' => 'eyJhbGciOiJSUzI1NiIsInR5cCI6IkpXVCJ9.eyJ1c2VySWQiOiI2YzM4YWM5Mi0yMmJlLTQ0YmUtOWZjZS1hYTlkYzZlYjFiZGQiLCJhY2NvdW50SWQiOiIwODM0M2IwNi0yYjlkLTRjNTYtYWRmOS1kOWZkY2NmYTE4ZmQiLCJjcmVhdGVkQXQiOiIxNzc0NDk5MTc5MjY4Iiwicm9sZSI6ImRldmVsb3BlciIsInN1YiI6InhkYWdoYXRhQGdtYWlsLmNvbSIsIm5hbWUiOiJPeHlkYSBTdG9yZSIsImxpbmsiOiJveHlkYSIsImlzU2VsZkRvbWFpbiI6bnVsbCwiaWF0IjoxNzc0NDk5MTc5fQ.atze-ZzI_-kyfnnnfiFpxuZegsZENg-e5ppuYij1AapZLEBZoj27ioH_N69Ek3A0BMUMA2OzX6VrD1VG9YqxY61VTkFFvvNcwmE9uDnCYnqEVUZ8G3n7v7yNum1k9gNXVDEgPYn4M0pA1GDxtV7HbgDkkSucaWcb7PcMdsbXMGPkNPtOR3vksBxpkV2aGZGFr_k3WvWwSte2y8Q7qOZ-C9yvBA27Hd6bHFa4oYyKsNsCnRzLnMcAn6jFeV8AYoCsbu0zfxpjJ88l9ELIe2vFQv42-j2KlP_pSXhl_fasa3OyngN_VYsFJCmT93DZsqSZ4Ci5yjf-dxqrmeD0BZu7-Q', // Hardcode agar aman dari accidental leak di .env, karena ini hanya digunakan untuk server-side request
        'license_verify_url' => 'https://api.mayar.id/software/v1/license/verify',
        'product_id' => '0d213961-643f-4ee2-a519-50b670949c2b', // Hardcode agar aman dari accidental leak di .env, karena ini hanya digunakan untuk server-side request
        'license_enforce' => (bool) env('LICENSE_ENFORCE', true),
        'grace_hours' => (int) env('LICENSE_GRACE_HOURS', 24),
        'cache_minutes' => (int) env('LICENSE_CACHE_MINUTES', 10),
    ],

];
