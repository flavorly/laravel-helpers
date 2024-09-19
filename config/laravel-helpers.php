<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Math Helper Configuration
    |--------------------------------------------------------------------------
    |
    | All notable configurations will show here.
    |
    */
    'math' => [
        'scale' => 10,
        'storage_scale' => 10,
        'rounding_mode' => \Brick\Math\RoundingMode::DOWN,
    ],

    'typescript' => [
        'replace' => [
            'Flavorly.InertiaFlash.' => '',
            'Flavorly.LaravelHelpers.' => '',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Debug Saloon Requests / Responses
    |--------------------------------------------------------------------------
    |
    | When enabled, all requests and responses will be logged to ray
    |
    */
    'debug-requests' => false,
];
