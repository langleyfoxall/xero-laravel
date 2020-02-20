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
            'token' => env('XERO_TOKEN'),
            'tenant_id' => env('XERO_TENANT_ID'),
        ],
    ],
];
