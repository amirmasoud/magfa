<?php

return [
    /*
     |--------------------------------------------------------------------------
     | MAGFA SMS SETTINGS
     |--------------------------------------------------------------------------
     |
     | Add your sender number and credentials.
     |
     */
    'sender' => '3000',

    'username' => env('MAGFA_USERNAME'),
    'password' => env('MAGFA_PASSWORD'),
    'domain'   => env('MAGFA_DOMAIN')
];
