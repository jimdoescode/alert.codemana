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
        new \Alerts\Services\HtmlTemplateNameParser(__DIR__ . '/src/Alerts/Views'),
        new \Symfony\Component\Templating\Loader\FilesystemLoader([])
    );

});

$app['converter.service'] = $app->share(function () use ($app) {

    return new \Alerts\Services\Converter();
});