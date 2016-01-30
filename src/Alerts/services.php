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