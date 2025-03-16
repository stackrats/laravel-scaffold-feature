<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Laravel Scaffold Feature
    |--------------------------------------------------------------------------
    |
    | This file is for configuring the Laravel Scaffold Feature package.
    |
    */

    'root_dirs' => [
        'Features/'         => 'App/Features/',
        'Shared/Features/'  => 'App/Shared/Features/',
        '/'                 => 'App/',
    ],

    'validation' => [
        'dir_pattern'     => '/^(?:[A-Z][a-zA-Z0-9]*|_[A-Z][a-zA-Z0-9]*)(?:\/(?:[A-Z][a-zA-Z0-9]*|_[A-Z][a-zA-Z0-9]*))*$/',
        'feature_pattern' => '/^[A-Z][a-zA-Z0-9]*$/',
    ],

];