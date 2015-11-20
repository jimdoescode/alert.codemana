<?php

require 'vendor/autoload.php';

$app = new Silex\Application();
//If no environment variable is set then default to dev.
$app['env'] = getenv('APP_ENV') ?: 'dev';

$app->register(new Silex\Provider\ServiceControllerServiceProvider());
$app->register(new Silex\Provider\SwiftmailerServiceProvider());
$app->register(new Igorw\Silex\ConfigServiceProvider(__DIR__ . "/src/Alerts/Config/{$app['env']}.php"));