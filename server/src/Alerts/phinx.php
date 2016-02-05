<?php

require 'bootstrap.php';

/**
 * This is the configuration file for the Phinx Migration tool
 * Since this file is not located at the root of the project
 * you need to specify its location when running a phinx command
 *
 * php vendor/bin/phinx migrate --configuration=src/Alerts/phinx.php
 */

return [
    //Point Phinx at the Migrations directory
    'paths' => ['migrations' => __DIR__ . '/Migrations'],

    //Set the database to whatever is configured during bootstrap
    'environments' => [
        'default_migration_table' => 'migrations',
        'default_database' => $app['env'],
        $app['env'] => [
            'name' => $app['database']['pdo']['dbname'],
            'connection' => $app['pdo.service']
        ]
    ]
];
