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
        'token' => env('POSTMARK_TOKEN'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'resend' => [
        'key' => env('RESEND_KEY'),
    ],

    'yandex' => [
        'object_storage' => [
            'key_id' => env('YANDEX_OBJECT_STORAGE_KEY_ID'),
            'secret' => env('YANDEX_OBJECT_STORAGE_SECRET'),
            'bucket' => env('YANDEX_OBJECT_STORAGE_BUCKET'),
            'region' => env('YANDEX_OBJECT_STORAGE_REGION', 'ru-central1'),
            'endpoint' => env('YANDEX_OBJECT_STORAGE_ENDPOINT', 'https://storage.yandexcloud.net'),
        ],
    ],

    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel' => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],

    'gigachat' => [
        'auth_url' => env('GIGACHAT_AUTH_URL', 'https://ngw.devices.sberbank.ru:9443/api/v2/oauth'),
        'base_url' => env('GIGACHAT_BASE_URL', 'https://gigachat.devices.sberbank.ru/api/v1'),
        'client_id' => env('GIGACHAT_CLIENT_ID', ''),
        'secret' => env('GIGACHAT_SECRET', ''),
    ],
];
