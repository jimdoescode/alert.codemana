<?php

$app['github.repository'] = $app->share(function () use ($app) {

    return new \Alerts\Repositories\Http\GitHub();

});
