<?php

namespace Untitled;

use Http\HttpRequest;
use Http\HttpResponse;

require __DIR__ . '/../vendor/autoload.php';

error_reporting(E_ALL);

$environment = 'development';

/**
 * Register the error handler
 */
$whoops = new \Whoops\Run;
if ($environment !== 'production') {
    $whoops->pushHandler(new \Whoops\Handler\PrettyPageHandler);
} else {
    $whoops->pushHandler(function ($e) {
        echo 'Todo: Friendly error page and send an email to the developer';
    });
}
$whoops->register();

/**
 * Environment
 */
$dotenv = new \Dotenv\Dotenv(__DIR__ . '/../');
$dotenv->load();

/**
 * Wrap request and response
 */
$injector = include(__DIR__ . '/../conf/dependencies.php');
$request = $injector->make('Http\HttpRequest');
$response = $injector->make('Http\HttpResponse');

/**
 * Define routes
 */
$dispatcher = \FastRoute\simpleDispatcher(function (\FastRoute\RouteCollector $r) {           
    $moduleRoutePaths = glob(__DIR__ . '/modules/*/conf/routes.php');
    foreach ($moduleRoutePaths as $moduleRoutePath) {
        $routes = include($moduleRoutePath);
        foreach ($routes as $route) {
            $r->addRoute($route[0], $route[1], $route[2]);
        }
    }
});

/**
 * Dispatch to controller
 */
$routeInfo = $dispatcher->dispatch($request->getMethod(), $request->getPath());
switch ($routeInfo[0]) {
    case \FastRoute\Dispatcher::NOT_FOUND:
        $response->setContent('404 - Page not found');
        $response->setStatusCode(404);
        break;
    case \FastRoute\Dispatcher::METHOD_NOT_ALLOWED:
        $response->setContent('405 - Method not allowed');
        $response->setStatusCode(405);
        break;
    case \FastRoute\Dispatcher::FOUND:
        $className = $routeInfo[1][0];
        $method = $routeInfo[1][1];
        $vars = $routeInfo[2];
        $class = $injector->make($className);
        $class->$method($vars);
        break;
}

/**
 * Parse response
 */
foreach ($response->getHeaders() as $header) {
    header($header, false);
}

echo $response->getContent();