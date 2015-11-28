<?php

$app['github.repository'] = $app->share(function () use ($app) {
    return new \Alerts\Repositories\Http\GitHub();
});

$app['watchedRepos.repository'] = $app->share(function () use ($app) {
    return new \Alerts\Repositories\Dummy\WatchedRepos();
});
