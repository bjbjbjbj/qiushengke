<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Default Filesystem Disk
    |--------------------------------------------------------------------------
    |
    | Here you may specify the default filesystem disk that should be used
    | by the framework. The "local" disk, as well as a variety of cloud
    | based disks are available to your application. Just store away!
    |
    */

    'default' => 'local',

    /*
    |--------------------------------------------------------------------------
    | Default Cloud Filesystem Disk
    |--------------------------------------------------------------------------
    |
    | Many applications store files both locally and in the cloud. For this
    | reason, you may specify a default "cloud" driver here. This driver
    | will be bound as the Cloud disk implementation in the container.
    |
    */

    'cloud' => 's3',

    /*
    |--------------------------------------------------------------------------
    | Filesystem Disks
    |--------------------------------------------------------------------------
    |
    | Here you may configure as many filesystem "disks" as you wish, and you
    | may even configure multiple disks of the same driver. Defaults have
    | been setup for each driver as an example of the required options.
    |
    | Supported Drivers: "local", "ftp", "s3", "rackspace"
    |
    */

    'disks' => [

        'local' => [
            'driver' => 'local',
            'root' => storage_path('app'),
        ],

        'public' => [
            'driver' => 'local',
            'root' => storage_path('app/public'),
            'url' => env('APP_URL').'/storage',
            'visibility' => 'public',
        ],

        'live' => [
            'driver' => 'local',
            'root' => storage_path('app/public/live'),
            'url' => env('APP_URL').'/storage',
            'visibility' => 'public',
        ],
        'detail' => [
            'driver' => 'local',
            'root' => storage_path('app/public/detail'),
            'url' => env('APP_URL').'/storage',
            'visibility' => 'public',
        ],
        'matches' => [
            'driver' => 'local',
            'root' => storage_path('app/public/matches'),
            'url' => env('APP_URL').'/storage',
            'visibility' => 'public',
        ],
        'match' => [
            'driver' => 'local',
            'root' => storage_path('app/public/match'),
            'url' => env('APP_URL').'/storage',
            'visibility' => 'public',
        ],
        'basket' => [
            'driver' => 'local',
            'root' => storage_path('app/public/basket'),
            'url' => env('APP_URL').'/storage',
            'visibility' => 'public',
        ],

        'change'=>[
            'driver' => 'local',
            'root' => storage_path('static/change'),
            'url' => env('APP_URL').'/storage',
            'visibility' => 'public',
        ],
        'schedule'=>[
            'driver' => 'local',
            'root' => storage_path('static/schedule'),
            'url' => env('APP_URL').'/storage',
            'visibility' => 'public',
        ],
        'terminal'=>[
            'driver' => 'local',
            'root' => storage_path('static/terminal'),
            'url' => env('APP_URL').'/storage',
            'visibility' => 'public',
        ],
        'league'=>[
            'driver' => 'local',
            'root' => storage_path('static/league'),
            'url' => env('APP_URL').'/storage',
            'visibility' => 'public',
        ],

        's3' => [
            'driver' => 's3',
            'key' => env('AWS_KEY'),
            'secret' => env('AWS_SECRET'),
            'region' => env('AWS_REGION'),
            'bucket' => env('AWS_BUCKET'),
        ],

    ],

];
