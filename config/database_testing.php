<?php

/*
 * Testing Database Configuration
 * 
 * This file provides database configurations specifically for testing.
 * You can switch between different testing database setups here.
 */

return [
    

    'strategies' => [
                
        'mysql_test' => [
            'driver' => 'mysql',
            'host' => env('DB_TEST_HOST', '127.0.0.1'),
            'port' => env('DB_TEST_PORT', '3306'),
            'database' => env('DB_TEST_DATABASE', 'restaurant_reservation_test'),
            'username' => env('DB_TEST_USERNAME', 'root'),
            'password' => env('DB_TEST_PASSWORD', ''),
            'unix_socket' => env('DB_SOCKET', ''),
            'charset' => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
            'prefix' => '',
            'prefix_indexes' => true,
            'strict' => true,
            'engine' => null,
            'options' => extension_loaded('pdo_mysql') ? array_filter([
                PDO::MYSQL_ATTR_SSL_CA => env('MYSQL_ATTR_SSL_CA'),
            ]) : [],
        ],
    ],
    

    
];
