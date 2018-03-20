<?php

namespace Untitled;

use Http\HttpRequest;
use Http\HttpResponse;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use Untitled\Core\Application;
use Noodlehaus\Config;

require __DIR__ . '/../vendor/autoload.php';

error_reporting(E_ALL);

/**
 * Environment
 */
$dotenv = new \Dotenv\Dotenv(__DIR__ . '/../');
$dotenv->load();

/**
 * Set environment
 */
$environment = getenv('ENVIRONMENT');
Application::getInstance()->setEnvironment($environment);

/**
 * Logger
 */
$logger = new Logger(getenv('APP_NAME'));
$logger->pushHandler(new StreamHandler(__DIR__ . '/../var/application.log', Logger::WARNING));
Application::getInstance()->setLogger($logger);

/**
 * Register the error handler
 */
$whoops = new \Whoops\Run;
if ($environment !== Application::ENV_PRODUCTION) {
    $whoops->pushHandler(new \Whoops\Handler\PrettyPageHandler);
} else {
    $whoops->pushHandler(function ($e) {
        Application::getInstance()->getLogger()->error($e->getMessage());
    });
}
$whoops->register();

/**
 * Wrap request and response
 */
$injector = include(__DIR__ . '/../conf/dependencies.php');
Application::getInstance()->setInjector($injector);
$request = $injector->make('Http\HttpRequest');
$response = $injector->make('Http\HttpResponse');

/**
 * Load configurations
 */
$moduleConfigPaths = glob(__DIR__ . '/modules/*/conf/settings.php');
$configurations = new Config($moduleConfigPaths);
Application::getInstance()->setConfigurations($configurations);

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
Application::getInstance()->setRouteInfo($routeInfo);
switch ($routeInfo[0]) {
    case \FastRoute\Dispatcher::NOT_FOUND:
        $response->setContent1('404 - Page not found');
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