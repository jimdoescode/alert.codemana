<?php

require 'bootstrap.php';
require 'services.php';
require 'repositories.php';

$app['hook.controller'] = $app->share(function () use ($app) {
    return new \Alerts\Controllers\Hook(
        $app['github.repository'],
        $app['emailer.service'],
        $app['watchedRepo.repository']
    );
});


$app->get('/', function (\Symfony\Component\HttpFoundation\Request $request) use ($app) {
    return new \Symfony\Component\HttpFoundation\Response('Hello Silex', 200);
});


$app->post('/hook/github', 'hook.controller:postGithub');

$app->run();
