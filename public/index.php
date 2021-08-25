<?php

use Laminas\Diactoros\{ServerRequestFactory, ResponseFactory, Response};
use Laminas\HttpHandlerRunner\Emitter\{EmitterStack, SapiEmitter};
use Laminas\HttpHandlerRunner\RequestHandlerRunner;
use Laminas\Stratigility\MiddlewarePipe;
use Freemework\Container\Container;
use Freemework\Http\Router\Router;
use Freemework\Http\Router\RouteCollection;
use Freemework\Http\Router\Route;
use Freemework\Http\Middleware\RouteMiddleware;
use Psr\Http\Server\MiddlewareInterface;
use Laminas\Stratigility\Handler\NotFoundHandler;
use Freemework\Http\Router\RouteGroup;
use Freemework\Http\Application;
use Twig\Loader\FilesystemLoader;
use Twig\Environment;
use Freemework\Template\Twig\Extension\RouteExtension;
use Freemework\Template\Twig\TwigRenderer;

// Debug mode functions
ini_set('error_reporting', E_ALL);
ini_set('display_errors', 1);

// Root directory
define ('ROOT_DIR', dirname(__DIR__));

try {
    if (file_exists(ROOT_DIR . DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR . 'autoload.php')) {
        require ROOT_DIR . DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR . 'autoload.php';
    } else {
        throw new DomainException('Composer: Autoload file vendor/autoload.php not exist.');
    }

    $container = require ROOT_DIR . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'dependencies.php';
    $app = $container->get(Application::class);

    require $app::CONF_DIR . DIRECTORY_SEPARATOR . 'routes.php';
    require $app::CONF_DIR . DIRECTORY_SEPARATOR . 'middleware.php';

    $request = ServerRequestFactory::fromGlobals();
    $app->run($request);

} catch (Throwable $e) {
    echo $e->getMessage();
}
