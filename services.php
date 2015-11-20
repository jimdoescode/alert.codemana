<?php

$app['emailer.service'] = $app->share(function () use ($app) {

    return new \Alerts\Services\Emailers\SwiftMailer(
        $app['mailer'], //Set by the SwiftmailerServiceProvider
        [$app['email']['from'] => $app['email']['name']]
    );

});