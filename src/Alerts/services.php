<?php

$app['emailer.service'] = $app->share(function () use ($app) {
    return new \Alerts\Services\Emailers\SwiftMailer(
        $app['mailer'], //Set by the SwiftmailerServiceProvider
        [$app['email']['from'] => $app['email']['name']],
        $app['view.service']
    );
});

$app['view.service'] = $app->share(function () use ($app) {
    return new \Symfony\Component\Templating\PhpEngine(
        new \Alerts\Services\HtmlTemplateNameParser(__DIR__ . '/Views'),
        new \Symfony\Component\Templating\Loader\FilesystemLoader([])
    );
});

$app['pdo.service'] = $app->share(function () use ($app) {
    return new \PDO(
        $app['database']['pdo']['connection'],
        $app['database']['pdo']['user'],
        $app['database']['pdo']['password']
    );
});

$app['converter.service'] = $app->share(function () use ($app) {
    return new \Alerts\Services\Converter();
});

$app['log.service'] = $app->share(function () use ($app) {
    $handlers = [
        //TODO: Uncomment whichever handler you want to use for logging.
        //new \Monolog\Handler\RedisHandler($app['redis.service'], $app['log']['key'], $app['log']['level']),
        //new \Monolog\Handler\DynamoDbHandler($app['dynamodb.service'], $app['log']['key'], $app['log']['level'])
        new \Monolog\Handler\RotatingFileHandler($app['log']['file'], 1, $app['log']['level'])
    ];
    return new \Monolog\Logger($app['log']['name'], $handlers);
});

$app['oauth2.storage.service'] = $app->share(function () use ($app) {
    //return new \OAuth2\Storage\DynamoDB($app['dynamodb.service'], $app['database']['dynamodb']['oauth']);
    //return new OAuth2\Storage\Memory();
    return new OAuth2\Storage\Pdo($app['pdo.service'], [
        'client_table' => 'oauth_clients',
        'access_token_table' => 'oauth_access_tokens',
        'code_table' => 'oauth_authorizations',
        'refresh_token_table' => 'oauth_refresh_tokens',
        'scope_table' => 'oauth_scopes'
    ]);
});

$app['oauth2.service'] = $app->share(function () use ($app) {
    return new \OAuth2\Server($app['oauth2.storage.service']);
});

/**
 * Because we don't have a "normal" login process we need to
 * make an access token without going through the whole token
 * exchange process. This service will let us do that.
 */
$app['oauth2.token.service'] = $app->share(function () use ($app) {
    return new \OAuth2\ResponseType\AccessToken(
        $app['oauth2.storage.service'],
        $app['oauth2.storage.service']
    );
});