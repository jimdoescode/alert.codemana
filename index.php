<?php

require 'bootstrap.php';
require 'services.php';

$app['hook.controller'] = $app->share(function () use ($app) {
    return new \Alerts\Controllers\Hook(
        $app['github.service'],
        $app['emailer.service']
    );
});


$app->get('/', function (\Symfony\Component\HttpFoundation\Request $request) use ($app) {
    return new \Symfony\Component\HttpFoundation\Response('Hello Silex', 200);
});


$app->post('/', 'hook.controller:postIndex');

$app->run();
