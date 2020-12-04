<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Xero Laravel configuration
    |--------------------------------------------------------------------------
    |
    | This file is for storing the configuration for the Langley Foxall
    | Xero Laravel package.
    |
    */

    'apps' => [
        'default' => [
            'client_id'     => env('XERO_CLIENT_ID'),
            'client_secret' => env('XERO_CLIENT_SECRET'),
            'redirect_uri'  => env('XERO_REDIRECT_URI'),
            'scope'         => 'openid email profile offline_access accounting.settings.read',
        ],
    ],
];
