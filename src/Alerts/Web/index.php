<?php

require '../bootstrap.php';
require '../services.php';
require '../repositories.php';

$app['install.controller'] = $app->share(function () use ($app) {
    return new \Alerts\Controllers\Install(
        $app['github.repository'],
        $app['watchedRepos.repository'],
        $app['project.url'],
        $app['log.service']
    );
});

$app['hook.controller'] = $app->share(function () use ($app) {
    return new \Alerts\Controllers\Hook(
        $app['github.repository'],
        $app['emailer.service'],
        $app['watchedRepos.repository'],
        $app['log.service']
    );
});

$app['github.oauth.controller'] = $app->share(function () use ($app) {
    return new \Alerts\Controllers\GitHubOAuth(
        $app['github.repository'],
        $app['log.service']
    );
});

$app->error(function(\Exception $e, $code) use ($app) {
    $response = ['message' => $e->getMessage()];

    if ($app['debug']) {
        $response['file'] = $e->getFile();
        $response['line'] = $e->getLine();
        $response['trace'] = $e->getTrace();
    }

    $app['log.service']->error($e->getMessage(), [
        'file' => $e->getFile(),
        'line' => $e->getLine(),
        'trace' => $e->getTraceAsString()
    ]);

    return new \Symfony\Component\HttpFoundation\JsonResponse($response, $code);
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

$app->post('/hooks/github', 'hook.controller:postGitHub');
$app->get('/hooks/github/install', 'install.controller:postGitHub');
$app->get('/github/authorize', 'github.oauth.controller:getAuthorize');

$app->run();
