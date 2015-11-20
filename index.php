<?php

require 'bootstrap.php';
require 'services.php';

$app->match('/', function(\Symfony\Component\HttpFoundation\Request $request) {

    return new \Symfony\Component\HttpFoundation\Response('Hello Silex', 202);

})->method('GET|POST');


$app->post('/', function(\Symfony\Component\HttpFoundation\Request $request) {

    return new \Symfony\Component\HttpFoundation\Response('Hello Silex', 202);

});

$app->run();
