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

            'app_type' => 'private',
            'oauth'    => [
                'callback'         => 'http://localhost/',
                'consumer_key'     => env('XERO_CONSUMER_KEY'),
                'consumer_secret'  => env('XERO_CONSUMER_SECRET'),
                'rsa_private_key'  => 'file://'.storage_path('app/xero-key-pair/privatekey.pem'),
            ],

        ],
    ],

];
