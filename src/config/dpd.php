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

    'key'        => env('DPD_KEY', null),
    'user'       => env('DPD_USER', null),
    'login'      => env('DPD_LOGIN', null),
    'password'   => env('DPD_PASSWORD', null),
    'prefix'     => 'dpd',
    'middleware' => ['web'],

];
