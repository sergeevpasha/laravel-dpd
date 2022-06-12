<?php

return [

    /*
    |--------------------------------------------------------------------------
    | DPD Default configuration
    |--------------------------------------------------------------------------
    |
    | This options must be set in order to use DPD API.
    |
    */

    'key'        => env('DPD_KEY'),
    'user'       => env('DPD_USER'),
    'login'      => env('DPD_LOGIN'),
    'password'   => env('DPD_PASSWORD'),
    'prefix'     => 'dpd',
    'middleware' => ['web'],

];
