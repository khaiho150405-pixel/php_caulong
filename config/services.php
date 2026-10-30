<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Resend, Postmark, AWS, and more. This file provides the de facto
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

    'momo' => [
        'mock' => env('MOMO_MOCK', true),
        'endpoint' => env('MOMO_ENDPOINT', 'https://test-payment.momo.vn/v2/gateway/api/create'),
        'partner_code' => env('MOMO_PARTNER_CODE', 'MOMO5RGX20191128'),
        'access_key' => env('MOMO_ACCESS_KEY', 'M8brj9K6E22vXoDB'),
        'secret_key' => env('MOMO_SECRET_KEY', 'nqQiVSgDMy809JoPF6OzP5OdBUB550Y4'),
        'return_url' => env('MOMO_RETURN_URL', 'http://localhost:8000/DonHang/ThanhToanMomoSuccess'),
        'notify_url' => env('MOMO_NOTIFY_URL', 'http://localhost:8000/DonHang/SavePayment'),
    ],

    'gemini' => [
        // KHÔNG ghi API key trực tiếp vào mã nguồn - khai báo GEMINI_API_KEY trong file .env
        'api_key' => env('GEMINI_API_KEY'),
        'model' => env('GEMINI_MODEL', 'gemini-2.5-flash'),
    ],

];
