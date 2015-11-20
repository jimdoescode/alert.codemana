<?php

return [

    'project.name' => 'CodeMana Alerts',

    'debug' => true,

    'email' => [
        'from' => 'test@alerts.codemana.edu',
        'name' => 'CodeMana Alert'
    ],

    'swiftmailer.use_spool' => false,
    'swiftmailer.options' => [
        'host' => 'localhost',
        'port' => '25',
        'username' => '',
        'password' => '',
        'encryption' => null,
        'auth_mode' => null,
    ],

];