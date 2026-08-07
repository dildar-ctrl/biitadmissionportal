<?php

use App\Models\User;

return [

    /*
    |--------------------------------------------------------------------------
    | Authentication Defaults
    |--------------------------------------------------------------------------
    */

     'defaults' => [
        'guard' => 'web',
        'passwords' => 'candidates',
    ],

    /*
    |--------------------------------------------------------------------------
    | Authentication Guards
    |--------------------------------------------------------------------------
    */

      'guards' => [
        'web' => [
            'driver' => 'session',
            'provider' => 'candidates',
        ],

        'admin' => [
            'driver' => 'session',
            'provider' => 'admins',
        ],
      ],



    /*
    |--------------------------------------------------------------------------
    | User Providers
    |--------------------------------------------------------------------------
    */

     'providers' => [
        'candidates' => [
            'driver' => 'eloquent',
            'model' => App\Models\CandidateLogin::class,
        ],

        'admins' => [
            'driver' => 'eloquent',
            'model' => App\Models\AdminUser::class,
        ],

        // 'users' => [
        //     'driver' => 'database',
        //     'table' => 'users',
        // ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Resetting Passwords
    |--------------------------------------------------------------------------
    */

      'passwords' => [
        'candidates' => [
            'provider' => 'candidates',
            'table' => 'password_reset_tokens',
            'expire' => 60,
            'throttle' => 60,
        ],
    ],



    /*
    |--------------------------------------------------------------------------
    | Password Confirmation Timeout
    |--------------------------------------------------------------------------
    */

     'password_timeout' => 10800,


];