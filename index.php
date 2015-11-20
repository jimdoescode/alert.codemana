<?php

require 'vendor/autoload.php';

$klein = new \Klein\Klein();

$klein->respond(['GET', 'POST'], '/', function (\Klein\Request $request, \Klein\Response $response) {

    $response->code(202);
    $response->body('Hello Response');

    return $response;
});

$klein->dispatch();
