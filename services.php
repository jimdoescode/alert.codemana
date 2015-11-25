<?php

$app['emailer.service'] = $app->share(function () use ($app) {

    return new \Alerts\Services\Emailers\SwiftMailer(
        $app['mailer'], //Set by the SwiftmailerServiceProvider
        [$app['email']['from'] => $app['email']['name']]
    );

});

$app['github.service'] = $app->share(function () use ($app) {

    $client = new \GuzzleHttp\Client([
        // Default parameters
        'defaults' => ['debug' => false, 'exceptions' => false],
        // Base URI is used with relative requests
        'base_uri' => 'https://api.github.com',
        // You can set any number of default request options.
        'timeout'  => 2.0,
        //TODO: Here we would put in our github oauth credentials
    ]);

    return new \Alerts\Services\GitHub($client);

});