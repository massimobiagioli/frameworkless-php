<?php

$injector = new \Auryn\Injector;

// Core
$injector->alias('Http\Request', 'Http\HttpRequest');
$injector->share('Http\HttpRequest');
$injector->define('Http\HttpRequest', [
    ':get' => $_GET,
    ':post' => $_POST,
    ':cookies' => $_COOKIE,
    ':files' => $_FILES,
    ':server' => $_SERVER
]);
$injector->alias('Http\Response', 'Http\HttpResponse');
$injector->share('Http\HttpResponse');

// Modules
$depPaths = glob(__DIR__ . '/modules/*/conf/dependencies.php');
foreach ($depPaths as $depPath) {
    include($depPath);    
}

return $injector;