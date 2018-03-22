<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Default Database Connection Name
    |--------------------------------------------------------------------------
    |
    | Here you may specify which of the database connections below you wish
    | to use as your default connection for all database work. Of course
    | you may use many connections at once using the Database library.
    |
    */

    'default' => env('DB_CONNECTION', 'mysql'),

    /*
    |--------------------------------------------------------------------------
    | Database Connections
    |--------------------------------------------------------------------------
    |
    | Here are each of the database connections setup for your application.
    | Of course, examples of configuring each database platform that is
    | supported by Laravel is shown below to make development simple.
    |
    |
    | All database work in Laravel is done through the PHP PDO facilities
    | so make sure you have the driver for your particular database of
    | choice installed on your machine before you begin development.
    |
    */

    'connections' => [

        'sqlite' => [
            'driver' => 'sqlite',
            'database' => env('DB_DATABASE', database_path('database.sqlite')),
            'prefix' => '',
        ],

        'mysql' => [
            'driver' => 'mysql',
            'host' => env('DB_HOST', '127.0.0.1'),
            'port' => env('DB_PORT', '3306'),
            'database' => env('DB_DATABASE', 'forge'),
            'username' => env('DB_USERNAME', 'forge'),
            'password' => env('DB_PASSWORD', ''),
            'charset' => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
            'prefix' => '',
            'strict' => true,
            'engine' => null,
        ],

        'win_matches' => [
            'driver' => 'mysql',
            'host' => env('DB_HOST_MATCHES', '127.0.0.1'),
            'port' => env('DB_PORT_MATCHES', '3306'),
            'database' => env('DB_DATABASE_MATCHES', 'win_matches'),
            'username' => env('DB_USERNAME_MATCHES', 'root'),
            'password' => env('DB_PASSWORD_MATCHES', ''),
            'charset'   => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
            'prefix' => '',
            'strict' => true,
            'engine' => null,
        ],

        'liaogou_match' => [
            'driver' => 'mysql',
            'host' => env('DB_HOST_LG_MATCH', '127.0.0.1'),
            'port' => env('DB_PORT_LG_MATCH', '3306'),
            'database' => env('DB_DATABASE_LG_MATCH', 'liaogou_matches'),
            'username' => env('DB_USERNAME_LG_MATCH', 'root'),
            'password' => env('DB_PASSWORD_LG_MATCH', ''),
            'charset'   => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
            'prefix' => '',
            'strict' => true,
            'engine' => null,
        ],

        'liaogou_lottery' => [
            'driver' => 'mysql',
            'host' => env('DB_HOST_LG_LOTTERY', '127.0.0.1'),
            'port' => env('DB_PORT_LG_LOTTERY', '3306'),
            'database' => env('DB_DATABASE_LG_LOTTERY', 'liaogou_matches'),
            'username' => env('DB_USERNAME_LG_LOTTERY', 'root'),
            'password' => env('DB_PASSWORD_LG_LOTTERY', ''),
            'charset'   => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
            'prefix' => '',
            'strict' => true,
            'engine' => null,
        ],

        'liaogou_analyse' => [
            'driver' => 'mysql',
            'host' => env('DB_HOST_LG_ANALYSE', '127.0.0.1'),
            'port' => env('DB_PORT_LG_ANALYSE', '3306'),
            'database' => env('DB_DATABASE_LG_ANALYSE', 'liaogou_matches'),
            'username' => env('DB_USERNAME_LG_ANALYSE', 'root'),
            'password' => env('DB_PASSWORD_LG_ANALYSE', ''),
            'charset'   => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
            'prefix' => '',
            'strict' => true,
            'engine' => null,
        ],

        'liaogou_archive' => [
            'driver' => 'mysql',
            'host' => env('DB_HOST_LG_ARCHIVE', '127.0.0.1'),
            'port' => env('DB_PORT_LG_ARCHIVE', '3306'),
            'database' => env('DB_DATABASE_LG_ARCHIVE', 'liaogou_matches'),
            'username' => env('DB_USERNAME_LG_ARCHIVE', 'root'),
            'password' => env('DB_PASSWORD_LG_ARCHIVE', ''),
            'charset'   => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
            'prefix' => '',
            'strict' => true,
            'engine' => null,
        ],

        'moro' => [
            'driver' => 'mysql',
            'host' => env('DB_HOST_MORO', '127.0.0.1'),
            'port' => env('DB_PORT_MORO', '3306'),
            'database' => env('DB_DATABASE_MORO', 'liaogou_matches'),
            'username' => env('DB_USERNAME_MORO', 'root'),
            'password' => env('DB_PASSWORD_MORO', ''),
            'charset'   => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
            'prefix' => '',
            'strict' => true,
            'engine' => null,
        ],

        'pgsql' => [
            'driver' => 'pgsql',
            'host' => env('DB_HOST', '127.0.0.1'),
            'port' => env('DB_PORT', '5432'),
            'database' => env('DB_DATABASE', 'forge'),
            'username' => env('DB_USERNAME', 'forge'),
            'password' => env('DB_PASSWORD', ''),
            'charset' => 'utf8',
            'prefix' => '',
            'schema' => 'public',
            'sslmode' => 'prefer',
        ],

        'analyse_match' => [
            'driver' => 'mysql',
            'host' => env('DB_HOST_ANALYSE_MATCH', '127.0.0.1'),
            'port' => env('DB_PORT_ANALYSE_MATCH', '3306'),
            'database' => env('DB_DATABASE_ANALYSE_MATCH', 'liaogou_matches'),
            'username' => env('DB_USERNAME_ANALYSE_MATCH', 'root'),
            'password' => env('DB_PASSWORD_ANALYSE_MATCH', ''),
            'charset'   => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
            'prefix' => '',
            'strict' => true,
            'engine' => null,
        ],

        'analyse_lottery' => [
            'driver' => 'mysql',
            'host' => env('DB_HOST_ANALYSE_LOTTERY', '127.0.0.1'),
            'port' => env('DB_PORT_ANALYSE_LOTTERY', '3306'),
            'database' => env('DB_DATABASE_ANALYSE_LOTTERY', 'liaogou_matches'),
            'username' => env('DB_USERNAME_ANALYSE_LOTTERY', 'root'),
            'password' => env('DB_PASSWORD_ANALYSE_LOTTERY', ''),
            'charset'   => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
            'prefix' => '',
            'strict' => true,
            'engine' => null,
        ],

        'analyse_analyse' => [
            'driver' => 'mysql',
            'host' => env('DB_HOST_ANALYSE_ANALYSE', '127.0.0.1'),
            'port' => env('DB_PORT_ANALYSE_ANALYSE', '3306'),
            'database' => env('DB_DATABASE_ANALYSE_ANALYSE', 'liaogou_matches'),
            'username' => env('DB_USERNAME_ANALYSE_ANALYSE', 'root'),
            'password' => env('DB_PASSWORD_ANALYSE_ANALYSE', ''),
            'charset'   => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
            'prefix' => '',
            'strict' => true,
            'engine' => null,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Migration Repository Table
    |--------------------------------------------------------------------------
    |
    | This table keeps track of all the migrations that have already run for
    | your application. Using this information, we can determine which of
    | the migrations on disk haven't actually been run in the database.
    |
    */

    'migrations' => 'migrations',

    /*
    |--------------------------------------------------------------------------
    | Redis Databases
    |--------------------------------------------------------------------------
    |
    | Redis is an open source, fast, and advanced key-value store that also
    | provides a richer set of commands than a typical key-value systems
    | such as APC or Memcached. Laravel makes it easy to dig right in.
    |
    */

    'redis' => [

        'client' => 'predis',

        'default' => [
            'host' => env('REDIS_HOST', '127.0.0.1'),
            'password' => env('REDIS_PASSWORD', null),
            'port' => env('REDIS_PORT', 6379),
            'database' => 0,
        ],

    ],

];
