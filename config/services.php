<?php

// use Victorlopezalonso\LaravelUtils\Providers\GoogleCustomProvider;

return [
    'github' => [
        'client_id' => env('GITHUB_CLIENT_ID'),
        'client_secret' => env('GITHUB_CLIENT_SECRET'),
        'redirect' => '',
    ],

    // 'google' => [
    //     'provider' => GoogleCustomProvider::class,
    //     'client_id' => env('GOOGLE_CLIENT_ID'),
    //     'client_secret' => env('GOOGLE_CLIENT_SECRET'),
    //     'redirect' => '',
    // ],

    'apple' => [
        'client_id' => env('APPLE_CLIENT_ID'),
        'client_secret' => env('APPLE_CLIENT_SECRET'),
        'redirect' => env('APPLE_REDIRECT_URI')
    ],
];
