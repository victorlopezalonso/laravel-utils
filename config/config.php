<?php

return [
    'environments' => [
        'local' => 'local',
        'testing' => 'testing',
        'develop' => 'develop',
        'staging' => 'staging',
        'production' => 'production',
    ],
    'headers' => [
        'x-api-key' => 'x-api-key',
        'language' => 'language',
        'app-version' => 'app-version',
        'os' => 'os',
        'os-version' => 'os-version',
        'brand' => 'brand',
        'model' => 'model'
    ],
    'os' => [
        'android' => 'android',
        'ios' => 'ios',
        'web' => 'web',
    ],
    'languages' => [
        'es',
        'en',
    ],
    'default_language' => 'es',
    'api_default_per_page' => 15,
    'roles' => [
        'root' => 'root',
        'admin' => 'admin',
        'user' => 'user',
    ],
    'genders' => [
        'male' => 'male',
        'female' => 'female',
        'other' => 'other',
    ],
];
