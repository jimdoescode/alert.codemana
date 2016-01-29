<?php

return [

    'project.name' => 'CodeMana Alerts',
    'project.url' => 'https://alerts.codemana.com',

    'debug' => true,

    'email' => [
        'from' => 'test@alerts.codemana.com',
        'name' => 'CodeMana Alert'
    ],

    'github' => [
        'client_id' => '',
        'client_secret' => ''
    ],

    'log' => [
        'level' => \Monolog\Logger::DEBUG,
        'name' => 'codemana.alerts',
        'key' => 'logs',
        'file' => __DIR__.'/../../../errors.monolog.log'
    ],

    'swiftmailer.use_spool' => false,
    'swiftmailer.options' => [
        'host' => '',
        'port' => '587',
        'username' => '',
        'password' => '',
        'encryption' => 'tls',
        'auth_mode' => null,
    ],

];