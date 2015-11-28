<?php

require '../bootstrap.php';
require '../services.php';
require '../repositories.php';

$app['hook.controller'] = $app->share(function () use ($app) {
    return new \Alerts\Controllers\Hook(
        $app['github.repository'],
        $app['emailer.service'],
        $app['watchedRepos.repository']
    );
});


$app->get('/', function (\Symfony\Component\HttpFoundation\Request $request) use ($app) {
    $patchModels = $app['github.repository']->getChangePatches(
        'jimdoescode/alert.codemana.com',
        '875d94292d3ccf09486431f27b9282473c0b4dc3',
        '1bbf396eda8ed870eb1a9070bd196e772d7cb22d',
        [],
        ['modified', 'removed']
    );

    return new \Symfony\Component\HttpFoundation\Response(
        $app['view.service']->render('htmlEmail', ['patchFiles' => $patchModels]),
        200
    );
});


$app->post('/hooks/github', 'hook.controller:postGithub');

$app->run();
