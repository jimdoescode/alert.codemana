<?php

require '../bootstrap.php';

$addCorsHeaders = function (\Symfony\Component\HttpFoundation\Request $request,
                            \Symfony\Component\HttpFoundation\Response $response) use ($app) {

    $response->headers->set('Access-Control-Allow-Origin', $app['project.url'], false);
    return $response;
};

//Pre-flight responses should include the needed CORS headers.
$addOptionsHeaders = function (\Symfony\Component\HttpFoundation\Request $request,
                               \Symfony\Component\HttpFoundation\Response $response) use ($app) {

    $response->headers->set('Access-Control-Allow-Methods', 'POST, PUT, GET, DELETE, OPTIONS', false);
    $response->headers->set('Access-Control-Allow-Headers', 'Origin, Accept, Authorization, Content-Type, X-Requested-With', false);
    return $response;
};

//Set up routing with OAuth middleware that will be run before each secured request
$validateUser = function (\Symfony\Component\HttpFoundation\Request $request, \Silex\Application $app) {
    return $app['oauth2.controller']->validateRequest($request, 'user');
};

$app['oauth2.controller'] = $app->share(function () use ($app) {
    return new \Alerts\Controllers\OAuth2(
        $app['oauth2.service'],
        $app['users.repository'],
        $app['log.service']
    );
});

$app['install.controller'] = $app->share(function () use ($app) {
    return new \Alerts\Controllers\Install(
        $app['github.repository'],
        $app['watchedRepos.repository'],
        $app['project.url'],
        $app['log.service']
    );
});

$app['home.controller'] = $app->share(function () use ($app) {
    return new \Alerts\Controllers\Home(
        $app['view.service'],
        $app['github']['client_id'],
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

$app['login.controller'] = $app->share(function () use ($app) {
    return new \Alerts\Controllers\Login(
        $app['oauth2.token.service'],
        $app['users.repository'],
        $app['github.repository'],
        $app['log.service']
    );
});

$app->error(function(\Exception $e, $code) use ($app) {
    $response = ['message' => $e->getMessage()];

    if ($app['debug']) {
        $response['file'] = $e->getFile();
        $response['line'] = $e->getLine();
        $response['trace'] = $e->getTraceAsString();
    }

    $app['log.service']->error($e->getMessage(), [
        'file' => $e->getFile(),
        'line' => $e->getLine(),
        'trace' => $e->getTraceAsString()
    ]);

    return new \Symfony\Component\HttpFoundation\JsonResponse($response, $code);
});

/*$app->get('/', function (\Symfony\Component\HttpFoundation\Request $request) use ($app) {
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
*/

//Api Routes
$app->post('/token', 'oauth2.controller:postToken')
    ->after($addCorsHeaders);
$app->options('/token', 'oauth2.controller:optionsToken')
    ->after($addCorsHeaders)
    ->after($addOptionsHeaders);
$app->post('/hooks/github/install', 'install.controller:postGitHub')
    ->before($validateUser)
    ->after($addCorsHeaders);
$app->options('/hooks/github/install', 'install.controller:optionsIndex')
    ->after($addCorsHeaders)
    ->after($addOptionsHeaders);

//Direct routes
$app->post('/hooks/github', 'hook.controller:postGitHub');
$app->get('/github/login', 'login.controller:getGitHubAuthorize');

$app->get('/', 'home.controller:getIndex')
    ->before(function (\Symfony\Component\HttpFoundation\Request $request, \Silex\Application $app) {
        return $app['oauth2.controller']->validateRequest($request, 'user', true);
    });

$app->run();
